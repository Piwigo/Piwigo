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

if (jQuery.cookie('page-menu') == 'hidden') {
	jQuery("head").append("<style type=\"text/css\">#the_page #menubar {display:none;} #content.contentWithMenu, #the_page > .content {margin-left:35px;}</style>");
} else {
	jQuery("head").append("<style type=\"text/css\">#content.contentWithMenu, #the_page > .content {margin-left:240px;}</style>");
}

function hideMenu(delay) {
	var menubar=jQuery("#menubar");
	var menuswitcher=jQuery("#menuSwitcher");
	var content=jQuery("#the_page > .content");
	var pcontent=jQuery("#content");
	
	menubar.hide(delay);
	menuswitcher.addClass("menuhidden").removeClass("menushown");
	content.addClass("menuhidden").removeClass("menushown");
	pcontent.addClass("menuhidden").removeClass("menushown");
	jQuery.cookie('page-menu', 'hidden', {path: "/"});
	
}

function showMenu(delay) {

	var menubar=jQuery("#menubar");
	var menuswitcher=jQuery("#menuSwitcher");
	var content=jQuery("#the_page > .content");
	var pcontent=jQuery("#content");

	menubar.show(delay);
	menuswitcher.addClass("menushown").removeClass("menuhidden");
	content.addClass("menushown").removeClass("menuhidden");
	pcontent.addClass("menushown").removeClass("menuhidden");
	jQuery.cookie('page-menu', 'visible', {path: "/"});
	
}

jQuery("document").ready(function(jQuery){

	var sidemenu = jQuery.cookie('page-menu');
	var menubar=jQuery("#menubar");

	if (menubar.length == 1) {

		jQuery("#menuSwitcher").html("<div class=\"switchArrow\">&nbsp;</div>");

		// if cookie says the menu is hiding, keep it hidden!
		if (sidemenu == 'hidden') {
			hideMenu(0);
		} else {
			showMenu(0);
		}
	
		jQuery("#menuSwitcher").click(function(){
			if (jQuery("#menubar").is(":hidden")) {
				showMenu(0);
				return false;
			} else {
				hideMenu(0);
				return false;
			}
		});


	}	
	
});


