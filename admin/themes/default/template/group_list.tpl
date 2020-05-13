{footer_script}
var pwg_token = "{$PWG_TOKEN}";
var str_member_default = "{'member'|@translate}"
var str_members_default = "{'members'|@translate}"
var str_group_created = "{'Group added'|@translate}"
var str_renaming_done = "{'Group renamed'|@translate}"
var str_name_taken = "{'Name is already taken'|@translate}"
var str_group_deleted = '{'Group "%s" succesfully deleted'|@translate}'
var str_groups_deleted = '{'Groups \{%s\} succesfully deleted'|@translate}'
var str_set_default = "{'Set as group for new users'|@translate}"
var str_unset_default = "{'Unset as group for new users'|@translate}"
var str_delete = '{'Delete group "%s"?'|@translate}'
var str_yes_delete_confirmation = "{'Yes, delete'|@translate}"
var str_no_delete_confirmation = "{"No, I have changed my mind"|@translate}"
var str_user_associated = "{"User associated"|@translate}"
var str_user_dissociated = '{'User "%s" Dissociated from this group'|@translate}'
var str_user_list = "{"User List"|@translate}"

var serverKey = '{$CACHE_KEYS.users}'
var serverId = '{$CACHE_KEYS._hash}'
var rootUrl = '{$ROOT_URL}'
{/footer_script}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}

{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}

{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}
{combine_css path="admin/themes/default/fontello/css/animation.css"}

{combine_script id='common' load='footer' path='admin/themes/default/js/group_list.js'}

{* Define template function for the content of Groups*}
{function name=groupContent}
{function groupContent}
  <div id="group-{$grp_id}" class="GroupContainer" data-id={$grp_id} {if $grp_is_default}data-default=1 {else}data-default=0 {/if} style="order: -{$grp_id}">
    <div class="Group-checkbox in-selection-mode">
      <label class="Group-checkbox-label" for="Group-Checkbox-selection-{$grp_id}"></label>
      <input type="checkbox" id="Group-Checkbox-selection-{$grp_id}">
      <span class="group-checkmark"></span>
    </div>
    <div class="groupHeader">
      <div class="groupIcon"> 
        <div class="icon-users-1" style="color:{$icon_color};background-color:{$icon_background_color}"></div>
        <div class="groupMessage icon-ok"></div>
        <div class="groupError icon-cancel"></div>
      </div>

      <div class="icon-star not-in-selection-mode is-default-token{if !$grp_is_default} deactivate{/if}" ></div> 
     
      <div class="icon-ellipsis-vert group-dropdown-options not-in-selection-mode">
        <div id="GroupOptions">
          <option class="icon-docs group-dropdown-option" id="GroupDuplicate" value="duplicate">{'Duplicate'|@translate}</option>
          <option class="icon-trash group-dropdown-option" id="GroupDelete" value="delete">{'Delete'|@translate}</option>
          <option class="icon-star group-dropdown-option" id="GroupDefault" value="delete"></option>
        </div>
      </div>

      <div class="Group-name">
        <div class="Group-name-container">
          <p id="group_name">{$grp_name}</p>
          <span class="icon-pencil"></span>
        </div>
        <div class="group-rename">
          <form>
            <input type="text" class="group_name-editable" value="{$grp_name}">
            <input type="submit" hidden>
          </form>
          <span class="icon-ok validate"></span>
          <span class="icon-cancel"></span>
        </div>
      </div>

      <div id="EditGroupName">
        <input class="input-edit-group-name" type="text" name="username" maxlength="50" size="20" placeholder="{$grp_name}">
        <button class="icon-ok submit" name="submit_add" id="EditGroupNameSubmit" type="submit">Change group name</button>
        <a id="EditGroupcancel">{'Cancel'|@translate}</a>
      </div>

      <p class="group_number_users">{$grp_members}</p>
    </div>

    <a id="UserListTrigger" class="icon-user-1 manage-users not-in-selection-mode GroupManagerButtons">Manage users</a>
    <a class="icon-lock manage-permissions not-in-selection-mode GroupManagerButtons" href="admin.php?page=group_perm&group_id={$grp_id}">Manage permissions</a>
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
  <p>{'Selection mode'|@translate}</p>
</div>

<div id="selection-mode-block" class="in-selection-mode">
  <div class="Selection-mode-content">
   <p id="nothing-selected">{'No group selected, no action possible.'|@translate}</p>
   <div class="SelectionModeGroup">
    <p>{'Your selection'|@translate}</p>
    <div class="SelectionModeGroupList">

    <div class="DeleteGroupList">
      
    </div>

    </div>
    <button id="MergeSelectionMode" class="icon-object-group unavailable">{'Merge'|@translate}</button>
    <button id="DeleteSelectionMode" class="icon-trash-1 unavailable">{'Delete'|@translate}</button>
   </div>

   <div id="MergeOptionsBlock">
   <p>{'Choose which group to merge these groups into'|@translate}</p>
   <p class="ItalicTextInfo">{'The other groups will be removed'|@translate}</p>
    <div class="MergeOptionsContainer">
      <select id="MergeOptionsChoices"> 
      </select>
    </div>
    
    <button class="icon-ok ConfirmMergeButton">Confirm merge</button>
    <a id="CancelMerge" onclick="updateSelectionPanel('Selection')">Cancel</a>
   </div>
 

   <div id="ConfirmGroupAction">
    <p>You are about to delete <span class="number-Selected">0</span> groups, are you sure?</p>
    <button class="icon-ok ConfirmDeleteButton">{'Yes, delete'|@translate}</button>
    <a id="CancelDelete" onclick="updateSelectionPanel('Selection')">{"No, I have changed my mind"|@translate}</a>
    </div>
  
  </div>
</div>

<div class="group-manager">

  <div class="groups">

    <div id="addGroupForm" class="GroupContainer">
      <div class="groupError icon-cancel"></div>
      <div class="addGroupBlock">
        <div class="icon-plus-circled"></div>
        <p id="addGroup">{'Add group'|translate}</p>
      </div>
      <form>
        <fieldset>
           <div class="addGroupFormLabelAndInput">
            <label class="addGroupLabel" for="addGroupNameInput">{'Group name'|translate}</label>
            <input type="text" id="addGroupNameInput" name="groupname" maxlength="50" size="20" placeholder="Photographers...">
          </div>
          <div class="actionButtons">
            <button name="submit" type="submit" class="buttonLike">
              <i class='icon-plus'> </i> {'Add'|translate}
            </button> 
            <br/>
            <a id="addGroupClose" onclick="hideAddGroupForm()">{'Cancel'|@translate}</a>
          </div>
          <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
        </fieldset>
      </form>
    </div>

    {* Template Group (for js application) *}
    {groupContent grp_id="template" grp_name="Template" grp_members=0 grp_is_default=false}

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
        grp_is_default=$group.IS_DEFAULT
      }
      
    {/foreach}
    {/if}
  </div>

<div id="UserList" class="UserListPopIn">

  <div class="UserListPopInContainer">

    <a class="icon-cancel CloseUserList"></a>

    <div class="group-name-block">
      <span class="icon-list-bullet"></span>
      <p></p>
      <span class="UserNumberBadge">25</span>
    </div>

    <div class="UserListAddFilterUsers">
      <div class="AddUserBlock">
        <p>Associate User</p>
        <select class="UserSearch" placeholder="John Doe"></select>
        <button class="icon-user-add submit" name="submit_add" id="UserSubmit" type="submit"></button>
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