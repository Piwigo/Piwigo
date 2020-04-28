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

$("#showAddGroup").click(function () {
  $("#addGroupForm").css("display", "inline-block");
  $("#showAddGroup").css("display", "none");
});

var closeAddGroup = function () {
  $("#addGroupForm").css("display", "none");
  $("#showAddGroup").css("display", "inline-block");
};

$("#addGroupClose").click(closeAddGroup);

$(".actionButtons input").click(closeAddGroup);

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
          $("#showAddGroup .groupError").delay(10).html(contents);
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
  let contents = groupBox.find("#group_name").html();
  groupBox.find("#group_name").blur(function() {
    if (contents!=groupBox.find("#group_name").html()){
      jQuery.ajax({
        url: "ws.php?format=json&method=pwg.groups.setInfo",
        type: "POST",
        data: "group_id=" + id + "&pwg_token=" + pwg_token + "&name="+groupBox.find("#group_name").html(),
        success: function (raw_data) {
          data = jQuery.parseJSON(raw_data);
          if (data.stat === "ok") {
            groupBox.find(".groupMessage").html(renaming_done);
            groupBox.find(".groupMessage").fadeIn();
            groupBox.find(".groupMessage").delay(3000).fadeOut();
            contents = groupBox.find("#group_name").html();
          } else {
            groupBox.find(".groupError").html(name_taken);
            groupBox.find(".groupError").fadeIn();
            groupBox.find(".groupError").delay(3000).fadeOut();
            groupBox.find("#group_name").delay(10).html(contents);
          }
        },
        error: function (err) {
          console.log(err);
        },
      });
    }
  });
};

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
      $("#group_name").attr("contenteditable", false);
      $(".GroupManagerButtons").removeClass("visible");
    } else {
      $(".in-selection-mode").fadeOut();
      $(".not-in-selection-mode").fadeIn();
      $(".GroupManagerButtons").addClass("visible");
      $(".Group-checkbox input").attr("checked", false);
      $(".Group-checkbox input[type='checkbox']").trigger("change");
      $("#group_name").attr("contenteditable", true);
    }
  });
});

/*-------
 Hide group options on click on the screen
 -------*/

$(document).mouseup(function (e) {
  if ($(e.target).closest("#GroupOptions").length === 0) {
    $(".group-dropdown-options #GroupOptions").hide();
  }
});

/*-------
 Update Selection Panel
 -------*/
var state = "NoSelection";

var updateSelectionPanel = function (changedState = "") {
  let numSelect = $(".DeleteGroupList div").length;
  
  if (numSelect == 0) {
    updateStatePanel("NoSelection")
  } else if (numSelect == 1 && (state == "OptionMerge" || state == "NoSelection" || state == "Selection")) {
    updateStatePanel("OneSelected")
  } else if (changedState != "") {
    updateStatePanel(changedState)
  } else if (numSelect > 1 && state == "OneSelected") {
    updateStatePanel("Selection");
  }

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
    $("#MergeSelectionMode").hide();
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
    $(".SelectionModeGroup").hide();
    $("#nothing-selected").show();
    $("#MergeOptionsBlock").hide();
    $("#ConfirmGroupAction").hide();
  } else {
    $(".SelectionModeGroup").show();
    $("#nothing-selected").hide();
  }
};

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