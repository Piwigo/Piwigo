//{*<-- Getting and Displaying Activities -->*}

if (additional_filt_type)
{
    object_filter = additional_filt_type;
}

get_user_activity(activity_page, uid_filter, action_filter, object_filter, [date_min_filter, date_max_filter], additional_filt_value);

function get_user_activity(page, uid, action, object, date, id) {

    $.ajax({
        url: "ws.php?format=json&method=pwg.activity.getList",
        type: "POST",
        dataType: "json",
        data: {
            page: page - 1,
            uid: uid,
            action : action,
            object : object,
            offset : page_offsets[page - 1],
            date_min : date[0],
            date_max : date[1],
            id : additional_filt_value
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
            /* console log to help debug
            {* console.log(data); *}*/
            uid_filter = uid;
            action_filter = action;
            object_filter = object;
            date_min_filter = date[0];
            date_max_filter = date[1];

            //setCreationDate(data.result['result_lines'][data.result['result_lines'].length-1].date, data.result['result_lines'][0].date);
            $(".loading").hide();
            
            if (data.result['result_lines'].length > 0)
            {
                data.result['result_lines'].forEach(line => {
                    lineConstructor(line);
                });
            }
            else
            {
                emptyLine();
            }

            current_page_offset = page_offsets[page - 1];
            end_page = data.result['end_page'];
            if (!(page_offsets.includes(data.result['page_offset'])))
            {
                page_offsets.push(data.result['page_offset']);
            }
            
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

    $(".tab-title").show();
    $(".activity-noresult").hide();
    newLine.removeClass("hide");

    /* console log to help debug 
    {* console.log(line); *}*/
    newLine.attr("id", line.id);

    var final_albumInfos;

    //{* Determines wich string need to be placed in the line constructed *}

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
            newLine.find(".action-section").addClass("icon-user-1");
            newLine.find(".action-name").html(line.action);
            final_albumInfos = 'x' + line.counter;
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
            newLine.find(".action-section").addClass("icon-user-1");
            newLine.find(".action-name").html(line.action);
            final_albumInfos = 'x' + line.counter;
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
    newLine.find(".detail-item-1").attr("title", "IP: " + line.ip_address);

    if (line.detailsType == "script") {
        newLine.find(".detail-item-2").html(line.details.script);
        newLine.find(".detail-item-2").attr('title', 'Script');
    } else if (line.detailsType == "method") {
        newLine.find(".detail-item-2").html(line.details.method);
        newLine.find(".detail-item-2").attr('title', 'API Method');
    }   
    
    if (line.details.agent) {
        const api_key = line.details.connected_with ? 'API Key, ' : '';
        const details = line.details.connected_with ? '<i class="icon-key"></i>' + line.details.agent : line.details.agent; 
        newLine.find(".detail-item-3").html(details);
        newLine.find(".detail-item-3").attr('title', api_key + 'User-Agent: ' + line.details.agent);
    } else if (line.details.users && line.action != "logout" && line.action != "login") {
        const user_string = [...new Set(line.details.users)].toString();
        newLine.find(".detail-item-3").html(user_string);
        newLine.find(".detail-item-3").attr('title', users_key + ": " + user_string);
    } else {
        newLine.find(".detail-item-3").remove();
    }

    newLine.addClass("uid-" + line.user_id);

    displayLine(newLine);
}

function displayLine(line) {
    $(".tab").append(line);
}

function emptyLine() {
    $(".tab-title").hide();
    $(".activity-noresult").show();
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

//{* Pagination *}

function move_to_page(page) {
    if (page < 0)
        return;
    actual_page = page;
    update_pagination_menu(page);
    get_user_activity(page, uid_filter, action_filter, object_filter, [date_min_filter, date_max_filter], additional_filt_value);
}

$('.pagination-arrow.rigth').on('click', () => {
    move_to_page(actual_page + 1);
})

$('.pagination-arrow.left').on('click', () => {
    move_to_page(actual_page - 1);
})

function update_pagination_menu(page) {
    updateArrows();
    update_pagination_items();
    if (end_page && actual_page == 1) {
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
    if (end_page) {
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
    if (actual_page != 1) {
        append_pagination_item(actual_page)
    }
    if (!end_page) {
        append_pagination_item();
    }   
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

function page_reset(){
    activity_page = 1;
    current_page_offset = 0;
    page_offsets = [0];
    actual_page = 1;
    end_page = false;
}


$(document).ready(function () {
    $("h1").append(`<span class='badge-number'>`+ (nb_users - 1) +`</span>`);

    $('select.user-selecter').on('change', function (user) {
        if ($(".user-selecter .selectize-input").hasClass("full")) {
            page_reset();
            if ($(".user-selecter .selectize-input .item").data("value") == 'none')
            {
                //{* call ajax sur activity list sans uid *}
                get_user_activity(1, undefined, action_filter, object_filter, [date_min_filter, date_max_filter], additional_filt_value);
            }
            else
            {
                //{* call ajax sur activity list avec uid en param *}
                get_user_activity(1, $(".user-selecter .selectize-input .item").data("value"), action_filter, object_filter, [date_min_filter, date_max_filter], additional_filt_value);
            }
        }
    });

    $('select.action-selecter').on('change', function (user) {
        if ($(".action-selecter .selectize-input").hasClass("full")) {
            page_reset();
            if ($(".action-selecter .selectize-input .item").data("value") == 'none')
            {
                //{* call ajax sur activity list sans action et object *}
                if (additional_filt_type)
                {
                    get_user_activity(1, uid_filter, undefined, object_filter, [date_min_filter, date_max_filter], additional_filt_value);
                }
                else
                {
                    get_user_activity(1, uid_filter, undefined, undefined, [date_min_filter, date_max_filter], additional_filt_value);
                }
            }
            else
            {
                //{* call ajax sur activity list avec action et object en param *}
                object =  $(".action-selecter .selectize-input .item").data("value").split("/")[0];
                action =  $(".action-selecter .selectize-input .item").data("value").split("/")[1];
                get_user_activity(1, uid_filter, action, object, [date_min_filter, date_max_filter], additional_filt_value);
            }
        }
    });

    $('#date_min_activity').on('change', function(user) {
        page_reset();
        if ($('#date_min_activity').val()=='')
        {
            document.getElementById('date_max_activity').setAttribute("min", date_min);
        }
        else
        {
            document.getElementById('date_max_activity').setAttribute("min", $('#date_min_activity').val());
        }
        get_user_activity(activity_page, uid_filter, action_filter, object_filter, [$('#date_min_activity').val(), date_max_filter], additional_filt_value);
    })
    
    $('#date_max_activity').on('change', function(user) {
        page_reset();
        if ($('#date_max_activity').val()=='')
        {
            document.getElementById('date_min_activity').setAttribute("max", date_max);
        }
        else
        {
            document.getElementById('date_min_activity').setAttribute("max", $('#date_max_activity').val());
        }
        get_user_activity(activity_page, uid_filter, action_filter, object_filter, [date_min_filter, $('#date_max_activity').val()], additional_filt_value);
    })

    jQuery('.user-selecter').selectize();
    jQuery('.user-selecter')[0].selectize.setValue(null);

    jQuery('.action-selecter').selectize();
    jQuery('.action-selecter')[0].selectize.setValue(null);

    if (additional_filt_type)
    {
        $("#activityMoreFilters").addClass("extend-padding"); 
    }
    else
    {
        $("#activityMoreFiltersContent").hide();
    }
    //var used to prevent the user to interfere with the collapsible when it's toggling, to avoid some problems
    var toggleTriggered = false;
    $("#activityMoreFilters").on("click", function(){
        if ($("#activityMoreFiltersContent").css('display') == 'none' && toggleTriggered == false)
        {
            toggleTriggered = true;
            $("#activityMoreFilters").addClass("extend-padding");
            $("#activityMoreFiltersContent").slideToggle(function(){
                toggleTriggered = false;
            });
        }
        else if ($("#activityMoreFiltersContent").css('display') == 'flex' && toggleTriggered == false)
        {
            toggleTriggered = true;
            $("#activityMoreFiltersContent").slideToggle(function(){
                $("#activityMoreFilters").removeClass("extend-padding");
                toggleTriggered = false;
            });
        }
    })
});