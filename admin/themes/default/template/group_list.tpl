<div class="titrePage">
  <h2>{'Group management'|@translate}</h2>
</div>

<form method="post" name="add_user" action="{$F_ADD_ACTION}" class="properties">
  <fieldset>
    <legend>{'Add group'|@translate}</legend>
    <span class="property">
      <label for="groupname">{'Group name'|@translate}</label>
    </span>
    <input type="text" id="groupname" name="groupname" maxlength="50" size="20">
		<input type="submit" name="submit_add" value="{'Add'|@translate}">
		<input type="hidden" name="pwg_token" value="{$PWG_TOKEN}"> 

  </fieldset>
</form>

<table class="table2">
  <tr class="throw">
    <th>{'Group name'|@translate}</th>
    <th>{'Members'|@translate}</th>
    <th>{'Actions'|@translate}</th>
  </tr>
  {if not empty($groups)}
  {foreach from=$groups item=group name=group_loop}
  <tr class="{if $smarty.foreach.group_loop.index is odd}row1{else}row2{/if}">
    <td>{$group.NAME}<i><small>{$group.IS_DEFAULT}</small></i></td>
    <td><a href="{$group.U_MEMBERS}">{$group.MEMBERS}</a></td>
    <td style="text-align:center;">
      <a href="{$group.U_PERM}">
        <img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/permissions.png" style="border:none" alt="{'Permissions'|@translate}" title="{'Permissions'|@translate}"></a>
      <a href="{$group.U_DELETE}" onclick="return confirm('{'delete'|@translate|@escape:'javascript'}' 
				+ '\n\n' + '{'Are you sure?'|@translate|@escape:'javascript'}');">
        <img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/delete.png" style="border:none" alt="{'Delete'|@translate}" title="{'Delete'|@translate}"></a>
      <a href="{$group.U_ISDEFAULT}" onclick="return confirm('{'Toggle \'default group\' property'|@translate|@escape:'javascript'}' 
				+'\n\n' + '{'Are you sure?'|@translate|@escape:'javascript'}');">
        <img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/toggle_is_default_group.png" style="border:none" alt="{'Toggle \'default group\' property'|@translate}" title="{'Toggle \'default group\' property'|@translate}"></a>
    </td>
  </tr>
  {/foreach}
  {/if}
</table>
