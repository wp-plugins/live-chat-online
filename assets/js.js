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
            'tagPrefix' : 'livechats',
            'userHash'  : '',
            'translation' : {
                'title'         : 'Need help?',
                'first_message' : 'Put your message here ...',
                'finish'        : 'Finish',
                'user_name'     : 'You',
                'powered_by'    : 'Powered by'
            },
            'admin_name': 'Customer support',
            'start_message': 'How can we help you?',
            'user_name': 'You',
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

            this.read('user_last', 50);

            //prepare messages
            this.prepareMessages(1);

            $(document.body).append(this.prepareTemplate());

            //containers
            containers.chat = $('.'+ops.tagPrefix+'_container');
            containers.body = $('.'+ops.tagPrefix+'_body');
            containers.title = $('.'+ops.tagPrefix+'_title');
            containers.write = $('.'+ops.tagPrefix+'_write');
            containers.textarea = containers.chat.find('textarea');
            containers.eBut = containers.chat.find('.'+ops.tagPrefix+'_btn_enter');
            containers.fBut = containers.chat.find('.'+ops.tagPrefix+'_btn_finish');
            containers.aBtn = containers.title.find('.'+ops.tagPrefix+'_btn');

            //listen new messages
            thisChat.update();

            //open chat
            if(ops.startUpOpen == 1 ){
                this.animateChat('show');
            }

            //events
            containers.textarea.on('keydown', function(e){if(e.which == 13){thisChat.send();return false;}});
            containers.eBut.on('click',function(){thisChat.send();});
            containers.fBut.on('click',function(){thisChat.finish();});
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
                            });
                        }
                    }
                });
            }
        };

        this.prepareMessages = function(addDefault){
            //add default message
            if(addDefault == 1){
                this.messageTmpl(ops.admin_name, ops.start_message, '', '0','1');
            }

            if( Object.keys(ops.messages).length > 0 ){
                ops.messagesHtml = '';
                var newMessages = 0;

                $.each(ops.messages, function(ind,val){
                    if( containers.body.find('#message_'+val.message_id).length == 0){
                        thisChat.messageTmpl(val.name, val.text, val.time, val.message_id, val.type);
                        newMessages = 1;
                    }
                });

                if(newMessages == 1){
                    containers.body.append( ops.messagesHtml );
                    containers.body.scrollTop(containers.body.prop("scrollHeight"));
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
            text = text.replace(/[&<>"']/g, function(m) { return map[m]; });
            text = text.replace('&lt;br /&gt;','<br />');
            return text;
        };

        this.prepareTemplate = function(){
            return '' +
                '<div id="'+ops.userHash+'" class="'+ops.tagPrefix+'_container">' +
                    '<div class="'+ops.tagPrefix+'_title">' +
                        '<span class="'+ops.tagPrefix+'_text">'+ops.translation.title+'</span>' +
                        '<span class="'+ops.tagPrefix+'_btn_finish">&nbsp;</span>' +
                        '<span class="'+ops.tagPrefix+'_btn">&nbsp;</span>' +
                    '</div>' +
                    '<div class="'+ops.tagPrefix+'_body">'+ops.messagesHtml+'</div>' +
                    '<div class="'+ops.tagPrefix+'_write">' +
                        '<div class="'+ops.tagPrefix+'_top_write">' +
                            '<span class="'+ops.tagPrefix+'_preloader">&nbsp;</span>' +
                        '</div>' +
                        '<div class="'+ops.tagPrefix+'_middle_write">' +
                            '<textarea placeholder="'+ops.translation.first_message+'"></textarea>' +
                            '<span class="'+ops.tagPrefix+'_btn_enter">&nbsp;</span>' +
                        '</div>' +
                        '<div class="'+ops.tagPrefix+'_bottom_write"><a target="_blank" href="http://realtime-chat.com">'+ops.translation.powered_by+' <b>rtc</b></a></div>' +
                    '</div>' +
                '</div>' +
            '';
        };

        this.messageTmpl = function(name, text, time, id, type){
            var id      = (!id ? '' : id);
            var type    = (!type ? 0 : type);
            var time = (!time ? '' : time);
            var text = (!text ? '' : text);
            var name = (!name ? ops.translation.user_name : name);
            ops.messagesHtml += '<div id="message_'+id+'" class="'+ops.tagPrefix+'_message message_type_'+type+'">' +
                '<span class="'+ops.tagPrefix+'_name">'+thisChat.escape(name)+'</span>' +
                '<span class="'+ops.tagPrefix+'_time">'+thisChat.escape(time)+'</span>' +
                '<span class="'+ops.tagPrefix+'_text">'+thisChat.escape(text)+'</span>' +
            '</div>';
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