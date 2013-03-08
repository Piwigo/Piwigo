<li id="languageSwitch">{strip}<a id="languageSwitchLink" title="{'Language'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
  <span class="pwg-icon flags langflag-{$lang_switch.Active.code}">&nbsp;</span><span class="pwg-button-text">{'Language'|@translate}</span>
</a>
<div id="languageSwitchBox" class="switchBox">
  <div class="switchBoxTitle">{'Language'|@translate}</div> 
  {foreach from=$lang_switch.flags item=flag name=f}
  <a rel="nofollow" href="{$flag.url}">
    {if $lang_info.direction=="ltr"}<span class="pwg-icon flags langflag-{$flag.code}">{$flag.alt}</span>{$flag.title}{else}{$flag.title}<span class="pwg-icon flags langflag-{$flag.code}">{$flag.alt}</span>{/if}
  </a>
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

{* <!-- stylish for themes missing .switchBox styles --> *
{if $LANGUAGE_SWITCH_LOAD_STYLE}
{combine_css path=$LANGUAGE_SWITCH_PATH|@cat:"style.css"}
{/if}

{* <!-- common style specific for LanguageSwitch --> *}
{combine_css path=$LANGUAGE_SWITCH_PATH|@cat:"language_switch.css"}