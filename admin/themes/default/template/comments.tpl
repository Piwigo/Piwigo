{combine_script id="comments" load="footer" path="admin/themes/default/js/comments.js"}
{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}
{footer_script}
const str_yes_delete_confirmation = "{'Yes, delete'|@translate|@escape:'javascript'}"
const str_no_delete_confirmation = "{"No, I have changed my mind"|@translate|@escape:'javascript'}"
const str_delete = "{'Are you sure you want to delete comment #%s?'|@translate|@escape:'javascript'}"
const str_deletes = "{'Are you sure you want to delete "%d" comments?'|@translate|@escape:'javascript'}"
const str_no_comments_selected = "{'No comments selected, no actions possible.'|@translate|@escape:'javascript'}"
const pwg_token = "{$PWG_TOKEN}"
const str_an_error_has = "{"An error has occured"|@translate|@escape:'javascript'}"
const str_comment_validated = "{"The comment has been validated."|@translate|@escape:'javascript'}"
const str_comments_validated = "{"The comments have been validated."|@translate|@escape:'javascript'}"
const str_and_others = "{"and %s others"|@translate}"
{/footer_script}
<style>
  #tabsheet {
    margin: 0;
  }
</style>
<div class="comments" id="comments">
  <div class="comments-filters">
    <div class="comments-tabs-filters">
      <div class="tab-filters" id="tabFilters">
        <input type="radio" name="p-filter" class="filter" data-status="all" id="seeAll" checked="">
        <label for="seeAll">{"All"|translate}<span class="filter-badge" id="commentsAll"></span></label>

        <input type="radio" name="p-filter" class="filter" data-status="validated" id="seeValidated">
        <label class="filterLabel" for="seeValidated">{"Validated"|translate}<span class="filter-badge"
            id="commentsValidated"></span></label>

        <input type="radio" name="p-filter" class="filter" data-status="pending" id="seePending">
        <label class="filterLabel" for="seePending">{"Waiting"|translate}<span class="filter-badge"
            id="commentsPending"></span></label>
      </div>

      <div class='comments-selection-controller' id="commentsSelectController">
        <p>{'Select'|@translate}</p>
        <p class="comments-selection-btn" id="selectAll">{'All'|@translate}</p>
        <p class="comments-selection-btn" id="selectNone">{'None'|@translate}</p>
        <p class="comments-selection-btn" id="selectInvert">{'Invert'|@translate}</p>
      </div>

      <div class="comments-advanced-filter">
        <div class="advanced-filter-btn icon-filter" id="commentFilters">
          <span>{"Filters"|@translate}</span>
        </div>
        <div class="comments-search">
          <span class="icon-search comments-search-icon"> </span>
          <span class="icon-cancel comments-search-cancel"></span>
          <input id="commentSearchInput" type="text" placeholder="{"Search"|translate}">
        </div>
      </div>
    </div>

    <div class="comments-selection-mode" id="headerSelectMode">
      <div class="comments-selection-switch">
        <label class="switch">
          <input type="checkbox" id="toggleSelectionMode">
          <span class="slider round"></span>
        </label>
        <span>{'Selection mode'|@translate}</span>
      </div>
    </div>
  </div>

  <div class="comments-container" id="commentContainer">
    <div class="comments-area">
      <div class="comments-advanced-filters" id="advancedFilters">
        <div class="comments-filters-container">

          <div class="advanced-filter-author">
            <label class="advanced-filter-item-label" for="filter_author">{"Author"|translate}</label>
            <div class="advanced-filter-select-container advanced-filter-item-container">
              <select class="advanced-filter-select" id="filter_author">
                <option value="" selected="">--</option>
              </select>
            </div>
          </div>

          <div class="advanced-filter-date-start">
            <label class="advanced-filter-item-label" for="filter_date_start">{'Start-Date'|translate}</label>
            <div class="">
              <input id="filter_date_start" type="date" class="advanced-filter-select comments-filters-date" />
            </div>
          </div>

          <div class="advanced-filter-date-end">
            <label class="advanced-filter-item-label" for="filter_date_end">{'End-Date'|translate}</label>
            <div class="">
              <input id="filter_date_end" type="date" class="advanced-filter-select comments-filters-date" />
            </div>
          </div>

        </div>

        <div class="comments-reset-filters">
          <p class="icon-ccw" id="commentsResetFilters"> {"Clear"|translate}
          <p>
        </div>
      </div>

      <div class="comments-list" id="commentsList"></div>

      <div class="user-pagination">
        <div class="pagination-per-page" id="commentsNb">
          <span class="thumbnailsActionsShow" style="font-weight: bold;">{"Display"|translate}</span>
          <a id="pagination-per-page-5" class="selected-pagination">5</a>
          <a id="pagination-per-page-10">10</a>
          <a id="pagination-per-page-25">25</a>
          <a id="pagination-per-page-50">50</a>
        </div>

        <div class="pagination-container">
          <div class="pagination-arrow left unavailable">
            <span class="icon-left-open"></span>
          </div>
          <div class="pagination-item-container">
            {* <a data-page="1" class="actual">1</a> *}
          </div>
          <div class="user-update-spinner icon-spin6 animate-spin" style="display: none;"></div>
          <div class="pagination-arrow rigth unavailable">
            <span class="icon-left-open"></span>
          </div>
        </div>
      </div>
    </div>

    <div class="comments-selection" id="contentSelectMode">
      <p class="comments-no-selection" id="commentsNoSelection">{"No comments selected, no actions possible."|translate}
      </p>
      <div class="comments-selections" id="commentsSelection">
        <p>{'Your selection'|@translate}</p>

        <div class="comments-selected" id="commentsSelected">

        </div>

        <div class="comments-and-others" id="commentsSelectedOthers">
        </div>

        <button id="ValisateSelectionMode" class="icon-ok">{'Validate'|@translate}</button>
        <button id="DeleteSelectionMode" class="icon-trash-1">{'Delete'|@translate}</button>
      </div>
    </div>
  </div>
</div>

<div class="bg-modal" id="modalViewComment">
  <div class="modal-content comments-modal">
    <a class="icon-cancel close-modal" id="closeModalViewComment"></a>
    <div class="comments-modal-container">

      <div class="comments-modal-header">
        <div class="comments-modal-infos">
          <div class="comment-date">
            <i class="icon-calendar"></i>
            <p class="comment-datetime"></p>
          </div>
        </div>

        <div class="comments-modal-img-info">
          <div class="comments-modal-img-i">
            
          </div>
          <img class="comments-modal-img" src="" />
        </div>
      </div>

      <div class="comments-modal-body">

      </div>

      <div class="comments-modal-footer">
        <button class="comments-modal-delete buttonLike buttonSecondary" type="button" id="commentsModalDelete"><i class="icon-trash-1"></i> {"Delete"|translate}</button>
        <button class="comments-modal-validate buttonLike" type="button" id="commentsModalValidate"><i class="icon-ok"></i> {"Validate"|translate}</button>
      </div>

    </div>
  </div>
</div>

<div class="comment-template">
  <div class="comment-container">
    <a class="comment-link" href=""><img class="comment-img" src="" /></a>
    <div class="comment-content">
      <div>
        <div class="comment-msgs">
          <p class="comment-msg"></p>
          <input type="checkbox" name="comment-selected" value="" class="comment-select-checkbox icon-circle-empty">
        </div>
        <div class="comment-author">
          <i class="comment-author-icon"></i>
          <p class="comment-author-name"></p>
        </div>
        <div class="comment-date">
          <i class="icon-calendar"></i>
          <p class="comment-datetime"></p>
        </div>
      </div>

      <div class="comment-footer">
        <p class="comment-hash"></p>
        <div class="comment-buttons">
          <button class="comment-validate" type="button"><i class="icon-ok"></i> {"Validate"|translate}</button>
          <button class="comment-delete" type="button"><i class="icon-trash-1"></i> {"Delete"|translate}</button>
        </div>
      </div>
    </div>
  </div>
</div>