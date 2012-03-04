jQuery("document").ready(function(jQuery){

	var menubar=jQuery("#menubar");
	var content=jQuery("#content");

	if ( (menubar.length == 1) && (content.length ==1)) {
		menubar.after("<div id=\"menuswitcher\">«</div>");
	
		jQuery("#menuswitcher").click(function(){
			if (jQuery("#menubar").is(":hidden")) {
				showMenu(0);
				return false;
			} else {
				hideMenu(0);
				return false;
			}
		});

		// creates a variable with the contents of the cookie side-menu
		var sidemenu = jQuery.cookie('side-menu');
		
		// if cookie says the menu is hiding, keep it hidden!
		if (sidemenu == 'hiding') {
			hideMenu(0);
		} else {
			showMenu(0);
		}

	}
	
	var comments=jQuery("#thePicturePage #comments");
	if (comments.length == 1) {
		var comments_button=jQuery("#comments h3");

		if (comments_button.length == 0) {
			jQuery("#addComment").before("<h3>Comments</h3>");
			comments_button=jQuery("#comments h3");
		}
	
		if (jQuery.cookie('comments') == 'visible') {
			comments_button.addClass("comments_toggle").addClass("comments_toggle_off");
		} else {
			comments.addClass("comments_hidden");
			comments_button.addClass("comments_toggle").addClass("comments_toggle_on");
		}
		
		comments_button.click(function() {

			var comments=jQuery("#thePicturePage #comments");
			if (comments.hasClass("comments_hidden")) {
					comments.removeClass("comments_hidden");
					comments_button.addClass("comments_toggle_off").removeClass("comments_toggle_on");;
					jQuery.cookie('comments', 'visible', {path: "/"});
				} else {
					comments.addClass("comments_hidden");
					comments_button.addClass("comments_toggle_on").removeClass("comments_toggle_off");;
					jQuery.cookie('comments', 'hidden', {path: "/"});
				}
			
		});
	
	}
	
	
});

function hideMenu(delay) {

	var menubar=jQuery("#menubar");
	var menuswitcher=jQuery("#menuswitcher");
	var content=jQuery("#the_page > .content");
	var pcontent=jQuery("#content");
	
	menubar.hide(delay);
	menuswitcher.addClass("menuhidden").removeClass("menushown");
	menuswitcher.text("»");
	content.addClass("menuhidden").removeClass("menushown");
	pcontent.addClass("menuhidden").removeClass("menushown");
	jQuery.cookie('side-menu', 'hiding', {path: "/"});
	
}

function showMenu(delay) {

	var menubar=jQuery("#menubar");
	var menuswitcher=jQuery("#menuswitcher");
	var content=jQuery("#the_page > .content");
	var pcontent=jQuery("#content");

	menubar.show(delay);
	menuswitcher.addClass("menushown").removeClass("menuhidden");
	menuswitcher.text("«");
	content.addClass("menushown").removeClass("menuhidden");
	pcontent.addClass("menushown").removeClass("menuhidden");
	jQuery.cookie('side-menu', 'showing', {path: "/"});
	
}

/**
 * Cookie plugin
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 */
jQuery.cookie=function(name,value,options){if(typeof value!='undefined'){options=options||{};if(value===null){value='';options=jQuery.extend({},options);options.expires=-1;}
var expires='';if(options.expires&&(typeof options.expires=='number'||options.expires.toUTCString)){var date;if(typeof options.expires=='number'){date=new Date();date.setTime(date.getTime()+(options.expires*24*60*60*1000));}else{date=options.expires;}
expires='; expires='+date.toUTCString();}
var path=options.path?'; path='+(options.path):'';var domain=options.domain?'; domain='+(options.domain):'';var secure=options.secure?'; secure':'';document.cookie=[name,'=',encodeURIComponent(value),expires,path,domain,secure].join('');}else{var cookieValue=null;if(document.cookie&&document.cookie!=''){var cookies=document.cookie.split(';');for(var i=0;i<cookies.length;i++){var cookie=jQuery.trim(cookies[i]);if(cookie.substring(0,name.length+1)==(name+'=')){cookieValue=decodeURIComponent(cookie.substring(name.length+1));break;}}}
return cookieValue;}};

