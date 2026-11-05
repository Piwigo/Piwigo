{if !$smarty.capture.inc_global_search}
{capture name="inc_global_search"}1{/capture}
{if empty($load_mode)}{$load_mode='footer'}{/if}
{combine_css path="admin/themes/default/css/components/global_search.css"}
{combine_script id='global_search' load=$load_mode path='admin/themes/default/js/global_search.js'}
{footer_script}
  let str_global_search_no_result = '{'No results'|translate|escape:javascript}';
  let str_global_search_no_search = '{'No search in progress'|translate|escape:javascript}';
  let str_global_search_error = '{'An error has occured'|translate|escape:javascript}';
{/footer_script}

<div class="bg-modal" id="global_search_modal" style="display: none;">
  <div class="modal-content gsearch-modal">
    <a class="close-modal icon-cancel" id="global_search_close"></a>

    <div class="modal-icon-container">
      <span class="modal-icon-header icon-blue icon-search"></span>
    </div>
    <div class="modal-title-header">
      <p>{'Global search'|translate}</p>
    </div>

    <div class="gsearch-search-container">
      <div class="gsearch-search">
        <span class="icon-search search-icon"></span>
        <input type="text" id="global_search_input" class="search-input gsearch-input" placeholder="{'Search'|translate}" autocomplete="off" spellcheck="false" />
        <span class="icon-cancel gsearch-clear" id="global_search_clear" style="display: none;"></span>
      </div>
    </div>

    <div class="gsearch-tabs" id="global_search_tabs">
      <a class="gsearch-tab active" data-type="all">{'All'|translate}</a>
      <a class="gsearch-tab" data-type="albums">{'Albums'|translate}<span class="gsearch-tab-count"></span></a>
      <a class="gsearch-tab" data-type="photos">{'Photos'|translate}<span class="gsearch-tab-count"></span></a>
      <a class="gsearch-tab" data-type="users">{'Users'|translate}<span class="gsearch-tab-count"></span></a>
      <a class="gsearch-tab" data-type="people">{'People'|translate}<span class="gsearch-tab-count"></span></a>
      <a class="gsearch-tab" data-type="plugins">{'Plugins'|translate}<span class="gsearch-tab-count"></span></a>
      <a class="gsearch-tab" data-type="pages">{'Pages'|translate}<span class="gsearch-tab-count"></span></a>
    </div>

    <div class="gsearch-results" id="global_search_results">
      <p class="gsearch-status" id="global_search_status">{'No search in progress'|translate}</p>
      <div class="gsearch-list" id="global_search_list"></div>
      <i class="searching icon-spin6 animate-spin gsearch-pending" id="global_search_pending" style="display: none;"></i>
    </div>
  </div>
</div>
{/if}
