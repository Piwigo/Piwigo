{include file='include/autosize.inc.tpl'}
{include file='include/datepicker.inc.tpl'}
{include file='include/colorbox.inc.tpl'}

{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}

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
}());

var file_height = jQuery(".pictureBlocsContainer #albumThumbnail .preview-box.cboxElement").height();
var file_height_calc = (515 - file_height) / 2;
console.log(file_height, file_height_calc);
/*jQuery(".pictureBlocsContainer #albumThumbnail .preview-box.cboxElement").css("padding-top", file_height_calc);*/

{/footer_script}

<form action="{$F_ACTION}" method="post" id="catModify">
  <div class="pictureBlocsContainer">
    <div id="albumThumbnail" style="width: 50%;">
      <div style="position: relative;right: 0;">
        <div class="albumThumbnailActions">
          <a class="icon-download" href="{$U_DOWNLOAD}"title="{'Download'|translate}"></a>
          <a class="icon-trash-1" href="{$U_DELETE}" title="{'delete photo'|@translate}" 
          onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');"></a>
        </div>
      </div>
      <a href="{$FILE_SRC}" class="preview-box" title="{$TITLE|htmlspecialchars}" style="display: inline-block;"><img src="{$FILE_SRC}"
      alt="{'Thumbnail'|translate}"></a>
    </div>

    <div class="pictureInfoFormBlock" style="width: 50%;">
      <div class="pictureInfoBlocks">
        <div id="albumLinks" style="vertical-align:top;">
          <div class="pictureInfoLeftBlock">
            <i class="icon-picture"></i>
            <div style="width: 100%;">
              <div class="pictureInfoBlockTitle">{$INTRO.file}</div>
              <div class="pictureInfoBlockInfos" style="display: flex; justify-content: space-between;">
                <div>{$INTRO.dimensions}</div>
                <div>{$INTRO.megapixel}</div>
                <div>{$INTRO.filesize}</div>
              </div>
              <div class="pictureInfoBlockInfos">{$INTRO.formats}</div>
              <div class="pictureInfoBlockInfos">{$INTRO.filetype}</div>
            </div>
          </div>
          <div class="pictureInfoRightBlock">
            <i class="icon-calendar-clock"></i>
            <div>
              <div class="pictureInfoBlockTitle">{$INTRO.add_date}</div>
              <div class="pictureInfoBlockInfos">{$INTRO.add_date_since}</div>
              <div class="pictureInfoBlockInfos">{$INTRO.added_by}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="pictureInputsBlocks">
        <p>
          <strong class="pictureFormLabel">{'Title'|@translate}</strong>
          <input type="text" class="large" name="name" value="{$NAME|@escape}">
        </p>
        <p>
          <strong class="pictureFormLabel">{'Author'|@translate}</strong>
          <input type="text" class="large" name="author" value="{$AUTHOR}">
        </p>
        <p>
          <strong class="pictureFormLabel">{'Creation date'|@translate}</strong>
          <input type="hidden" name="date_creation" value="{$DATE_CREATION}">
          <label>
            <i class="icon-calendar-clock"></i>
            <input type="text" data-datepicker="date_creation" data-datepicker-unset="date_creation_unset" readonly> 
          </label>
        </p>
        <p>
          <strong class="pictureFormLabel">{'Linked albums'|@translate}</strong>
          <select data-selectize="categories" data-value="{$associated_albums|@json_encode|escape:html}"
          placeholder="{'Type in a search term'|translate}"
          data-default="{$STORAGE_ALBUM}" name="associate[]" multiple style="width:97.3%;"></select>
        </p>
        <p>
          <strong class="pictureFormLabel">{'Representation of albums'|@translate}</strong>
          <select data-selectize="categories" data-value="{$represented_albums|@json_encode|escape:html}"
          placeholder="{'Type in a search term'|translate}"
          name="represent[]" multiple style="width:97.3%;"></select>
        </p>
        <p>
          <strong class="pictureFormLabel">{'Tags'|@translate}</strong>
          <select data-selectize="tags" data-value="{$tag_selection|@json_encode|escape:html}"
          placeholder="{'Type in a search term'|translate}"
          data-create="true" name="tags[]" multiple style="width:97.3%;"></select>
        </p>
        <p>
          <strong class="pictureFormLabel">{'Description'|@translate}</strong>
          <textarea name="description" id="description" class="description">{$DESCRIPTION}</textarea>
        </p>
        <p>
          <strong class="pictureFormLabel">{'Who can see this photo?'|@translate}</strong>
          <div class="pictureLevels">
            <select name="level" size="1">
              {html_options options=$level_options selected=$level_options_selected}
            </select>
          </div>
        </p>
        <p style="margin:40px 0 30px 0">
          <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
          <input class="submit" type="submit" value="{'Save Settings'|@translate}" name="submit">
        </p>
      </div>
    </div>
  </div>
</form>