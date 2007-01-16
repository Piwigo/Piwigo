<!-- $Id$ -->
<h2>{lang:title_history}</h2>

<h3>{L_STAT_TITLE}</h3>

<img class="image" src="{SRC_REPORT}" alt="{lang:history chart}" />

<table class="table2" id="dailyStats">
  <tr class="throw">
    <th>{PERIOD_LABEL}</th>
    <th>{lang:Pages seen}</th>
  </tr>
<!-- BEGIN statrow -->
  <tr class="{statrow.T_CLASS}">
    <td>{statrow.VALUE}</td>
    <td class="number">{statrow.PAGES}</td>
  </tr>
<!-- END statrow -->
</table>
