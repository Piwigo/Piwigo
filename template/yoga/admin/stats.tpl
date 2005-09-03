<!-- $Id$ -->
<h2>{lang:title_history}</h2>

<h3>{L_STAT_TITLE}</h3>
<img class="image" src="{IMG_MONTHLY_REPORT}" alt="{L_STAT_MONTHLY_ALT}" />
<h3>{L_STAT_MONTH_TITLE}</h3>
<!-- TODO : center the table ??? -->
<table class="table2" width="60%">
<tr class="throw">
    <th>{L_MONTH}</th>
	<th>{L_PAGES_SEEN}</th>
    <th>{L_VISITORS}</th>
    <th>{L_PICTURES}</th>
</tr>
<!-- BEGIN month -->
  <tr class="{month.T_CLASS}">
    <td>{month.MONTH}</td>
    <td>{month.PAGES}</td>
    <td>{month.VISITORS}</td>
	<td>{month.IMAGES}</td>
  </tr>
<!-- END month -->
</table>
<br />
