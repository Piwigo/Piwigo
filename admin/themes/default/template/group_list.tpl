{footer_script}
{literal}
$(document).ready(function() {
  $('.groups input').change(function () { $(this).parent('p').toggleClass('group_select'); });
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

    if (nbSelected2) {
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
    if ($(this).val() != -1 ) {
      $("#applyActionBlock").show();
    }
    else {
      $("#applyActionBlock").hide();
    }
  });

});

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
  $('#input-user-name').show();
  $('#UserSubmit').show();
  $('#form-btn').hide();
  $(".groups .list_user").css("max-height","100px");
});

$('#cancel').click(function(){
  $('#cancel').hide();
  $('#addUserLabel').hide()
  $('#input-user-name').hide();
  $('#UserSubmit').hide();
  $('#form-btn').show();
  $(".groups .list_user").css("max-height","200px");
});

/*-------
Add Group toggle
-------*/

$('#addGroup').click(function(){
  $('#addGroupForm').css('display', 'inline-block');
  $('#showAddGroup').css('display', 'none');
});

$('#addGroupClose').click(function(){
  $('#addGroupForm').css('display', 'none');
  $('#showAddGroup').css('display', 'inline-block');
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
Selection mode toggle actions,  
changes "..." in group block to checkbox,
disables group actions in selection mode
-------*/

$(function () {
        $("#toggleSelectionMode").click(function () {
            if ($(this).is(":checked")) {
                $("#selection-mode-block").show();
                $(".group-dropdown-options").css('display', 'none');
                $(".Group-checkbox").css('display','block');
                $(".GroupManagerButtons").addClass("DisableInSelectionMode");
                $("#group_name").attr('contenteditable', false);
                $(".icon-pencil").addClass("DisableInSelectionMode");
                $("label").css("cursor","default")
            } else {
                $("#selection-mode-block").hide();
                $(".group-dropdown-options").css('display', 'block');
                $(".Group-checkbox").css('display','none');
                $(".Group-checkbox-container input").prop("checked", false);
                $('#group-1 li').removeClass("OrangeBackground"); 
                $('.groups .icon-users-1:first-child').removeClass("GreyBackground"); 
                $(".GroupManagerButtons").removeClass("DisableInSelectionMode");
                $("#group_name").attr('contenteditable', true);
                $(".icon-pencil").removeClass("DisableInSelectionMode");
                $("label").css("cursor","pointer")
            }
        });
    });


/*-------
Change background color of group block if checked in selection mode
-------*/

$(".Group-checkbox-container input[type='checkbox']").change(function(){
if($(this).is(":checked")){
    $('#group-1 li').addClass("OrangeBackground"); 
    $('.groups .icon-users-1:first-child').addClass("GreyBackground");
    $('.groups .group_number_users').addClass("GreyFont");
}else{
    $('#group-1 li').removeClass("OrangeBackground"); 
    $('.groups .icon-users-1:first-child').removeClass("GreyBackground"); 
} });

/*-------
Display group name in selection list
-------*/
/*
$(function () {
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
show group options in "..." on click
-------*/

$(".group-dropdown-options").click(function GroupOptions(){
    $("#GroupOptions").toggle();
  });

$(document).mouseup(function (e) { 
            if ($(e.target).closest("#GroupOptions").length 
                        === 0) { 
                $("#GroupOptions").hide(); 
            } 
        }); 

/*-------
Merge Options in selection mode
-------*/

function MergeOptions(){
  document.getElementById("MergeOptionsBlock").style.display = "block";
  document.getElementById("DeleteSelectionMode").style.display = "none";
  document.getElementById("MergeSelectionMode").style.display = "none";
};

/*-------
Cancel Merge options
-------*/

function CancelAction(){
  document.getElementById("MergeOptionsBlock").style.display = "none";
  document.getElementById("ConfirmGroupAction").style.display = "none";
  document.getElementById("DeleteSelectionMode").style.display = "block";
  document.getElementById("MergeSelectionMode").style.display = "block";
};

/*-------
Display deletion confirmation
-------*/

function DeleteValidation(){
  document.getElementById("ConfirmGroupAction").style.display = "block";
  document.getElementById("DeleteSelectionMode").style.display = "none";
  document.getElementById("MergeSelectionMode").style.display = "none";
};

{/literal}
{/footer_script}



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

<div id="selection-mode-block">
  <div class="Selection-mode-content">
   <p id="nothing-selected">No selection</p>
   <div class="SelectionModeGroup">
    <p> Your selection</p>
    <div class="SelectionModeGroupList">

    <div >
      <a class="icon-cancel"></a>
      <p>Group</p>  
    </div>  
    <div >
      <a class="icon-cancel"></a>
      <p>Group</p>  
    </div>
    <div >
      <a class="icon-cancel"></a>
      <p>Group</p>  
    </div>
    <div>
      <a class="icon-cancel"></a>
      <p>Group</p>  
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
      <select id="MergeOptionsChoices">
        <option value="volvo">Group 1 Group 1 Group1 Group1</option>
        <option value="saab">Group 2</option>
        <option value="opel">Group 3</option>
        <option value="audi">Group 4</option>
      </select>
    </div>
    
    <button class="icon-ok ConfirmMergeButton">Confirm merge</button>
    <a id="CancelMerge" onclick="CancelAction()">Cancel</a>
   </div>
 

   <div id="ConfirmGroupAction">
    <p>You are about to delete [4] groups, are you sure?</p>
    <button class="icon-ok ConfirmDeleteButton">Yes, delete</button>
    <a id="CancelDelete" onclick="CancelAction()">No, I have changed my mind</a>
    </div>
  
  </div>
</div>

<div class="group-manager">
<form method="post" name="add_user" action="{$F_ADD_ACTION}" class="properties">
  <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">

  <ul class="groups">

    <div class="showCreateAlbum" id="showAddGroup">
      <div class="addGroupBlock">
       <div class="icon-plus-circled"></div>
        <a href="#" id="addGroup">{'Add group'|translate}</a>
      </div>
    </div>

    <div id="addGroupForm">
      <div class="addGroupFormBlock">
      <div class="icon-users-1"></div>
        <form method="post" name="add_user" action="{$F_ADD_ACTION}" class="properties">
          <fieldset>
          <p class="addGroupFormTitle">{'Add group'|@translate}</p>
             <div class="addGroupFormLabelAndInput">
              <p class="addGroupLabel">{'Group name'|translate}</p>
              <input type="text" name="groupname" maxlength="50" size="20" placeholder="ExampleGroup">
            </div>
            <div class="actionButtons">   
              <button class="icon-ok submit" name="submit_add" type="submit">{'Add group'|translate}</button>
              <a href="#" id="addGroupClose">{'Cancel'|@translate}</a>
            </div>
            <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">

          </fieldset>
        </form>
        </div>
    </div>

    {if not empty($groups)}
    {foreach from=$groups item=group name=group_loop}
    <div id="group-{$group.ID}" class="GroupContainer">
      <li>
        <div class="icon-users-1"></div>
        
        <div class="icon-ellipsis-vert group-dropdown-options" onclick="GroupOptions()">
          <div id="GroupOptions">
              <option class="icon-docs" id="GroupDuplicate" value="duplicate">{'Duplicate'|@translate}</option>
              <option class="icon-trash" id="GroupDelete" value="delete">{'Delete'|@translate}</option>
          </div>
        </div>

        <div class="Group-checkbox">
          <label class="Group-checkbox-container">
            <input type="checkbox" id="Group-Checkbox-selection">
            <span class="group-checkmark"></span>
          </label>
        </div>
        <label>
          <span class="icon-pencil"></span>
          <div class="Group-name-container">
            <p id="group_name" contenteditable="true">{$group.NAME}</p>
          </div>
        </label>

        <div id="EditGroupName">
          <input id="input-edit-group-name" type="text" name="username" maxlength="50" size="20" placeholder="{$group.NAME}">
          <button class="icon-ok submit" name="submit_add" id="EditGroupNameSubmit" type="submit">Change group name</button>
          <a id="EditGroupcancel">{'Cancel'|@translate}</a>
        </div>

        <p class="group_number_users">{$group.MEMBERS}</p>

      <div class="GroupManagerButtons">
        <a id="UserListTrigger" class="icon-user-1 manage-users">Manage users</a>
        <a class="icon-lock manage-permissions">Manage permissions</a>
      </div>

      <!-- <a class="icon-group group_users" href="{$group.U_USERS}">{'Users'|translate}</a>
        <a class="icon-lock group_perm" href="{$group.U_PERM}">{'Permissions'|translate}</a>-->
      </li>
    </div>
    {/foreach}
    {/if}
  </ul>

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
        <input id="input-user-name" type="text" name="username" maxlength="50" size="20" placeholder="ExampleUser">
        <button class="icon-ok submit" name="submit_add" id="UserSubmit" type="submit">Select user</button>
      </div>
      <div class="FilterUserBlock">
        <div class="AmountOfUsersShown">
          <p>Showing <strong>39</strong> users out of <strong>251</strong></p>
        </div>
        <span class="icon-filter"></span>
        <p>Filter</p>
        <input id="input-user-name" type="text" name="username" maxlength="50" size="20" placeholder="Username">
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
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Lexie Driscoll</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Kenan Fernandez</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Rafi Mccall</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Mai Riley</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
        <span class="icon-user-1"></span>
        <p>Kim Mckee</p>
        <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Eilidh Galindo</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Joann Wilson</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Guy Hutchings</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Tyrese Levine</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Ibrar Gill</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Gabrielle Byers</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Markus Tapia</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Humera Davila</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Elaina Carrillo</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Glenda Akhtar</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Aeryn Wall</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Florrie Vu</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Paisley Huber</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Ishmael Cochran</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Lexi-Mai Cullen</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Umair Long</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Liyana Lawrence</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Madeeha Tomlinson</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Kadie Schneider</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Cassian Mooney</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Elsie-Mae Leblanc</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Blossom Rollins</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Selena Summers</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Amari Swanson</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Jolie Pearce</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Lillie-Rose Wheatley</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Buster Howell</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Darcey Duarte</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Grady Irwin</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Dru Wilder</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <!--<div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Amit Ashley</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Rajan Leigh</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Nathalie Newton</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>
        <div class="UsernameBlock">
          <span class="icon-user-1"></span>
          <p>Lance Clemons</p>
          <div class="Tooltip">
            <span class="icon-cancel"></span>
            <p class="TooltipText">Dissociate user from this group</p>
          </div>
        </div>-->
        </div>
  
    </div>

  
    <div class="LinkUserManager">
      <a>
      <span class="icon-users-cog"></span>
      Manage users with user manager</a>
    </div>
      
  </div>

</div>

</form>
</form>
</div>