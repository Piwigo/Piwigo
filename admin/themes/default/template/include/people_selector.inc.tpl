{if !$smarty.capture.inc_people_selector}
{capture name="inc_people_selector"}1{/capture}
{* {combine_css path="admin/themes/default/css/components/people_selector.css"} *}
{combine_script id='people_selector' load=$load_mode path='admin/themes/default/js/people_selector.js'}
{footer_script}
  let str_no_search_in_progress = '{'No search in progress'|translate|escape:javascript}';
  let str_people_found = '{'"<b>%d</b> people found'|translate|escape:javascript}';
  let str_person_found = '{'"<b>%d</b> person found'|translate|escape:javascript}';
  let str_create_and_select = '{"Create and select"|translate|escape:javascript}';
  let str_select_people = '{'Select one or more people'|translate|escape:javascript}';
  let str_associate_people = '{'Associate people'|translate|escape:javascript}';
  let str_associate = '{'Associate'|translate|escape:javascript}';
  let str_select = '{'Select'|translate|escape:javascript}';
{/footer_script}

<div class="bg-modal" id="people_selector_modal" style="display: block;">
  <div class="modal-content">
    <a class="close-modal icon-cancel" id="people_selector_close"></a>

    <div class="modal-icon-container">
      <span id="people_selector_icon" class="modal-icon-header icon-blue icon-user-1"></span>
    </div>
    <div class="modal-title-header" id="people_selector_title">
      <p>{'Select one or more people'|translate}</p>
    </div>

    {* PEOPLE SELECTOR BODY *}
    <div class="modal-body">
      {* PEOPLE SELECTOR SEARCH *}
      <div class="people-selector-search-container">
        <div class="people-search">
          <span class="icon-search search-icon"></span>
          <span class="icon-cancel search-cancel"></span>
          <input id="people_selector_search" class="search-input people-selector-search" type="text" placeholder="{"Search"|translate}" />
        </div>

        <p class="people-selector-search-status" id="people_selector_search_status">{'No search in progress'|translate}</p>
        <i class="searching gallery-icon-spin6 animate-spin" id="people_selector_search_pending" style="display: none;"></i>
      </div>

      <p class="people-selector-empty" id="people_selector_empty" style="display: none;">{'You don\'t have any people yet. Please create one.'|translate}</p>

      {* PEOPLE SELECTOR LIST *}
      <div class="people-selector-list" id="people_selector_list"></div>
    </div>

    {* PEOPLE SELECTOR FOOTER *}
    <div class="modal-footer">
      <p class="modal-footer-close" id="people_selector_cancel">{'Close'|translate}</p>
      <div class="modal-footer-group">
        <p class="modal-footer-error icon-cancel-circled" id="people_selector_error" style="display: none;">/</p>
        <p class="buttonLike" id="people_selector_save"><span class="icon-plus"></span>{'Select'|translate}</p>
      </div>
    </div>

  </div>
</div>
{include file='include/person_card.inc.tpl'}

<style>
.people-selector-search {
  width: 100%;
  margin: unset;
}

.people-selector-search-container {
  text-align: center;
}

.people-selector-empty {
  text-align: center;
  font-weight: bold;
  font-size: 18px;
}
</style>
{/if}