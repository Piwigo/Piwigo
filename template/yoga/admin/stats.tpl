<!-- $Id$ -->
<h2>{lang:title_history}</h2>

<h3>{L_STAT_TITLE}</h3>
<img class="image" src="{IMG_REPORT}" alt="{L_STAT_MONTHLY_ALT}" />

<h3>{L_STAT_DETAIL_TITLE}</h3>
<table class="table2" width="60%">
<tr class="throw">
    <th>{L_VALUE}</th>
    <th>{L_PAGES_SEEN}</th>
    <th>{L_VISITORS}</th>
    <th>{L_PICTURES}</th>
</tr>
<!-- BEGIN statrow -->
  <tr class="{statrow.T_CLASS}">
    <td>{statrow.VALUE}</td>
    <td>{statrow.PAGES}</td>
    <td>{statrow.VISITORS}</td>
    <td>{statrow.IMAGES}</td>
  </tr>
<!-- END statrow -->
</table>

<h3>{L_DATE_TITLE}</h3>
<table class="table2" width="98%">
<tr class="throw">
    <th>{L_STAT_HOUR}</th>
    <th>{L_STAT_LOGIN}</th>
    <th>{L_STAT_ADDR}</th>
    <th>{L_STAT_CATEGORY}</th>
    <th>{L_STAT_FILE}</th>
    <th>{L_STAT_PICTURE}</th>
</tr>
<!-- BEGIN detail -->
  <tr class="{detail.T_CLASS}">
    <td nowrap>{detail.HOUR}</td>
    <td>{detail.LOGIN}</td>
    <td>{detail.IP}</td>
  <td>{detail.CATEGORY}</td>
  <td>{detail.FILE}</td>
  <td>{detail.PICTURE}</td>
  </tr>
<!-- END detail -->
</table>


<!-- BEGIN navigation -->
<div class="admin">
{navigation.NAV_BAR}
</div>
<!-- END navigation -->