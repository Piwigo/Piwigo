{combine_script id='jquery.ui.slider' require='jquery.ui' load='header' path='themes/default/js/ui/minified/jquery.ui.slider.min.js'}
{combine_css path="themes/default/js/ui/theme/jquery.ui.slider.css"}

{footer_script}
jQuery(document).ready(function(){
  $("h1").append("<span class='badge-number'>"+{$nb_total}+"</span>");

  function highlighComments() {
    jQuery(".checkComment").each(function() {
      var parent = jQuery(this).parent('tr');
      if (jQuery(this).children("input[type=checkbox]").is(':checked')) {
        jQuery(parent).addClass('selectedComment'); 
      }
      else {
        jQuery(parent).removeClass('selectedComment'); 
      }
    });
  }

  jQuery(".checkComment").click(function(event) {
    var checkbox = jQuery(this).children("input[type=checkbox]");
    if (event.target.type !== 'checkbox') {
      jQuery(checkbox).prop('checked', !jQuery(checkbox).prop('checked'));
    }
    highlighComments();
  });

  jQuery("#commentSelectAll").click(function () {
    jQuery(".checkComment input[type=checkbox]").prop('checked', true);
    highlighComments();
    return false;
  });

  jQuery("#commentSelectNone").click(function () {
    jQuery(".checkComment input[type=checkbox]").prop('checked', false);
    highlighComments();
    return false;
  });

  jQuery("#commentSelectInvert").click(function () {
    jQuery(".checkComment input[type=checkbox]").each(function() {
      jQuery(this).prop('checked', !$(this).prop('checked'));
    });
    highlighComments();
    return false;
  });

  $(".comment-select-checkbox").on("change", function(event) {
    if ($(this).prop("checked")){
      $(this).removeClass("icon-circle-empty")
      $(this).addClass("icon-ok-circled")
    }
    else {
      $(this).removeClass("icon-ok-circled")
      $(this).addClass("icon-circle-empty")
    }
  });

  $("#toggleSelectionMode").on("click", function() {
    if ($(".comment-select-checkbox").css("visibility") == "visible") {
      $(".comment-buttons-container").css("visibility", "visible");
      $(".comment-select-checkbox").css("visibility", "hidden");
      $(".comment-selection-content").hide();
      $(".comment-container").css("margin-inline-end", "0em")
      $("#advanced-filter-menu").css("margin-inline", "23px 10px")
    }
    else {
      $(".comment-select-checkbox").css("visibility", "visible");
      $(".comment-buttons-container").css("visibility", "hidden");
      $(".comment-selection-content").css("display", "flex")
      $(".comment-container").css("margin-inline-end", "5em")
      $("#advanced-filter-menu").css("margin-inline", "23px 260px")
    }
  })

  $(".advanced-filter-btn").on("click", function() { 
    if ($("#advanced-filter-menu").css("display") == "none") {
      $("#advanced-filter-menu").css("display", "flex")
      $("#advanced-filter-menu").css("margin-bottom", "1em")
      $(".commentFilter").css("margin-bottom", "0.2em")
      $(".commentFilter .advanced-filter-btn").css("height", "100%")
      $(".commentFilter .advanced-filter-btn").css("height", "100%")
    }
    else {
      $("#advanced-filter-menu").css("display", "none")
      $("#advanced-filter-menu").css("margin-bottom", "0.2em")
      $(".commentFilter").css("margin-bottom", "1em")
      $(".commentFilter .advanced-filter-btn").css("height", "27px")
    }
  })

  $(".advanced-filter-close").on("click", function() {
    $("#advanced-filter-menu").css("display", "none")
    $("#advanced-filter-menu").css("margin-bottom", "0.2em")
    $(".commentFilter").css("margin-bottom", "1em")
    $(".commentFilter .advanced-filter-btn").css("height", "27px")
  })

});
{/footer_script}

<div class="commentFilter">

  <div class="pluginTypeFilter">
    <input type="radio" name="p-filter" class="filter" id="seeAll" checked=""><label for="seeAll" href="{$F_ACTION}&amp;filter=all">{'All'|@translate}<span class="filter-badge">{$nb_total}</span></label>
    <input type="radio" name="p-filter" class="filter" id="seeValidated"><label class="filterLabel" for="seeValidated" href="{$F_ACTION}&amp;filter=validated">{'Validated'|@translate}<span class="filter-badge">{$nb_total}</span></label>
    <input type="radio" name="p-filter" class="filter" id="seeWaiting"><label class="filterLabel" for="seeWaiting" href="{$F_ACTION}&amp;filter=pending">{'Waiting'|@translate}<span class="filter-badge">{$nb_pending}</span></label>
  </div>

  {if !empty($navbar) }{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}

  <div class="commentFilter">
  <div class="advanced-filter-btn icon-filter">
    <span>{'Filters'|@translate}</span>
    <span class="filter-counter"></span>
  </div>

  <div id='search-comment'>
    <span class='icon-search search-icon'> </span>
    <span class="icon-cancel search-cancel"></span>
    <input class='search-input' type='text' placeholder='{'Search'|@translate}'>
  </div>


  <div class="userActions">
    <label class="switch">
      <input type="checkbox" id="toggleSelectionMode">
      <span class="slider round"></span>
    </label>
    Mode sélection
  </div>

  </div>

</div>

<div id="advanced-filter-menu" class="advanced-filter advanced-filter-new-plugin advanced-filter-open" style="display: none; margin-inline: 23px 10px;">
  <div class="advanced-filter-header">
    <span class="advanced-filter-title"></span>
    <span class="advanced-filter-close icon-cancel"></span>
  </div>

  <div class="advanced-filter-container">
    
    <div class="advanced-filter-item advanced-filter-author-status">
      <label class="advanced-filter-item-label" for="author-filter">{'Status'|@translate}</label>
      <div class="advanced-filter-item-container">
          <select class="user-action-select advanced-filter-select doubleSelect" name="filter_status">
            <option value="" label="" selected></option>
            {foreach from=$nb_users_by_status key=status_value item=status}
              {if isset($status.name) and isset($status.counter)}
                <option value="{$status_value}">{$status.name} ({$status.counter})</option>
              {else}
                <option value="{$status_value}" disabled>{$status}</option>
              {/if}
            {/foreach}
          </select>
        </div>
    </div>

    <div class="advanced-filter-item advanced-filter-author">
      <label class="advanced-filter-item-label" for="tag-filter">{'Comment author'|@translate}</label>
      <div class="advanced-filter-item-container">
          <select class="user-action-select advanced-filter-select doubleSelect" name="filter_status">
            <option value="" label="" selected></option>
            {foreach from=$nb_users_by_status key=status_value item=status}
              {if isset($status.name) and isset($status.counter)}
                <option value="{$status_value}">{$status.name} ({$status.counter})</option>
              {else}
                <option value="{$status_value}" disabled>{$status}</option>
              {/if}
            {/foreach}
          </select>
        </div>
    </div>

    <div class="advanced-filter-item advanced-filter-mentions">
      <label class="advanced-filter-item-label" for="tag-filter">{'Mentions'|@translate}</label>
      <div class="advanced-filter-item-container">
          <select class="user-action-select advanced-filter-select doubleSelect" name="filter_status">
            <option value="" label="" selected></option>
            {foreach from=$nb_users_by_status key=status_value item=status}
              {if isset($status.name) and isset($status.counter)}
                <option value="{$status_value}">{$status.name} ({$status.counter})</option>
              {else}
                <option value="{$status_value}" disabled>{$status}</option>
              {/if}
            {/foreach}
          </select>
        </div>
    </div>

    <div class="advanced-filter-item advanced-filter-revision-date">
        <label class="advanced-filter-item-label" for="revision-date-filter">
            {'Date of comment'|@translate}<span class="revision-date">{}</span>
        </label>
        <div class="advanced-filter-item-container">
            <div id="revision-date" class="select-bar"></div>
            <div class="slider-bar-wrapper">
                <div class="slider-bar-container revision-date-filter-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all" aria-disabled="false"><div class="ui-slider-range ui-widget-header ui-corner-all ui-slider-range-min" style="width: 0%;"></div><a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 0%;"></a></div>
            </div>
        </div>
    </div>
  </div>
</div>


{if !empty($comments) }


<form method="post" action="{$F_ACTION}" id="pendingComments" class="comment-form">
  
<div class="comment-container">
  {foreach from=$comments item=comment name=comment}
  <div valign="top" class="comment-box">
    
    <a class="illustration" href="{$comment.U_PICTURE}"><img src="{$comment.TN_SRC}"></a>

    <div class="comment">
      <input type="checkbox" name="comments[]" value="{$comment.ID}" class="comment-select-checkbox icon-circle-empty">
      <blockquote> " {$comment.CONTENT} "</blockquote>
      {if $comment.IS_PENDING}<span class="pendingFlag">{'Waiting'|@translate}</span>{/if}
      <strong>  <span id="badge-user" class="badge-user icon-king"></span> {$comment.AUTHOR}</strong>
      <p> <span class="icon-calendar"></span> {$comment.DATE}</p>

      <div class="comment-buttons-container">
        <button class="approve-comment"  type="submit" name="validate" value="{'Validate'|@translate}"><i class="icon-ok"></i> {'Validate'|@translate}</button>
        <button class="delete-comment"  type="submit" name="reject" value="{'Reject'|@translate}"><i class="icon-trash-1"></i> {'Reject'|@translate}</button>
      </div>
    </div>
    
  </div>
  {/foreach}

</div>

<div class="comment-selection-content">

  <span class="checkActions">
    <span class="badge-guest icon-check"></span> {'Select:'|@translate} <span class="badge-grey"> {$nb_total} </span>
  </span>

  <a href="#" id="commentSelectAll" class="selectButton">{'All'|@translate}</a>
  <a href="#" id="commentSelectInvert" class="selectButton">{'Invert'|@translate}</a>


  <p class="checkActions">
    <span class="badge-red icon-cog"></span> {'Action:'|@translate}
  </p>
  <a href="#" id="commentSelectAll" class="selectButton2 icon-ok">{'Validate'|@translate}</a>
  <a href="#" id="commentSelectInvert" class="selectButton2 icon-trash-1">{'Delete'|@translate}</a>


  <div class="savebar-footer">
    <div class="savebar-footer-start">
    </div>
    <div class="savebar-footer-end">
{if isset($save_success)}
      <div class="savebar-footer-block">
        <div class="badge info-message">
          <i class="icon-ok"></i>{$save_success}
        </div>
      </div>
{/if}
{if isset($save_error)}
      <div class="savebar-footer-block">
        <div class="badge info-error">
          <i class="icon-cancel-circled"></i>{$save_error}
        </div>
      </div>
{/if}
{if isset($save_warning)}
  <div class="savebar-footer-block">
    <div class="badge info-warning">
      <i class="icon-attention"></i>{$save_warning}
    </div>
  </div>
{/if}
    </div>
  </div>
</div>

</form>
{/if}
