<div class="titrePage">
  <h2>{'title_groups'|@translate}</h2>
</div>

<form method="post" name="add_user" action="{$F_ADD_ACTION}" class="properties">
  <fieldset>
    <legend>{'Add group'|@translate}</legend>

    <span class="property">
      <label for="groupname">{'Group name'|@translate}</label>
    </span>
    <input type="text" id="groupname" name="groupname" maxlength="50" size="20">

    <p>
      <input class="submit" type="submit" name="submit_add" value="{'Add'|@translate}" {$TAG_INPUT_ENABLED}>
    </p>
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
        <img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/permissions.png" class="button" style="border:none" alt="{'permissions'|@translate}" title="{'permissions'|@translate}"></a>
      <a href="{$group.U_DELETE}" onclick="return confirm('{'delete'|@translate|@escape:'javascript'}' + '\n\n' + '{'Are you sure?'|@translate|@escape:'javascript'}');">
        <img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/delete.png" class="button" style="border:none" alt="{'delete'|@translate}" title="{'delete'|@translate}" {$TAG_INPUT_ENABLED}></a>
      <a href="{$group.U_ISDEFAULT}" onclick="return confirm('{'toggle_is_default_group'|@translate|@escape:'javascript'}' +'\n\n' + '{'Are you sure?'|@translate|@escape:'javascript'}');">
        <img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/toggle_is_default_group.png" class="button" style="border:none" alt="{'toggle_is_default_group'|@translate}" title="{'toggle_is_default_group'|@translate}" {$TAG_INPUT_ENABLED}></a>
    </td>
  </tr>
  {/foreach}
  {/if}
</table>
