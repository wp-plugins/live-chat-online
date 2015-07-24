<?php
/*
Plugin Name: Live Chat Online
Plugin URI: http://www.realtime-chat.com
Description: Live Chat Online
Version: 1.1.0
Author: realtime-chat.com
Author URI: http://www.realtime-chat.com
*/

defined( 'ABSPATH' ) or die( 'Hacking attempt!' );

if ( ! class_exists( 'LiveChats' ) ) {
    class LiveChats
    {
        public static $table_prefix     = 'live-chats_';
        public static $optionParameters = 'live-chats_options';
        public static $cookiePrefix     = 'live-chats_hash';
        public static $tag_prefix       = 'livechats';
        public static $setting_page_url = '/wp-admin/admin.php?page=live_chat_online_page';
        public static $defaultOptions   = array(
            //template
            'width'                     => array('value' => 314         , 'filter' => 'int'         , 'js_block' => 'template'),
            'position'                  => array('value' => 'right'     , 'filter' => ''            , 'js_block' => 'template'),
            'status'                    => array('value' => 1           , 'filter' => ''            , 'js_block' => 'template'),
            'panel_background'          => array('value' => '#BC392B'   , 'filter' => 'hex_color'   , 'js_block' => 'color'),
            'panel_border_color'        => array('value' => '#BC392B'   , 'filter' => 'hex_color'   , 'js_block' => 'color'),
            'body_background'           => array('value' => '#FFFFFF'   , 'filter' => 'hex_color'   , 'js_block' => 'color'),

            //title
            'panel_title'               => array('value' => 'Need help?', 'filter' => 'text'        , 'js_block' => 'text'),
            'btn_finish_text'           => array('value' => 'Close'     , 'filter' => 'text'        , 'js_block' => 'text'),
            'btn_finish_background'     => array('value' => '#8A2D24'   , 'filter' => 'hex_color'   , 'js_block' => 'color'),
            'btn_finish_color'          => array('value' => '#333333'   , 'filter' => 'hex_color'   , 'js_block' => 'color'),
            'btn_finish_border_color'   => array('value' => '#DADAD9'   , 'filter' => 'hex_color'   , 'js_block' => 'color'),
            'btn_expand_background'     => array('value' => '#8A2D24'   , 'filter' => 'hex_color'   , 'js_block' => 'color'),

            //body
            'admin_signature'           => array('value' => 'Customer support', 'filter' => 'text', 'js_block' => 'text'),
            'admin_signature_color'     => array('value' => '#BC392B', 'filter' => 'hex_color', 'js_block' => 'color'),
            'admin_text_color'          => array('value' => '#333333', 'filter' => 'hex_color', 'js_block' => 'color'),
            'hello_message'             => array('value' => 'How can I help you?', 'filter' => 'text', 'js_block' => 'text'),

            'user_signature'            => array('value' => 'You'           , 'filter' => 'text'        , 'js_block' => 'text'),
            'user_signature_color'      => array('value' => '#000000'       , 'filter' => 'hex_color'   , 'js_block' => 'color'),
            'user_text_color'           => array('value' => '#333333'       , 'filter' => 'hex_color'   , 'js_block' => 'color'),
            'time_color'                => array('value' => '#909090'       , 'filter' => 'hex_color'   , 'js_block' => 'color'),
            'message_border_color'      => array('value' => '#F2F2F2'       , 'filter' => 'hex_color'   , 'js_block' => 'color'),
            'send_email'                => array('value' => 'Send email'    , 'filter' => 'text'        , 'js_block' => 'text'),
            'thank_message'             => array('value' => '', 'filter' => 'text', 'js_block' => 'text'),

            //write
            'enter_text_placeholder'    => array('value' => 'Put your message here ...', 'filter' => 'text', 'js_block' => 'text'),
            'write_panel_background'    => array('value' => '#BC392B'   , 'filter' => 'hex_color'   , 'js_block' => 'color'),
            'write_area_background'     => array('value' => '#FFFFFF'   , 'filter' => 'hex_color'   , 'js_block' => 'color'),
            'write_area_color'          => array('value' => '#333333'   , 'filter' => 'hex_color'   , 'js_block' => 'color'),

            //offline
            'offline_message'           => array('value' => 'Sorry, but we are offline now. We will communicate with you as soon as possible. Please, leave your contact email and your message.', 'filter' => 'text', 'js_block' => 'text'),
            'offline_thank_message'     => array('value' => 'Your message was sent. Thank you.', 'filter' => 'text', 'js_block' => 'text'),
            'email_label'               => array('value' => 'Email'         , 'filter' => 'text'        , 'js_block' => 'text'),
            'name_label'                => array('value' => 'Name'          , 'filter' => 'text'        , 'js_block' => 'text'),
            'message_label'             => array('value' => 'Message'       , 'filter' => 'text'        , 'js_block' => 'text'),

            'auth_email'                => array('value' => ''              , 'filter' => 'text'        , 'js_block' => ''),    //keep login of user fro authorization

        );
        public static $translation = array(
            'en'    => array(
                'page_settings_title'       => 'Live Chat Online',
                's_tab_color'               => 'Colors',
                's_width'                   => 'Width',
                's_panel_background'        => 'Title area Background',
                's_panel_border_color'      => 'Chat border color',
                's_tab_template'            => 'Template',
                's_position'                => 'Position',
                's_position_left'           => 'Left',
                's_position_right'          => 'Right',
                's_status'                  => 'Chat status',
                's_status_1'                => 'Chat online',
                's_status_2'                => 'Chat hidden',
                's_status_3'                => 'Chat offline',

                's_btn_finish_background'   => 'Finish button background',
                's_btn_finish_color'        => 'Finish button color',
                's_btn_finish_border_color' => 'Finish button border color',
                's_btn_expand_background'   => 'Expand button background',

                's_body_background'         => 'Message area background',
                's_admin_signature_color'   => 'Admin signature color',
                's_admin_text_color'        => 'Admin text color',
                's_user_signature_color'    => 'User signature color',
                's_user_text_color'         => 'User text color',
                's_time_color'              => 'Message time color',
                's_message_border_color'    => 'Message border color',

                's_write_panel_background'  => 'Write area background',
                's_write_area_background'   => 'Write box background',
                's_write_area_color'        => 'Write box color',

                's_tab_text'                => 'Text',
                's_admin_signature'         => 'Admin Signature',
                's_user_signature'          => 'User Signature',
                's_hello_message'           => '"Hello" message in online mode',
                's_panel_title'             => 'Panel title',
                's_enter_text_placeholder'  => 'Enter text placeholder',
                's_btn_finish_text'         => 'Finish button label',
                's_thank_message'           => '"Thank" message',
                's_offline_message'         => '"Offline hello" message',
                's_email_label'             => 'Email',
                's_name_label'              => 'Name',
                's_message_label'           => 'Message label',
                's_send_email'              => 'Send email button',
                's_offline_thank_message'   => '"Offline thank" message',

                's_tab_auth'                => 'Authorization',
                's_tab_auth_desc'           => '<b>If you NOT registered at [secure_admin_url]</b>, enter your email and password to use as your Account Data for authorization on [secure_admin_url].<br /><br /><b>If you already have an account at [secure_admin_url]</b> and you want to Sign-In, so please, enter your registered credential data (email and password twice). ',
                's_personal_key'            => 'Plugin key',
                's_personal_key_desc'       => 'ATTENTION!!! This key should be the same as in admin panel of site',

                's_tab_registration'    => 'Chat Registration and Activation',
                's_auth_email'          => 'Login (E-mail)',
                's_auth_password1'      => 'Password',
                's_auth_password2'      => 'Confirm password',
                's_auth_register_btn'   => 'Register & Activate',
                's_auth_login_btn'      => 'Login',

                's_tab_plugin_status'=> 'Status of chat',

                //answers
                'answer_server_not_answer'          => 'Could not connect to server',
                'answer_incorrect_data'             => 'Form was filed incorrectly',
                'answer_incorrect_url'              => 'Incorrect url of domain',
                'answer_this_username_busy'         => 'Username is busy or incorrect',
                'answer_login_ok'                   => 'Login successful',
                'answer_user_not_found'             => 'Username not found',
                'answer_incorrect_login_or_password'=> 'Incorrect username or password'
            )
        );

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

            //offline message
            $text   = @strip_tags(trim($_POST['text']));
            $name   = @strip_tags(trim($_POST['name']));
            $email  = @strip_tags(trim($_POST['email']));

            if( empty($mode) or empty($user_hash) or !in_array($mode, array('add', 'read', 'finish', 'send_email')) ){
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

            //send message
            if( $mode == 'send_email' and !empty($user_hash) and !empty($user_ip) and !empty($user_browser) and !empty($text) and !empty($name) and !empty($email) ){
                $sendRes = LiveChatsApi::send_offline_messages($user_hash, $user_ip, $user_browser, $text, $name, $email, $message_created, $message_page);

                print json_encode(array('result' => $sendRes));exit;
            }

            exit;
        }

        /**
         * Init chat. Should be started at all pages on frontend
         */
        public static function wp_head(){
            $personalKey    = (string)get_option(LiveChatsApi::$optionKey, '');
            if(empty($personalKey)){return true;}//disable for unconnected plugin

            //settings
            $options  = self::plugin_options( 'get' );
            if( (int)$options['status'] == 2){return true;}

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
                    'cookie_prefix' => self::$cookiePrefix,
                    'tag_prefix'    => self::$tag_prefix,
                    'sound_path'    => plugins_url(LiveChatsApi::$plugin_name . '/assets'),
                );
                foreach($options as $k_op => $v_op){
                    if( isset(self::$defaultOptions[$k_op]) and !empty(self::$defaultOptions[$k_op]['js_block']) ){
                        $js_parameters[self::$defaultOptions[$k_op]['js_block']][$k_op] = $v_op;
                    }
                }
                $js_parameters['text']['powered_by'] = 'Powered by';
                wp_localize_script(LiveChatsApi::$plugin_name.'_js', 'livechats_parameters', $js_parameters);

                //run
                wp_enqueue_script( 'jquery' );
                wp_enqueue_script( LiveChatsApi::$plugin_name.'_js' );
                wp_enqueue_style( LiveChatsApi::$plugin_name.'_css' );


                ob_start();// Prevent output before cookies
            }
        }

        //work with options of plugin
        public static function plugin_options($mode = 'add', $options = array()){
            if(empty($options)){
                $options = array();
                foreach(self::$defaultOptions as $k_op => $v_op){
                    $options[$k_op] = $v_op['value'];
                }
            }

            if($mode == 'add'){
                update_option(self::$optionParameters, $options);
            }

            if( $mode == 'get' ){
                $optionValue = get_option(self::$optionParameters, $options);
                foreach(self::$defaultOptions as $k_op => $v_op){
                    $optionValue[$k_op] = (empty($optionValue[$k_op]) ? $v_op['value'] : $optionValue[$k_op]);
                }
                return $optionValue;
            }

            if( $mode == 'remove' ){
                delete_option( LiveChatsApi::$optionKey );
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

        //Settings
        public static function settings_page(){
            $pluginPath = plugins_url(LiveChatsApi::$plugin_name . '/assets');
            wp_register_script( LiveChatsApi::$plugin_name.'_js', $pluginPath.'/jquery.minicolors.min.js', array(), LiveChatsApi::$plugin_version, false);
            wp_register_style( LiveChatsApi::$plugin_name.'_css', $pluginPath.'/jquery.minicolors.css' );
            wp_register_style( LiveChatsApi::$plugin_name.'_css1', $pluginPath.'/adm_css.css' );

            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( LiveChatsApi::$plugin_name.'_js' );
            wp_enqueue_style( LiveChatsApi::$plugin_name.'_css' );
            wp_enqueue_style( LiveChatsApi::$plugin_name.'_css1' );

            $options        = self::plugin_options( 'get' );
            $personalKey    = (string)get_option(LiveChatsApi::$optionKey, '');

            ?>
            <div class="wrap" id="livechats_settings_page_over">
                <h2><?php echo self::translate('page_settings_title');?></h2>
                <?php if(!empty($_GET['m']) ){ ?>
                <div class="livechats_answer_msg"><?php echo esc_html(base64_decode($_GET['m']));?></div>
                <?php } ?>

                <div id="livechats_settings_page">
                    <form method="post" action="<?php echo admin_url( 'admin-post.php?action=send_auth' )?>">
                        <div id="livechats_auth_tab" class="tab">
                            <table class="form-table">
                                <tr class="row_title">
                                    <th scope="row" colspan="3"><?php echo self::translate('s_tab_registration');?></th>
                                </tr>
                                <tr>
                                    <th scope="row"><span class="label"><?php echo self::translate('s_auth_email');?>:</span></th>
                                    <td><input style="float:left;" <?php echo (!empty($options['auth_email']) ? 'readonly="readonly"' : '');?> type="text" name="<?php echo self::$optionParameters;?>[auth_email]" value="<?php echo @esc_attr($options['auth_email']);?>" /></td>
                                    <?php if(empty($personalKey)){ ?>
                                    <td rowspan="3">
                                        <div class="full_td_desc"><?php echo str_replace('[secure_admin_url]', '<a target="_blank" href="'.LiveChatsApi::$sitePublic.'">'.str_replace('http://','',LiveChatsApi::$sitePublic).'</a>', self::translate('s_tab_auth_desc'));?></div>
                                    </td>
                                    <?php } ?>
                                </tr>
                                <tr>
                                    <th scope="row"><span class="label"><?php echo self::translate('s_auth_password1');?>:</span></th>
                                    <td><input style="float:left;" type="password" name="<?php echo self::$optionParameters;?>[auth_password1]" value="<?php echo esc_attr('');?>" /></td>
                                </tr>
                                <?php if(empty($personalKey)){ ?>
                                <tr>
                                    <th scope="row"><span class="label"><?php echo self::translate('s_auth_password2');?>:</span></th>
                                    <td><input style="float:left;" type="password" name="<?php echo self::$optionParameters;?>[auth_password2]" value="<?php echo esc_attr('');?>" /></td>
                                </tr>
                                <?php } ?>
                                <tr>
                                    <th scope="row"></th>
                                    <td><input id="register_but" type="submit" value="<?php echo (empty($personalKey) ? self::translate('s_auth_register_btn') : self::translate('s_auth_login_btn'));?>" /></td>
                                </tr>
                            </table>
                        </div>
                        <input type="hidden" name="<?php echo self::$optionParameters;?>[auth_process]" value="1" />
                    </form>

                    <div style="float:left;width:100%;height: 0px;">&nbsp;</div>

                    <form method="post" action="options.php">
                    <?php settings_fields( self::$optionParameters );?>
                    <div id="livechats_text_tab" class="tab">
                        <table class="form-table">
                            <tr class="row_title">
                                <th scope="row" colspan="2"><?php echo self::translate('s_tab_text');?></th>
                            </tr>
                            <?php
                            foreach(self::$defaultOptions as $k_op => $v_op){
                                if($v_op['js_block'] == 'text'){
                            ?>
                                <tr>
                                    <th style="width: 230px;" scope="row"><?php echo self::translate('s_'.$k_op);?>:</th>
                                    <td><textarea name="<?php echo self::$optionParameters;?>[<?php echo $k_op;?>]"><?php echo esc_attr( @$options[$k_op] ); ?></textarea></td>
                                </tr>
                            <?php
                                }
                            }
                            ?>
                        </table>
                    </div>

                    <div id="livechats_color_tab" class="tab">
                        <table class="form-table">
                            <tr class="row_title">
                                <th scope="row" colspan="2"><?php echo self::translate('s_tab_color');?></th>
                            </tr>
                            <?php
                            foreach(self::$defaultOptions as $k_op => $v_op){
                                if($v_op['js_block'] == 'color'){
                                    ?>
                                    <tr>
                                        <th scope="row"><?php echo self::translate('s_'.$k_op);?>:</th>
                                        <td style="text-align: right;"><input class="s_colorpicker" type="text" name="<?php echo self::$optionParameters;?>[<?php echo $k_op;?>]" value="<?php echo esc_attr( @$options[$k_op] ); ?>" /></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </table>
                    </div>

                    <div id="livechats_template_tab" class="tab">
                        <table class="form-table">
                            <tr class="row_title">
                                <th scope="row" colspan="6"><?php echo self::translate('s_tab_template');?></th>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo self::translate('s_width');?>:</th>
                                <td>
                                    <input style="width:90px;" type="text" name="<?php echo self::$optionParameters;?>[width]" value="<?php echo esc_attr( @$options['width'] ); ?>" />
                                    <span style="float: left;line-height: 28px;">px</span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo self::translate('s_position');?>:</th>
                                <td>
                                    <select style="width: 185px;" name="<?php echo self::$optionParameters;?>[position]">
                                        <option <?php echo ( (empty($options['position']) or @$options['position'] == 'right') ? 'selected="selected"' : '');?> value="right"><?php echo self::translate('s_position_right');?></option>
                                        <option <?php echo ( @$options['position'] == 'left' ? 'selected="selected"' : '');?> value="left"><?php echo self::translate('s_position_left');?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo self::translate('s_status');?>:</th>
                                <td>
                                    <select style="width: 185px;" name="<?php echo self::$optionParameters;?>[status]">
                                        <option <?php echo ( @(int)$options['status'] == 1 ? 'selected="selected"' : '');?> value="1"><?php echo self::translate('s_status_1');?></option>
                                        <option <?php echo ( @(int)$options['status'] == 3 ? 'selected="selected"' : '');?> value="3"><?php echo self::translate('s_status_3');?></option>
                                        <option <?php echo ( @(int)$options['status'] == 2 ? 'selected="selected"' : '');?> value="2"><?php echo self::translate('s_status_2');?></option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <?php submit_button(); ?>
                    </form>
                </div>
            </div>
            <script>
                jQuery(document).ready( function() {
                    jQuery.each(jQuery('.s_colorpicker'), function() {
                        jQuery(this).minicolors({
                            defaultValue    : jQuery(this).attr('data-defaultValue') || '',
                            letterCase      : jQuery(this).attr('data-letterCase') || 'lowercase',
                            inline          : jQuery(this).attr('data-inline') === 'true',
                            position        : jQuery(this).attr('data-position') || 'bottom left',
                            theme           : 'default'
                        });
                    });
                })
            </script>
            <?php
        }

        //Process auth from from plugin
        public static function send_auth(){
            $personalKey    = (string)get_option(LiveChatsApi::$optionKey, '');

            $authEmail      = @trim($_POST[self::$optionParameters]['auth_email']);
            $authPassword1  = @trim($_POST[self::$optionParameters]['auth_password1']);
            $authPassword2  = @trim($_POST[self::$optionParameters]['auth_password2']);
            $auth_process   = @(int)$_POST[self::$optionParameters]['auth_process'];
            $processRes     = array();

            if($auth_process){
                if(empty($personalKey)){
                    //registration
                    if( !empty($authEmail) and !empty($authPassword1) and !empty($authPassword2) and $authPassword1 == $authPassword2 ){
                        $processRes = LiveChatsApi::send_auth('register', array('username' => $authEmail, 'password' => $authPassword1));
                    }else{
                        $processRes = array('status' => 0, 'msg' => self::translate('answer_incorrect_login_or_password'));
                    }
                }else{
                    //authorization
                    if( !empty($authEmail) and !empty($authPassword1) ){
                        $processRes = LiveChatsApi::send_auth('login', array('username' => $authEmail, 'password' => $authPassword1));
                    }else{
                        $processRes = array('status' => 0, 'msg' => self::translate('answer_incorrect_login_or_password'));
                    }
                }
                if( !empty($processRes['redirect_url']) ){
                    wp_redirect($processRes['redirect_url']);exit;
                }else{
                    wp_redirect(site_url().self::$setting_page_url.'&m='.base64_encode($processRes['msg']));exit;
                }
            }
        }

        public static function validate_settings($input, $sendOptions = 1){
            $input = array_map('trim',$input);

            //filter
            $inputTmp = array();
            foreach(self::$defaultOptions as $k_item => $v_item){
                $inputTmp[$k_item] = @$input[$k_item];
                //hex_color validator
                if($v_item['filter'] == 'hex_color'){
                    $inputTmp[$k_item] = self::validate_hex_color($inputTmp[$k_item]);
                }
                //text validator
                if($v_item['filter'] == 'text'){
                    $inputTmp[$k_item] = self::validate_text($inputTmp[$k_item]);
                }
                //int validator
                if($v_item['filter'] == 'int'){
                    $inputTmp[$k_item] = (int)$inputTmp[$k_item];
                }
                //other validators
                if(empty($v_item['filter']) and $k_item == 'c_position'){
                    $inputTmp[$k_item] = (!in_array($inputTmp[$k_item], array('right', 'left')) ? '' : $inputTmp[$k_item]);
                }
                if(empty($v_item['filter']) and $k_item == 'c_status'){
                    $inputTmp[$k_item] = (!in_array((int)$inputTmp[$k_item], array(1, 2, 3)) ? '' : $inputTmp[$k_item]);
                }

                //check if empty
                $inputTmp[$k_item] = (empty($inputTmp[$k_item]) ? $v_item['value'] : $inputTmp[$k_item]);
            }
            $input = $inputTmp;

            //do not change login of user here
            if( empty($input['auth_email']) ){
                $options = self::plugin_options('get');
                $input['auth_email'] = $options['auth_email'];
            }

            //send options to server
            if($sendOptions == 1){
                LiveChatsApi::send_options($input);
            }

            return $input;
        }

        public static function validate_hex_color($hex_color = ''){
            $hex_color = trim($hex_color);
            if( empty($hex_color) or !preg_match('/^#[a-f0-9]{6}$/i', $hex_color) ){return '';}
            return $hex_color;
        }

        public static function validate_text($text){
            $text = trim($text);
            $text = strip_tags($text);
            $text = trim($text);
            return $text;
        }

        public static function translate($var, $lang = 'en'){
            $text = $var;
            if( isset(self::$translation[$lang]) ){
                if( isset(self::$translation[$lang][$var]) ){
                    $text = self::$translation[$lang][$var];
                }elseif( isset(self::$translation['en'][$var]) ){
                    $text =  self::$translation['en'][$var];
                }
            }

            return $text;
        }

        /**
         * Notice for user. Only in admin panel
         */
        public static function admin_notices(){
            if(is_admin()) {
                /*
                global $status, $page, $s;
                $context    = $status;
                $plugin     = LiveChatsApi::$plugin_name.'/'.LiveChatsApi::$plugin_name.'.php';
                $nonce      = wp_create_nonce('deactivate-plugin_' . $plugin);
                $actions    = 'plugins.php?action=deactivate&amp;plugin=' . urlencode($plugin) . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s  . '&amp;_wpnonce=' . $nonce;

                $personaKey     = (string)get_option(LiveChatsApi::$optionKey, '');
                $pluginStatus   = is_plugin_active($plugin);
                if( !empty($personaKey) or !$pluginStatus ){return;}

                echo '<div style="height:50px;line-height:50px;font-size:16px;font-weight:bold;" class="notice-warning notice">To use "live-chats" plugin, please add this site to <a target="_blank" href="'.LiveChatsApi::$sitePublic.'">your account</a> at '.str_replace('http://','',LiveChatsApi::$sitePublic).' or <a href="'.$actions.'">deactivate</a> Live Chats plugin.</div>';
                */
            }
        }

        public static function admin_init(){
            if (get_option(self::$optionParameters.'_redirect', false)) {
                delete_option(self::$optionParameters.'_redirect');
                wp_redirect(site_url().self::$setting_page_url);exit;
            }
        }

        public static function admin_menu(){
            if(is_admin()) {
                //settings menu for admin
                add_menu_page('Live Chat Online', 'Live Chat Online', 'manage_options', 'live_chat_online_page', array('LiveChats', 'settings_page'));
                add_action( 'admin_init', array('LiveChats', 'register_settings') );
            }
        }

        public static function register_settings(){
            register_setting( self::$optionParameters, self::$optionParameters, array('LiveChats', 'validate_settings') );
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

            //add flag for redirect
            add_option(self::$optionParameters.'_redirect', true);
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

        public static $plugin_name      = 'live-chat-online';
        public static $plugin_version   = '1.1.0';
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
            9 => array('status' => 1, 'msg' => 'update options success'),
            10 => array('status' => 0, 'msg' => 'update options failed')
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
        public static function requestServer($args, $returnResult = 0){
            $postRes = wp_remote_post( self::$siteSecureAction, $args );
            if ( is_wp_error( $postRes ) ) {
                $error = array( 'wp_error' => $postRes->get_error_message() );
                return ($returnResult ? $postRes : false);
            }

            return ($returnResult ? $postRes : true);
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

                'plugin'            => self::$plugin_name,
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

                'plugin'            => self::$plugin_name,
                'plugin_version'    => LiveChatsApi::$plugin_version,
                'domain'            => site_url()
            );
            $requestVars = array('body' => array(LiveChatsApi::$tagAnswer => LiveChatsApi::convertString($parameters,'encode')));

            return LiveChatsApi::requestServer( $requestVars );
        }

        //Send settings from plugin
        public static function send_options($options){
            $personalKey = (string)get_option(LiveChatsApi::$optionKey, '');
            if(empty($personalKey)){return false;}

            $data = array(
                'action'            => 'update_options',
                'key'               => $personalKey,
                'options'           => json_encode($options),
                'plugin'            => self::$plugin_name,
                'plugin_version'    => self::$plugin_version,
                'domain'            => site_url()
            );
            $requestVars = array('body' => array(LiveChatsApi::$tagAnswer => LiveChatsApi::convertString($data,'encode')));

            return LiveChatsApi::requestServer( $requestVars );
        }

        //Send offline message from user
        public static function send_offline_messages($hash, $ip, $browser, $text, $name, $email, $created, $message_page){
            $personalKey = (string)get_option(LiveChatsApi::$optionKey, '');
            if(empty($personalKey)){return false;}

            $data = array(
                'action'            => 'offline_message',
                'key'               => $personalKey,
                'hash'              => $hash,
                'ip'                => $ip,
                'browser'           => $browser,
                'message'           => $text,
                'user_name'         => $name,
                'user_email'        => $email,
                'message_page'      => $message_page,
                'created'           => $created,

                'plugin'            => self::$plugin_name,
                'plugin_version'    => self::$plugin_version,
                'domain'            => site_url()
            );
            $requestVars = array('body' => array(LiveChatsApi::$tagAnswer => LiveChatsApi::convertString($data,'encode')));

            return LiveChatsApi::requestServer( $requestVars );
        }

        //Send auth parameters. Registration/authorization/pre-registration
        public static function send_auth($mode, $vars = array()){
            $personalKey    = (string)get_option(LiveChatsApi::$optionKey, '');
            $personalKey    = (empty($personalKey) ? md5(time().rand(1,9999).microtime().rand(1,9999)) : $personalKey );
            $site           = site_url();
            $data           = array();

            if($mode == 'check'){
                $data = array(
                    'action'            => 'auth_check',
                    'plugin'            => self::$plugin_name,
                    'plugin_version'    => self::$plugin_version,
                    'domain'            => site_url()
                );
            }
            if($mode == 'register'){
                $data = array(
                    'action'            => 'auth_register',
                    'key'               => $personalKey,
                    'username'          => $vars['username'],
                    'password'          => $vars['password'],
                    'plugin'            => self::$plugin_name,
                    'plugin_version'    => self::$plugin_version,
                    'domain'            => site_url()
                );
            }
            if($mode == 'login'){
                $data = array(
                    'action'            => 'auth_login',
                    'key'               => $personalKey,
                    'username'          => $vars['username'],
                    'password'          => $vars['password'],
                    'plugin'            => self::$plugin_name,
                    'plugin_version'    => self::$plugin_version,
                    'domain'            => site_url()
                );
            }

            $requestVars    = array('body' => array(LiveChatsApi::$tagAnswer => LiveChatsApi::convertString($data,'encode')));
            $requestAnswer  =  LiveChatsApi::requestServer( $requestVars, 1 );

            $answerBody     = @trim($requestAnswer['body']);
            $answerBodyData = array();
            if( !empty($answerBody) ){
                $answerBodyData = self::convertString($answerBody,'decode');
            }

            if(@(int)$answerBodyData['status'] == 1){
                //save key
                add_option(self::$optionKey, $personalKey);

                //save username of user
                $options = LiveChats::plugin_options('get');
                $options['auth_email'] = $vars['username'];
                LiveChats::plugin_options('add',$options);

            }

            return array(
                'status'        => @(int)$answerBodyData['msg'],
                'msg'           => LiveChats::translate( (empty($answerBodyData['msg']) ? 'answer_server_not_answer' : $answerBodyData['msg']) ),
                'redirect_url'  => @$answerBodyData['redirect_url']
            );
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
            if( LiveChats::add_messages($user_hash, $user_ip, '', $message, $created, '', 1) ){
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
        protected static function action_update_options($data){
            $options = @(array)json_decode($data['options'],1);
            unset($options['personal_key']);
            if( !empty($options) ){
                $options = LiveChats::validate_settings($options, 0);
            }
            if( !empty($options) ){
                LiveChats::plugin_options('add',$options);
                self::printAnswer(9);
            }

            self::printAnswer(10);
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
add_action( 'admin_menu'                    , array( 'LiveChats', 'admin_menu'));       //menu for admin
add_action( 'admin_notices'                 , array( 'LiveChats', 'admin_notices') );   //notice for admin
add_action( 'admin_init'                    , array( 'LiveChats', 'admin_init') );      //uses for redirect after activation
add_action( 'admin_post_send_auth'          , array( 'LiveChats', 'send_auth') );       //auth from plugin
add_action( 'wp_ajax_LiveChatsAjax'         , array( 'LiveChats', 'plugin_ajax' ) );    //listening ajax request
add_action( 'wp_ajax_nopriv_LiveChatsAjax'  , array( 'LiveChats', 'plugin_ajax' ) );    //listening ajax request

