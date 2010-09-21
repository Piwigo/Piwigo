
{include file='include/datepicker.inc.tpl'}

{literal}
<script type="text/javascript">
  pwg_initialization_datepicker("#start_day", "#start_month", "#start_year", "#start_linked_date", null, null, "#end_linked_date");
  pwg_initialization_datepicker("#end_day", "#end_month", "#end_year", "#end_linked_date", null, "#start_linked_date", null);
</script>
{/literal}

<div class="titrePage">
  <h2>{'History'|@translate} {$TABSHEET_TITLE}</h2>
</div>

<form class="filter" method="post" name="filter" action="{$F_ACTION}">
<fieldset>
  <legend>{'Filter'|@translate}</legend>
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
      <input id="start_year" name="start_year" value="{$START_YEAR}" type="text" size="4" maxlength="4" >
      <input id="start_linked_date" name="start_linked_date" type="hidden" size="10" disabled="disabled">
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
      <input id="end_year" name="end_year" value="{$END_YEAR}" type="text" size="4" maxlength="4" >
      <input id="end_linked_date" name="end_linked_date" type="hidden" size="10" disabled="disabled">
    </li>
  </ul>

  <label>
    {'Element type'|@translate}
    <select name="types[]" multiple="multiple" size="4">
      {html_options values=$type_option_values output=$type_option_values|translate selected=$type_option_selected}
    </select>
  </label>

  <label>
    {'User'|@translate}
    <select name="user">
      <option value="-1">------------</option>
      {html_options options=$user_options selected=$user_options_selected}
    </select>
  </label>

  <label>
    {'Image id'|@translate}
    <input name="image_id" value="{$IMAGE_ID}" type="text" size="5">
  </label>

  <label>
    {'File name'|@translate}
    <input name="filename" value="{$FILENAME}" type="text">
  </label>

  <label>
    {'Thumbnails'|@translate}
    <select name="display_thumbnail">
      {html_options options=$display_thumbnails selected=$display_thumbnail_selected}
    </select>
  </label>

  <input class="submit" type="submit" name="submit" value="{'Submit'|@translate}">
</fieldset>
</form>

{if isset($search_summary)}
<fieldset>
  <legend>{'Summary'|@translate}</legend>

  <ul>
    <li>{$search_summary.NB_LINES}, {$search_summary.FILESIZE}</li>
    <li>
      {$search_summary.USERS}
      <ul>
        <li>{$search_summary.MEMBERS}</li>
        <li>{$search_summary.GUESTS}</li>
      </ul>
    </li>
  </ul>
</fieldset>
{/if}

{if !empty($navbar) }{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}

<table class="table2" id="detailedStats">
<tr class="throw">
  <th>{'Date'|@translate}</th>
  <th>{'Time'|@translate}</th>
  <th>{'User'|@translate}</th>
  <th>{'IP'|@translate}</th>
  <th>{'Element'|@translate}</th>
  <th>{'Element type'|@translate}</th>
  <th>{'Section'|@translate}</th>
  <th>{'Album'|@translate}</th>
  <th>{'Tags'|@translate}</th>
</tr>
{if !empty($search_results) }
{foreach from=$search_results item=detail name=res_loop}
<tr class="{if $smarty.foreach.res_loop.index is odd}row1{else}row2{/if}">
  <td class="hour">{$detail.DATE}</td>
  <td class="hour">{$detail.TIME}</td>
  <td>{$detail.USER}</td>
  <td>{$detail.IP}</td>
  <td>{$detail.IMAGE}</td>
  <td>{$detail.TYPE}</td>
  <td>{$detail.SECTION}</td>
  <td>{$detail.CATEGORY}</td>
  <td>{$detail.TAGS}</td>
</tr>
{/foreach}
{/if}
</table>

{if !empty($navbar) }{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}
