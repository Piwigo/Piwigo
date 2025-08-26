{if isset($ADD_TO_ALBUM) or isset($selected_category_name)}{$can_upload=true}{else}{$can_upload=false}{/if}

{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='jquery.plupload' load='footer' require='jquery' path='themes/default/js/plugins/plupload/plupload.full.min.js'}
{combine_script id='jquery.plupload.queue' load='footer' require='jquery' path='themes/default/js/plugins/plupload/jquery.plupload.queue/jquery.plupload.queue.min.js'}
{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}

{combine_css path="themes/default/js/plugins/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css"}

{assign var="plupload_i18n" value="themes/default/js/plugins/plupload/i18n/`$lang_info.plupload_code`.js"}
{if "PHPWG_ROOT_PATH"|@constant|@cat:$plupload_i18n|@file_exists}
  {combine_script id="plupload_i18n-`$lang_info.plupload_code`" load="footer" path=$plupload_i18n require="jquery.plupload.queue"}
{/if}

{include file='include/colorbox.inc.tpl'}
{if !$DISPLAY_FORMATS}
  {include file='include/add_album.inc.tpl'}
{/if}

{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}

{combine_script id='piecon' load='footer' path='themes/default/js/plugins/piecon.js'}
{combine_script id='add_photo' load='footer' path='admin/themes/default/js/photos_add_direct.js'}

{html_style}
.addAlbumFormParent { display: none; } /* specific to this page, do not move in theme.css */
{/html_style}

{footer_script}

const formatMode = {if $DISPLAY_FORMATS}true{else}false{/if};
const haveFormatsOriginal = {if $HAVE_FORMATS_ORIGINAL}true{else}false{/if};
const originalImageId = haveFormatsOriginal? '{if isset($FORMATS_ORIGINAL_INFO['id'])} {$FORMATS_ORIGINAL_INFO['id']} {else} -1 {/if}' : -1;
const imageFormatsExtensions = '{$FORMATS_EXT_INFO}';
const nb_albums = {$NB_ALBUMS|escape:javascript};
const chunk_size = '{$chunk_size}kb';
const max_file_size = '{$max_file_size}mb';
const format_update_warning = "{'This format already exists, it will be overwritten !'|translate}";
const format_remove = "{'Remove'|translate}";
var pwg_token = '{$pwg_token}';
const photosAdded_label = "{'%d photos uploaded'|translate|escape:javascript}";
const photosUpdated_label = "{'%d photos updated'|translate|escape:javascript}";
const formatsAdded_label = "{'%d formats added for %d photos'|translate|escape:javascript}";
const formatsUpdated_label = "{'%d formats updated for %d photos'|translate|escape:javascript}";
const batch_Label = "{'Manage this set of %d photos'|translate|escape:javascript}";
const albumSummary_label = "{'Album "%s" now contains %d photos'|translate|escape:javascript}";
const str_format_warning = "{'Error when trying to detect formats'|translate|escape:javascript}";
const str_ok = "{'Ok'|translate|escape:javascript}";
const str_format_warning_multiple = "{'There is multiple image in the database with the following names : %s.'|translate|escape:javascript}";
const str_format_warning_notFound = "{'No picture found with the following name : %s.'|translate|escape:javascript}";
const str_and_X_others = "{'and %d more'|translate|escape:javascript}";
const str_upload_in_progress = "{'Upload in progress'|translate|escape:javascript}";
const str_drop_album_ab = '{'Drop into album'|@translate|escape:javascript}';
const file_ext = "{$file_exts}";
const format_ext = "{$format_ext}"; 
const uploadedPhotos = [];
let uploadCategory = null;
const addedPhotos = [];
const updatedPhotos = [];
let related_categories_ids = {$selected_category|json_encode};

{/footer_script}

<div id="photosAddContent">

{if count($setup_errors) > 0}
  <div class="errors">
    <ul>
    {foreach from=$setup_errors item=error}
      <li>{$error}</li>
    {/foreach}
    </ul>
  </div>
  {else}
    {if count($setup_warnings) > 0}
  <div class="warnings">
    <ul>
      {foreach from=$setup_warnings item=warning}
      <li>{$warning}</li>
      {/foreach}
    </ul>
    <div class="hideButton" style="text-align:center"><a href="{$hide_warnings_link}">{'Hide'|@translate}</a></div>
  </div>
    {/if}
  {/if} {* $setup_errors *}
  
  {if $PROMOTE_MOBILE_APPS}
    <div class="promote-apps">
      <div class="promote-content">
        <div class="left-side">
          <img src="https://sandbox.piwigo.com/uploads/4/y/1/4y1zzhnrnw//2023/01/24/20230124175152-015bc1e3.png">
        </div>
        <div class="promote-text">
          <span>{"Piwigo is also on mobile."|@translate|escape:javascript}</span>
          <span>{"Try now !"|@translate|escape:javascript}</span>
        </div>
        <div class="right-side">
          <div>
            <a href="{$PHPWG_URL}/mobile-applications" target="_blank"><span class="go-to-porg icon-link-1">{"Discover"|@translate|escape:javascript}</span></a>
          </div>
        </div>
      </div>
      <span class="dont-show-again icon-cancel tiptip" title="{'Understood, do not show again'|translate|escape:javascript}"></span>
    </div>
  {/if}

  {if $ENABLE_FORMATS and $can_upload}
    <div class="format-mode-group-manager">
    <label class="switch" onClick="window.location.replace('{$SWITCH_FORMAT_MODE_URL}'); $('.switch .slider').addClass('loading');">
      <input type="checkbox" id="toggleFormatMode" {if $DISPLAY_FORMATS}checked{/if}>
      <span class="slider round"></span>
    </label>
      <p>{'Upload Formats'|@translate}</p>
    </div>
  {/if}

  {if !$DISPLAY_FORMATS}
  <div class="addAlbumEmptyCenter"{if $NB_ALBUMS > 0} style="display:none;"{/if}>
    <div class="addAlbumEmpty">
      <div class="addAlbumEmptyTitle">{'Welcome!'|translate}</div>
      <p class="addAlbumEmptyInfos">{'Piwigo requires an album to add photos.'|translate}</p>
      <a class="buttonLike" id="btnFirstAlbum">{'Create a first album'|translate}</a>
    </div>
  </div>
  {/if}

<div class="infos" style="display:none"><i class="eiw-icon icon-ok"></i></div>
<div class="errors" style="display:none"><i class="eiw-icon icon-cancel"></i><ul></ul></div>

<p class="afterUploadActions" style="margin:10px; display:none;"> 
  {if !$DISPLAY_FORMATS}
    <a class="batchLink icon-pencil"></a><span class="buttonSeparator">{'or'|translate}</span><a href="admin.php?page=photos_add" class="secondary_button icon-plus-circled">{'Add another set of photos'|@translate}</a>
  {else}
    <a href="admin.php?page=photos_add&formats" class="icon-plus-circled">{'Add another set of formats'|@translate}</a>
  {/if}
</p>

  <form id="uploadForm" class="{if $DISPLAY_FORMATS}format-mode{/if}" enctype="multipart/form-data" method="post" action="{$form_action}"{if $NB_ALBUMS == 0} style="display:none;"{/if}>
    {if not $DISPLAY_FORMATS}
    <fieldset class="selectAlbum">
      <legend><span class="icon-folder-open icon-red"></span>{'Drop into album'|@translate}</legend>
      <div class="selectedAlbum"{if !$can_upload} style="display: none"{/if} id="selectedAlbum">
        <span class="icon-sitemap" id="selectedAlbumName">{if isset($ADD_TO_ALBUM)}{$ADD_TO_ALBUM}{elseif isset($selected_category_name)}{$selected_category_name}{/if}</span>
        <a class="icon-pencil" id="selectedAlbumEdit"></a>
      </div>
      <div class="selectAlbumSelector" {if $can_upload} style="display: none"{/if} id="addPhotosAS">
        <p class="head-button-1 icon-folder-open" id="btnPhotosAS">{"Select or create an album"|translate}</p>
      </div>
    </fieldset>
    {elseif $HAVE_FORMATS_ORIGINAL}
    <fieldset class="originalPicture">
      <legend><span class="icon-link-1 icon-red"></span>{'Picture to associate formats with'|@translate}</legend>
      <a class='info-framed' href='{$FORMATS_ORIGINAL_INFO['u_edit']}' title='{'Edit photo'|@translate}'>
        <div class='info-framed-icon'>
          <img src='{$FORMATS_ORIGINAL_INFO['src']}'></i>
        </div>
        <div class='info-framed-container'>
          <div class='info-framed-title'>{$FORMATS_ORIGINAL_INFO['name']}</div>
          {if isset($FORMATS_ORIGINAL_INFO['formats'])}<div>{$FORMATS_ORIGINAL_INFO['formats']}</div>{/if}
          <div>{$FORMATS_ORIGINAL_INFO.ext}</div>
        </div>
      </a>
    </fieldset>
    {/if}
{*
    <p class="showFieldset"><a id="showPermissions" href="#">{'Manage Permissions'|@translate}</a></p>

    <fieldset id="permissions" style="display:none">
      <legend>{'Who can see these photos?'|@translate}</legend>

      <select name="level" size="1">
        {html_options options=$level_options selected=$level_options_selected}
      </select>
    </fieldset>
*}
    <fieldset class="selectFiles">

      <legend>
        <div style="display:flex;align-items: center;">
          <span class="icon-file-image icon-yellow"></span>{'Select files'|@translate}
          {if !$DISPLAY_FORMATS}
          <div id="uploadOptions" class="upload-options">
            <span class="icon-equalizer rotate-element upload-options-icon"></span>{'Options'|@translate}
          </div>
          {/if}
        </div>
      {if !$DISPLAY_FORMATS}
      <div class="upload-options-content" id="uploadOptionsContent">
        <label class="switch">
          <input type="checkbox" id="toggleUpdateMode">
          <span class="slider round"></span>
        </label>
        <div style="margin-left: 6px;">
          <p>{'If a photo in this album has the same filename, update the file without changing the photo\'s properties'|@translate}</p>
        </div>
      </div>
      {/if}
      </legend>

      <div class="selectFilesButtonBlock">
        <button id="addFiles" class="buttonLike icon-plus-circled" {if !$can_upload}disabled{/if}>
          {if not $DISPLAY_FORMATS}{'Add Photos'|translate}{else}{'Add formats'|@translate}{/if}
        </button>
        <div class="selectFilesinfo">
          {if isset($original_resize_maxheight)}
          <p class="uploadInfo">{'The picture dimensions will be reduced to %dx%d pixels.'|@translate:$original_resize_maxwidth:$original_resize_maxheight}</p>
          {/if}
            <p id="uploadWarningsSummary">
            {if not $DISPLAY_FORMATS}
              {'Allowed file types: %s.'|@translate:$upload_file_types}
            {else}
              {'Allowed file types: %s.'|@translate:$str_format_ext} 
              {if !$HAVE_FORMATS_ORIGINAL}<p>{'The original picture will be detected with the filename (without extension).'|@translate}</p>{/if}
            {/if}
            </p>
          </p>
            {if isset($max_upload_resolution)}
            {'Approximate maximum resolution: %dM pixels (that\'s %dx%d pixels).'|@translate:$max_upload_resolution:$max_upload_width:$max_upload_height}
            {/if}
          </p>
        </div>
      </div>
      <div class="photosUploader" id="uploader" {if !$can_upload}style="display: none;"{/if}>
        <p>Your browser doesn't have HTML5 support.</p>
      </div>
      <div class="selectAlbumFirst" id="chooseAlbumFirst" {if $can_upload}style="display: none;"{/if}>
        <p>{"First choose an album, then add your files"|translate}</p>
      </div>
    </fieldset>
    
    <div id="uploadingActions" style="display:none">
      <div class="big-progressbar" style="max-width:98%;margin-bottom: 10px;">
        <div class="progressbar" style="width:0%"></div>
      </div>
      <button id="cancelUpload" class="buttonLike icon-cancel-circled">{'Cancel'|translate}</button>
    </div>

    <button id="startUpload" class="buttonLike icon-upload" disabled>{'Start Upload'|translate}</button>

  </form>

  <fieldset style="display:none" class="Addedphotos">
    <div id="uploadedPhotos"></div>
  </fieldset>

</div> <!-- photosAddContent -->
<div class="bg-modal" id="addFirstAlbum">
  <div class="new-album-modal-content">
     <a class="icon-cancel close-modal" id="closeFirstAlbum"></a>

    <div class="AddIconContainer">
     <span class="AddIcon icon-blue icon-add-album"></span>
    </div>
    <div class="AddIconTitle">
      <span>{'Create your first album'|translate}</span>
    </div>
    <div class="AddAlbumInputContainer">
      <label class="user-property-label AddAlbumLabelUsername">{'Album name'|translate}
        <input class="user-property-input" id="inputFirstAlbum">
      </label>
    </div>
    <a class="buttonLike icon-plus" id="btnAddFirstAlbum">{'Create and select'|translate}</a>

  </div>
</div>

{include file='include/album_selector.inc.tpl'}