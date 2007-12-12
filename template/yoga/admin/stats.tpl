<!-- $Id$ -->
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

<h3>{L_STAT_TITLE}</h3>

<table class="table2" id="dailyStats">
  <tr class="throw">
    <th>{PERIOD_LABEL}</th>
    <th>{lang:Pages seen}</th>
    <th></th>
  </tr>
<!-- BEGIN statrow -->
  <tr>
    <td style="white-space: nowrap">{statrow.VALUE}</td>
    <td class="number">{statrow.PAGES}</td>
    <td><div class="statBar" style="width:{statrow.WIDTH}px"></div></td>
  </tr>
<!-- END statrow -->
</table>
