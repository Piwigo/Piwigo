<!-- $Id$ -->
<h2>{lang:title_groups}</h2>

<form method="post" name="add_user" action="{F_ADD_ACTION}" class="properties">
  <fieldset>
    <legend>{lang:Add group}</legend>
    <label>{lang:Group name}</label><input type="text" name="groupname" maxlength="50" size="20" />
    <p><input type="submit" name="submit_add" value="{lang:Add}" /></p>
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
      <a href="{group.U_PERM}"><img src="{themeconf:icon_dir}/permissions.png" class="button" style="border:none" alt="permissions" title="{lang:permissions}" /></a>
      <a href="{group.U_DELETE}"><img src="{themeconf:icon_dir}/delete.png" class="button" style="border:none" alt="delete" title="{lang:delete}" /></a>
    </td>
  </tr>
  <!-- END group -->
</table>
