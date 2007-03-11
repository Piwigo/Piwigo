<div class="titrePage">
  <ul class="categoryActions">
    <li>
      <a
        href="{U_HELP}"
        onclick="popuphelp(this.href); return false;"
        title="{lang:Help}"
      >
        <img src="{themeconf:icon_dir}/help.png" class="button" alt="(?)">
      </a>
    </li>
  </ul>
  <h2>{lang:History} {TABSHEET_TITLE}</h2>
  {TABSHEET}
</div>

<form class="filter" method="post" name="filter" action="{F_ACTION}">
<fieldset>
  <legend>{lang:Filter}</legend>
  <ul>
    <li><label>{lang:search_date_from}</label></li>
    <li>
      <select name="start_day">
        <!-- BEGIN start_day -->
        <option {start_day.SELECTED} value="{start_day.VALUE}">{start_day.OPTION}</option>
        <!-- END start_day -->
      </select>
      <select name="start_month">
        <!-- BEGIN start_month -->
        <option {start_month.SELECTED} value="{start_month.VALUE}">{start_month.OPTION}</option>
        <!-- END start_month -->
      </select>
      <input name="start_year" value="{START_YEAR}" type="text" size="4" maxlength="4" >
    </li>
  </ul>
  <ul>
    <li><label>{lang:search_date_to}</label></li>
    <li>
      <select name="end_day">
        <!-- BEGIN end_day -->
        <option {end_day.SELECTED} value="{end_day.VALUE}">{end_day.OPTION}</option>
        <!-- END end_day -->
      </select>
      <select name="end_month">
        <!-- BEGIN end_month -->
        <option {end_month.SELECTED} value="{end_month.VALUE}">{end_month.OPTION}</option>
        <!-- END end_month -->
      </select>
      <input name="end_year" value="{END_YEAR}" type="text" size="4" maxlength="4" >
    </li>
  </ul>

  <label>
    {lang:Element type}
    <select name="types[]" multiple="multiple" size="4">
      <!-- BEGIN types_option -->
      <option value="{types_option.VALUE}" {types_option.SELECTED}>
        {types_option.CONTENT}
      </option>
      <!-- END types_option -->
    </select>
  </label>

  <label>
    {lang:User}
    <select name="user">
      <!-- BEGIN user_option -->
      <option value="{user_option.VALUE}" {user_option.SELECTED}>
        {user_option.CONTENT}
      </option>
      <!-- END user_option -->
    </select>
  </label>

  <input class="submit" type="submit" name="submit" value="{lang:submit}" />
</fieldset>
</form>

<!-- BEGIN summary -->
<fieldset>
  <legend>{lang:Summary}</legend>

  <ul>
    <li>{summary.FILESIZE}</li>
  </ul>
</fieldset>
<!-- END summary -->

<!-- BEGIN navigation -->
<div class="admin">
{navigation.NAVBAR}
</div>
<!-- END navigation -->

<table class="table2" id="detailedStats">
  <tr class="throw">
    <th>{lang:date}</th>
    <th>{lang:time}</th>
    <th>{lang:user}</th>
    <th>{lang:IP}</th>
    <th>{lang:image}</th>
    <th>{lang:Element type}</th>
    <th>{lang:section}</th>
    <th>{lang:category}</th>
    <th>{lang:tags}</th>
  </tr>
<!-- BEGIN detail -->
  <tr class="{detail.T_CLASS}">
    <td class="hour">{detail.DATE}</td>
    <td class="hour">{detail.TIME}</td>
    <td>{detail.USER}</td>
    <td>{detail.IP}</td>
    <td>{detail.IMAGE}</td>
    <td>{detail.TYPE}</td>
    <td>{detail.SECTION}</td>
    <td>{detail.CATEGORY}</td>
    <td>{detail.TAGS}</td>
  </tr>
<!-- END detail -->
</table>

<!-- BEGIN navigation -->
<div class="admin">
{navigation.NAVBAR}
</div>
<!-- END navigation -->
