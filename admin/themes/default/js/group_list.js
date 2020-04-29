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
  $(".input-user-name").show();
  $("#UserSubmit").show();
  $("#form-btn").hide();
  $(".groups .list_user").css("max-height", "100px");
});

$("#cancel").click(function () {
  $("#cancel").hide();
  $("#addUserLabel").hide();
  $(".input-user-name").hide();
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
    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.groups.add",
      type: "POST",
      data: "name=" + name + "&pwg_token=" + pwg_token,
      success: function (raw_data) {
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
          newgroup.find(".group_number_users").html("0 " + member_default);
          hideAddGroupForm();
          //Setup the icon color
          var colors = [["#ffa744", "#ffe9cf"],["#896af3", "#e0daf4"], ["#6ece5e","#d6ffcf"],["#2883c3","#cfebff"]];
          var colorId = Number(id)%4;
          newgroup.find(".icon-users-1").attr("style", "color:"+colors[colorId][0]+"; background-color:"+colors[colorId][1]);  
          
          setupGroupBox(newgroup);
          
          //Place group in first Place 
          newgroup.appendTo(".groups");
          newgroup.find(".groupMessage").html(group_created);
          newgroup.find(".groupMessage").fadeIn();
          newgroup.find(".groupMessage").delay(4000).fadeOut();
        } else {
          $("#showAddGroup .groupError").html(name_taken);
          $("#showAddGroup .groupError").fadeIn();
          $("#showAddGroup .groupError").delay(3000).fadeOut();
        }
      },
      error: function (err) {
        console.log(err);
      },
    });
  });
});

/*-------
 User List group pop-in
 -------*/

var modalUL = document.getElementById("UserList");

var btn = document.getElementById("UserListTrigger");

var close = document.getElementsByClassName("CloseUserList")[0];

btn.onclick = function () {
  modalUL.style.visibility = "visible";
};

close.onclick = function () {
  modalUL.style.visibility = "hidden";
};

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
    }
  });

  /* Display the option on the click on "..." */
  groupBox.find(".group-dropdown-options").click(function GroupOptions() {
    $(this).find("#GroupOptions").toggle();
  });

  /* Set the delete action */
  groupBox.find("#GroupDelete").on("click", function () {
    deleteGroup(id);
  });

  /* Set the rename action */
  groupBox.find(".Group-name .icon-pencil").on("click", function () {
    groupBox.find(".Group-name-container form").show();
    groupBox.find(".Group-name-container .icon-pencil").hide();
    groupBox.find(".Group-name-container .icon-ok").show();
    groupBox.find(".Group-name-container p").css("opacity", 0)
  });

  groupBox.find(".Group-name-container .icon-ok").on("click", function () {
    renameGroup(id, groupBox.find(".group_name-editable").val())
  });

  groupBox.find(".Group-name-container form").on("submit", function (e) {
    e.preventDefault();
    renameGroup(id, groupBox.find(".group_name-editable").val())
  });

  /* Hide group options and rename field on click on the screen */

  $(document).mouseup(function (e) {
    if ($(e.target).closest("#group-"+id+" #GroupOptions").length === 0) {
      groupBox.find(".group-dropdown-options #GroupOptions").hide();
    }
    if ($(e.target).closest("#group-"+id+" .Group-name").length === 0) {
      groupBox.find(".Group-name-container form").hide();
      groupBox.find(".Group-name-container .icon-pencil").removeAttr("style");
      groupBox.find(".Group-name-container .icon-ok").hide();
      groupBox.find(".Group-name-container p").css("opacity", 1);
      groupBox.find(".group_name-editable").val(groupBox.find(".Group-name-container p").html());
    }
  });
};

/* Group Ajax Functions */
var deleteGroup = function (id) {
  jQuery.ajax({
    url: "ws.php?format=json&method=pwg.groups.delete",
    type: "POST",
    data: "group_id=" + id + "&pwg_token=" + pwg_token,
    success: function (raw_data) {
      data = jQuery.parseJSON(raw_data);
      if (data.stat === "ok") {
        $("#group-" + id).remove();
      }
    },
    error: function (err) {
      console.log(err);
    },
  });
};

var renameGroup = function(id, newName) {
  jQuery.ajax({
    url: "ws.php?format=json&method=pwg.groups.setInfo",
    type: "POST",
    data: "group_id=" + id + "&pwg_token=" + pwg_token + "&name="+newName,
    success: function (raw_data) {
      data = jQuery.parseJSON(raw_data);
      if (data.stat === "ok") {
        newName = data.result.groups[0].name;
        //Display message
        $("#group-" + id).find(".groupMessage").html(renaming_done);
        $("#group-" + id).find(".groupMessage").fadeIn();
        $("#group-" + id).find(".groupMessage").delay(3000).fadeOut();
        $("#group-" + id).find("#group_name").html(newName);

        //Hide editable field
        $("#group-" + id).find(".Group-name-container form").hide();
        $("#group-" + id).find(".Group-name-container span").show();
        $("#group-" + id).find(".Group-name-container .icon-ok").hide();
        $("#group-" + id).find(".Group-name-container p").css("opacity", 1)
      } else {
        //Display error message
        $("#group-" + id).find(".groupError").html(name_taken);
        $("#group-" + id).find(".groupError").fadeIn();
        $("#group-" + id).find(".groupError").delay(3000).fadeOut();
      }
    },
    error: function (err) {
      console.log(err);
    },
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
      $(".in-selection-mode").show();
      $(".not-in-selection-mode").hide();
      $("#group_name").attr("contenteditable", false);
      $(".GroupManagerButtons").removeClass("visible");
    } else {
      $(".in-selection-mode").fadeOut();
      $(".not-in-selection-mode").removeAttr("style");
      $(".Group-checkbox input").attr("checked", false);
      $(".Group-checkbox input[type='checkbox']").trigger("change");
      $("#group_name").attr("contenteditable", true);
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