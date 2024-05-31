

{combine_script id='common' load='header' require='jquery' path='admin/themes/default/js/common.js'}

{combine_script id='jquery.selectize' load='header' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}

{combine_script id='jquery.ui.slider' require='jquery.ui' load='header' path='themes/default/js/ui/minified/jquery.ui.slider.min.js'}
{combine_css path="themes/default/js/ui/theme/jquery.ui.slider.css"}

{combine_script id='jquery.confirm' load='header' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}

{combine_script id='jquery.tipTip' load='header' path='themes/default/js/plugins/jquery.tipTip.minified.js'}

{combine_css path="admin/themes/default/fontello/css/animation.css" order=10} {* order 10 is required, see issue 1080 *}

{footer_script}

/* Translates */
const title_msg = '{'Are you sure you want to delete the user "%s"?'|@translate|escape:'javascript'}';
const are_you_sure_msg  = '{'Are you sure?'|@translate|@escape:'javascript'}';
const confirm_msg = '{'Yes, I am sure'|@translate|@escape}';
const cancel_msg = '{'No, I have changed my mind'|@translate|@escape}';
const str_and_others_tags = '{'and %s others'|@translate|escape:javascript}';
const missingConfirm = "{'You need to confirm deletion'|translate|escape:javascript}";
const missingUsername = "{'Please, enter a login'|translate|escape:javascript}";
const fieldNotEmpty = "{'Name field must not be empty'|@translate|escape:javascript}"
const noMatchPassword = "{'The passwords do not match'|@translate|escape:javascript}";
const missingField = "{'Please complete all fields'|@translate|escape:javascript}";
const passwordUpdated = "{'Password updated'|@translate|escape:javascript}";
const passwordCopied = "{'Password copied'|@translate|escape:javascript}";
const copyPassword = "{'Copy password'|@translate|escape:javascript}";
const mailSentAt = "{'Mail sent to %s [%s].'|@translate|escape:javascript}";
const errorMailSent = "{'Error sending email'|@translate|escape:javascript}";
const cannotSendMail = "{'Cannot send an email to this user because he doesn\'t have an email address'|@translate|escape:javascript}";
const mainUserContinue = "{'You are about to set %s as main user instead of %s, do you wish to continue ?'|@translate|escape:javascript}";
const mainUserRewrite = "{'To be sure, please rewrite the word “%s” below'|@translate|escape:javascript}";
const mainUserValidate = "{'You can now change the main user from %s to %s.'|@translate|escape:javascript}";
const mainUserSuccess = "{'%s is the new main user'|@translate|escape:javascript}";
const mainUserStr = "{'Main user'|@translate|escape:javascript}";
const mainAskWebmaster = "{'You are not authorised to change the main user, please ask your webmaster'|@translate|escape:javascript}";
const mainUserSet = "{'Set as main user'|@translate|escape:javascript}";
const mainUserUpgradeWebmaster = "{'This user must first be defined as the webmaster before it can be upgraded to the main user'|@translate|escape:javascript}";
const errorStr = "{'an error happened'|@translate|escape:javascript}";
const copyLinkStr = "{'Copied link'|@translate|escape:javascript}";
const cantCopy = "{'You cannot copy the password if the connection to this site is not secure.'|@translate|escape:javascript}";

const registered_str = '{"Registered"|@translate|escape:javascript}';
const last_visit_str = '{"Last visit"|@translate|escape:javascript}';
const dates_infos = '{'between %s and %s'|translate|escape:javascript}'
const hide_str = '{'Hide'|@translate|escape:javascript}';
const show_str = '{'Show'|@translate|escape:javascript}';
const user_added_str = '{'User %s added'|@translate|escape:javascript}';
const str_popin_update_btn = '{'Update'|@translate|escape:javascript}';
const filtered_users = '{'<b>%d</b> filtered users'|@translate|escape:javascript}';
const filtered_user = '{'<b>%d</b> filtered user'|@translate|escape:javascript}';
const history_base_url = "{$U_HISTORY}";

const status_to_str = {
  'webmaster': "{'user_status_webmaster'|translate}",
  'admin': "{'user_status_admin'|translate}",
  'normal': "{'user_status_normal'|translate}",
  'generic': "{'user_status_generic'|translate}",
  'guest': "{'user_status_guest'|translate}",
}

const view_selector = '{$view_selector}';
const pagination = '{$pagination}';

months = [
  "{'Jan'|@translate}",
  "{'Feb'|@translate}",
  "{'Mar'|@translate}",
  "{'Apr'|@translate}",
  "{'May'|@translate}",
  "{'Jun'|@translate}",
  "{'Jul'|@translate}",
  "{'Aug'|@translate}",
  "{'Sep'|@translate}",
  "{'Oct'|@translate}",
  "{'Nov'|@translate}",
  "{'Dec'|@translate}"
];

/* Template variables */
connected_user = {$connected_user};
connected_user_status = "{$connected_user_status}";
owner_id = {$owner};
owner_username = "{$owner_username}";
let groups_arr_name = [{$groups_arr_name}];
let groups_arr_id = [{$groups_arr_id}];
groups_arr = groups_arr_id.map((elem, index) => [elem, groups_arr_name[index]]);
guest_id = {$guest_id};
nb_days = "{'%d days'|@translate}";
//per page is too long for the popin
nb_photos = "{'%d photos'|@translate}";
nb_photos_per_page = "{'%d photos per page'|@translate}";
pwg_token = '{$PWG_TOKEN}';
has_group = "{$filter_group}";

let register_dates_str = '{$register_dates}';
let register_dates = register_dates_str.split(',');
{literal}
let groupOptions = groups_arr.map(x => ({value: x[0], label: x[1], isSelected: 0}));
{/literal}

/* Startup */
setupRegisterDates(register_dates);
selectionMode(false);
get_guest_info();
update_user_list();
update_selection_content();

$(".icon-help-circled").tipTip({
  'maxWidth':'700px',
  'fadeIn': '1000',
});

$(document).ready(function() {
  // Only webmaster can set admin or webmaster to others users
  if (connected_user_status !== 'webmaster') {
    $('select[name="status"] option[value="webmaster"], select[name="status"] option[value="admin"]').attr("disabled", true);
  }
  // We set the applyAction btn click event here so plugins can add cases to the list 
  // which is not possible if this JS part is in a JS file
  // see #1571 on Github
  jQuery("#applyAction").click(function() {
      let action = jQuery("select[name=selectAction]").prop("value");
      let method = 'pwg.users.setInfo';
      let data = {
          pwg_token: pwg_token,
          user_id: selection.map(x => x.id)
      };
      switch (action) {
          case 'delete':
              if (!($("#permitActionUserList .user-list-checkbox[name=confirm_deletion]").attr("data-selected") === "1")) {
                  alert(missingConfirm);
                  return false;
              }
              method = 'pwg.users.delete';
              break;
          case 'group_associate':
              method = 'pwg.groups.addUser';
              data.group_id = jQuery("#permitActionUserList select[name=associate]").prop("value");
              break;
          case 'group_dissociate':
              method = 'pwg.groups.deleteUser';
              data.group_id = jQuery("#permitActionUserList select[name=dissociate]").prop("value");
              break;
          case 'status':
              data.status = jQuery("#permitActionUserList select[name=status]").prop("value");
              break;
          case 'enabled_high':
              data.enabled_high = $("#permitActionUserList .user-list-checkbox[name=enabled_high_yes]").attr("data-selected") === "1" ? true : false;
              break;
          case 'level':
              data.level = jQuery("#permitActionUserList select[name=level]").val();
              break;
          case 'nb_image_page':
              data.nb_image_page = jQuery("#permitActionUserList input[name=nb_image_page]").val();
              break;
          case 'theme':
              data.theme = jQuery("#permitActionUserList select[name=theme]").val();
              break;
          case 'language':
              data.language = jQuery("#permitActionUserList select[name=language]").val();
              break;
          case 'recent_period':
              data.recent_period = recent_period_values[$('#permitActionUserList .period-select-bar .slider-bar-container').slider("option", "value")];;
              break;
          case 'expand':
              data.expand = $("#permitActionUserList .user-list-checkbox[name=expand_yes]").attr("data-selected") === "1" ? true : false;
              break;
          case 'show_nb_comments':
              data.show_nb_comments = $("#permitActionUserList .user-list-checkbox[name=show_nb_comments_yes]").attr("data-selected") === "1" ? true : false
              break;
          case 'show_nb_hits':
              data.show_nb_hits = $("#permitActionUserList .user-list-checkbox[name=show_nb_hits_yes]").attr("data-selected") === "1" ? true : false;
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
              jQuery("#applyActionBlock .infos").fadeOut();
          },
          success:function(data) {
              jQuery("#applyActionLoading").hide();
              jQuery("#applyActionBlock .infos").fadeIn();
              jQuery("#applyActionBlock .infos").css("display", "inline-block");
              update_user_list();
              if (action == 'delete') {
                  selection = [];
                  update_selection_content();
              }
          },
          error:function(XMLHttpRequest, textStatus, errorThrows) {
              jQuery("#applyActionLoading").hide();
          }
      });
      return false;
  });
});

{/footer_script}

{combine_script id='user_list' load='footer' path='admin/themes/default/js/user_list.js'}

{combine_script id='jquery.cookie' path='themes/default/js/jquery.cookie.js' load='footer'}

<div class="selection-mode-group-manager" style="right:30px">
  <label class="switch">
    <input type="checkbox" id="toggleSelectionMode">
    <span class="slider round"></span>
  </label>
  <p>{'Selection mode'|@translate}</p>
</div>


<div id="user-table">
  <div id="user-table-content">
    <div class="user-manager-header">

      <div class="UserViewSelector">
        <input type="radio" name="layout" class="switchLayout" id="displayCompact" {if $view_selector == 'compact'}checked{/if}/><label for="displayCompact"><span class="icon-th-large firstIcon tiptip" title="{'Compact View'|translate}"></span></label><input type="radio" name="layout" class="switchLayout tiptip" id="displayLine" {if $view_selector == 'line'}checked{/if}/><label for="displayLine"><span class="icon-th-list tiptip" title="{'Line View'|translate}"></span></label><input type="radio" name="layout" class="switchLayout" id="displayTile" {if $view_selector == 'tile'}checked{/if}/><label for="displayTile"><span class="icon-pause lastIcon tiptip" title="{'Tile View'|translate}"></span></label>
      </div>

      <div style="display:flex;justify-content:space-between; flex-grow:1;">
        <div style="display:flex; align-items: center;">
          <div class="not-in-selection-mode user-header-button add-user-button" style="margin: auto;">
            <label class="head-button-2 icon-plus-circled">
              <p>{'Add a user'|@translate}</p>
            </label>
          </div>

          <div class="not-in-selection-mode user-header-button" style="margin: auto;">
            <label class="head-button-2 icon-user-secret edit-guest-user-button">
              <p>{'Edit guest user'|@translate}</p>
            </label>
          </div>
          <div id="AddUserSuccess">
            <label class="icon-ok">
              <span>{'New user added'|@translate}</span><span class="icon-pencil edit-now">{'Edit'|@translate}</span>
            </label>
          </div>
          <div class="in-selection-mode">
            <div id="checkActions">
              <span>{'Select'|@translate}</span>
              <a href="#" id="selectAllPage">{'The whole page'|@translate}</a>
              <a href="#" id="selectSet">{'The whole set'|@translate}</a><span class="loading" style="display:none"><img src="themes/default/images/ajax-loader-small.gif"></span>
              <a href="#" id="selectNone">{'None'|@translate}</a>
              <a href="#" id="selectInvert">{'Invert'|@translate}</a>
              <span id="selectedMessage"></span>
            </div>
          </div>
        </div>
        <div style="display:flex; width: 270px;">
        </div>
      </div>
      <div class="not-in-selection-mode" style="width: 264px; height:2px">
      </div>
    </div>
    <div class="filtered-users"></div>
    <div class="advanced-filter-btn icon-filter">
      <span>{'Filters'|@translate}</span>
      <span class="filter-counter"></span>
    </div>
    <div id='search-user'>
        <div class='search-info'> </div>
          {*This input (#user_search2) is used to bait the chrome autocomplete tool. It is hidden in navigator and is not meant to be seen.*}
          <input id="user_search2" class='search-input2' type='text' placeholder='{'Search'|@translate}'> 
          <span class='icon-search search-icon'> </span>
          <span class="icon-cancel search-cancel"></span>
          <input id="user_search" class='search-input' type='text' placeholder='{'Search'|@translate}'>
        </div>
    <div class="advanced-filter">
      <div class="advanced-filter-header">
        <span class="advanced-filter-title">{'Advanced filters'|@translate}</span>
        <span class="advanced-filter-close icon-cancel"></span>
      </div>
      <div class="advanced-filter-container">
      <div class="advanced-filter-status advanced-filter-item">
          <label class="advanced-filter-item-label">{'Status'|@translate}</label>
          <div class="advanced-filter-select-container advanced-filter-item-container">
            <select class="user-action-select advanced-filter-select" name="filter_status">
              <option value="" label="" selected></option>
              {html_options options=$pref_status_options}
            </select>
          </div>
        </div>
        <div class="advanced-filter-level advanced-filter-item">
          <label class="advanced-filter-item-label">{'Privacy level'|@translate}</label>
          <div class="advanced-filter-select-container advanced-filter-item-container">
            <select class="user-action-select advanced-filter-select" name="filter_level" size="1">
              <option value="" label="" selected></option>
              {html_options options=$level_options}
            </select>
          </div>
        </div>
        <div class="advanced-filter-group advanced-filter-item">
          <label class="advanced-filter-item-label">{'Group'|@translate}</label>
          <div class="advanced-filter-select-container advanced-filter-item-container">
            <select class="user-action-select advanced-filter-select" name="filter_group">
              <option value="" label="" selected></option>
              {html_options options=$association_options}
            </select>
          </div>
        </div>
        <div class="advanced-filter-date advanced-filter-item">
          <div class="advanced-filter-date-title" style="display:flex">
            <span class="advanced-filter-item-label">{'Registered'|@translate}</span>
            <span class='dates-infos'></span>
          </div>
          <div class="dates-select-bar">
              <div class="slider-bar-wrapper">
                <div class="slider-bar-container"></div>
              </div>
            </div>
        </div>
      </div>
    </div>
    <div class="user-container-header">
      <!-- edit / select -->
      <div class="user-header-col user-header-select no-flex-grow">
      </div>
      <!-- icon -->
      <div class="user-header-col user-header-initials no-flex-grow">
      </div>
      <!-- username -->
      <div class="user-header-col user-header-username">
        <span id="usr-list-user">{'Username'|@translate} <span id="icon-usr-list-user" class="icon-up" style="display: none;"></span></span>
      </div>
      <!-- status -->
      <div class="user-header-col user-header-status">
        <span>{'Status'|@translate}</span>
      </div>
      <!-- email adress -->
      <div class="user-header-col user-header-email not-in-selection-mode">
        <span>{'Email Adress'|@translate}</span>
      </div>
      {* <!-- groups -->
      <div class="user-header-col user-header-groups">
        <span>{'Groups'|@translate}</span>
      </div> *}
      <!-- registration date -->
      <div class="user-header-col user-header-registration">
        <span id="usr-list-registered">{'Registered'|@translate} <span id="icon-usr-list-registered" class="icon-up"></span></span>
      </div>
       <!-- groups -->
       <div class="user-header-col user-header-groups">
       <span>{'Groups'|@translate}</span>
     </div>
    </div>
    <div class="user-update-spinner icon-spin6 animate-spin"></div>
    <div class="user-container-wrapper">
    </div>
    <!-- Pagination -->
    <div class="user-pagination">
      <div class="pagination-per-page">
        <span class="thumbnailsActionsShow" style="font-weight: bold;">{'Display'|@translate}</span>
        <a id="pagination-per-page-5">5</a>
        <a id="pagination-per-page-10">10</a>
        <a id="pagination-per-page-25">25</a>
        <a id="pagination-per-page-50">50</a>
      </div>

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
    </div>
  </div>
  <div id="selection-mode-block" class="in-selection-mode tag-selection" style="width: 250px; min-width:250px;display: block;position:relative">
    <div class="user-selection-content">
      <div class="selection-mode-ul">
        <p>{'Your selection'|@translate}</p>
        <div class="user-selected-list">
        </div>
        <div class="selection-other-users"></div>
      </div>
      <fieldset id="action">
        <legend>{'Action'|@translate}</legend>

        <div id="forbidAction">{'No users selected, no actions possible.'|@translate}</div>
        <div id="permitActionUserList" style="display:block">

          <div class="user-action-select-container">
            <select class="user-action-select" name="selectAction">
              <option value="-1">{'Choose an action'|@translate}</option>
              <optgroup label="Actions">
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
              </optgroup>
            </select>
          </div>
          {* delete *}
          <div id="action_delete" class="bulkAction">
            <div class="user-list-checkbox" name="confirm_deletion">
              <span class="select-checkbox">
                <i class="icon-ok"></i>
              </span>
              <span class="user-list-checkbox-label">{'Are you sure?'|@translate}</span>
            </div>
          </div>

          {* status *}
          <div id="action_status" class="bulkAction">
            <div class="user-action-select-container">
              <select class="user-action-select" name="status">
                {html_options options=$pref_status_options selected=$pref_status_selected}
              </select>
            </div>
          </div>

          {* group_associate *}
          <div id="action_group_associate" class="bulkAction">
            <div class="user-action-select-container">
              <select class="user-action-select" name="associate">
                {html_options options=$association_options}
              </select>
            </div>
          </div>

          {* group_dissociate *}
          <div id="action_group_dissociate" class="bulkAction">
            <div class="user-action-select-container">
              <select class="user-action-select" name="dissociate">
                {html_options options=$association_options}
              </select>
            </div>
          </div>

          {* enabled_high *}
          <div id="action_enabled_high" class="bulkAction yes_no_radio">
            <span class="user-list-checkbox" name="enabled_high_yes">
              <span class="select-checkbox">
                <i class="icon-ok"></i>
              </span>
              <span class="user-list-checkbox-label">{'Yes'|@translate}</span>
            </span>
            <span class="user-list-checkbox" data-selected="1" name="enabled_high_no">
              <span class="select-checkbox">
                <i class="icon-ok"></i>
              </span>
              <span class="user-list-checkbox-label">{'No'|@translate}</span>
            </span>
          </div>

          {* level *}
          <div id="action_level" class="bulkAction">
            <div class="user-action-select-container">
              <select class="user-action-select" name="level" size="1">
                {html_options options=$level_options selected=$level_selected}
              </select>
            </div>
          </div>

          {* nb_image_page *}
          <div id="action_nb_image_page" class="bulkAction">
            <div class="user-property-label photos-select-bar">{'Photos per page'|translate}
              <br/>
              <span class="nb-img-page-infos"></span>
              <div class="slider-bar-wrapper">
                <div class="slider-bar-container"></div>
              </div>
              <input name="nb_image_page" />
            </div>
          </div>

          {* theme *}
          <div id="action_theme" class="bulkAction">

            <div class="user-action-select-container">
              <select class="user-action-select" name="theme" size="1">
                {html_options options=$theme_options selected=$theme_selected}
              </select>
            </div>
          </div>

          {* language *}
          <div id="action_language" class="bulkAction">
            <div class="user-action-select-container">
              <select class="user-action-select" name="language" size="1">
                {html_options options=$language_options selected=$language_selected}
              </select>
            </div>
          </div>

          {* recent_period *}
          <div id="action_recent_period" class="bulkAction">
            <div class="user-property-label period-select-bar">{'Recent period'|translate}
              <br />
              <span class="recent_period_infos"></span>
              <div class="slider-bar-wrapper">
                <div class="slider-bar-container"></div>
              </div>
            </div>
          </div>

          {* expand *}
          <div id="action_expand" class="bulkAction yes_no_radio">
            <span class="user-list-checkbox" name="expand_yes">
              <span class="select-checkbox">
                <i class="icon-ok"></i>
              </span>
              <span class="user-list-checkbox-label">{'Yes'|@translate}</span>
            </span>
            <span class="user-list-checkbox" data-selected="1" name="expand_no">
              <span class="select-checkbox">
                <i class="icon-ok"></i>
              </span>
              <span class="user-list-checkbox-label">{'No'|@translate}</span>
            </span>
          </div>

          {* show_nb_comments *}
          <div id="action_show_nb_comments" class="bulkAction yes_no_radio">
            <span class="user-list-checkbox" name="show_nb_comments_yes">
              <span class="select-checkbox">
                <i class="icon-ok"></i>
              </span>
              <span class="user-list-checkbox-label">{'Yes'|@translate}</span>
            </span>
            <span class="user-list-checkbox" data-selected="1" name="show_nb_comments_no">
              <span class="select-checkbox">
                <i class="icon-ok"></i>
              </span>
              <span class="user-list-checkbox-label">{'No'|@translate}</span>
            </span>
          </div>

          {* show_nb_hits *}
          <div id="action_show_nb_hits" class="bulkAction yes_no_radio">
            <span class="user-list-checkbox" name="show_nb_hits_yes">
              <span class="select-checkbox">
                <i class="icon-ok"></i>
              </span>
              <span class="user-list-checkbox-label">{'Yes'|@translate}</span>
            </span>
            <span class="user-list-checkbox" data-selected="1" name="show_nb_hits_no">
              <span class="select-checkbox">
                <i class="icon-ok"></i>
              </span>
              <span class="user-list-checkbox-label">{'No'|@translate}</span>
            </span>
          </div>

          <p id="applyActionBlock" style="display:none" class="actionButtons">
            <input id="applyAction" class="submit" type="submit" value="{'Apply action'|@translate}" name="submit"> <span id="applyOnDetails"></span></input>
            <span id="applyActionLoading" style="display:none"><img src="themes/default/images/ajax-loader-small.gif"></span>
            <br />
            <span class="infos icon-ok icon-green" style="display:inline-block;display:none;max-width:100%;margin:0;margin-top:30px;min-height:0;">{'Users modified'|translate}</span>
          </p>
        </div> {* #permitActionUserList *}
      </fieldset>
    </div>
  </div>
</div>

<!-- User container template -->
<div id="template">
  <div class="user-container">
  <!-- edit-v1 -->
    <div class="user-col user-container-select tmp-select in-selection-mode user-first-col no-flex-grow">
      <div class="user-container-checkbox user-list-checkbox" name="select_container">
        <span class="select-checkbox">
          <i class="icon-ok"></i>
        </span>
      </div>
    </div>
    <div class="user-col user-container-edit tmp-edit not-in-selection-mode user-first-col no-flex-grow">
      <span class="icon-pencil"></span>
    </div>
    <div class="user-col user-container-initials no-flex-grow">
      <div class="user-container-initials-wrapper">
        <span><!-- initials --></span>
      </div>
    </div>
    <div class="user-col user-container-username">
      <span><!-- name --></span>
    </div>
    <div class="user-col user-container-status">
      <span><!-- status --></span>
    </div>
    <div class="user-col user-container-email not-in-selection-mode">
      <span><!-- email --></span>
    </div>
    {* <div class="user-col user-container-groups">
      <!-- groups -->
    </div> *}
    <div class="user-col user-container-registration">
      <div>
        {* <span class="icon-clock registration-clock"></span> *}
        <div class="user-container-registration-info-wrapper">
          {* <span class="user-container-registration-date"><b><!-- date DD/MM/YY --></b></span>
          <span class="user-container-registration-time"><!-- time HH:mm:ss --></span> *}
          <span class="user-container-registration-date-since"><!-- date_since --></span>
        </div>
      </div>
    </div>
    <div class="user-col user-container-groups">
      <!-- groups -->
    </div>
  </div>
  <span class="user-groups group-primary"></span>
  <span class="user-groups group-bonus"></span>
  <div class="user-selected-item">
    <a class="icon-cancel"></a>
    <p></p>
  </div>
</div>

<div id="UserList" class="UserListPopIn">

  <div class="UserListPopInContainer">

    <a class="icon-cancel CloseUserList"></a>
    <div class="summary-properties-update-container">
      <div class="summary-properties-container">
        <div class="summary-container">
          <div class="edit-user-icons">
            <span class="icon-king tiptip" id="who_is_the_king" title="plg is the king ?"></span>
            <span class="delete-user-button icon-trash-1"></span>
          </div>
          <div class="user-property-initials">
            <div>
              <span class="icon-blue"><!-- Initials (JP) --></span>
            </div>
          </div>
          <div class="user-property-username">
            <span class="edit-username-title"><!-- Name (Jessy Pinkman) --></span>
            <span class="edit-username-specifier"><!-- You specifire (you) --></span>
            <span class="edit-username icon-pencil"></span>
          </div>
          <div class="user-property-password-container">
            <div class="user-property-password edit-password">
              <p class="user-property-button button-edit-password-icon head-button-2"><span class="icon-key user-edit-icon"> </span>{'Password'|@translate}</p>
            </div>
            <div class="user-property-permissions">
              <a href="#" ><p class="user-property-button head-button-2"> <span class="icon-lock user-edit-icon"> </span>{'Permissions'|@translate}</p></a>
            </div>
            <div class="user-stats">
              <div class="user-property-history">
                <a href="" ><p class="user-property-button head-button-2"> <span class="icon-signal user-edit-icon"> </span>{'Visit history'|@translate}</p></a>
              </div>
            </div>
          </div>
          <div class="user-property-register-visit">
            <div>
              <span class="edit-user-register-label">{'Registered'|@translate}</span>
              <span class="user-property-register"><!-- Registered date XX/XX/XXXX --></span>
            </div>
            <div>
              <span class="edit-user-lastvisit-label">{'Last visit'|@translate}</span>
              <span class="user-property-last-visit"><!-- Last Visit date XX/XX/XXXX --></span>
            </div>
          </div>
        </div>
        <div class="edit-user-tab">
          <div class="edit-user-tab-title">
            <p class="edit-user-tabsheet selected tiptip" id="name_tab_properties" title="{'Properties'|@translate}">{'Properties'|@translate}</p>
            <p class="edit-user-tabsheet tiptip" id="name_tab_preferences" title="{'Preferences'|@translate}">{'Preferences'|@translate}</p>
            {* <p class="edit-user-tabsheet tiptip" id="name_tab_notifications" title="{'Notifications'|@translate}">{'Notifications'|@translate}</p> *}
          </div>
          <div class="edit-user-slides">
            <!-- Pop in tabs 1 Properties -->
            <div class="properties-container" id="tab_properties">
              <div class="user-property-email">
                <p class="user-property-label">{'Email Adress'|@translate}</p>
                <input type="text" class="user-property-input" value="contact@jessy-pinkman.com" disabled="false" />
              </div>
              <div class="user-property-status">
                <p class="user-property-label">{'Status'|@translate}
                  <span class="icon-help-circled" title="<div class='tooltip-status-content'>
                    <div class='tooltip-status-row'><span class='tooltip-col1'>{'user_status_webmaster'|translate}</span><span class='tooltip-col2'>{'Has access to all administration functionnalities. Can manage both configuration and content.'|translate}</span></div>
                    <div class='tooltip-status-row'><span class='tooltip-col1'>{'user_status_admin'|translate}</span><span class='tooltip-col2'>{'Has access to administration. Can only manage content: photos/albums/users/tags/groups.'|translate}</span></div>
                    <div class='tooltip-status-row'><span class='tooltip-col1'>{'user_status_normal'|translate}</span><span class='tooltip-col2'>{'No access to administration, can see private content with appropriate permissions.'|translate}</span></div>
                    <div class='tooltip-status-row'><span class='tooltip-col1'>{'user_status_generic'|translate}</span><span class='tooltip-col2'>{'Can be shared by several individuals without conflict (they cannot change the password).'|translate}</span></div>
                    <div class='tooltip-status-row'><span class='tooltip-col1'>{'user_status_guest'|translate}</span><span class='tooltip-col2'>{'Equivalent to deactivation. The user is still in the list, but can no longer log in.'|translate}</span></div>
                  </div">
                  </span>
                </p>
                <div class="user-property-select-container">
                  <select name="status" class="user-property-select">
                    <option value="webmaster">{'user_status_webmaster'|@translate}</option>
                    <option value="admin">{'user_status_admin'|@translate}</option>
                    <option value="normal">{'user_status_normal'|@translate}</option>
                    <option value="generic">{'user_status_generic'|@translate}</option>
                    <option value="guest">{'user_status_guest'|@translate} ({'Deactivated'|@translate})</option>
                  </select>
                </div>
              </div>
              <div class="user-property-level">
                <p class="user-property-label">{'Privacy level'|@translate}</p>
                <div class="user-property-select-container">
                  <select name="privacy" class="user-property-select">
                    <option value="0">{'Level 0'|@translate}</option>
                    <option value="1">{'Level 1'|@translate}</option>
                    <option value="2">{'Level 2'|@translate}</option>
                    <option value="4">{'Level 4'|@translate}</option>
                    <option value="8">{'Level 8'|@translate}</option>
                  </select>
                </div>
              </div>
              <div class="user-property-group-container">
                <p class="user-property-label">{'Groups'|@translate}</p>
                <div class="user-property-select-container user-property-group">
                  <select class="user-property-select" data-selectize="groups"
                    placeholder="{'Select groups or type them'|translate}" name="group_id[]" multiple
                    style="box-sizing:border-box;"></select>
                  <p class="user-property-group-text">
                    {* {'Some of these groups give access to notifications. To find out more, go to the Notifications tab.'|@translate} *}
                  </p>
                </div>
              </div>

              <div class="user-list-checkbox" name="hd_enabled" style="margin-bottom: 35px;">
                <span class="select-checkbox">
                  <i class="icon-ok"></i>
                </span>
                <span class="user-list-checkbox-label">{'High definition enabled'|translate}</span>
              </div>
            </div>
            <!-- Pop in tabs 2 Preferences -->
            <div class="preferencies-container" id="tab_preferences">
              <div class="user-property-label photos-select-bar">{'Photos per page'|translate}
                <span class="nb-img-page-infos"></span>
                <div class="slider-bar-wrapper">
                  <div class="slider-bar-container"></div>
                </div>
                <input name="recent_period" />
              </div>
              <div class="user-property-theme">
                <p class="user-property-label">{'Theme'|@translate}</p>
                <div class="user-property-select-container">
                  <select name="privacy" class="user-property-select">
                    {html_options options=$theme_options selected=$theme_selected}
                  </select>
                </div>
              </div>
              <div class="user-property-lang">
                <p class="user-property-label">{'Language'|@translate}</p>
                <div class="user-property-select-container">
                  <select name="privacy" class="user-property-select">
                    {html_options options=$language_options selected=$language_selected}
                  </select>
                </div>
              </div>
              <div class="user-property-label period-select-bar">{'Recent period'|translate}
                <span class="recent_period_infos"></span>
                <div class="slider-bar-wrapper">
                  <div class="slider-bar-container"></div>
                </div>
              </div>

              <div class="user-group-checkbox">
                <div class="user-list-checkbox" name="expand_all_albums">
                  <span class="select-checkbox">
                    <i class="icon-ok"></i>
                  </span>
                  <span class="user-list-checkbox-label">{'Expand all albums'|translate}</span>
                </div>
                <div class="user-list-checkbox" name="show_nb_comments">
                  <span class="select-checkbox">
                    <i class="icon-ok"></i>
                  </span>
                  <span class="user-list-checkbox-label">{'Show number of comments'|translate}</span>
                </div>
                <div class="user-list-checkbox" name="show_nb_hits">
                  <span class="select-checkbox">
                    <i class="icon-ok"></i>
                  </span>
                  <span class="user-list-checkbox-label">{'Show number of hits'|translate}</span>
                </div>
              </div>

            </div>
             <!-- Pop in tabs 3 Notifications WIP-->
            {* <div class="notifications-container" id="tab_notifications">
              <p style="margin: 0;">Notifications tab WIP</p>
            </div> *}
          </div>
        </div>
      </div>
      <div class="update-container">
        <span class="close-update-button icon-cancel-circled">{'Close'|@translate}</span>
        <p>
          <span class="update-user-success icon-green icon-ok">{'User updated'|@translate}</span>
          <span class="update-user-fail icon-cancel"></span>
          <span class="update-user-button"><i class='icon-floppy'></i>{'Update'|@translate}</span>
        </p>
      </div>
    </div>
  </div>

  {* Modal edit username in pop in User *}
  <div class="user-property-username-change">
    <div class="user-property-username-change-content">
      
      <div class="user-property-username-change-input">
        <div class="summary-input-container">
          <input class="user-property-input user-property-input-username" value=""
            placeholder="{'Username'|@translate}" />
        </div>
        <div class="group-button">
          <span class="edit-username-cancel">{'Cancel'|@translate}</span>
          <span class="icon-floppy edit-username-validate">{'Validate'|@translate}</span>
        </div>
      </div>

      <div class="edit-username-success" style="display: none;">
        <div class="update-username-success icon-green">
          <span class="icon-ok"></span>
          <span>{'Username successfully modified'|@translate}</span>
        </div>
        <p class="edit-username-success-ok"><span class="icon-button icon-ok" id="close_username_success">{'Ok'|@translate}</span></p>
      </div>

    </div>
  </div>
  {* Modal edit password in pop in User *}
  <div class="user-property-password-change">
    <div class="user-property-password-change-content">

      <div class="user-property-password-choice">
        <div class="password-choice-content">
          <p class="head-button-2" id="copy_password_link"><span class="icon-link-1"></span> {'Copy the password link'|@translate}</p>
          <p class="head-button-2" id="send_password_link"><span class="icon-mail-alt"></span> {'Resend password link'|@translate}</p>
          <p class="head-button-2" id="edit_modal_password"><span class="icon-pencil"></span> {'Change password'|@translate}</p>
        </div>
        <p class="edit-password-cancel">{'Cancel'|@translate}</p>
      </div>

      <div class="user-property-password-change-inputs" style="display: none;">
        <div class="summary-input-container">
          <div class="user-property-input-icon" style="margin-bottom: 10px;">
            <input class="user-property-input user-property-input-password" value=""
              placeholder="{'New password'|@translate}" type="password" id="edit_user_password" />
            <span class="icon-eye icon-show-password"></span>
          </div>
          <div class="user-property-input-icon">
            <input class="user-property-input user-property-input-password-conf" value=""
              placeholder="{'Confirm Password'|@translate}" type="password" id="edit_user_conf_password" />
            <span class="icon-eye icon-show-password"></span>
          </div>
        </div>
        <div class="EditUserGenPassword">
          <span class="icon-dice-solid"></span><span>{'Generate random password'|@translate}</span>
        </div>
        <div class="EditUserErrors icon-cancel">
        </div>
        <div class="group-button">
          <span class="edit-password-cancel">{'Cancel'|@translate}</span>
          <span class="icon-floppy edit-password-validate">{'Validate'|@translate}</span>
        </div>
      </div>

      <div class="edit-password-success" id="edit_password_success_change" style="display: none;">
        <div class="update-password-success icon-green">
          <span class="icon-ok" id="password_msg_success">{'Password updated'|@translate}</span>
        </div>
        <p class="user-property-button head-button-2" id="copy_password"><span class="icon-docs button-copy-pass">{'Copy password'|@translate}</span></p>
        <p class="edit-password-success-ok"><span class="icon-button icon-ok" id="close_password_success">{'Ok'|@translate}</span></p>
      </div>

      <div class="edit-password-success" id="edit_password_result_mail" style="display: none;">
        <div class="update-password-success icon-green" id="result_send_mail">
          <span class="icon-ok" id="icon_password_msg_result_mail"></span>
          <span id="password_msg_result_mail">text</span>
        </div>
        <p class="edit-password-success-ok"><span class="icon-button icon-ok" id="close_password_mail_close">{'Ok'|@translate}</span></p>
      </div>

      <div class="edit-password-success" id="edit_password_result_mail_copy" style="display: none;">
        <div class="edit-password-success-reset-link">
          <input class="edit-password-success-input" id="result_send_mail_copy_input" />
          <span class="icon-docs" id="result_send_mail_copy_btn"></span>
        </div>
        <div class="update-password-success icon-green" id="result_send_mail_copy">
          <span class="icon-ok" id="result_send_mail_copy_icon"></span>
          <span id="result_send_mail_copy_msg">{'Copied link'|@translate}</span>
        </div>
        <p class="edit-password-success-ok"><span class="icon-button icon-ok" id="close_password_mail_send_close">{'Ok'|@translate}</span></p>
      </div>
      
    </div>
  </div>
  {* Modal edit main user in pop in User *}
  <div class="user-property-main-user-change">
    <div class="user-property-main-user-content">
      <div class="user-property-main-user-content-header">
        <span class="icon-king main-user-icon"></span>
        <span class="main-user-title">{'Changing the main user'|@translate}</span>
      </div>

      <div class="user-property-main-user-body">
        <div class="main-user-proceed">
          <span class="main-user-proceed-desc">{'You are about to set %s as main user instead of %s, do you wish to continue?'|@translate}</span>
          <div class="main-user-proceed-footer">
            <span class="user-property-main-user-cancel">{'Cancel'|@translate}</span>
            <span class="head-button-2 main-user-btn-proceed"><span class="icon-right">{'Yes, let\'s proceed'|@translate}</span></span>
          </div>
        </div>

        <div class="main-user-rewrite" style="display: none;">
          <span class="main-user-rewrite-desc">{'To be sure, please rewrite the word “%s” below'|@translate} :</span>
          <div class="main-user-rewrite-footer">
            <input type="text" id="main_user_rewrite" />
            <span id="main_user_rewrite_icon"></span>
          </div>
        </div>

        <div class="main-user-validate" style="display: none;">
          <span class="main-user-validate-desc">{'You can now change the main user from %s to %s.'|@translate}</span>
          <div class="main-user-validate-footer">
            <span class="user-property-main-user-cancel">{'Cancel'|@translate}</span>
            <span class="main-user-btn-validate"><span class="icon-floppy"></span> {'Validate'|@translate}</span>
          </div>
        </div>

        <div class="main-user-success" style="display: none;">
          <div class="update-main-user-success icon-green">
            <span class="icon-ok"></span>
            <span class="main-user-success-desc">{'%s is the new main user'|@translate}</span>
          </div>
          <span class="user-property-main-user-close"><span class="icon-button icon-ok" id="main_user_success_close">{'Ok'|@translate}</span></span>
        </div>
      </div>

    </div>
  </div>
</div>

<div id="GuestUserList" class="UserListPopIn">

  <div class="GuestUserListPopInContainer">

    <a class="icon-cancel CloseUserList CloseGuestUserList"></a>
    <div id="guest-msg" style="background-color:#B9E2F8;padding:5px;border-left:3px solid blue;display:flex;align-items:center;margin-bottom:30px">
      <span class="icon-info-circled-1" style="background-color:#B9E2F8;color:#26409D;font-size:3em"></span><span style="font-size:1.1em;color:#26409D;font-weight:bold;">{'Users not logged in will have these settings applied, these settings are used by default for new users'|@translate}</span>
    </div>
    <div class="summary-properties-update-container">
      <div class="summary-properties-container">
        <div class="summary-container">
          <div class="user-property-initials">
            <div>
              <span class="icon-blue"><i class="icon-user-secret"> </i></span>
            </div>
          </div>
          <div class="user-property-username">
            <span class="edit-username-title"><!-- name -> Jessy Pinkman --></span>
            <span class="edit-username-specifier"><!-- you specifier(you) --></span>
          </div>
          <div class="user-property-username-change">
            <div class="summary-input-container">
              <input class="user-property-input user-property-input-username" value="" placeholder="{'Username'|@translate}" />
            </div>
            <span class="icon-ok edit-username-validate"></span>
            <span class="icon-cancel-circled edit-username-cancel"></span>
          </div>
          <div class="user-property-password-container">
            <div class="user-property-password edit-password">
              <p class="user-property-button head-button-2 unavailable"><span class="icon-key user-edit-icon"></span>{'Change Password'|@translate}</p>
            </div>
            <div class="user-property-password-change">
              <div class="summary-input-container">
              <input class="user-property-input user-property-input-password" value="" placeholder="{'Password'|@translate}" />
              </div>
              <span class="icon-ok edit-password-validate"></span>
              <span class="icon-cancel-circled edit-password-cancel"></span>
            </div>
            <div class="user-property-permissions">
              <a href="admin.php?page=user_perm&user_id={$guest_id}"><p class="user-property-button head-button-2"><span class="icon-lock user-edit-icon"></span>{'Permissions'|@translate}</p></a>
            </div>
          </div>
        </div>
        
        <div class="guest-edit-user-tab">
          <div class="guest-edit-user-tab-title">
            <p class="guest-edit-user-tabsheet selected tiptip" id="name_guest_tab_properties" title="{'Properties'|@translate}">{'Properties'|@translate}</p>
            <p class="guest-edit-user-tabsheet tiptip" id="name_guest_tab_preferences" title="{'Preferences'|@translate}">{'Preferences'|@translate}</p>
          </div>

          <div class="guest-edit-user-slides">
            <div class="properties-container" id="guest_tab_properties">
              <div class="user-property-email">
                <p class="user-property-label">{'Email Adress'|@translate}</p>
                <input type="text" class="user-property-input" value="N/A" readonly />
              </div>
              <div class="user-property-status">
                <p class="user-property-label">{'Status'|@translate}</p>
                <div class="user-property-select-container notClickableBefore">
                  <select name="status" class="user-property-select notClickable">
                    <option value="guest">{'Guest'|@translate}</option>
                  </select>
                </div>
              </div>
              <div class="user-property-level">
                <p class="user-property-label">{'Privacy Level'|@translate}</p>
                <div class="user-property-select-container">
                  <select name="privacy" class="user-property-select">
                    <option value="0">{'Level 0'|@translate}</option>
                    <option value="1">{'Level 1'|@translate}</option>
                    <option value="2">{'Level 2'|@translate}</option>
                    <option value="4">{'Level 4'|@translate}</option>
                    <option value="8">{'Level 8'|@translate}</option>
                  </select>
                </div>
              </div>
              <div class="user-property-group-container">
                <p class="user-property-label">{'Groups'|@translate}</p>
                <div class="user-property-select-container user-property-group">
                  <select class="user-property-select" data-selectize="groups"
                    placeholder="{'Select groups or type them'|translate}" name="group_id[]" multiple
                    style="box-sizing:border-box;"></select>
                </div>
              </div>

              <div class="user-list-checkbox" name="hd_enabled">
                <span class="select-checkbox">
                  <i class="icon-ok"></i>
                </span>
                <span class="user-list-checkbox-label">{'High definition enabled'|translate}</span>
              </div>
            </div>

            <div class="preferences-container" id="guest_tab_preferences">
              <div class="user-property-label photos-select-bar">{'Photos per page'|translate}
                <span class="nb-img-page-infos"></span>
                <div class="slider-bar-wrapper">
                  <div class="slider-bar-container"></div>
                </div>
                <input name="recent_period" />
              </div>
              <div class="user-property-theme">
                <p class="user-property-label">{'Theme'|@translate}</p>
                <div class="user-property-select-container">
                  <select name="privacy" class="user-property-select">
                    {html_options options=$theme_options selected=$theme_selected}
                  </select>
                </div>
              </div>
              <div class="user-property-lang">
                <p class="user-property-label">{'Language'|@translate}</p>
                <div class="user-property-select-container">
                  <select name="privacy" class="user-property-select">
                    {html_options options=$language_options selected=$language_selected}
                  </select>
                </div>
              </div>
              <div class="user-property-label period-select-bar">{'Recent period'|translate}
                <span class="recent_period_infos">
                  <!-- 7 days -->
                </span>
                <div class="slider-bar-wrapper">
                  <div class="slider-bar-container"></div>
                </div>
              </div>

              <div class="user-list-checkbox" name="expand_all_albums">
                <span class="select-checkbox">
                  <i class="icon-ok"></i>
                </span>
                <span class="user-list-checkbox-label">{'Expand all albums'|translate}</span>
              </div>
              <div class="user-list-checkbox" name="show_nb_comments">
                <span class="select-checkbox">
                  <i class="icon-ok"></i>
                </span>
                <span class="user-list-checkbox-label">{'Show number of comments'|translate}</span>
              </div>
              <div class="user-list-checkbox" name="show_nb_hits">
                <span class="select-checkbox">
                  <i class="icon-ok"></i>
                </span>
                <span class="user-list-checkbox-label">{'Show number of hits'|translate}</span>
              </div>
            </div>
          </div>
        </div>
        
      </div>

      <div class="update-container">
        <span class="close-update-button icon-cancel-circled">{'Close'|@translate}</span>
        <p>
          <span class="update-user-success icon-green">{'User updated'|@translate}</span>
          <span class="update-user-fail  icon-cancel"></span>
          <span class="update-user-button"><i class='icon-floppy'></i>{'Update'|@translate}</span>
        </p>
      </div>

    </div>
  </div>
</div>

<div id="AddUser" class="UserListPopIn">
  <div class="AddUserPopInContainer">
    <a class="icon-cancel CloseUserList CloseAddUser"></a>
    
    <div class="AddIconContainer">
      <span class="AddIcon icon-blue icon-plus-circled"></span>
    </div>
    <div class="AddIconTitle">
      <span>{'Add a new user'|@translate}</span>
    </div>
    <div class="AddUserInputContainer">
      <label class="user-property-label AddUserLabelUsername">{'Username'|@translate}
        <input class="user-property-input" />
      </label>
    </div>

    <div class="AddUserInputContainer">
      <div class="AddUserPasswordWrapper">
        <label for="AddUserPassword" class="user-property-label AddUserLabelPassword">{'Password'|@translate}</label>
        <span id="show_password" class="icon-eye"></span>
      </div>
      <input id="AddUserPassword" class="user-property-input" type="password"/>

      <div class="AddUserGenPassword">
        <span class="icon-dice-solid"></span><span>{'Generate random password'|@translate}</span>
      </div>
    </div>

    <div class="AddUserInputContainer">
      <label class="user-property-label AddUserLabelEmail">{'Email'|@translate}
        <input class="user-property-input" />
      </label>
    </div>

    <div class="user-list-checkbox" name="send_by_email">
      <span class="select-checkbox">
        <i class="icon-ok"></i>
      </span>
      <span class="user-list-checkbox-label">{'Send connection settings by email'|translate}</span>
    </div>

    <div class="AddUserErrors  icon-cancel">
    </div>

    <div class="AddUserSubmit">
      <span class="icon-plus"></span><span>{'Add User'|@translate}</span>
    </div>

    <div class="AddUserCancel" style="display:none;">
      <span>{'Cancel'|@translate}</span>
    </div>
  </div>
</div>

<style>

.icon-help-circled {
  color: #777777 !important;
  cursor: help;
}

#show_password {
  position: absolute;
  left: 240px;
  top: 29px;
  z-index: 100; {*used to fix firefox auto fill input*}
}

/* general */
.no-flex-grow {
    flex-grow:0 !important;
}

#template {
    display:none;
}

#the_king {
  color: #F4AB4F;
  margin-left: 5px;
}

.can-change:hover {
  cursor: pointer;
}

.cannot-change:hover {
  cursor: default;
}

/* selection mode */

.user-selection-content {
	margin-top: 90px;
	padding: 5px;
}

#user-table #selection-mode-block{
  display:none;
  position: relative;
  width: 223px;
  top: -30px;
  min-height: 100%;
}

#forbidAction {
  padding:5px;
}
/* user header */

.user-manager-header {
	display: flex;
	flex-wrap: nowrap;
	align-items: center;
	overflow: hidden;
  padding-bottom:10px;
}


#AddUserSuccess {
  display:none;
  position: absolute;
  top:-135px;
  right:17px;
  font-weight:bold;
}

#AddUserSuccess label {
  padding: 10px;
  cursor: default;
}

#AddUserSuccess .edit-now {
  color: #3a3a3a;
  cursor: pointer;
  margin-left:10px;
}

.user-header-button {
  position:relative;
}

/* filters bar */

#user_search {
    width: 200px;
}

#user_search2 {
  position: absolute;
  top: -20000px;
}

.advanced-filter-date {
  width: auto;
}

/* Pagination */
.user-pagination {
    margin: 0;
    display: flex;
    padding: 0;
    justify-content: space-between;
    align-items: center;
}

.selected-pagination {
  background: #ffd2a1;
}

/* User Table */
#user-table {
    margin-left:30px;
    margin-top: 30px;
    display:flex;
    flex-wrap:nowrap;
    min-height: calc(100vh - 216px);

    position: relative;
}

#user-table-content {
    max-width:100%;
    flex-grow:1;
    display:flex;
    flex-direction:column;
    margin-right:30px;
}

.user-container-header {
    display:flex;
    text-align:left;
    font-size:1.1em;
    font-weight:bold;
    margin-top:20px;
    color:#9e9e9e;
}

.user-header-col {
    height:30px;
    flex-grow:1;
}

/* User Container */
.user-container {
    display:flex;
    width:100%;
    height:50px;

    font-weight:bold;
    border-radius:10px;
    margin-bottom:10px;
    transition: background-color 500ms linear;
    box-shadow: 0px 2px 2px #00000024;
}

.user-header-select,
.user-container-select,
.user-container-edit {
    width:40px;
}

.user-header-initials,
.user-container-initials {
    width:70px;
}

.user-header-username{
  width: 20%;
  max-width: 195px;
}
.user-container-username {
  width: 20%;
  max-width: 150px;

  white-space: nowrap;

  overflow: hidden;
  text-overflow: ellipsis;

  padding-right: 10px;
}

.user-container-username span {
  max-width: 100px;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-header-status,
.user-container-status {
    width:10%;
    max-width: 110px;
}

.user-header-email,
.user-container-email {
    width:20%;
    max-width: 220px;
    margin-right: 20px;
}
.user-container-email span {
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-header-groups,
.user-container-groups {
    width:20%;
    max-width: 900px;
    min-width: 100px;
}

.user-header-col.user-header-registration,
.user-col.user-container-registration {
  flex-grow: 0;
}

.user-groups .group-primary {
  width: 100px;
}

.user-header-registration,
.user-container-registration {
    width: 10% !important;
    max-width: 700px;
    min-width: 130px;
    margin-left: auto;
}

.user-col {
    text-align: left;
    padding: 0;
    display:flex;
    align-items:center;
    flex-grow:1;
}

.user-first-col {
    border-top-left-radius: 15%;
    border-bottom-left-radius: 15%;
    cursor:pointer;
}

.user-container-checkbox.user-list-checkbox {
    margin-bottom:0px;
}


.user-container-checkbox.user-list-checkbox .select-checkbox {
  background-color: #F3F3F3;
}

.user-container-checkbox.user-list-checkbox i {
    margin-left:7px;
}

.user-container-select {
    display:flex;
    justify-content:center;
    align-items:center;
}

.user-container-select span {
    font-size:1.5em;
    border: 1px solid #E6E6E6;
    border-radius:50%;
    background-color:#F3F3F3;
    width:27px;
}

.user-container-select span > i {
    display:none;
}

.user-container-edit {
  justify-content: center;
}

.user-container-edit span {
    font-weight:bold;
    font-size:1.5em;
    cursor:pointer;
    width:27px;
}

.user-container-initials-wrapper {
    padding-left:10px;
}

.user-container-initials-wrapper > span {
    border-radius:50%;
    padding:5px;
    width:40px;
    height:40px;
    display:inline-block;
    text-align:center;
    font-size:1.5em;
    line-height:1.9em;
}

.user-container-status {
    text-transform:capitalize;
}

.user-container-registration {
    width:15%;
}

.user-container-registration > div {
    display:flex;
}

.registration-clock {
    background:#E3E5E5;
    padding:5px;
    width:50%;
    height:50%;
    border-radius:30px;
    margin-right:5px;
    font-size:1.5em;
}

.user-container-registration-info-wrapper {
    display:flex;
    flex-direction:column;
}

.user-groups {
    margin-right: 5px;
    border-radius:9999px;
    padding: 10px 15px;
}

.group-primary {
    max-width:30%;
    text-overflow: ellipsis;
    overflow:hidden;
    white-space:nowrap;
}

/* User Edit Pop-in */
#UserList {
    font-size:1em;
}

#guest-msg {
  max-width: 835px;
}

.UserListPopIn{
    position: fixed;
    z-index: 100;
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%;
    overflow: auto; 
    background-color: rgba(0,0,0,0.7);
}

.UserListPopInContainer{
    display:block;
    position:absolute;
    left:50%;
    top: 50%;
    transform:translate(-50%, -48%);
    text-align:left;
    padding:30px;
    display:flex;
    width:745px;
    height: 630px;
}

.summary-properties-update-container {
    height:100%;
    display:flex;
    flex-direction:column;
    width: 100%;
}

.summary-properties-container {
    display:flex;
    flex-grow:1;
}

.summary-container {
    width:35%;
    display:flex;
    flex-direction:column;
    align-items:center;
    padding-right:30px;
}

.edit-user-tab,
.guest-edit-user-tab {
  width: 65%;
  padding-left: 30px;
  overflow-x: auto;
}

.edit-user-slides,
.guest-edit-user-slides {
  display: flex;
  overflow-x: auto;
  scroll-snap-type: x mandatory;
  scroll-behavior: smooth;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.edit-user-slides::-webkit-scrollbar,
.guest-edit-user-slides::-webkit-scrollbar {
    display: none;
}

.edit-user-slides > div,
.guest-edit-user-slides > div {
  scroll-snap-align: start;
  flex-shrink: 0;
  transform-origin: center center;
  transform: scale(1);
  transition: transform 0.5s;
  position: relative;
  width: 100%;
  margin-right: 5px;
  margin-top: 20px;
}

.UserListPopInContainer .update-container,
.GuestUserListPopInContainer .update-container {
  display: flex;
  justify-content: space-between;
  padding-top:20px;
}

/* general pop in rules */
.user-property-column-title {
    font-weight:bold;
    margin-bottom:15px;
    font-size:1.4em;
}

.user-property-column-title > p {
    margin:0;
}

.edit-user-tab-title {
  display: flex;
  /* justify-content: space-between; */
  gap: 30px;
  font-weight: bold;
  font-size: 14px;
}

.guest-edit-user-tab-title {
  display: flex;
  gap: 40px;
  font-weight: bold;
  font-size: 14px;
}

.edit-user-tab-title .edit-user-tabsheet,
.guest-edit-user-tab-title .guest-edit-user-tabsheet {
  padding-bottom: 5px;
  margin: 0;
  border-bottom: solid 4px transparent;
  transition: border-bottom 0.4s;
  transition: color 0.3s;
  text-decoration: none;
}
.edit-user-tab-title p:hover,
.guest-edit-user-tab-title p:hover {
  cursor: pointer;
}

.tab-with-plugin {
  max-width: 100px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.mores-plugins {
  cursor: pointer;
  position: relative;
  padding: 5px 10px;
}

.mores-plugins::after {
  content: "";
  position: absolute;
  top: 45%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: #f0f0f0;
  width: 0px;
  height: 0px;
  border-radius: 100%;
  transition: ease-in-out 0.2s;
  z-index: 0;
  opacity: 0;
  z-index: -1;
}

.mores-plugins:hover::after {
  width: 35px;
  height: 35px;
  opacity: 1;
}

.dropdown-mores-plugins {
  display: none;
  position: absolute;
  width: 150px;
  z-index: 1;
  top: 65px;
  right: 20px;
  background: linear-gradient(130deg, #ff7700 0%, #ffa744 100%);
  color: white;
  border-radius: 10px;
}

.dropdown-mores-plugins::after {
  content: " ";
  position: absolute;
  bottom: 99%;
  left: 78%;
  border-width: 5px;
  border-style: solid;
  border-color: transparent transparent #ff952a transparent;
}

#dropdown_mores_plugins_content {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

#dropdown_mores_plugins .selected,
#dropdown_mores_plugins .edit-user-tabsheet {
  border-bottom: unset !important;
}

#dropdown_mores_plugins .edit-user-tabsheet {
  padding: 5px 0;
  font-size: 13px;
  color: white;
}

#dropdown_mores_plugins .edit-user-tabsheet::before {
  content : "\002022";
  padding: 0 5px;
  opacity: 0;
  transition: opacity 0.05s, padding 0.05s;
}

#dropdown_mores_plugins .edit-user-tabsheet.selected::before {
  padding: 0 10px;
  opacity: 1;
}

#dropdown_mores_plugins .edit-user-tabsheet:hover {
  background-color: #00000012;
}

.user-property-label {
    color:#A4A4A4;
    font-weight:bold;
    font-size:1.1em;
    margin-bottom:5px;
}

.user-property-label span,
.dates-infos {
	color: #ff7700;
	font-weight: bold;
  margin-left: 5px;
}

.user-property-input-icon {
  display: flex;
  align-items: center;
  padding: 5px;
  color: #353535;
  background-color: #F3F3F3;
}

.user-property-input-icon .icon-show-password {
  font-size: 20px;
  cursor: pointer;
}

.user-property-input-icon .icon-show-password:hover {
  color:#ffa646;
}

.user-property-input-icon .user-property-input {
  background-color: transparent;
  padding: 0 0 0 5px !important;
  font-size: 16px;
}

.user-property-input-icon .user-property-input:hover {
  background-color: transparent;
}

.user-property-input {
    width: 100%;
    box-sizing:border-box;
    font-size:1.1em;
    padding:8px 16px;
    border:none;
}

{* .AddUserPopInContainer .user-property-input {
  background-color: #F3F3F3;
} *}

.user-property-button {
    margin-top:0;
    font-size:1.1em;
    margin-bottom:15px;
    cursor: ;
    border:none;
    margin-right: 0;
    font-weight: normal;
}

.button-edit-password-icon {
  cursor: pointer;
}

.user-property-password .user-property-button {
  display: flex;
  align-items: center;
}

.user-property-select {
    box-sizing: border-box;
    -webkit-appearance:none;
    border:none;
    width:100%;
    padding: 10px;
    font-size:1.1em;
}

.user-property-select-container::before {
  margin-top: 10px;
  margin-left: 418px;
}

.user-action-select-container {
  position:relative;
}

.user-list-checkbox {
    margin-bottom:15px;
}

.user-list-checkbox {
  user-select: none;
}

.user-list-checkbox i {
    margin-left:7px;
}

.user-list-checkbox-label {
    margin-left: 5px;
    vertical-align:top;
    font-size:14px;
    cursor:pointer;
}

/* summary section */
.edit-user-icons {
  width: 100%;
  display: flex;
  justify-content: space-between;
}

.edit-user-icons span::before{
  font-size: 16px;
}

.delete-user-button {
  cursor: pointer;
}

.user-property-initials {
    margin-bottom: 20px;
}

.user-property-initials i {
  margin-left: 5px;
}

.user-property-initials span{
    border-radius:50%;
    padding:5px;
    width:83px;
    height:83px;
    display:inline-block;
    text-align:center;
    font-size:4em;
    line-height:83px;
    font-weight:bold;
}

.user-property-username {
    font-weight:bold;
    margin-bottom:45px;
    height:30px;
    display: flex;
    gap: 5px;
    max-width: 250px;
}

.user-property-username-change {
    display: none;
    position: fixed;
    z-index: 100;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.7);
}

.user-property-username-change-content {
  display: block;
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -48%);
  text-align: left;
  padding: 40px;
  border-radius: 10px;
  width: 280px;
}

.user-property-username-change-content .group-button,
.user-property-password-change-content .group-button {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.user-property-username-change-content .summary-input-container {
  width: 100%;
  margin-bottom: 35px;
}

.user-property-password .icon-edit-pass {
  margin-left: auto;
  padding: 10px;
  background-color: #ffa744;
  color: #3c3c3c;
}

.user-property-password .icon-edit-pass:hover {
  background-color: #ff7700;
}

.user-property-password-change,
.user-property-main-user-change {
  display: none;
  position: fixed;
  z-index: 100;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.7);
}

.user-property-password-change-content {
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -48%);
  text-align: left;
  padding: 40px 40px 40px 40px;
  border-radius: 10px;
  width: 280px;
}

.user-property-password-change-content .summary-input-container {
  width: 100%;
}

.user-property-password-choice .head-button-2 {
  display: block;
  text-align: center;
  margin-right: 0;
  margin-bottom: 20px;
}

.user-property-password-choice .edit-password-cancel {
  text-align: center;
  margin: 0;
}

.edit-password-success,
.edit-username-success {
  display: flex;
  flex-direction: column;
}

.edit-password-success {
  padding-top: 30px;
}

.edit-password-success .edit-password-success-input {
  background-color: transparent;
  font-size: 15px;
  border: none;
  flex: auto;
  padding: 0 5px;
}

.edit-password-success .edit-password-success-reset-link {
  display: flex;
  align-items: center;
  width: 100%;
  margin: 10px 0;
  background-color: #F3F3F3;
}

.edit-password-success .edit-password-success-reset-link span {
  padding: 5px;
  cursor: pointer;
}

.edit-password-success .edit-password-success-reset-link span:hover {
  color: #ffffff;
  background-color: #ff7700;
}

#result_send_mail_copy {
  margin-bottom: 0px;
}

#edit_password_result_mail_copy {
  padding-top: 0px;
}

.update-password-success,
.update-password-fail,
.update-main-user-success {
  padding: 10px;
  font-weight: bold;
  margin-bottom: 20px;
}

#result_send_mail {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
}

#edit_password_result_mail {
  padding-top: 20px;
}

.update-username-success {
  display: flex;
  padding: 10px;
  font-weight: bold;
  margin-bottom: 20px;
  align-items: center;
  gap: 10px;
}

.edit-password-success-ok {
  align-self: center;
  margin-top: 30px;
}

.edit-username-success-ok {
  align-self: center;
}

.edit-password-success-ok .icon-button,
.edit-username-success-ok .icon-button,
.user-property-main-user-close .icon-button {
  padding: 10px 20px;
  font-size: 1.1em;
  font-weight: bold;
  cursor: pointer;
  background-color: #E8E8E8;
  color: #777;
}

.edit-password-success-ok .icon-button:hover,
.edit-username-success-ok .icon-button:hover,
.user-property-main-user-close .icon-button:hover {
  background-color: #ddd;
  color: #3A3A3A;
}

.edit-password-success-ok .icon-button::before,
.edit-username-success-ok .icon-button::before,
.user-property-main-user-close .icon-button::before {
  margin-left: 0;
}

.button-copy-pass {
  margin: auto;
  color: #777;
  font-weight: bold;
}

.EditUserGenPassword {
  margin-top: 15px;
  font-size: 1.1em;
  cursor:pointer;
}
.EditUserGenPassword:hover, .EditUserGenPassword:active {
  color:#ffa646;
}

.EditUserGenPassword span {
  margin-right:10px;
}

.EditUserErrors {
  opacity: 0;
  padding: 7px 0;
  border-left: solid 3px red;
  margin: 10px 0;
  font-weight: bold;
}

.summary-input-container {
  width:171px;
  display:inline-block;
}

.edit-username {
    font-size:1.2em;
    cursor:pointer;
    color: #ffa744;
}

.edit-username-title {
    font-size:1.4em;
    width: auto;
    max-width: 200px;
    overflow-x: hidden;
    text-overflow: ellipsis;
}

.edit-username-specifier {
    font-size:1.5em;
    color:#A4A4A4;
}

.user-property-input.user-property-input-username,
.user-property-input.user-property-input-password {
    padding: 9px;
}

.user-property-password-container {
    display:flex;
    flex-direction:column;
    margin-bottom:30px;
    width:100%;
}

.edit-username-validate,
.edit-password-validate {
    display: block;
    cursor: pointer;
    background-color: #ffa744;
    color: #3c3c3c;
    font-size: 1.1em;
    font-weight: 700;
    padding: 10px 20px;
}

.edit-username-validate:hover,
.edit-password-validate:hover {
    background-color: #f70;
    color: #000;
    cursor: pointer;
}

.edit-username-cancel,
.edit-password-cancel,
.user-property-main-user-cancel {
    cursor:pointer;
    font-size:1.1em;
}

.edit-username-cancel:hover,
.edit-password-cancel:hover,
.user-property-main-user-cancel:hover {
    color: #000;
}

.user-property-register-visit {
    width: 100%;
    color:#A4A4A4;
    font-size:1.1em;
    display:flex;
    align-items: first baseline;
    text-align: center;
    margin-top: auto;
    margin-bottom: 15px;
    justify-content: space-between;
}

.user-property-register-visit span {
    margin:0;
}

.user-property-register-visit div {
  display: flex;
  flex-direction: column;
}

.user-property-register,
.user-property-last-visit, {
  font-size: 11px !important;
}

.user-property-register, .user-property-last-visit {
  min-width: 80px;
}

.edit-user-register-label,
.edit-user-lastvisit-label {
  font-weight: 600;
}

.user-property-main-user-content {
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  text-align: left;
  padding: 30px;
  border-radius: 10px;
  width: 300px;
  display: flex;
  flex-direction: column;
}

.user-property-main-user-content-header {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 5px;
}

.user-property-main-user-content-header .main-user-icon {
  border-radius: 50%;
  width: 40px;
  height: 40px;
  display: flex;
  font-size: 15px;
  justify-content: center;
  align-items: center;
  align-self: center;
  background-color: #FFECF0;
  color: #FF0155;
}

.user-property-main-user-content-header .main-user-icon::before {
  margin-left: .1em;
}

.user-property-main-user-content-header .main-user-title {
  font-weight: 700;
  font-size: 14px;
}

.user-property-main-user-body .main-user-proceed,
.user-property-main-user-body .main-user-rewrite,
.user-property-main-user-body .main-user-validate,
.user-property-main-user-body .main-user-success {
  display: flex;
  flex-direction: column;
}

.user-property-main-user-body .main-user-proceed-footer,
.user-property-main-user-body .main-user-validate-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.user-property-main-user-body .main-user-rewrite-footer {
  display: flex;
}

.user-property-main-user-body .main-user-rewrite-footer input {
  width: 100%;
}

.user-property-main-user-body .main-user-rewrite-footer span {
  background-color: transparent !important;
}

.user-property-main-user-body .main-user-proceed-desc,
.user-property-main-user-body .main-user-rewrite-desc,
.user-property-main-user-body .main-user-validate-desc,
.user-property-main-user-body .update-main-user-success {
  margin: 20px 0;
}

.user-property-main-user-body .main-user-btn-proceed {
  margin-right: 0;
}

.user-property-main-user-body .user-property-main-user-close {
  margin-top: 10px;
  align-self: center;
}

/* properties */

.properties-container {
  display: flex;
  flex-direction: column;
  gap: 25px;
  /* margin-top: 25px; */
}

.properties-container p {
  margin: 0 0 5px 0;
}

.properties-container .user-list-checkbox {
  display: flex;
  align-items: center;
  gap: 5px;
}

.properties-container .select-checkbox {
  width: 25px;
  height: 25px;
}

.properties-container .select-checkbox i {
  font-size: 22px;
}

.user-property-group-text {
  font-size: 11px;
}

.user-property-select > .selectize-input.items {
    padding:0;
}

.user-property-group .selectize-input.items {
    border:none;
}

.user-property-group-container .selectize-input {
  height: 70px !important;
}


/* preferences */

.preferencies-container {
  display: flex;
  flex-direction: column;
  gap: 30px;
}

.preferencies-container p {
  margin: 0 0 5px 0;
}

.preferences-container {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.preferences-container .user-property-label.period-select-bar {
  margin: 15px 0;
}

.user-group-checkbox {
  display: flex;
  flex-direction: column;
  padding-top: 10px;
}

.nb-img-page-infos {
    color:#353535;
    font-weight:normal;
}

.photos-select-bar input {
    display:none;
}

.recent_period_infos {
    color:#353535;
    font-weight:normal;
}

/* plugins tabsheet */
.edit-user-tabsheet-plugins {
  width: 435px !important;
  height: 480px !important;
  margin-right: 15px !important;
  margin-top: 29px !important;
}
/* for firefox */
@-moz-document url-prefix() {
  .edit-user-tabsheet-plugins {
    margin-top: 25px !important;
  }
}

/* update */

.update-user-button,
.user-property-main-user-body .main-user-btn-validate {
    cursor:pointer;
    color:#353535;
    padding:10px 20px;
    font-size:1.1em;
    font-weight:bold;
    background-color: #ffa744;
    color: #3c3c3c;
}

.update-user-button:hover,
.user-property-main-user-body .main-user-btn-rewrite:hover {
    background-color: #ff7700;
}

.update-user-button.can-update {
    background-color: #FFC275;
    color: white;
}

.close-update-button {
    cursor: pointer;
    color: #A4A4A4;
    padding:10px 0;
    font-size:1.1em;
    font-weight:bold;
    align-self: center;
}
.close-update-button.icon-cancel-circled::before {
  margin-left: 0;
}

.update-user-success {
    padding:10px;
    display:none;
}

.update-user-fail {
    padding:11px;
    display:none;
}

/* Guest Pop in */

#GuestUserList {
  display:none;
}

.GuestUserListPopIn {
    position: fixed;
    z-index: 100;
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%;
    overflow: auto; 
    background-color: rgba(0,0,0,0.7);
}


.GuestUserListPopInContainer{
    display:flex;
    position:absolute;
    left:50%;
    top: 50%;
    transform:translate(-50%, -48%);
    text-align:left;
    background-color:white;
    padding:30px;
    width:745px;
    height: 630px;
    flex-direction:column;
    border-radius:15px;
}

.unavailable {
  color:#CBCBCB;
}

.unclickable {
  pointer-events: none;
}
/* Add User Pop In */

#AddUser {
  display:none;
}

.AddUserPopInContainer{
    display:flex;
    position:absolute;
    left:50%;
    top: 50%;
    transform:translate(-50%, -48%);
    text-align:left;
    padding:20px;
    flex-direction:column;
    border-radius:15px;
    align-items:center;
    width: 270px;
}

.AddIconContainer {
  margin-top: 10px;
}

.AddIcon {
  border-radius:9999px;
  padding:10px;
  font-size: 2em;
}

.AddIconTitle {
  font-size:1.4em;
  font-weight:bold;
  margin-bottom:20px;
  margin-top:15px;
  text-align: center;
}

.AddUserInputContainer {
  display: flex;
  flex-direction: column;
  margin: 20px 0px;
  width:100%;
}

.AddUserLabel {
  display:block;
  font-size:1.3em;
}

.AddUserInput {
  display:block;
  font-size:1.3em;
  padding: 10px 5px;
}

.AddUserPasswordWrapper {
  display:flex;
  justify-content:space-between;
  position: relative;
}

.AddUserPasswordWrapper span {
  font-size:1.3em;
  cursor:pointer;
}


.AddUserPasswordWrapper:hover {
  color:#ffa646;
}

.AddUserGenPassword {
  margin-top: 5px;
  font-size: 1.1em;
  cursor:pointer;
}
.AddUserGenPassword:hover, .AddUserGenPassword:active {
  color:#ffa646;
}

.AddUserGenPassword span {
  margin-right:10px;
}

.AddUserErrors {
  visibility:hidden;
  width:100%;
  padding:5px;
  border-left:solid 3px red;
}

.AddUserSubmit {
  cursor:pointer;
  font-weight:bold;
  color: #3F3E40;
  background-color: #FFA836;
  padding: 10px;
  margin: 20px;
  font-size:1em;
  margin-bottom:0;
}

.AddUserCancel {
  color: #3F3E40;
  font-weight: bold;
  cursor: pointer;
  font-size:1em;
}

/* Selectize Inputs (groups) */

#UserList .user-property-group .selectize-input,
#GuestUserList .user-property-group .selectize-input {
  overflow-y: scroll;
}

#UserList .item,
#UserList .item.active,
#GuestUserList .item,
#GuestUserList .item.active {
  background-image:none;
  background-color: #ffa646;
  border-color: transparent;

  border-radius: 20px;
}

#UserList .item .remove,
#GuestUserList .item .remove {
  background-color: transparent;
  border-top-right-radius: 20px;
  border-bottom-right-radius: 20px;

  border-left: 1px solid transparent;

}
#UserList .item .remove:hover,
#GuestUserList .item .remove:hover {
  background-color: #ff7700;
}

/* selection panel */
#permitActionUserList .user-list-checkbox i {
	margin-left: 0px;
}

.user-selected-item {
	display: flex;
	margin: 10px;
	text-align: start;
}

.user-selected-item p {
	width: 85%;
	text-overflow: ellipsis;
	overflow: hidden;
	white-space: nowrap;
	color: #a0a0a0;
	margin: 0;
}

.selection-other-users {
  display:block;
	color: #ffa646;
	font-weight: bold;
	font-size: 15px;
}

.user-action-select {
	-webkit-appearance: none;
	padding: 5px 10px;
  width:100%;
}

.user-action-select[name="selectAction"] {
  margin-bottom:30px;
}

.search-icon {
  top: 20px;
  z-index: 13;
}

/*----------------------
Advanced filter
----------------------*/

.filter-div {
  margin-left: 500px;
}

.advanced-filter-btn {
  position: absolute;
  right: 650px;
  margin-right:10px;
  
  display: flex;
  justify-content: center;
}

#search-user {
  position: absolute;
  z-index: 2;
  right: 404px;
  top: -3px;
}

.extended-filter-btn {
  height: 30px;
}

#advanced-filter-container {
  display:none;
  padding:15px;
  font-size:1em;
}

.advanced-filter-header {
  display:flex;
  justify-content:space-between;
  margin-bottom:10px;
}

.advanced-filter-title {
  font-weight:bold;
}

.advanced-filter-status, 
.advanced-filter-level {
  max-width: 160px;
  width: 16%;
}

.advanced-filter-group {
  max-width: 160px;
  width: 20%;
}

.advanced-filter-date {
  width: 52%;
  min-width: 330px;
  margin: 0 auto 0 auto;

  display: flex;
  flex-direction: column;
  justify-content: center;
} 

.advanced-filter-date-title {
  width: 100%;
  display: flex;
  flex-direction: row;
}

.slider-bar-wrapper {
  margin-top: 12px;
  margin-bottom: 0;
}

.advanced-filter-date {
  padding-right:15px;
}

.advanced-filter-label {
  text-align:left;
  display:block;
  margin-bottom:5px;
  white-space: nowrap;
}

.advanced-filter-select {
  display:block;
}

.advanced-filter-close {
  font-size: 1.8em;
  color: #C5C5C5;
  cursor:pointer;
}

.user-update-spinner {
  display:none;
  font-size: 25px;
}

.UserListPopInContainer .selectize-dropdown-content .option{
  font-size: 0.9em;
  margin-bottom:5px;
}

#permitActionUserList #applyActionBlock {
  margin: 30px 0 0 0;
  display:flex;
  flex-direction:column;
}

.yes_no_radio .user-list-checkbox{
  cursor:pointer;
}

.yes_no_radio .user-list-checkbox .user-list-checkbox-label {
  margin-left: 0;
  margin-right: 10px;
}

#user-table #action {
  padding: 0;
}

.user-header-initials {
  width: 10px;
}

/*View Selector*/

.selectedAlbum-first {
  margin-left: 0px;
}

.UserViewSelector {
  padding: 6px 0px;
  margin-right: 0px;
  border-radius: 10px;

  position: absolute;
  z-index: 2;
  right: 280px;
}

.UserViewSelector span {
  border-radius: 0;
  padding: 6px;
}

/* Should be done with :first-child and :last-child but doesn't work */

.UserViewSelector label span.firstIcon{
  border-radius: 7px 0 0 7px;
}

.UserViewSelector label span.lastIcon{
  border-radius: 0 7px 7px 0;
}

.icon-th-large, .icon-th-list, .icon-pause {
  padding: 10px;
  font-size: 19px;

  transition: 0.3s;
}

.switchLayout {
  display: none;
}


/* Tile View */

.tileView {
  display: flex;
  flex-direction: row;

  flex-wrap: wrap;

  margin-bottom: 20px;
}

.tileView .user-container{
  display: flex;
  flex-direction: column;

  width: 220px;
  height: 250px;

  margin: 20px 20px 20px 0;
}

.tileView .user-container-registration {
  display: none;
}

.tileView .user-container-status,
.tileView .user-container-username {
  margin: 0 auto;
  justify-content: center;
  max-height: 18px;
}

.tileView .user-container-username {
  margin-top: 10px;
  margin-bottom: 5px;
  font-size: 13px;

  height: 15px;

  width: 140px;
  overflow: hidden;
}

.tileView .user-container-username span {
  max-width: 140px;
  overflow: hidden;
  text-overflow: ellipsis;

  text-align: left;
  white-space: nowrap;
}

.tileView .user-container-email {
  margin: 10px auto;
  justify-content: center;
  max-height: 40px;
  width: 190px;
}

.tileView .user-container-groups {
  margin: auto auto 15px auto;
  justify-content: center;
  max-height: 40px;
  width: 90%;
  min-width: 0px;
}

.tileView .group-primary {
  max-width: 45%;
  font-size: 11px;
}

.tileView .group-bonus {
  font-size: 11px;
}

.tileView .user-groups {
  padding: 5px 10px;
}

.tileView .user-container .user-container-edit,
.tileView .user-container .user-container-select {
  height: 40px;
  width: 40px;
  margin: 5px 0 0 5px;
  border-radius: 50%;

  display: flex;
  justify-content: center;
  align-items: center;
}

.tileView .user-container .user-container-checkbox {
  transform: translate3d(-1px, 1px, 0px);
}

.tileView .user-container-initials-wrapper {
    padding-left:0px;
}

.tileView .user-container-initials {
  margin: -10px auto 0 auto;
  justify-content: center;
  max-height: 40px;
}

.hide {
  display: none !important;
}

.tileView .user-container-edit {
  color: transparent;
}

.tileView .user-container:hover .user-container-edit{
  color: #777;
}

.tileView .user-container-username {
  padding-right: 0;
}

/* Compact View */

.compactView {
  display: flex;

  flex-direction: row;
  flex-wrap: wrap;

  margin-bottom: 35px;
}

.compactView .user-container-initials-wrapper > span {
  height: 40px;
  width: 40px;
}


.compactView .user-container {
  height: 50px;
  padding: 0 50px 0 0;

  width: min-content;

  margin: 20px 20px 0  0 !important;
  border-radius: 25px;

  position: relative;
}

.compactView .user-container .user-container-status,
.compactView .user-container .user-container-email,
.compactView .user-container .user-container-groups,
.compactView .user-container .user-container-registration {
  display: none !important;
}

.compactView .user-container-username  {
  width: max-content;
  min-width: auto;

  margin-right: 10px ;
}

.compactView .user-container-initials-wrapper {
  padding-left: 0;
}

.compactView .user-container .user-container-edit,
.compactView .user-container .user-container-select {
  position: absolute;
  right: 0px;

  height: 50px;
  width: 50px;
  border-radius: 50%;

  display: flex;
  justify-content: center;
  align-items: center;
}

.compactView .user-container-initials {
  width: 60px;
}

.compactView .user-container .user-container-checkbox {
  transform: translate3d(-1px, 1px, 0px);
}

.compactView .group-primary {
  max-width: 100px;
}

.compactView .user-container-username {
  padding-right: 0;
}

/* Line View */

.lineView {
  margin-bottom: 20px;
}

.lineView .user-container-username {
  margin-left: -15px;
}

.lineView .user-container.container-selected {
  height: 50px;
  margin-bottom: 10px;
}

.lineView .user-container-initials-wrapper > span {
  padding: 0px;
  height: 35px;
  width: 35px;

  display: flex;
  justify-content: center;
  align-items: center;
}

.lineView .user-container .tmp-edit {
  display: flex;
}

.lineView .group-primary{
  margin-right: 15px;
}


/* User Edit */

.user-edit-icon {
  margin-right: 5px;
}

.selectize-input.items .item {
  color: #000 !important;
}

/* Selection mode */ 

.selectable {
  cursor: pointer;
}

.selectable .select-checkbox {
  width: 25px;
  height: 25px;

  border: solid #ffa646 2px;
}

.selectable .select-checkbox i {
 color: white;
 margin: 5px 0 0 13px;
}

.selectable:hover .select-checkbox{
  background-color: #ffa646 !important;
}

.selectable .user-first-col:hover {
    background-color: transparent;
}

.selectable .user-container-select {
  border: none;
}

.tooltip-status-content {
  text-align:left;
  font-size:14px;
}
.tooltip-status-row {
  margin-top:10px;
  margin-bottom: 20px;
  padding: 0 10px;
}
.tooltip-col1 {
  display:inline-block;
  width:150px;
  vertical-align: top;
}
.tooltip-col2 {
  display:inline-block;
  max-width:500px;
}

.notClickable {
  pointer-events: none;
}

.notClickable:hover {
  cursor: not-allowed;
}

.notClickableBefore:before {
  color: #bbb;
}

.filter-counter {
  background: #ffa500;
  border-radius: 50%;
  justify-content: center;

  font-size: 10px;
  padding: 1px 6px;
  color: black;

  margin:0 4px 0 7px;
  display: none;  
}

.filtered-users {
  position: absolute;
  right: 770px;
  line-height: 38px;
}

@media (max-width: 1550px) {
  #user_search {
    width: 120px;
  }
  .advanced-filter-btn {
    right: 570px;
  }
  .filtered-users {
    right: 690px;
  }
}

@media (max-width: 1465px) {
  #user_search {
    width: 70px;
  }
  .advanced-filter-btn {
    right: 520px;
  }
  .filtered-users {
    right: 640px;
  }
}
</style>
