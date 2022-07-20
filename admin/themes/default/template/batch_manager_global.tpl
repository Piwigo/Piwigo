{include file='include/datepicker.inc.tpl' load_mode='async'}
{include file='include/colorbox.inc.tpl' load_mode='async'}
{include file='include/add_album.inc.tpl' load_mode='async'}

{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

{combine_script id='jquery.ui.slider' require='jquery.ui' load='async' path='themes/default/js/ui/minified/jquery.ui.slider.min.js'}
{combine_css path="themes/default/js/ui/theme/jquery.ui.slider.css"}
{combine_script id='doubleSlider' load='async' require='jquery.ui.slider' path='admin/themes/default/js/doubleSlider.js'}

{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}

{combine_script id='jquery.progressBar' load='async' path='themes/default/js/plugins/jquery.progressbar.min.js'}
{combine_script id='jquery.ajaxmanager' load='async' path='themes/default/js/plugins/jquery.ajaxmanager.js'}

{combine_script id='batchManagerGlobal' load='async' require='jquery,datepicker,jquery.colorbox,addAlbum,doubleSlider' path='admin/themes/default/js/batchManagerGlobal.js'}

{footer_script}
var lang = {
	Cancel: '{'Cancel'|translate|escape:'javascript'}',
	deleteProgressMessage: "{'Deletion in progress'|translate|escape:'javascript'}",
	syncProgressMessage: "{'Synchronization in progress'|translate|escape:'javascript'}",
	AreYouSure: "{'Are you sure?'|translate|escape:'javascript'}",
  generateMsg: "{'Generate multiple size images'|@translate}"
};

jQuery(document).ready(function() {

  {* <!-- TAGS --> *}
  var tagsCache = new TagsCache({
    serverKey: '{$CACHE_KEYS.tags}',
    serverId: '{$CACHE_KEYS._hash}',
    rootUrl: '{$ROOT_URL}'
  });

  tagsCache.selectize(jQuery('[data-selectize=tags]'), { lang: {
    'Add': '{'Create'|translate}'
  }});

  {* <!-- CATEGORIES --> *}
  window.categoriesCache = new CategoriesCache({
    serverKey: '{$CACHE_KEYS.categories}',
    serverId: '{$CACHE_KEYS._hash}',
    rootUrl: '{$ROOT_URL}'
  });
  
  var associated_categories = {$associated_categories|@json_encode};

  categoriesCache.selectize(jQuery('[data-selectize=categories]'), {
    filter: function(categories, options) {
      if (this.name == 'dissociate') {
        var filtered = jQuery.grep(categories, function(cat) {
          return !!associated_categories[cat.id];
        });

        if (filtered.length > 0) {
          options.default = filtered[0].id;
        }

        return filtered;
      }
      else {
        return categories;
      }
    }
  });

});

var nb_thumbs_page = {$nb_thumbs_page};
var nb_thumbs_set = {$nb_thumbs_set};
var applyOnDetails_pattern = "{'on the %d selected photos'|@translate}";
var all_elements = [{if !empty($all_elements)}{','|@implode:$all_elements}{/if}];

var selectedMessage_pattern = "{'%d of %d photos selected'|@translate}";
var selectedMessage_none = "{'No photo selected, %d photos in current set'|@translate}";
var selectedMessage_all = "{'All %d photos are selected'|@translate}";

$(document).ready(function() {
  function checkPermitAction() {
    var nbSelected = 0;
    if ($("input[name=setSelected]").is(':checked')) {
      nbSelected = nb_thumbs_set;
    }
    else {
      nbSelected = $(".thumbnails input[type=checkbox]").filter(':checked').length;
    }

    if (nbSelected == 0) {
      $("#permitAction").hide();
      $("#forbidAction").show();
    }
    else {
      $("#permitAction").show();
      $("#forbidAction").hide();
    }

    $("#applyOnDetails").text(
      sprintf(
        applyOnDetails_pattern,
        nbSelected
      )
    );

    // display the number of currently selected photos in the "Selection" fieldset
    if (nbSelected == 0) {
      $("#selectedMessage").text(
        sprintf(
          selectedMessage_none,
          nb_thumbs_set
        )
      );
    }
    else if (nbSelected == nb_thumbs_set) {
      $("#selectedMessage").text(
        sprintf(
          selectedMessage_all,
          nb_thumbs_set
        )
      );
    }
    else {
      $("#selectedMessage").text(
        sprintf(
          selectedMessage_pattern,
          nbSelected,
          nb_thumbs_set
        )
      );
    }
  }



  $("[id^=action_]").hide();

  $("select[name=selectAction]").change(function () {
    $("[id^=action_]").hide();

    var action = $(this).prop("value");
    if (action == 'move') {
      action = 'associate';
    }

    $("#action_"+action).show();

    if ($(this).val() != -1) {
      $("#applyActionBlock").show();
    }
    else {
      $("#applyActionBlock").hide();
    }
    if ($(this).val() == "delete" || $(this).val() == "delete_derivatives") {
      $("#confirmDel").css("visibility", "visible");
    } else {
      $("#confirmDel").css("visibility", "hidden");  
    }
  });

  $(".wrap1 label").click(function (event) {
    $("input[name=setSelected]").prop('checked', false);

    var li = $(this).closest("li");
    var checkbox = $(this).children("input[type=checkbox]");

    checkbox.triggerHandler("shclick",event);

    if ($(checkbox).is(':checked')) {
      $(li).addClass("thumbSelected");
    }
    else {
      $(li).removeClass('thumbSelected');
    }

    checkPermitAction();
  });

  $("#selectAll").click(function () {
    $("input[name=setSelected]").prop('checked', false);
    selectPageThumbnails();
    checkPermitAction();
    return false;
  });

  function selectPageThumbnails() {
    $(".thumbnails label").each(function() {
      var checkbox = $(this).children("input[type=checkbox]");

      $(checkbox).prop('checked', true).trigger("change");
      $(this).closest("li").addClass("thumbSelected");
    });
  }

  $("#selectNone").click(function () {
    $("input[name=setSelected]").prop('checked', false);

    $(".thumbnails label").each(function() {
      var checkbox = $(this).children("input[type=checkbox]");

      if (jQuery(checkbox).is(':checked')) {
        $(checkbox).prop('checked', false).trigger("change");
      }

      $(this).closest("li").removeClass("thumbSelected");
    });
    checkPermitAction();
    return false;
  });

  $("#selectInvert").click(function () {
    $("input[name=setSelected]").prop('checked', false);

    $(".thumbnails label").each(function() {
      var checkbox = $(this).children("input[type=checkbox]");

      $(checkbox).prop('checked', !$(checkbox).is(':checked')).trigger("change");

      if ($(checkbox).is(':checked')) {
        $(this).closest("li").addClass("thumbSelected");
      }
      else {
        $(this).closest("li").removeClass('thumbSelected');
      }
    });
    checkPermitAction();
    return false;
  });

  $("#selectSet").click(function () {
    selectPageThumbnails();
    $("input[name=setSelected]").prop('checked', true);
    checkPermitAction();
    return false;
  });


  jQuery("input[name=confirm_deletion]").change(function() {
    jQuery("#confirmDel span.errors").css("visibility", "hidden");
  });

  jQuery('#applyAction').click(function() {
		var action = jQuery('[name="selectAction"]').val();
		if (action == 'delete_derivatives') {
			let d_count = $('#confirmDel input[type=checkbox]').filter(':checked').length
			let e_count = $('input[name="setSelected"]').is(':checked') ? nb_thumbs_set : $('.thumbnails input[type=checkbox]').filter(':checked').length;
      if (!jQuery("#confirmDel input[name=confirm_deletion]").is(':checked')) {
        jQuery("#confirmDel span.errors").css("visibility", "visible");
        return false;
      } else {
        return true;
      }
    }

		if (action != 'generate_derivatives'
			|| derivatives.finished() )
		{
			return true;
		}

		jQuery('.bulkAction').hide();

		var queuedManager = jQuery.manageAjax.create('queued', {
			queue: true,
			cacheResponse: false,
			maxRequests: 1
		});

		derivatives.elements = [];
		if (jQuery('input[name="setSelected"]').is(':checked'))
			derivatives.elements = all_elements;
		else
			jQuery('.thumbnails input[type=checkbox]').each(function() {
				if (jQuery(this).is(':checked')) {
					derivatives.elements.push(jQuery(this).val());
				}
			});

		jQuery('#applyActionBlock').hide();
		jQuery('select[name="selectAction"]').hide();
    jQuery('.permitActionListButton div').addClass('hidden');
		jQuery('#regenerationMsg').show();

		progress_start();
    progress();
		getDerivativeUrls();
		return false;
  });

  checkPermitAction();

  jQuery("select[name=filter_prefilter]").change(function() {
    jQuery("#empty_caddie").toggle(jQuery(this).val() == "caddie");
    jQuery("#duplicates_options").toggle(jQuery(this).val() == "duplicates");
    jQuery("#delete_orphans").toggle(jQuery(this).val() == "no_album");
    jQuery("#sync_md5sum").toggle(jQuery(this).val() == "no_sync_md5sum");
  });
});

{*<!-- sliders config -->*}
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

{/footer_script}

{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}
{combine_css path="admin/themes/default/fontello/css/animation.css" order=10} {* order 10 is required, see issue 1080 *}

<div id="batchManagerGlobal">
  <form action="{$F_ACTION}" method="post">
  <input type="hidden" name="start" value="{$START}">
  <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">

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
          <input name="q" size=40 value="{$filter.search.q|stripslashes|htmlspecialchars}">
          <a href="admin/popuphelp.php?page=quick_search" onclick="popuphelp(this.href);return false;" title="{'Help'|@translate}"><span class="icon-help-circled">{'Search tips'|translate}</span></a>
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

  <fieldset>

    <legend><span class='icon-check icon-blue '></span>{'Selection'|@translate}</legend>

  {if !empty($thumbnails)}
  <p id="checkActions">
{if $nb_thumbs_set > $nb_thumbs_page}
    <a href="#" id="selectAll">{'The whole page'|@translate}</a>
    <a href="#" id="selectSet">{'The whole set'|@translate}</a>
{else}
    <a href="#" id="selectAll">{'All'|@translate}</a>
{/if}
    <a href="#" id="selectNone">{'None'|@translate}</a>
    <a href="#" id="selectInvert">{'Invert'|@translate}</a>

    <span id="selectedMessage"></span>

    <input type="checkbox" name="setSelected" style="display:none" {if count($selection) == $nb_thumbs_set}checked="checked"{/if}>
  </p>

	<ul class="thumbnails">
		{html_style}
UL.thumbnails SPAN.wrap2{ldelim}
  width: {$thumb_params->max_width()+2}px;
}
UL.thumbnails SPAN.wrap2 {ldelim}
  height: {$thumb_params->max_height()+25}px;
}
		{/html_style}
		{foreach from=$thumbnails item=thumbnail}
		{assign var='isSelected' value=$thumbnail.id|@in_array:$selection}
		<li{if $isSelected} class="thumbSelected"{/if}>
			<span class="wrap1">
				<label class="font-checkbox">
					<span class="icon-check"></span><input type="checkbox" name="selection[]" value="{$thumbnail.id}" {if $isSelected}checked="checked"{/if}>
					<span class="wrap2">
					<div class="actions">
            <a href="{$thumbnail.U_EDIT}" target="_blank" class="icon-pencil" title="{'Edit photo'|@translate}"></a>
            <a href="{$thumbnail.FILE_SRC}" class="preview-box icon-zoom-square" title="{'Zoom'|@translate}"></a>
          </div>
						{if $thumbnail.level > 0}
						<em class="levelIndicatorF" title="{'Who can see these photos?'|@translate} : ">{'Level %d'|@sprintf:$thumbnail.level|@translate}</em>
						{/if}
						<img src="{$thumbnail.thumb->get_url()}" alt="{$thumbnail.file}" title="{$thumbnail.TITLE|@escape:'html'}" {$thumbnail.thumb->get_size_htm()}>
					</span>
				</label>
			</span>
		</li>
		{/foreach}
	</ul>

  {if !empty($navbar) }
  <div class="batchManager-pagination">
    <div class="pagination-per-page">
      <span>{'display'|@translate}</span>
      <a href="{$U_DISPLAY}&amp;display=20">20</a>
      <a href="{$U_DISPLAY}&amp;display=50">50</a>
      <a href="{$U_DISPLAY}&amp;display=100">100</a>
      <a href="{$U_DISPLAY}&amp;display=all">{'all'|@translate}</a>
    </div>

    {include file='navigation_bar.tpl'|@get_extent:'navbar'}
  </div>
  {/if}

  {else}
  <div class="selectionEmptyBlock">{'No photo in the current set.'|@translate}</div>
  {/if}
  </fieldset>

  <fieldset id="action">

    <legend><span class='icon-cog icon-red'></span>{'Action'|@translate}</legend>
      <div id="forbidAction"{if count($selection) != 0} style="display:none"{/if}>{'No photos selected, no actions possible.'|@translate}</div>
      <div id="permitAction"{if count($selection) == 0} style="display:none"{/if}>
    
    <div class="permitActionListButton">
      <div>
        <select name="selectAction">
          <option value="-1">{'Choose an action'|@translate}</option>
          <option disabled="disabled">------------------</option>
          <option value="delete" class="icon-trash">{'Delete selected photos'|@translate}</option>
          <option value="associate">{'Associate to album'|@translate}</option>
          <option value="move">{'Move to album'|@translate}</option>
      {if !empty($associated_categories)}
          <option value="dissociate">{'Dissociate from album'|@translate}</option>
      {/if}
          <option value="add_tags">{'Add tags'|@translate}</option>
      {if !empty($associated_tags)}
          <option value="del_tags">{'remove tags'|@translate}</option>
      {/if}
          <option value="author">{'Set author'|@translate}</option>
          <option value="title">{'Set title'|@translate}</option>
          <option value="date_creation">{'Set creation date'|@translate}</option>
          <option value="level" class="icon-lock">{'Who can see these photos?'|@translate}</option>
          <option value="metadata">{'Synchronize metadata'|@translate}</option>
      {if ($IN_CADDIE)}
          <option value="remove_from_caddie">{'Remove from caddie'|@translate}</option>
      {else}
          <option value="add_to_caddie">{'Add to caddie'|@translate}</option>
      {/if}
    		<option value="delete_derivatives">{'Delete multiple size images'|@translate}</option>
    		<option value="generate_derivatives">{'Generate multiple size images'|@translate}</option>
      {if !empty($element_set_global_plugins_actions)}
        {foreach from=$element_set_global_plugins_actions item=action}
          <option value="{$action.ID}">{$action.NAME}</option>
        {/foreach}
      {/if}
        </select>
      </div>
      <p id="confirmDel" style="visibility:hidden">
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="confirm_deletion" value="1"> {'Are you sure?'|@translate}</input>
        </label><br/><br/>
        <span class="errors" style="visibility:hidden;margin:0;">{"You need to confirm deletion"|translate}</span>
      </p>
      <p id="applyActionBlock" style="display:none;margin:1em 0 0 0;" class="actionButtons">
        <button id="applyAction" name="submit" type="submit" class="buttonLike">
          <i class="icon-cog-alt"></i> {'Apply action'|translate}
        </button>

        <span id="applyOnDetails"></span>
      </p>
    </div>
    <div class="permitActionItem">
      <!-- delete -->
      <div id="action_delete" class="bulkAction">
      </div>

      <!-- associate -->{* also used for "move" action *}
      <div id="action_associate" class="bulkAction">
        <select data-selectize="categories" data-default="" name="associate" style="width:600px" placeholder="{'Select an album... or type it!'|@translate}"></select>
        <a href="#" data-add-album="associate" title="{'create a new album'|@translate}" class="icon-plus"></a>
      </div>

      <!-- dissociate -->
      <div id="action_dissociate" class="bulkAction">
        <select data-selectize="categories" placeholder="{'Type in a search term'|translate}"
          name="dissociate" style="width:600px"></select>
      </div>


      <!-- add_tags -->
      <div id="action_add_tags" class="bulkAction">
        <select data-selectize="tags" data-create="true" placeholder="{'Type in a search term'|translate}"
          name="add_tags[]" multiple style="width:400px;"></select>
      </div>

      <!-- del_tags -->
      <div id="action_del_tags" class="bulkAction">
  {if !empty($associated_tags)}
        <select data-selectize="tags" name="del_tags[]" multiple style="width:400px;"
          placeholder="{'Type in a search term'|translate}">
        {foreach from=$associated_tags item=tag}
          <option value="{$tag.id}">{$tag.name}</option>
        {/foreach}
        </select>
  {/if}
      </div>

      <!-- author -->
      <div id="action_author" class="bulkAction">
      <input type="text" class="large" name="author" placeholder="{'Type here the author name'|@translate}">
      <label class="font-checkbox"><span class="icon-check"></span><input type="checkbox" name="remove_author"> {'remove author'|@translate}</label>
      </div>

      <!-- title -->
      <div id="action_title" class="bulkAction">
      <input type="text" class="large" name="title" placeholder="{'Type here the title'|@translate}">
      <label class="font-checkbox"><span class="icon-check"></span><input type="checkbox" name="remove_title"> {'remove title'|@translate}</label>
      </div>

      <!-- date_creation -->
      <div id="action_date_creation" class="bulkAction">
        <label class="font-checkbox"><span class="icon-check"></span><input type="checkbox" name="remove_date_creation"> {'remove creation date'|@translate}</label><br>
        <div id="set_date_creation">
          <input type="hidden" name="date_creation" value="{$DATE_CREATION}">
          <label>
            <i class="icon-calendar"></i>
            <input type="text" data-datepicker="date_creation" readonly>
          </label>
        </div>
      </div>

      <!-- level -->
      <div id="action_level" class="bulkAction">
          <select name="level" size="1">
            {html_options options=$level_options selected=$level_options_selected}
          </select>
      </div>

      <!-- metadata -->
      <div id="action_metadata" class="bulkAction">
      </div>

      <!-- generate derivatives -->
      <div id="action_generate_derivatives" class="bulkAction">
        <div class="deleteDerivButtons">
          <a href="javascript:selectGenerateDerivAll()">{'All'|@translate}</a>
          <a href="javascript:selectGenerateDerivNone()">{'None'|@translate}</a>
        </div>
        <br>
        {foreach from=$generate_derivatives_types key=type item=disp}
          <label class="font-checkbox"><span class="icon-check"></span><input type="checkbox" name="generate_derivatives_type[]" value="{$type}"> {$disp}</label>
        {/foreach}
      </div>

      <!-- delete derivatives -->
      <div id="action_delete_derivatives" class="bulkAction">
        <div class="deleteDerivButtons">
          <a href="javascript:selectDelDerivAll()">{'All'|@translate}</a>
          <a href="javascript:selectDelDerivNone()">{'None'|@translate}</a>
        </div>
        <br>
        {foreach from=$del_derivatives_types key=type item=disp}
          <label class="font-checkbox"><span class="icon-check"></span><input type="checkbox" name="del_derivatives_type[]" value="{$type}"> {$disp}</label>
        {/foreach}
      </div>

      <!-- plugins -->
  {if !empty($element_set_global_plugins_actions)}
    {foreach from=$element_set_global_plugins_actions item=action}
      <div id="action_{$action.ID}" class="bulkAction">
      {if !empty($action.CONTENT)}{$action.CONTENT}{/if}
      </div>
    {/foreach}
  {/if}
      </div>
    </div> <!-- #permitAction -->
    <div id="regenerationMsg" class="bulkAction" style="display:none;margin-left:0;">
        <div id="regenerationStatus" style="margin-bottom:10px;">
          <span id="regenerationText">{'Generate multiple size images'|@translate}</span>
          <span class="badge-number" style="font-size:12.8px"></span>
        </div>
        <input type="hidden" name="regenerateSuccess" value="0">
        <input type="hidden" name="regenerateError" value="0">
      </div>
    <!-- progress bar -->
    <div id="uploadingActions" style="display:none">
      <div class="big-progressbar" style="max-width:100%;margin-bottom: 10px;">
        <div class="progressbar" style="width:0%"></div>
      </div>
    </div>
  </fieldset>

  </form>

</div> <!-- #batchManagerGlobal -->

<style>
#action_associate .selectize-input {
  min-width: 500px;
  height: 44px;
}

#action_add_tags .item,
#action_add_tags .item.active {
  background-image:none;
  background-color: #ffa646;
  border-color: transparent;
  color: black;

  border-radius: 20px;
}

#action_add_tags .item .remove,
#action_add_tags .item .remove {
  background-color: transparent;
  border-top-right-radius: 20px;
  border-bottom-right-radius: 20px;
  color: black;
  
  border-left: 1px solid transparent;

}
#action_add_tags .item .remove:hover,
#action_add_tags .item .remove:hover {
  background-color: #ff7700;
}
</style>