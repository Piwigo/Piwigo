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

if (jQuery.cookie('picture-menu') == 'visible') {
	jQuery("head").append("<style type=\"text/css\">#content.contentWithMenu, #the_page > .content {margin-left:240px;}</style>");
} else {
	jQuery("head").append("<style type=\"text/css\">#the_page #menubar {display:none;} #content.contentWithMenu, #the_page > .content {margin-left:35px;}</style>");
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
	jQuery.cookie('picture-menu', 'hidden', {path: "/"});
	
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
	jQuery.cookie('picture-menu', 'visible', {path: "/"});
	
}

function hideInfo(delay) {

	var imageInfos=jQuery("#imageInfos");
	var infoswitcher=jQuery("#infoSwitcher");
	var theImage=jQuery("#theImage");
	
	imageInfos.hide(delay);
	infoswitcher.addClass("infohidden").removeClass("infoshown");
	theImage.addClass("infohidden").removeClass("infoshown");
	jQuery.cookie('side-info', 'hidden', {path: "/"});

}

function showInfo(delay) {

	var imageInfos=jQuery("#imageInfos");
	var infoswitcher=jQuery("#infoSwitcher");
	var theImage=jQuery("#theImage");
	
	imageInfos.show(delay);
	infoswitcher.addClass("infoshown").removeClass("infohidden");
	theImage.addClass("infoshown").removeClass("infohidden");
	jQuery.cookie('side-info', 'visible', {path: "/"});

}

jQuery("document").ready(function(jQuery){
	
	// side-menu show/hide

	var sidemenu = jQuery.cookie('picture-menu');
	var menubar=jQuery("#menubar");

	if (menubar.length == 1 && p_main_menu!="disabled") {

		jQuery("#menuSwitcher").html("<div class=\"switchArrow\">&nbsp;</div>");

		// if cookie says the menu is hiding, keep it hidden!
		if (sidemenu == 'hidden') {
			hideMenu(0);
		} else if (sidemenu == 'visible') {
			showMenu(0);
		} else if (p_main_menu == 'off') {
			hideMenu(0);
		}	else {
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

	// info show/hide
	
	var sideinfo = jQuery.cookie('side-info');
	var imageInfos=jQuery("#imageInfos");

	if (imageInfos.length == 1 && p_pict_descr!="disabled") {

		jQuery("#infoSwitcher").html("<div class=\"switchArrow\">&nbsp;</div>");

		// if cookie says the menu is hiding, keep it hidden!
		if (sideinfo == 'hidden') {
			hideInfo(0);
		} else if (sideinfo == 'visible') {
			showInfo(0);
		} else if (p_pict_descr == 'off') {
			hideInfo(0);
		} else {
			showInfo(0);
		}
	
		jQuery("#infoSwitcher").click(function(){
			if (jQuery("#imageInfos").is(":hidden")) {
				showInfo(0);
				return false;
			} else {
				hideInfo(0);
				return false;
			}
		});
	
	}

	// comments show/hide

	var commentsswicther=jQuery("#commentsSwitcher");
	var comments=jQuery("#thePicturePage #comments");
	
	commentsswicther.html("<div class=\"switchArrow\">&nbsp;</div>");
	
	if (comments.length == 1 && p_pict_comment!="disabled") {
		var comments_button=jQuery("#comments h3");

		if (comments_button.length == 0) {
			jQuery("#addComment").before("<h3>Comments</h3>");
			comments_button=jQuery("#comments h3");
		}
	
		if (jQuery.cookie('comments') == 'hidden') {
			comments.addClass("commentshidden");
			comments_button.addClass("comments_toggle").addClass("comments_toggle_on");
		} else if (jQuery.cookie('comments') == 'visible') {
			comments.addClass("commentsshown");
			comments_button.addClass("comments_toggle").addClass("comments_toggle_off");
		} else if (p_pict_comment == 'off') {
			comments.addClass("commentshidden");
			comments_button.addClass("comments_toggle").addClass("comments_toggle_on");
		} else {
			comments.addClass("commentsshown");
			comments_button.addClass("comments_toggle").addClass("comments_toggle_off");
		}
		
		comments_button.click(function() { commentsToggle() });
		commentsswicther.click(function() { commentsToggle() });
	
	}
  
  var top = jQuery('#commentAdd').offset().top - parseFloat(jQuery('#commentAdd').css('marginTop').replace(/auto/, 0));
  jQuery(window).scroll(function (event) {
    // what the y position of the scroll is
    var y = jQuery(this).scrollTop();
  
    // whether that's below the form
    if (y >= top) {
      // if so, ad the fixed class
      jQuery('#commentAdd').addClass('fixed');
    } else {
      // otherwise remove it
      jQuery('#commentAdd').removeClass('fixed');
    }
  });

	
});

function commentsToggle() {
	var comments=jQuery("#thePicturePage #comments");
	var comments_button=jQuery("#comments h3");

	if (comments.hasClass("commentshidden")) {
			comments.removeClass("commentshidden").addClass("commentsshown");
			comments_button.addClass("comments_toggle_off").removeClass("comments_toggle_on");;
			jQuery.cookie('comments', 'visible', {path: "/"});
		} else {
			comments.addClass("commentshidden").removeClass("commentsshown");
			comments_button.addClass("comments_toggle_on").removeClass("comments_toggle_off");;
			jQuery.cookie('comments', 'hidden', {path: "/"});
		}

}
