<div class="titrePage">
  <h2>{lang:Plugins}
  </h2>
</div>


<!-- BEGIN plugins -->
<table class="table2">
<thead><tr class="throw">
  <td>{lang:Name}</td>
  <td>{lang:Version}</td>
  <td>{lang:Description}</td>
  <td>{lang:Actions}</td>
</tr></thead>
<!-- BEGIN plugin -->
<tr class="{plugins.plugin.CLASS}">
  <td>{plugins.plugin.NAME}</td>
  <td>{plugins.plugin.VERSION}</td>
  <td>{plugins.plugin.DESCRIPTION}</td>
  <td>
  <!-- BEGIN action -->
  <a href="{plugins.plugin.action.U_ACTION}" {TAG_INPUT_ENABLED}>{plugins.plugin.action.L_ACTION}</a>
  <!-- END action -->
  </td>
</tr>
<!-- END plugin -->
</table>
<!-- END plugins -->