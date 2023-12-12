const color_icons = ["icon-red", "icon-blue", "icon-yellow", "icon-purple", "icon-green"];

function line_constructor(line){
    let new_line = $('#body_example').clone();
    const line_details_example = $('#line_details_example').clone();
    const initial_user = line.username.charAt(0).toUpperCase();

    // id
    new_line.attr('id', line.id);

    // Display major information
    if (line.major_infos) {
        new_line.addClass('major-infos');
    }

    // Display object
    new_line.find('.icon_object').addClass(line.object_icon);
    new_line.find('.text_object').text(line.object).attr('title', line.object);

    // Display action
    new_line.find('.color_action').addClass(line.action_color);
    new_line.find('.icon_action').addClass(line.action_icon);
    new_line.find('.text_action').text(line.action).attr('title', line.action);

    // Display Username
    'System' == line.username
    ? new_line.find('.icon_user').addClass('icon-robot-head')
    : new_line.find('.icon_user').addClass(color_icons[line.user_id % 5]).html(initial_user);
    new_line.find('.text_username').text(line.username).attr('title', line.username);

    // Display date & hour
    new_line.find('.text_date').text(line.date).attr('title', line.date + ' ' + line.hour);
    new_line.find('.text_hour').text(line.hour);

    // Display Details
    switch(line.detail.type){
        case 'empty':
        default:
            // For empty CSS do it for us but he exist
            break;
        
        // Here is when we want to display only one element in details (and default)
        case 'error':
        case 'version':
        case 'maintenance_action':
            let new_line_detail = line_details_example.clone();
            new_line_detail.removeAttr('id');

            new_line_detail.find('.icon_details').addClass(line.detail.icon);
            new_line_detail.find('.text_details').text(line.detail.text).attr('title', line.detail.text);
            new_line.find('.tab-body-details').append(new_line_detail);
            break;

        // Here is when we want to display multiple elements one by one (Work if details can have one or multiple elements)
        case 'db_fs_version':
        case 'config_section':
            Object.keys(line.detail)
            .filter((key) => 'type' !== key)
            .forEach((key) => {
                let detail = line.detail[key];
                let new_line_details = line_details_example.clone();

                new_line_details.removeAttr('id');
                new_line_details.find('.icon_details').addClass(detail.icon);
                new_line_details.find('.text_details').text(detail.text).attr('title', detail.text);
                new_line.find('.tab-body-details').append(new_line_details);
            });
            break;

        // Here is when we need to specific the format for somes types
        // from_to
        case 'from_to':
            let from = line_details_example.clone();
            from.removeAttr('id');
            from.find('.icon_details').addClass(line.detail[0].icon);
            from.find('.text_details').text(line.detail[0].text).attr('title', line.detail[0].text);
            new_line.find('.tab-body-details').append(from);

            new_line.find('.tab-body-details').append('<span class="icon-right">  </span>');

            let to = line_details_example.clone();
            to.removeAttr('id');
            to.find('.icon_details').addClass(line.detail[1].icon);
            to.find('.text_details').text(line.detail[1].text).attr('title', line.detail[1].text);
            new_line.find('.tab-body-details').append(to);
            break;
    }

    $('#tab-body-content').append(new_line);
}

function get_system_activities(){
    $.ajax({
        url: window.location.href,
        type: 'GET',
        data: {
            method: 'pwg.activity_sys.getList'
        },
        dataType: 'json',
        success: (response) => {
            const lines = response.data;
            // help to debug
            // console.log(lines);
            $('.loading').hide();
            lines.forEach((line) => line_constructor(line));

        },
        error: (e) => {
            console.log(e);
        }
    });
}

$(document).ready(function() {
    get_system_activities();
});