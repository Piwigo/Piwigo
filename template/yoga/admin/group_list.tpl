<h2>{lang:title_groups}</h2>

<form class="filter" method="post" name="add_user" action="{F_ADD_ACTION}">
  <fieldset>
    <legend>{lang:Add group}</legend>
    <label>{lang:Group name} <input type="text" name="groupname" maxlength="50" size="20" /></label>
    <input type="submit" name="submit_add" value="{lang:Add}" />
  </fieldset>
</form>

<table class="table2">
  <tr class="throw">
    <th>{lang:Group name}</th>
    <th>{lang:Members}</th>
    <th>{lang:Actions}</th>
  </tr>
  <!-- BEGIN group -->
  <tr class="{group.CLASS}">
    <td>{group.NAME}</td>
    <td><a href="{group.U_MEMBERS}">{group.MEMBERS}</a></td>
    <td style="text-align:center;">
      <a href="{group.U_PERM}"><img src="./template/yoga/theme/permissions.png" style="border:none" alt="permissions" title="{lang:permissions}" /></a>
      <a href="{group.U_DELETE}"><img src="./template/yoga/theme/delete.png" style="border:none" alt="delete" title="{lang:delete}" /></a>
    </td>
  </tr>
  <!-- END group -->
</table>
