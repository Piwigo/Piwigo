{include file='include/colorbox.inc.tpl'}
{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

{combine_script id='jquery.dataTables' load='footer' path='themes/default/js/plugins/jquery.dataTables.js'}
{combine_css path="themes/default/js/plugins/datatables/css/jquery.dataTables.css"}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}

{combine_script id='jquery.underscore' load='footer' path='themes/default/js/plugins/underscore.js'}

{combine_script id='jquery.ui.slider' require='jquery.ui' load='footer' path='themes/default/js/ui/minified/jquery.ui.slider.min.js'}
{combine_css path="themes/default/js/ui/theme/jquery.ui.slider.css"}

{footer_script}
var selectedMessage_pattern = "{'%d of %d users selected'|translate|escape:javascript}";
var selectedMessage_none = "{'No user selected of %d users'|translate|escape:javascript}";
var selectedMessage_all = "{'All %d users are selected'|translate|escape:javascript}";
var applyOnDetails_pattern = "{'on the %d selected users'|translate|escape:javascript}";
var newUser_pattern = "&#x2714; {'User %s added'|translate|escape:javascript}";
var registeredOn_pattern = "{'Registered on %s, %s.'|translate|escape:javascript}";
var lastVisit_pattern = "{'Last visit on %s, %s.'|translate|escape:javascript}";
var missingConfirm = "{'You need to confirm deletion'|translate|escape:javascript}";
var missingUsername = "{'Please, enter a login'|translate|escape:javascript}";

var allUsers = [{$all_users}];
var selection = [];
var pwg_token = "{$PWG_TOKEN}";

var protectedUsers = [{$protected_users}];
var passwordProtectedUsers = [{$password_protected_users}];
var guestUser = {$guest_user};

var truefalse = {
  'true':"{'Yes'|translate}",
  'false':"{'No'|translate}",
};

var statusLabels = {
{foreach from=$label_of_status key=status item=label}
  '{$status}' : '{$label|escape:javascript}',
{/foreach}
};
{/footer_script}

{footer_script}{literal}
jQuery(document).ready(function() {
  /**
   * Add user
   */
  jQuery("#addUser").click(function() {
    jQuery("#addUserForm").toggle();
    jQuery("#showAddUser .infos").hide();
    jQuery("input[name=username]").focus();
    return false;
  });

  jQuery("#genPass").click(function(e){
    e.preventDefault();

    var characterSet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';

    var i;
    var password;
    var length = getRandomInt(8, 15);

    password = '';
    for (i = 0; i < length; i++) {
      password += characterSet.charAt(Math.floor(Math.random() * characterSet.length));
    }

    jQuery('input[name=password]').val(password);
  });

  jQuery("#addUserClose").click(function() {
    jQuery("#addUserForm").hide();
    return false;
  });

  jQuery("#addUserForm").submit(function() {
    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.users.add",
      type:"POST",
      data: jQuery(this).serialize()+"&pwg_token="+pwg_token,
      beforeSend: function() {
        jQuery("#addUserForm .errors").hide();

        if (jQuery("input[name=username]").val() == "") {
          jQuery("#addUserForm .errors").html('&#x2718; '+missingUsername).show();
          return false;
        }

        jQuery("#addUserForm .loading").show();
      },
      success:function(data) {
        oTable.api().draw();
        jQuery("#addUserForm .loading").hide();

        var data = jQuery.parseJSON(data);
        if (data.stat == 'ok') {
          jQuery("#addUserForm input[type=text], #addUserForm input[type=password]").val("");

          var new_user = data.result.users[0];
          allUsers.push(parseInt(new_user.id));
          jQuery("#showAddUser .infos").html(sprintf(newUser_pattern, new_user.username)).show();
          checkSelection();

          jQuery("#addUserForm").hide();
        }
        else {
          jQuery("#addUserForm .errors").html('&#x2718; '+data.message).show();
        }
      },
      error:function(XMLHttpRequest, textStatus, errorThrows) {
        jQuery("#addUserForm .loading").hide();
      }
    });

    return false;
  });

  /**
   * Table with users
   */
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

  var recent_period_values = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,25,30,40,50,60,80,99];

  function getRecentPeriodInfoFromIdx(idx) {
    return sprintf(
      "{/literal}{'%d days'|@translate}{literal}",
      recent_period_values[idx]
    );
  }

  var nb_image_page_values = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,35,40,45,50,60,70,80,90,100,200,300,500,999];

  function getNbImagePageInfoFromIdx(idx) {
    return sprintf(
      "{/literal}{'%d photos per page'|@translate}{literal}",
      nb_image_page_values[idx]
    );
  }

  /* nb_image_page slider */
  var nb_image_page_init = getSliderKeyFromValue(jQuery('#action_nb_image_page input[name=nb_image_page]').val(), nb_image_page_values);
  
  jQuery('#action_nb_image_page .nb_image_page_infos').html(getNbImagePageInfoFromIdx(nb_image_page_init));
  
  jQuery('#action_nb_image_page .nb_image_page').slider({
    range: "min",
    min: 0,
    max: nb_image_page_values.length - 1,
    value: nb_image_page_init,
    slide: function( event, ui ) {
      jQuery('#action_nb_image_page .nb_image_page_infos').html(getNbImagePageInfoFromIdx(ui.value));
    },
    stop: function( event, ui ) {
      jQuery('#action_nb_image_page input[name=nb_image_page]').val(nb_image_page_values[ui.value]).trigger('change');
    }
  });

  /* recent_period slider */
  var recent_period_init = getSliderKeyFromValue(jQuery('#action_recent_period input[name=recent_period]').val(), recent_period_values);
  jQuery('#action_recent_period .recent_period_infos').html(getRecentPeriodInfoFromIdx(recent_period_init));
  
  jQuery('#action_recent_period .recent_period').slider({
    range: "min",
    min: 0,
    max: recent_period_values.length - 1,
    value: recent_period_init,
    slide: function( event, ui ) {
      jQuery('#action_recent_period .recent_period_infos').html(getRecentPeriodInfoFromIdx(ui.value));
    },
    stop: function( event, ui ) {
      jQuery('#action_recent_period input[name=recent_period]').val(recent_period_values[ui.value]).trigger('change');
    }
  });

  /* Formating function for row details */
  function fnFormatDetails(oTable, nTr) {
		var userId = oTable.api().row(nTr).data()[0];
    console.log("userId = "+userId);
    var sOut = null;

    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.users.getList",
      type:"POST",
      data: {
        user_id: userId,
        display: "all",
      },
      success:function(data) {
        jQuery("#user"+userId+" .loading").hide();

        var data = jQuery.parseJSON(data);
        if (data.stat == 'ok') {
          var user = data.result.users[0];

          /* Prepare data for template */
          user.statusOptions = [];
          jQuery("#action select[name=status] option").each(function() {
            var option = {value:jQuery(this).val(), label:jQuery(this).html(), isSelected:false};
          
            if (user.status == jQuery(this).val()) {
              option.isSelected = true;
            }
          
            user.statusOptions.push(option);
          });
          
          user.levelOptions = [];
          jQuery("#action select[name=level] option").each(function() {
            var option = {value:jQuery(this).val(), label:jQuery(this).html(), isSelected:false};
          
            if (user.level == jQuery(this).val()) {
              option.isSelected = true;
            }
          
            user.levelOptions.push(option);
          });
          
          user.groupOptions = [];
          jQuery("#action select[name=associate] option").each(function() {
            var option = {value:jQuery(this).val(), label:jQuery(this).html(), isSelected:false};
          
            if (user.groups.indexOf( parseInt(jQuery(this).val()) ) != -1) {
              option.isSelected = true;
            }
          
            user.groupOptions.push(option);
          });
          
          user.themeOptions = [];
          jQuery("#action select[name=theme] option").each(function() {
            var option = {value:jQuery(this).val(), label:jQuery(this).html(), isSelected:false};
          
            if (user.theme == jQuery(this).val()) {
              option.isSelected = true;
            }
          
            user.themeOptions.push(option);
          });
          
          user.languageOptions = [];
          jQuery("#action select[name=language] option").each(function() {
            var option = {value:jQuery(this).val(), label:jQuery(this).html(), isSelected:false};
          
            if (user.language == jQuery(this).val()) {
              option.isSelected = true;
            }
          
            user.languageOptions.push(option);
          });
          
          user.isGuest = (parseInt(userId) == guestUser);
          user.isProtected = (protectedUsers.indexOf(parseInt(userId)) != -1);
          user.isPasswordProtected = (passwordProtectedUsers.indexOf(parseInt(userId)) != -1);
          
          user.registeredOn_string = sprintf(
            registeredOn_pattern,
            user.registration_date_string,
            user.registration_date_since
          );
          
          user.lastVisit_string = "";
          if (typeof user.last_visit != 'undefined') {
            user.lastVisit_string = sprintf(lastVisit_pattern, user.last_visit_string, user.last_visit_since);
          }
          
          user.email = user.email || '';
          
          user.statusLabel = statusLabels[user.status];
          
		      /* Render the underscore template */
          _.templateSettings.variable = "user";
          
          var template = _.template(
            jQuery("script.userDetails").html()
		      );
          
          jQuery("#user"+userId).html(template(user));

          /* groups select */
          jQuery('[data-selectize=groups]').selectize({
            valueField: 'value',
            labelField: 'label',
            searchField: ['label'],
            plugins: ['remove_button']
          });

          var groupSelectize = jQuery('[data-selectize=groups]')[0].selectize;

          groupSelectize.load(function(callback) {
            callback(user.groupOptions);
          });

          jQuery.each(jQuery.grep(user.groupOptions, function(group) {
            return group.isSelected;
          }), function(i, group) {
            groupSelectize.addItem(group.value);
          });

          /* nb_image_page slider */
          var nb_image_page_init = getSliderKeyFromValue(jQuery('#user'+userId+' input[name=nb_image_page]').val(), nb_image_page_values);
          
          jQuery('#user'+userId+' .nb_image_page_infos').html(getNbImagePageInfoFromIdx(nb_image_page_init));
          
          jQuery('#user'+userId+' .nb_image_page').slider({
            range: "min",
            min: 0,
            max: nb_image_page_values.length - 1,
            value: nb_image_page_init,
            slide: function( event, ui ) {
              jQuery('#user'+userId+' .nb_image_page_infos').html(getNbImagePageInfoFromIdx(ui.value));
            },
            stop: function( event, ui ) {
              jQuery('#user'+userId+' input[name=nb_image_page]').val(nb_image_page_values[ui.value]).trigger('change');
            }
          });

          /* recent_period slider */
          var recent_period_init = getSliderKeyFromValue(jQuery('#user'+userId+' input[name=recent_period]').val(), recent_period_values);
          jQuery('#user'+userId+' .recent_period_infos').html(getRecentPeriodInfoFromIdx(recent_period_init));
          
          jQuery('#user'+userId+' .recent_period').slider({
            range: "min",
            min: 0,
            max: recent_period_values.length - 1,
            value: recent_period_init,
            slide: function( event, ui ) {
              jQuery('#user'+userId+' .recent_period_infos').html(getRecentPeriodInfoFromIdx(ui.value));
            },
            stop: function( event, ui ) {
              jQuery('#user'+userId+' input[name=recent_period]').val(recent_period_values[ui.value]).trigger('change');
            }
          });
        }
        else {
          console.log('error loading user details');
        }
      },
      error:function(XMLHttpRequest, textStatus, errorThrows) {
        console.log('technical error loading user details');
      }
    });

    jQuery(".user_form_popin")
      .attr("id", "user"+userId)
      .html('<div class="popinWait"><span><img class="loading" src="themes/default/images/ajax-loader-small.gif"> {/literal}{'Loading...'|translate|escape:'javascript'}{literal}</span></div>')
    ;
  }

jQuery(document).on('click', '.close-user-details',  function(e) {
  jQuery('.user_form_popin').colorbox.close();
  e.preventDefault();
});


  /* change password */
  jQuery(document).on('click', '.changePasswordOpen',  function() {
    var userId = jQuery(this).parentsUntil('form').parent().find('input[name=user_id]').val();

    jQuery(this).hide();
    jQuery('#user'+userId+' .changePasswordDone').hide();
    jQuery('#user'+userId+' .changePassword').show();
    jQuery('#user'+userId+' .changePassword input[type=text]').focus();

    return false;
  });

  jQuery(document).on('click', '.changePassword a.updatePassword',  function() {
    var userId = jQuery(this).parentsUntil('form').parent().find('input[name=user_id]').val();

    jQuery('#user'+userId+' .changePassword a .text').hide();
    jQuery('#user'+userId+' .changePassword a img').show();

    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.users.setInfo",
      type:"POST",
      data: {
        pwg_token:pwg_token,
        user_id:userId,
        password: jQuery('#user'+userId+' .changePassword input[type=text]').val()
      },
      beforeSend: function() {
        jQuery('#user'+userId+' .changePassword input[type=text]').val("");
      },
      success:function(data) {
        jQuery('#user'+userId+' .changePassword a .text').show();
        jQuery('#user'+userId+' .changePassword a img').hide();
        jQuery('#user'+userId+' .changePassword').hide();
        jQuery('#user'+userId+' .changePasswordOpen').show();
        jQuery('#user'+userId+' .changePasswordDone').show();
      },
      error:function(XMLHttpRequest, textStatus, errorThrows) {
      }
    });

    return false;
  });

  jQuery(document).on('click', '.changePassword a.cancel',  function() {
    var userId = jQuery(this).parentsUntil('form').parent().find('input[name=user_id]').val();

    jQuery('#user'+userId+' .changePassword').hide();
    jQuery('#user'+userId+' .changePasswordOpen').show();

    return false;
  });

  /* change username */
  jQuery(document).on('click', '.changeUsernameOpen a',  function() {
    var userId = jQuery(this).parentsUntil('form').parent().find('input[name=user_id]').val();
    var username = jQuery('#user'+userId+' .username').html();

    jQuery('#user'+userId+' .changeUsernameOpen').hide();
    jQuery('#user'+userId+' .changeUsername').show();
    jQuery('#user'+userId+' .changeUsername input[type=text]').val(username).focus();

    return false;
  });

  jQuery(document).on('click', 'a.updateUsername',  function() {
    var userId = jQuery(this).parentsUntil('form').parent().find('input[name=user_id]').val();

    jQuery('#user'+userId+' .changeUsername a .text').hide();
    jQuery('#user'+userId+' .changeUsername a img').show();

    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.users.setInfo",
      type:"POST",
      data: {
        pwg_token:pwg_token,
        user_id:userId,
        username: jQuery('#user'+userId+' .changeUsername input[type=text]').val()
      },
      success:function(data) {
        jQuery('#user'+userId+' .changeUsername a .text').show();
        jQuery('#user'+userId+' .changeUsername a img').hide();
        jQuery('#user'+userId+' .changeUsername').hide();
        jQuery('#user'+userId+' .changeUsernameOpen').show();

        var data = jQuery.parseJSON(data);
        jQuery('#user'+userId+' .username').html(data.result.users[0].username);
      },
      error:function(XMLHttpRequest, textStatus, errorThrows) {
      }
    });

    return false;
  });

  jQuery(document).on('click', '.changeUsername a.cancel',  function() {
    var userId = jQuery(this).parentsUntil('form').parent().find('input[name=user_id]').val();

    jQuery('#user'+userId+' .changeUsername').hide();
    jQuery('#user'+userId+' .changeUsernameOpen').show();

    return false;
  });

  /* display the "save" button when a field changes */
  jQuery(document).on('change', '.userProperties input, .userProperties select',  function() {
    var userId = jQuery(this).parentsUntil('form').parent().find('input[name=user_id]').val();

    jQuery('#user'+userId+' input[type=submit]').show();
    jQuery('#user'+userId+' .propertiesUpdateDone').hide();
  });

  /* delete user */
  jQuery(document).on('click', '.userDelete a',  function() {
    if (!confirm("{/literal}{'Are you sure?'|translate|escape:javascript}{literal}")) {
      return false;
    }

    var userId = jQuery(this).data('user_id');
    var username = jQuery('#user'+userId+' .username').html();

    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.users.delete",
      type:"POST",
      data: {
        user_id:userId,
        pwg_token:pwg_token
      },
      beforeSend: function() {
        jQuery('#user'+userId+' .userDelete .loading').show();
      },
      success:function(data) {
        jQuery('.user_form_popin').colorbox.close();
        jQuery('#showAddUser .infos').html('&#x2714; User '+username+' deleted').show();
      },
      error:function(XMLHttpRequest, textStatus, errorThrows) {
        jQuery('#user'+userId+' .userDelete .loading').hide();
      }
    });

    return false;
  });

  jQuery(document).on('click', '.userProperties input[type=submit]',  function() {
    var userId = jQuery(this).data('user_id');

    var formData = jQuery('#user'+userId+' form').serialize();
    formData += '&pwg_token='+pwg_token;

    if (jQuery('#user'+userId+' form select[name="group_id[]"] option:selected').length == 0) {
      formData += '&group_id=-1';
    }

    if (!jQuery('#user'+userId+' form input[name=enabled_high]').is(':checked')) {
      formData += '&enabled_high=false';
    }

    if (!jQuery('#user'+userId+' form input[name=expand]').is(':checked')) {
      formData += '&expand=false';
    }

    if (!jQuery('#user'+userId+' form input[name=show_nb_hits]').is(':checked')) {
      formData += '&show_nb_hits=false';
    }

    if (!jQuery('#user'+userId+' form input[name=show_nb_comments]').is(':checked')) {
      formData += '&show_nb_comments=false';
    }

    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.users.setInfo",
      type:"POST",
      data: formData,
      beforeSend: function() {
        jQuery('#user'+userId+' .submitWait').show();
      },
      success:function(data) {
        jQuery('#user'+userId+' .submitWait').hide();

        var html_message;

        var data = jQuery.parseJSON(data);
        if (data.stat == 'ok') {
          var message = sprintf(
            "{/literal}{'User %s updated'|translate|escape:javascript}{literal}",
            data.result.users[0].username
          );

          html_message = '<span class="infos">&#x2714; '+message+'</span>';
        }
        else {
          html_message = '<span class="errors">&#x2718; '+data.message+'</span>';
        }

        jQuery('#user'+userId+' .propertiesUpdateDone')
          .html(html_message)
          .show();
      },
      error:function(XMLHttpRequest, textStatus, errorThrows) {
        jQuery('#user'+userId+' .submitWait').hide();
      }
    });

    return false;
  });

  /* Add event listener for opening and closing details
   * Note that the indicator for showing which row is open is not controlled by DataTables,
   * rather it is done here
   */
  jQuery(document).on('click', '#userList tbody td .openUserDetails',  function() {
    var nTr = this.parentNode.parentNode;

    jQuery.colorbox({
      inline:true,
      title:"{/literal}{'Edit user'|translate}{literal}",
      href:".user_form_popin",
      onClosed: function() {
        oTable.api().draw();
      }
    });

    fnFormatDetails(oTable, nTr);
  });


  /* first column must be prefixed with the open/close icon */
  var aoColumns = [
    {
      visible:false
    },
    {
      render: function(data, type, full) {
        return '<label><input type="checkbox" data-user_id="'+full[0]+'"> '+data+'</label> <a title="{/literal}{'Open user details'|translate|escape:'javascript'}{literal}" class="icon-pencil openUserDetails">{/literal}{'edit'|translate}{literal}</a>';
      }
    }
  ];

  for (i=2; i<jQuery("#userList thead tr th").length; i++) {
    aoColumns.push(null);
  }

  var oTable = jQuery('#userList').dataTable({
    pageLength: 10,
    autoWidth: false,
    deferRender: true,
    processing: true,
    serverSide: true,
		serverMethod: "POST",
    ajaxSource: "admin/user_list_backend.php",
		pagingType: "simple",
    language: {
      processing: "{/literal}{'Loading...'|translate|escape:'javascript'}{literal}",
      lengthMenu: sprintf("{/literal}{'Show %s users'|translate|escape:'javascript'}{literal}", '_MENU_'),
      zeroRecords: "{/literal}{'No matching user found'|translate|escape:'javascript'}{literal}",
      info: sprintf("{/literal}{'Showing %s to %s of %s users'|translate|escape:'javascript'}{literal}", '_START_', '_END_', '_TOTAL_'),
      infoEmpty: "{/literal}{'No matching user found'|translate|escape:'javascript'}{literal}",
      infoFiltered: sprintf("{/literal}{'(filtered from %s total users)'|translate|escape:'javascript'}{literal}", '_MAX_'),
      search: '<span class="icon-search"></span>'+"{/literal}{'Search'|translate|escape:'javascript'}{literal}",
      loadingRecords: "{/literal}{'Loading...'|translate|escape:'javascript'}{literal}",
      paginate: {
          first:    "{/literal}{'First'|translate|escape:'javascript'}{literal}",
          previous: '← '+"{/literal}{'Previous'|translate|escape:'javascript'}{literal}",
          next:     "{/literal}{'Next'|translate|escape:'javascript'}{literal}"+' →',
          last:     "{/literal}{'Last'|translate|escape:'javascript'}{literal}",
      }
    },
    "drawCallback": function( oSettings ) {
      jQuery("#userList input[type=checkbox]").each(function() {
        var user_id = jQuery(this).data("user_id");
        jQuery(this).prop('checked', (selection.indexOf(user_id) != -1));
      });
    },
    columns: aoColumns
  });

  /**
   * Selection management
   */
  function checkSelection() {
    if (selection.length > 0) {
      jQuery("#forbidAction").hide();
      jQuery("#permitAction").show();

      jQuery("#applyOnDetails").text(
        sprintf(
          applyOnDetails_pattern,
          selection.length
        )
      );

      if (selection.length == allUsers.length) {
        jQuery("#selectedMessage").text(
          sprintf(
            selectedMessage_all,
            allUsers.length
          )
        );
      }
      else {
        jQuery("#selectedMessage").text(
          sprintf(
            selectedMessage_pattern,
            selection.length,
            allUsers.length
          )
        );
      }
    }
    else {
      jQuery("#forbidAction").show();
      jQuery("#permitAction").hide();

      jQuery("#selectedMessage").text(
        sprintf(
          selectedMessage_none,
          allUsers.length
        )
      );
    }

    jQuery("#applyActionBlock .infos").hide();
  }

  jQuery(document).on('change', '#userList input[type=checkbox]',  function() {
    var user_id = jQuery(this).data("user_id");

    array_delete(selection, user_id);

    if (jQuery(this).is(":checked")) {
      selection.push(user_id);
    }

    checkSelection();
  });

  jQuery("#selectAll").click(function () {
    selection = allUsers;
    jQuery("#userList input[type=checkbox]").prop('checked', true);
    checkSelection();
    return false;
  });

  jQuery("#selectNone").click(function () {
    selection = [];
    jQuery("#userList input[type=checkbox]").prop('checked', false);
    checkSelection();
    return false;
  });

  jQuery("#selectInvert").click(function () {
    var newSelection = [];
    for(var i in allUsers)
    {
      if (selection.indexOf(allUsers[i]) == -1) {
        newSelection.push(allUsers[i]);
      }
    }
    selection = newSelection;

    jQuery("#userList input[type=checkbox]").each(function() {
      var user_id = jQuery(this).data("user_id");
      jQuery(this).prop('checked', (selection.indexOf(user_id) != -1));
    });

    checkSelection();
    return false;
  });

  /**
   * Action management
   */
  jQuery("[id^=action_]").hide();
  
  jQuery("select[name=selectAction]").change(function () {
    jQuery("#applyActionBlock .infos").hide();

    jQuery("[id^=action_]").hide();

    jQuery("#action_"+$(this).prop("value")).show();
  
    if (jQuery(this).val() != -1) {
      jQuery("#applyActionBlock").show();
    }
    else {
      jQuery("#applyActionBlock").hide();
    }
  });

  jQuery("#permitAction input, #permitAction select").click(function() {
    jQuery("#applyActionBlock .infos").hide();
  });

  jQuery("#applyAction").click(function() {
    var action = jQuery("select[name=selectAction]").prop("value");
    var method = 'pwg.users.setInfo';
    var data = {
      pwg_token: pwg_token,
      user_id: selection
    };

    switch (action) {
      case 'delete':
        if (!jQuery("input[name=confirm_deletion]").is(':checked')) {
          alert(missingConfirm);
          return false;
        }
        method = 'pwg.users.delete';
        break;
      case 'group_associate':
        method = 'pwg.groups.addUser';
        data.group_id = jQuery("select[name=associate]").prop("value");
        break;
      case 'group_dissociate':
        method = 'pwg.groups.deleteUser';
        data.group_id = jQuery("select[name=dissociate]").prop("value");
        break;
      case 'status':
        data.status = jQuery("select[name=status]").prop("value");
        break;
      case 'enabled_high':
        data.enabled_high = jQuery("input[name=enabled_high]:checked").val();
        break;
      case 'level':
        data.level = jQuery("select[name=level]").val();
        break;
      case 'nb_image_page':
        data.nb_image_page = jQuery("input[name=nb_image_page]").val();
        break;
      case 'theme':
        data.theme = jQuery("select[name=theme]").val();
        break;
      case 'language':
        data.language = jQuery("select[name=language]").val();
        break;
      case 'recent_period':
        data.recent_period = jQuery("input[name=recent_period]").val();
        break;
      case 'expand':
        data.expand = jQuery("input[name=expand]:checked").val();
        break;
      case 'show_nb_comments':
        data.show_nb_comments = jQuery("input[name=show_nb_comments]:checked").val();
        break;
      case 'show_nb_hits':
        data.show_nb_hits = jQuery("input[name=show_nb_hits]:checked").val();
        break;
      default:
        alert("Unexpected action");
        return false;
    }

    jQuery.ajax({
      url: "ws.php?format=json&method="+method,
      type:"POST",
      data: data,
      beforeSend: function() {
        jQuery("#applyActionLoading").show();
      },
      success:function(data) {
        oTable.api().draw();
        jQuery("#applyActionLoading").hide();
        jQuery("#applyActionBlock .infos").show();

        if (action == 'delete') {
          var allUsers_new = [];
          for(var i in allUsers)
          {
            if (selection.indexOf(allUsers[i]) == -1) {
              allUsers_new.push(allUsers[i]);
            }
          }
          allUsers = allUsers_new;
          console.log('allUsers_new.length = '+allUsers_new.length);
          selection = [];
          checkSelection();
        }
      },
      error:function(XMLHttpRequest, textStatus, errorThrows) {
        jQuery("#applyActionLoading").hide();
      }
    });

    return false;
  });

});
{/literal}{/footer_script}

{html_style}{literal}
.dataTables_wrapper, .dataTables_info {clear:none;}
.dataTables_wrapper .dataTables_info {clear:none;}
.dataTables_wrapper::after {clear:none;}

table.dataTable {clear:right;padding-top:10px;}
.dataTable td img {margin-bottom: -6px;margin-left: -6px;}

.paginate_button, .paginate_button:hover {background:none !important;}
.dataTables_wrapper .dataTables_paginate .paginate_button {color:#005E89 !important;}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {color:#D54E21 !important; text-decoration:underline !important; border-color:transparent;}

.paginate_button.next {padding-right:3px !important;}

table.dataTable tbody th,
table.dataTable tbody td {
  padding: 3px 5px;
}

.bulkAction {margin-top:10px;}
#addUserForm p {margin-left:0;}
#applyActionBlock .actionButtons {margin-left:0;}
span.infos, span.errors {background-image:none; padding:2px 5px; margin:0;border-radius:5px;}

.userStats {margin-top:10px;}
.recent_period_infos {margin-left:10px;}
.nb_image_page, .recent_period {width:340px;margin-top:5px;}
#action_recent_period .recent_period {display:inline-block;}
.checkActions {padding:0 1em;}
{/literal}{/html_style}

<div class="titrePage">
  <h2>{'User list'|@translate}</h2>
</div>

<p class="showCreateAlbum" id="showAddUser">
  <a href="#" id="addUser" class="icon-plus-circled">{'Add a user'|translate}</a>
  <span class="infos" style="display:none"></span>
</p>

<form id="addUserForm" style="{if !isset($show_add_user)}display:none{/if}" method="post" name="add_user" action="{$F_ADD_ACTION}">
  <fieldset class="with-border">
    <legend>{'Add a user'|@translate}</legend>

    <p>
      <strong>{'Username'|translate}</strong><br>
      <input type="text" name="username" maxlength="50" size="20">
    </p>

    <p>
      <strong>{'Password'|translate}</strong><br>
      <input type="{if $Double_Password}password{else}text{/if}" name="password">
      <a id="genPass" href="#" class="icon-lock">{'generate random password'|translate}</a>
    </p>
    
{if $Double_Password}
    <p>
      <strong>{'Confirm Password'|@translate}</strong><br>
      <input type="password" name="password_confirm">
    </p>
{/if}

    <p>
      <strong>{'Email address'|@translate}</strong><br>
      <input type="text" name="email">
    </p>

    <p>
      <label><input type="checkbox" name="send_password_by_mail"> <strong>{'Send connection settings by email'|@translate}</strong></label>
    </p>

    <p class="actionButtons">
      <input class="submit" name="submit_add" type="submit" value="{'Submit'|@translate}">
      <a href="#" id="addUserClose">{'Cancel'|@translate}</a>
      <span class="loading" style="display:none"><img src="themes/default/images/ajax-loader-small.gif"></span>
      <span class="errors" style="display:none"></span>
    </p>
  </fieldset>
</form>

<form method="post" name="preferences" action="">

<table id="userList">
  <thead>
    <tr>
      <th>id</th>
      <th>{'Username'|@translate}</th>
      <th>{'Status'|@translate}</th>
      <th>{'Email address'|@translate}</th>
      <th>{'Groups'|@translate}</th>
      <th>{'Privacy level'|@translate}</th>
      <th>{'registration date'|@translate}</th>
    </tr>
  </thead>
</table>

<div style="clear:right"></div>

<p class="checkActions">
  {'Select:'|@translate}
  <a href="#" id="selectAll">{'All'|@translate}</a>,
  <a href="#" id="selectNone">{'None'|@translate}</a>,
  <a href="#" id="selectInvert">{'Invert'|@translate}</a>

  <span id="selectedMessage"></span>
</p>

<fieldset id="action">
  <legend>{'Action'|@translate}</legend>

  <div id="forbidAction">{'No user selected, no action possible.'|@translate}</div>
  <div id="permitAction" style="display:none">

    <select name="selectAction">
      <option value="-1">{'Choose an action'|@translate}</option>
      <option disabled="disabled">------------------</option>
      <option value="delete" class="icon-trash">{'Delete selected users'|@translate}</option>
      <option value="status">{'Status'|@translate}</option>
      <option value="group_associate">{'associate to group'|translate}</option>
      <option value="group_dissociate">{'dissociate from group'|@translate}</option>
      <option value="enabled_high">{'High definition enabled'|@translate}</option>
      <option value="level">{'Privacy level'|@translate}</option>
      <option value="nb_image_page">{'Number of photos per page'|@translate}</option>
      <option value="theme">{'Theme'|@translate}</option>
      <option value="language">{'Language'|@translate}</option>
      <option value="recent_period">{'Recent period'|@translate}</option>
      <option value="expand">{'Expand all albums'|@translate}</option>
{if $ACTIVATE_COMMENTS}
      <option value="show_nb_comments">{'Show number of comments'|@translate}</option>
{/if}
      <option value="show_nb_hits">{'Show number of hits'|@translate}</option>
    </select>

    {* delete *}
    <div id="action_delete" class="bulkAction">
      <p><label><input type="checkbox" name="confirm_deletion" value="1"> {'Are you sure?'|@translate}</label></p>
    </div>

    {* status *}
    <div id="action_status" class="bulkAction">
      <select name="status">
        {html_options options=$pref_status_options selected=$pref_status_selected}
      </select>
    </div>

    {* group_associate *}
    <div id="action_group_associate" class="bulkAction">
      {html_options name=associate options=$association_options selected=$associate_selected}
    </div>

    {* group_dissociate *}
    <div id="action_group_dissociate" class="bulkAction">
      {html_options name=dissociate options=$association_options selected=$dissociate_selected}
    </div>

    {* enabled_high *}
    <div id="action_enabled_high" class="bulkAction">
      <label><input type="radio" name="enabled_high" value="true">{'Yes'|@translate}</label>
      <label><input type="radio" name="enabled_high" value="false" checked="checked">{'No'|@translate}</label>
    </div>

    {* level *}
    <div id="action_level" class="bulkAction">
      <select name="level" size="1">
        {html_options options=$level_options selected=$level_selected}
      </select>
    </div>

    {* nb_image_page *}
    <div id="action_nb_image_page" class="bulkAction">
      <strong class="nb_image_page_infos"></strong>
      <div class="nb_image_page"></div>
      <input type="hidden" name="nb_image_page" value="{$NB_IMAGE_PAGE}">
    </div>

    {* theme *}
    <div id="action_theme" class="bulkAction">
      <select name="theme" size="1">
        {html_options options=$theme_options selected=$theme_selected}
      </select>
    </div>

    {* language *}
    <div id="action_language" class="bulkAction">
      <select name="language" size="1">
        {html_options options=$language_options selected=$language_selected}
      </select>
    </div>

    {* recent_period *}
    <div id="action_recent_period" class="bulkAction">
      <div class="recent_period"></div>
      <span class="recent_period_infos"></span>
      <input type="hidden" name="recent_period" value="{$RECENT_PERIOD}">
    </div>

    {* expand *}
    <div id="action_expand" class="bulkAction">
      <label><input type="radio" name="expand" value="true">{'Yes'|@translate}</label>
      <label><input type="radio" name="expand" value="false" checked="checked">{'No'|@translate}</label>
    </div>

    {* show_nb_comments *}
    <div id="action_show_nb_comments" class="bulkAction">
      <label><input type="radio" name="show_nb_comments" value="true">{'Yes'|@translate}</label>
      <label><input type="radio" name="show_nb_comments" value="false" checked="checked">{'No'|@translate}</label>
    </div>

    {* show_nb_hits *}
    <div id="action_show_nb_hits" class="bulkAction">
      <label><input type="radio" name="show_nb_hits" value="true">{'Yes'|@translate}</label>
      <label><input type="radio" name="show_nb_hits" value="false" checked="checked">{'No'|@translate}</label>
    </div>

    <p id="applyActionBlock" style="display:none" class="actionButtons">
      <input id="applyAction" class="submit" type="submit" value="{'Apply action'|@translate}" name="submit"> <span id="applyOnDetails"></span>
      <span id="applyActionLoading" style="display:none"><img src="themes/default/images/ajax-loader-small.gif"></span>
      <span class="infos" style="display:none">&#x2714; {'Users modified'|translate}</span>
    </p>

  </div> {* #permitAction *}
</fieldset>

</form> 

{* Underscore Template Definition *}
<script type="text/template" class="userDetails">
<form>
  <div class="userActions">
<% if (!user.isPasswordProtected) { %>
    <span class="changePasswordDone infos" style="display:none">&#x2714; {'Password updated'|translate}</span>
    <span class="changePassword" style="display:none">{'New password'|translate} <input type="text"> <a href="#" class="buttonLike updatePassword"><img src="themes/default/images/ajax-loader-small.gif" style="margin-bottom:-1px;margin-left:1px;display:none;"><span class="text">{'Submit'|translate}</span></a> <a href="#" class="cancel">{'Cancel'|translate}</a></span>
    <a class="icon-key changePasswordOpen" href="#">{'Change password'|translate}</a>
    <br>
<% } %>

    <a target="_blank" href="admin.php?page=user_perm&amp;user_id=<%- user.id %>" class="icon-lock">{'Permissions'|translate}</a>

<% if (!user.isProtected) { %>
    <br><span class="userDelete"><img class="loading" src="themes/default/images/ajax-loader-small.gif" style="display:none;"><a href="#" class="icon-trash" data-user_id="<%- user.id %>">{'Delete'|translate}</a></span>
<% } %>

  </div>

  <span class="changeUsernameOpen"><strong class="username"><%- user.username %></strong>

<% if (!user.isGuest) { %>
  <a href="#" class="icon-pencil">{'Change username'|translate}</a></span>
  <span class="changeUsername" style="display:none">
  <input type="text"> <a href="#" class="buttonLike updateUsername"><img src="themes/default/images/ajax-loader-small.gif" style="margin-bottom:-1px;margin-left:1px;display:none;"><span class="text">{'Submit'|translate}</span></a> <a href="#" class="cancel">{'Cancel'|translate}</a>
<% } %>

  </span>

  <div class="userStats"><%- user.registeredOn_string %><br><%- user.lastVisit_string %></div>

  <div class="userPropertiesContainer">
    <input type="hidden" name="user_id" value="<%- user.id %>">
    <div class="userPropertiesSet">
      <div class="userPropertiesSetTitle">{'Properties'|translate}</div>

      <div class="userProperty"><strong>{'Email address'|translate}</strong>
        <br>
<% if (!user.isGuest) { %>
        <input name="email" type="text" value="<%- user.email %>">
<% } else { %>
      {'N/A'|translate}
<% } %>
      </div>

      <div class="userProperty"><strong>{'Status'|translate}</strong>
        <br>
<% if (!user.isProtected) { %>
        <select name="status">
  <% _.each( user.statusOptions, function( option ){ %>
          <option value="<%- option.value%>" <% if (option.isSelected) { %>selected="selected"<% } %>><%- option.label %></option>
  <% }); %>
        </select>
<% } else { %>
        <%- user.statusLabel %>
<% } %>
      </div>

      <div class="userProperty"><strong>{'Privacy level'|translate}</strong>
        <br>
        <select name="level">
<% _.each( user.levelOptions, function( option ){ %>
          <option value="<%- option.value%>" <% if (option.isSelected) { %>selected="selected"<% } %>><%- option.label %></option>
<% }); %>
        </select>
      </div>

      <div class="userProperty"><label><input type="checkbox" name="enabled_high"<% if (user.enabled_high == 'true') { %> checked="checked"<% } %>> <strong>{'High definition enabled'|translate}</strong></label></div>

      <div class="userProperty"><strong>{'Groups'|translate}</strong><br>
        <select data-selectize="groups" placeholder="{'Type in a search term'|translate}" 
          name="group_id[]" multiple style="width:340px;"></select>
      </div>
    </div>

    <div class="userPropertiesSet userPrefs">
      <div class="userPropertiesSetTitle">{'Preferences'|translate}</div>

      <div class="userProperty"><strong class="nb_image_page_infos"></strong>
        <div class="nb_image_page"></div>
        <input type="hidden" name="nb_image_page" value="<%- user.nb_image_page %>">
      </div>

      <div class="userProperty"><strong>{'Theme'|translate}</strong><br>
        <select name="theme">
<% _.each( user.themeOptions, function( option ){ %>
          <option value="<%- option.value%>" <% if (option.isSelected) { %>selected="selected"<% } %>><%- option.label %></option>
<% }); %>
        </select>
      </div>

      <div class="userProperty"><strong>{'Language'|translate}</strong><br>
        <select name="language">
<% _.each( user.languageOptions, function( option ){ %>
          <option value="<%- option.value%>" <% if (option.isSelected) { %>selected="selected"<% } %>><%- option.label %></option>
<% }); %>
        </select>
      </div>

      <div class="userProperty"><strong>{'Recent period'|translate}</strong> <span class="recent_period_infos"></span>
        <div class="recent_period"></div>
        <input type="hidden" name="recent_period" value="<%- user.recent_period %>">
      </div>

      <div class="userProperty"><label><input type="checkbox" name="expand"<% if (user.expand == 'true') { %> checked="checked"<% }%>> <strong>{'Expand all albums'|translate}</strong></label></div>

      <div class="userProperty"><label><input type="checkbox" name="show_nb_comments"<% if (user.show_nb_comments == 'true') { %> checked="checked"<% }%>> <strong>{'Show number of comments'|translate}</strong></label></div>

      <div class="userProperty"><label><input type="checkbox" name="show_nb_hits"<% if (user.show_nb_hits == 'true') { %> checked="checked"<% }%>> <strong>{'Show number of hits'|translate}</strong></label></div>

    </div>

    <div style="clear:both"></div>
  </div> {* userPropertiesContainer *}

  <input type="submit" value="{'Update user'|translate|escape:html}" data-user_id="<%- user.id %>">
  <img class="submitWait" src="themes/default/images/ajax-loader-small.gif" style="display:none">
  <a href="#close" class="icon-cancel-circled close-user-details" title="{'Close user details'|translate}">{'close'|translate}</a>
  <span class="propertiesUpdateDone" style="display:none">
    <span class="infos">&#x2714; ...</span>
    <span class="errors">&#x2718; ...</span>
  </span>
</form>
</script>

<div style="display:none">
  <div class="user_form_popin userProperties"></div>
</div>