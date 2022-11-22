{include file='include/autosize.inc.tpl'}
{include file='include/datepicker.inc.tpl'}
{include file='include/colorbox.inc.tpl'}

{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}

{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}

{footer_script}
(function(){
{* <!-- CATEGORIES --> *}
var categoriesCache = new CategoriesCache({
  serverKey: '{$CACHE_KEYS.categories}',
  serverId: '{$CACHE_KEYS._hash}',
  rootUrl: '{$ROOT_URL}'
});

categoriesCache.selectize(jQuery('[data-selectize=categories]'));

{* <!-- TAGS --> *}
var tagsCache = new TagsCache({
  serverKey: '{$CACHE_KEYS.tags}',
  serverId: '{$CACHE_KEYS._hash}',
  rootUrl: '{$ROOT_URL}'
});

tagsCache.selectize(jQuery('[data-selectize=tags]'), { lang: {
  'Add': '{'Create'|translate}'
}});

{* <!-- DATEPICKER --> *}
jQuery(function(){ {* <!-- onLoad needed to wait localization loads --> *}
  jQuery('[data-datepicker]').pwgDatepicker({
    showTimepicker: true,
    cancelButton: '{'Cancel'|translate}'
  });
});

{* <!-- THUMBNAILS --> *}
jQuery("a.preview-box").colorbox({
	photo: true
});

str_are_you_sure = '{'Are you sure?'|translate|escape:javascript}';
str_yes = '{'Yes, delete'|translate|escape:javascript}';
str_no = '{'No, I have changed my mind'|translate|@escape:'javascript'}';
url_delete = '{$U_DELETE}';
str_albums_found = '{"<b>%d</b> albums found"|translate|escape:javascript}';
str_album_found = '{"<b>1</b> album found"|translate|escape:javascript}';
str_result_limit = '{"<b>%d+</b> albums found, try to refine the search"|translate|escape:javascript}';
str_orphan = '{'This photo is an orphan'|@translate|escape:javascript}';
str_no_search_in_progress = '{'No search in progress'|@translate|escape:javascript}';

related_categories_ids = {$related_categories_ids|@json_encode};
str_already_in_related_cats = '{'This albums is already in related categories list'|translate|escape:javascript}';

{literal}
$('#action-delete-picture').on('click', function() {
  $.confirm({
    title: str_are_you_sure,
    draggable: false,
    titleClass: "groupDeleteConfirm",
    theme: "modern",
    content: "",
    animation: "zoom",
    boxWidth: '30%',
    useBootstrap: false,
    type: 'red',
    animateFromElement: false,
    backgroundDismiss: true,
    typeAnimated: false,
    buttons: {
        confirm: {
          text: str_yes,
          btnClass: 'btn-red',
          action: function () {
            window.location.href = url_delete.replaceAll('amp;', '');
          }
        },
        cancel: {
          text: str_no
        }
    }
  });
})
{/literal}

}());
{/footer_script}

{combine_script id='picture_modify' load='footer' path='admin/themes/default/js/picture_modify.js'}
{combine_css path="admin/themes/default/fontello/css/animation.css" order=10} {* order 10 is required, see issue 1080 *}

<form action="{$F_ACTION}" method="post" id="pictureModify">
{if $INTRO.is_svg}
  <div id='picture-preview' class="svg-container">
{else}
  <div id='picture-preview'>
{/if}
    <div class='picture-preview-actions'>
      {if isset($U_JUMPTO)}
        <a class="icon-eye" href="{$U_JUMPTO}" title="{'Open in gallery'|@translate}"></a>
      {else}
        <a class="icon-eye unavailable" title="{'You don\'t have access to this photo'|translate}"></a>
      {/if}
      <a class="icon-download" href="{$U_DOWNLOAD}" title="{'Download'|translate}"></a>
      <a class="icon-signal" href="{$U_HISTORY}" title="{'Visit history'|translate}"></a>
      {if !url_is_remote($PATH)}
      <a class="icon-arrows-cw" href="{$U_SYNC}" title="{'Synchronize metadata'|@translate}"></a>
      <a class="icon-trash" title="{'delete photo'|@translate}" id='action-delete-picture'></a>
      {/if}
    </div>
    <a href="{$FILE_SRC}" class="preview-box icon-zoom-in" title="{$TITLE|htmlspecialchars}" >
      {if $INTRO.is_svg}
      <img src="{$PATH}" alt="{'Thumbnail'|translate}" class="svg-image" style="{if $FORMAT}width:100%; max-height:100%;{else}max-width:100%; height:100%;{/if}">
      {else}
      <img src="{$TN_SRC}" alt="{'Thumbnail'|translate}" class="other-image-format" style="{if $FORMAT}width:100%; max-height:100%;{else}max-width:100%; height:100%;{/if}">
      {/if}
    </a>
  </div>
  <div id='picture-content'>
    <div id='picture-infos'>
      <div class='info-framed'>
        <div class='info-framed-icon'>
          <i class='icon-picture'></i>
        </div>
        <div class='info-framed-container'>
          <div class='info-framed-title'>{$INTRO.file}</div>
          <div>{$INTRO.size}</div>
          <div>{if isset($INTRO.formats)}{$INTRO.formats} {/if}</div>
          <div>{$INTRO.ext}</div>
        </div>
      </div>

      <div class='info-framed'>
        <div class='info-framed-icon'>
          <span class='icon-calendar'></span>
        </div>
        <div class='info-framed-container'>
          <div class='info-framed-title'>{$INTRO.date}</div>
          <div>{$INTRO.age}</div>
          <div>{$INTRO.added_by}</div>
          <div>{$INTRO.stats}</div>
        </div>
      </div>
    </div>


    <p>
      <strong>{'Title'|@translate}</strong>
      <br>
      <input type="text" class="large" name="name" value="{$NAME|@escape}">
    </p>

    <p>
      <strong>{'Author'|@translate}</strong>
      <br>
      <input type="text" class="large" name="author" value="{$AUTHOR}">
    </p>

    <p>
      <strong>{'Creation date'|@translate}</strong>
      <br>
      <input type="hidden" name="date_creation" value="{$DATE_CREATION}">
      <label class="date-input">
        <i class="icon-calendar"></i>
        <input type="text" data-datepicker="date_creation" data-datepicker-unset="date_creation_unset" readonly>
      </label>
      <a href="#" class="icon-cancel-circled" id="date_creation_unset">{'unset'|translate}</a>
    </p>

    <p>
      <strong>{'Linked albums'|@translate} <span class="linked-albums-badge {if $related_categories|@count < 1 } badge-red {/if}"> {$related_categories|@count} </span></strong>
      {if $related_categories|@count < 1}
        <span class="orphan-photo">{'This photo is an orphan'|@translate}</span>
      {else}
        <span class="orphan-photo"></span>
      {/if}
      <br>
      <select class="invisible-related-categories-select" name="associate[]" multiple>
      {foreach from=$related_categories item=$cat_path key=$key}
        <option selected value="{$key}"></option>
      {/foreach}
      </select>
      <div class="related-categories-container">
      {foreach from=$related_categories item=$cat_path key=$key}
      <div class="breadcrumb-item"><span class="link-path">{$cat_path['name']}</span>{if $cat_path['unlinkable']}<span id={$key} class="icon-cancel-circled remove-item"></span>{else}<span id={$key} class="icon-help-circled help-item tiptip" title="{'This picture is physically linked to this album, you can\'t dissociate them'|translate}"></span>{/if}</div>
      {/foreach}
      </div>
      <div class="breadcrumb-item linked-albums add-item {if $related_categories|@count < 1 } highlight {/if}"><span class="icon-plus-circled"></span>{'Add'|translate}</div>
    </p>

    <p>
      <strong>{'Representation of albums'|@translate}</strong>
      <br>
      <select data-selectize="categories" data-value="{$represented_albums|@json_encode|escape:html}"
        placeholder="{'Type in a search term'|translate}"
        name="represent[]" multiple style="width:calc(100% + 2px);"></select>
    </p>

    <p>
      <strong>{'Tags'|@translate}</strong>
      <br>
      <select data-selectize="tags" data-value="{$tag_selection|@json_encode|escape:html}"
        placeholder="{'Type in a search term'|translate}"
        data-create="true" name="tags[]" multiple style="width:calc(100% + 2px);"></select>
    </p>

    <p>
      <strong>{'Description'|@translate}</strong>
      <br>
      <textarea name="description" id="description" class="description">{$DESCRIPTION}</textarea>
    </p>

    <p>
      <strong>{'Who can see this photo?'|@translate}</strong>
      <br>
      <div class='select-icon icon-down-open'> </div>
      <select name="level" size="1">
        {html_options options=$level_options selected=$level_options_selected}
      </select>
   </p>

    <p>
      <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
      <input class="submit" type="submit" value="{'Save Settings'|@translate}" name="submit">
    </p>
  </div>

</form>

<div id="addLinkedAlbum" class="linkedAlbumPopIn">
  <div class="linkedAlbumPopInContainer">
    <a class="icon-cancel ClosePopIn"></a>
    
    <div class="AddIconContainer">
      <span class="AddIcon icon-blue icon-plus-circled"></span>
    </div>
    <div class="AddIconTitle">
      <span>{'Associate to album'|@translate}</span>
    </div>

    <div id="linkedAlbumSearch">
      <span class='icon-search search-icon'> </span>
      <span class="icon-cancel search-cancel-linked-album"></span>
      <input class='search-input' type='text' placeholder='{'Search'|@translate}'>
    </div>
    <div class="limitReached"></div>
    <div class="noSearch"></div>
    <div class="searching icon-spin6 animate-spin"> </div>

    <div id="searchResult">
    </div>
  </div>
</div>

<style>
.selectize-input  .item,
.selectize-input .item.active {
  background-image:none !important;
  background-color: #ffa646 !important;
  border-color: transparent !important;
  color: black !important;

  border-radius: 20px !important;
}

.selectize-input .item .remove,
.selectize-input .item .remove {
  background-color: transparent !important;
  border-top-right-radius: 20px !important;
  border-bottom-right-radius: 20px !important;
  color: black !important;
  
  border-left: 1px solid transparent !important;

}
.selectize-input .item .remove:hover,
.selectize-input .item .remove:hover {
  background-color: #ff7700 !important;
}
</style>
