const DELAY_FEEDBACK = 3000;
/*-------
Group Popin
-------*/

$(".group_details_popup_trigger").click(function () {
  $(".Group_details-popup-container").show();
});

$(".CloseGroupPopup").click(function () {
  $(".Group_details-popup-container").hide();
});

//Number On Badge
function updateBadge() {
  $('.badge-number').html($('.GroupContainer').length - 2) //Less the add group div and the template
}

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
  if (isToggle) {
    deployAddGroupForm();
  } 
  else {
    hideAddGroupForm();
  } 
})

var deployAddGroupForm = function () {
  $(".addGroupBlock").animate({
    top: "20%",
    padding: "0px"
  }, 400, complete=function(){
    $("#addGroupForm form").fadeIn();
    $("#addGroupNameInput").focus();  
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
    loadState.changeHTML($(".actionButtons button"), "<i class='icon-spin6 animate-spin'> </i>");
    loadState.changeAttribute($(".actionButtons button"), "style", "pointer-events: none");
    loadState.changeAttribute($(".actionButtons a"), "style", "pointer-events: none");

    if (name.replace(/\s/g, '').length != 0) {
      jQuery.ajax({
        url: "ws.php?format=json&method=pwg.groups.add",
        type: "POST",
        data: {
          'name': name,
          'pwg_token': pwg_token,
        },
        success: function (raw_data) {
          loadState.reverse();
          data = jQuery.parseJSON(raw_data);
          if (data.stat === "ok") {
            $(".addGroupFormLabelAndInput input").val('');
            group = data.result.groups[0];
            groupBox = createGroup(group);
            $("#addGroupForm").after(groupBox);
            setupGroupBox(groupBox);
            updateBadge();
          } else {
            $("#addGroupForm .groupError").html(str_name_not_empty);
            $("#addGroupForm .groupError").fadeIn();
            $("#addGroupForm .groupError").delay(DELAY_FEEDBACK).fadeOut();
          }
        },
        error: function (err) {
          console.log(err);
        },
      });     
    } else {
      loadState.reverse();
      $("#addGroupForm .groupError").html(str_name_not_empty);
      $("#addGroupForm .groupError").fadeIn();
      $("#addGroupForm .groupError").delay(DELAY_FEEDBACK).fadeOut();
    }
  });
});

var createGroup = function(group) {
  //Setup the group
  newgroup = $("#group-template").clone().attr("id", "group-" + group.id);
  newgroup.attr("data-id", group.id);
  newgroup.find("#group_name").html(group.name);
  newgroup.find(".group_name-editable").val(group.name);
  newgroup.find(".Group-checkbox label").attr("for", "Group-Checkbox-selection-" + group.id);
  newgroup.find(".Group-checkbox input").attr("id", "Group-Checkbox-selection-" + group.id);
  newgroup.find(".input-edit-group-name").attr("placeholder", group.name);
  newgroup.find(".group_number_users").html(group.nb_users+" " + ((group.nb_users > 1)? str_members_default:str_member_default));
  newgroup.find(".group_name-editable").html(group.name);
  newgroup.find(".manage-permissions").attr("href", "admin.php?page=group_perm&group_id="+group.id)
  hideAddGroupForm();

  //Setup the icon color
  var colors = ["icon-red", "icon-blue", "icon-yellow", "icon-purple", "icon-green"];
  var colorId = Number(group.id)%5;
  newgroup.find(".icon-users-1").addClass(colors[colorId]);  
  
  //Place group in first Place 
  newgroup.find(".groupMessage").html(str_group_created);
  newgroup.find(".groupMessage").fadeIn();
  newgroup.find(".groupMessage").delay(DELAY_FEEDBACK).fadeOut();
  return newgroup;
}

/*-------
 SETUP JS ON GROUP BOX
 -------*/
jQuery(document).ready(function () {
  $(".GroupContainer").each(function () {
    if ($(this).attr("id") != "group-template")
      setupGroupBox($(this));
  });
});
var setupGroupBox = function (groupBox) {
  var id = groupBox.data("id");

  /* Change background color of group block if checked in selection mode */
  groupBox.find(".Group-checkbox input[type='checkbox']").change(function () {
    toogleSelection(id, groupBox.find(".Group-checkbox input[type='checkbox']").is(":checked"));
  });
  groupBox.find(".Group-checkbox input[type='checkbox']").attr("checked", false)

  /* Display the option on the click on "..." */
  groupBox.find(".group-dropdown-options").click(function GroupOptions() {
    $(this).find("#GroupOptions").toggle();
  });

  /* Set the delete action */
  groupBox.find("#GroupDelete").on("click", function () {
    deleteGroup(id);
  });

  /* Set the rename action */
  groupBox.find(".Group-name .icon-pencil, #GroupEdit").on("click", function () {
    displayRenameForm(true, id);
    setTimeout(() => {groupBox.find("#GroupOptions").hide()}, 10)
  });

  groupBox.find(".group-rename .validate").on("click", function () {
      groupBox.find(".group-rename form").trigger("submit");
  });

  groupBox.find(".group-rename form").on("submit", function (e) {
    e.preventDefault();
    renameGroup(id, groupBox.find(".group_name-editable").val())
  });

  groupBox.find(".group-rename .icon-cancel").on('click', function() {
    displayRenameForm(false, id)
    groupBox.find(".group_name-editable").val(groupBox.find(".Group-name-container p").html());
  })

  /* Hide group options and rename field on click on the screen */

  $(document).mouseup(function (e) {
    e.stopPropagation();
    let option_is_clicked = false
    $("#GroupOptions div").each(function () {
      if (!($(this).has(e.target).length === 0)) {
        option_is_clicked = true;
      }
    })
    if (!option_is_clicked) {
      groupBox.find("#GroupOptions").hide();
    }
  });

  /* Setup the default action */
  if (groupBox.data("default") == 1) {
    setupDefaultActions(id, true);
  } else if (groupBox.data("default") == 0) {
    setupDefaultActions(id, false);   
  }

  groupBox.find(".manage-users").on("click", function(){openUserManager(id)});
  
  groupBox.find("#GroupDuplicate").on("click", function(){duplicateAction(id)})

};

var toogleSelection = function(group_id, toggle) {
  groupBox = $("#group-"+group_id);
  if (toggle) {
    groupBox.find(".Group-checkbox input").attr("checked", true);
    groupBox.addClass("GroupBackgroudSelected");
    groupBox.find(".icon-users-1").addClass("OrangeIcon");
    groupBox.find(".group_number_users").addClass("OrangeFont");

    //Display item selection on selection panel
    item = $(
      "<div data-id=" +
        group_id +
        ">" +
        "<a class='icon-cancel'></a>" +
        "<p>" +
        groupBox.find("#group_name").html() +
        "</p> </div>"
    );
    item.appendTo(".DeleteGroupList");
    item.find("a").on("click", function () {
      groupBox.find(".Group-checkbox input").attr("checked", false);
      toogleSelection(group_id);
    });
    updateSelectionPanel();
    option = $('<option value="'+group_id+'">'+groupBox.find("#group_name").html()+'</option>')
    option.appendTo("#MergeOptionsChoices");
  } else {
    groupBox.find(".Group-checkbox input").attr("checked", false);
    groupBox.removeClass("GroupBackgroudSelected");
    groupBox.find(".icon-users-1").removeClass("OrangeIcon");
    groupBox.find(".group_number_users").removeClass("OrangeFont");
    $(".DeleteGroupList div").each(function () {
      if ($(this).data("id") == group_id) {
        $(this).remove();
      }
    });
    updateSelectionPanel();
    $("#MergeOptionsChoices option").each(function () {
      if ($(this).attr("value") == group_id) {
        $(this).remove();
      }
    });
  }
}

/* Group Ajax and Display Functions */
var deleteGroup = function (id) {
  $.confirm({
    title: str_delete.replace("%s",$("#group-"+id+" #group_name").html()),
    draggable: false,
    titleClass: "jconfirmDeleteConfirm",
    theme: "modern",
    content: "",
    animation: "zoom",
    boxWidth: '30%',
    useBootstrap: false,
    type: 'red',
    animateFromElement: false,
    backgroundDismiss: true,
    typeAnimated: false,
    buttons: {
        confirm: {
          text: str_yes_delete_confirmation,
          btnClass: 'btn-red',
          action: function () {
            let groupName = $("#group-"+id+" .Group-name-container p").html()
            $.alert({
              ...{title : str_group_deleted.replace("%s",groupName),
                content: function() {
                return jQuery.ajax({
                  url: "ws.php?format=json&method=pwg.groups.delete",
                  type: "POST",
                  data: "group_id=" + id + "&pwg_token=" + pwg_token,
                  success: function (raw_data) {
                    data = jQuery.parseJSON(raw_data);
                    if (data.stat === "ok") {
                      $("#group-" + id).remove();
                      $(".DeleteGroupList div[data-id="+id+"]").remove()
                      $("#MergeOptionsChoices option[value="+ id +"]").remove()
                    }
                    updateBadge();
                  },
                  error: function (err) {
                    console.log(err);
                  },
                })
              }},
              ...jConfirm_alert_options
            });
          }
        },
        cancel: {
          text: str_no_delete_confirmation
        }
    }
  });
};

var renameGroup = function(id, newName) {
  let loadState = new TemporaryState();
  loadState.changeHTML($("#group-" + id + " .group-rename .validate"), "<i class='animate-spin icon-spin6'></i>")
  loadState.removeClass($("#group-" + id + " .group-rename .validate"), "icon-ok")
  loadState.changeAttribute($("#group-" + id + " .group-rename span"), "style", "pointer-events: none");

  if (newName.replace(/\s/g, '').length != 0) {
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
  } else {
    loadState.reverse();
    $("#group-" + id).find(".groupError").html(str_name_not_empty);
    $("#group-" + id).find(".groupError").fadeIn();
    $("#group-" + id).find(".groupError").delay(DELAY_FEEDBACK).fadeOut();
  }
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
  $("#group-" + id + " #GroupDefault").css("width", $("#group-" + id + " #GroupDefault").width())
  $("#group-" + id + " #GroupDefault").html("<i class='icon-spin6 animate-spin'> </i>")
  $("#group-" + id + " #GroupDefault").removeClass("icon-star");
  $("#group-" + id + " #GroupDefault").attr("style", "pointer-events: none; text-align: center;")
  $("#group-" + id).find(".is-default-token").addClass("icon-spin6").addClass("animate-spin").removeClass("icon-star")
  jQuery.ajax({
    url: "ws.php?format=json&method=pwg.groups.setInfo",
    type: "POST",
    data: "group_id=" + id + "&pwg_token=" + pwg_token + "&is_default="+is_default,
    success: function (raw_data) {
      data = jQuery.parseJSON(raw_data);
      $("#group-"+id+" #GroupOptions").hide();
      if (data.stat === "ok") {
        if (is_default) {
          setupDefaultActions(id,true)
        } else {
          setupDefaultActions(id,false)
        }
      }
    },
    error: function (err) {
      console.log(err);
    },
  });
};

var setupDefaultActions = function(id, is_default) {
  $("#group-" + id + " #GroupDefault").attr("style", "");
  $("#group-" + id + " #GroupDefault").addClass("icon-star")
  $("#group-" + id).find(".is-default-token").removeClass("icon-spin6").removeClass("animate-spin").addClass("icon-star")
  if (is_default) {
    $("#group-" + id).find("#GroupDefault").html(str_unset_default);
    $("#group-" + id).find(".is-default-token").attr("title",str_unset_default)
    $("#group-" + id).find("#GroupDefault").unbind("click");
    $("#group-" + id).find(".is-default-token").removeClass("deactivate");
    $("#group-" + id).find("#GroupDefault").on("click", function(){setDefaultGroup(id, false)})
    $("#group-" + id).find(".is-default-token").on("click", function(){setDefaultGroup(id, false)})
  } else {
    $("#group-" + id).find("#GroupDefault").html(str_set_default);
    $("#group-" + id).find(".is-default-token").attr("title",str_set_default)
    $("#group-" + id).find(".is-default-token").addClass("deactivate");
    $("#group-" + id).find("#GroupDefault").on("click", function(){setDefaultGroup(id, true)})
    $("#group-" + id).find(".is-default-token").unbind("click");
  }
}

var duplicateAction = function(id) {
  let loadState = new TemporaryState();
  loadState.changeHTML($("#group-" + id + " #GroupDuplicate"), "<i class='icon-spin6 animate-spin'> </i>")
  loadState.removeClass($("#group-" + id + " #GroupDuplicate"), "icon-docs");
  loadState.changeAttribute($("#group-" + id + " #GroupDuplicate"), "style", "pointer-events: none; text-align: center;")
  copy_name = $("#group-" + id + " #group_name").html() + str_copy;

  let name_exist = function(name) {
    exist = false;
    $(".Group-name-container p").each(function () {
      if ($(this).html() === name)
        exist = true
    })
    return exist;
  }

  let i = 1;
  while (name_exist(copy_name)) 
  {
    copy_name = $("#group-" + id + " #group_name").html() + str_other_copy.replace("%s", i++)
  }

  jQuery.ajax({
    url: "ws.php?format=json&method=pwg.groups.duplicate",
    type: "POST",
    data: "group_id=" + id + "&pwg_token=" + pwg_token + "&copy_name=" + copy_name,
    success: function (raw_data) {
      data = jQuery.parseJSON(raw_data);
      loadState.reverse();
      if (data.stat === "ok") {
        $("#group-"+id+" #GroupOptions").hide();
        group = data.result.groups[0];
        let groupbox = createGroup(group)
        groupbox.insertAfter($("#group-"+id));
        setupGroupBox(groupbox);
        updateBadge();

        /* data.result.groups[0].is_default is a string */
        if(data.result.groups[0].is_default == "true") {
          setupDefaultActions(data.result.groups[0].id, true);
        }
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
  $("#toggleSelectionMode").attr("checked", false)
  $("#toggleSelectionMode").click(function () {
    if ($(this).is(":checked")) {
      $(".in-selection-mode").show();
      $(".not-in-selection-mode").hide();
      $(".GroupManagerButtons").removeClass("visible");
    } else {
      $(".in-selection-mode").hide();
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
 Merge function on button's pannel
 -------*/

$('.ConfirmMergeButton').on("click", function() {
  let loadState = new TemporaryState();
  loadState.changeAttribute($('.ConfirmMergeButton'), "style", "pointer-events: none");
  loadState.changeHTML($('.ConfirmMergeButton'), "<i class='icon-spin6 animate-spin'> </i>");
  loadState.removeClass($('.ConfirmMergeButton'), "icon-ok");
  merge_group = [];
  str_merge_group = "";
  name_merge = [];
  name_dest = [];
  dest_grp = $("#MergeOptionsChoices").val();

  $(".DeleteGroupList div").each(function () {
    if (dest_grp != $(this).attr("data-id")) 
    {
      str_merge_group += "&merge_group_id[]="+$(this).attr("data-id");
      merge_group.push($(this).attr("data-id"));
      name_merge.push($(this).find("p").html())
    } else {
      name_dest = $(this).find("p").html();
    }
  })

  jQuery.ajax({
    url: "ws.php?format=json&method=pwg.groups.merge",
    type: "POST",
    data: "destination_group_id=" + dest_grp + str_merge_group + "&pwg_token=" + pwg_token,
    success: function (raw_data) {
      loadState.reverse();
      data = jQuery.parseJSON(raw_data);
      if (data.stat === "ok") {
        updateSelectionPanel('Selection');
        merge_group.forEach(function(id) {
          ($("#group-"+id).fadeOut(complete=function(){
            $(this).remove();
          }))
        })
        toogleSelection(dest_grp, false)
        $(".DeleteGroupList").html("");
        $("#MergeOptionsChoices").html("");

        $.alert({
          ...{title: str_merged_into
            .replace("%s1",name_merge.toString())
            .replace("%s2",name_dest),
            content: "",},
          ...jConfirm_alert_options
        });

        $("#group-"+dest_grp + " .group_number_users").html("<i class='icon-spin6 animate-spin'> </i>");
        jQuery.ajax({
          url: "ws.php?format=json&method=pwg.users.getList",
          type: "POST",
          data: "group_id=" + dest_grp,
          success: function (raw_data) {
            data = jQuery.parseJSON(raw_data);
            let number = data.result.users.length;
            $("#group-"+dest_grp + " .group_number_users").html(
              number + " " + ((number > 1)? str_members_default:str_member_default)
            );
            updateBadge();
          }
        })
      };
    }
  })
})

/*-------
 Delete function on button's pannel
 -------*/

$('.ConfirmDeleteButton').on("click", function() {
  let names = [];
  let ids = [];
  $('.DeleteGroupList div').each(function () {
    let id = $(this).data('id');
    names.push($("#group-"+id+" #group_name").html());
    ids.push(id);
  });

  let loadState = new TemporaryState;
  loadState.changeAttribute($('.ConfirmDeleteButton'), "style", "pointer-events: none");
  loadState.changeHTML($('.ConfirmDeleteButton'), "<i class='icon-spin6 animate-spin'> </i>");
  loadState.removeClass($('.ConfirmDeleteButton'),"icon-ok");

  str_id = ""
  ids.forEach(function(id) {
    str_id += "group_id[]=" + id + "&"
  })

  jQuery.ajax({
    url: "ws.php?format=json&method=pwg.groups.delete",
    type: "POST",
    data: str_id + "pwg_token=" + pwg_token,
    success: function (raw_data) {
      data = jQuery.parseJSON(raw_data);
      if (data.stat === "ok") {
        $(".DeleteGroupList div").each(function() {
            $(this).remove();
            $("#group-" + $(this).attr("data-id")).remove();
            $("#MergeOptionsChoices option[value="+ $(this).attr("data-id") +"]").remove()
        })
            
        loadState.reverse();
        updateSelectionPanel("NoSelection");
        $.alert({
          ...{title: str_groups_deleted.replace("%s",names.toString()),
            content: "",
          },
          ...jConfirm_alert_options
        });
        updateBadge();
      }
    },
    error: function (err) {
      console.log(err);
    },
  });
});

/*-------
 Manage User Part
 -------*/

// Initialize the research user bar
var selectize;

// Initialize the cache
var usersCache = {};

var usersInGroup = [];

// Max offset of the user container (322 = 6 lines)
var maxOffsetUserCont = 322;

var dissociateUserInfo = $("<div class='ValidationUserDissociated'>"
  + "<p class='icon-ok'></p>"
  + "</div>").appendTo(".group-name-block").hide();

var associateUserInfo = $("<div class='ValidationUserAssociated'>"
+ "<p class='icon-ok'></p>"
+ "</div>");

// Setup the user research bar
$(function() {

  // initialize the Selectize control
  $select = $('.AddUserBlock select').selectize({});

  // fetch the instance
  selectize = $select[0].selectize;


  var idSearch = "";
  $('.UserSearch input').on("focus", function() {
    if (idSearch != $("#UserList").attr("data-group_id")) {
      updateUserSearch();
    }
  });

  // Update User search bar (remove group users in selection)
  updateUserSearch = function () {
    selectize.clear();
    if (usersCache = {}) {
      usersCache = new UsersCache({
        serverKey: serverKey,
        serverId: serverId,
        rootUrl: rootUrl
      });
    }
    JSON.parse(usersCache.storage[usersCache.key]).data.forEach(function(u){
      selectize.addOption({value:u.id, text:u.username})
    })
    idSearch = $("#UserList").attr("data-group_id");
    for (const [key, value] of Object.entries(selectize.options)) {
      if (value.username === "guest") {
        selectize.removeOption(value.id);
      }
    }
    $('.UsernameBlock').each(function(){
      selectize.removeOption($(this).data("id"));
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
        //Set the popin name 
        $(".group-name-block p").html(
          $("#group-" + grp_id + " #group_name").html() + " / " + str_user_list
        )
        $(".UsersInGroupList").html("");

        //Display the popin
        $('#UserList').fadeIn();

        //Fill with user blocks
        usersInGroup = data.result.users;
        // Sort in alphabetic order
        usersInGroup.sort(function( a, b ) {
          if ( a.username.toLowerCase() < b.username.toLowerCase() ){
            return -1;
          } else return 1
        });
        let i = 0;
        while ($(".UsersInGroupList").outerHeight() <= maxOffsetUserCont && usersInGroup[i] != undefined){
          getUserDisplay(usersInGroup[i].username, usersInGroup[i].id, grp_id).appendTo(".UsersInGroupList");
          i++;
        };
        while ($(".UsersInGroupList").height() > maxOffsetUserCont) {
          $(".UsernameBlock").last().remove();
        }
        updateMembernumber(usersInGroup.length, grp_id);
        //Attribute the group id to the div
        $("#UserList").attr("data-group_id", grp_id);

        $(".LinkUserManager a").attr("href","admin.php?page=user_list&group="+grp_id)
      }
    },
    error: function (err) {
      console.log(err);
    },
  });
}

//Add a user block
var getUserDisplay = function(username, user_id, grp_id) {
  let userBlock = $('<div class="UsernameBlock" data-id='+user_id+'>'+
      '<span class="icon-user-1"></span>'+
      '<p>'+username+'</p>'+
      '<div class="Tooltip">'+
        '<span class="icon-cancel"></span>'+
        '<p class="TooltipText">'+str_user_dissociate+'</p>'+
      '</div>'+
    '</div>');

  while ($(".UsersInGroupList")[0].offsetHeight > maxOffsetUserCont) {
    $(".UsernameBlock").last().remove();
  }

  //Setup the delete action
  userBlock.find(".icon-cancel").on("click", function () {
    userBlock.find(".icon-cancel").addClass("icon-spin6")
    userBlock.find(".icon-cancel").addClass("animate-spin")
    userBlock.find(".icon-cancel").css("pointer-events", "none")
    userBlock.find(".icon-cancel").removeClass("icon-cancel")
    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.groups.deleteUser",
      type: "POST",
      data: "group_id=" + grp_id + "&user_id=" + user_id + "&pwg_token=" + pwg_token,
      success: function (raw_data) {
        data = jQuery.parseJSON(raw_data);
        if (data.stat === "ok") {
          let str = str_user_dissociated.replace("%s", username)
          associateUserInfo.fadeOut();
          dissociateUserInfo.find("p").html(str);
          dissociateUserInfo.fadeIn()

          $(".UsernameBlock").css("margin-right", "10px").css("border", "none");
          userBlock.remove()

          updateUserSearch();

          while ($(".UsersInGroupList").height() > maxOffsetUserCont) {
            $(".UsernameBlock").last().remove();
          }

          usersInGroup = usersInGroup.filter(u => u.id != user_id)

          //Update member number
          updateMembernumber(parseInt($(".UserNumberBadge").html()) -1, grp_id);
        }
      }
    });
  })
  return userBlock;
} 

//Update member number function
function updateMembernumber(number, grp_id) {
  $(".GroupContainer[data-id="+grp_id+"] .group_number_users")
    .html(number + " " + ((number > 1)? str_members_default:str_member_default));
  $(".UserNumberBadge").html(number);
  $(".AmountOfUsersShown strong:nth-child(2)").html(number)
  $(".AmountOfUsersShown strong:nth-child(1)").html($(".UsernameBlock").length)
}

// Close pop-up on cross click
$(".CloseUserList").on("click", function() {$('#UserList').fadeOut();})

// Adding Group Action
$(".AddUserBlock button").on("click", function () {
  let grp_id = $("#UserList").attr("data-group_id");
  let usersString = ""
  // Get selected ids
  let id = selectize.getValue();

  if (id != "") {
    let loadState = new TemporaryState();
    loadState.changeHTML($("#UserSubmit"),"<i class='icon-spin6 animate-spin'> </i>");
    loadState.removeClass($("#UserSubmit"),"icon-user-add");
    loadState.changeAttribute($("#UserSubmit"),"css","pointer-events:none")
    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.groups.addUser",
      type: "POST",
      data: "group_id=" + grp_id+ "&user_id=" + id + "&pwg_token=" + pwg_token,
      success: function (raw_data) {
        loadState.reverse()
        data = jQuery.parseJSON(raw_data);

        if (data.stat === "ok") {
          // Get the username
          let username = "undefined";
          JSON.parse(usersCache.storage[usersCache.key]).data.forEach(function(u) {
            if (u.id == id) {
              username = u.username;
            }
          })
          let userBlock = getUserDisplay(username, id, grp_id).prependTo(".UsersInGroupList");
          
          dissociateUserInfo.fadeOut()
          
          $(".UsernameBlock:first").css("margin-right", "0px").css("border", "2px solid #c2f5c2");
          $(".UsernameBlock").slice(1).css("margin-right", "10px").css("border", "none");
          associateUserInfo.remove()
          associateUserInfo.insertAfter(userBlock);
          associateUserInfo.find("p").html(str_user_associated);
          associateUserInfo.fadeIn()

          updateUserSearch();

          usersInGroup.push({username: username, id:id});
          
          while ($(".UsersInGroupList").height() > maxOffsetUserCont) {
            $(".UsernameBlock").last().remove();
          }

          //Update member number
          updateMembernumber(parseInt($(".UserNumberBadge").html()) + 1, grp_id);
        }
      }
    });
  }
});

$(".input-user-name").on("input", function() {
  searchString = $(this).val().toLowerCase();
  grp_id = $(".UserListPopIn").data("group_id");
  if (searchString != "") {
    $(".UsersInGroupListContainer").css("min-height", $(".UsersInGroupListContainer").height())
    usersInGroup.forEach(function(u) {
      let isSearched = u.username.toLowerCase().includes(searchString)
      if ($(".UsernameBlock[data-id="+u.id+"]").length != 0) {
        if (!isSearched) {
          $(".UsernameBlock[data-id="+u.id+"]").remove();
        }
      } else if (isSearched) {
        getUserDisplay(u.username, u.id, grp_id)
          .prependTo(".UsersInGroupList");
      }
    })
  } else {
    $(".UsersInGroupListContainer").css("min-height", "")
    $(".UsersInGroupList").html("");
    let i = 0;
    while ($(".UsersInGroupList").outerHeight() <= maxOffsetUserCont && usersInGroup[i] != undefined){
      getUserDisplay(usersInGroup[i].username, usersInGroup[i].id, grp_id)
        .appendTo(".UsersInGroupList");
      i++;
    }
  }
  $(".AmountOfUsersShown strong:nth-child(1)").html($(".UsernameBlock").length)
  while ($(".UsersInGroupList").height() > maxOffsetUserCont) {
    $(".UsernameBlock").last().remove();
  }
})