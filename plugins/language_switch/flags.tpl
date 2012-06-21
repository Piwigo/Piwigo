<li>{strip}<a id="languageSwitchLink" title="{'Language'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
  <span class="pwg-icon" style="background:url('{$lang_switch.Active.img}') center center no-repeat;">&nbsp;</span><span class="pwg-button-text">{'Language'|@translate}</span>
</a>
<div id="languageSwitchBox" class="switchBox">
  <div class="switchBoxTitle">{'Language'|@translate}</div> 
  {foreach from=$lang_switch.flags item=flag name=f}
  <a rel="nofollow" href="{$flag.url}"><img class="flags" src="{$flag.img}" alt="{$flag.alt}"> {$flag.title}</a>
  {if ($smarty.foreach.f.index+1)%3 == 0}<br>{/if}
  {/foreach}
</div>
{/strip}</li>

{footer_script require='jquery'}{literal}
jQuery("#languageSwitchLink").click(function() {
	var elt = jQuery("#languageSwitchBox");
	elt.css("left", Math.min(jQuery(this).offset().left, jQuery(window).width() - elt.outerWidth(true) - 5))
		.css("top", jQuery(this).offset().top + jQuery(this).outerHeight(true))
		.toggle();
});
jQuery("#languageSwitchBox").on("mouseleave", function() {
	jQuery(this).hide();
});
{/literal}{/footer_script}

{* <!-- switchBox structure for theme which don't include default style --> *}
{if $themeconf.parent != 'default' or (isset($themeconf.load_parent_local_head) and $themeconf.load_parent_local_head == false) }
{combine_css path=$LANGUAGE_SWITCH_PATH|@cat:"default.css"}
{/if}

{* <!-- stylish for non core themes (should be removed when all themes are updated) --> *}
{if $themeconf.name != 'clear' and $themeconf.name != 'dark' and $themeconf.name != 'elegant' and $themeconf.name != 'Sylvia'}
{combine_css path=$LANGUAGE_SWITCH_PATH|@cat:"style.css"}
{/if}

{* <!-- common style specific for LanguageSwitch --> *}
{combine_css path=$LANGUAGE_SWITCH_PATH|@cat:"language_switch.css"}