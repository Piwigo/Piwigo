{combine_script id='doubleSlider' load='footer' require='jquery.ui.slider' path='admin/themes/default/js/doubleSlider.js'}
{combine_script id='jquery.selectize' load='header' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}
{combine_script id='jquery.ui.slider' require='jquery.ui' load='async' path='themes/default/js/ui/minified/jquery.ui.slider.min.js'}
{combine_css path="themes/default/js/ui/theme/jquery.ui.slider.css"}
{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}

{footer_script}
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
{if isset($filter_category_selected) and $filter_category_selected}
const selected_filter_cat_ids = ["{$filter_category_selected|@json_encode|escape:html}"];
{else}
const selected_filter_cat_ids = [];
{/if}

const str_select_album = "{'Select at least one album'|@translate|escape:javascript}";
const str_select_tag = "{'Select at least one tag'|@translate|escape:javascript}";
let errorFilters = '';
{/footer_script}

{combine_script id='batchManagerFilter' load='footer' path='admin/themes/default/js/batchManagerFilter.js'}
<fieldset>
<legend><span class='icon-filter icon-green'></span>{'Filter'|@translate}</legend>
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
    {* categories *}
    <li id="filter_category" {if !isset($filter.category)}style="display:none"{/if}>
      <input type="checkbox" name="filter_category_use" class="useFilterCheckbox" {if isset($filter.category)}checked="checked"{/if}>
      <p>{'Album'|@translate}</p>
      <a href="#" class="removeFilter" title="{'remove this filter'|translate}"><span>[x]</span></a>
      {* <select data-selectize="categories" data-value="{$filter_category_selected|@json_encode|escape:html}"
        data-default="first" name="filter_category"></select> *}
        <div id="selectedAlbumFilterArea" {if !$filter_category_selected}style="display: none;"{/if}>
          <div class="selectedAlbum" id="selectedAlbumFilter">
            <input type="hidden" name="filter_category" id="filterCategoryValue" value="{$filter_category_selected|@json_encode|escape:html}">
            <span class="icon-sitemap" id="selectedAlbumNameFilter">{$filter_category_selected_name}</span>
            <a class="icon-pencil" id="selectedAlbumEditFilter"></a>
          </div>
          <label class="font-checkbox"><span class="icon-check"></span><input type="checkbox" name="filter_category_recursive" {if isset($filter.category_recursive)}checked="checked"{/if}> {'include child albums'|@translate}</label>
        </div>
        <p class="head-button-1" id="selectAlbumFilter" {if $filter_category_selected}style="display: none;"{/if}>
          {"Select an album"|translate}
        </p>
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
      <p title="{'Help'|@translate}" class="help-popin-search">
        <i class="icon-help-circled"></i>
        <span>{'Search tips'|translate}</span>
      </p>
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

    <div id="errorFilter" class="icon-red-error" style="display: none; margin-left: 10px;"></div>
  </div>
</div>
</fieldset>

<div class="bg-modal" id="modalQuickSearch">
  <div class="modal-content">
    <a class="icon-cancel close-modal" id="closeModalQuickSearch"></a>

    <div class="quick-search-content">
      <div class="quick-search-header">
        <div class="AddIconContainer">
          <span class="AddIcon icon-blue icon-search"></span>
        </div>
      </div>
      <div class="quick-search-syntax">
        {assign var=is_dark_mode value=$themeconf.colorscheme == 'dark'}
        {include file='themes/default/template/help/quick_search.tpl' dark_mode=$is_dark_mode}
      </div>
    </div>
  </div>
</div>
{include file='include/album_selector.inc.tpl'}