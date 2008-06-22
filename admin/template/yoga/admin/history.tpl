{* $Id$ *}
<div class="titrePage">
  {$TABSHEET}
  <ul class="categoryActions">
    <li>
      <a
        href="{$U_HELP}"
        onclick="popuphelp(this.href); return false;"
        title="{'Help'|@translate}"
      >
        <img src="{$themeconf.icon_dir}/help.png" class="button" alt="(?)">
      </a>
    </li>
  </ul>
  <h2>{'History'|@translate} {$TABSHEET_TITLE}</h2>
</div>

<form class="filter" method="post" name="filter" action="{$F_ACTION}">
<fieldset>
  <legend>{'Filter'|@translate}</legend>
  <ul>
    <li><label>{'search_date_from'|@translate}</label></li>
    <li>
      <select name="start_day">
        <option value="0">--</option>
        {section name=day start=1 loop=32}
        <option value="{$smarty.section.day.index}" {if $smarty.section.day.index==$START_DAY_SELECTED}selected="selected"{/if}>{$smarty.section.day.index}</option>
        {/section}
      </select>
      <select name="start_month">
      {html_options options=$month_list selected=$START_MONTH_SELECTED}
      </select>
      <input name="start_year" value="{$START_YEAR}" type="text" size="4" maxlength="4" >
    </li>
  </ul>
  <ul>
    <li><label>{'search_date_to'|@translate}</label></li>
    <li>
      <select name="end_day">
        <option value="0">--</option>
        {section name=day start=1 loop=32}
        <option value="{$smarty.section.day.index}" {if $smarty.section.day.index==$END_DAY_SELECTED}selected="selected"{/if}>{$smarty.section.day.index}</option>
        {/section}
      </select>
      <select name="end_month">
      {html_options options=$month_list selected=$END_MONTH_SELECTED}
      </select>
      <input name="end_year" value="{$END_YEAR}" type="text" size="4" maxlength="4" >
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

  <input class="submit" type="submit" name="submit" value="{'Submit'|@translate}" />
</fieldset>
<fieldset>
  <legend>{'Display'|@translate}</legend>
  <ul>
    <li>
      {'Thumbnails'|@translate}
      <select name="display_thumbnail">
        {html_options values=$display_thumbnail_values output=$display_thumbnail_values|translate selected=$display_thumbnail_selected}
      </select>
    </li>
  </ul>
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


{if !empty($NAV_BAR)}
<div class="navigationBar">
  {$NAV_BAR}
</div>
{/if}


<table class="table2" id="detailedStats">
<tr class="throw">
  <th>{'Date'|@translate}</th>
  <th>{'time'|@translate}</th>
  <th>{'user'|@translate}</th>
  <th>{'IP'|@translate}</th>
  <th>{'image'|@translate}</th>
  <th>{'Element type'|@translate}</th>
  <th>{'section'|@translate}</th>
  <th>{'category'|@translate}</th>
  <th>{'tags'|@translate}</th>
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


{if !empty($NAV_BAR)}
<div class="navigationBar">
  {$NAV_BAR}
</div>
{/if}
