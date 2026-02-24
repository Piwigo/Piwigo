{include file='include/datepicker.inc.tpl' load_mode='async'}
{include file='include/colorbox.inc.tpl' load_mode='footer'}
{include file='include/add_album.inc.tpl' load_mode='async'}

{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

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
var all_elements = [{if !empty($all_elements)}{$all_elements|join:","}{/if}];

var selectedMessage_pattern = "{'%d of %d photos selected'|@translate}";
var selectedMessage_none = "{'No photo selected, %d photos in current set'|@translate}";
var selectedMessage_all = "{'All %d photos are selected'|@translate}";
const str_add_alb_associate = "{"Add Album"|@translate}";
const str_select_alb_associate = "{"Select an album"|@translate}";

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
    {* if (action == 'move') {
      action = 'associate';
    } *}

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
    $("input[name=setSelected]").prop('checked', false).trigger('change');

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
    $("input[name=setSelected]").prop('checked', false).trigger('change');
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
    $("input[name=setSelected]").prop('checked', false).trigger('change');

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
    $("input[name=setSelected]").prop('checked', false).trigger('change');

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
    $("input[name=setSelected]").prop('checked', true).trigger('change');
    checkPermitAction();
    return false;
  });

  $("input[name=setSelected]").change(function() {
    $('input[name=whole_set]').val(this.checked ? all_elements.join(',') : '');
  });

  {*
    if the whole set is selected on page load (after a first action has been applied),
    trigger a change to make sure input[name=whole_set] is updated
  *}
  if ($('input[name="setSelected"]').is(':checked')) {
    $("input[name=setSelected]").trigger('change');
  }

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
{/footer_script}

{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}
{combine_css path="admin/themes/default/fontello/css/animation.css" order=10} {* order 10 is required, see issue 1080 *}

<div id="batchManagerGlobal">
  <form action="{$F_ACTION}" method="post">
  <input type="hidden" name="start" value="{$START}">
  <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
  {include file='include/batch_manager_filter.inc.tpl' 
  title={'Batch Manager Filter'|@translate}
  searchPlaceholder={'Filters'|@translate}
  }
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
    <input type="hidden" name="whole_set" value="">
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
          <option value="level" class="icon-lock">{'Who can see these photos?'|@translate} ({'Privacy level'|translate})</option>
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

      <!-- associate -->
      <div id="action_associate" class="bulkAction">
        <div class="head-button-2 icon-plus-circled" id="associate_as">
          <p>{"Select an album"|translate}</p>
        </div>
        <div class="selected-associate-action">
        </div>
      </div>

      <!-- move -->
      <div id="action_move" class="bulkAction">
        <select data-selectize="categories" data-default="" name="move" style="width:600px" placeholder="{'Select an album... or type it!'|@translate}"></select>
        <a href="#" data-add-album="move" title="{'create a new album'|@translate}" class="icon-plus"></a>
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
      <label class="font-checkbox"><span class="icon-check"></span><input type="checkbox" name="remove_author"> {'remove author'|@translate}</label>
      <input type="text" class="large" name="author" placeholder="{'Type here the author name'|@translate}">
      </div>

      <!-- title -->
      <div id="action_title" class="bulkAction">
      <label class="font-checkbox"><span class="icon-check"></span><input type="checkbox" name="remove_title"> {'remove title'|@translate}</label>
      <input type="text" class="large" name="title" placeholder="{'Type here the title'|@translate}">
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
{include file='include/album_selector.inc.tpl'}

<style>
#action_move .selectize-input {
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
