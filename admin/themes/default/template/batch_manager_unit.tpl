{include file='include/autosize.inc.tpl'}
{include file='include/datepicker.inc.tpl'}
{include file='include/colorbox.inc.tpl'}

{combine_script id='jquery.sort' load='footer' path='themes/default/js/plugins/jquery.sort.js'}

{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}

{combine_script id='jquery.selectize' load='header' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}
{combine_script id='doubleSlider' load='footer' require='jquery.ui.slider' path='admin/themes/default/js/doubleSlider.js'}

{combine_script id='jquery.ui.slider' require='jquery.ui' load='header' path='themes/default/js/ui/minified/jquery.ui.slider.min.js'}
{combine_css path="themes/default/js/ui/theme/jquery.ui.slider.css"}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}

{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}

{combine_css path="admin/themes/default/fontello/css/animation.css" order=10}

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

str_are_you_sure = '{'Are you sure?'|translate|escape:javascript}';
str_yes = '{'Yes, delete'|translate|escape:javascript}';
str_no = '{'No, I have changed my mind'|translate|@escape:'javascript'}';
str_albums_found = '{"<b>%d</b> albums found"|translate|escape:javascript}';
str_album_found = '{"<b>1</b> album found"|translate|escape:javascript}';
str_result_limit = '{"<b>%d+</b> albums found, try to refine the search"|translate|escape:javascript}';
str_orphan = '{'This photo is an orphan'|@translate|escape:javascript}';
str_no_search_in_progress = '{'No search in progress'|@translate|escape:javascript}';
str_already_in_related_cats = '{'This albums is already in related categories list'|translate|escape:javascript}';

}());
const strs_privacy = {
  "0" : "{$level_options[8]}",
  "1" : "{$level_options[4]}",
  "2" : "{$level_options[2]}", 
  "3" : "{$level_options[1]}",
  "4" : "{$level_options[0]}",
};
<!-- sliders config -->

var sliders = {
  widths: {
    values: [{$dimensions.widths}],
    selected: {
      min: {$dimensions.selected.min_width},
      max: {$dimensions.selected.max_width},
    },
    text: '{'between %d and %d pixels'|translate|escape:'javascript'}'
  },

  heights: {
    values: [{$dimensions.heights}],
    selected: {
      min: {$dimensions.selected.min_height},
      max: {$dimensions.selected.max_height},
    },
    text: '{'between %d and %d pixels'|translate|escape:'javascript'}'
  },

  ratios: {
    values: [{$dimensions.ratios}],
    selected: {
      min: {$dimensions.selected.min_ratio},
      max: {$dimensions.selected.max_ratio},
    },
    text: '{'between %.2f and %.2f'|translate|escape:'javascript'}'
  },

  filesizes: {
    values: [{$filesize.list}],
    selected: {
      min: {$filesize.selected.min},
      max: {$filesize.selected.max},
    },
    text: '{'between %s and %s MB'|translate|escape:'javascript'}'
  }
};



console.log(sliders);
{/footer_script}

{combine_script id='batchManagerUnit' load='footer' require='jquery.ui.effect-blind,jquery.sort' path='admin/themes/default/js/batchManagerUnit.js'}

<div id="batchManagerGlobal" style="margin-bottom: 80px;">

<div style="clear:both"></div>

{debug}
{if isset($ELEMENT_IDS)}<div><input type="hidden" name="element_ids" value="{$ELEMENT_IDS}"></div>{/if}
<fieldset>
<legend><span class='icon-filter icon-green'></span>{'Filter'|@translate}</legend>
<form method="post" action="{$F_ACTION}" class="filter">
<div class="filterBlock">
  <ul id="filterList">
    <li id="filter_prefilter" {if !isset($filter.prefilter)}style="display:none"{/if}>
      <input type="checkbox" name="filter_prefilter_use" class="useFilterCheckbox" {if isset($filter.prefilter)}checked="checked"{/if}>
      <p>{'Predefined filter'|@translate}</p>
      <a href="#" class="removeFilter" title="{'remove this filter'|@translate}"><span>[x]</span></a>
      <select name="filter_prefilter">
        {foreach from=$prefilters item=prefilter}
          {assign 'optionClass' ''}
          {if $prefilter.ID eq 'no_album'}{assign 'optionClass' 'icon-heart-broken'}{/if}
          {if $prefilter.ID eq 'caddie'}{assign 'optionClass' 'icon-flag'}{/if}

        <option value="{$prefilter.ID}"  class="{$optionClass}" {if isset($filter.prefilter) && $filter.prefilter eq $prefilter.ID}selected="selected"{/if}>{$prefilter.NAME}</option>
        {/foreach}
      </select>
      <a id="empty_caddie" href="admin.php?page=batch_manager&amp;action=empty_caddie" style="{if !isset($filter.prefilter) or $filter.prefilter ne 'caddie'}display:none{/if}">{'Empty caddie'|translate}</a>
{if $NB_ORPHANS > 0}
      <a id="delete_orphans" href="#" style="{if !isset($filter.prefilter) or $filter.prefilter ne 'no_album'}display:none{/if}" class="icon-trash">{'Delete %d orphan photos'|translate:$NB_ORPHANS}</a>
{/if}
{if $NB_NO_MD5SUM > 0}
<a id="sync_md5sum" href="#" style="{if !isset($filter.prefilter) or $filter.prefilter ne 'no_sync_md5sum'}display:none{/if}" class="icon-arrows-cw">{'Compute %d missing checksums'|translate:{$NB_NO_MD5SUM}}</a>
{/if}

      <span id="add_md5sum" style="display:none">
        <img class="loading" src="themes/default/images/ajax-loader-small.gif">
        <span id="md5sum_added">0</span>% -
        <span id="md5sum_to_add" data-origin="{$NB_NO_MD5SUM}">{$NB_NO_MD5SUM}</span>
        {'checksums to add'|translate}
      </span>

      <span id="add_md5sum_error" class="errors" style="display:none"></span>

      <span id="orphans_deletion" style="display:none">
        <img class="loading" src="themes/default/images/ajax-loader-small.gif">
        <span id="orphans_deleted">0</span>% -
        <span id="orphans_to_delete" data-origin="{$NB_ORPHANS}">{$NB_ORPHANS}</span>
        {'orphans to delete'|translate}
      </span>

      <span id="orphans_deletion_error" class="errors" style="display:none"></span>

      <span id="duplicates_options" style="{if !isset($filter.prefilter) or $filter.prefilter ne 'duplicates'}display:none{/if}">
        {'based on'|translate}
        <label class="font-checkbox"><span class="icon-check"></span><input type="checkbox" name="filter_duplicates_filename" {if isset($filter.duplicates_filename)}checked="checked"{/if}> {'file name'|translate}</label>
        <label class="font-checkbox" title="md5sum"><span class="icon-check"></span><input type="checkbox" name="filter_duplicates_checksum" {if isset($filter.duplicates_checksum)}checked="checked"{/if}> {'checksum'|translate} <i class="icon-help-circled tiptip" title="translated md5sum definition here !"> </i></label>
        <label class="font-checkbox"><span class="icon-check"></span><input type="checkbox" name="filter_duplicates_date" {if isset($filter.duplicates_date) or (isset($filter.prefilter) and $filter.prefilter ne 'duplicates')}checked="checked"{/if}> {'date & time'|translate}</label>
        <label class="font-checkbox"><span class="icon-check"></span><input type="checkbox" name="filter_duplicates_dimensions" {if isset($filter.duplicates_dimensions)}checked="checked"{/if}> {'width & height'|translate}</label>
      </span>
    </li>

    <li id="filter_category" {if !isset($filter.category)}style="display:none"{/if}>
      <input type="checkbox" name="filter_category_use" class="useFilterCheckbox" {if isset($filter.category)}checked="checked"{/if}>
      <p>{'Album'|@translate}</p>
      <a href="#" class="removeFilter" title="{'remove this filter'|translate}"><span>[x]</span></a>
      <select data-selectize="categories" data-value="{$filter_category_selected|@json_encode|escape:html}"
        data-default="first" name="filter_category"></select>
      <label class="font-checkbox"><span class="icon-check"></span><input type="checkbox" name="filter_category_recursive" {if isset($filter.category_recursive)}checked="checked"{/if}> {'include child albums'|@translate}</label>
    </li>

    <li id="filter_tags" {if !isset($filter.tags)}style="display:none"{/if}>
      <input type="checkbox" name="filter_tags_use" class="useFilterCheckbox" {if isset($filter.tags)}checked="checked"{/if}>
      <p>{'Tags'|@translate}</p>
      <a href="#" class="removeFilter" title="{'remove this filter'|translate}"><span>[x]</span></a>
      <select data-selectize="tags" data-value="{$filter_tags|@json_encode|escape:html}"
        placeholder="{'Type in a search term'|translate}"
        name="filter_tags[]" multiple style="width:600px;"></select>
      <label class="font-checkbox"><span class="icon-circle-empty"></span><span><input type="radio" name="tag_mode" value="AND" {if !isset($filter.tag_mode) or $filter.tag_mode eq 'AND'}checked="checked"{/if}> {'All tags'|@translate}</span></label>
      <label class="font-checkbox"><span class="icon-circle-empty"></span><span><input type="radio" name="tag_mode" value="OR" {if isset($filter.tag_mode) and $filter.tag_mode eq 'OR'}checked="checked"{/if}> {'Any tag'|@translate}</span></label>
    </li>

    <li id="filter_level" {if !isset($filter.level)}style="display:none"{/if}>
      <input type="checkbox" name="filter_level_use" class="useFilterCheckbox" {if isset($filter.level)}checked="checked"{/if}>
      <p>{'Privacy level'|@translate}</p>
      <a href="#" class="removeFilter" title="{'remove this filter'|translate}"><span>[x]</span></a>
      <select name="filter_level" size="1">
        {html_options options=$filter_level_options selected=$filter_level_options_selected}
      </select>
      <label class="font-checkbox"><span class="icon-check"></span><input type="checkbox" name="filter_level_include_lower" {if isset($filter.level_include_lower)}checked="checked"{/if}> {'include photos with lower privacy level'|@translate}</label>
    </li>

    <li id="filter_dimension" {if !isset($filter.dimension)}style="display:none"{/if}>
      <input type="checkbox" name="filter_dimension_use" class="useFilterCheckbox" {if isset($filter.dimension)}checked="checked"{/if}>
      <p>{'Dimensions'|translate}</p>
      <a href="#" class="removeFilter" title="{'remove this filter'|translate}"><span>[x]</span></a>
      <blockquote>
        <div data-slider="widths" class="dimensionSlidersBlocks">
          <div class="dimensionSlidersTitleButtons">
            <div>
              {'Width'|translate} <span class="slider-info">{'between %d and %d pixels'|translate:$dimensions.selected.min_width:$dimensions.selected.max_width}</span>
            </div>
            <a class="slider-choice dimension-cancel" data-min="{$dimensions.bounds.min_width}" data-max="{$dimensions.bounds.max_width}">{'Reset'|translate}</a>
          </div>
          <div class="slider-slider"></div>

          <input type="hidden" data-input="min" name="filter_dimension_min_width" value="{$dimensions.selected.min_width}">
          <input type="hidden" data-input="max" name="filter_dimension_max_width" value="{$dimensions.selected.max_width}">
        </div>

        <div data-slider="heights" class="dimensionSlidersBlocks">
          <div class="dimensionSlidersTitleButtons">
            <div>
              {'Height'|translate} <span class="slider-info">{'between %d and %d pixels'|translate:$dimensions.selected.min_height:$dimensions.selected.max_height}</span>
            </div>
            <a class="slider-choice dimension-cancel" data-min="{$dimensions.bounds.min_height}" data-max="{$dimensions.bounds.max_height}">{'Reset'|translate}</a>
          </div>
          <div class="slider-slider"></div>
          
          <input type="hidden" data-input="min" name="filter_dimension_min_height" value="{$dimensions.selected.min_height}">
          <input type="hidden" data-input="max" name="filter_dimension_max_height" value="{$dimensions.selected.max_height}">
        </div>

        <div data-slider="ratios" class="dimensionSlidersBlocks">
            <div style="margin-bottom: 11px;">
              <span>{'Ratio'|translate} ({'Width'|@translate}/{'Height'|@translate})</span>
              <span class="slider-info">{'between %.2f and %.2f'|translate:$dimensions.selected.min_ratio:$dimensions.selected.max_ratio}</span>
            </div>
            <div class="dimensionSlidersRatioButtons">
              <div>
              {if isset($dimensions.ratio_portrait)} <a class="slider-choice" data-min="{$dimensions.ratio_portrait.min}" data-max="{$dimensions.ratio_portrait.max}">{'Portrait'|translate}</a>{/if}
              {if isset($dimensions.ratio_square)} <a class="slider-choice" data-min="{$dimensions.ratio_square.min}" data-max="
              {$dimensions.ratio_square.max}">{'square'|translate}</a>{/if}
              {if isset($dimensions.ratio_landscape)} <a class="slider-choice" data-min="{$dimensions.ratio_landscape.min}" data-max="{$dimensions.ratio_landscape.max}">{'Landscape'|translate}</a>{/if}
              {if isset($dimensions.ratio_panorama)} <a class="slider-choice" data-min="{$dimensions.ratio_panorama.min}" data-max="{$dimensions.ratio_panorama.max}">{'Panorama'|translate}</a>{/if}
              </div>
              <div>
               <a class="slider-choice dimension-cancel" data-min="{$dimensions.bounds.min_ratio}" data-max="{$dimensions.bounds.max_ratio}">{'Reset'|translate}</a>
              </div>
            </div>
          <div class="slider-slider"></div>

          <input type="hidden" data-input="min" name="filter_dimension_min_ratio" value="{$dimensions.selected.min_ratio}">
          <input type="hidden" data-input="max" name="filter_dimension_max_ratio" value="{$dimensions.selected.max_ratio}">
        </div>
      </blockquote>
    </li>

    <li id="filter_search"{if !isset($filter.search)} style="display:none"{/if}>
      <input type="checkbox" name="filter_search_use" class="useFilterCheckbox"{if isset($filter.search)} checked="checked"{/if}>
      <p>{'Search'|@translate}</p>
      <a href="#" class="removeFilter" title="{'remove this filter'|translate}"><span>[x]</span></a>
      <input name="q" size=40 value="{if isset($filter.search)} {$filter.search.q|stripslashes|htmlspecialchars}{/if}">
      <a href="admin/popuphelp.php?page=quick_search&amp;output=content_only" title="{'Help'|@translate}" class="help-popin-search"><span class="icon-help-circled">{'Search tips'|translate}</span></a>
      {combine_script id='core.scripts' load='async' path='themes/default/js/scripts.js'}
{if (isset($no_search_results))}
<div>{'No results for'|@translate} :
<em><strong>
{foreach $no_search_results as $res}
{if !$res@first} &mdash; {/if}
{$res}
{/foreach}
</strong></em>
</div>
{/if}
    </li>

    <li id="filter_filesize" {if !isset($filter.filesize)}style="display:none"{/if}>
      <input type="checkbox" name="filter_filesize_use" class="useFilterCheckbox" {if isset($filter.filesize)}checked="checked"{/if}>
      <p>{'Filesize'|translate}</p>
      <a href="#" class="removeFilter" title="{'remove this filter'|translate}"><span>[x]</span></a>
      <blockquote>
        <div data-slider="filesizes">
          <span class="slider-info">{'between %s and %s MB'|translate:$filesize.selected.min:$filesize.selected.max}</span>
          <a class="slider-choice dimension-cancel" data-min="{$filesize.bounds.min}" data-max="{$filesize.bounds.max}">{'Reset'|translate}</a>
          <div class="slider-slider"></div>

          <input type="hidden" data-input="min" name="filter_filesize_min" value="{$filesize.selected.min}">
          <input type="hidden" data-input="max" name="filter_filesize_max" value="{$filesize.selected.max}">
        </div>
      </blockquote>
    </li>
  </ul>

  <div class='noFilter'>{'No filter, add one'|@translate}</div>

  <div class="filterActions">
    <div id="addFilter">
      <div class="addFilter-button icon-plus" onclick="$('.addFilter-dropdown').slideToggle()">{'Add a filter'|@translate}</div>
      <div class="addFilter-dropdown">
        <a data-value="filter_prefilter" {if isset($filter.prefilter)}class="disabled"{/if}>{'Predefined filter'|@translate}</a>
        <a data-value="filter_category" {if isset($filter.category)}class="disabled"{/if}>{'Album'|@translate}</a>
        <a data-value="filter_tags" {if isset($filter.tags)}class="disabled"{/if}>{'Tags'|@translate}</a>
        <a data-value="filter_level" {if isset($filter.level)}class="disabled"{/if}>{'Privacy level'|@translate}</a>
        <a data-value="filter_dimension" {if isset($filter.dimension)}class="disabled"{/if}>{'Dimensions'|@translate}</a>
        <a data-value="filter_filesize" {if isset($filter.filesize)}class="disabled"{/if}>{'Filesize'|@translate}</a>
        <a data-value="filter_search"{if isset($filter.search)} class="disabled"{/if}>{'Search'|@translate}</a>
      </div>
      <a id="removeFilters" class="icon-cancel" style="display: none;">{'Remove all filters'|@translate}</a>
    </div>

    <button id="applyFilter" name="submitFilter" type="submit">
      <i class="icon-arrows-cw"></i> {'Refresh photo set'|@translate}
    </button>
  </div>
</div>

</fieldset>
</form>
<legend style="padding: 1em;"><span class='icon-menu icon-blue'></span><span>{count($all_elements)} images sélectionnées</span></legend>
{if !empty($elements) }

<div style="margin: 10px 0; display: flex; justify-content: space-between; padding: 1em;">

  <div style="margin-right: 21px;" class="pagination-per-page">
    <span style="font-weight: bold;color: unset;">{'photos per page'|@translate} :</span>
    <a href="{$U_ELEMENTS_PAGE}&amp;display=5">5</a>
    <a href="{$U_ELEMENTS_PAGE}&amp;display=10">10</a>
    <a href="{$U_ELEMENTS_PAGE}&amp;display=50">50</a>
  </div>
  <div style="margin-left: 22px;">
    <div class="pagination-reload">
      {if !empty($navbar) }<a class="button-reload tiptip" title="Pagination has changed and needs to be reloaded !" style="display: none;" href="{$F_ACTION}"><i class="icon-cw"></i></a>{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}
    </div>
  </div>

    
</div>
{foreach from=$elements item=element}
  {footer_script}
  var related_category_ids_{$element.ID} = {$element.related_category_ids};  
  url_delete_{$element.id} = '{$element.U_DELETE}';
  {/footer_script}

<div class="deleted-element" data-image_id="{$element.ID}" style="display: none;"><i class="icon-ok">&#xe819;</i><p>Image #{$element.ID} '{$element.FILE}' was succesfully deleted</p></div>
<fieldset class="elementEdit" id="picture-{$element.ID}" data-image_id="{$element.ID}">
  <div class="pictureIdLabel">#{$element.ID}</div>
  <div class="media-box">
    <img src="{$element.TN_SRC}" alt="imagename" class="media-box-embed" style="{if $element.FORMAT}width:100%; max-height:100%;{else}max-width:100%; height:100%;{/if}">
    <div class="media-hover">
    <div class='picture-preview-actions'>
    <a class="preview-box icon-zoom-square tiptip" href="{$element.FILE_SRC}" title="Zoom"></a>
    <a class="icon-download tiptip" href="{$element.U_DOWNLOAD}" title="Download"></a>
    <a class="icon-signal tiptip" href="{$element.U_HISTORY}" title="Visit history"></a>
    <a target="_blank" class="icon-pencil tiptip" href="{$element.U_EDIT}" title="{'Edit photo'|@translate}"></a>
    {if !url_is_remote($element.PATH)}
      <a class="icon-arrows-cw tiptip" href="{$element.U_SYNC}" title="{'Synchronize metadata'|@translate}"></a>
      <a class="icon-trash tiptip action-delete-picture" title="{'delete photo'|@translate}"></a>
    {/if}

  </div>
  {if isset($element.U_JUMPTO)}
    <a class="see-out" href="{$element.U_JUMPTO}" >
    <p><i class="icon-left-open"></i>{'Open in gallery'|@translate}</p>
  {else}
    <a class="see-out disabled" href="#" >
    <p class="" title="{'You don\'t have access to this photo'|translate}" ><i class="icon-left-open"></i>{'Open in gallery'|translate}</p>
  {/if}
    </a>
  </div>  
  </div>
   <div class="main-info-container">
    <div class="main-info-block">
    <div class='info-framed-icon' style="margin-right:0px;">
    <i class='icon-picture'></i>
    </div>
      <span class="main-info-title">{$element.FILE}</span>
      <span class="main-info-desc">{$element.DIMENSIONS}</span>
      <span class="main-info-desc">{$element.FILESIZE}</span>
      <span class="main-info-desc">{$element.EXT}</span>

    </div>
    <div class="main-info-block">
      <div class='info-framed-icon' style="margin-right:0px;">
        <span class='icon-calendar'></span>
      </div>
      <span class="main-info-title">{$element.POST_DATE}</span>
      <span class="main-info-desc">{$element.AGE}</span>
      <span class="main-info-desc">{$element.ADDED_BY}</span>
      <span class="main-info-desc">{$element.STATS}</span>
    </div>
  </div>
  <div class="info-container">
    
    <div class="half-line-info-box">
       <strong>{'Title'|@translate}</strong>
       <input type="text" name="name" id="name-{$element.id}" value="{$element.NAME}">
    </div>
    
    <div class="calendar-box">
      <strong>{'Creation date'|@translate}</strong>
      <input type="hidden" id="date_creation-{$element.id}" name="date_creation-{$element.id}" value="{$element.DATE_CREATION}">
      <label class="calendar-input">
        <i class="icon-calendar"></i>
        <input type="text" data-datepicker="date_creation-{$element.id}" data-datepicker-unset="date_creation_unset-{$element.id}" readonly>
        <a href="#" class="icon-cancel-circled unset" id="date_creation_unset-{$element.id}"></a>
      </label>
      
      
    </div>
    
    <div class="half-line-info-box">
      <strong>{'Author'|@translate}</strong>
      <input type="text" name="author" id="author-{$element.id}" value="{$element.AUTHOR}">
    </div>
    
    <div class="half-line-info-box">
    <div class="privacy-label-container">
    <strong>Qui peut voir ?</strong> <i>Niveau de confidentialité</i>
    </div>
    <select name="level" id="level-{$element.id}" size="1">
      {html_options options=$level_options selected=$element.level_options_selected}
    </select>
    {* <div class="advanced-filter-item advanced-filter-privacy" >
    <div class="privacy-label-container">
    <strong>{'Who can see this photo?'|@translate}</strong>
    <label class="advanced-filter-item-label" for="privacy-filter" >
    <span class="privacy">{$level_options[$element.LEVEL]}</span>
    </label>
    </div>
    <div class="advanced-filter-item-container">
      <div id="privacy-filter" class="select-bar"></div>
        <div class="slider-bar-wrapper">
            <div class="slider-bar-container privacy-filter-slider" value="{$element.LEVEL_CONVERT}" id="{$element.ID}"></div>
          </div>
      </div>
  </div> *}
    </div>
    
    <div class="full-line-tag-box">
    <strong>{'Tags'|@translate}</strong>
    <select id="tags-{$element.id}" data-selectize="tags" data-value="{$element.TAGS|@json_encode|escape:html}"
    placeholder="{'Type in a search term'|translate}"
    data-create="true" name="tags" id="tags-{$element.id}[]" multiple></select>

    </div>
    
    <div class="full-line-info-box" id="{$element.ID}">
  <strong>{'Linked albums'|@translate} <span class="linked-albums-badge {if $element.related_categories|@count < 1 } badge-red {/if}"> {$element.related_categories|@count} </span></strong>
    {if $element.related_categories|@count < 1}
      <span class="orphan-photo">{'This photo is an orphan'|@translate}</span>
    {else}
      <span class="orphan-photo"></span>
    {/if}
    <div class="related-categories-container">
    {foreach from=$element.related_categories item=$cat_path key=$key}
    <div class="breadcrumb-item album-listed"><span class="link-path">{$cat_path['name']}</span>{if $cat_path['unlinkable']}<span id={$key} class="icon-cancel-circled remove-item"></span>{else}<span id={$key} class="icon-help-circled help-item tiptip" title="{'This picture is physically linked to this album, you can\'t dissociate them'|translate}"></span>{/if}</div>
    {/foreach}
    </div>
    <div class="breadcrumb-item linked-albums add-item {if $element.related_categories|@count < 1 } highlight {/if}"><span class="icon-plus-circled"></span>{'Add'|translate}</div>
    </div>
    
    <div class="full-line-description-box">
      <strong>{'Description'|@translate}</strong>
      <textarea cols="50" rows="4" name="description" class="description-box" id="description-{$element.id}">{$element.DESCRIPTION}</textarea>
    </div>
    <div class="validation-container">
    <div class="save-button-container">
    <div class="buttonLike action-save-picture"><i class="icon-floppy"></i>{'Submit'|@translate}</div>
    </div>
    <div class="local-unsaved-badge badge-container" style="display: none;"><div class="badge-unsaved"><i class="icon-attention">&#xe829;</i>You have unsaved changes</div></div>
    <div class="local-succes-badge badge-container" style="display: none;"><div class="badge-succes"><i class="icon-ok">&#xe819;</i>Changes saved</div></div>
    <div class="local-error-badge badge-container" style="display: none;"><div class="badge-error"><i class="icon-cancel">&#xe822;</i>An error occured</div></div>
    </div>
  </div>
</fieldset>
{/foreach}

<div style="margin: 30px 0; display: flex; justify-content: space-between;  padding: 1em;">
  <div style="margin-right: 21px;" class="pagination-per-page">
  <span style="font-weight: bold;color: unset;">{'photos per page'|@translate} :</span>
  <a href="{$U_ELEMENTS_PAGE}&amp;display=5">5</a>
  <a href="{$U_ELEMENTS_PAGE}&amp;display=10">10</a>
  <a href="{$U_ELEMENTS_PAGE}&amp;display=50">50</a>
</div>
    <div style="margin-left: 22px;">
    
    <div class="pagination-reload">
    {if !empty($navbar) }<a class="button-reload tiptip" title="Pagination has changed and needs to be reloaded !" style="display: none;" href="{$F_ACTION}"><i class="icon-cw"></i></a>{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}
    </div>
    </div>

    
  </div>
{/if}

<div class="bottom-save-bar">
  <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
  <div class="badge-container global-unsaved-badge" style="display: none;">
    <div class="badge-unsaved"><i class="icon-attention">&#xe829;</i>
        <span id="unsaved-count"></span> image(s) contains unsaved changes
    </div>
  </div>
  <div class="badge-container global-succes-badge" style="display: none;">
    <div class="badge-succes"><i class="icon-attention">&#xe829;</i>
      Changes saved
    </div>
  </div>
  <div class="badge-container global-error-badge" style="display: none;">
    <div class="badge-error"><i class="icon-attention">&#xe829;</i>
      Error during save
    </div>
  </div>
  <div class="buttonLike action-save-global"><i class="icon-floppy"></i>Save all photos</div>  
</div>

</div>

{include file='include/album_selector.inc.tpl' 
  title={'Associate to album'|@translate}
  searchPlaceholder={'Search'|@translate}
}
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

.selectize-input.items.not-full.has-options,
.selectize-input.items.not-full.has-options.focus.input-active.dropdown-active,
.selectize-input.items.not-full, 
.selectize-input.items.full{
  border: 1px solid #D3D3D3 !important;
}

.breadcrumb-item.add-item.highlight{
  color: #3C3C3C !important;
}

.breadcrumb-item{
  margin: 5px 0 5px 0 !important;
}

.album-listed{
  background-color: #FFFFFF !important;
}

.elementEdit{
  position: relative;
  display:flex;
  flex-direction:row;
  box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.2);
  background-color:#FAFAFA;
  padding:0px;
  margin: 1.5em !important;
  border-radius: 4px;
}

.pictureIdLabel{
  position: absolute;
  bottom: 0;
  right: 0;
  color:#7A7A7A;
  font-size: 20px;
  padding: 10px;
}


.media-box{
  display: flex;
  background-color: #3C3C3C;
  width:33%;
  justify-content: center;
  position: relative;
  border-radius: 4px 0 0 4px;
}

.media-box-embed{
  height: 100%;
  object-fit: contain;
  position: absolute; 
}

.media-hover{
  opacity:0%;
  background-color: #0000009c;
  position: relative;
  height: 100%;
  width: 100%;
}

.media-hover:hover{
  opacity: 100%;
}

.main-info-container{
  display:flex;
  flex-direction:column;
  text-align:center;
  padding:20px;
  row-gap:15px;
  width:200px;
}

.main-info-block{
  display:flex;
  flex-direction:column;
  border: 1px solid #D3D3D3;
  background: #FFF;
  border-radius: 2px;
  flex:1;
  align-items: center;
  justify-content: center;
}

.main-info-icon{
  width:40px;
  height:40px;
  margin-bottom:5px;
  fill: #3C3C3C;
}

.main-info-title{
  color: #000;
  text-align: center;
  font-size: 12px;
  font-weight: 700;
  line-height: normal;
  width:100px;
  overflow-wrap: break-word;
}

.main-info-desc{
  color: #777;
  text-align: center;
  font-family: "Open Sans";
  font-size: 12px;
  font-style: normal;
  font-weight: 400;
  line-height: normal;
  width:100px;
}

.info-container{
  flex:1;
  display:flex;
  flex-direction:row;
  flex-wrap:wrap;
  align-content: flex-start;
  padding: 20px 0px;
  gap: 10px 0px;
  color:#7A7A7A;
  text-align: left;
}

.half-line-info-box{
  flex: 0 0 calc(50% - 20px);
  margin: 0px 10px;
  display:flex;
  flex-direction:column;
  text-align:left;
  height: 50px;
}

.full-line-info-box{
  flex: 0 0 calc(100% - 20px);
  margin: 0px 10px;
  display:flex;
  flex-direction:column;
}

.full-line-tag-box{
  flex: 0 0 calc(100% - 20px);
  margin: 0px 10px;
  display:flex;
  flex-direction:column;
}

.calendar-box{
  flex: 0 0 calc(50% - 20px);
  height: 50px;
  margin: 0px 10px;
  display:flex;
  flex-direction:column;
}

.full-line-info-box input,
.half-line-info-box input,
.half-line-info-box select{
  display: flex;
  border-radius: 2px;
  padding: 0 7px;
  border: 1px solid #D3D3D3;
  background: #FFF;
  flex: 1;
}

.full-line-tag-box select{
  display: flex;
  border-radius: 2px;
  padding: 0 7px;
  border: 1px solid #D3D3D3;
  background: #FFF;
}

.calendar-input{
  display: flex;
  border-radius: 2px;
  padding-left: 7px;
  border: 1px solid #D3D3D3;
  background: #FFF;
  align-items: center;
  justify-content: space-between;
  flex: 1;
}

.calendar-box input{
  border:none;
  outline: none;
  height: 90%;
  width: 90%;
}

.full-line-description-box{
  flex: 0 0 calc(100% - 20px);
  min-height: 50px;
  margin: 0px 10px;
  display:flex;
  flex-direction:column;
}

.description-box{
  resize: none;
  border-radius: 2px;
  border: 1px solid #D3D3D3;
  background: #FFF;
}
.full-line-info-box input,
.half-line-info-box input,
.description-box{
  outline: none !important;
}



.privacy-label-container{
  display: flex;
  flex-direction: row;
  gap: 5px;
}

.privacy-label-container span{
  color: #ffa646;
  font-weight: bold;
}
.bottom-save-bar{
  display:flex;
  flex-direction: row;
  position: fixed;
  bottom: 0;
  right: 0;
  width: calc(100% - 205px);
  background-color: #ffffff;
  justify-content: flex-end;
  align-items: center;
  z-index: 101;
  border-top: 1px solid #CCCCCC;
}

.action-save-global{
  margin: 10px 0;
  margin-right: 2%;
}

.badge-container {
  text-align: right;
  margin-right: 2%;
}
.badge-unsaved{
  padding: 5px 10px;
  border-radius: 100px;
  background-color: #FADDA2;
  color: #E18C32;
}

.badge-succes{
  padding: 5px 10px;
  border-radius: 100px;
  background-color: #D6FFCF;
  color: #6DCE5E;
}

.badge-error{
  padding: 5px 10px;
  border-radius: 100px;
  background-color: #F8D7DC;
  color: #EB3D33;
}

.badge-count{
  padding: 10px 10px;
  border-radius: 100px;
  background-color: #3C3C3C;
  color: #FFFFFF;
}

.pagination-reload{
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
}

.deleted-element{
  display:flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
  background-color:#D6FFCF;
  color: #6DCE5E;
  padding:0px;
  margin: 1.5em !important;
  border-radius: 4px;
}

.validation-container{
  margin: 20px 0 0 2px;
  display: flex;
  flex-direction: row;
  justify-content: flex-start;
  align-items: center;  
  flex: 1;
  gap: 10px;
}

.save-button-container{
  display: flex;
  justify-content: center;
  align-items: center;
  width: 90px;
  height: 45px;
}

.disabled {
  pointer-events: none;
  opacity: 0.5;
}


</style>
