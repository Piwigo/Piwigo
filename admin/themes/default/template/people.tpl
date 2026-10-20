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
{/footer_script}

<div class="people-main">
  {* PEOPLE HEADER *}
  <div class="people-header">
    <p class="head-button-2 icon-plus-circled" id="add_person">{"Add a person"|translate}</p>
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