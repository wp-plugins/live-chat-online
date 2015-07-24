(function($) {
    var livechats;

    $( document ).ready(function() {
        if(!livechats){
            livechats = new LiveChats();
            livechats.init();
        }
    });

    function LiveChats(){
        var thisChat = this;
        var thisChatListener;
        var ops = {
            'tagPrefix' : livechats_parameters.tag_prefix,
            'userHash' : '',
            'site_url' : livechats_parameters.site_url,
            'sound_path' : livechats_parameters.sound_path,
            'messagesHtml' : '', //html of messages
            'messages' : {}, //loaded array of messages
            'autoFinishTimeout': 900, //finish chat after 15 min without any action
            'startUpOpen': 0,    //open chat after loading
            'ajaxAction' : 'LiveChatsAjax',
            'parameters': livechats_parameters
        };
        var containers = {};

        this.init = function(mode, vars){
            var newChat = 0;

            var user_hash   = $.cookie(ops.parameters.cookie_prefix);
            ops.startUpOpen = $.cookie(ops.parameters.cookie_prefix+'_status');
            if( !user_hash || user_hash == '' ){user_hash = this.randString();}
            ops.userHash    = user_hash;
            $.cookie(ops.parameters.cookie_prefix, user_hash, {path:'/'});

            //only if chat online
            if(ops.parameters.template.status == 1) {
                this.read('user_last', 50);

                //prepare messages
                this.prepareMessages(1);

                $(document.body).append(this.prepareTemplate());
            }

            //only if chat offline
            if(ops.parameters.template.status == 3){
                $(document.body).append(this.prepareOfflineTemplate());
            }



            //containers
            containers.chat = $('.'+ops.tagPrefix+'_container');
            containers.body = $('.'+ops.tagPrefix+'_body');
            containers.title = $('.'+ops.tagPrefix+'_title');
            containers.write = $('.'+ops.tagPrefix+'_write');
            containers.textarea = containers.chat.find('textarea');
            containers.eBut = containers.chat.find('.'+ops.tagPrefix+'_btn_enter');
            containers.fBut = containers.chat.find('.'+ops.tagPrefix+'_btn_finish');
            containers.aBtn = containers.title.find('.'+ops.tagPrefix+'_btn');
            containers.sEBut = containers.chat.find('.'+ops.tagPrefix+'_btn_send_email');
            containers.userName = containers.chat.find('#'+ops.tagPrefix+'_user_name');
            containers.userEmail = containers.chat.find('#'+ops.tagPrefix+'_user_email');
            containers.userMessage = containers.chat.find('#'+ops.tagPrefix+'_user_message');

            //only if chat online
            if(ops.parameters.template.status == 1) {
                //listen new messages
                thisChat.update();

                //open chat
                if(ops.startUpOpen == 1 ){
                    this.animateChat('show');
                }
            }


            //events
            containers.textarea.on('keydown', function(e){if(e.which == 13){thisChat.send();return false;}});
            containers.eBut.on('click',function(){thisChat.send();});
            containers.fBut.on('click',function(){thisChat.finish();});
            containers.sEBut.on('click',function(){thisChat.sendEmail();});
            containers.aBtn.on('click',function(){
                if( containers.chat.hasClass('active') ){
                    thisChat.animateChat('hide');
                }else{
                    thisChat.animateChat('show');
                }
            });
        };

        this.animateChat = function(mode){
            if(mode == 'show'){
                //remove any additional class
                containers.chat.removeClass('finish');

                containers.chat.animate({
                    height: (containers.title.outerHeight() + containers.body.outerHeight() + containers.write.outerHeight() + 9)
                }, 500, function() {
                    containers.chat.addClass('active');
                    ops.startUpOpen = 1;
                    $.cookie(ops.parameters.cookie_prefix+'_status', ops.startUpOpen, {path:'/'});
                });
            }
            if(mode == 'hide'){
                containers.chat.animate({
                    height: (containers.title.outerHeight() + 7)
                }, 500, function() {
                    containers.chat.removeClass('active');
                    ops.startUpOpen = 0;
                    $.cookie(ops.parameters.cookie_prefix+'_status', ops.startUpOpen, {path:'/'});
                });
            }
        };

        this.read = function(queryMode, countMessage, successCallback, errorCallback){
            $.ajax({
                type: "POST",
                url: ops.parameters.request_url,
                data: {
                    'mode'          : 'read',
                    'action'        : ops.ajaxAction,
                    'queryMode'     : queryMode,
                    'countMessage'  : countMessage
                },
                dataType: "json",
                success: function(data){
                    if(data && data.messages.length > 0){
                        $.each(data.messages, function(ind,val){
                            ops.messages[val.message_id] = val;
                        });
                        thisChat.prepareMessages();
                    }
                    if(successCallback){
                        successCallback();
                    }
                },
                error: function(){
                    if(errorCallback){
                        successCallback();
                    }
                }
            });
        };

        this.send = function(){
            var text =  $.trim(containers.textarea.val());
            containers.textarea.val('');

            if( text != '' ){
                containers.chat.addClass('loading');

                $.ajax({
                    type: "POST",
                    url: ops.parameters.request_url,
                    data: {
                        'mode'          : 'add',
                        'action'        : ops.ajaxAction,
                        'message'       : text,
                        'message_page'  : window.location.href
                    },
                    dataType: "json",
                    cache: false,
                    success: function(data){
                        if(data && data.result == 1){
                            thisChat.read('user_last',1,function(){
                                containers.chat.removeClass('loading');

                                //play sound
                                thisChat.playSound('send');
                            });
                        }
                    }
                });
            }
        };

        this.prepareMessages = function(addDefault){
            //add default message
            if(addDefault == 1){
                this.messageTemplate(ops.parameters.text.admin_signature, ops.parameters.text.hello_message, '', '0','1');
            }

            if( Object.keys(ops.messages).length > 0 ){
                ops.messagesHtml = '';
                var newMessages = 0;
                var newAnswer = 0;

                $.each(ops.messages, function(ind,val){
                    if( containers.body.find('#message_'+val.message_id).length == 0){
                        thisChat.messageTemplate(val.name, val.text, val.time, val.message_id, val.type);
                        newMessages = 1;

                        //check answer from admin
                        if(val.type == 1){
                            newAnswer = 1;
                        }
                    }
                });

                if(newMessages == 1){
                    containers.body.append( ops.messagesHtml );
                    containers.body.scrollTop(containers.body.prop("scrollHeight"));
                }

                if(newAnswer == 1){
                    //play sound
                    thisChat.playSound('get');
                }
            }
        };

        this.randString = function() {
            var result          = '';
            var words           = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789';
            var max_position    = words.length - 1;
            var position        = 0;
            for( i = 0; i < 10; ++i ) {
                position = Math.floor ( Math.random() * max_position );
                result = result + words.substring(position, position + 1);
            }
            return result;
        };

        this.escape = function(text){
            var map = {'&': '&amp;','<': '&lt;','>': '&gt;','"': '&quot;',"'": '&#039;'};
            var text = (!text ? '' : text);
            text = text.replace(/[&<>"']/g, function(m) { return map[m]; });
            text = text.replace('&lt;br /&gt;','<br />');
            return text;
        };

        this.playSound = function(sound){
            var path    = ops.parameters.sound_path;
            var sound   = ( !sound ? 'get' : sound);
            sound = path+'/'+sound;

            $(document.body).append("<div id='"+ops.tagPrefix+"_play_sound'><embed src='"+sound+".mp3' hidden='true' autostart='true' loop='false' class='playSound'>" + "<audio autoplay='autoplay' style='display:none;' controls='controls'><source src='"+sound+".mp3' /><source src='"+sound+".wav' /></audio></div>");
            setTimeout(function(){
                $('#'+ops.tagPrefix+'_play_sound').remove();
            },1000);
        };

        this.prepareTemplate = function(){
            return '' +
                '<div id="'+ops.userHash+'" class="'+ops.tagPrefix+'_container">' +
                    '<div class="'+ops.tagPrefix+'_title">' +
                        '<span class="'+ops.tagPrefix+'_text">'+ops.parameters.text.panel_title+'</span>' +
                        '<span class="'+ops.tagPrefix+'_btn_finish">&nbsp;</span>' +
                        '<span class="'+ops.tagPrefix+'_btn">&nbsp;</span>' +
                    '</div>' +
                    '<div class="'+ops.tagPrefix+'_body_over"><div class="'+ops.tagPrefix+'_body">'+ops.messagesHtml+'</div></div>' +
                    '<div class="'+ops.tagPrefix+'_write">' +
                        '<div class="'+ops.tagPrefix+'_top_write">' +
                            '<span class="'+ops.tagPrefix+'_preloader">&nbsp;</span>' +
                        '</div>' +
                        '<div class="'+ops.tagPrefix+'_middle_write">' +
                            '<textarea placeholder="'+ops.parameters.text.hello_message+'"></textarea>' +
                            '<span class="'+ops.tagPrefix+'_btn_enter">&nbsp;</span>' +
                        '</div>' +
                        '<div class="'+ops.tagPrefix+'_bottom_write"><a target="_blank" href="http://realtime-chat.com">'+ops.parameters.text.powered_by+' <b>rtc</b></a></div>' +
                    '</div>' +
                '</div>' +
                this.prepareTemplateCss() +
            '';
        };

        this.messageTemplate = function(name, text, time, id, type){
            var id      = (!id ? '' : id);
            var type    = (!type ? 0 : type);
            var time = (!time ? '' : time);
            var text = (!text ? '' : text);
            var name = (!name ? ops.parameters.text.user_name : name);
            ops.messagesHtml += '<div id="message_'+id+'" class="'+ops.tagPrefix+'_message message_type_'+type+'">' +
                '<span class="'+ops.tagPrefix+'_name">'+thisChat.escape(name)+'</span>' +
                '<span class="'+ops.tagPrefix+'_time">'+thisChat.escape(time)+'</span>' +
                '<span class="'+ops.tagPrefix+'_text">'+thisChat.escape(text)+'</span>' +
            '</div>';
        };

        this.prepareOfflineTemplate = function(){
            return '' +
                '<div id="'+ops.userHash+'" class="'+ops.tagPrefix+'_container">' +
                    '<div class="'+ops.tagPrefix+'_title">' +
                        '<span class="'+ops.tagPrefix+'_text">'+ops.parameters.text.panel_title+'</span>' +
                        '<span class="'+ops.tagPrefix+'_btn_finish">&nbsp;</span>' +
                        '<span class="'+ops.tagPrefix+'_btn">&nbsp;</span>' +
                    '</div>' +
                    '<div class="'+ops.tagPrefix+'_body_over">' +
                        '<div class="'+ops.tagPrefix+'_body">' +
                            '<div class="'+ops.tagPrefix+'_body_line hi">'+ops.parameters.text.offline_message+'</div>' +
                            '<div class="'+ops.tagPrefix+'_body_line thanks">'+ops.parameters.text.offline_thank_message+'</div>' +
                            '<div class="'+ops.tagPrefix+'_body_line">' +
                                '<div class="'+ops.tagPrefix+'_body_line_label">'+ops.parameters.text.name_label+'</div>' +
                                '<div class="'+ops.tagPrefix+'_body_line_input"><input type="text" id="'+ops.tagPrefix+'_user_name" /></div>' +
                            '</div>' +
                            '<div class="'+ops.tagPrefix+'_body_line">' +
                                '<div class="'+ops.tagPrefix+'_body_line_label">'+ops.parameters.text.email_label+'</div>' +
                                '<div class="'+ops.tagPrefix+'_body_line_input"><input type="text" id="'+ops.tagPrefix+'_user_email" /></div>' +
                            '</div>' +
                            '<div class="'+ops.tagPrefix+'_body_line">' +
                                '<div class="'+ops.tagPrefix+'_body_line_label">'+ops.parameters.text.message_label+'</div>' +
                                '<div class="'+ops.tagPrefix+'_body_line_input"><textarea id="'+ops.tagPrefix+'_user_message"></textarea></div>' +
                            '</div>' +
                            '<div class="'+ops.tagPrefix+'_body_line center">' +
                                '<span class="'+ops.tagPrefix+'_btn_send_email">'+ops.parameters.text.send_email+'</span>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="'+ops.tagPrefix+'_bottom_write"><a target="_blank" href="http://realtime-chat.com">'+ops.parameters.text.powered_by+' <b>rtc</b></a></div>' +
                    '</div>' +
                '</div>' +
                this.prepareTemplateCss() +
                '';
        };

        this.prepareTemplateCss = function(){
            var style = '' +
                '<style>' +
                '.'+ops.tagPrefix+'_container{background:'+ops.parameters.color.panel_background+';border-color:'+ops.parameters.color.panel_border_color+';width:'+ops.parameters.template.width+'px;'+ops.parameters.template.position+':40px;}' +
                '.'+ops.tagPrefix+'_title{background:'+ops.parameters.color.panel_background+';width:'+(ops.parameters.template.width-34)+'px;}' +
                '.'+ops.tagPrefix+'_body_over{background:'+ops.parameters.color.panel_background+';}' +
                '.'+ops.tagPrefix+'_body{background:'+ops.parameters.color.body_background+';width:'+(ops.parameters.template.width-34)+'px;}' +
                '.'+ops.tagPrefix+'_btn_finish{background-color:'+ops.parameters.color.btn_finish_background+';color:'+ops.parameters.color.btn_finish_color+';border-color:'+ops.parameters.color.btn_finish_border_color+';}' +
                '.'+ops.tagPrefix+'_title .'+ops.tagPrefix+'_btn{background-color:'+ops.parameters.color.btn_expand_background+';border-color:'+ops.parameters.color.btn_finish_border_color+';}' +
                '.'+ops.tagPrefix+'_message.message_type_1 .'+ops.tagPrefix+'_name{color:'+ops.parameters.color.admin_signature_color+';}' +
                '.'+ops.tagPrefix+'_message.message_type_1 .'+ops.tagPrefix+'_text{color:'+ops.parameters.color.admin_text_color+';}' +
                '.'+ops.tagPrefix+'_message.message_type_0 .'+ops.tagPrefix+'_name{color:'+ops.parameters.color.user_signature_color+';}' +
                '.'+ops.tagPrefix+'_message.message_type_0 .'+ops.tagPrefix+'_text{color:'+ops.parameters.color.user_text_color+';}' +
                '.'+ops.tagPrefix+'_message .'+ops.tagPrefix+'_time{color:'+ops.parameters.color.time_color+';}' +
                '.'+ops.tagPrefix+'_message{border-color:'+ops.parameters.color.message_border_color+';}' +
                '.'+ops.tagPrefix+'_top_write, .'+ops.tagPrefix+'_bottom_write{background-color:'+ops.parameters.color.write_panel_background+';border-color:'+ops.parameters.color.write_panel_background+';}' +
                '.'+ops.tagPrefix+'_middle_write textarea{background:'+ops.parameters.color.write_area_background+';color:'+ops.parameters.color.write_area_color+';width:'+(ops.parameters.template.width-10)+'px;}' +

                '#'+ops.tagPrefix+'_user_name, #'+ops.tagPrefix+'_user_email, #'+ops.tagPrefix+'_user_message{background:'+ops.parameters.color.write_area_background+';color:'+ops.parameters.color.write_area_color+';width:'+(ops.parameters.template.width-10)+'px;}' +
                '.'+ops.tagPrefix+'_btn_send_email{background:'+ops.parameters.color.btn_finish_background+';color:'+ops.parameters.color.btn_finish_color+';border-color:'+ops.parameters.color.btn_finish_border_color+';}' +
                '</style>';

            return style;
        };

        this.finish = function(){
            //remove all messages from chat
            $.each(containers.body.children(),function(ind,val){
                if( ind > 0){
                    $(val).remove();
                }
            });

            thisChat.animateChat('hide');

            //remove all messages from db
            $.ajax({
                type: "POST",
                url: ops.parameters.request_url,
                data: {
                    'mode'          : 'finish',
                    'action'        : ops.ajaxAction,
                    'message_page'  : window.location.href
                },
                dataType: "json",
                cache: false,
                success: function(data){
                    //set new hash
                    ops.userHash = thisChat.randString();
                    $.cookie(ops.parameters.cookie_prefix, ops.userHash);

                    ops.messages = {};
                }
            });

            //close chat
        };

        this.sendEmail = function(){

            var text    =  $.trim(containers.userMessage.val());
            var name    =  $.trim(containers.userName.val());
            var email   =  $.trim(containers.userEmail.val());

            //send email
            if(text != '' && name != '' && email != ''){
                $.ajax({
                    type: "POST",
                    url: livechats_parameters.request_url,
                    data: {
                        'mode'          : 'send_email',
                        'action'        : ops.ajaxAction,
                        'message_page'  : window.location.href,
                        'text'          : text,
                        'name'          : name,
                        'email'         : email
                    },
                    dataType: "json",
                    cache: false,
                    success: function(data){
                        //set new hash
                        ops.chatHash = thisChat.randString();
                        $.cookie(livechats_parameters.cookie_prefix, ops.chatHash);

                        containers.chat.addClass('finish');

                        containers.userMessage.val('');
                        containers.userName.val('');
                        containers.userEmail.val('');

                        //close chat
                        setTimeout(function(){
                            thisChat.animateChat('close');
                        }, 3000)
                    }
                });
            }
        };

        this.update = function(){
            var pause = (ops.startUpOpen == 1 ? 2000 : 15000);
            thisChatListener = setTimeout(function(){
                thisChat.read('user_last',10,function(){thisChat.update()});
            },pause);
        }
    }
})(jQuery);

//cookie
(function ($) {
    jQuery.cookie = function(name, value, options) {
        if (typeof value != 'undefined') { // name and value given, set cookie
            options = options || {};
            if (value === null) {
                value = '';
                options = $.extend({}, options); // clone object since it's unexpected behavior if the expired property were changed
                options.expires = -1;
            }
            var expires = '';
            if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
                var date;
                if (typeof options.expires == 'number') {
                    date = new Date();
                    date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
                } else {
                    date = options.expires;
                }
                expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
            }
            // NOTE Needed to parenthesize options.path and options.domain
            // in the following expressions, otherwise they evaluate to undefined
            // in the packed version for some reason...
            var path = options.path ? '; path=' + (options.path) : '';
            var domain = options.domain ? '; domain=' + (options.domain) : '';
            var secure = options.secure ? '; secure' : '';
            document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
        } else { // only name given, get cookie
            var cookieValue = null;
            if (document.cookie && document.cookie != '') {
                var cookies = document.cookie.split(';');
                for (var i = 0; i < cookies.length; i++) {
                    var cookie = jQuery.trim(cookies[i]);
                    // Does this cookie string begin with the name we want?
                    if (cookie.substring(0, name.length + 1) == (name + '=')) {
                        cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                        break;
                    }
                }
            }
            return cookieValue;
        }
    };
})(jQuery);

//resize
(function($){
    $.fn.jqDrag=function(h){return i(this,h,'d');};
    $.fn.jqResize=function(h){return i(this,h,'r');};
    $.jqDnR={dnr:{},e:0,
        drag:function(v){
            if(M.k == 'd')E.css({left:M.X+v.pageX-M.pX,top:M.Y+v.pageY-M.pY});
            else E.css({width:Math.max(v.pageX-M.pX+M.W,0),height:Math.max(v.pageY-M.pY+M.H,0)});
            return false;},
        stop:function(){E.css('opacity',M.o);$().unbind('mousemove',J.drag).unbind('mouseup',J.stop);}
    };
    var J=$.jqDnR,M=J.dnr,E=J.e,
        i=function(e,h,k){return e.each(function(){h=(h)?$(h,e):e;
            h.bind('mousedown',{e:e,k:k},function(v){var d=v.data,p={};E=d.e;
                // attempt utilization of dimensions plugin to fix IE issues
                if(E.css('position') != 'relative'){try{E.position(p);}catch(e){}}
                M={X:p.left||f('left')||0,Y:p.top||f('top')||0,W:f('width')||E[0].scrollWidth||0,H:f('height')||E[0].scrollHeight||0,pX:v.pageX,pY:v.pageY,k:d.k,o:E.css('opacity')};
                E.css({opacity:0.8});$().mousemove($.jqDnR.drag).mouseup($.jqDnR.stop);
                return false;
            });
        });},
        f=function(k){return parseInt(E.css(k))||false;};
})(jQuery);