{* Example of resizeable *}
{*
{include file='include/resize.inc.tpl'}
*}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}

{footer_script}
jQuery(document).ready(function() {
  jQuery("#authors, #tags, #categories").each(function() {
    jQuery(this).selectize({
      plugins: ['remove_button'],
      maxOptions:jQuery(this).find("option").length
    });
  })
});
{/footer_script}

{if isset($MENUBAR)}{$MENUBAR}{/if}
<div id="content" class="content{if isset($MENUBAR)} contentWithMenu{/if}">

	<div class="titrePage">
		<ul class="categoryActions">
			{combine_script id='core.scripts' load='async' path='themes/default/js/scripts.js'}
			<li><a href="{$U_HELP}" onclick="popuphelp(this.href); return false;" title="{'Help'|@translate}" class="pwg-state-default pwg-button">
				<span class="pwg-icon pwg-icon-help"></span><span class="pwg-button-text">{'Help'|@translate}</span>
			</a></li>
		</ul>
		<h2><a href="{$U_HOME}">{'Home'|@translate}</a>{$LEVEL_SEPARATOR}{'Search'|@translate}</h2>
	</div>

{include file='infos_errors.tpl'}

<form class="filter" method="post" name="search" action="{$F_SEARCH_ACTION}">
<fieldset>
  <legend>{'Search for words'|@translate}</legend>
  <p>
    <input type="text" name="search_allwords">
    <br>
    <label><input type="radio" name="mode" value="AND" checked="checked"> {'Search for all terms'|@translate}</label>
    <label><input type="radio" name="mode" value="OR"> {'Search for any term'|@translate}</label>
  </p>

  <p>
    <strong>{'Apply on properties'|translate}</strong><br>
    <label><input type="checkbox" name="fields[]" value="name" checked="checked"> {'Photo title'|translate}</label>
    <label><input type="checkbox" name="fields[]" value="comment" checked="checked"> {'Photo description'|translate}</label>
    <label><input type="checkbox" name="fields[]" value="file" checked="checked"> {'File name'|translate}</label>
{if isset($TAGS)}
    <label><input type="checkbox" name="search_in_tags" value="tags"> {'Tags'|translate}</label>
{/if}
  </p>

</fieldset>

{if count($AUTHORS)>=1}
<fieldset>
  <legend>{'Search for Author'|@translate}</legend>
  <p>
    <select id="authors" placeholder="{'Type in a search term'|translate}" name="authors[]" multiple>
{foreach from=$AUTHORS item=author}
      <option value="{$author.author|strip_tags:false|escape:html}">{$author.author|strip_tags:false} ({$author.counter|translate_dec:'%d photo':'%d photos'})</option>
{/foreach}
    </select>
  </p>
</fieldset>
{/if}

{if isset($TAGS)}
<fieldset>
  <legend>{'Search tags'|@translate}</legend>
  <p>
  <select id="tags" placeholder="{'Type in a search term'|translate}" name="tags[]" multiple>
{foreach from=$TAGS item=tag}
    <option value="{$tag.id}">{$tag.name} ({$tag.counter|translate_dec:'%d photo':'%d photos'})</option>
{/foreach}
  </select>
  <label><span><input type="radio" name="tag_mode" value="AND" checked="checked"> {'All tags'|@translate}</span></label>
  <label><span><input type="radio" name="tag_mode" value="OR"> {'Any tag'|@translate}</span></label>
  </p>
</fieldset>
{/if}

<fieldset>
  <legend>{'Search by date'|@translate}</legend>
  <ul>
    <li><label>{'Kind of date'|@translate}</label></li>
    <li><label>
      <input type="radio" name="date_type" value="date_creation" checked="checked"> {'Creation date'|@translate}
    </label></li>
    <li><label>
      <input type="radio" name="date_type" value="date_available"> {'Post date'|@translate}
    </label></li>
  </ul>
  <ul>
    <li><label>{'Date'|@translate}</label></li>
    <li>
      <select id="start_day" name="start_day">
          <option value="0">--</option>
        {section name=day start=1 loop=32}
          <option value="{$smarty.section.day.index}" {if $smarty.section.day.index==$START_DAY_SELECTED}selected="selected"{/if}>{$smarty.section.day.index}</option>
        {/section}
      </select>
      <select id="start_month" name="start_month">
        {html_options options=$month_list selected=$START_MONTH_SELECTED}
      </select>
      <input id="start_year" name="start_year" type="text" size="4" maxlength="4" >
      <input id="start_linked_date" name="start_linked_date" type="hidden" size="10" disabled="disabled">
    </li>
    <li>
      <a class="date_today" href="#" onClick="document.search.start_day.value={$smarty.now|date_format:"%d"};document.search.start_month.value={$smarty.now|date_format:"%m"};document.search.start_year.value={$smarty.now|date_format:"%Y"};return false;">{'today'|@translate}</a>
    </li>
  </ul>
  <ul>
    <li><label>{'End-Date'|@translate}</label></li>
    <li>
      <select id="end_day" name="end_day">
          <option value="0">--</option>
        {section name=day start=1 loop=32}
          <option value="{$smarty.section.day.index}" {if $smarty.section.day.index==$END_DAY_SELECTED}selected="selected"{/if}>{$smarty.section.day.index}</option>
        {/section}
      </select>
      <select id="end_month" name="end_month">
        {html_options options=$month_list selected=$END_MONTH_SELECTED}
      </select>
      <input id="end_year" name="end_year" type="text" size="4" maxlength="4" >
      <input id="end_linked_date" name="end_linked_date" type="hidden" size="10" disabled="disabled">
    </li>
    <li>
      <a class="date_today" href="#" onClick="document.search.end_day.value={$smarty.now|date_format:"%d"};document.search.end_month.value={$smarty.now|date_format:"%m"};document.search.end_year.value={$smarty.now|date_format:"%Y"};return false;">{'today'|@translate}</a>
    </li>
  </ul>
</fieldset>

<fieldset>
  <legend>{'Search in albums'|@translate}</legend>
  <p>
    <select id="categories" name="cat[]" multiple>
      {html_options options=$category_options selected=$category_options_selected}
    </select>
    <label><input type="checkbox" name="subcats-included" value="1" checked="checked"> {'Search in sub-albums'|@translate}</label>
  </p>
</fieldset>
<p>
  <input type="submit" name="submit" value="{'Submit'|@translate}">
  <input type="reset" value="{'Reset'|@translate}">
</p>
</form>

<script type="text/javascript"><!--
document.search.search_allwords.focus();
//--></script>

</div> <!-- content -->
