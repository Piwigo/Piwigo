{footer_script}

var pwg_token = "{$PWG_TOKEN}";
var member_default = "{'member'|@translate}"

{literal}


/*-------
Group Popup
-------*/

$(".group_details_popup_trigger").click(function(){
   $('.Group_details-popup-container').show();
});

$(".CloseGroupPopup").click(function(){
   $('.Group_details-popup-container').hide();
});


/*-------
Add User toggle and reduces height of user list when add user form is visible
-------*/

$('#form-btn').click(function(){
  $('#cancel').show();
  $('#addUserLabel').show()
  $('.input-user-name').show();
  $('#UserSubmit').show();
  $('#form-btn').hide();
  $(".groups .list_user").css("max-height","100px");
});

$('#cancel').click(function(){
  $('#cancel').hide();
  $('#addUserLabel').hide()
  $('.input-user-name').hide();
  $('#UserSubmit').hide();
  $('#form-btn').show();
  $(".groups .list_user").css("max-height","200px");
});

/*-------
Add Group toggle
-------*/

$('#showAddGroup').click(function(){
  $('#addGroupForm').css('display', 'inline-block');
  $('#showAddGroup').css('display', 'none');
});

var closeAddGroup = function () {
  $('#addGroupForm').css('display', 'none');
  $('#showAddGroup').css('display', 'inline-block');
}

$('#addGroupClose').click(closeAddGroup);

$('.actionButtons input').click(closeAddGroup);

/*-------
Add Group Submit
-------*/

jQuery(document).ready(function() {
  $('#addGroupForm form').on("submit", function(e) {
    e.preventDefault();
    let name = $('#addGroupForm input[type=text]').val();
    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.groups.add",
      type:"POST",
      data: "name="+name+"&pwg_token="+pwg_token,
      success: function(raw_data) {
        data = jQuery.parseJSON(raw_data);
        if (data.stat === "ok") {
          id = data.result.groups[0].id;
          newgroup = $('#group-template').clone().attr('id', "group-"+id);
          newgroup.attr("data-id", id);
          newgroup.find("#group_name").html(name);
          newgroup.find(".Group-checkbox label").attr("for", "Group-Checkbox-selection-"+id)
          newgroup.find(".Group-checkbox input").attr("id", "Group-Checkbox-selection-"+id)
          newgroup.find(".input-edit-group-name").attr('placeholder', name);
          newgroup.find(".group_number_users").html("0 "+member_default);
          setupGroupBox(newgroup);
          newgroup.appendTo('.groups')
        }
      },
      error: function(err) {
        console.log(err);
      }
    });
  });
});

/*-------
User List group pop-in
-------*/

var modalUL = document.getElementById("UserList");

var btn = document.getElementById("UserListTrigger");

var close = document.getElementsByClassName("CloseUserList")[0];

btn.onclick = function() {
  modalUL.style.visibility = "visible";
}

close.onclick = function() {
  modalUL.style.visibility = "hidden";
}

/*-------
SETUP JS ON GROUP BOX
-------*/
jQuery(document).ready(function() {
  $(".GroupContainer").each(function () {
    setupGroupBox($(this));
  })
});

var setupGroupBox = function (groupBox) {

  var id = groupBox.data('id');

  /* Change background color of group block if checked in selection mode */
  groupBox.find(".Group-checkbox input[type='checkbox']").change(function(){
      if($(this).is(":checked")){
        groupBox.addClass("OrangeBackground"); 
        groupBox.find('.icon-users-1').addClass("OrangeIcon");
        groupBox.find('.group_number_users').addClass("OrangeFont");

        item = $("<div data-id="+groupBox.data('id')+">"
        +"<a class='icon-cancel'></a>"
        +"<p>"+groupBox.find("#group_name").html()+"</p> </div>");
        item.appendTo(".DeleteGroupList");
        item.find("a").on('click', function() {
          groupBox.find(".Group-checkbox input").attr("checked", false);
          groupBox.find(".Group-checkbox input[type='checkbox']").trigger("change");
        });
      }else{
        groupBox.removeClass("OrangeBackground"); 
        groupBox.find('.icon-users-1').removeClass("OrangeIcon"); 
        groupBox.find('.group_number_users').removeClass("OrangeFont");
        $(".DeleteGroupList div").each(function(){
          if ($(this).data('id') == id) {$(this).remove()}
        })
      } 
  });

  /* Display the option on the click on "..." */
  groupBox.find(".group-dropdown-options").click(function GroupOptions(){
    $(this).find("#GroupOptions").toggle();
  });

  /* Set the delete action */
  groupBox.find("#GroupDelete").on('click', function(){
    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.groups.delete",
      type:"POST",
      data: "group_id="+id+"&pwg_token="+pwg_token,
      success: function(raw_data) {
        data = jQuery.parseJSON(raw_data);
        if (data.stat === "ok") {
          groupBox.remove();
        }
      },
      error: function(err) {
        console.log(err);
      }
    });
  });
}

/*-------
Selection mode toggle actions,  
changes "..." in group block to checkbox,
disables group actions in selection mode
-------*/

$(function () {
  $("#toggleSelectionMode").click(function () {
    if ($(this).is(":checked")) {
      $(".in-selection-mode").fadeIn();
      $(".not-in-selection-mode").fadeOut();
      $("#group_name").attr('contenteditable', false);
    } else {
      $(".in-selection-mode").fadeOut();
      $(".not-in-selection-mode").fadeIn();
      $(".Group-checkbox input").attr("checked", false);
      $(".Group-checkbox input[type='checkbox']").trigger("change");
      $("#group_name").attr('contenteditable', true);
    }
  });
});


/*-------
Display group name in selection list
-------*/
/*
jQuery(document).ready(function() {
        $("#Group-Checkbox-selection").click(function () {
            if ($(this).is(":checked")) {
              $('.SelectionModeGroup').show();
              $('#nothing-selected').hide();
            }else{
              $('.SelectionModeGroup').hide();
              $('#nothing-selected').show();
            }
        )};
});
*/

/*-------
Hide group options on click on the screen
-------*/

$(document).mouseup(function (e) { 
  if ($(e.target).closest("#GroupOptions").length === 0) { 
    $(".group-dropdown-options #GroupOptions").hide(); 
  } 
});

/*-------
Merge Options in selection mode
-------*/

function MergeOptions(){
  jQuery("#MergeOptionsBlock").show();
  jQuery("#DeleteSelectionMode").hide();
  jQuery("#MergeSelectionMode").hide();
};

/*-------
Cancel Merge options or deletion confirmation
-------*/

function CancelAction(){
  jQuery("#MergeOptionsBlock").hide();
  jQuery("#ConfirmGroupAction").hide();
  jQuery("#DeleteSelectionMode").show();
  jQuery("#MergeSelectionMode").show();
};

/*-------
Display deletion confirmation
-------*/

function DeleteValidation(){
  jQuery("#ConfirmGroupAction").show();
  jQuery("#DeleteSelectionMode").hide();
  jQuery("#MergeSelectionMode").hide();
};



{/literal}
{/footer_script}

{* Define template function for the content of Groups*}
{function name=groupContent}
{function groupContent}
  <div id="group-{$grp_id}" class="GroupContainer" data-id={$grp_id}>
    <div class="icon-users-1"></div>
          
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

    <div class="GroupManagerButtons not-in-selection-mode">
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
    <button id="MergeSelectionMode" class="icon-object-group" onclick="MergeOptions()">Merge</button>
    <button id="DeleteSelectionMode" class="icon-trash-1" onclick="DeleteValidation()">Delete</button>
   </div>

   <div id="MergeOptionsBlock">
   <p>Choose which group to merge these groups into</p>
   <p class="ItalicTextInfo">The other groups will be removed</p>
    <div class="MergeOptionsContainer">
      <label for="MergeOptionsChoices">Merge into:</label>
      <select id="MergeOptionsChoices"> {* TODO *}
      </select>
    </div>
    
    <button class="icon-ok ConfirmMergeButton" onclick="CancelAction()">Confirm merge</button>
    <a id="CancelMerge" onclick="CancelAction()">Cancel</a>
   </div>
 

   <div id="ConfirmGroupAction">
    <p>You are about to delete [4] groups, are you sure?</p>
    <button class="icon-ok ConfirmDeleteButton" onclick="CancelAction()">Yes, delete</button>
    <a id="CancelDelete" onclick="CancelAction()">No, I have changed my mind</a>
    </div>
  
  </div>
</div>

<div class="group-manager">

  <div class="groups">

    <div class="showCreateGroup" id="showAddGroup">
      <div class="addGroupBlock">
       <div class="icon-plus-circled"></div>
        <p id="addGroup">{'Add group'|translate}</p>
      </div>
    </div>

    <div id="addGroupForm">
      <div class="addGroupFormBlock">
      <div class="icon-users-1"></div>
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

    {if not empty($groups)}
    {foreach from=$groups item=group name=group_loop}
      {groupContent grp_id=$group.ID grp_name=$group.NAME grp_members=$group.MEMBERS}
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