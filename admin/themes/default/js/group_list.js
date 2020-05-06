const DELAY_FEEDBACK = 3000;

/*-------
Group Popup
-------*/

$(".group_details_popup_trigger").click(function () {
  $(".Group_details-popup-container").show();
});

$(".CloseGroupPopup").click(function () {
  $(".Group_details-popup-container").hide();
});

/*-------
 Add User toggle and reduces height of user list when add user form is visible
 -------*/

$("#form-btn").click(function () {
  $("#cancel").show();
  $("#addUserLabel").show();
  $(".UserSearch").show();
  $("#UserSubmit").show();
  $("#form-btn").hide();
  $(".groups .list_user").css("max-height", "100px");
});

$("#cancel").click(function () {
  $("#cancel").hide();
  $("#addUserLabel").hide();
  $(".UserSearch").hide();
  $("#UserSubmit").hide();
  $("#form-btn").show();
  $(".groups .list_user").css("max-height", "200px");
});

/*-------
 Add Group toggle
 -------*/
var isToggle = true;
$(".addGroupBlock").on("click", function() {
  if (isToggle) deployAddGroupForm()
  else hideAddGroupForm();
})

var deployAddGroupForm = function () {
  $(".addGroupBlock").animate({
    top: "20%",
    padding: "0px"
  }, 400, complete=function(){
    $("#addGroupForm form").fadeIn();
  });
  isToggle = false;
}

var hideAddGroupForm = function () {
  $("#addGroupForm form").fadeOut(function(){
    $(".addGroupBlock").animate({
      top: "50%",
      padding: "100px 0"
    }, 400)
  });
  isToggle = true;
}

/*-------
 Add Group Submit
 -------*/

jQuery(document).ready(function () {
  $("#addGroupForm form").on("submit", function (e) {
    e.preventDefault();
    let name = $("#addGroupForm input[type=text]").val();
    let loadState = new TemporaryState();
    loadState.changeHTML($(".actionButtons button"), "<i class='icon-spin6 animate-spin'> </i>Loading...");
    loadState.changeAttribute($(".actionButtons button"), "style", "pointer-events: none");
    loadState.changeAttribute($(".actionButtons a"), "style", "pointer-events: none");
    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.groups.add",
      type: "POST",
      data: "name=" + name + "&pwg_token=" + pwg_token,
      success: function (raw_data) {
        loadState.reverse();
        data = jQuery.parseJSON(raw_data);
        if (data.stat === "ok") {
          $(".addGroupFormLabelAndInput input").val('');
          id = data.result.groups[0].id;
          //Setup the group
          newgroup = $("#group-template").clone().attr("id", "group-" + id);
          newgroup.css("order", -id);
          newgroup.attr("data-id", id);
          newgroup.find("#group_name").html(name);
          newgroup.find(".Group-checkbox label").attr("for", "Group-Checkbox-selection-" + id);
          newgroup.find(".Group-checkbox input").attr("id", "Group-Checkbox-selection-" + id);
          newgroup.find(".input-edit-group-name").attr("placeholder", name);
          newgroup.find(".group_number_users").html("0 " + str_member_default);
          newgroup.find(".group_name-editable").html(name);
          hideAddGroupForm();
          //Setup the icon color
          var colors = [["#ffa744", "#ffe9cf"],["#896af3", "#e0daf4"], ["#6ece5e","#d6ffcf"],["#2883c3","#cfebff"]];
          var colorId = Number(id)%4;
          newgroup.find(".icon-users-1").attr("style", "color:"+colors[colorId][0]+"; background-color:"+colors[colorId][1]);  
          
          setupGroupBox(newgroup);
          
          //Place group in first Place 
          newgroup.appendTo(".groups");
          newgroup.find(".groupMessage").html(str_group_created);
          newgroup.find(".groupMessage").fadeIn();
          newgroup.find(".groupMessage").delay(DELAY_FEEDBACK).fadeOut();
        } else {
          $("#showAddGroup .groupError").html(str_name_taken);
          $("#showAddGroup .groupError").fadeIn();
          $("#showAddGroup .groupError").delay(DELAY_FEEDBACK).fadeOut();
        }
      },
      error: function (err) {
        console.log(err);
      },
    });
  });
});

/*-------
 SETUP JS ON GROUP BOX
 -------*/
jQuery(document).ready(function () {
  $(".GroupContainer").each(function () {
    setupGroupBox($(this));
  });
});

var setupGroupBox = function (groupBox) {
  var id = groupBox.data("id");

  /* Change background color of group block if checked in selection mode */
  groupBox.find(".Group-checkbox input[type='checkbox']").change(function () {
    if ($(this).is(":checked")) {
      groupBox.addClass("OrangeBackground");
      groupBox.find(".icon-users-1").addClass("OrangeIcon");
      groupBox.find(".group_number_users").addClass("OrangeFont");

      //Display item selection on selection panel
      item = $(
        "<div data-id=" +
          groupBox.data("id") +
          ">" +
          "<a class='icon-cancel'></a>" +
          "<p>" +
          groupBox.find("#group_name").html() +
          "</p> </div>"
      );
      item.appendTo(".DeleteGroupList");
      item.find("a").on("click", function () {
        groupBox.find(".Group-checkbox input").attr("checked", false);
        groupBox
          .find(".Group-checkbox input[type='checkbox']")
          .trigger("change");
      });
      updateSelectionPanel();
      option = $('<option value="'+id+'">'+groupBox.find("#group_name").html()+'</option>')
      option.appendTo("#MergeOptionsChoices");
    } else {
      groupBox.removeClass("OrangeBackground");
      groupBox.find(".icon-users-1").removeClass("OrangeIcon");
      groupBox.find(".group_number_users").removeClass("OrangeFont");
      $(".DeleteGroupList div").each(function () {
        if ($(this).data("id") == id) {
          $(this).remove();
        }
      });
      updateSelectionPanel();
      $("#MergeOptionsChoices option").each(function () {
        if ($(this).attr("value") == id) {
          $(this).remove();
        }
      });
    }
  });

  /* Display the option on the click on "..." */
  groupBox.find(".group-dropdown-options").click(function GroupOptions() {
    $(this).find("#GroupOptions").toggle();
  });

  /* Set the delete action */
  groupBox.find("#GroupDelete").on("click", function () {
    $.confirm({
      title: str_delete+' '+groupBox.find("#group_name").html(),
      content: str_are_you_sure,
      boxWidth: '30%',
      useBootstrap: false,
      type: 'red',
      typeAnimated: true,
      buttons: {
          confirm: {
            text: str_yes_delete_confirmation,
            btnClass: 'btn-red',
            action: function () {
              deleteGroup(id);
            }
          },
          cancel: {
            text: str_no_delete_confirmation
          }
      }
    });
  });

  /* Set the rename action */
  groupBox.find(".Group-name .icon-pencil").on("click", function () {
    displayRenameForm(true, id);
  });

  groupBox.find(".group-rename .validate").on("click", function () {
      groupBox.find(".group-rename form").trigger("submit");
  });

  groupBox.find(".group-rename form").on("submit", function (e) {
    console.log("submit");
    e.preventDefault();
    if (groupBox.find(".group_name-editable").val() != groupBox.find("#group_name").html())
      renameGroup(id, groupBox.find(".group_name-editable").val())
    else 
      displayRenameForm(false, id)
  });

  groupBox.find(".group-rename .icon-cancel").on('click', function() {
    displayRenameForm(false, id)
    groupBox.find(".group_name-editable").val(groupBox.find(".Group-name-container p").html());
  })

  /* Hide group options and rename field on click on the screen */

  $(document).mouseup(function (e) {
    if ($(e.target).closest("#group-"+id+" #GroupOptions").length === 0) {
      groupBox.find(".group-dropdown-options #GroupOptions").hide();
    }
  });

  /* Setup the default action */
  if (groupBox.data("default") == 1) {
    setupDefaultActions(id, true);
  } else if (groupBox.data("default") == 0) {
    setupDefaultActions(id, false);
  }

  groupBox.find(".manage-users").on("click", function(){openUserManager(id)});
  

};

/* Group Ajax and Display Functions */
var deleteGroup = function (id) {
  jQuery.ajax({
    url: "ws.php?format=json&method=pwg.groups.delete",
    type: "POST",
    data: "group_id=" + id + "&pwg_token=" + pwg_token,
    success: function (raw_data) {
      data = jQuery.parseJSON(raw_data);
      if (data.stat === "ok") {
        $.alert({
          title: str_group_deleted,
          content: "",
          boxWidth: '20%',
          useBootstrap: false
        });
        $("#group-" + id).remove();
      }
    },
    error: function (err) {
      console.log(err);
    },
  });
};

var renameGroup = function(id, newName) {
  let loadState = new TemporaryState();
  loadState.changeHTML($("#group-" + id + " .group-rename .validate"), "<i class='animate-spin icon-spin6'></i>")
  loadState.removeClass($("#group-" + id + " .group-rename .validate"), "icon-ok")
  loadState.changeAttribute($("#group-" + id + " .group-rename span"), "style", "pointer-events: none");
  jQuery.ajax({
    url: "ws.php?format=json&method=pwg.groups.setInfo",
    type: "POST",
    data: "group_id=" + id + "&pwg_token=" + pwg_token + "&name="+newName,
    success: function (raw_data) {
      data = jQuery.parseJSON(raw_data);
      loadState.reverse();
      if (data.stat === "ok") {
        newName = data.result.groups[0].name;
        //Display message
        $("#group-" + id).find(".groupMessage").html(str_renaming_done);
        $("#group-" + id).find(".groupMessage").fadeIn();
        $("#group-" + id).find(".groupMessage").delay(DELAY_FEEDBACK).fadeOut();
        $("#group-" + id).find("#group_name").html(newName);

        //Hide editable field
        displayRenameForm(false, id);
      } else {
        //Display error message
        $("#group-" + id).find(".groupError").html(str_name_taken);
        $("#group-" + id).find(".groupError").fadeIn();
        $("#group-" + id).find(".groupError").delay(DELAY_FEEDBACK).fadeOut();
      }
    },
    error: function (err) {
      console.log(err);
    },
  });
}

// Hide or display rename form
var displayRenameForm = function(doDisplay, grp_id) {
  if (doDisplay) {
    $("#group-" + grp_id).find(".group-rename").css("display", "flex");
    $("#group-" + grp_id).find(".Group-name-container .icon-pencil").hide();
    $("#group-" + grp_id).find(".Group-name-container p").css("opacity", 0)
  } else {
    $("#group-" + grp_id).find(".group-rename").hide();
    $("#group-" + grp_id).find(".Group-name-container .icon-pencil").removeAttr("style");
    $("#group-" + grp_id).find(".Group-name-container p").css("opacity", 1);
  }
}

var setDefaultGroup = function (id, is_default) {
  let loadState = new TemporaryState();
  loadState.removeClass($("#group-" + id + " .is-default-token"), "icon-star");
  loadState.addClass($("#group-" + id + " .is-default-token"), "icon-spin6");
  loadState.addClass($("#group-" + id + " .is-default-token"), "animate-spin");
  loadState.changeAttribute($("#group-" + id + " .is-default-token"), "style", "pointer-events: none; display:block")
  loadState.changeAttribute($("#group-" + id + " #GroupDefault"), "style", "pointer-events: none")
  jQuery.ajax({
    url: "ws.php?format=json&method=pwg.groups.setInfo",
    type: "POST",
    data: "group_id=" + id + "&pwg_token=" + pwg_token + "&is_default="+is_default,
    success: function (raw_data) {
      data = jQuery.parseJSON(raw_data);
      loadState.reverse();
      if (data.stat === "ok") {
        if (is_default) {
          setupDefaultActions(id,true)
          //$("#group-" + id).find(".groupMessage").html(str_set_default_state);
        } else {
          setupDefaultActions(id,false)
          //$("#group-" + id).find(".groupMessage").html(str_unset_default_state);
        }
        //$("#group-" + id).find(".groupMessage").fadeIn();
        //$("#group-" + id).find(".groupMessage").delay(DELAY_FEEDBACK).fadeOut();
      }
    },
    error: function (err) {
      console.log(err);
    },
  });
};

var setupDefaultActions = function(id, is_default) {
  if (is_default) {
    $("#group-" + id).find("#GroupDefault").html(str_unset_default);
    $("#group-" + id).find(".is-default-token").attr("title",str_unset_default)
    $("#group-" + id).find(".is-default-token").removeClass("deactivate");
    $("#group-" + id).find("#GroupDefault").unbind("click");
    $("#group-" + id).find("#GroupDefault").on("click", function(){setDefaultGroup(id, false)})
    $("#group-" + id).find(".is-default-token").on("click", function(){setDefaultGroup(id, false)})
  } else {
    $("#group-" + id).find("#GroupDefault").html(str_set_default);
    $("#group-" + id).find(".is-default-token").attr("title",str_set_default)
    $("#group-" + id).find(".is-default-token").addClass("deactivate");;
    $("#group-" + id).find("#GroupDefault").unbind("click");
    $("#group-" + id).find("#GroupDefault").on("click", function(){setDefaultGroup(id, true)})
    $("#group-" + id).find(".is-default-token").unbind("click");
  }
}


/*-------
 Selection mode toggle actions,  
 changes "..." in group block to checkbox,
 disables group actions in selection mode
 -------*/

$(function () {
  $("#toggleSelectionMode").click(function () {
    if ($(this).is(":checked")) {
      $(".in-selection-mode").show();
      $(".not-in-selection-mode").hide();
      $(".GroupManagerButtons").removeClass("visible");
    } else {
      $(".in-selection-mode").fadeOut();
      $(".not-in-selection-mode").removeAttr("style");
      $(".Group-checkbox input").attr("checked", false);
      $(".Group-checkbox input[type='checkbox']").trigger("change");
    }
  });
});

/*-------
 Update Selection Panel
 -------*/
var state = "NoSelection";

var updateSelectionPanel = function (changedState = "") {
  let numSelect = $(".DeleteGroupList div").length;
  
  if (numSelect == 0) {
    updateStatePanel("NoSelection")
  } else if (changedState == "") {
    if (numSelect == 1 && state != "ConfirmDeletion")
      updateStatePanel("OneSelected")
    if (numSelect > 1 && state == "OneSelected")
      updateStatePanel("Selection");
  } else {
    if (changedState == "Selection" && numSelect == 1)
      updateStatePanel("OneSelected")
    else 
      updateStatePanel(changedState)
  }

  console.log(state);

  $(".number-Selected").html(numSelect + "");
};

/*Update the state of the panel in 5 states :
 NoSelection, OneSelected, ConfirmDeletion, Selection, OptionMerge
 */
var updateStatePanel = function (newState = "Selection") {
  state = newState;
  switch (newState) {
    case "OneSelected":
    $("#DeleteSelectionMode").show();
    $("#MergeSelectionMode").show();
    buttonUnavailable($("#MergeSelectionMode"));
    buttonAvailable($("#DeleteSelectionMode"), "updateSelectionPanel('ConfirmDeletion')");
    $("#MergeOptionsBlock").hide();
    $("#ConfirmGroupAction").hide();
      break;
    case "ConfirmDeletion":
    $("#DeleteSelectionMode").hide();
    $("#MergeSelectionMode").hide();
    $("#MergeOptionsBlock").hide();
    $("#ConfirmGroupAction").show();
      break;
    case "Selection":
    $("#DeleteSelectionMode").show();
    $("#MergeSelectionMode").show();
    buttonAvailable($("#MergeSelectionMode"), "updateSelectionPanel('OptionMerge')");
    buttonAvailable($("#DeleteSelectionMode"), "updateSelectionPanel('ConfirmDeletion')");
    $("#MergeOptionsBlock").hide();
    $("#ConfirmGroupAction").hide();
      break;
      case "OptionMerge":
    $("#DeleteSelectionMode").hide();
    $("#MergeSelectionMode").hide();
    $("#MergeOptionsBlock").show();
    $("#ConfirmGroupAction").hide();
      break;
  }
  if (newState == "NoSelection") {
    $("#DeleteSelectionMode").show();
    $("#MergeSelectionMode").show();
    buttonUnavailable($("#MergeSelectionMode"));
    buttonUnavailable($("#DeleteSelectionMode"));
    $(".SelectionModeGroup").hide();
    $("#nothing-selected").show();
    $("#MergeOptionsBlock").hide();
    $("#ConfirmGroupAction").hide();
  } else {
    $(".SelectionModeGroup").show();
    $("#nothing-selected").hide();
  }
};

var buttonAvailable = function(button, onClick) {
  button.removeClass("unavailable");
  button.attr("OnClick", onClick);
}

var buttonUnavailable = function(button) {
  button.addClass("unavailable");
  button.removeAttr("OnClick");
}

/*-------
 Delete function on button's pannel
 -------*/

$('.ConfirmDeleteButton').on("click", function() {
  $('.DeleteGroupList div').each(function () {
    deleteGroup($(this).data('id'));
    $(this).remove();
    updateSelectionPanel("NoSelection");
  })
})

/*-------
 Manage User Part
 -------*/

// Initialize the research user bar
var selectize;

// List of users
var usersSearch = [];

// Setup the user research bar
$(function() {

  // initialize the Selectize control
  $select = $('.AddUserBlock input').selectize({
    delimiter: ',',
    persist: false,
    plugins: ['remove_button']
  });

  // fetch the instance
  selectize = $select[0].selectize;

  var idSearch = "";
  $('.UserSearch input').on("focus", function() {
    // Import users if it is not already done
    if (usersSearch.length == 0) {
      jQuery.ajax({
        url: "ws.php?format=json&method=pwg.users.getList",
        type: "POST",
        data: "",
        success: function (raw_data) {
          data = jQuery.parseJSON(raw_data);
          if (data.stat === "ok") {
            usersSearch = data.result.users;
            updateUserSearch();
            selectize.refreshOptions();
          }
        }
      });
    } else if (idSearch != $("#UserList").attr("data-group_id")) {
      updateUserSearch();
    }
  });

  // Update User search bar (remove group users in selection)
  updateUserSearch = function () {
    idSearch = $("#UserList").attr("data-group_id");
    selectize.clearOptions();
    usersSearch.forEach(function(u){
      isInGroup = false;
      $('.UsernameBlock').each(function(){
        if ($(this).data("id")==u.id)
          isInGroup = true;
      })
      if (!isInGroup) {
        selectize.addOption({value:u.id, text:u.username})
      }
    })
  }
});

// Display the user manager for a specific group
var openUserManager = function(grp_id) {
  let loadState = new TemporaryState();
  loadState.removeClass($("#group-" + grp_id + " #UserListTrigger"),'icon-user-1')
  loadState.changeAttribute($("#group-" + grp_id + " #UserListTrigger"), "style", "pointer-events: none");
  loadState.changeHTML($("#group-" + grp_id + " #UserListTrigger"), "<i class='icon-spin6 animate-spin'> </i>");
  jQuery.ajax({
    url: "ws.php?format=json&method=pwg.users.getList",
    type: "POST",
    data: "group_id=" + grp_id,
    success: function (raw_data) {
      loadState.reverse();
      data = jQuery.parseJSON(raw_data);
      if (data.stat === "ok") {
        //Fill with user blocks
        let users = data.result.users;
        $(".UsersInGroupList").html('');
        $(".UserNumberBadge").html(''+users.length);
        users.forEach(u => {
          addUserDisplay(u.username, u.id, grp_id);
        });
        $('#UserList').fadeIn();
        //Attribute the group id to the div
        $("#UserList").attr("data-group_id", grp_id);
        //Clear the selection
        selectize.clear();
      }
    },
    error: function (err) {
      console.log(err);
    },
  });
}

//Add a user block
var addUserDisplay = function(username, user_id, grp_id) {
  let userBlock = $('<div class="UsernameBlock" data-id='+user_id+'>'+
      '<span class="icon-user-1"></span>'+
      '<p>'+username+'</p>'+
      '<div class="Tooltip">'+
        '<span class="icon-cancel"></span>'+
        '<p class="TooltipText">Dissociate user from this group</p>'+
      '</div>'+
      '<div class="UserInfo"><p>User Dissociated</p></div>'+
    '</div>');
    userBlock.appendTo(".UsersInGroupList");

    //Setup the delete action
    userBlock.find(".icon-cancel").on("click", function () {
    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.groups.deleteUser",
      type: "POST",
      data: "group_id=" + grp_id + "&user_id=" + user_id + "&pwg_token=" + pwg_token,
      success: function (raw_data) {
        data = jQuery.parseJSON(raw_data);
        if (data.stat === "ok") {
          //Setup User Info
          userBlock.find(".UserInfo")
            .css("display", "flex")
            .addClass("UserInfo-dissociated").removeClass("UserInfo-associated")
            .hide()
            .find("p").html(str_user_dissociated);
          userBlock.find(".UserInfo").fadeIn();
          userBlock.delay(1000).fadeOut(function(){
            userBlock.remove();
            updateUserSearch();
            //Update member number
            $(".GroupContainer[data-id="+grp_id+"] .group_number_users")
            .html(($(".UsernameBlock").length) + " " + str_member_default);
            $(".UserNumberBadge").html(''+$(".UsernameBlock").length);
          })
        }
      }
    });
  })
  return userBlock;
} 

// Close pop-up on cross click
$(".CloseUserList").on("click", function() {$('#UserList').fadeOut();})

// Adding Group Action
$(".AddUserBlock button").on("click", function () {
  let grp_id = $("#UserList").attr("data-group_id")
  let usersString = ""
  // Get selected ids
  let ids = selectize.getValue();
  ids.split(',').forEach(function(id){
    usersString += "&user_id[]="+id
  });
  jQuery.ajax({
    url: "ws.php?format=json&method=pwg.groups.addUser",
    type: "POST",
    data: "group_id=" + grp_id+ usersString + "&pwg_token=" + pwg_token,
    success: function (raw_data) {
      data = jQuery.parseJSON(raw_data);
      ids.split(',').forEach(function(id){
        // Get the username
        let username = "undefined";
        usersSearch.forEach(function(u) {
          if (u.id == id) {
            username = u.username;
          }
        })
        let userBlock = addUserDisplay(username, id, grp_id)

        //Setup User Info
        userBlock.find(".UserInfo")
          .css("display", "flex")
          .addClass("UserInfo-associated").removeClass("UserInfo-dissociated")
          .show()
          .find("p").html(str_user_associated);
        userBlock.delay(1000).fadeIn();
        userBlock.find(".UserInfo").delay(1000).fadeOut();

        updateUserSearch();
        //Update member number
        $(".GroupContainer[data-id="+grp_id+"] .group_number_users")
        .html(($(".UsernameBlock").length) + " " + str_member_default);
        $(".UserNumberBadge").html(''+$(".UsernameBlock").length);
      })
    }
  }); 
});

// Class to implement a temporary state and reverse it
class TemporaryState {
  //Arrays to reverse changes
  attrChanges = []; //Attribute changes : {object(s), attribute, (old) value}
  classChanges = []; //Class changes : {object(s), state(add:true/remove:false), class}
  htmlChanges = []; //Html changes : {object(s), (old) html}

  /**
   * Change an attribute of an object
   * @param {HTML Node} obj HTML Object(s)
   * @param {String} attr Attribute
   * @param {String} tempVal Temporary value of the attribute 
   */
  changeAttribute(obj, attr, tempVal) {
    for (let i = 0; i < obj.length; i++) {
      this.attrChanges.push({
        object: $(obj[i]),
        attribute: attr,
        value: $(obj[i]).attr(attr)
      })
    }
    obj.attr(attr, tempVal)
  }

  /**
   * Add/remove a class temporarily
   * @param {HTML Node} obj HTML Object
   * @param {Boolean} st Add (true) or Remove (false) the class
   * @param {String} loadclass Class Name
   */
  changeClass(obj, st, tempclass) {
    for (let i = 0; i < obj.length; i++) {
      if (!($(obj[i]).hasClass(tempclass) && st)) {
        this.classChanges.push({
          object: $(obj[i]),
          state: !st,
          class: tempclass
        })
        if (st) 
          $(obj[i]).addClass(tempclass)
        else
          $(obj[i]).removeClass(tempclass)
      }
    }
  }

  addClass(obj, tempclass) {
    this.changeClass(obj, true, tempclass);
  }

  removeClass(obj, tempclass) {
    this.changeClass(obj, false, tempclass);
  }

  changeHTML(obj, temphtml) {
    for (let i = 0; i < obj.length; i++) {
      this.htmlChanges.push({
        object:$(obj[i]),
        html:$(obj[i]).html()
      })
    }
    obj.html(temphtml);
  }

  /**
   * Reverse all the changes and clear the history
   */
  reverse() {
    this.attrChanges.forEach(function(change) {
      if (change.value == undefined) {
        change.object.removeAttr(change.attribute);
      } else {
        change.object.attr(change.attribute, change.value)
      }
    })
    this.classChanges.forEach(function(change) {
      if (change.state)
        change.object.addClass(change.class)
      else
        change.object.removeClass(change.class)
    })
    this.htmlChanges.forEach(function(change) {
      change.object.html(change.html);
    })
    this.attrChanges = [];
    this.classChanges = [];
    this.htmlChanges = [];
  }
}