{footer_script}
{literal}
$(document).ready(function() {

  $(".grp_action").hide();
  $("input[name=group_selection]").click(function() {

    var nbSelected = 0;
    nbSelected = $("input[name=group_selection]").filter(':checked').length;

    if (nbSelected == 0) {
      $("#permitAction").hide();
      $("#forbidAction").show();
    }
    else {
      $("#permitAction").show();
      $("#forbidAction").hide();
    }
    $("p[group_id="+$(this).attr("value")+"]").each(function () {
     $(this).toggle();
    });
    
    if (nbSelected<2) {
      $("#two_to_select").show();
      $("#two_atleast").hide();
    }
    else {
      $("#two_to_select").hide();
      $("#two_atleast").show();
    }
  });
  $("[id^=action_]").hide();
  $("select[name=selectAction]").change(function () {
    $("[id^=action_]").hide();
    $("#action_"+$(this).attr("value")).show();  
    if ($(this).val() != -1) {
      $("#applyActionBlock").show();
    }
    else {
      $("#applyActionBlock").hide();
    }
  });
});

{/literal}
{/footer_script}



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
<form method="post" name="add_user" action="{$F_ADD_ACTION}" class="properties">
  <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
  
  <table class="table2">
    <tr class="throw">
      <th></th>
      <th>{'Group name'|@translate}</th>
      <th>{'Members'|@translate}</th>
      <th>{'Actions'|@translate}</th>
    </tr>
    {if not empty($groups)}
    {foreach from=$groups item=group name=group_loop}
    <tr class="{if $smarty.foreach.group_loop.index is odd}row1{else}row2{/if}">
      <td><input name="group_selection" type="checkbox" value="{$group.ID}"></td>
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
  
  <fieldset id="action">
    <legend>{'Action'|@translate}</legend>
      <div id="forbidAction"{if count($selection) != 0} style="display:none"{/if}>{'No group selected, no action possible.'|@translate}</div>
      <div id="permitAction"{if count($selection) == 0} style="display:none"{/if}>

        <select name="selectAction">
          <option value="-1">{'Choose an action'|@translate}</option>
          <option disabled="disabled">------------------</option>
          <option value="rename">{'Rename'|@translate}</option>
          <option value="delete">{'Delete'|@translate}</option>
          <option value="merge">{'Merge selected groups'|@translate}</option>
          <option value="duplicate">{'Duplicate'|@translate}</option>
          <option value="manage_members">{'Manage the members'|@translate}</option>
          <option value="manage_pemissions">{'Manage permissions'|@translate}</option>
          <option value="toggle_default">{'Toggle \'default group\' property'|@translate}</option>
      {if !empty($element_set_groupe_plugins_actions)}
        {foreach from=$element_set_groupe_plugins_actions item=action}
          <option value="{$action.ID}">{$action.NAME}</option>
        {/foreach}
      {/if}
        </select>

        <!-- rename -->
        <div id="action_rename" class="bulkAction">
        {if not empty($groups)}
        {foreach from=$groups item=group}
        <p group_id="{$group.ID}" class="grp_action">
          <input type="text" class="large" name="rename_{$group.ID}" value="{$group.NAME}" onfocus="this.value=(this.value=='{$group.NAME}') ? '' : this.value;" onblur="this.value=(this.value=='') ? '{$group.NAME}' : this.value;">
        </p>
        {/foreach}
        {/if}
        </div>

        <!-- merge -->
        <div id="action_merge" class="bulkAction">
          <p id="two_to_select">{'Please select at least two groups'|@translate}</p>
          {assign var='mergeDefaultValue' value='Type here the name of the new group'|@translate}
          <p id="two_atleast">
            <input type="text" class="large" name="merge" value="{$mergeDefaultValue}" onfocus="this.value=(this.value=='{$mergeDefaultValue}') ? '' : this.value;" onblur="this.value=(this.value=='') ? '{$mergeDefaultValue}' : this.value;">
          </p>
        </div>

        <!-- delete -->
        <div id="action_delete" class="bulkAction">
        <p><label><input type="checkbox" name="confirm_deletion" value="1"> {'Are you sure?'|@translate}</label></p>
        </div>

        <!-- duplicate -->
        <div id="action_duplicate" class="bulkAction">
        {assign var='duplicateDefaultValue' value='Type here the name of the new group'|@translate}
        {if not empty($groups)}
        {foreach from=$groups item=group}
        <p group_id="{$group.ID}" class="grp_action">
          {$group.NAME} > <input type="text" class="large" name="duplicate_{$group.ID}" value="{$duplicateDefaultValue}" onfocus="this.value=(this.value=='{$duplicateDefaultValue}') ? '' : this.value;" onblur="this.value=(this.value=='') ? '{$duplicateDefaultValue}' : this.value;">
        </p>
        {/foreach}
        {/if}
        </div>

        <!-- manage_members -->
        <div id="action_manage_members" class="bulkAction">
        {if not empty($groups)}
        {foreach from=$groups item=group}
        <p group_id="{$group.ID}" class="grp_action">
          {$group.NAME} > {if $group.NB_MEMBERS!=0}<a href="{$group.U_MEMBERS}" title="{'Manage the members'|@translate}">{'Manage the members'|@translate}</a>{else}{'No members to manage'|@translate}{/if}
        </p>
        {/foreach}
        {/if}
        </div>

        <!-- manage_pemissions -->
        <div id="action_manage_pemissions" class="bulkAction">
        {if not empty($groups)}
        {foreach from=$groups item=group}
        <p group_id="{$group.ID}" class="grp_action">
          {$group.NAME} > <a href="{$group.U_PERM}" title="{'Permissions'|@translate}">{'Manage permissions'|@translate}</a>
        </p>
        {/foreach}
        {/if}
        </div>

        <!-- toggle_default -->
        <div id="action_toggle_default" class="bulkAction">
        {if not empty($groups)}
        {foreach from=$groups item=group}
        <p group_id="{$group.ID}" class="grp_action">
          {$group.NAME} > {if empty($group.IS_DEFAULT)}{'This group will be set to default'|@translate}{else}{'This group will be unset to default'|@translate}{/if}
        </p>
        {/foreach}
        {/if}
        </div>
    
    
        <!-- plugins -->
    {if !empty($element_set_groupe_plugins_actions)}
      {foreach from=$element_set_groupe_plugins_actions item=action}
        <div id="action_{$action.ID}" class="bulkAction">
        {if !empty($action.CONTENT)}{$action.CONTENT}{/if}
        </div>
      {/foreach}
    {/if}
    
        <p id="applyActionBlock" style="display:none" class="actionButtons">
          <input id="applyAction" class="submit" type="submit" value="{'Apply action'|@translate}" name="submit"> <span id="applyOnDetails"></span></p>
    </div> <!-- #permitAction -->
  </fieldset>
</form>
</form>