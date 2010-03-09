 /*
 * TipTip
 * Copyright 2010 Drew Wilson
 * www.drewwilson.com
 * code.drewwilson.com/entry/tiptip-jquery-plugin
 *
 * Version 1.2   -   Updated: Jan. 13, 2010
 *
 * This Plug-In will create a custom tooltip to replace the default
 * browser tooltip. It is extremely lightweight and very smart in
 * that it detects the edges of the browser window and will make sure
 * the tooltip stays within the current window size. As a result the
 * tooltip will adjust itself to be displayed above, below, to the left 
 * or to the right depending on what is necessary to stay within the
 * browser window. It is completely customizable as well via CSS.
 *
 * This TipTip jQuery plug-in is dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */
(function($){$.fn.tipTip=function(options){var defaults={maxWidth:"200px",edgeOffset:3,delay:400,fadeIn:200,fadeOut:200,enter:function(){},exit:function(){}};var opts=$.extend(defaults,options);if($("#tiptip_holder").length<=0){var tiptip_holder=$('<div id="tiptip_holder" style="max-width:'+opts.maxWidth+';"></div>');var tiptip_content=$('<div id="tiptip_content"></div>');var tiptip_arrow=$('<div id="tiptip_arrow"></div>');$("body").append(tiptip_holder.html(tiptip_content).prepend(tiptip_arrow.html('<div id="tiptip_arrow_inner"></div>')));}else{var tiptip_holder=$("#tiptip_holder");var tiptip_content=$("#tiptip_content");var tiptip_arrow=$("#tiptip_arrow");}
return this.each(function(){var org_elem=$(this);var org_title=org_elem.attr("title");if(org_title!=""){org_elem.removeAttr("title");var timeout=false;org_elem.hover(function(){opts.enter.call(this);tiptip_content.html(org_title);tiptip_holder.hide().removeAttr("class").css("margin","0");tiptip_arrow.removeAttr("style");var top=parseInt(org_elem.offset()['top']);var left=parseInt(org_elem.offset()['left']);var org_width=parseInt(org_elem.outerWidth());var org_height=parseInt(org_elem.outerHeight());var tip_w=tiptip_holder.outerWidth();var tip_h=tiptip_holder.outerHeight();var w_compare=Math.round((org_width-tip_w)/2);var h_compare=Math.round((org_height-tip_h)/2);var marg_left=Math.round(left+w_compare);var marg_top=Math.round(top+org_height+opts.edgeOffset);var t_class="";var arrow_top="";var arrow_left=Math.round(tip_w-12)/2;if(w_compare<0){if((w_compare+left)<parseInt($(window).scrollLeft())){t_class="_right";arrow_top=Math.round(tip_h-13)/2;arrow_left=-12;marg_left=Math.round(left+org_width+opts.edgeOffset);marg_top=Math.round(top+h_compare);}else if((tip_w+left)>parseInt($(window).width())){t_class="_left";arrow_top=Math.round(tip_h-13)/2;arrow_left=Math.round(tip_w);marg_left=Math.round(left-(tip_w+opts.edgeOffset+5));marg_top=Math.round(top+h_compare);}}
if((top+org_height+opts.edgeOffset+tip_h+8)>parseInt($(window).height()+$(window).scrollTop())){t_class=t_class+"_top";arrow_top=tip_h;marg_top=Math.round(top-(tip_h+5+opts.edgeOffset));}else if(((top+org_height)-(opts.edgeOffset+tip_h))<0||t_class==""){t_class=t_class+"_bottom";arrow_top=-12;marg_top=Math.round(top+org_height+opts.edgeOffset);}
if(t_class=="_right_top"||t_class=="_left_top"){marg_top=marg_top+5;}else if(t_class=="_right_bottom"||t_class=="_left_bottom"){marg_top=marg_top-5;}
if(t_class=="_left_top"||t_class=="_left_bottom"){marg_left=marg_left+5;}
tiptip_arrow.css({"margin-left":arrow_left+"px","margin-top":arrow_top+"px"});tiptip_holder.css({"margin-left":marg_left+"px","margin-top":marg_top+"px"}).attr("class","tip"+t_class);if(timeout){clearTimeout(timeout);}
timeout=setTimeout(function(){tiptip_holder.stop(true,true).fadeIn(opts.fadeIn);},opts.delay);},function(){opts.exit.call(this);if(timeout){clearTimeout(timeout);}tiptip_holder.fadeOut(opts.fadeOut);});}});}})(jQuery);