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

str_are_you_sure = '{'Are you sure?'|translate}';
str_yes = '{'Yes, delete'|translate}';
str_no = '{'No, I have changed my mind'|translate|@escape:'javascript'}';
url_delete = '{$U_DELETE}';

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

<form action="{$F_ACTION}" method="post" id="pictureModify">
  <div id='picture-preview'>
    <div class='picture-preview-actions'>
      {if isset($U_JUMPTO)}
        <a class="icon-eye" href="{$U_JUMPTO}" title="{'Open in gallery'|@translate}"></a>
      {else}
        <a class="icon-eye unavailable" title="{'You don\'t have access to this photo'|translate}"></a>
      {/if}
      <a class="icon-download" href="{$U_DOWNLOAD}" title="{'Download'|translate}"></a>
      {if !url_is_remote($PATH)}
      <a class="icon-arrows-cw" href="{$U_SYNC}" title="{'Synchronize metadata'|@translate}"></a>
      <a class="icon-trash" title="{'delete photo'|@translate}" id='action-delete-picture'></a>
      {/if}
    </div>
    <a href="{$FILE_SRC}" class="preview-box icon-zoom-in" title="{$TITLE|htmlspecialchars}" style="{if $FORMAT}width{else}height{/if}:35vw">
      <img src="{$TN_SRC}" alt="{'Thumbnail'|translate}" style="{if $FORMAT}width{else}height{/if}:100%">
    </a>
  </div>
  <div id='picture-content'>
    <div id='picture-infos'>
      <div class='picture-infos-category'>
        <div class='picture-infos-icon'>
          <span class='icon-picture'></span>
        </div>
        <div class='picture-infos-container'>
          <div class='picture-infos-title'>{$INTRO.file}</div>
          <div>{$INTRO.size}</div>
          <div>{$INTRO.formats}</div>
          <div>{$INTRO.ext}</div>
        </div>
      </div>

      <div class='picture-infos-category'>
        <div class='picture-infos-icon'>
          <span class='icon-calendar'></span>
        </div>
        <div class='picture-infos-container'>
          <div class='picture-infos-title'>{$INTRO.date}</div>
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
      <strong>{'Linked albums'|@translate}</strong>
      <br>
      <select data-selectize="categories" data-value="{$associated_albums|@json_encode|escape:html}"
        placeholder="{'Type in a search term'|translate}"
        data-default="{$STORAGE_ALBUM}" name="associate[]" multiple style="width:calc(100% + 2px);"></select>
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
