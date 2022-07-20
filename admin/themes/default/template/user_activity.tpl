{include file='include/colorbox.inc.tpl'}
{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}

{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}
{combine_css path="admin/themes/default/fontello/css/animation.css" order=10} {* order 10 is required, see issue 1080 *}
{footer_script}
{* <!-- USERS --> *}
var usersCache = new UsersCache({
  serverKey: '{$CACHE_KEYS.users}',
  serverId: '{$CACHE_KEYS._hash}',
  rootUrl: '{$ROOT_URL}'
});

const color_icons = ["icon-red", "icon-blue", "icon-yellow", "icon-purple", "icon-green"];
var activity_page = 1;
let actual_page = 1;
let max_page = 1;
let uid_filter;
const page_ellipsis = '<span>...</span>'
const page_item = '<a data-page="%d">%d</a>';
var create_selecter = true;
const users_key = "{"Users"|@translate}";

const line_key = "{'%s line'|translate}";
const lines_key = "{'%s lines'|translate}";

{*<-- Translation keys -->*}

var actionType_add = "{'add'|translate}";
var actionType_delete = "{'deletion'|translate}";
var actionType_move = "{'move'|translate}";
var actionType_edit = "{'edit'|translate}";
var actionType_login = "{'login'|translate}";
var actionType_logout = "{'logout'|translate}";

{* Album keys *}

var actionInfos_album_added = "{'%d album added'|translate}";
var actionInfos_album_deleted = "{'%d album deleted'|translate}";
var actionInfos_album_edited = "{'%d album edited'|translate}";
var actionInfos_album_moved = "{'%d album moved'|translate}";

var actionInfos_albums_added = "{'%d albums added'|translate}";
var actionInfos_albums_deleted = "{'%d albums deleted'|translate}";
var actionInfos_albums_edited = "{'%d albums edited'|translate}";
var actionInfos_albums_moved = "{'%d albums moved'|translate}";

{* User keys *}

var actionInfos_user_added = "{'%d user added'|translate}";
var actionInfos_user_deleted = "{'%d user deleted'|translate}";
var actionInfos_user_edited = "{'%d user edited'|translate}";
var actionInfos_user_logged_in = "{'%d user logged in'|translate}";
var actionInfos_user_logged_out = "{'%d user logged out'|translate}";

var actionInfos_users_added = "{'%d users added'|translate}";
var actionInfos_users_deleted = "{'%d users deleted'|translate}";
var actionInfos_users_edited = "{'%d users edited'|translate}";
var actionInfos_users_logged_in = "{'%d users logged in'|translate}";
var actionInfos_users_logged_out = "{'%d users logged out'|translate}";

{* Photo keys *}

var actionInfos_photo_added = "{'%d photo added'|translate}";
var actionInfos_photo_deleted = "{'%d photo deleted'|translate}";
var actionInfos_photo_edited = "{'%d photo edited'|translate}";
var actionInfos_photo_moved = "{'%d photo moved'|translate}";

var actionInfos_photos_added = "{'%d photos added'|translate}";
var actionInfos_photos_deleted = "{'%d photos deleted'|translate}";
var actionInfos_photos_edited = "{'%d photos edited'|translate}";
var actionInfos_photos_moved = "{'%d photos moved'|translate}";

{* Group keys *}

var actionInfos_group_added = "{'%d group added'|translate}";
var actionInfos_group_deleted = "{'%d group deleted'|translate}";
var actionInfos_group_edited = "{'%d group edited'|translate}";
var actionInfos_group_moved = "{'%d group moved'|translate}";

var actionInfos_groups_added = "{'%d groups added'|translate}";
var actionInfos_groups_deleted = "{'%d groups deleted'|translate}";
var actionInfos_groups_edited = "{'%d groups edited'|translate}";
var actionInfos_groups_moved = "{'%d groups moved'|translate}";

{* Tags keys *}

var actionInfos_tag_added = "{'%d tag added'|translate}";
var actionInfos_tag_deleted = "{'%d tag deleted'|translate}";
var actionInfos_tag_edited = "{'%d tag edited'|translate}";
var actionInfos_tag_moved = "{'%d tag moved'|translate}";

var actionInfos_tags_added = "{'%d tags added'|translate}";
var actionInfos_tags_deleted = "{'%d tags deleted'|translate}";
var actionInfos_tags_edited = "{'%d tags edited'|translate}";
var actionInfos_tags_moved = "{'%d tags moved'|translate}";

{*<-- Getting and Displaying Activities -->*}

get_user_activity(activity_page, uid_filter);

function get_user_activity(page, uid) {
    $.ajax({
        url: "ws.php?format=json&method=pwg.activity.getList",
        type: "POST",
        dataType: "json",
        data: {
            page: page - 1,
            uid: uid,
        },
        beforeSend: () => {
          $('.tab').contents(':not(#-1):not(.loading)').remove();
          $(".loading").show();
          $('.pagination-arrow.rigth').addClass('unavailable');
          $('.pagination-arrow.left').addClass('unavailable');
          $(".pagination-item-container").hide();
          $(".user-update-spinner").addClass("icon-spin6");
        },
        success: (data) => {
            /* console log to help debug */
            {* console.log(data); *}
            uid_filter = uid;

            setCreationDate(data.result['result_lines'][data.result['result_lines'].length-1].date, data.result['result_lines'][0].date);
            $(".loading").hide();

            data.result['result_lines'].forEach(line => {
                lineConstructor(line);
            });

            max_page = data.result['max_page'];
            $(".user-update-spinner").removeClass("icon-spin6");
            $(".pagination-item-container").show();
            update_pagination_menu();
        }, 
        error: (e) => {
            console.log("ajax call failed");
            console.log(e);
        }
    })
}

function lineConstructor(line) {
    let newLine = $("#-1").clone();

    newLine.removeClass("hide");

    /* console log to help debug */
    {* console.log(line); *}
    newLine.attr("id", line.id);

    var final_albumInfos;

    {* Determines wich string need to be placed in the line constructed *}

    if (line.counter > 1) {
        // pluriel
        switch (line.action) {
            case "edit":
            newLine.find(".action-type").addClass("icon-blue");
            newLine.find(".user-pic").addClass(color_icons[line.user_id % 5]);
            newLine.find(".action-icon").addClass("icon-pencil");

            newLine.find(".action-name").html(actionType_edit);
                switch (line.object) {
                    case "user":
                    final_albumInfos = actionInfos_users_edited.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-user-1");
    
                    break;
                    case "album":
                    final_albumInfos = actionInfos_albums_edited.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-folder-open");

                    break;
                    case "group":
                    final_albumInfos = actionInfos_groups_edited.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-users-1");

                    break;
                    case "photo":
                    final_albumInfos = actionInfos_photos_edited.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-picture");

                    break;
                    case "tag":
                    final_albumInfos = actionInfos_tags_edited.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-tags");

                    break;
                    default:
                     final_albumInfos = line.counter + " " +line.object + " " + line.action;
                    break;
                }
                
            break;

            case "add":
            newLine.find(".action-type").addClass("icon-green");
            newLine.find(".user-pic").addClass(color_icons[line.user_id % 5]);
            newLine.find(".action-icon").addClass("icon-plus");

            newLine.find(".action-name").html(actionType_add);
                switch (line.object) {
                    case "user":
                    final_albumInfos = actionInfos_users_added.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-user-1");

                    break;
                    case "album":
                    final_albumInfos = actionInfos_albums_added.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-folder-open");

                    break;
                    case "group":
                    final_albumInfos = actionInfos_groups_added.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-users-1");

                    break;
                    case "photo":
                    final_albumInfos = actionInfos_photos_added.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-picture");

                    break;
                    case "tag":
                    final_albumInfos = actionInfos_tags_added.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-tags");

                    break;
                    default:
                     final_albumInfos = line.counter + " " +line.object + " " + line.action;
                    break;
                }

            break;

            case "delete":
            newLine.find(".action-type").addClass("icon-red");
            newLine.find(".user-pic").addClass(color_icons[line.user_id % 5]);
            newLine.find(".action-icon").addClass("icon-trash-1");

            newLine.find(".action-name").html(actionType_delete);
                switch (line.object) {
                    case "user":
                    final_albumInfos = actionInfos_users_deleted.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-user-1");

                    break;
                    case "album":
                    final_albumInfos = actionInfos_albums_deleted.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-folder-open");

                    break;
                    case "group":
                    final_albumInfos = actionInfos_groups_deleted.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-users-1");

                    break;
                    case "photo":
                    final_albumInfos = actionInfos_photos_deleted.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-picture");

                    break;
                    case "tag":
                    final_albumInfos = actionInfos_tags_deleted.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-tags");

                    break;
                    default:
                     final_albumInfos = line.counter + " " +line.object + " " + line.action;
                    break;
                }

            break;

            case "move":
            newLine.find(".action-type").addClass("icon-yellow");
            newLine.find(".user-pic").addClass(color_icons[line.user_id % 5]);
            newLine.find(".action-icon").addClass("icon-move");

            newLine.find(".action-name").html(actionType_move);
                switch (line.object) {
                    case "album":
                    final_albumInfos = actionInfos_albums_moved.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-folder-open");

                    break;
                    case "group":
                    final_albumInfos = actionInfos_groups_moved.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-users-1");

                    break;
                    case "photo":
                    final_albumInfos = actionInfos_photos_moved.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-picture");

                    break;
                    case "tag":
                    final_albumInfos = actionInfos_tags_moved.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-tags");

                    break;
                    default:
                     final_albumInfos = line.counter + " " +line.object + " " + line.action;
                    break;
                }

            break;

            case "login":
            newLine.find(".action-type").addClass("icon-purple");
            newLine.find(".user-pic").addClass(color_icons[line.user_id % 5]);
            newLine.find(".action-icon").addClass("icon-key");
            newLine.find(".action-section").addClass("icon-user-1");

            newLine.find(".action-name").html(actionType_login);

            final_albumInfos = actionInfos_users_logged_in.replace('%d', line.counter);

            break;

            case "logout":
            newLine.find(".action-type").addClass("icon-purple");
            if (line.user_id != 2) {
              newLine.find(".user-pic").addClass(color_icons[line.user_id % 5]);
            } else {
              newLine.find(".user-pic").addClass(color_icons[line.object_id[0] % 5]);
            }
            newLine.find(".action-icon").addClass("icon-logout");
            newLine.find(".action-section").addClass("icon-user-1");

            newLine.find(".action-name").html(actionType_logout);

            final_albumInfos = actionInfos_users_logged_out.replace('%d', line.counter);

            break;

            default:
            newLine.find(".action-type").addClass("icon-purple");
            newLine.find(".user-pic").addClass(color_icons[line.user_id % 5]);
            break;
        }
    } else {
        // singulier
        switch (line.action) {
            case "edit":
            newLine.find(".action-type").addClass("icon-blue");
            newLine.find(".user-pic").addClass(color_icons[line.user_id % 5]);
            newLine.find(".action-icon").addClass("icon-pencil");

            newLine.find(".action-name").html(actionType_edit);
                switch (line.object) {
                    case "user":
                    final_albumInfos = actionInfos_user_edited.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-user-1");

                    break;
                    case "album":
                    final_albumInfos = actionInfos_album_edited.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-folder-open");

                    break;
                    case "group":
                    final_albumInfos = actionInfos_group_edited.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-users-1");

                    break;
                    case "photo":
                    final_albumInfos = actionInfos_photo_edited.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-picture");

                    break;
                    case "tag":
                    final_albumInfos = actionInfos_tag_edited.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-tags");

                    break;
                    default:
                     final_albumInfos = line.counter + " " +line.object + " " + line.action;
                    break;
                }

            
            break;
            case "add":
            newLine.find(".action-type").addClass("icon-green");
            newLine.find(".user-pic").addClass(color_icons[line.user_id % 5]);
            newLine.find(".action-icon").addClass("icon-plus");

            newLine.find(".action-name").html(actionType_add);
                switch (line.object) {
                    case "user":
                    final_albumInfos = actionInfos_user_added.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-user-1");

                    break;
                    case "album":
                    final_albumInfos = actionInfos_album_added.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-folder-open");

                    break;
                    case "group":
                    final_albumInfos = actionInfos_group_added.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-users-1");

                    break;
                    case "photo":
                    final_albumInfos = actionInfos_photo_added.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-picture");

                    break;
                    case "tag":
                    final_albumInfos = actionInfos_tag_added.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-tags");

                    break;
                    default:
                     final_albumInfos = line.counter + " " +line.object + " " + line.action;

                    break;
                }

            break;
            case "delete":
            newLine.find(".action-type").addClass("icon-red");
            newLine.find(".user-pic").addClass(color_icons[line.user_id % 5]);
            newLine.find(".action-icon").addClass("icon-trash-1");

            newLine.find(".action-name").html(actionType_delete);
                switch (line.object) {
                    case "user":
                    final_albumInfos = actionInfos_user_deleted.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-user-1");

                    break;
                    case "album":
                    final_albumInfos = actionInfos_album_deleted.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-folder-open");

                    break;
                    case "group":
                    final_albumInfos = actionInfos_group_deleted.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-users-1");

                    break;
                    case "photo":
                    final_albumInfos = actionInfos_photo_deleted.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-picture");

                    break;
                    case "tag":
                    final_albumInfos = actionInfos_tag_deleted.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-tags");

                    break;
                    default:
                     final_albumInfos = line.counter + " " +line.object + " " + line.action;
                    break;
                }

            break;
            case "move":
            newLine.find(".action-type").addClass("icon-yellow");
            newLine.find(".user-pic").addClass(color_icons[line.user_id % 5]);
            newLine.find(".action-icon").addClass("icon-move");

            newLine.find(".action-name").html(actionType_move);
                switch (line.object) {
                    case "album":
                    final_albumInfos = actionInfos_album_moved.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-folder-open");

                    break;
                    case "group":
                    final_albumInfos = actionInfos_group_moved.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-users-1");

                    break;
                    case "photo":
                    final_albumInfos = actionInfos_photo_moved.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-picture");

                    break;
                    case "tag":
                    final_albumInfos = actionInfos_tag_moved.replace('%d', line.counter);
                    newLine.find(".action-section").addClass("icon-tags");

                    break;
                    default:
                     final_albumInfos = line.counter + " " +line.object + " " + line.action;
                    break;
                }

            break;
            case "login":
            newLine.find(".action-type").addClass("icon-purple");
            newLine.find(".user-pic").addClass(color_icons[line.user_id % 5]);
            newLine.find(".action-icon").addClass("icon-key");
            newLine.find(".action-section").addClass("icon-user-1");

            newLine.find(".action-name").html(actionType_login);

            final_albumInfos = actionInfos_user_logged_in.replace('%d', line.counter);

            break;
            case "logout":
            newLine.find(".action-type").addClass("icon-purple");
            if (line.user_id != 2) {
              newLine.find(".user-pic").addClass(color_icons[line.user_id % 5]);
            } else {
              newLine.find(".user-pic").addClass(color_icons[line.object_id[0] % 5]);
            }
            newLine.find(".action-icon").addClass("icon-logout");
            newLine.find(".action-section").addClass("icon-user-1");

            newLine.find(".action-name").html(actionType_logout);
          
            final_albumInfos = actionInfos_user_logged_out.replace('%d', line.counter);

            break;

            default:
            newLine.find(".action-type").addClass("icon-purple");
            newLine.find(".user-pic").addClass(color_icons[line.user_id % 5]);
            break;
        }
    }

    newLine.find(".action-infos-test").html(final_albumInfos);

    /* Action_section */
    newLine.find(".nb_items").html(line.counter);
    
    /* Date_section */
    newLine.find(".date-day").html(line.date);
    newLine.find(".date-hour").html(line.hour);

    /* User _Section */
    newLine.find(".user-name").html(line.username);
    newLine.find(".user-pic").html(get_initials(line.username));

    /* Detail_section */
    newLine.find(".detail-item-1").html(line.ip_address);
    newLine.find(".detail-item-1").attr("title", "IP");

    if (line.detailsType == "script") {
        newLine.find(".detail-item-2").html(line.details.script);
        newLine.find(".detail-item-2").attr('title', 'Script');
    } else if (line.detailsType == "method") {
        newLine.find(".detail-item-2").html(line.details.method);
        newLine.find(".detail-item-2").attr('title', 'API Method');
    }   
    
    if (line.details.agent) {
        newLine.find(".detail-item-3").html(line.details.agent);
        newLine.find(".detail-item-3").attr('title', line.details.agent);
    } else if (line.details.users_string && line.action != "logout" && line.action != "login") {
        newLine.find(".detail-item-3").html(line.details.users_string);
        newLine.find(".detail-item-3").attr('title', users_key + ": " +line.details.users_string);
    } else {
        newLine.find(".detail-item-3").remove();
    }

    newLine.addClass("uid-" + line.user_id);

    displayLine(newLine);
}

function displayLine(line) {
    $(".tab").append(line);
}

function get_initials(username) {
    let words = username.toUpperCase().split(" ");
    let res = words[0][0];

    if (words.length > 1 && words[1][0] !== undefined ) {
        res += words[1][0];
    }
    return res;
}

function setCreationDate(startDate, endDate) {
    $(".start-date").html(startDate)

    $(".end-date").html(endDate)
}

{* Pagination *}

function move_to_page(page) {
    if (page < 0 || page > max_page)
        return;
    actual_page = page;
    update_pagination_menu();
    get_user_activity(page, uid_filter);
}

$('.pagination-arrow.rigth').on('click', () => {
    move_to_page(actual_page + 1);
})

$('.pagination-arrow.left').on('click', () => {
    move_to_page(actual_page - 1);
})

function update_pagination_menu() {
    {* max_page = Math.ceil(nb_filtered_users / per_page); *}
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


$(document).ready(function () {
    $("h1").append(`<span class='badge-number'>`+{$nb_users - 1}+`</span>`);

    $('select').on('change', function (user) {
        if ($(".selectize-input").hasClass("full")) {
          {* call ajax sur activity list avec uid en param *}
          get_user_activity(1, $(".selectize-input .item").data("value"));
        }
    });

    jQuery('.user-selecter').selectize();
    jQuery(".user-selecter")[0].selectize.setValue(null);

    jQuery(".cancel-icon").click(function() {
      jQuery(".user-selecter")[0].selectize.clear(true);
      $(".line").css('display', 'flex');
    });
});

{/footer_script}

<div class="container"> 

    <div class="activity-header">
        <div class="select-user">
            <span class="select-user-title"> {'Selected user'|translate} </span>
            
            <select class="user-selecter" placeholder="{'none'|translate}" single style="width:250px; height: 10px;">
            {foreach from=$ulist item=$user}
            <option value="{$user.id}"> <span class='username_filter'>{$user.username}</span><span class='nb_lines_str'> ({if $user.nb_lines == 1}{'%d Activity'|translate:$user.nb_lines}{else}{'%d Activities'|translate:$user.nb_lines}{/if}) </span></option>
            {/foreach}
            </select>
            
            <span class="icon-cancel cancel-icon"> </span>
        </div>
        <div class="acivity-time">
            <span class="acivity-time-text"> {'Activity time from'|translate}</span>
            <span class="start-date">
                <span class="icon-spin6 animate-spin"></span>
            </span>
            <span class="acivity-time-text"> {'to'|translate}</span>
            <span class="end-date">
                <span class="icon-spin6 animate-spin"></span>
            </span>
        </div>
        <a class="download_csv tiptip" title="{'Download all activities'|translate}" href="admin.php?page=user_activity&type=download_logs"> 
            <i class="icon-download"> </i>
        </a>
    </div>
{if max_page != 1}
  <div class="pagination-container">
      <div class="pagination-arrow left">
        <span class="icon-left-open"></span>
      </div>
      <div class="pagination-item-container">
      </div>
      <div class="user-update-spinner icon-spin6 animate-spin"></div>
      <div class="pagination-arrow rigth">
        <span class="icon-left-open"></span>
      </div>
    </div>
{/if}
    

    <div class="tab-title">
        <div class="action-title">
            {'Action'|translate}
        </div>

        <div class="date-title">
            {'Date'|translate}
        </div>

        <div class="user-title">
            {'User'|translate}
        </div>

        <div class="detail-title">
            {'Details'|translate}
        </div>
    </div>


    <div class="tab">
    <div class="loading"> 
        <span class="icon-spin6 animate-spin"> </span>
    </div>
        <div class="line hide" id="-1">
            <div class="action-section">
                <div class="action-type">
                    <span class="action-icon"></span>
                    <span class="action-name"> Edit </span>
                </div>
                <div class="action-infos">
                    <span class="action-infos-test"> T </span>
                </div>
            </div>

            <div class="date-section">
                <span class="icon-clock"> </span>
                <span class="date-day">1 Janvier 1970</span>
                <span class="date-hour">a 00:00</span>
            </div> 

            <div class="user-section">
                <div class="user-pic">
                </div>
                <div class="user-name">
                    Username
                </div>
            </div>

            <div class="detail-section">
                <div class="detail-item detail-item-1">
                    detail 1
                </div>
                <div class=" detail-item detail-item-2">
                    detail 2
                </div>
                <div class="detail-item detail-item-3">
                    detail 3
                </div>
            </div>
        </div>
    </div>
</div>

<style>

.container {
    padding: 0 30px;
}

.container,
.tab {
    display: flex;
    flex-direction: column;
}

.tab-title {
    display: flex;
    flex-direction: row;
}

.hide {
    display: none !important;
}

.tab-title div {
    text-align: left;
    font-size: 1.1em;
    font-weight: bold;

    margin: 0 20px 10px 0px;

    color: #9e9e9e;

    padding-bottom: 5px;
}

.tab-title div:first-child {
    margin: 0 0 10px 35px;
}

.tab-title .action-title, 
.line .action-section {
    min-width: 320px;
    max-width: 340px;
}
.tab-title .action-title {
    min-width: 298px !important;
}

.tab-title .date-title, 
.line .date-section {
    min-width: 280px;
    max-width: 300px;
}

.tab-title .user-title, 
.line .user-section {
    min-width: 200px;
    max-width: 250px;
}


.line .action-section,
.line .date-section,
.line .user-section,
.tab-title .action-title,
.tab-title .date-title,
.tab-title .user-title {
    text-align: left;
    {* width: 22%; *}
}

.line .action-section,
.line .date-section,
.line .user-section {
    margin: 0 20px 0 0;
}

.line .detail-section,
.tab-title .detail-title {
    display: flex;
    flex-grow: 1;
    margin-right: 0;
}

.action-section {
    display: flex;
    flex-direction: row;
    align-items: center;
}

.action-type {
    margin: 0 10px 0 15px;
    padding: 3px 10px;
    border-radius: 20px;

    white-space: nowrap;
}

.action-infos {
    display: flex;
    flex-direction: row;
}

.action-infos span {
    margin-right: 5px;
}

.date-section .date-day {
    font-weight: bold;
}

.user-section {
    display: flex;
    flex-direction: row;
    align-items: center;
}

.user-section .user-pic {
    width: 30px;
    height: 30px;

    min-width: 30px;

    border-radius: 50%;

    margin-right: 10px;

    display: flex;

    justify-content: center;
    align-items: center;

    font-weight: 600;
    font-size: 17px;
}

.user-section .user-name {
    font-weight: bold;
}

/* Activity Header */

.activity-header {
    display: flex;
    flex-direction: row;

    align-items: center;

    height: 100px;
    width: 100%;
}

.select-user span {
    font-size: 15px;
    font-weight: bold;

    margin-right: 20px;
}

.acivity-time {
    margin: 0 25px;
}

.user-selecter {
    width: 150px;
}


/* Selectize */
.selectize-control.single.user-selecter {
    height: 30px;
}

.selectize-control.single .selectize-input {
    height: 30px;
    padding: 0 10px;

    display: flex;
    align-items: center;
    justify-content: left;
}

.selectize-input {
    text-align: left;
}

.selectize-control.single .selectize-input input{
    height: 30px;
}

.selectize-dropdown {
    text-align: left;
}

.cancel-icon {
    margin: 0 0 0 10px !important;

    cursor: pointer;
}

.loading {
    font-size: 25px;
}

.action-section::before {
    margin: 0 -5px 0 10px;
    opacity: 0.6;
}
</style>