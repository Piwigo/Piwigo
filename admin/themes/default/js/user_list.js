const color_icons = ["icon-red", "icon-blue", "icon-yellow", "icon-purple", "icon-green"];
const status_arr = ['webmaster', 'admin', 'normal', 'generic', 'guest'];
const level_arr = ['0', '1', '2', '4', '8'];
let current_users = [];
let guest_id = 0;
let guest_user = {};
let connected_user = 0;
let groups_arr = [];
let nb_days = '';
let nb_photos = '';
let nb_photos_per_page = '';
let last_user_index = -1;
let last_user_id = -1;
let pwg_token = '';
let selection = [];
let first_update = true;
let total_users = 0
/*----------------
Escape of pop-in
----------------*/

//get out of pop in via escape key
$(document).on('keydown', function (e) {
    if ( e.keyCode === 27) { // ESC button
        $("#UserList").fadeOut();
    }
});

//get out of pop in via clicking outside pop in
//$(document).on('click', function (e) {
//    console.log($(e.target));
//    if ($(e.target) && $(e.target).closest(".UserListPopInContainer").length === 0) {
//        $("#UserList").fadeOut();
//    }
//});

/*----------------
Group Selectize
----------------*/

jQuery('[data-selectize=groups]').selectize({
    valueField: 'value',
    labelField: 'label',
    searchField: ['label'],
    plugins: ['remove_button']
});

let groupSelectize = jQuery('[data-selectize=groups]')[0].selectize;
let groupGuestSelectize = jQuery('[data-selectize=groups]')[1].selectize;

/*-----------------
OnClick functions
-----------------*/
function open_user_list() {
    hide_temporary_messages();
    $("#UserList").fadeIn();
}

function close_user_list() {
    hide_temporary_messages();
    $("#UserList").fadeOut();
}

function open_guest_user_list() {
    hide_temporary_messages();
    $("#GuestUserList").fadeIn();
}

function close_guest_user_list() {
    hide_temporary_messages();
    $("#GuestUserList").fadeOut();
}


function isSelectionMode() {
    return $("#toggleSelectionMode").is(":checked")
}


$( document ).ready(function() {
    $(".user-property-register").tipTip({
        maxWidth: "300px",
        delay: 0,
        fadeIn: 200,
        fadeOut: 200
    });
    $(".user-property-last-visit").tipTip({
        maxWidth: "300px",
        delay: 0,
        fadeIn: 200,
        fadeOut: 200
    });
    $(".advanced-filter-level select option").eq(1).remove();
    $('.edit-password').click(function () {
        $('.user-property-password').hide();
        $('.user-property-password-change').show().css('display', 'flex');
    })

    $('.edit-password-cancel').click(function () {
      //possibly reset input value
        $('.user-property-password').show();
        $('.user-property-password-change').hide();
    })

    $('.edit-username').click(function () {
        $('.user-property-username').hide();
        $('.user-property-username-change').show().css('display', 'flex');
    })

    $('.edit-username-cancel').click(function () {
        //possibly reset input value
        $('.user-property-username').show();
        $('.user-property-username-change').hide();
    })

    $('#UserList .close-update-button').click(close_user_list);
    $('.CloseUserList').click(close_user_list);

    $("#toggleSelectionMode").attr("checked", false);
    $("#toggleSelectionMode").click(function () {
        let isSelection = $(this).is(":checked");
        selectionMode(isSelection);
    });
    $('.edit-guest-user-button').click(open_guest_user_list);
    $('.CloseGuestUserList').click(close_guest_user_list);
    $('#GuestUserList .close-update-button').click(close_guest_user_list);

    $("#show_password").click(function() {
        if ($(this).hasClass("icon-eye")) {
            $(this).removeClass("icon-eye");
            $(this).addClass("icon-eye-off");
            $("#AddUserPassword").get(0).type = "text";
        } else {
            $(this).removeClass("icon-eye-off");
            $(this).addClass("icon-eye");
            $("#AddUserPassword").get(0).type = "password";
        }
    })
    /* Action */
    jQuery("[id^=action_]").hide();

    jQuery("select[name=selectAction]").change(function () {
        jQuery("#applyActionBlock .infos").hide();
        jQuery("[id^=action_]").hide();
        jQuery("#action_"+$(this).prop("value")).show();
        if (jQuery(this).val() != -1) {
            jQuery("#applyActionBlock").show();
        } else {
            jQuery("#applyActionBlock").hide();
        }
    });
    $(".yes_no_radio .user-list-checkbox").unbind("click").click(function () {
        if ($(this).attr("data-selected") !== "1") {
            $(this).attr("data-selected", "1");
            $(this).siblings().attr("data-selected", "0");
        }
    })
    $(".AddUserGenPassword").click(gen_password);
    $('.AddUserSubmit').click(add_user);
    $('.AddUserCancel').click(add_user_close);
    $(".CloseAddUser").click(add_user_close);   


    //open add user pop in
    $('.add-user-button').click(add_user_open);

    /* Select */

    jQuery("#selectSet").click(function () {
        select_whole_set();
        return false;
    });


    jQuery("#selectAllPage").click(function () {
        let selection_ids = selection.map(x => x.id);
        for (let i = 0; i < current_users.length; i++) {
            if (!selection_ids.includes(current_users[i].id)) {
                selection.push({id: current_users[i].id, username: current_users[i].username});
            }
        }
        update_selection_content();
        return false;
    });

    jQuery("#selectNone").click(function () {
        selection = [];
        update_selection_content();
        return false;
    });

    jQuery("#selectInvert").click(function () {
        let selection_ids = selection.map(x => x.id);
        for (let i = 0; i < current_users.length; i++) {
            if (selection_ids.includes(current_users[i].id)) {
                selection.splice(selection.findIndex((x) => x.id == current_users[i].id), 1);
            } else {
                selection.push({id: current_users[i].id, username: current_users[i].username});
            }
        }
        update_selection_content();
        return false;
    });

    $("#permitActionUserList select[name=selectAction]").val("-1");

    $(".advanced-filter-btn").click(advanced_filter_button_click);
    $(".advanced-filter span.icon-cancel").click(advanced_filter_hide);
    $(".advanced-filter-select").change(update_user_list);
    $("#user_search").on("input", update_user_list);


    /*View manager*/

    if ($("#displayCompact").is(":checked")) {
        setDisplayCompact();
    };

    if ($("#displayLine").is(":checked")) {
        setDisplayLine();
    };

    if ($("#displayTile").is(":checked")) {
        setDisplayTile();
    };

    $("#displayCompact").change(function () {
        setDisplayCompact();

        if ($(".addAlbum").hasClass("input-mode")) {
            $(".addAlbum p").hide();
        }
        set_view_selector('compact');
    });

    $("#displayLine").change(function () {
        setDisplayLine();

        if ($(".addAlbum").hasClass("input-mode")) {
            $(".addAlbum p").hide();
        }
        set_view_selector('line');
    });

    $("#displayTile").change(function () {
        setDisplayTile();

        if ($(".addAlbum").hasClass("input-mode")) {
            $(".addAlbum p").show();
        }
        set_view_selector('tile');
    });

    /* Pagination */

    switch (pagination) {
      case '5':
        $("#pagination-per-page-5").addClass("selected-pagination");
        $("#pagination-per-page-10").removeClass("selected-pagination");
        $("#pagination-per-page-25").removeClass("selected-pagination");
        $("#pagination-per-page-50").removeClass("selected-pagination");
        break;
      case '10':
        $("#pagination-per-page-5").removeClass("selected-pagination");
        $("#pagination-per-page-10").addClass("selected-pagination");
        $("#pagination-per-page-25").removeClass("selected-pagination");
        $("#pagination-per-page-50").removeClass("selected-pagination");
      
        break;
      case '25':
        $("#pagination-per-page-5").removeClass("selected-pagination");
        $("#pagination-per-page-10").removeClass("selected-pagination");
        $("#pagination-per-page-25").addClass("selected-pagination");
        $("#pagination-per-page-50").removeClass("selected-pagination");
      
        break;
      case '50':
        $("#pagination-per-page-5").removeClass("selected-pagination");
        $("#pagination-per-page-10").removeClass("selected-pagination");
        $("#pagination-per-page-25").removeClass("selected-pagination");
        $("#pagination-per-page-50").addClass("selected-pagination");
        break;
      default:

        break;
    }

    $("#pagination-per-page-"+pagination).trigger('click');

    if (has_group) {
      advanced_filter_button_click();
      $("select[name='filter_group']").val(has_group);
      update_user_list();
    }

    $('.search-cancel').on('click', function () {
      $('.search-input').val('');
      $('.search-input').trigger ("input");
    })
    
    $('.search-input').on('input', function() {
      if ($('.search-input').val() == '') {
        $('.search-cancel').hide();
      } else {
        $('.search-cancel').show();
      }
    })
});

function set_view_selector(view_type) {
  $.ajax({
    url: "ws.php?format=json&method=pwg.users.preferences.set",
    type: "POST",
    dataType: "JSON",
    data: {
      param: 'user-manager-view',
      value: view_type,
    }
  })
}

function setDisplayTile() {
    $(".user-container-wrapper").removeClass("compactView").removeClass("lineView").addClass("tileView");
    $(".user-header-col").addClass("hide");

}

function setDisplayLine() {
    $(".user-container-wrapper").removeClass("tileView").removeClass("compactView").addClass("lineView");
    $(".user-header-col").removeClass("hide");

}

function setDisplayCompact() {
    $(".user-container-wrapper").removeClass("tileView").removeClass("lineView").addClass("compactView");
    $(".user-header-col").addClass("hide");

    if (per_page < 10) {
        per_page = 10
        update_pagination_menu();
        update_user_list();
    
        $("#pagination-per-page-5").removeClass("selected-pagination");
        $("#pagination-per-page-10").addClass("selected-pagination");
        $("#pagination-per-page-25").removeClass("selected-pagination");
        $("#pagination-per-page-50").removeClass("selected-pagination");
    }
}


/*----------------
Checkboxes
----------------*/

function checkbox_change() {
    if ($(this).attr('data-selected') == '1') {
        $(this).find("i").hide();
    } else {
        $(this).find("i").show();
    }
}

function checkbox_click() {
    if ($(this).attr('data-selected') == '1') {
        $(this).attr('data-selected', '0');
        $(this).find("i").hide();
    } else {
        $(this).attr('data-selected', '1');
        $(this).find("i").show();
    }
}

$('.user-list-checkbox').unbind("change").change(checkbox_change);
$('.user-list-checkbox').unbind("click").click(checkbox_click);

/* ---------------
User edit sliders 
----------------*/
const nb_image_page_values = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,35,40,45,50,60,70,80,90,100,200,300,500,999];
const recent_period_values = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,25,30,40,50,60,80,99];
const recent_period_init = 0;
//const recent_period_init = getSliderKeyFromValue($('.period-select-bar input[name=recent_period]').val(), recent_period_values);
const nb_image_page_init = 0;
//const nb_image_page_init = getSliderKeyFromValue($('.photos-select-bar input[name=nb_image_page]').val(), nb_image_page_values);

/**
* find the key from a value in the startStopValues array
*/
function getSliderKeyFromValue(value, values) {
    for (var key in values) {
        if (values[key] >= value) {
            return key;
        }
    }
    return 0;
}

function getNbImagePageInfoFromIdx(idx) {
    return sprintf(
        nb_photos,
        nb_image_page_values[idx]
    );
}

function getRecentPeriodInfoFromIdx(idx) {
    return sprintf(
        nb_days,
        recent_period_values[idx]
    );
    //return recent_period_values[idx].toString();
}

/* Photos bar slider */
jQuery('#UserList .photos-select-bar .slider-bar-container').slider({
    range: "min",
    min: 0,
    max: nb_image_page_values.length - 1,
    value: nb_image_page_init,
    change: function( event, ui ) {
        $('#UserList .photos-select-bar .nb-img-page-infos').html(getNbImagePageInfoFromIdx(ui.value));
    },
    slide: function( event, ui ) {
        $('#UserList .photos-select-bar .nb-img-page-infos').html(getNbImagePageInfoFromIdx(ui.value));
    },
    stop: function( event, ui ) {
        $('#UserList .photos-select-bar input[name=nb_image_page]').val(nb_image_page_values[ui.value]).trigger('change');
    }
});


jQuery('#GuestUserList .photos-select-bar .slider-bar-container').slider({
    range: "min",
    min: 0,
    max: nb_image_page_values.length - 1,
    value: nb_image_page_init,
    change: function( event, ui ) {
        $('#GuestUserList .photos-select-bar .nb-img-page-infos').html(getNbImagePageInfoFromIdx(ui.value));
    },
    slide: function( event, ui ) {
        $('#GuestUserList .photos-select-bar .nb-img-page-infos').html(getNbImagePageInfoFromIdx(ui.value));
    },
    stop: function( event, ui ) {
        $('#GuestUserList .photos-select-bar input[name=nb_image_page]').val(nb_image_page_values[ui.value]).trigger('change');
    }
});

$('#permitActionUserList .photos-select-bar .nb-img-page-infos').html(getNbImagePageInfoFromIdx(0));
jQuery('#permitActionUserList .photos-select-bar .slider-bar-container').slider({
    range: "min",
    min: 0,
    max: nb_image_page_values.length - 1,
    value: nb_image_page_init,
    change: function( event, ui ) {
        $('#permitActionUserList .photos-select-bar .nb-img-page-infos').html(getNbImagePageInfoFromIdx(ui.value));
    },
    slide: function( event, ui ) {
        $('#permitActionUserList .photos-select-bar .nb-img-page-infos').html(getNbImagePageInfoFromIdx(ui.value));
    },
    stop: function( event, ui ) {
        $('#permitActionUserList .photos-select-bar input[name=nb_image_page]').val(nb_image_page_values[ui.value]).trigger('change');
    }
});

/* recent_period slider */
$('#UserList .period-select-bar .slider-bar-container').slider({
    range: "min",
    min: 0,
    max: recent_period_values.length - 1,
    value: recent_period_init,
    change: function( event, ui ) {
        $('#UserList .period-select-bar .recent_period_infos').html(getRecentPeriodInfoFromIdx(ui.value));
    },
    slide: function( event, ui ) {
        $('#UserList .period-select-bar .recent_period_infos').html(getRecentPeriodInfoFromIdx(ui.value));
    },
    stop: function( event, ui ) {
        $('#UserList .period-select-bar input[name=recent_period]').val(recent_period_values[ui.value]).trigger('change');
    }
});

$('#GuestUserList .period-select-bar .slider-bar-container').slider({
    range: "min",
    min: 0,
    max: recent_period_values.length - 1,
    value: recent_period_init,
    change: function( event, ui ) {
        $('#GuestUserList .period-select-bar .recent_period_infos').html(getRecentPeriodInfoFromIdx(ui.value));
    },
    slide: function( event, ui ) {
        $('#GuestUserList .period-select-bar .recent_period_infos').html(getRecentPeriodInfoFromIdx(ui.value));
    },
    stop: function( event, ui ) {
        $('#GuestUserList .period-select-bar input[name=recent_period]').val(recent_period_values[ui.value]).trigger('change');
    }
});

$('#permitActionUserList .period-select-bar .slider-bar-container').slider({
    range: "min",
    min: 0,
    max: recent_period_values.length - 1,
    value: recent_period_init,
    change: function( event, ui ) {
        $('#permitActionUserList .period-select-bar .recent_period_infos').html(getRecentPeriodInfoFromIdx(ui.value));
    },
    slide: function( event, ui ) {
        $('#permitActionUserList .period-select-bar .recent_period_infos').html(getRecentPeriodInfoFromIdx(ui.value));
    },
    stop: function( event, ui ) {
        $('#permitActionUserList .period-select-bar input[name=recent_period]').val(recent_period_values[ui.value]).trigger('change');
    }
});
$('#permitActionUserList .photos-select-bar .slider-bar-container').slider("option", "value", 0);
let period_info = getRecentPeriodInfoFromIdx(0);
$('#permitActionUserList .period-select-bar .recent_period_infos').html(period_info);


/* -----------
Pagination
------------*/

let per_page = 5;
let actual_page = 1;
let max_page = 1;
let nb_filtered_users = 0;
const page_ellipsis = '<span>...</span>'
const page_item = '<a data-page="%d">%d</a>';
let promise_pending = false;
let update_ask = false;

function move_to_page(page) {
    if (page < 1 || page > max_page)
        return;
    actual_page = page;
    update_pagination_menu();
    update_user_list();
}

$('.pagination-arrow.rigth').on('click', () => {
    move_to_page(actual_page + 1);
})

$('.pagination-arrow.left').on('click', () => {
    move_to_page(actual_page - 1);
})

$('.pagination-per-page a').on('click',function () {

    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.users.preferences.set",
      type: "POST",
      data: {
          param: "user-manager-pagination",
          value: parseInt($(this).html()),
          is_json: false,
      }
  });

    per_page = parseInt($(this).html());
    actual_page = 1;
    update_pagination_menu();
    update_user_list();


    $(this).parent().children("a").removeClass("selected-pagination");

    $(this).addClass("selected-pagination");
});

function append_pagination_item(page = null) {
    if (page != null) {
        let new_tag = $(page_item.replace(/%d/g, page));
        $('.pagination-item-container').append(new_tag);
        if (actual_page == page) {
            new_tag.addClass('actual');
        }
        new_tag.on('click', () => {
            move_to_page(new_tag.data('page'));
        })
    } else {
        $('.pagination-item-container').append($(page_ellipsis));
    }
}

function update_pagination_items() {
    $('.pagination-item-container a').remove();
    $('.pagination-item-container span').remove();

    append_pagination_item(1);

    if (actual_page > 2) {
        append_pagination_item();
    }
    if (actual_page != 1 && actual_page != max_page) {
        append_pagination_item(actual_page)
    }
    if (actual_page < (max_page - 1)) {
        append_pagination_item();
    }   
    append_pagination_item(max_page);

}

function update_pagination_menu() {
    max_page = Math.ceil(nb_filtered_users / per_page);
    updateArrows();
    update_pagination_items();
    if (max_page <= 1) {
        $('.pagination-container').hide();
    } else {
        $('.pagination-container').show();
    }
}

function updateArrows() {
    if (actual_page == 1) {
        $('.pagination-arrow.left').addClass('unavailable');
    } else {
        $('.pagination-arrow.left').removeClass('unavailable');
    }   
    if (actual_page == max_page) {
        $('.pagination-arrow.rigth').addClass('unavailable');
    } else {
        $('.pagination-arrow.rigth').removeClass('unavailable');
    }
}

/*------------------
Advanced filter
------------------*/

function advanced_filter_button_click() {
    if (!$(".advanced-filter").hasClass("advanced-filter-open")) {
        advanced_filter_show();
    } else { 
        advanced_filter_hide();
    }
    // update_user_list();
}

function advanced_filter_show() {
    $(".advanced-filter-btn, .advanced-filter").addClass("advanced-filter-open");
}

function advanced_filter_hide() {
    $(".advanced-filter-btn, .advanced-filter").removeClass("advanced-filter-open");
}

let months = [];

function getDateStr(date) {
    let date_arr = date.split(' ');
    let curr_month = months[parseInt(date_arr[0]) - 1];
    return curr_month + " " + date_arr[1]
}

function setupRegisterDates(register_dates) {
    $('.advanced-filter .dates-select-bar .slider-bar-container').slider({
        range: true,
        min: 0,
        max: register_dates.length - 1,
        values: [0, register_dates.length - 1],
        change: function( event, ui ) {
            $(".advanced-filter .dates-infos").html(sprintf(dates_infos, getDateStr(register_dates[ui.values[0]]), getDateStr(register_dates[ui.values[1]])));
        },
        slide: function( event, ui ) {
            $(".advanced-filter .dates-infos").html(sprintf(dates_infos, getDateStr(register_dates[ui.values[0]]), getDateStr(register_dates[ui.values[1]])));
        },
        stop: function( event, ui ) {
            $(".advanced-filter .dates-infos").html(sprintf(dates_infos, getDateStr(register_dates[ui.values[0]]), getDateStr(register_dates[ui.values[1]])));
            update_user_list();
        }
    });

    $(".advanced-filter .dates-infos").html(sprintf(dates_infos, getDateStr(register_dates[0]), getDateStr(register_dates[register_dates.length - 1])));
            
}
/*------------------
Add User
------------------*/

function gen_password(e) {
    e.preventDefault();

    var characterSet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';

    var i;
    var password;
    var length = getRandomInt(8, 15);

    password = '';
    for (i = 0; i < length; i++) {
      password += characterSet.charAt(Math.floor(Math.random() * characterSet.length));
    }

    jQuery("#AddUserPassword").val(password);
}

function add_user_close() {
    $("#AddUser").fadeOut();
}

function add_user_open() {
    $('#AddUser .AddUserInput').val('');
    $("#AddUser").fadeIn();
    $(".AddUserLabelUsername input").first().focus();
}

/*------------------
Selection mode
------------------*/

function checkbox_container_change() {
    if ($(this).attr('data-selected') == '1') {
        $(this).attr('data-selected', '0');
        $(this).find("i").hide();
    } else {
        $(this).attr('data-selected', '1');
        $(this).find("i").show();
    }
}

function checkbox_container_click() {
    let curr_container = $(this).closest(".user-container");
    let in_container = curr_container.length != 0;
    let curr_user = in_container ? current_users[parseInt(curr_container.attr("key"))] : {id: -1};
    if ($(this).attr('data-selected') == '1') {
        $(this).attr('data-selected', '0');
        $(this).find("i").hide();
        if (in_container) {
            curr_container.removeClass("container-selected");
            selection = selection.filter((elem) => elem.id != curr_user.id);
        }
    } else {
        $(this).attr('data-selected', '1');
        $(this).find("i").show();
        if (in_container) {
            curr_container.addClass("container-selected");
            selection.push({id: curr_user.id, username: curr_user.username})
        }
    }
    if (in_container) {
        update_selection_content();
    }
}

function create_user_selected_item(user) {
    let new_elem = $("#template .user-selected-item").clone();
    new_elem.attr("data-id", user.id.toString());
    new_elem.find("p").html(user.username);
    new_elem.find("a").click(() => {
        selection.splice(selection.findIndex((i) => i.id == user.id), 1);
        update_selection_content();
    })
    return new_elem;
}

function generate_user_selected_items() {
    let items_created = 0;
    let others = selection.length - 5;
    $('.user-selected-list .user-selected-item').remove();
    for (let i = 0; i < selection.length && items_created < 5; i++) {
        if (typeof selection[i].username !== 'undefined') {
            $('.user-selected-list').append(create_user_selected_item(selection[i])); 
            items_created += 1;
        }
    }
    if (others >= 1) {
        $(".selection-other-users").html(str_and_others_tags.replace('%s', others))
        $(".selection-other-users").show()
    } else {
        $(".selection-other-users").hide();
    }
    return
}

function fill_user_selected_list() {
    let elems_with_username = 0;
    for (let i = 0; i < selection.length && elems_with_username < 5;i++) {
        if (typeof selection[i].username !== 'undefined') {
            elems_with_username += 1;
        }
    }
    if (elems_with_username < 5 && elems_with_username != selection.length) {
        get_first_selection_usernames(generate_user_selected_items);
    } else {
        generate_user_selected_items();
    }
}

function update_selection_content() {
    number = selection.length;
    fill_user_selected_list();
    if (number == 0) {
        $("#forbidAction").show();
        $('.selection-mode-ul').hide();
        $("#permitActionUserList").hide();
    } else {
        $("#forbidAction").hide();
        $("#permitActionUserList").show();
        $('.selection-mode-ul').show();
    }
    set_selected_to_selection();
    $("#applyActionBlock .infos").hide();
}

function set_selected_to_selection() {
    if (!$("#toggleSelectionMode").is(":checked")) {
        return
    }
    $(".user-container-wrapper .user-container").each(function (index) {
        selection_ids = selection.map(x => x.id);
        if (selection_ids.includes(current_users[index].id)) {
            $(this).addClass("container-selected");
            $(this).find(".user-list-checkbox").attr("data-selected", "1");
            $(this).find(".user-list-checkbox i").show();
        } else {
            $(this).removeClass("container-selected");
            $(this).find(".user-list-checkbox").attr("data-selected", "0");
            $(this).find(".user-list-checkbox i").hide();
        }
    })
}

function selectionMode(isSelection) {
    $("#permitActionUserList select[name=selectAction]").val("-1");
    $("#permitActionUserList select[name=selectAction]").trigger("change");
    if (isSelection) {
        //resets the selection
        //selection = [];
        set_selected_to_selection();
        $(".in-selection-mode").show();
        $(".not-in-selection-mode").hide();

        if (view_selector === "tile") {
            $(".user-container-email").show();
        }

        if (view_selector === "compact") {
            $(".user-container-email").css({
                display: "none"
            })
        }

        $(".user-container").addClass("selectable");
    } else {
        $(".container-selected").removeClass("container-selected");
        $(".in-selection-mode").hide();
        $(".not-in-selection-mode").show();

        if (view_selector === "tile" || view_selector === "line") {
            $(".user-container-email").css({
                display: "flex"
            })
        }

        $(".user-container").removeClass("selectable");

    }
}


/*------------------
General functions
------------------*/

function hide_temporary_messages() {
    $(".update-user-success").hide();
    $("#AddUserSuccess").hide();
    $('.error-msg').hide();
}

function get_group_name_from_id(id) {
    for (let i = 0; i < groups_arr.length; i++) {
        if (groups_arr[i][0] == id) {
            return (groups_arr[i][1]);
        }
    }
    return ("group_id error");
}

function get_container_index_from_uid(uid) {
    for (let i = 0; i < current_users.length; i++) {
        if (current_users[i].id == uid) {
            return i;
        }
    }
    return -1;
}

/*-----------------------
Generate User Containers
-----------------------*/
function user_container_click() {
    if (!isSelectionMode()) {
        return;
    }
    let curr_container = $(this)
    let in_container = curr_container.length != 0;
    let container_checkbox = $(this).find('.user-list-checkbox');
    let curr_user = in_container ? current_users[parseInt(curr_container.attr("key"))] : {id: -1};
    if (container_checkbox.attr('data-selected') == '1') {
        container_checkbox.attr('data-selected', '0');
        container_checkbox.find("i").hide();
        if (in_container) {
            curr_container.removeClass("container-selected");
            selection = selection.filter((elem) => elem.id != curr_user.id);
        }
    } else {
        container_checkbox.attr('data-selected', '1');
        container_checkbox.find("i").show();
        if (in_container) {
            curr_container.addClass("container-selected");
            selection.push({id: curr_user.id, username: curr_user.username})
        }
    }
    if (in_container) {
        update_selection_content();
    }
}

function generate_groups(container, groups) {
    container.find(".user-container-groups").html('');
    if (groups.length >= 1) {
        let primary_grp = $("#template .group-primary").clone();
        primary_grp.html(get_group_name_from_id(groups[0]));
        primary_grp.addClass(color_icons[groups[0] % 5]);
        container.find(".user-container-groups").append(primary_grp);
    }
    if (groups.length >= 2) {
        let primary_grp = $("#template .group-primary").clone();
        primary_grp.html(get_group_name_from_id(groups[1]));
        primary_grp.addClass(color_icons[groups[1] % 5]);
        container.find(".user-container-groups").append(primary_grp);
    }
    if (groups.length >= 3) {
        let bonus_grp = $("#template .group-bonus").clone();
        bonus_grp.html("...");
        bonus_grp.addClass(color_icons[groups[2] % 5]);
        bonus_grp.addClass("tiptip");
        let groups_in_title = "";
        for (let i = 2; i < groups.length; i++) {
            groups_in_title += get_group_name_from_id(groups[i]) + ", ";
        }
        groups_in_title = groups_in_title.substring(0, groups_in_title.length -2);
        bonus_grp.prop('title', groups_in_title);
        container.find(".user-container-groups").append(bonus_grp);
    }
}

function get_initials(username) {
    let words = username.toUpperCase().split(" ");
    let res = words[0][0];

    if (words.length > 1 && words[1][0] !== undefined ) {
        res += words[1][0];
    }
    return res;
}

function fill_container_user_info(container, user_index) {
    let user = current_users[user_index];
    let registration_dates = user.registration_date.split(' ');
    container.attr('key', user_index);
    container.find(".user-container-username span").html(user.username);
    container.find(".user-container-initials span").html(get_initials(user.username)).addClass(color_icons[user.id % 5]);
    container.find(".user-container-status span").html(user.status);
    container.find(".user-container-email span").html(user.email);
    generate_groups(container, user.groups);
    container.find(".user-container-registration-date").html(registration_dates[0]);
    container.find(".user-container-registration-time").html(registration_dates[1]);
    container.find(".user-container-registration-date-since").html(user.registration_date_since);
}  

function generate_user_list() {
    $("#user-table-content").find(".user-container").remove();
    for (let i = 0; i < current_users.length; i++) {
        let new_container = $("#template .user-container").clone();
        fill_container_user_info(new_container, i);
        $("#user-table-content .user-container-wrapper").append(new_container);
    }
    $('.user-container .user-list-checkbox').unbind("change").change(checkbox_change);
    $('.user-container .user-list-checkbox').unbind("click");
    $(".user-container").click(user_container_click);
}

/*---------------------
Fill the pop-in values
---------------------*/

function get_formatted_date(date_str) {
    if (date_str === null) {
        return "N/A"
    }
    let first_part = date_str.split(' ')[0];
    let formatted = first_part.split('-').join('/');
    console.log(formatted);
    return (formatted);
}

function get_status_index(status) {
    for (let i = 0; i < status_arr.length; i++) {
        if (status_arr[i] === status) {
            return i;
        }
    }
    return 0;
}

function get_level_index(level) {
    for (let i = 0; i < level_arr.length; i++) {
        if (level_arr[i] === level) {
            return i;
        }
    }
    return 0;
}

function set_selected_groups(groups) {
    for (let i = 0; i < groupOptions.length; i++) {
        groupOptions[i].isSelected = groups.includes(groupOptions[i].value);
    }
}

function fill_user_edit_summary(user_to_edit, pop_in, isGuest) {
    console.log(isGuest);
    if (isGuest) {
      pop_in.find('.user-property-initials span').removeClass(color_icons.join(' ')).addClass(color_icons[user_to_edit.id % 5]);
    } else {
      pop_in.find('.user-property-initials span').html(get_initials(user_to_edit.username)).removeClass(color_icons.join(' ')).addClass(color_icons[user_to_edit.id % 5]);
    }
    pop_in.find('.user-property-username span:first').html(user_to_edit.username); 
    
    
    if (user_to_edit.id === connected_user || user_to_edit.id === 1) {
        pop_in.find('.user-property-username .edit-username-specifier').show();
    } else {
        pop_in.find('.user-property-username .edit-username-specifier').hide();
    }
    pop_in.find('.user-property-username-change input').val(user_to_edit.username);
    pop_in.find('.user-property-password-change input').val('');
    pop_in.find('.user-property-permissions a').attr('href', `admin.php?page=user_perm&user_id=${user_to_edit.id}`);
    pop_in.find('.user-property-register').html(get_formatted_date(user_to_edit.registration_date));
    pop_in.find('.user-property-register').tipTip({content:`${registered_str}<br />${user_to_edit.registration_date_since}`});
    pop_in.find('.user-property-last-visit').html(get_formatted_date(user_to_edit.last_visit));
    pop_in.find('.user-property-last-visit').tipTip({content: `${last_visit_str}<br />${user_to_edit.last_visit_since}`});
    pop_in.find('.user-property-history a').attr('href', history_base_url + user_to_edit.id);
}

function fill_user_edit_properties(user_to_edit, pop_in) {
    let status_index = get_status_index(user_to_edit.status);
    let level_index = get_level_index(user_to_edit.level);
    let current_group_selectize = user_to_edit.id === guest_id ? groupGuestSelectize : groupSelectize;

    pop_in.find('.user-property-email input').val(user_to_edit.email);
    pop_in.find(`.user-property-status select option:eq(${status_index})`).prop("selected", true);
    pop_in.find(`.user-property-level select option:eq(${level_index})`).prop('selected', true);
    pop_in.find('.photos-select-bar input').val(user_to_edit.recent_period);
    set_selected_groups(user_to_edit.groups);
    current_group_selectize.clear();
    current_group_selectize.load(function(callback) {
        callback(groupOptions);
    });
    jQuery.each(jQuery.grep(groupOptions, function(group) {
        return group.isSelected;
    }), function(i, group) {
        current_group_selectize.addItem(group.value);
    });
    pop_in.find('.user-list-checkbox[name="hd_enabled"]').attr('data-selected', user_to_edit.enabled_high == 'true' ? '1' : '0');
}

function fill_user_edit_preferences(user_to_edit, pop_in) {
    let slider_key_photos = getSliderKeyFromValue(parseInt(user_to_edit.nb_image_page), nb_image_page_values);
    let slider_key_period = getSliderKeyFromValue(parseInt(user_to_edit.recent_period), recent_period_values);
    
    pop_in.find('.photos-select-bar .slider-bar-container').slider("option", "value", slider_key_photos);
    pop_in.find('.user-property-theme select option').each(function () {
        if ($(this).val() == user_to_edit.theme) {
            $(this).prop('selected', true);
        }
    });
    pop_in.find('.user-property-lang select option').each(function () {
        if ($(this).val() == user_to_edit.language) {
            $(this).prop('selected', true);
        }
    });
    pop_in.find('.period-select-bar .slider-bar-container').slider("option", "value", slider_key_period);
    pop_in.find('.user-list-checkbox[name="expand_all_albums"]').attr('data-selected', user_to_edit.expand == 'true' ? '1' : '0');
    pop_in.find('.user-list-checkbox[name="show_nb_comments"]').attr('data-selected', user_to_edit.show_nb_comments == 'true' ? '1' : '0');
    pop_in.find('.user-list-checkbox[name="show_nb_hits"]').attr('data-selected', user_to_edit.show_nb_hits == 'true' ? '1' : '0');   
}

function fill_user_edit_update(user_to_edit, pop_in) {
    pop_in.find('.update-user-button').unbind("click").click(
        user_to_edit.id === guest_id ? update_guest_info : update_user_info);
    pop_in.find('.edit-username-validate').unbind("click").click(update_user_username);
    pop_in.find('.edit-password-validate').unbind("click").click(update_user_password);
    pop_in.find('.delete-user-button').unbind("click").click(function () {
        $.confirm({
            title: title_msg.replace('%s', user_to_edit.username),
            buttons: {
                confirm: {
                    text: confirm_msg,
                    btnClass: 'btn-red',
                    action: function () {
                        delete_user(user_to_edit.id);
                    }
                },
                cancel: {
                    text: cancel_msg
                }
            },
            ...jConfirm_confirm_options
        });
    })
}

function fill_user_edit_permissions(user_to_edit, pop_in) {
  if (user_to_edit.id != connected_user) {
    // I'm not the connected user
    if (!is_owner(connected_user)) {
      // I'm not the owner, you need to test my permissions
      if (is_owner(user_to_edit.id)) {
        // I want to edit the owner but I'm not the owner (No matter my status)
        pop_in.find(".delete-user-button").hide();
        pop_in.find(".user-property-password.edit-password").hide();
        pop_in.find(".user-property-email .user-property-input").attr('disabled','disabled');
        pop_in.find(".user-property-status .user-property-select").addClass("notClickable");
        pop_in.find(".user-property-username .edit-username").hide();
      } else {
        pop_in.find(".user-property-password.edit-password").show();
        pop_in.find(".user-property-email .user-property-input").removeAttr('disabled');
        pop_in.find(".user-property-status .user-property-select").removeClass("notClickable");
        pop_in.find(".user-property-username .edit-username").show();
      }
      
      if (user_to_edit.status == connected_user_status && connected_user_status == "webmaster" && !is_owner(user_to_edit.id)) {
        // I have the same status than the user I want to edit and I'm a webmaster, I can do whatever I want
        pop_in.find(".delete-user-button").show();
        pop_in.find(".user-property-password.edit-password").show();
        pop_in.find(".user-property-email .user-property-input").removeAttr('disabled');
        pop_in.find(".user-property-status .user-property-select").removeClass("notClickable");
        pop_in.find(".user-property-username .edit-username").show();
      } else if (user_to_edit.status == connected_user_status && connected_user_status == "admin") {
        // I have the same status than the user I want to edit and I'm an admin, I can do whatever I want but edit the status
        pop_in.find(".delete-user-button").hide();
        pop_in.find(".user-property-password.edit-password").show();
        pop_in.find(".user-property-email .user-property-input").removeAttr('disabled');
        pop_in.find(".user-property-username .edit-username").removeClass("notClickable");
        pop_in.find(".user-property-status .user-property-select").addClass("notClickable");
      } else if (user_to_edit.status == "webmaster" && connected_user_status == "admin") {
        // I'm admin and I want to edit webmaster
        pop_in.find(".user-property-password.edit-password").hide();
        pop_in.find(".user-property-email .user-property-input").attr('disabled','disabled');
        pop_in.find(".user-property-status .user-property-select").addClass("notClickable");
        pop_in.find(".user-property-username .edit-username").hide();
      } else if (user_to_edit.status == "admin" && connected_user_status == "webmaster") {
        // I'm webmaster and I want to edit admin
        pop_in.find(".user-property-password.edit-password").show();
        pop_in.find(".user-property-email .user-property-input").removeAttr('disabled');
        pop_in.find(".user-property-status .user-property-select").removeClass("notClickable");
        pop_in.find(".user-property-username .edit-username").show();
      }
    } else {
      // I'm the owner, I can do whatever I want. No need to test, I am GOD here
      pop_in.find(".delete-user-button").show();
      pop_in.find(".user-property-password.edit-password").show();
      pop_in.find(".user-property-email .user-property-input").removeAttr('disabled');
      pop_in.find(".user-property-status .user-property-select").removeClass("notClickable");
      pop_in.find(".user-property-username .edit-username").show();
    }
  } else {
    // I'm the connected user, I can do whatever I want on my profile but kill myself (Suicide is not allowed) and edit my status
    pop_in.find(".delete-user-button").hide();
    pop_in.find(".user-property-password.edit-password").show();
    pop_in.find(".user-property-email .user-property-input").removeAttr('disabled');
    pop_in.find(".user-property-status .user-property-select").addClass("notClickable");
    pop_in.find(".user-property-username .edit-username").show();
  }

  $(".notClickableBefore").removeClass("notClickableBefore");
  $(".notClickable").parent().addClass("notClickableBefore");
}

function is_owner(user_id) {
  return user_id === owner_id;
}

function fill_user_edit(user_to_edit) {
    let pop_in = $('.UserListPopInContainer');
    fill_user_edit_summary(user_to_edit, pop_in, false);
    fill_user_edit_properties(user_to_edit, pop_in);
    fill_user_edit_preferences(user_to_edit, pop_in);
    fill_user_edit_update(user_to_edit, pop_in);
    fill_user_edit_permissions(user_to_edit, pop_in);
}

function fill_guest_edit() {
    let user_to_edit = guest_user;
    let pop_in = $('.GuestUserListPopInContainer');
    fill_user_edit_summary(user_to_edit, pop_in, true);
    fill_user_edit_properties(user_to_edit, pop_in);
    fill_user_edit_preferences(user_to_edit, pop_in);
    fill_user_edit_update(user_to_edit, pop_in);
}

/*-------------------
Fill data for setInfo
-------------------*/

function fill_ajax_data_from_properties(ajax_data, pop_in) {
    let groups_selected = pop_in.find('.user-property-group .selectize-input .item').map(function () {
        return parseInt($(this).attr('data-value'));
    } ).get();
    console.log(groups_selected);
    ajax_data['email'] = pop_in.find('.user-property-email input').val();
    if (connected_user_status == "admin" && pop_in.find('.user-property-status select').val() != "webmaster" && pop_in.find('.user-property-status select').val() != "admin") {
      ajax_data['status'] = pop_in.find('.user-property-status select').val();
    } else if (connected_user_status == "webmaster"){
      ajax_data['status'] = pop_in.find('.user-property-status select').val();
    }
    console.log(ajax_data['status']);
    ajax_data['level'] = pop_in.find('.user-property-level select').val();
    ajax_data['group_id'] = groups_selected.length == 0 ? -1 : groups_selected;
    ajax_data['enabled_high'] = pop_in.find('.user-list-checkbox[name="hd_enabled"]').attr('data-selected') == '1' ? true : false ;
    return ajax_data
}

function fill_ajax_data_from_preferences(ajax_data, pop_in) {
    ajax_data['theme'] = pop_in.find('.user-property-theme select').val();
    ajax_data['language'] = pop_in.find('.user-property-lang select').val();
    ajax_data['nb_image_page'] = nb_image_page_values[pop_in.find('.photos-select-bar .slider-bar-container').slider("option", "value")];
    ajax_data['recent_period'] = recent_period_values[pop_in.find('.period-select-bar .slider-bar-container').slider("option", "value")];
    ajax_data['expand'] = pop_in.find('.user-list-checkbox[name="expand_all_albums"]').attr('data-selected') == '1' ? true : false;
    ajax_data['show_nb_comments'] = pop_in.find('.user-list-checkbox[name="show_nb_comments"]').attr('data-selected') == '1' ? true : false ;
    ajax_data['show_nb_hits'] = pop_in.find('.user-list-checkbox[name="show_nb_hits"]').attr('data-selected') == '1' ? true : false ;
    return ajax_data
}

function fill_ajax_data_from_container(ajax_data, pop_in) {
    ajax_data = fill_ajax_data_from_properties(ajax_data, pop_in);
    ajax_data = fill_ajax_data_from_preferences(ajax_data, pop_in);
    return ajax_data
}

/*----------------
Ajax Requests
----------------*/

function get_first_selection_usernames(callback) {
    let first_ids = selection.slice(0, 50).map(x => x.id);
    jQuery.ajax({
        url: "ws.php?format=json&method=pwg.users.getList",
        type: "POST",
        data: {
            display: "username",
            order: "id",
            user_id: first_ids,
            exclude: [guest_id]
        },
        success:function(data) {
            data = jQuery.parseJSON(data);
            let result = data.result.users;
            for (let i = 0; i < result.length;i++) {
                let index = selection.findIndex(x => x.id === result[i].id);
                if (index != -1) {
                    selection[index].username = result[i].username;
                }
            }
            callback();
        }
    });
}

function select_whole_set() {
    jQuery.ajax({
        url: "ws.php?format=json&method=pwg.users.getList",
        type: "POST",
        data: {
            display: "only_id",
            order: "id",
            page: actual_page - 1,
            per_page: 0,
            exclude: [guest_id],
            status: $(".advanced-filter-select[name=filter_status]").val(),
            group_id: $(".advanced-filter-select[name=filter_group]").val(),
            min_level: $(".advanced-filter-select[name=filter_level]").val(),
            max_level: $(".advanced-filter-select[name=filter_level]").val(),
            min_register: register_dates[$(".dates-select-bar .slider-bar-container").slider("option", "values")[0]],
            max_register: register_dates[$(".dates-select-bar .slider-bar-container").slider("option", "values")[1]],
        },
        beforeSend: function() {
            $("#checkActions .loading").show();
        },
        success:function(data) {
            data = jQuery.parseJSON(data);
            selection = data.result.map((x) => {
                return {id: x};
            });
            $("#checkActions .loading").hide();
            update_selection_content();
        },
        error:function(XMLHttpRequest, textStatus, errorThrows) {
            $("#checkActions .loading").hide();
        }
    });
}

function update_user_username() {
    let pop_in_container = $('.UserListPopInContainer');
    let ajax_data = {
        pwg_token: pwg_token,
        user_id: last_user_id
    };
    ajax_data['username'] = pop_in_container.find('.user-property-input-username').val();
    if (ajax_data.username.replace(/\s/g, '').length == 0) {
        $(".update-user-fail").html(fieldNotEmpty).fadeIn().delay(1500).fadeOut(2500);
        return
    }
    jQuery.ajax({
        url: "ws.php?format=json&method=pwg.users.setInfo",
        type: "POST",
        data: ajax_data,
        success: (raw_data) => {
            data = jQuery.parseJSON(raw_data);
            if (data.stat == 'ok') {
                if (last_user_index != -1) {
                    current_users[last_user_index].username = data.result.users[0].username;
                    $('#UserList .user-property-username .edit-username-title').html(current_users[last_user_index].username);
                    $('#UserList .user-property-initials span').html(get_initials(current_users[last_user_index].username));
                    fill_container_user_info($('#user-table-content .user-container').eq(last_user_index), last_user_index);
                }
                $("#UserList .update-user-success").fadeIn().delay(1500).fadeOut(2500);
                $('.user-property-username').show();
                $('.user-property-username-change').hide();
            }
        }
    })
}

function update_user_password() {
    let pop_in_container = $('.UserListPopInContainer');
    let ajax_data = {
        pwg_token: pwg_token,
        user_id: last_user_id
    };
    ajax_data['password'] = pop_in_container.find('.user-property-input-password').val();
    jQuery.ajax({
        url: "ws.php?format=json&method=pwg.users.setInfo",
        type: "POST",
        data: ajax_data,
        success: (raw_data) => {
            data = jQuery.parseJSON(raw_data);
            if (data.stat == 'ok') {
                $("#UserList .update-user-success").fadeIn().delay(1500).fadeOut(2500);
                $('.user-property-password').show();
                $('.user-property-password-change').hide();
            }
        }
    })
}

function update_user_info() {

    //Show spinner
    $(".update-user-button i").removeClass("icon-floppy").addClass("icon-spin6 animate-spin");
    $(".update-user-button").addClass("unclickable");
    let pop_in_container = $('.UserListPopInContainer');
    let ajax_data = {
        pwg_token: pwg_token,
        user_id: last_user_id
    };

    ajax_data = fill_ajax_data_from_container(ajax_data, pop_in_container);
    jQuery.ajax({
        url: "ws.php?format=json&method=pwg.users.setInfo",
        type: "POST",
        data: ajax_data,
        beforeSend: function() {
            $("#UserList .update-user-fail").fadeOut();
            $("#UserList .update-user-success").fadeOut();
        },
        success: function(raw_data) {
            data = jQuery.parseJSON(raw_data);
            if (data.stat === 'ok') {
                let result_user = data.result.users[0];
                if (last_user_index != -1) {
                    current_users[last_user_index].email = result_user.email;
                    current_users[last_user_index].enabled_high = result_user.enabled_high;
                    current_users[last_user_index].expand = result_user.expand;
                    current_users[last_user_index].groups = result_user.groups;
                    current_users[last_user_index].language = result_user.language;
                    current_users[last_user_index].level = result_user.level;
                    current_users[last_user_index].nb_image_page = result_user.nb_image_page;
                    current_users[last_user_index].recent_period = result_user.recent_period;
                    current_users[last_user_index].show_nb_comments = result_user.show_nb_comments;
                    current_users[last_user_index].show_nb_hits = result_user.show_nb_hits;
                    current_users[last_user_index].status = result_user.status;
                    current_users[last_user_index].theme = result_user.theme;
                    fill_container_user_info($('#user-table-content .user-container').eq(last_user_index), last_user_index);
                }
                $("#UserList .update-user-success").fadeIn().delay(1500).fadeOut(2500);

                //Hide spinner
                $(".update-user-button i").removeClass("icon-spin6 animate-spin").addClass("icon-floppy");
                $(".update-user-button").removeClass("unclickable");

            } else if (data.stat === 'fail') {
                $("#UserList .update-user-fail").html(data.message);
                $("#UserList .update-user-fail").fadeIn();
            }
        }
    });
}

function get_guest_info() {
    jQuery.ajax({
        url: "ws.php?format=json&method=pwg.users.getList",
        type: "POST",
        data: {
            display: "all",
            user_id: guest_id
        },
        success: (raw_data) => {
            data = jQuery.parseJSON(raw_data);
            if (data.stat == 'ok') {
                guest_user = data.result.users[0];
                fill_guest_edit();
            }
        }
    });
}

function get_user_info(uid, callback=None) {
    jQuery.ajax({
        url: "ws.php?format=json&method=pwg.users.getList",
        type: "POST",
        data: {
            display: "all",
            user_id: uid
        },
        success: (raw_data) => {
            data = jQuery.parseJSON(raw_data);
            if (data.stat == 'ok') {
                let result_user = data.result.users[0];
                fill_user_edit(result_user);
                callback();
            }
        }
    });
}

function update_guest_info() {
    //Show spinner
    $(".update-user-button i").removeClass("icon-floppy").addClass("icon-spin6 animate-spin");
    $(".update-user-button").addClass("unclickable");

    let pop_in_container = $('.GuestUserListPopInContainer');
    let ajax_data = {
        pwg_token: pwg_token,
        user_id: guest_id
    };
    ajax_data = fill_ajax_data_from_container(ajax_data, pop_in_container);
    ajax_data.email = undefined;
    ajax_data.status = undefined;
    jQuery.ajax({
        url: "ws.php?format=json&method=pwg.users.setInfo",
        type: "POST",
        data: ajax_data,
        success: function(raw_data) {
            data = jQuery.parseJSON(raw_data);
            if (data.stat == 'ok') {
                $("#GuestUserList .update-user-success").fadeIn().delay(1500).fadeOut(2500);
            }
             //Hide spinner
            $(".update-user-button i").removeClass("icon-spin6 animate-spin").addClass("icon-floppy");
            $(".update-user-button").removeClass("unclickable");
        }
    });
}

function update_user_list() {
    let update_data = {
        display: "all",
        order: "id DESC", // We want the most recent user first
        page: actual_page - 1,
        per_page: per_page,
        exclude: [guest_id]
    }
    if ($("#user_search").val().length != 0) {
      update_data["filter"] = $("#user_search").val();
    }
    if ($(".advanced-filter").hasClass('advanced-filter-open')) {
        update_data["status"] = $(".advanced-filter-select[name=filter_status]").val();
        update_data["group_id"] = $(".advanced-filter-select[name=filter_group]").val();
        update_data["min_level"] = $(".advanced-filter-select[name=filter_level]").val();
        update_data["max_level"] = $(".advanced-filter-select[name=filter_level]").val();
        update_data["min_register"] = register_dates[$(".dates-select-bar .slider-bar-container").slider("option", "values")[0]];
        update_data["max_register"] = register_dates[$(".dates-select-bar .slider-bar-container").slider("option", "values")[1]];
    }
    jQuery.ajax({
        url: "ws.php?format=json&method=pwg.users.getList",
        type: "POST",
        data: update_data,
        beforeSend: function () {
            $(".user-update-spinner").show();
        },
        success: function (raw_data) {
            data = jQuery.parseJSON(raw_data);
            if (data.stat === "fail") {
                console.log(data.message);
                return;
            }
            total_users = data.result.total_count;
            if (first_update) {
                $("h1").append(`<span class='badge-number'>${total_users}</span>`);
                first_update = false;
            }
            nb_filtered_users = data.result.total_count;
            update_pagination_menu();
            current_users = data.result.users;
            generate_user_list();
            $(".user-col.user-first-col.user-container-edit").click(function () {
                let uid_index = $(this).closest('.user-container').attr('key');
                last_user_id = current_users[uid_index].id;
                last_user_index = uid_index;
                fill_user_edit(current_users[uid_index]);
                $("#UserList").fadeIn();
            });
            set_selected_to_selection();

            $(".user-update-spinner").hide();

            let nb_filters = 0;
            ($(".advanced-filter-select[name=filter_status]").val() != "") ? nb_filters += 1 : false;
            ($(".advanced-filter-select[name=filter_group]").val() != "") ? nb_filters += 1 : false;
            ($(".advanced-filter-select[name=filter_level]").val() != "") ? nb_filters += 1 : false;
            ($(".dates-select-bar .slider-bar-container").slider("option", "values")[0] != 0) ? nb_filters += 1 : false;
            ($(".dates-select-bar .slider-bar-container").slider("option", "values")[1] != register_dates.length -1) ? nb_filters += 1 : false;
        
            show_filter_infos(nb_filters);
        },
        error: (raw_data) => {
            $(".user-update-spinner").hide();
        }
    });
}

function add_user() {
    let ajax_data = {
        pwg_token: pwg_token,
    }
    ajax_data.username = $('.AddUserLabelUsername .user-property-input').val();
    ajax_data.password = $('#AddUserPassword').val();
    ajax_data.email = $(".AddUserLabelEmail .user-property-input").val();
    ajax_data.send_password_by_mail = $('.user-list-checkbox[name="send_by_email"]').attr("data-selected") == "1" ? true : false;
    jQuery.ajax({
        url: "ws.php?format=json&method=pwg.users.add",
        type:"POST",
        data: ajax_data,
        beforeSend: function() {
            $("#AddUser .AddUserErrors").css("visibility", "hidden");
            if ($(".AddUserLabelUsername .user-property-input").val() == "") {
                $("#AddUser .AddUserErrors").html(missingUsername);
                $("#AddUser .AddUserErrors").css("visibility", "visible");
                return false;
            }
        },
        success: (raw_data) => {
            let data = jQuery.parseJSON(raw_data);
            if (data.stat == 'ok') {
                let new_user_id = data.result.users[0].id;
                update_user_list();
                add_user_close();
                $("#AddUser .user-property-input").val("");
                $("#AddUserSuccess .edit-now").unbind("click").click(() => {
                    last_user_id = new_user_id;
                    last_user_index = get_container_index_from_uid(new_user_id);
                    if (last_user_index != -1) {
                        fill_user_edit(current_users[last_user_index]);
                        open_user_list();
                    } else {
                        get_user_info(new_user_id, open_user_list);
                    }
                })
                $("#AddUserSuccess label span:first").html(user_added_str.replace("%s", ajax_data.username));
                $("#AddUserSuccess").css("display", "flex");
            }
            else {
                $("#AddUser .AddUserErrors").html(data.message)
                $("#AddUser .AddUserErrors").css("visibility", "visible");
            }
        }
    });
}

function delete_user(uid) {
    jQuery.ajax({
        url: "ws.php?format=json&method=pwg.users.delete",
        type:"POST",
        data: {
            user_id:uid,
            pwg_token:pwg_token
        },
        beforeSend: function() {
            //jQuery('#user'+uid+' .userDelete .loading').show();
        },
        success:function(data) {
            close_user_list();
            update_user_list();
            // msg where user was deleted
            //jQuery('#showAddUser .infos').html('&#x2714; User '+username+' deleted').show();
        },
        error:function(XMLHttpRequest, textStatus, errorThrows) {
            //error just hide loading
            //jQuery('#user'+uid+' .userDelete .loading').hide();
        }
    })
}

function show_filter_infos(nb_filters) {
  if ($("#user_search").val().length != 0 || nb_filters != 0) {
    if (total_users != "1") {
      $(".filtered-users").html(filtered_users.replace(/%d/g, total_users));
    } else {
      $(".filtered-users").html(filtered_user.replace(/%d/g, total_users));
    }
  } else {
    $(".filtered-users").html("");
  }
  
  if (nb_filters != 0) {
    $(".advanced-filter-btn").css({
      width: "80px",
    });
    $(".filter-counter").html(nb_filters).css('display', 'flex');
  } else {
    $(".advanced-filter-btn").css({
      width: "70px",
    });
    $(".filter-counter").css('display', 'none').html(0);
  }
}
