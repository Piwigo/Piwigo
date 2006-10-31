<div class="titrePage">
  <h2>
<!-- BEGIN plugin_menu -->
<!-- BEGIN menu_item -->
  <span style="margin-left:2px;"><a href="{plugin_menu.menu_item.URL}">{plugin_menu.menu_item.NAME}</a></span>
<!-- END menu_item -->
<!-- END plugin_menu -->
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
  <a href="{plugins.plugin.action.U_ACTION}">{plugins.plugin.action.L_ACTION}</a>
  <!-- END action -->
  </td>
</tr>
<!-- END plugin -->
</table>
<!-- END plugins -->