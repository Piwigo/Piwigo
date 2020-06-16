{footer_script}
var pwg_token = "{$PWG_TOKEN}";
var str_delete = '{'Delete tag "%s"?'|@translate}'
var str_delete_tags = '{'Delete tags \{%s\}?'|@translate}'
var str_yes_delete_confirmation = "{'Yes, delete'|@translate}"
var str_no_delete_confirmation = "{"No, I have changed my mind"|@translate}"
var str_tag_deleted = '{'Tag "%s" succesfully deleted'|@translate}'
var str_tags_deleted = '{'Tags \{%s\} succesfully deleted'|@translate}'
var str_already_exist = '{'Tag "%s" already exists'|@translate}'
var str_tag_created = '{'Tag "%s" created'|@translate}'
var str_tag_renamed = '{'Tag "%s1" renamed in "%s2"'|@translate}'
var str_delete_orphan_tags = '{'Delete orphan tags ?'|@translate}'
var str_orphan_tags = '{'You have %s1 orphan : %s2'|@translate}';
var str_delete_them = '{'Delete them'|@translate}';
var str_keep_them = '{'Keep them'|@translate}';
var str_copy = '{' (copy)'|@translate}'
var str_other_copy = '{' (copy %s)'|@translate}'
var str_merged_into = '{'Tag(s) \{%s1\} succesfully merged into "%s2"'|@translate}'
{/footer_script}

{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}
{combine_css path="admin/themes/default/fontello/css/animation.css"}
{combine_script id='tiptip' load='header' path='themes/default/js/plugins/jquery.tipTip.minified.js'}
{combine_script id='tags' load='footer' path='admin/themes/default/js/tags.js'}

{function name=tagContent}
{function tagContent}
    <p class='tag-name'>{$tag_name}</p>
    <a class="icon-ellipsis-vert showOptions not-in-selection-mode"></a>
    <div class="tag-dropdown-block">
      <a class='tag-dropdown-action icon-eye view' href="{$tag_U_VIEW}" {if !$has_image} style='display:none' {/if}>{'View in gallery'|@translate}</a>
      <a class='tag-dropdown-action icon-picture manage' href="{$tag_U_EDIT}" {if !$has_image} style='display:none' {/if}>{'Manage photos'|@translate}</a>
      <a class='tag-dropdown-action icon-pencil edit'> {'Edit'|@translate}</a>
      <a class='tag-dropdown-action icon-trash delete'> {'Delete'|@translate}</a>
      <a class='tag-dropdown-action icon-docs duplicate'> {'Duplicate'|@translate}</a>
    </div>
    <span class="select-checkbox in-selection-mode">
      <i class="icon-ok"> </i>
    </span>
    <div class="tag-rename">
      <form>
        <input type="text" class="tag-name-editable" placeholder="{$tag_name}">
        <input type="submit" hidden>
      </form>
      <span class="icon-ok validate"></span>
      <span class="icon-cancel"></span>
    </div>
{/function}
{/function}

<div class="titrePage">
  <h2>{'Manage tags'|@translate}</h2>
</div>

<div class="selection-mode-group-manager">
  <label class="switch">
    <input type="checkbox" id="toggleSelectionMode">
    <span class="slider round"></span>
  </label>
  <p>{'Selection mode'|@translate}</p>
</div>

<div id="selection-mode-block" class="in-selection-mode tag-selection">
  <div class="tag-selection-content">

    <p id="nothing-selected">{'No tag selected, no action possible.'|@translate}</p>

    <div class="selection-mode-tag">
      <p>{'Your selection'|@translate}</p>
      <div class="tag-list">
        
      </div>
      <button id="MergeSelectionMode" class="icon-object-group unavailable">{'Merge'|@translate}</button>
      <button id="DeleteSelectionMode" class="icon-trash-1">{'Delete selected tags'|@translate}</button>
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
    <span class='icon-filter'> </span>
    <input class='search-input' type='text' placeholder='{'Search'|@translate}'>
  </div>
  <form id='add-tag'>
    <span class='icon-cancel'></span>
    <span class='icon-plus-circled icon-validate'></span>
    <label class='add-tag-container'>
      <p>{'Add a tag'|@translate}</p>
      <input type='text' id='add-tag-input' placeholder="{'Add a tag'|@translate}">
      <input type='submit' hidden>
    </label>
  </form>
  {if $warning_tags != ""}
  <div class='tag-warning tag-info icon-attention'><p> {$warning_tags} </p></div>
  {/if}
  <div class='tag-message tag-info icon-ok' {if $message_tags != ""}style='display:flex'{/if}> <p> {$message_tags} </p> </div>
  <div class='tag-error tag-info icon-cancel'> <p> </p> </div>
</div>

<div class='tag-container'>
  {foreach from=$all_tags item=tag}
  <div class='tag-box' data-id='{$tag.id}' data-selected='0'>
    {tagContent 
        tag_name=$tag.name
        tag_U_VIEW=$tag.U_VIEW 
        tag_U_EDIT=$tag.U_EDIT
        has_image=($tag.counter > 0)
      }
  </div>
  {/foreach}
</div>
<div class="emptyResearch"> {'No tag found'|@translate} </div>

<div class='tag-template' style='display:none'>
  {tagContent 
    tag_name='%name%'
    tag_U_VIEW='%U_VIEW%' 
    tag_U_EDIT='%U_EDIT%'
    has_image=false
  }
</div> 