{footer_script}
var pwg_token = "{$PWG_TOKEN}";
var orphan_tag_names = {$orphan_tag_names_array};
var str_delete = '{'Delete tag "%s"?'|@translate}';
var str_delete_tags = '{'Delete tags \{%s\}?'|@translate}';
var str_yes_delete_confirmation = "{'Yes, delete'|@translate}";
var str_no_delete_confirmation = "{"No, I have changed my mind"|@translate}";
var str_yes_rename_confirmation = "{'Yes, rename'|@translate}";
var str_tag_deleted = '{'Tag "%s" succesfully deleted'|@translate}';
var str_tags_deleted = '{'Tags \{%s\} succesfully deleted'|@translate}';
var str_already_exist = '{'Tag "%s" already exists'|@translate}';
var str_tag_created = '{'Tag "%s" created'|@translate}';
var str_tag_renamed = '{'Tag "%s1" renamed in "%s2"'|@translate}';
var str_tag_rename = '{'Rename "%s"'|@translate}';
var str_delete_orphan_tags = '{'Delete orphan tags ?'|@translate}';
var str_orphan_tags = '{'You have %s1 orphan : %s2'|@translate}';
var str_delete_them = '{'Delete them'|@translate}';
var str_keep_them = '{'Keep them'|@translate}';
var str_copy = '{' (copy)'|@translate}';
var str_other_copy = '{' (copy %s)'|@translate}';
var str_merged_into = '{'Tag(s) \{%s1\} succesfully merged into "%s2"'|@translate}';
var str_and_others_tags = '{'and %s others'|@translate}';
var str_others_tags_available = '{'%s other tags available...'|@translate}'
var str_number_photos = '{'%d photos'}'
var str_no_photos = '{'no photo'}'
var str_select_all_tag = '{'Select all %d tags'|@translate}';
var str_clear_selection = '{'Clear Selection'|@translate}';
var str_selection_done = '{'The %d tags on this page are selected'|@translate}';
var str_tag_selected = '{'<b>%d</b> tag selected'|@translate}';
var str_tags_found = '{'<b>%d</b> tags found'|@translate}';
var str_tag_found = '{'<b>%d</b> tag found'|@translate}';

$(document).ready(function() {
  $("h1").append('<span class="badge-number">{$total}</span>');
});

{/footer_script}

{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}
{combine_css path="admin/themes/default/fontello/css/animation.css" order=10} {* order 10 is required, see issue 1080 *}
{combine_script id='tiptip' load='header' path='themes/default/js/plugins/jquery.tipTip.minified.js'}
{combine_script id='tags' load='footer' path='admin/themes/default/js/tags.js'}
{combine_script id='jquery.cookie' path='themes/default/js/jquery.cookie.js' load='footer'}

{footer_script}
if (!$.cookie("pwg_tags_per_page")) {
  $.cookie("pwg_tags_per_page", "100");
}
{/footer_script}

<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='expires' content='0'>
<meta http-equiv='pragma' content='no-cache'>

{function name=tagContent}
{function tagContent}
    <p class='tag-name'>{$tag_name}</p>
    <a class="icon-ellipsis-vert showOptions"></a>
    <div class="tag-dropdown-block dropdown">
      <div class="dropdown-content">
        <div class='tag-dropdown-header'>
          <b>{$tag_name}</b>
          <i>{if !$has_image}{'no photo'|@translate}{else}{'%d photos'|@translate:$tag_count}{/if}</i>
        </div>
        <a class='dropdown-option icon-eye view' href="{$tag_U_VIEW}" {if !$has_image} style='display:none' {/if}> {'View in gallery'|@translate}</a>
        <a class='dropdown-option icon-picture manage' href="{$tag_U_EDIT}" {if !$has_image} style='display:none' {/if}> {'Manage photos'|@translate}</a>
        <a class='dropdown-option icon-pencil edit'> {'Edit'|@translate}</a>
        <a class='dropdown-option icon-docs duplicate'> {'Duplicate'|@translate}</a>
        <a class='dropdown-option icon-trash delete'> {'Delete'|@translate}</a>
      </div>
    </div>
    <span class="select-checkbox">
      <i class="icon-ok"> </i>
    </span>
{/function}
{/function}

<div class="selection-mode-group-manager">
  <label class="switch">
    <input type="checkbox" id="toggleSelectionMode">
    <span class="slider round"></span>
  </label>
  <p>{'Selection mode'|@translate}</p>
</div>

<div id="selection-mode-block" class="in-selection-mode tag-selection">
  <div class="tag-selection-content">

    <p id="nothing-selected">{'No tags selected, no actions possible.'|@translate}</p>

    <div class="selection-mode-tag">
      <p>{'Your selection'|@translate}</p>
      <div class="tag-list" data-list='[]'>
        
      </div>
      <div class="selection-other-tags"></div>
      <button id="MergeSelectionMode" class="icon-object-group unavailable" title="{'At least 2 selected tags are needed to merge'|@translate}">{'Merge'|@translate}</button>
      <button id="DeleteSelectionMode" class="icon-trash-1">{'Delete'|@translate}</button>
    </div>

    <div id="MergeOptionsBlock">
      <p>{'Choose which tag to merge these tags into'|@translate}</p>
      <p class="ItalicTextInfo">{'The other tags will be removed'|@translate}</p>
      <div class="MergeOptionsContainer">
        <select id="MergeOptionsChoices"> 
        </select>
      </div>
      <button class="icon-ok ConfirmMergeButton">Confirm merge</button>
      <a id="CancelMerge">Cancel</a>
    </div>

  </div>
</div>

<div class='tag-header'>
  <div id='search-tag'>
    <div class='search-info'> </div>
    <span class='icon-search search-icon'> </span>
    <span class="icon-cancel search-cancel"></span>
    <input class='search-input' type='text' placeholder='{'Search'|@translate}'>
  </div>
  <form id='add-tag' class='not-in-selection-mode'>
    <span class='icon-cancel-circled'></span>
    <label class='add-tag-label icon-plus-circled {if $total == 0} head-button-1 {else} head-button-2 {/if}'>
      <p>{'Add a tag'|@translate}</p>
      <div class='add-tag-container'>
        <input type='text' id='add-tag-input' placeholder="{'New tag'|@translate}">
        <input type='submit' hidden>
        <span class='icon-plus icon-validate'></span>
      </div>
    </label>
  </form>
  <div class='selection-controller in-selection-mode'>
    <p>{'Select'|@translate}</p>
    <a id="selectAll">{'All'|@translate}</a>
    <a id="selectNone">{'None'|@translate}</a>
    <a id="selectInvert">{'Invert'|@translate}</a> 
  </div>
  {if $warning_tags != ""}
  <div class='tag-warning tag-info icon-attention not-in-selection-mode'><p> {$warning_tags} </p></div>
  {/if}
  <div class='tag-message tag-info icon-ok not-in-selection-mode' {if $message_tags != ""}style='display:flex'{/if}> <p> {$message_tags} </p> </div>
  <div class='tag-error tag-info icon-cancel not-in-selection-mode'> <p> </p> </div>
</div>
<div class="pageLoad">
  <i class='icon-spin6 animate-spin'> </i>
</div>

<div class="tag-select-message">
  <div></div> <a></a>
</div>

<div id="RenameTag" class="RenameTagPopIn">
  <div class="RenameTagPopInContainer">
    <a class="icon-cancel ClosePopIn"></a>
    
    <div class="AddIconContainer">
      <span class="AddIcon icon-blue icon-tags"></span>
    </div>
    <div class="AddIconTitle">
      <span>{'Rename "%s"'|@translate}</span>
    </div>
    <div class="RenameTagInputContainer">
      <label class="tag-property-label TagRenameLabelUsername">{'Tag name'|@translate}
        <input type="text" class="tag-property-input"/> 
      </label>
    </div>

    <div class="TagErrors icon-cancel">
    </div>

    <div class="TagSubmitOptions">
      <div class="TagSubmit">
        <span>{'Rename Tag'|@translate}</span>
      </div>

      <div class="TagLoading">
        <i class='icon-spin6 animate-spin'></i>
      </div>

      <div class="TagCancel">
        <span>{'Cancel'|@translate}</span>
      </div>
    </div>
  </div>
</div>

<div class='tag-container' data-tags='{$data|@json_encode|escape:html}' data-per_page={$per_page}>
  {foreach from=$first_tags item=tag}
  <div class='tag-box' data-id='{$tag.id}' data-selected='0'>
  {if isset($tag.counter)}
    {tagContent 
        tag_name = $tag.name
        tag_U_VIEW = 'index.php?/tags/%s-%s'|@sprintf:$tag['id']:$tag['url_name']
        tag_U_EDIT = 'admin.php?page=batch_manager&amp;filter=tag-%s'|@sprintf:$tag['id']
        has_image = ($tag.counter > 0)
        tag_count = $tag.counter
      }
  {else}
    {tagContent 
        tag_name = $tag.name
        tag_U_VIEW = 'index.php?/tags/%s-%s'|@sprintf:$tag['id']:$tag['url_name']
        tag_U_EDIT = 'admin.php?page=batch_manager&amp;filter=tag-%s'|@sprintf:$tag['id']
        has_image = false
        tag_count = 0
      }
  {/if}

  </div>
  {/foreach}
</div>
<div class="emptyResearch"> {'No tag found'|@translate} </div>
<div class="tag-pagination">
  <div class="pagination-per-page">
    <span class="thumbnailsActionsShow" style="font-weight: bold;">{'Display'|@translate}</span>
    <a id="100"
  {if !isset($smarty.cookies.pwg_tags_per_page) || !$smarty.cookies.pwg_tags_per_page || $smarty.cookies.pwg_tags_per_page == 100} 
    class="selected"
  {/if}
    >100</a>
    <a id="200"
  {if isset($smarty.cookies.pwg_tags_per_page) && $smarty.cookies.pwg_tags_per_page == 200} 
    class="selected"
  {/if}
    >200</a>
    <a id="500"
  {if isset($smarty.cookies.pwg_tags_per_page) && $smarty.cookies.pwg_tags_per_page == 500} 
    class="selected"
  {/if}
    >500</a>
    <a id="1000"
  {if isset($smarty.cookies.pwg_tags_per_page) && $smarty.cookies.pwg_tags_per_page == 1000} 
    class="selected"
  {/if}
    >1000</a>
  </div>

  <div class="pagination-container">
    <div class="pagination-arrow left">
      <span class="icon-left-open"></span>
    </div>
    <div class="pagination-item-container">
    </div>
    <div class="pagination-arrow rigth">
      <span class="icon-left-open"></span>
    </div>
  </div>
</div>

<div class='tag-template' style='display:none'>
  {tagContent 
    tag_name='%name%'
    tag_U_VIEW='%U_VIEW%' 
    tag_U_EDIT='%U_EDIT%'
    has_image=false
    tag_count='%count%'
  }
</div> 