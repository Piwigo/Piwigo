{* Example of resizeable *}
{*
{include file='include/resize.inc.tpl'}
*}

{* Example of datepicker *}
{*
{include file='include/datepicker.inc.tpl'}

{footer_script}{literal}
  pwg_initialization_datepicker("#start_day", "#start_month", "#start_year", "#start_linked_date", null, null, "#end_linked_date");
  pwg_initialization_datepicker("#end_day", "#end_month", "#end_year", "#end_linked_date", null, "#start_linked_date", null);
 jQuery().ready(function(){ $(".date_today").hide(); });
{/literal}{/footer_script}
*}

{if isset($MENUBAR)}{$MENUBAR}{/if}
<div id="content" class="content">

	<div class="titrePage">
		<ul class="categoryActions">
			{combine_script id='core.scripts' load='async' path='themes/default/js/scripts.js'}
			<li><a href="{$U_HELP}" onclick="popuphelp(this.href); return false;" title="{'Help'|@translate}" class="pwg-state-default pwg-button">
				<span class="pwg-icon pwg-icon-help">&nbsp;</span><span class="pwg-button-text">{'Help'|@translate}</span>
			</a></li>
		</ul>
		<h2>{'Search'|@translate}</h2>
	</div>

{if isset($errors) }
<div class="errors">
  <ul>
    {foreach from=$errors item=error}
    <li>{$error}</li>
    {/foreach}
  </ul>
</div>
{/if}

<form class="filter" method="post" name="search" action="{$F_SEARCH_ACTION}">
<fieldset>
  <legend>{'Filter'|@translate}</legend>
  <label>{'Search for words'|@translate}
    <input type="text" style="width: 300px" name="search_allwords" size="30">
  </label>
  <ul>
    <li><label>
      <input type="radio" name="mode" value="AND" checked="checked">{'Search for all terms'|@translate}
    </label></li>
    <li><label>
      <input type="radio" name="mode" value="OR">{'Search for any terms'|@translate}
    </label></li>
  </ul>
  <label>{'Search for Author'|@translate}
    <input type="text" style="width: 300px" name="search_author" size="30">
  </label>
</fieldset>

{if isset($TAG_SELECTION)}
<fieldset>
  <legend>{'Search tags'|@translate}</legend>
  {$TAG_SELECTION}
  <label><span><input type="radio" name="tag_mode" value="AND" checked="checked"> {'All tags'|@translate}</span></label>
  <label><span><input type="radio" name="tag_mode" value="OR"> {'Any tag'|@translate}</span></label>
</fieldset>
{/if}

<fieldset>
  <legend>{'Search by Date'|@translate}</legend>
  <ul>
    <li><label>{'Kind of date'|@translate}</label></li>
    <li><label>
      <input type="radio" name="date_type" value="date_creation" checked="checked">{'Creation date'|@translate}
    </label></li>
    <li><label>
      <input type="radio" name="date_type" value="date_available">{'Post date'|@translate}
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
  <legend>{'Search Options'|@translate}</legend>
  <label>{'Search in albums'|@translate}
    <select class="categoryList" name="cat[]" multiple="multiple" >
      {html_options options=$category_options selected=$category_options_selected}
    </select>
  </label>
  <ul>
    <li><label>{'Search in sub-albums'|@translate}</label></li>
    <li><label>
      <input type="radio" name="subcats-included" value="1" checked="checked">{'Yes'|@translate}
    </label></li>
    <li><label>
      <input type="radio" name="subcats-included" value="0">{'No'|@translate}
    </label></li>
  </ul>
</fieldset>
<p>
  <input class="submit" type="submit" name="submit" value="{'Submit'|@translate}">
  <input class="submit" type="reset" value="{'Reset'|@translate}">
</p>
</form>

<script type="text/javascript"><!--
document.search.search_allwords.focus();
//--></script>

</div> <!-- content -->
