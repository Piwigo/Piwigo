{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{include file='include/tag_selection.inc.tpl'}

{html_style}
.showInfo { text-indent:5px; }
form fieldset p { margin-left:0; }
{/html_style}

{footer_script require='jquery'}
/**
 * Add tag
 */
jQuery("#addTag").click(function() {
  jQuery("#addTagForm").toggle();
  jQuery("input[name=add_tag]").focus();
  return false;
});

jQuery("#addTagClose").click(function() {
  jQuery("#addTagForm").hide();
  return false;
});

jQuery("#selectionMode").click(function() {
  if (jQuery(this).hasClass("icon-check-empty")) {
    jQuery("#selectionMode").removeClass("icon-check-empty").addClass("icon-check");
    jQuery('label.font-checkbox span').show();
    jQuery('ul.tagSelection a.showInfo').hide();
    jQuery('fieldset#action').show();
    jQuery('fieldset#selectTags legend').html("{'Tag selection'|translate|escape:javascript}");
  }
  else {
    jQuery("#selectionMode").removeClass("icon-check").addClass("icon-check-empty");
    jQuery('label.font-checkbox span').hide();
    jQuery('ul.tagSelection a.showInfo').show();
    jQuery('fieldset#action').hide();
    jQuery('fieldset#selectTags legend').html("{'Tags'|translate|escape:javascript}");
  }
  return false;
});


jQuery('.showInfo').tipTip({
  'delay' : 0,
  'fadeIn' : 200,
  'fadeOut' : 200,
  'maxWidth':'300px',
  'keepAlive':true,
  'activation':'click'
});

function displayDeletionWarnings() {
  jQuery(".warningDeletion").show();
  jQuery("input[name=destination_tag]:checked").parent("label").children(".warningDeletion").hide();
}

displayDeletionWarnings();

jQuery("#mergeTags label").click(function() {
  displayDeletionWarnings();
});

$("#searchInput").on("keydown", function(e) {
  var $this = $(this),
      timer = $this.data("timer");

  if (timer) {
    clearTimeout(timer);
  }

  $this.data("timer", setTimeout(function() {
    var val = $this.val();
    if (!val) {
      $(".tagSelection>li").show();
      $("#filterIcon").css("visibility","hidden");
    }
    else {
      $("#filterIcon").css("visibility","visible");
      var regex = new RegExp( val.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&"), "i" );
      $(".tagSelection>li").each(function() {
        var $li = $(this),
            text = $.trim( $("label", $li).text() );
        $li.toggle(regex.test(text));
      });
    }

  }, 300) );

  if (e.keyCode == 13) { // Enter
    e.preventDefault();
  }
});

jQuery('input[name="tags[]"]').click(function() {
  var nbSelected = 0;
  nbSelected = jQuery('input[name="tags[]"]').filter(':checked').length;

  if (nbSelected == 0) {
    jQuery("#permitAction").hide();
    jQuery("#forbidAction").show();
  }
  else {
    jQuery("#permitAction").show();
    jQuery("#forbidAction").hide();
  }
});

jQuery("[id^=action_]").hide();

jQuery("select[name=selectAction]").change(function () {
  jQuery("[id^=action_]").hide();

  jQuery("#action_"+jQuery(this).prop("value")).show();

  jQuery("#displayFormBlock").hide();
  jQuery("#applyActionBlock").hide();

  if (jQuery(this).val() != -1 ) {
    if (jQuery(this).val() == 'delete') {
      jQuery("#applyActionBlock").show();
      jQuery("#applyAction").attr("name", jQuery(this).val());
    }
    else {
      jQuery("#displayForm").attr("name", jQuery(this).val());
      jQuery("#displayFormBlock").show();
    }
  }
  else {
  }
});

jQuery("form").submit(function() {
  if (jQuery("select[name=selectAction]").val() == "delete") {
    if (!jQuery("input[name=confirm_deletion]").is(":checked")) {
      jQuery("#action_delete .errors").show();
      return false;
    }
  }

  if (jQuery("select[name=selectAction]").val() == "merge") {
    if (jQuery("ul.tagSelection input[type=checkbox]:checked").length < 2) {
      alert("{'Select at least two tags for merging'|@translate}");
      return false;
    }
  }
});

jQuery("input[name=confirm_deletion]").change(function() {
  jQuery("#action_delete .errors").hide();
});
{/footer_script}


<div class="titrePage">
  <h2>{'Manage tags'|@translate}</h2>
</div>

{if !isset($EDIT_TAGS_LIST) and !isset($DUPLIC_TAGS_LIST) and !isset($MERGE_TAGS_LIST)}
<p class="showCreateAlbum" id="showAddTag">
  <a class="icon-plus-circled" href="#" id="addTag">{'Add a tag'|translate}</a>
  <a class="icon-check-empty" href="#" id="selectionMode">{'Select tags'|translate}</a>
</p>

<form method="post" style="display:none" id="addTagForm" name="add_user" action="{$F_ACTION}" class="properties">
  <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">

  <fieldset class="with-border">
    <legend>{'Add a tag'|@translate}</legend>

    <label>
      {'New tag'|@translate}
      <input type="text" name="add_tag" size="50">
    </label>

    <p class="actionButtons">
      <input class="submit" type="submit" name="add" value="{'Submit'|@translate}">
      <a href="#" id="addTagClose">{'Cancel'|@translate}</a>
    </p>
  </fieldset>
</form>
{/if}

<form action="{$F_ACTION}" method="post">
  <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">

  {if isset($EDIT_TAGS_LIST)}
  <fieldset>
    <legend>{'Edit tags'|@translate}</legend>
    <input type="hidden" name="edit_list" value="{$EDIT_TAGS_LIST}">
    <table class="table2">
      <tr class="throw">
        <th>{'Current name'|@translate}</th>
        <th>{'New name'|@translate}</th>
      </tr>
      {foreach from=$tags item=tag}
      <tr>
        <td>{$tag.NAME}</td>
        <td><input type="text" name="tag_name-{$tag.ID}" value="{$tag.NAME}" size="50"></td>
      </tr>
      {/foreach}
    </table>

    <p>
      <input type="submit" name="edit_submit" value="{'Submit'|@translate}">
      <input type="submit" name="edit_cancel" value="{'Cancel'|@translate}">
    </p>
  </fieldset>
  {/if}

  {if isset($DUPLIC_TAGS_LIST)}
  <fieldset>
    <legend>{'Edit tags'|@translate}</legend>
    <input type="hidden" name="edit_list" value="{$DUPLIC_TAGS_LIST}">
    <table class="table2">
      <tr class="throw">
        <th>{'Source tag'|@translate}</th>
        <th>{'Name of the duplicate'|@translate}</th>
      </tr>
      {foreach from=$tags item=tag}
      <tr>
        <td>{$tag.NAME}</td>
        <td><input type="text" name="tag_name-{$tag.ID}" value="{$tag.NAME}" size="50"></td>
      </tr>
      {/foreach}
    </table>

    <p>
      <input type="submit" name="duplic_submit" value="{'Submit'|@translate}">
      <input type="submit" name="duplic_cancel" value="{'Cancel'|@translate}">
    </p>
  </fieldset>
  {/if}

  {if isset($MERGE_TAGS_LIST)}
  <fieldset id="mergeTags">
    <legend>{'Merge tags'|@translate}</legend>
    {'Select the destination tag'|@translate}

    <p>
    {foreach from=$tags item=tag name=tagloop}
    <label><input type="radio" name="destination_tag" value="{$tag.ID}"{if $smarty.foreach.tagloop.index == 0} checked="checked"{/if}> {$tag.NAME}<span class="warningDeletion"> {'(this tag will be deleted)'|@translate}</span></label><br>
    {/foreach}
    </p>

    <p>
      <input type="hidden" name="merge_list" value="{$MERGE_TAGS_LIST}">
      <input type="submit" name="merge_submit" value="{'Confirm merge'|@translate}">
      <input type="submit" name="merge_cancel" value="{'Cancel'|@translate}">
    </p>
  </fieldset>
  {/if}

{if !isset($EDIT_TAGS_LIST) and !isset($DUPLIC_TAGS_LIST) and !isset($MERGE_TAGS_LIST)}

  <fieldset id="selectTags">
    <legend>{'Tags'|@translate}</legend>

    {if count($all_tags)}
    <div><label><span class="icon-filter" style="visibility:hidden" id="filterIcon"></span>{'Search'|@translate} <input id="searchInput" type="text" size="12"></label></div>
    {/if}

    <ul class="tagSelection">
    {foreach from=$all_tags item=tag}
      <li>
        {capture name='showInfo'}{strip}
          <b>{$tag.name}</b> ({$tag.counter|@translate_dec:'%d photo':'%d photos'})<br>
          <a href="{$tag.U_VIEW}">{'View in gallery'|@translate}</a> |
          <a href="{$tag.U_EDIT}">{'Manage photos'|@translate}</a>
          {if !empty($tag.alt_names)}<br>{$tag.alt_names}{/if}
        {/strip}{/capture}
        <a class="icon-info-circled-1 showInfo" title="{$smarty.capture.showInfo|@htmlspecialchars}"></a>
        <label class="font-checkbox no-bold">
          <span class="icon-check" style="display:none"></span>
          <input type="checkbox" name="tags[]" value="{$tag.id}">
          {$tag.name}
        </label>
      </li>
    {/foreach}
    </ul>

  </fieldset>

  <fieldset id="action" style="display:none">
    <legend>{'Action'|@translate}</legend>
      <div id="forbidAction">{'No tag selected, no action possible.'|@translate}</div>
      <div id="permitAction" style="display:none">

        <select name="selectAction">
          <option value="-1">{'Choose an action'|@translate}</option>
          <option disabled="disabled">------------------</option>
          <option value="edit">{'Edit selected tags'|@translate}</option>
          <option value="duplicate">{'Duplicate selected tags'|@translate}</option>
          <option value="merge">{'Merge selected tags'|@translate}</option>
          <option value="delete">{'Delete selected tags'|@translate}</option>
{if !empty($tag_manager_plugin_actions)}
  {foreach from=$tag_manager_plugin_actions item=action}
          <option value="{$action.ID}">{$action.NAME}</option>
  {/foreach}
{/if}
        </select>

        <!-- delete -->
        <div id="action_delete" class="bulkAction">
          <p>
            <label class="font-checkbox">
              <span class="icon-check"></span>
              <input type="checkbox" name="confirm_deletion" value="1">
              {'Are you sure?'|@translate}
            </label>
            <span class="errors" style="display:none"><i class="icon-cancel"></i> we really need you to confirm</span>
          </p>
        </div>

{* plugins *}
{if !empty($tag_manage_plugin_actions)}
  {foreach from=$element_set_groupe_plugins_actions item=action}
        <div id="action_{$action.ID}" class="bulkAction">
    {if !empty($action.CONTENT)}{$action.CONTENT}{/if}
        </div>
  {/foreach}
{/if}
        <span id="displayFormBlock" style="display:none">
          <button id="displayForm" class="buttonLike" type="submit" name="">{'Display form'|translate} <i class="icon-right"></i></button>
        </span>

        <p id="applyActionBlock" style="display:none" class="actionButtons">
          <button id="applyAction" name="submit" type="submit" class="buttonLike">
            <i class="icon-trash"></i> {'Apply action'|translate} {* icon-trash because the only action is deletion *}
          </button>
          <span id="applyOnDetails"></span>
        </p>
      </div> {* #permitAction *}
  </fieldset>
{/if}

</form>
