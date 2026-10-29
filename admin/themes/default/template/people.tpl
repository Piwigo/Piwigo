{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='people' load='footer' path='admin/themes/default/js/people.js'}
{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}
{combine_css path="admin/themes/default/fontello/css/animation.css" order=10} {* order 10 is required, see issue 1080 *}
{footer_script}
  $("h1").append(`<span class='badge-number' id='people_badge_number'>`+{$nb_people}+`</span>`);

  const PWG_TOKEN = "{$PWG_TOKEN}";
  let str_add_new_people = "{'Add new person'|translate|escape:javascript}";
  let str_edit_people = "{'Edit person'|translate|escape:javascript}";
  let str_people_error_occured = "{"An error has occured"|translate|escape:javascript}";
  let str_people_choose_cover = "{'Choose the cover'|translate|escape:javascript}";
  let str_people_no_identified_photo = "{'No identified photo for this person.'|translate|escape:javascript}";
  let str_person_added = "{'Person added'|translate|escape:javascript}";
  let str_person_updated = "{'Person updated'|translate|escape:javascript}";
  let str_people_updated = "{'People updated'|translate|escape:javascript}";
  let str_person_deleted = "{'Person deleted'|translate|escape:javascript}";
  let str_people_deleted = "{'%d people deleted'|translate|escape:javascript}";
  let str_people_merged = "{"People merged into \"%s\""|translate|escape:javascript}";
  let str_cover_updated = "{'Cover updated'|translate|escape:javascript}";
  let str_delete_person = "{"Delete person \"%s\"?"|translate|escape:javascript}";
{/footer_script}

{* TOASTER *}
{include file=$PWG_TOASTER_TPL toaster_mode=$themeconf.colorscheme}

<div class="people-page">
{* top right of the page, at the top of the selection panel when it is open *}
<div class="people-selection-toggle">
  <label class="switch">
    <input type="checkbox" id="people_selection_toggle">
    <span class="slider round"></span>
  </label>
  <label for="people_selection_toggle">{'Selection mode'|translate}</label>
</div>

<div class="people-main">
  {* PEOPLE HEADER *}
  <div class="people-header">
    <p class="head-button-2 icon-plus-circled" id="add_person">{"Add a person"|translate}</p>
    <div class="people-selection-controller" id="people_selection_controller" style="display: none;">
      <p>{'Select'|translate}</p>
      <a data-select="all">{'All'|translate}</a>
      <a data-select="none">{'None'|translate}</a>
      <a data-select="invert">{'Invert'|translate}</a>
    </div>
    <div class="people-global-actions">
      <div class="people-search">
        <div class="search-info"></div>
        <span class="icon-search search-icon"> </span>
        <span class="icon-cancel search-cancel"></span>
        <input id="people_search" class="search-input" type="text" placeholder="{"Search"|translate}" />
      </div>
    </div>
  </div>

  {* PEOPLE STATE *}
  <div id="people_state" class="people-state">
    <i id="people_loading" class='people-loading icon-spin6 animate-spin'></i>
    <p id="people_empty_init" style="display: none;">{'You don\'t have any people yet. Please create one.'|translate}</p>
    <p id="people_empty" style="display: none;">{'No results'|translate}</p>
    <p id="people_error" style="display: none;">{'An error has occured'|translate}</p>
  </div>

  {* PEOPLE CONTAINER *}
  <div id="people_container" class="people-container"></div>

  {* PEOPLE PAGINATION *}
  <div id="people_pagination" class="people-pagination" style="display: none;">

      <div class="pagination-per-page">
        <span class="font-bold">{'people per page'|translate}&nbsp;:</span>
        <a href="#" class="people-per-page" data-value="50">50</a>
        <a href="#" class="people-per-page" data-value="150">150</a>
        <a href="#" class="people-per-page" data-value="300">300</a>
        <a href="#" class="people-per-page" data-value="500">500</a>
      </div>

      <div id="people_pagination_container" class="pagination-container"></div>

    </div>

</div>

{* PEOPLE SELECTION PANEL *}
<div class="people-selection selection-mode" id="people_selection" style="display: none;">
  <p class="people-selection-nothing" id="people_selection_nothing">{'No people selected, no actions possible.'|translate}</p>

  <div id="people_selection_actions" style="display: none;">
    <p class="people-selection-title">{'Your selection'|translate}</p>
    <div class="people-selection-list" id="people_selection_list"></div>

    {* same action block as the selection panel of the users *}
    <div class="people-selection-action">
      <p class="people-selection-action-title">{'Action'|translate}</p>
      <div class="user-action-select-container">
        <select class="user-action-select" id="people_selection_action_choice">
          <option value="">{'Choose an action'|translate}</option>
          <option value="merge" title="{'At least 2 selected people are needed to merge'|translate}">{'Merge'|translate}</option>
          <option value="delete">{'Delete'|translate}</option>
          <option value="visibility">{'Show in gallery'|translate}</option>
        </select>
      </div>

      {* merge *}
      <div class="bulkAction" id="people_selection_action_merge" style="display: none;">
        <p>{'Choose which person to merge these people into'|translate}</p>
        <div class="user-action-select-container">
          <select class="user-action-select" id="people_selection_merge_choice"></select>
        </div>
        <p class="people-selection-info">{'The other people will be removed'|translate}</p>
      </div>

      {* delete *}
      <div class="bulkAction" id="people_selection_action_delete" style="display: none;">
        <span class="user-list-checkbox" data-name="confirm_delete">
          <span class="select-checkbox"><i class="icon-ok"></i></span>
          <span class="user-list-checkbox-label">{'Are you sure?'|translate}</span>
        </span>
      </div>

      {* show in gallery *}
      <div class="bulkAction yes_no_radio" id="people_selection_action_visibility" style="display: none;">
        <span class="user-list-checkbox" data-name="visible_yes" data-selected="1">
          <span class="select-checkbox"><i class="icon-ok"></i></span>
          <span class="user-list-checkbox-label">{'Yes'|translate}</span>
        </span>
        <span class="user-list-checkbox" data-name="visible_no">
          <span class="select-checkbox"><i class="icon-ok"></i></span>
          <span class="user-list-checkbox-label">{'No'|translate}</span>
        </span>
      </div>

      <button type="button" class="people-selection-apply" id="people_selection_apply" style="display: none;">{'Apply action'|translate}</button>
      <p class="people-selection-error" id="people_selection_error" style="display: none;"></p>
    </div>
  </div>
</div>
</div>

{* PEOPLE MODAL *}
<div class="bg-modal" id="people_modal_person">
  <div class="modal-content">
    <a class="close-modal icon-cancel" id="people_modal_close"></a>

    <div class="modal-icon-container">
      <span id="people_modal_icon" class="modal-icon-header icon-blue icon-plus-circled"></span>
    </div>
    <div class="modal-title-header" id="people_modal_title">
      <p>{'Add new person'|translate}</p>
    </div>

    <div class="modal-body">
      <div class="modal-field">
        <label class="modal-field-label" for="people_modal_name">{'Name'|translate}</label>
        <input class="modal-input" type="text" name="people_modal_name" id="people_modal_name_input" />
      </div>

      <div>
        <label class="switch">
          <input type="checkbox" name="people_modal_show_gallery" id="people_modal_show_gallery" checked>
          <span class="slider round"></span>
        </label>
        <label for="people_modal_show_gallery">{'Show in the gallery'|translate}</label>
      </div>
    </div>
    <div class="modal-footer">
      <p class="modal-footer-close" id="people_modal_cancel">{'Close'|translate}</p>
      <div class="modal-footer-group">
        <p class="modal-footer-error icon-cancel-circled" id="people_modal_error" style="display: none;">/</p>
        <p class="buttonLike" id="people_modal_save"><span class="icon-floppy"></span>{'Save'|translate}</p>
      </div>
    </div>
  </div>
</div>

{* PEOPLE DELETE MODAL *}
<div class="bg-modal" id="people_modal_delete">
  <div class="modal-content">
    <a class="close-modal icon-cancel" id="people_modal_delete_close"></a>

    <div class="modal-icon-container">
      <span class="modal-icon-header icon-red icon-trash-1"></span>
    </div>
    <div class="modal-title-header">
      <p id="people_modal_delete_title"></p>
    </div>

    <div class="modal-footer">
      <p class="modal-footer-close" id="people_modal_delete_cancel">{'No, I have changed my mind'|translate}</p>
      <div class="modal-footer-group">
        <p class="buttonLike" id="people_modal_delete_confirm"><span class="icon-trash-1"></span>{'Yes, delete'|translate}</p>
      </div>
    </div>
  </div>
</div>

{* PEOPLE TEMPLATE *}
{assign var="options" value=[
  'show' => [ 'class' => 'icon-lock show', 'text' => 'Show in gallery'],
  'hide' => [ 'class' => 'icon-lock hide', 'text' => 'Don\'t show in gallery'],
  'edit' => [ 'class' => 'icon-pencil edit', 'text' => 'Edit'],
  'cover' => [ 'class' => 'icon-picture cover', 'text' => 'Change the cover'],
  'delete' => [ 'class' => 'icon-trash delete', 'text' => 'Delete'],
]}
{include file='include/person_card.inc.tpl' options=$options}

{* PICTURE SELECTOR *}
{include file='include/picture_selector.inc.tpl' load_mode='footer'}