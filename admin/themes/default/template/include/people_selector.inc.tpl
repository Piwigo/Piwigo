{if !$smarty.capture.inc_people_selector}
{capture name="inc_people_selector"}1{/capture}
{combine_css path="admin/themes/default/css/components/people_selector.css"}
{combine_script id='people_selector' load=$load_mode path='admin/themes/default/js/people_selector.js'}
{footer_script}
  let str_no_search_in_progress = '{'No search in progress'|translate|escape:javascript}';
  let str_people_found = '{'<b>%d</b> people found'|translate|escape:javascript}';
  let str_person_found = '{'<b>%d</b> person found'|translate|escape:javascript}';
  let str_people_already_associated = '{'Already associated'|translate|escape:javascript}';
  let str_people_selector_error = '{'An error has occured'|translate|escape:javascript}';
{/footer_script}

<div class="bg-modal" id="people_selector_modal" style="display: none;">
  <div class="modal-content">
    <a class="close-modal icon-cancel" id="people_selector_close"></a>

    <div class="modal-icon-container">
      <span id="people_selector_icon" class="modal-icon-header icon-blue icon-user-1"></span>
      <div class="people-selector-face" id="people_selector_face" style="display: none;"></div>
    </div>
    <div class="modal-title-header" id="people_selector_title">
      <p>{'Associate people'|translate}</p>
    </div>

    {* PEOPLE SELECTOR BODY *}
    <div class="modal-body">
      {* PEOPLE SELECTOR SEARCH *}
      <div class="people-selector-search-container" id="people_selector_search_container">
        <div class="people-search">
          <span class="icon-search search-icon"></span>
          <input id="people_selector_search" class="search-input people-selector-search" type="text" placeholder="{'Search'|translate}" />
        </div>

        <p class="people-selector-search-status" id="people_selector_search_status">{'No search in progress'|translate}</p>
        <i class="searching gallery-icon-spin6 animate-spin" id="people_selector_search_pending" style="display: none;"></i>
      </div>

      <p class="people-selector-empty" id="people_selector_empty" style="display: none;">{'You don\'t have any people yet. Please create one.'|translate}</p>

      {* PEOPLE SELECTOR LIST *}
      <div class="people-selector-list" id="people_selector_list"></div>

      {* PEOPLE SELECTOR CREATION *}
      <div class="people-selector-create" id="people_selector_create" style="display: none;">
        <label for="people_selector_create_name">{'Name'|translate}</label>
        <input type="text" class="user-property-input" id="people_selector_create_name" maxlength="255" />
        <p class="buttonLike" id="people_selector_create_save"><span class="icon-plus"></span>{'Create and select'|translate}</p>
      </div>
    </div>

    {* PEOPLE SELECTOR FOOTER *}
    <div class="modal-footer">
      <div class="people-selector-switch">
        <label class="switch">
          <input type="checkbox" id="people_selector_create_check" value="true">
          <span class="slider round"></span>
        </label>
        <label class="switch-label" for="people_selector_create_check"><span>{'Creation mode'|translate}</span>
          <i class="icon-help-circled tiptip" style="cursor: help" title="{'Activate create mode to create and select a person'|translate}"></i>
        </label>
      </div>
      <p class="modal-footer-error icon-cancel-circled" id="people_selector_error" style="display: none;"></p>
    </div>

  </div>
</div>
{include file='include/person_card.inc.tpl'}
{/if}
