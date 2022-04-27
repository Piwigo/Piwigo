{include file='include/autosize.inc.tpl'}
{include file='include/datepicker.inc.tpl'}
{include file='include/colorbox.inc.tpl'}

{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}

{footer_script}
(function(){
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
jQuery("a.preview-box").colorbox( {
	photo: true
});
}());
{/footer_script}

<form action="{$F_ACTION}" method="POST">
  <div style="margin: 30px 0; display: flex; justify-content: space-between;">
    <div style="margin-left: 22px;">
      {if !empty($navbar) }{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}
    </div>
    <div style="margin-right: 21px;" class="pagination-per-page">
      <span style="font-weight: bold;color: unset;">{'photos per page'|@translate} :</span>
      <a href="{$U_ELEMENTS_PAGE}&amp;display=5">5</a>
      <a href="{$U_ELEMENTS_PAGE}&amp;display=10">10</a>
      <a href="{$U_ELEMENTS_PAGE}&amp;display=50">50</a>
    </div>
  </div>
  <div style="clear:both"></div>

{if !empty($elements) }
<div><input type="hidden" name="element_ids" value="{$ELEMENT_IDS}"></div>
{foreach from=$elements item=element}
<fieldset class="elementEdit">
  <legend>{$element.LEGEND}</legend>

  <span class="thumb">
    <a href="{$element.FILE_SRC}" class="preview-box icon-zoom-in" title="{$element.LEGEND|@htmlspecialchars}"><img src="{$element.TN_SRC}" alt="" {if $element.is_svg}style="{if $current.width < 100}min-width: 100px;{/if}{if $current.height < 100} min-height: 100px; {/if}" {/if}></a>
    <a href="{$element.U_EDIT}" class="icon-pencil">{'Edit'|@translate}</a>
  </span>

  <table>

    <tr>
      <td><strong>{'Title'|@translate}</strong></td>
      <td><input type="text" class="large" name="name-{$element.id}" value="{$element.NAME}"></td>
    </tr>

    <tr>
      <td><strong>{'Author'|@translate}</strong></td>
      <td><input type="text" class="large" name="author-{$element.id}" value="{$element.AUTHOR}"></td>
    </tr>

    <tr>
      <td><strong>{'Creation date'|@translate}</strong></td>
      <td>
        <input type="hidden" name="date_creation-{$element.id}" value="{$element.DATE_CREATION}">
        <label>
          <i class="icon-calendar"></i>
          <input type="text" data-datepicker="date_creation-{$element.id}" data-datepicker-unset="date_creation_unset-{$element.id}" readonly>
        </label>
        <a href="#" class="icon-cancel-circled" id="date_creation_unset-{$element.id}">{'unset'|translate}</a>
      </td>
    </tr>
    <tr>
      <td><strong>{'Who can see this photo?'|@translate}</strong></td>
      <td>
        <select name="level-{$element.id}">
          {html_options options=$level_options selected=$element.LEVEL}
        </select>
      </td>
    </tr>

    <tr>
      <td><strong>{'Tags'|@translate}</strong></td>
      <td>
        <select data-selectize="tags" data-value="{$element.TAGS|@json_encode|escape:html}"
          placeholder="{'Type in a search term'|translate}"
          data-create="true" name="tags-{$element.id}[]" multiple style="width:500px;"></select>
      </td>
    </tr>

    <tr>
      <td><strong>{'Description'|@translate}</strong></td>
      <td><textarea cols="50" rows="5" name="description-{$element.id}" id="description-{$element.id}" class="description">{$element.DESCRIPTION}</textarea></td>
    </tr>

  </table>

</fieldset>
{/foreach}

{if !empty($navbar)}{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}

<p>
  <input type="submit" value="{'Submit'|@translate}" name="submit">
  <input type="reset" value="{'Reset'|@translate}">
</p>
{/if}

</form>

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

.thumb {
  float: right;
}
</style>