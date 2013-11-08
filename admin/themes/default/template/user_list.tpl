{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

{combine_script id='jquery.dataTables' load='footer' path='themes/default/js/plugins/jquery.dataTables.js'}
{combine_css path="themes/default/js/plugins/datatables/css/jquery.dataTables.css"}

{footer_script}
var selectedMessage_pattern = "{'%d of %d photos selected'|@translate}";
var selectedMessage_none = "{'No photo selected, %d photos in current set'|@translate}";
var selectedMessage_all = "{'All %d photos are selected'|@translate}";
var applyOnDetails_pattern = "{'on the %d selected users'|@translate}";
var newUser_pattern = "&#x2714; {'User %s added'|translate}";
var missingConfirm = "{'You need to confirm deletion'|translate}";
var missingUsername = "{'Please, enter a login'|translate}";

var allUsers = [{$all_users}];
var selection = [{$selection}];
var pwg_token = "{$PWG_TOKEN}";

var truefalse = {
  true:"{'Yes'|translate}",
  false:"{'No'|translate}",
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

  jQuery("#addUserClose").click(function() {
    jQuery("#addUserForm").hide();
    return false;
  });

  jQuery("#addUserForm").submit(function() {
    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.users.add",
      type:"POST",
      data: jQuery(this).serialize(),
      beforeSend: function() {
        jQuery("#addUserForm .errors").hide();

        if (jQuery("input[name=username]").val() == "") {
          jQuery("#addUserForm .errors").html('&#x2718; '+missingUsername).show();
          return false;
        }

        jQuery("#addUserForm .loading").show();
      },
      success:function(data) {
        oTable.fnDraw();
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
  /* Formating function for row details */
  function fnFormatDetails(oTable, nTr) {
    var userId = oTable.fnGetData(nTr)[0];
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

          var userDetails = '<input type="hidden" name="user_id" value="'+user.id+'">';
          userDetails += '<fieldset><legend>{/literal}{'Properties'|translate}{literal}</legend>';
          userDetails += '<div class="userProperty"><strong>{/literal}{'Username'|translate}{literal}</strong>';
          userDetails += '<br>'+user.username+'</div>';

          userDetails += '<div class="userProperty"><strong>{/literal}{'Email address'|translate}{literal}</strong>';
          userDetails += '<br><input name="email" type="text" value="'+user.email+'"></div>';

          userDetails += '<div class="userProperty"><strong>{/literal}{'Status'|translate}{literal}</strong>';
          userDetails += '<br><select name="status">';
          jQuery("#action select[name=status] option").each(function() {
            var selected = '';
            if (user.status == jQuery(this).val()) {
              selected = ' selected="selected"';
            }
            userDetails += '<option value="'+jQuery(this).val()+'"'+selected+'>'+jQuery(this).html()+'</option>';
          });
          userDetails += '</select></div>';

/*
          userDetails += '<div class="userProperty"><strong>{/literal}{'Groups'|translate}{literal}</strong>';
          userDetails += '<br><select multiple class="chzn-select" style="width:200px;">';
          jQuery("#action select[name=associate] option").each(function() {
            var selected = '';
            if (user.groups.indexOf(jQuery(this).val()) != -1) {
              selected = ' selected="selected"';
            }
            userDetails += '<option value="'+jQuery(this).val()+'"'+selected+'>'+jQuery(this).html()+'</option>';
          });
          userDetails += '</select></div>';
          // userDetails += '<br>'+user.groups.join(",")+'</div>';
*/

          userDetails += '<div class="userProperty"><strong>{/literal}{'Privacy level'|translate}{literal}</strong>';
          userDetails += '<br><select name="level">';
          jQuery("#action select[name=level] option").each(function() {
            var selected = '';
            if (user.level == jQuery(this).val()) {
              selected = ' selected="selected"';
            }
            userDetails += '<option value="'+jQuery(this).val()+'"'+selected+'>'+jQuery(this).html()+'</option>';
          });
          userDetails += '</select></div>';

          userDetails += '<div class="userProperty"><strong>{/literal}{'High definition enabled'|translate}{literal}</strong>';
          userDetails += '<br>';
          jQuery.each(truefalse, function(value, label) {
            var checked = '';
            if (user.enabled_high == value) {
              checked = ' checked="checked"';
            }
            userDetails += '<label><input type="radio" name="enabled_high" value="'+value+'"'+checked+'>'+label+'</label>';
          });
          userDetails += '</div>';

          userDetails += '</fieldset><fieldset><legend>{/literal}{'Preferences'|translate}{literal}</legend>';

          userDetails += '<div class="userProperty"><strong>{/literal}{'Number of photos per page'|translate}{literal}</strong>';
          userDetails += '<br>'+user.nb_image_page+'</div>';

          userDetails += '<div class="userProperty"><strong>{/literal}{'Theme'|translate}{literal}</strong>';
          userDetails += '<br><select name="theme">';
          jQuery("#action select[name=theme] option").each(function() {
            var selected = '';
            if (user.theme == jQuery(this).val()) {
              selected = ' selected="selected"';
            }
            userDetails += '<option value="'+jQuery(this).val()+'"'+selected+'>'+jQuery(this).html()+'</option>';
          });
          userDetails += '</select></div>';

          userDetails += '<div class="userProperty"><strong>{/literal}{'Language'|translate}{literal}</strong>';
          userDetails += '<br><select name="language">';
          jQuery("#action select[name=language] option").each(function() {
            var selected = '';
            if (user.language == jQuery(this).val()) {
              selected = ' selected="selected"';
            }
            userDetails += '<option value="'+jQuery(this).val()+'"'+selected+'>'+jQuery(this).html()+'</option>';
          });
          userDetails += '</select></div>';

          userDetails += '<div class="userProperty"><strong>{/literal}{'Recent period'|translate}{literal}</strong>';
          userDetails += '<br>'+user.recent_period+'</div>';

          userDetails += '<div class="userProperty"><strong>{/literal}{'Expand all albums'|translate}{literal}</strong>';
          userDetails += '<br>';
          jQuery.each(truefalse, function(value, label) {
            var checked = '';
            if (user.expand == value) {
              checked = ' checked="checked"';
            }
            userDetails += '<label><input type="radio" name="expand" value="'+value+'"'+checked+'>'+label+'</label>';
          });
          userDetails += '</div>';

          userDetails += '<div class="userProperty"><strong>{/literal}{'Show number of comments'|translate}{literal}</strong>';
          userDetails += '<br>';
          jQuery.each(truefalse, function(value, label) {
            var checked = '';
            if (user.show_nb_comments == value) {
              checked = ' checked="checked"';
            }
            userDetails += '<label><input type="radio" name="show_nb_comments" value="'+value+'"'+checked+'>'+label+'</label>';
          });
          userDetails += '</div>';

          userDetails += '<div class="userProperty"><strong>{/literal}{'Show number of hits'|translate}{literal}</strong>';
          userDetails += '<br>';
          jQuery.each(truefalse, function(value, label) {
            var checked = '';
            if (user.show_nb_hits == value) {
              checked = ' checked="checked"';
            }
            userDetails += '<label><input type="radio" name="show_nb_hits" value="'+value+'"'+checked+'>'+label+'</label>';
          });
          userDetails += '</div>';
          userDetails += '</fieldset>';

          userDetails += '<input type="submit" value="{/literal}{'Submit'|translate}{literal}" data-user_id="'+userId+'">';
          userDetails += '<img class="submitWait" src="themes/default/images/ajax-loader-small.gif" style="display:none">'

          jQuery("#user"+userId).append(userDetails);
        }
        else {
          console.log('error loading user details');
        }
      },
      error:function(XMLHttpRequest, textStatus, errorThrows) {
        console.log('technical error loading user details');
      }
    });
  
    return '<div id="user'+userId+'" class="userProperties"><img class="loading" src="themes/default/images/ajax-loader-small.gif"></div>';
  }

  jQuery(document).on('click', '.userProperties input[type=submit]',  function() {
    var userId = jQuery(this).data('user_id');

    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.users.setInfo",
      type:"POST",
      data: jQuery('#user'+userId).find('select, input').serialize(),
      beforeSend: function() {
        jQuery('#user'+userId+' .submitWait').show();
      },
      success:function(data) {
        jQuery('#user'+userId+' .submitWait').hide();
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
  jQuery(document).on('click', '#userList tbody td img',  function() {
    var nTr = this.parentNode.parentNode;
    if ( this.src.match('details_close') ) {
      /* This row is already open - close it */
      this.src = "admin/themes/default/icon/details_open.png";
      oTable.fnClose( nTr );
    }
    else {
      /* Open this row */
      this.src = "admin/themes/default/icon/details_close.png";
      oTable.fnOpen( nTr, fnFormatDetails(oTable, nTr), 'details' );
    }
  });


  /* first column must be prefixed with the open/close icon */
  var aoColumns = [
    {
      'bVisible':false
    },
    {
      "mRender": function(data, type, full) {
        return '<img src="admin/themes/default/icon/details_open.png"> <label><input type="checkbox" data-user_id="'+full[0]+'"> '+data+'</label>';
      }
    }
  ];

  for (i=2; i<jQuery("#userList thead tr th").length; i++) {
    aoColumns.push(null);
  }

  var oTable = jQuery('#userList').dataTable({
    "iDisplayLength": 10,
    "bDeferRender": true,
    "bProcessing": true,
    "bServerSide": true,
    "sAjaxSource": "admin/user_list_backend.php",
    "fnDrawCallback": function( oSettings ) {
      jQuery("#userList input[type=checkbox]").each(function() {
        var user_id = jQuery(this).data("user_id");
        jQuery(this).prop('checked', (selection.indexOf(user_id) != -1));
      });
    },
    "aoColumns": aoColumns
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
      user_id: selection
    };

    switch (action) {
      case 'delete':
        if (!jQuery("input[name=confirm_deletion]").is(':checked')) {
          alert(missingConfirm);
          return false;
        }
        method = 'pwg.users.delete';
        data.pwg_token = pwg_token;
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
        oTable.fnDraw();
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

{literal}
<style>
.dataTables_wrapper, .dataTables_info {clear:none;}
table.dataTable {clear:right;padding-top:10px;}
.dataTable td img {margin-bottom: -6px;margin-left: -6px;}
.bulkAction {margin-top:10px;}
#addUserForm p {margin-left:0;}
#applyActionBlock .actionButtons {margin-left:0;}
span.infos, span.errors {background-image:none; padding:2px 5px; margin:0;border-radius:5px;}

.userProperties {max-width:850px;}
.userProperties fieldset {border-width:0; border-top-width:1px;}
.userProperties fieldset legend {margin-left:-20px;padding-left:0;}
.userProperty {width:220px;float:left;margin-bottom:15px;}
</style>
{/literal}

<div class="titrePage">
  <h2>{'User list'|@translate}</h2>
</div>

<p class="showCreateAlbum" id="showAddUser">
  <a href="#" id="addUser">{'Add a user'|translate}</a>
  <span class="infos" style="display:none"></span>
</p>

<form id="addUserForm" style="display:none" method="post" name="add_user" action="{$F_ADD_ACTION}">
  <fieldset>
    <legend>{'Add a user'|@translate}</legend>

    <p>
      <strong>{'Username'|translate}</strong><br>
      <input type="text" name="username" maxlength="50" size="20">
    </p>

    <p>
      <strong>{'Password'|translate}</strong><br>
      <input type="{if $Double_Password}password{else}text{/if}" name="password">
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

  <div id="forbidAction"{if count($selection) != 0} style="display:none"{/if}>{'No user selected, no action possible.'|@translate}</div>
  <div id="permitAction"{if count($selection) == 0} style="display:none"{/if}>

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
      <option value="theme">{'Interface theme'|@translate}</option>
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
      <input size="4" maxlength="3" type="text" name="nb_image_page" value="{$NB_IMAGE_PAGE}">
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
      <input type="text" size="3" maxlength="2" name="recent_period" value="{$RECENT_PERIOD}">
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
      <span class="infos" style="display:none">&#x2714; Users modified</span>
    </p>

  </div> {* #permitAction *}
</fieldset>

</form> 
