<?php
/*
Plugin Name: Live Chat Online
Plugin URI: http://www.realtime-chat.com
Description: Live Chat Online
Version: 1.0.0
Author: realtime-chat.com
Author URI: http://www.realtime-chat.com
*/

defined( 'ABSPATH' ) or die( 'Hacking attempt!' );

if ( ! class_exists( 'LiveChats' ) ) {
    class LiveChats
    {
        public static $table_prefix     = 'live-chats_';
        public static $optionParameters = 'live-chats_options';
        public static $defaultOptions   = array(
            'chat_width'    => 300,
            'chat_color'    => '#bc392b',
            'chat_position' => 'js-br',
        );
        public static $cookiePrefix     = 'live-chats_hash';

        /**
         * Listen incoming request from js
         */
        public static function plugin_ajax(){
            $personalKey = (string)get_option(LiveChatsApi::$optionKey, '');
            if(empty($personalKey)){return;}

            //incoming parameters
            $mode           = @$_POST['mode'];
            $user_hash      = @$_POST['hash'];
            $user_hash      = (empty($user_hash) ? @$_COOKIE[self::$cookiePrefix] : $user_hash);
            $user_ip        = LiveChatsApi::getUserIP();
            $user_browser   = @$_SERVER['HTTP_USER_AGENT'];

            $message_text   = @strip_tags(trim($_POST['message']));         //text of message
            $message_page   = @strip_tags(trim($_POST['message_page']));    //referer
            $queryMode      = @$_POST['queryMode'];                         //action
            $limit          = @$_POST['countMessage'];                      //count of message for returning in read mode
            $message_created= date('Y-m-d H:i:s');                          //now
            $message_type   = 0;                                            //0 - user, 1 - admin

            if( empty($mode) or empty($user_hash) or !in_array($mode, array('add', 'read', 'finish')) ){
                die();
            }

            //save message from user
            if( $mode == 'add' and !empty($message_text) ){
                $addRes = self::add_messages($user_hash, $user_ip, $user_browser, $message_text, $message_created, $message_page, $message_type);
                if($addRes){
                    LiveChatsApi::send_messages($user_hash, $user_ip, $user_browser, $message_text, $message_created, $message_page);
                }

                print json_encode(array('result' => $addRes));exit;
            }

            //read message of user
            if( $mode == 'read' and !empty($user_hash) and !empty($user_ip) and !empty($user_browser) ){
                $messages = self::read_messages($user_hash, $user_ip, $user_browser, array('queryMode' => $queryMode, 'limit' => $limit));

                print json_encode(array('messages' => $messages));exit;
            }

            //finish chat
            if( $mode == 'finish' and !empty($user_hash) and !empty($user_ip) and !empty($user_browser) ){
                //send log operation "finish"
                LiveChatsApi::send_log($user_hash, $user_ip, $user_browser, $message_created, 'finish', array(), $message_page);

                //remove all messages
                self::remove_messages($user_hash, $user_ip, 'user');
            }

            exit;
        }

        /**
         * Init chat. Should be started at all pages on frontend
         */
        public static function wp_head(){
        	//delete_option( LiveChatsApi::$optionKey );
            //$personalKey = md5(time().rand(1,9999).microtime().rand(1,9999));if( !add_option(LiveChatsApi::$optionKey, $personalKey) ){LiveChatsApi::printAnswer(2);}

            $personalKey    = (string)get_option(LiveChatsApi::$optionKey, '');
            if(empty($personalKey)){return;}//disable for unconnected plugin

            //only for site
            if ( !is_admin() ) {
                $jsPath         = plugins_url(LiveChatsApi::$plugin_name . '/assets/js.js');
                $cssPath        = plugins_url( LiveChatsApi::$plugin_name . '/assets/css.css');

                wp_register_script(LiveChatsApi::$plugin_name.'_js', $jsPath, array(), LiveChatsApi::$plugin_version, false);
                wp_register_style( LiveChatsApi::$plugin_name.'_css', $cssPath );

                //parameters
                $js_parameters = array(
                    'site_url'      => site_url(),
                    'request_url'   => site_url() . '/wp-admin/admin-ajax.php',
                    'cookie_prefix' => self::$cookiePrefix
                );
                wp_localize_script(LiveChatsApi::$plugin_name.'_js', 'livechats_parameters', $js_parameters);

                //run
                wp_enqueue_script( 'jquery' );
                wp_enqueue_script( LiveChatsApi::$plugin_name.'_js' );
                wp_enqueue_style( LiveChatsApi::$plugin_name.'_css' );


                ob_start();// Prevent output before cookies
            }
        }

        /**
         * Notice for user. Only in admin panel
         */
        public static function admin_notices(){
            if(is_admin()) {
                global $status, $page, $s;
                $context    = $status;
                $plugin     = LiveChatsApi::$plugin_name.'/'.LiveChatsApi::$plugin_name.'.php';
                $nonce      = wp_create_nonce('deactivate-plugin_' . $plugin);
                $actions    = 'plugins.php?action=deactivate&amp;plugin=' . urlencode($plugin) . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s  . '&amp;_wpnonce=' . $nonce;

                $personaKey     = (string)get_option(LiveChatsApi::$optionKey, '');
                $pluginStatus   = is_plugin_active($plugin);
                if( !empty($personaKey) or !$pluginStatus ){return;}

                echo '<div style="height:50px;line-height:50px;font-size:16px;font-weight:bold;" class="notice-warning notice">To use "live-chats" plugin, please add this site to <a target="_blank" href="'.LiveChatsApi::$sitePublic.'">your account</a> at '.str_replace('http://','',LiveChatsApi::$sitePublic).' or <a href="'.$actions.'">deactivate</a> Live Chats plugin.</div>';
            }
        }

        //work with options of plugin
        public static function plugin_options($mode = 'add', $options = array()){
            if(!empty($options)){
                $options = self::$defaultOptions;
            }

            if($mode == 'add'){
                update_option(self::$optionParameters, $options);
            }

            if( $mode == 'get' ){
                return get_option(self::$optionParameters, $options);
            }

            if( $mode == 'remove' ){
                delete_option( self::$optionParameters );
            }
        }

        //Read messages from table
        public static function read_messages($user_hash, $user_ip, $user_browser = '', $parameters = array()){
            global $wpdb;

            $list = array();

            if( $parameters['queryMode'] == 'api_read' ){
                $sql =  $wpdb->prepare(
                    'SELECT * FROM  `'.$wpdb->base_prefix.self::$table_prefix.'messages` WHERE user_hash = %s AND user_ip = %s ORDER BY message_created DESC',
                    $user_hash, $user_ip
                );
                $list = $wpdb->get_results($sql);
            }

            if( $parameters['queryMode'] == 'user_last' ){
                $limit = @(int)$parameters['limit'];
                $limit = (empty($limit) ? 1 : $limit);

                $sql =  $wpdb->prepare('
                    SELECT id, message_text, message_created, message_type FROM  `'.$wpdb->base_prefix.self::$table_prefix.'messages`
                    WHERE user_hash = %s AND user_ip = %s AND ( (user_browser = %s AND message_type = 0) OR (user_browser = "" AND message_type = 1) )
                    ORDER BY message_created DESC
                    LIMIT 0, '.$limit,
                    $user_hash, $user_ip, $user_browser
                );
                $messages = $wpdb->get_results($sql);

                if( !empty($messages) ){
                    foreach($messages as $k_item => $v_item){
                        $list[] = array(
                            'name'          => ($v_item->message_type == 0 ? 'You' : 'Customer support'),
                            'message_id'    => $v_item->id.'_'.$user_hash,
                            'text'          => nl2br($v_item->message_text),
                            'time'          => date('H:i', @strtotime($v_item->message_created)),
                            'type'          => $v_item->message_type
                        );
                    }

                    if($limit > 1){
                        $list = array_reverse($list);
                    }
                }
            }

            return $list;
        }

        //Save message in table
        public static function add_messages($user_hash, $user_ip, $user_browser, $message_text, $message_created = '', $message_page = '', $message_type = 0){
            global $wpdb;
            $message_created = (empty($message_created) ? date('Y-m-d H:i:s') : $message_created);

            $sql =  $wpdb->prepare(
                'INSERT INTO `'.$wpdb->base_prefix.self::$table_prefix.'messages`
                SET user_hash = %s, user_ip = %s, user_browser = %s, message_text = %s, message_page = %s, message_created = %s, message_type = %d',
                $user_hash, $user_ip, $user_browser, $message_text, $message_page, $message_created, $message_type
            );
            return $wpdb->query( $sql );
        }

        //Remove messages from db
        public static function remove_messages($user_hash, $user_ip, $mode = '' ){
            global $wpdb;

            //remove sll message of user
            if($mode == 'user'){
                $sql =  $wpdb->prepare('DELETE FROM  `'.$wpdb->base_prefix.self::$table_prefix.'messages` WHERE user_hash = %s AND user_ip = %s', $user_hash, $user_ip);
                $wpdb->query($sql);
            }

            if($mode == 'all'){
                $sql =  'TRUNCATE TABLE `'.$wpdb->base_prefix.self::$table_prefix.'messages`';
                $wpdb->query($sql);
            }

            return true;
        }

        /**
         * Activation hook
         *
         * Create tables if they don't exist and add set plugin options
         */
        public static function install(){
            global $wpdb;

            // Get the correct character collate
            $charset_collate = 'utf8';
            if ( ! empty( $wpdb->charset ) ) {$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";}
            if ( ! empty( $wpdb->collate ) ) {$charset_collate .= " COLLATE $wpdb->collate";}

            if ( $wpdb->get_var( 'SHOW TABLES LIKE "' . $wpdb->base_prefix.self::$table_prefix.'message'.'" ' ) != $wpdb->base_prefix.self::$table_prefix.'message' ) {
                //create table
                $sql = '
                    CREATE TABLE IF NOT EXISTS `'.$wpdb->base_prefix.self::$table_prefix.'messages` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `user_hash` varchar(10) NOT NULL DEFAULT "",
                        `user_ip` varchar(40) NOT NULL DEFAULT "",
                        `user_browser` varchar(255) NOT NULL DEFAULT "",
                        `user_name` varchar(50) NOT NULL DEFAULT "",
                        `message_text` varchar(1000) NOT NULL DEFAULT "",
                        `message_page` varchar(255) NOT NULL DEFAULT "",
                        `message_created` datetime DEFAULT NULL,
                        `message_type` tinyint(1) NOT NULL DEFAULT "0" COMMENT "1 - admin, 0 - user",
                        PRIMARY KEY (`id`)
                    ) ENGINE=MyISAM DEFAULT CHARSET='.$charset_collate.' AUTO_INCREMENT=1
                ;';
                $wpdb->query( $sql );

                //set options
                self::plugin_options('add');
            }else{
                //clear table
                $sql = 'TRUNCATE TABLE `'.$wpdb->base_prefix.self::$table_prefix.'messages`';
                $wpdb->query( $sql );
            }
        }

        /**
         * Deactivation hook
         *
         * Clear table
         */
        public static function deactivation(){
            global $wpdb;

            $sql = 'TRUNCATE TABLE `'.$wpdb->base_prefix.self::$table_prefix.'messages`';
            $wpdb->query($sql);
        }

        /**
         * Uninstall hook
         *
         * Remove tables and plugin options
         */
        public static function uninstall(){
            global $wpdb;

            //remove table
            $sql = 'DROP TABLE IF EXISTS `'.$wpdb->base_prefix.self::$table_prefix.'messages`';
            $wpdb->query($sql);

            //remove options
            self::plugin_options('remove');
        }
    }

    class LiveChatsApi{

        public static $sitePublic       = 'http://www.realtime-chat.com';
        public static $siteSecure       = 'http://secure.realtime-chat.com';
        public static $siteSecureAction = 'http://secure.realtime-chat.com/from-plugin';

        public static $plugin_name      = 'live-chats';
        public static $plugin_version   = '1.0.0';
        public static $optionKey        = 'live-chats_key'; //name for key in options
        public static $personalKey      = '';               //current key of plugin

        public static $requestAnswers = array(
            0 => array('status' => 1, 'msg' => 'request success'),
            1 => array('status' => 1, 'msg' => 'connection success'),
            2 => array('status' => 0, 'msg' => 'connection failed'),
            3 => array('status' => 1, 'msg' => 'adding messages success'),
            4 => array('status' => 0, 'msg' => 'adding messages failed'),
            5 => array('status' => 1, 'msg' => 'ping success'),
            6 => array('status' => 0, 'msg' => 'incorrect request'),
            7 => array('status' => 0, 'msg' => 'plugin should be connected'),
            8 => array('status' => 0, 'msg' => 'wrong key'),
        );

        public static $tagAnswer = 'livechats_tag';

        /**
         * Listens incoming request
         *
         * Constructor for other methods
         */
        public static function init(){
            //check request
            if( !isset($_POST['livechats_request']) or empty($_POST['livechats_request']) ){
                return true;    //nothing necessary
            }

            self::$personalKey  = (string)get_option(self::$optionKey, '');
            $allowedHost        = @str_replace(array('http://','https://'),'',trim(self::$siteSecure,'/'));
            $allowedIP          = @gethostbyname( $allowedHost );

            $refererHost        = @str_replace(array('http://','https://'),'',trim($_SERVER["HTTP_REFERER"],'/'));
            $requestIP          = LiveChatsApi::getUserIP();

            //check request ip
            $requestIP = LiveChatsApi::getUserIP();
            if( empty($requestIP) or $requestIP != $allowedIP ){
                return true;    //wrong request ip
            }

            //check referer
            if( empty($refererHost) or $refererHost != $allowedHost ){
                return true;    //wrong referer
            }

            //check data, action, key
            $requestData    = self::convertString($_POST['livechats_request'], 'decode');
            if( empty($requestData) or empty($requestData['action']) or !method_exists('LiveChatsApi','action_'.$requestData['action']) ){
                self::printAnswer(6);
            }

            //check key for actions
            $action     = (string)'action_'.$requestData['action'];
            $requestKey = @(string)$requestData['key'];
            if( $action == 'action_connect' ){
                if( !empty(self::$personalKey) ){
                    self::printAnswer( (self::$personalKey == $requestKey ? 1 : 2) );
                }
            }else{
                if( empty(self::$personalKey) ){
                    self::printAnswer(7);
                }
                if( empty($requestKey) or self::$personalKey != $requestKey ){
                    self::printAnswer(8);
                }
            }

            //run action
            self::$action($requestData);

            exit;
        }

        /**
         * Send message from user to admin
         */
        public static function requestServer($args){
            $postRes = wp_remote_post( self::$siteSecureAction, $args );
            if ( is_wp_error( $postRes ) ) {
                $error = array( 'wp_error' => $postRes->get_error_message() );
                return false;
            }

            return true;
        }

        /**
         * Encode and decode array for hiding parameters from server
         *
         * @param $input
         * @param string $mode
         * @return mixed
         */
        public static function convertString($input, $mode = ''){
            if( empty($input) or empty($mode) ){
                return '';
            }
            if($mode == 'decode'){
                return @json_decode(base64_decode(strrev(base64_decode(urldecode($input)))), true);//array
            }
            if($mode == 'encode'){
                return @urldecode(base64_encode(strrev(base64_encode(json_encode($input)))));//string
            }
        }

        public static function getUserIP(){
            $user_ip = '';
            if      ( getenv('REMOTE_ADDR') ){ $user_ip = getenv('REMOTE_ADDR');}
            elseif  ( getenv('HTTP_FORWARDED_FOR') ){ $user_ip = getenv('HTTP_FORWARDED_FOR');}
            elseif  ( getenv('HTTP_X_FORWARDED_FOR') ){ $user_ip = getenv('HTTP_X_FORWARDED_FOR');}
            elseif  ( getenv('HTTP_X_COMING_FROM') ){ $user_ip = getenv('HTTP_X_COMING_FROM');}
            elseif  ( getenv('HTTP_VIA') ){ $user_ip = getenv('HTTP_VIA');}
            elseif  ( getenv('HTTP_XROXY_CONNECTION') ){ $user_ip = getenv('HTTP_XROXY_CONNECTION');}
            elseif  ( getenv('HTTP_CLIENT_IP') ){ $user_ip = getenv('HTTP_CLIENT_IP');}
            $user_ip = trim($user_ip);
            if ( empty($user_ip) ){ return '';}
            if ( !preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $user_ip) ){return '';}
            return $user_ip;
        }

        //Send message from user
        public static function send_messages($user_hash, $user_ip, $user_browser, $message_text, $message_created, $message_page = ''){
            $personalKey = (string)get_option(LiveChatsApi::$optionKey, '');
            if(empty($personalKey)){return false;}

            $parameters = array(
                'action'            => 'message',
                'key'               => $personalKey,

                'hash'              => $user_hash,
                'ip'                => $user_ip,
                'browser'           => $user_browser,
                'message'           => $message_text,
                'message_page'      => $message_page,
                'created'           => $message_created,

                'plugin_version'    => LiveChatsApi::$plugin_version,
                'domain'            => site_url()
            );
            $requestVars = array('body' => array(LiveChatsApi::$tagAnswer => LiveChatsApi::convertString($parameters,'encode')));

            return LiveChatsApi::requestServer( $requestVars );
        }

        //Send log from user
        public static function send_log($user_hash, $user_ip, $user_browser, $logCreated, $logCommand, $logParameters = array(), $referer_page = ''){
            $personalKey = (string)get_option(LiveChatsApi::$optionKey, '');
            if(empty($personalKey)){return false;}

            $parameters = array(
                'action'            => 'log',
                'key'               => $personalKey,

                'hash'              => $user_hash,
                'ip'                => $user_ip,
                'browser'           => $user_browser,
                'log_command'       => $logCommand,
                'log_data'          => $logParameters,
                'referer_page'      => $referer_page,
                'created'           => $logCreated,

                'plugin_version'    => LiveChatsApi::$plugin_version,
                'domain'            => site_url()
            );
            $requestVars = array('body' => array(LiveChatsApi::$tagAnswer => LiveChatsApi::convertString($parameters,'encode')));

            return LiveChatsApi::requestServer( $requestVars );
        }

        /**
         * Print answer of action
         * exit at the end
         * @param int $readyAnswer
         * @param array $data
         */
        public static function printAnswer($readyAnswer = 0, $data = array()){
            $answer = array(
                'msg_code'  => $readyAnswer,
                'msg'       => self::$requestAnswers[$readyAnswer]['msg'],
                'status'    => self::$requestAnswers[$readyAnswer]['status'],
                'data'      => $data,
            );
            $answer = self::convertString($answer, 'encode');
            print '<'.self::$tagAnswer.'>'.$answer.'</'.self::$tagAnswer.'>';

            exit;
        }

        /**
         * Connect plugin
         */
        protected static function action_connect(){
            $personalKey = md5(time().rand(1,9999).microtime().rand(1,9999));
            if( !add_option(self::$optionKey, $personalKey) ){
                self::printAnswer(2);
            }

            //send personal key to admin
            $requestVars = array(
                'body' => array(
                    self::$tagAnswer => self::convertString(array(
                        'action'    => 'connect',
                        'key'       => $personalKey,
                        'site'      => site_url(),
                        'pl'        => LiveChatsApi::$plugin_name,
                        'pl_v'      => LiveChatsApi::$plugin_version
                    ),'encode')
                )
            );
            $requestRes = self::requestServer($requestVars);

            if($requestRes){
                self::printAnswer(1);
            }else{
                self::printAnswer(2);
            }
        }

        /**
         * Read message of user by hash
         */
        protected static function action_read($data){
            $user_hash  = @$data['hash'];
            $user_ip    = @$data['ip'];
            $messages = LiveChats::read_messages($user_hash, $user_ip, '', 0, array('queryMode' => 'api_read'));
            self::printAnswer(0, array('messages' => $messages));
        }

        /**
         * Add message for user by hash
         */
        protected static function action_write($data){
            $user_hash      = @(string)$data['hash'];
            $user_ip        = @(string)$data['ip'];
            $message        = @(string)$data['message'];
            $created        = @(string)$data['created'];
            if( LiveChats::add_messages($user_hash, $user_ip, '', $message, $created, 1) ){
                self::printAnswer(3);
            }else{
                self::printAnswer(4);
            }
        }

        /**
         * Update ping plugin
         */
        protected static function action_ping(){
            self::printAnswer(5, array('plugin_version' => LiveChatsApi::$plugin_version));
        }

        /**
         * Update settings plugin
         */
        protected static function action_update_options(){

        }

        /**
         * Read settings plugin
         */
        protected static function action_read_options(){

        }
    }
}

register_activation_hook(   __FILE__, array( 'LiveChats', 'install' ) );        //install hook
register_deactivation_hook( __FILE__, array( 'LiveChats', 'deactivation' ) );   //deactivation hook
register_uninstall_hook(    __FILE__, array( 'LiveChats', 'uninstall' ) );      //uninstall hook

add_action( 'init', array('LiveChatsApi', 'init') );    //listening incoming request

add_action( 'wp_head'                       , array( 'LiveChats', 'wp_head') );         //show plugin
add_action( 'admin_notices'                 , array( 'LiveChats', 'admin_notices') );   //notice for admin
add_action( 'wp_ajax_LiveChatsAjax'         , array( 'LiveChats', 'plugin_ajax' ) );    //listening ajax request
add_action( 'wp_ajax_nopriv_LiveChatsAjax'  , array( 'LiveChats', 'plugin_ajax' ) );    //listening ajax request

