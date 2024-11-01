/*!
 * Weblineindia modal javascript v1.0
 * http://www.weblineindia.com
 */
var create_popup = {
		time:"",
		delay_time:"",
		more_delay:"",
		winH:"",
		winW:"",
		

		config:{
			
		},
		
		init:function(){
			time = welcomePopup.popup_time;
			
			delay_time = time*1000;
			more_delay = delay_time+1000;
			winH = jQuery(window).height();
			winW = jQuery(window).width();
			//setTimeout(function(){jQuery(".popup_bg").show()},delay_time);
			
			setTimeout(function(){jQuery('#overlay').show()}, delay_time);
			
			//jQuery('.popup_bg').css('position', 'fixed');
			setTimeout(function(){jQuery('.popup_bg').fadeIn(1000).show()},more_delay);
			
			jQuery('.btn_close').click(function() {
				create_popup.hide_popup();
		     });
			
			jQuery('.display').click(function(){
				create_popup.add_cookie();
			});
			
			jQuery( ".btn_close" ).trigger('click');
			},
			
			hide_popup:function(){
				
				jQuery('.popup_bg').hide();
			//	jQuery("#gradient").hide();		
				jQuery("#overlay").hide();				
			},
			
			add_cookie:function(){				
				var myCookie = create_popup.getCookie("popup");				
				if(!myCookie){
					create_popup.setCookie("popup", "hide", 1, "/");
				}
				jQuery('.popup_bg').hide();
			//	jQuery('.popup').hide();
			//	jQuery("#gradient").hide();		
				jQuery("#overlay").hide();
			},
			
			getCookie:function(name){	
				 	var dc = document.cookie;
				    var prefix = name + "=";
				    var begin = dc.indexOf("; " + prefix);
				    if (begin == -1) {
				        begin = dc.indexOf(prefix);
				        if (begin != 0) return null;
				    }
				    else
				    {
				        begin += 2;
				        var end = document.cookie.indexOf(";", begin);
				        if (end == -1) {
				        end = dc.length;
				        }
				    }
				    return unescape(dc.substring(begin + prefix.length, end));
			},
			
			setCookie:function(name, value, expires, path){
				var cookie_string = name + "=" + escape (value) +";";
				if(expires){
					expires = create_popup.setExpiration(expires);
					cookie_string += "expires=" + expires + "; ";
				}
				if(path){
					cookie_string += "path=" + path + "; ";
				}
				 document.cookie = cookie_string;
			},

			setExpiration:function(cookieLife){
			    var today = new Date();
			    var expr = new Date(today.getTime() + cookieLife * 24 * 60 * 60 * 1000);
			    return  expr.toGMTString();
			},
};	
jQuery(document).ready(function($){
	create_popup.init();

	//Get and checked close on esc option is enabled
	if( welcomePopup.close_on_esc && welcomePopup.close_on_esc == '1' ) {
		$(document).bind('keydown', function(e) { 
	        if (e.which == 27) {
				create_popup.hide_popup();
	        }
	    });
	}
});

