<!-- $Id$ -->
<h2>{lang:title_history}</h2>

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
    <td><div class="statBar" style="width:{statrow.WIDTH}px" /></td>
  </tr>
<!-- END statrow -->
</table>
