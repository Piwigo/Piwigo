{footer_script}
{literal}
$(document).ready(function() {

  $(".grp_action").hide();
  $("input.group_selection").click(function() {

    var nbSelected = 0;
    nbSelected = $("input.group_selection").filter(':checked').length;

    if (nbSelected == 0) {
      $("#permitAction").hide();
      $("#forbidAction").show();
    }
    else {
      $("#permitAction").show();
      $("#forbidAction").hide();
    }
    $("p[group_id="+$(this).prop("value")+"]").each(function () {
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
    $("#action_"+$(this).prop("value")).show();
    if ($(this).val() != -1 && $(this).val() !="manage_pemissions" && $(this).val() !="manage_members" ) {
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

  <ul style="text-align:center;">
    {if not empty($groups)}
    {foreach from=$groups item=group name=group_loop}
    <li style="vertical-align: middle;position: relative;display: inline-block;text-align: left;background-color: #ccc;height: 300px; width: 250px; margin: 5px">
      <p style="text-align: left;"><label>{$group.NAME}<i><small>{$group.IS_DEFAULT}</small></i><input class="group_selection" name="group_selection[]" type="checkbox" value="{$group.ID}"></label></p>
      <p style="text-align: left;max-height: 200px;overflow: auto;">{if $group.MEMBERS>0}<a href="{$group.U_MEMBERS}" title="{'Manage the members'|@translate}">{$group.MEMBERS}</a><br>{$group.L_MEMBERS}{else}{$group.MEMBERS}{/if}</p>
      <p style="text-align: left;position: absolute;bottom: 0"><a class="buttonLike" href="{$group.U_PERM}" title="{'Permissions'|@translate}">{'Manage Permissions'|@translate}</a></p>
    </li>
    {/foreach}
    {/if}
  </ul>

  <fieldset id="action">
    <legend>{'Action'|@translate}</legend>
      <div id="forbidAction">{'No group selected, no action possible.'|@translate}</div>
      <div id="permitAction" style="display:none">

        <select name="selectAction">
          <option value="-1">{'Choose an action'|@translate}</option>
          <option disabled="disabled">------------------</option>
          <option value="rename">{'Rename'|@translate}</option>
          <option value="delete">{'Delete'|@translate}</option>
          <option value="merge">{'Merge selected groups'|@translate}</option>
          <option value="duplicate">{'Duplicate'|@translate}</option>
          <option value="manage_members">{'Manage the members'|@translate}</option>
          <option value="manage_pemissions">{'Manage Permissions'|@translate}</option>
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
          {$group.NAME} > {if $group.NB_MEMBERS!=0}<a class="buttonLike" href="{$group.U_MEMBERS}" title="{'Manage the members'|@translate}">{'Manage the members'|@translate}</a>{else}{'No members to manage'|@translate}{/if}
        </p>
        {/foreach}
        {/if}
        </div>

        <!-- manage_pemissions -->
        <div id="action_manage_pemissions" class="bulkAction">
        {if not empty($groups)}
        {foreach from=$groups item=group}
        <p group_id="{$group.ID}" class="grp_action">
          {$group.NAME} > <a class="buttonLike" href="{$group.U_PERM}" title="{'Permissions'|@translate}">{'Manage Permissions'|@translate}</a>
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