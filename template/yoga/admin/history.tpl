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
  <h2>{lang:History}</h2>
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

  <ul>
    <li><label></label></li>
    <li></li>
  </ul>

  <label>
    {lang:Pictures}
    <select name="pictures">
      <!-- BEGIN pictures_option -->
      <option
        value="{pictures_option.VALUE}"
        {pictures_option.SELECTED}
      >
        {pictures_option.CONTENT}
      </option>
      <!-- END pictures_option -->
    </select>
  </label>

  <label>
    {lang:High quality}
    <select name="high">
      <!-- BEGIN high_option -->
      <option
        value="{high_option.VALUE}"
        {high_option.SELECTED}
      >
        {high_option.CONTENT}
      </option>
      <!-- END high_option -->
    </select>
  </label>

  <input class="submit" type="submit" name="submit" value="{lang:submit}" {TAG_INPUT_ENABLED}/>
</fieldset>
</form>

<h3>{L_DATE_TITLE}</h3>

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
    <th>{lang:high quality}</th>
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
    <td>
  <!-- BEGIN high -->
      <img src="{themeconf:icon_dir}/check.png" alt="{lang:yes}">
  <!-- END high -->
  <!-- BEGIN no_high -->
      <img src="{themeconf:icon_dir}/uncheck.png" alt="{lang:no}">
  <!-- END no_high -->
    </td>
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
