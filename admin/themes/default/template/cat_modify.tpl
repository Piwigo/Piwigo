{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='cat_modify' load='footer' path='admin/themes/default/js/cat_modify.js'}
{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}
{combine_css path="admin/themes/default/fontello/css/animation.css" order=10} {* order 10 is required, see issue 1080 *}
{combine_script id='jquery.tipTip' load='footer' path='themes/default/js/plugins/jquery.tipTip.minified.js'}

{footer_script}
const has_images_associated_outside = '{"delete album and all %d photos, even the %d associated to other albums"|@translate|escape:javascript}';
const has_images_becomming_orphans = '{'delete album and the %d orphan photos'|@translate|escape:javascript}';
const has_images_recursives = '{'delete only album, not photos'|@translate|escape:javascript}';
const cat_nav = '{$CATEGORIES_NAV|escape:javascript}';
const album_id = {$CAT_ID}
var parent_album = {$PARENT_CAT_ID}
var default_parent_album = {$PARENT_CAT_ID}
const album_name = "{$CAT_NAME}"
const nb_sub_albums = {$NB_SUBCATS}
const pwg_token = '{$PWG_TOKEN}'
const u_delete = '{$U_DELETE}'
var is_visible = '{$IS_VISIBLE}'

const str_cancel = '{'No, I have changed my mind'|@translate|@escape}'
const str_delete_album = '{'Delete album'|@translate|escape:javascript}'
const str_delete_album_and_his_x_subalbums = '{'Delete album "%s" and its %d sub-albums.'|@translate|escape:javascript}'
const str_just_now = '{'Just now'|@translate|escape:javascript}'

const str_dont_delete_photos = '{'delete only album, not photos'|@translate|escape:javascript}';
const str_delete_orphans = '{'delete album and the %d orphan photos'|@translate|escape:javascript}';
const str_delete_all_photos = '{'delete album and all %d photos, even the %d associated to other albums'|@translate|escape:javascript}';

str_albums_found = '{"<b>%d</b> albums found"|translate}';
str_album_found = '{"<b>1</b> album found"|translate}';
str_result_limit = '{"<b>%d+</b> albums found, try to refine the search"|translate|escape:javascript}';
str_orphan = '{'This photo is an orphan'|@translate}';
str_no_search_in_progress = '{'No search in progress'|@translate}';
str_already_in_related_cats = '{'This albums is already in related categories list'|@translate}';
str_album_comment_allow = '{'Comments allowed for sub-albums'|@translate}';
str_album_comment_disallow = '{'Comments disallowed for sub-albums'|@translate}';
str_root = '{'Root'|@translate}';
{/footer_script}

<div class="cat-modify">

  <div class="cat-modify-header">
    <div class="cat-modify-ariane">
    <a class="icon-sitemap tiptip" href="{$U_MOVE}" title="{'Manage sub-albums'|@translate}"></a>
      {$CATEGORIES_NAV}
    </div>

    <div class="cat-modify-actions">
      {if cat_admin_access($CAT_ID)}
        <a class="icon-eye tiptip" href="{$U_JUMPTO}" title="{'Open in gallery'|@translate}"></a>
      {/if}

      {if isset($U_MANAGE_ELEMENTS) }
        <a class="icon-picture tiptip" href="{$U_MANAGE_ELEMENTS}" title="{'Manage album photos'|@translate}"></a>
      {/if}

      <a class="icon-plus-circled tiptip" href="{$U_ADD_PHOTOS_ALBUM}" title="{'Add Photos'|translate}"></a>

      <a class="icon-sitemap tiptip" href="{$U_MOVE}" title="{'Manage sub-albums'|@translate}"></a>

      {if isset($U_SYNC) }
        <a class="icon-exchange tiptip" href="{$U_SYNC}" title="{'Synchronize'|@translate}"></a>
      {/if}

      {if isset($U_DELETE) }
        <a class="icon-trash deleteAlbum tiptip" href="#" title="{'Delete album'|@translate}"></a>
      {/if} 

      {* <a class="icon-ellipsis-vert tiptip" href="#" title="{'Comments'|@translate}"></a> *}

      <span class="icon-ellipsis-vert toggle-comment-option">
        <div class="comment-option">
          <span class="allow-comments icon-ok"> {'Allow comments for sub-albums'|translate} </span>
          <span class="disallow-comments icon-cancel" target="_blank">{'Disallow comments for sub-albums'|@translate}</span>
        </div>
      </span>

      {* Comment for extensions to add their custom actions *}
    </div>
  </div>

  <div class="cat-modify-content">

    <div class="cat-modify-infos">
      <div class="cat-modify-info-card cat-creation">
        <span class="cat-modify-info-title">{'Created'|@translate}</span>
        <span class="cat-modify-info-content">{$INFO_CREATION_SINCE}</span>
        <span class="cat-modify-info-subcontent">{$INFO_CREATION}</span>
      </div>
      <div class="cat-modify-info-card cat-modification">
        <span class="cat-modify-info-title">{'Modified'|@translate}</span>
        <span class="cat-modify-info-content">{$INFO_LAST_MODIFIED_SINCE}</span>
        <span class="cat-modify-info-subcontent">{$INFO_LAST_MODIFIED}</span>
      </div>
      <div title="{$INFO_TITLE}" class="cat-modify-info-card cat-photos">
        <span class="cat-modify-info-title">{'Photos'|@translate}</span>
        <span class="cat-modify-info-content">{$INFO_PHOTO}</span>
        <span class="cat-modify-info-subcontent">{$INFO_IMAGES_RECURSIVE}</span>
      </div>
      <div class="cat-modify-info-card cat-albums">
        <span class="cat-modify-info-title">{'sub-albums'|@translate}</span>
        <span class="cat-modify-info-content">{$INFO_DIRECT_SUB}</span>
        <span class="cat-modify-info-subcontent">{$INFO_SUBCATS}</span>
      </div>
      {if isset($U_SYNC) }
      <div class="cat-modify-info-card">
        <span class="cat-modify-info-title">{'Directory'}</span>
        <span class="cat-modify-info-content">{$CAT_FULL_DIR}</span>
      </div>
      {/if}
    </div>

    <div 
      class="cat-modify-representative {if !isset($representant)}icon-file-image{elseif !isset($representant.picture)}icon-dice-solid{/if}" 
      {if !isset($representant)}title="{'No photos in the current album, no thumbnail available'|@translate}"{/if} 
      {if isset($representant) && isset($representant.picture)}style="background-image:url('{$representant.picture.src}')"{/if}
      >
      {if isset($representant) and ($representant.ALLOW_SET_RANDOM || $representant.ALLOW_SET_RANDOM)}
      <div class="cat-modify-representative-actions">
        {if $representant.ALLOW_SET_RANDOM }
          <a class="refreshRepresentative buttonLike" id="refreshRepresentative" title="{'Find a new representant by random'|@translate}">
            <i class="icon-ccw"></i>
            {'Refresh thumbnail'|@translate}
          </a>
        {/if}
        {if isset($representant.ALLOW_DELETE)}
          <a class="deleteRepresentative buttonLike" id="deleteRepresentative" title="{'Delete Representant'|@translate}" style="{if !isset($representant.picture)}display:none{/if}">
            <i class="icon-cancel"></i>
            {'Remove thumbnail'|translate}
          </a>
        {/if}
      </div>
      {/if}
    </div>

    <div class="cat-modify-form">
      <div class="cat-modify-input-container">
        <label for="cat-name">{'Name'|@translate}</label>
        <input type="text" id="cat-name" value="{$CAT_NAME}" maxlength="255">
      </div>

      <div class="cat-modify-input-container">
        <label for="cat-comment">{'Description'|@translate}</label>
        <textarea resize="false" rows="5" name="comment" id="cat-comment">{$CAT_COMMENT}</textarea>
      </div>

      <div class="cat-modify-input-container">
        <label for="cat-parent">{'Parent album'|@translate}</label>
        <div class="icon-pencil" id="cat-parent">{$CATEGORIES_PARENT_NAV}</div>
      </div>

      {include file='include/album_selector.inc.tpl' 
        title={'New parent album'|@translate}
        searchPlaceholder={'Search'|@translate}
        show_root_btn=true
      }

      {if isset($CAT_COMMENTABLE)}
      <div class="cat-modify-switch-container">
        <div class="switch-input">
          <label class="switch">
            <input type="checkbox" name="commentable" id="cat-commentable" value="true" {if $CAT_COMMENTABLE == "true"}checked{/if}>
            <span class="slider round"></span>
          </label>
        </div>
        <label class="switch-label" for="cat-commentable"><span>{'Authorize comments'|@translate}</span> <i class="icon-help-circled tiptip" title="{'A photo can receive comments from your visitors if it belongs to an album with comments activated.'|@translate}" style="cursor:help"></i></label>
      </div>
      {/if}

      <div class="cat-modify-switch-container">
        <div class="switch-input">
          <label class="switch">
            <input type="checkbox" name="locked" id="cat-locked" value="true" {if $IS_VISIBLE == 'false'}checked{/if}>
            <span class="slider round"></span>
          </label>
          
        </div>    
        <label class="switch-label" for="cat-locked"><span>{'Locked album'|@translate}</span> <i class="icon-help-circled tiptip" title="{'Locked albums are disabled for maintenance. Only administrators can view them in the gallery. Lock this album will also lock his Sub-albums'|@translate}" style="cursor:help"></i></label>
      </div>
    </div>
  </div>

  <div class="cat-modify-footer">
    <div class="info-message icon-ok">{'Album updated'|@translate}</div>
    <div class="info-error icon-cancel">{'An error has occured while saving album settings'|@translate}</div>
    <span class="buttonLike" id="cat-properties-save"><i class="icon-floppy"></i> {'Save Settings'|@translate}</span>
    </div>
</div>

<style>
.toggle-comment-option {
  cursor: pointer;
  position: relative;
}

.toggle-comment-option::before{
  transform: scale(1.3);
}

.comment-option {
  position: absolute;
  display: flex;
  flex-direction: column;
  background: linear-gradient(130deg, #ff7700 0%, #ffa744 100%);
  right: -10px;
  top: 45px;
  width: max-content;
  border-radius: 10px;
}

.comment-option span, .comment-option a {
  padding: 5px 10px;
  text-decoration: none;
  color: white;
  font-weight: 600;
  text-align: initial;
}

.comment-option::after {
  content: " ";
  position: absolute;
  top: -10px;
  right: 21px;
  transform: rotate(0deg);
  border-width: 5px;
  border-style: solid;
  border-color: transparent transparent #ff7700 transparent;
}

.comment-option span:first-child::before {
  margin-right: -1px;
}

.comment-option span:hover:first-child {
  color: white;
  background-color: #00000012;
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
}

.comment-option span:hover:last-child {
  color: white;
  background-color: #00000012;
  border-bottom-left-radius: 10px;
  border-bottom-right-radius: 10px;
}

.put-to-root {
  width: 220px;
  margin-top: 5px;
}
.put-to-root p {
  margin: 0  auto;
}

.notClickable {
  opacity: 0.5;
  pointer-events: none;
}

.cat-modify-footer .spinner {
  width: 20px;
  height: 20px;
}

.warnings {
  display: none;
}
</style>