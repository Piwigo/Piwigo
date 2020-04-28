{footer_script}
var pwg_token = "{$PWG_TOKEN}";
var member_default = "{'member'|@translate}"
var group_created = "{'Group created'|@translate}"
var renaming_done = "{'Renaming done'|@translate}"
var name_taken = "{'Name is already taken'|@translate}"
{/footer_script}

{combine_script id='common' load='footer' path='admin/themes/default/js/group_list.js'}

{* Define template function for the content of Groups*}
{function name=groupContent}
{function groupContent}
  <div id="group-{$grp_id}" class="GroupContainer" data-id={$grp_id} style="order: -{$grp_id}">
    <div class="groupHeader"> 
      <div class="icon-users-1" style="color:{$icon_color};background-color:{$icon_background_color}"></div>
      <div class="groupMessage icon-ok"></div>
      <div class="groupError icon-cancel"></div>
    </div>
          
    <div class="icon-ellipsis-vert group-dropdown-options not-in-selection-mode">
      <div id="GroupOptions">
        <option class="icon-docs" id="GroupDuplicate" value="duplicate">{'Duplicate'|@translate}</option>
        <option class="icon-trash" id="GroupDelete" value="delete">{'Delete'|@translate}</option>
      </div>
    </div>

    <div class="Group-checkbox in-selection-mode">
      <label class="Group-checkbox-label" for="Group-Checkbox-selection-{$grp_id}"></label>
      <input type="checkbox" id="Group-Checkbox-selection-{$grp_id}">
      <span class="group-checkmark"></span>
    </div>

    <div class="Group-name">
      <span class="icon-pencil not-in-selection-mode"></span>
      <div class="Group-name-container">
        <p id="group_name" contenteditable="true">{$grp_name}</p>
      </div>
    </div>

    <div id="EditGroupName">
      <input class="input-edit-group-name" type="text" name="username" maxlength="50" size="20" placeholder="{$grp_name}">
      <button class="icon-ok submit" name="submit_add" id="EditGroupNameSubmit" type="submit">Change group name</button>
      <a id="EditGroupcancel">{'Cancel'|@translate}</a>
    </div>

    <p class="group_number_users">{$grp_members}</p>

    <div class="GroupManagerButtons visible">
      <a id="UserListTrigger" class="icon-user-1 manage-users">Manage users</a>
      <a class="icon-lock manage-permissions">Manage permissions</a>
    </div>
  </div>
{/function}
{/function}


<div class="titrePage">
  <h2>{'Group management'|@translate}</h2>
</div>

<div class="selection-mode-group-manager">
  <label class="switch">
    <input type="checkbox" id="toggleSelectionMode">
    <span class="slider round"></span>
  </label>
  <p> Selection mode</p>
</div>

<div id="selection-mode-block" class="in-selection-mode">
  <div class="Selection-mode-content">
   <p id="nothing-selected">No selection</p>
   <div class="SelectionModeGroup">
    <p> Your selection</p>
    <div class="SelectionModeGroupList">

    <div class="DeleteGroupList"> {* TODO *}
      
    </div>

    </div>
    <button id="MergeSelectionMode" class="icon-object-group" onclick="updateSelectionPanel('OptionMerge')">Merge</button>
    <button id="DeleteSelectionMode" class="icon-trash-1" onclick="updateSelectionPanel('ConfirmDeletion')">Delete</button>
   </div>

   <div id="MergeOptionsBlock">
   <p>Choose which group to merge these groups into</p>
   <p class="ItalicTextInfo">The other groups will be removed</p>
    <div class="MergeOptionsContainer">
      <label for="MergeOptionsChoices">Merge into:</label>
      <select id="MergeOptionsChoices"> {* TODO *}
      </select>
    </div>
    
    <button class="icon-ok ConfirmMergeButton" onclick="updateSelectionPanel('Selection')">Confirm merge</button>
    <a id="CancelMerge" onclick="updateSelectionPanel('Selection')">Cancel</a>
   </div>
 

   <div id="ConfirmGroupAction">
    <p>You are about to delete <span class="number-Selected">0</span> groups, are you sure?</p>
    <button class="icon-ok ConfirmDeleteButton" onclick="updateSelectionPanel('Selection')">Yes, delete</button>
    <a id="CancelDelete" onclick="updateSelectionPanel('Selection')">No, I have changed my mind</a>
    </div>
  
  </div>
</div>

<div class="group-manager">

  <div class="groups">

    <div class="showCreateGroup" id="showAddGroup">
      <div class="groupError icon-cancel"></div>
      <div class="addGroupBlock">
       <div class="icon-plus-circled"></div>
        <p id="addGroup">{'Add group'|translate}</p>
      </div>
    </div>

    <div id="addGroupForm">
      <div class="addGroupFormBlock">
        <form>
          <fieldset>
          <p class="addGroupFormTitle">{'Add group'|@translate}</p>
             <div class="addGroupFormLabelAndInput">
              <p class="addGroupLabel">{'Group name'|translate}</p>
              <input type="text" name="groupname" maxlength="50" size="20" placeholder="ExampleGroup">
            </div>
            <div class="actionButtons">   
              <input class="icon-ok submit" name="submit_add" type="submit" value="{'Add group'|translate}"><br/>
              <a id="addGroupClose">{'Cancel'|@translate}</a>
            </div>
            <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
          </fieldset>
        </form>
        </div>
    </div>

    {* Template Group (for js application) *}
    {groupContent grp_id="template" grp_name="Template" grp_members=0}

    {assign var='color_tab' value=[["#ffa744", "#ffe9cf"],["#896af3", "#e0daf4"], ["#6ece5e","#d6ffcf"],["#2883c3","#cfebff"]]}

    {if not empty($groups)}
    {foreach from=$groups item=group name=group_loop}
      {assign var='color_id' value=$group.ID%4}
      {groupContent 
        grp_id=$group.ID 
        grp_name=$group.NAME 
        grp_members=$group.MEMBERS 
        icon_color=$color_tab[$color_id][0]
        icon_background_color=$color_tab[$color_id][1]
      }
      
    {/foreach}
    {/if}
  </div>

<div id="UserList" class="UserListPopIn">

  <div class="UserListPopInContainer">

    <a class="icon-cancel CloseUserList"></a>

    <div class="group-name-block">
      <span class="icon-list-bullet"></span>
      <p id="group_name" contenteditable="true">{$group.NAME} / User List</p>
      <span class="UserNumberBadge">25</span>
    </div>

    <div class="UserListAddFilterUsers">
      <div class="AddUserBlock">
        <p>Associate User</p>
        <input class="input-user-name" type="text" name="username" maxlength="50" size="20" placeholder="ExampleUser">
        <button class="icon-ok submit" name="submit_add" id="UserSubmit" type="submit">Select user</button>
      </div>
      <div class="FilterUserBlock">
        <div class="AmountOfUsersShown">
          <p>Showing <strong>39</strong> users out of <strong>251</strong></p>
        </div>
        <span class="icon-filter"></span>
        <p>Filter</p>
        <input class="input-user-name" type="text" name="username" maxlength="50" size="20" placeholder="Username">
      </div>
    </div>

    <div class="UsersInGroupListContainer">
    
      <div class="UsersInGroupList row">
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Hester Hampton</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class='ValidationUserAssociated'>
           <p class="icon-ok">User Associated</p>
          </div>
        </div>
  
    </div>

  
    <div class="LinkUserManager">
      <a>
      <span class="icon-users-cog"></span>
      Manage users with user manager</a>
    </div>
      
  </div>

</div>
</div>