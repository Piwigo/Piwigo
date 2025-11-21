{include file='include/autosize.inc.tpl'}
{include file='include/datepicker.inc.tpl'}
{include file='include/colorbox.inc.tpl'}

{combine_script id='jquery.sort' load='footer' path='themes/default/js/plugins/jquery.sort.js'}

{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}

{combine_css path="admin/themes/default/fontello/css/animation.css" order=10}
{assign var='all_selected_album' value=[]}
{footer_script}
{* <!-- PLUGINS --> *}
var activePlugins = {$ACTIVE_PLUGINS|json_encode};
{if isset($CACHE_KEYS)}
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
{/if}


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

str_are_you_sure = "{'Are you sure?'|translate|escape:javascript}";
str_yes = "{'Yes, delete'|translate|escape:javascript}";
str_no = "{'No, I have changed my mind'|translate|@escape:'javascript'}";
str_orphan = "{'This photo is an orphan'|@translate|escape:javascript}";
str_meta_warning = "{'Warning ! Unsaved changes will be lost'|translate|escape:javascript}";
str_meta_yes = "{'I want to continue'|translate|escape:javascript}";
const str_title_ab = "{'Associate to album'|@translate}";

let b_current_picture_id;
{* Check Skeleton extension for more details about extensibility *}
pluginValues = [];
{/footer_script}


{combine_script id='batchManagerUnit' load='footer' require='jquery.ui.effect-blind,jquery.sort' path='admin/themes/default/js/batchManagerUnit.js'}
<div id="batchManagerGlobal" style="margin-bottom: 80px;">
	<div style="clear:both"></div>
	{if isset($ELEMENT_IDS)}
	<div>
		<input type="hidden" name="element_ids" value="{$ELEMENT_IDS}">
	</div>
	{/if}
  {*Filters*}
	<form method="post" action="{$F_ACTION}">
		{include file='include/batch_manager_filter.inc.tpl'   title={'Batch Manager Filter'|@translate}  searchPlaceholder={'Filters'|@translate}}
	</form>
	<legend style="padding: 1em;">
		<span class='icon-menu icon-blue'></span>
		{'List'|@translate}
		<span class="count-badge"> {count($all_elements)}</span>
	</legend>
	{if !empty($elements) }
	<div style="margin: 10px 0; display: flex; justify-content: space-between; padding: 1em; height: 33px;">
	  	<div style="margin-right: 21px;" class="pagination-per-page">
      		<span style="font-weight: bold;color: unset;">{'photos per page'|@translate} :</span>
    		<a href="{$U_ELEMENTS_PAGE}&amp;display=5" class="{if $per_page == 5}selected-pagination{/if}">5</a>
    		<a href="{$U_ELEMENTS_PAGE}&amp;display=10" class="{if $per_page == 10}selected-pagination{/if}">10</a>
    		<a href="{$U_ELEMENTS_PAGE}&amp;display=50" class="{if $per_page == 50}selected-pagination{/if}">50</a>
		</div>

		<div style="margin-left: 22px;">
			<div class="pagination-reload">
				{if !empty($navbar) }
				<a class="button-reload tiptip" title="{'Pagination has changed and needs to be reloaded !'|@translate}" style="display: none;" href="{$F_ACTION}"><i class="icon-cw"></i></a>
				{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}
			</div>
		</div>
	</div>
	{foreach from=$elements item=element} 
	{$all_selected_album[$element.ID] = json_decode($element.related_category_ids)} 
	{footer_script}
      url_delete_{$element.id} = '{$element.U_DELETE}';  
    {/footer_script}
	<div class="infos deleted-badge" data-image_id="{$element.ID}" style="display: none;">
		<i class="icon-ok" style="font-size: 20px;"></i>
		<p>
			&nbsp;{'Image'|@translate}&nbsp;
			<p style="font-weight: bold;">
				#{$element.ID} '{$element.FILE}'
			</p>
			&nbsp;{'was succesfully deleted'|@translate}
		</p>
	</div>
	<fieldset class="elementEdit" id="picture-{$element.ID}" data-image_id="{$element.ID}">
		<div class="metasync-success badge-container" style="display: none;">
			<div class="badge-succes">
				<i class="icon-ok"></i>
				{'Metadata sync complete'|@translate}
			</div>
		</div>
		<div class="pictureIdLabel">
			#{$element.ID}
		</div>
		<div class="media-box">
			<img src="{$element.TN_SRC}" alt="imagename" class="media-box-embed" style="{if $element.FORMAT}width:100%; max-height:100%;{else}max-width:100%; height:100%;{/if}">
			<div class="media-hover">
				<div class='picture-preview-actions'>
					<a class="preview-box icon-zoom-square tiptip" href="{$element.FILE_SRC}" title="{'Zoom'|@translate}"></a>
					<a class="icon-download tiptip" href="{$element.U_DOWNLOAD}" title="{'Download'|@translate}"></a>
					<a class="icon-signal tiptip" href="{$element.U_HISTORY}" title="{'Visit history'|@translate}"></a>
					<a class="icon-pulse tiptip" href="{$element.U_ACTIVITY}" title="{'Activity'|@translate}"></a>
					<a target="_blank" class="icon-pencil tiptip" href="{$element.U_EDIT}" title="{'Edit photo'|@translate}"></a>
					{if !url_is_remote($element.PATH)}
					<a class="icon-arrows-cw tiptip action-sync-metadata" title="{'Synchronize metadata'|@translate}"></a>
					<a class="icon-trash tiptip action-delete-picture" title="{'delete photo'|@translate}"></a>
					{/if}  
				</div>
				  {if isset($element.U_JUMPTO)}
				<a class="see-out" href="{$element.U_JUMPTO}" >
					<p>
						<i class="icon-left-open"></i>
						{'Open in gallery'|@translate}
					</p>
					  {else}
					<a class="see-out disabled" href="#" >
						<p class="" title="{'You don\'t have access to this photo'|translate}" >
							<i class="icon-left-open"></i>
							{'Open in gallery'|translate}
						</p>
						  {/if}
					</a>
				</div>
			</div>
			<div class="main-info-container">
				<div class="main-info-block">
					<div class='info-framed-icon' style="margin-right:0px;">
						<i class='icon-picture'></i>
					</div>
					<span class="main-info-title" id="filename">{$element.FILE}</span>
					<span class="main-info-desc" id="dimensions">{$element.DIMENSIONS}</span>
					<span class="main-info-desc" id="filesize">{$element.FILESIZE}</span>
					<span class="main-info-desc">{$element.EXT}</span>
				</div>
				<div class="main-info-block">
					<div class='info-framed-icon' style="margin-right:0px;">
						<span class='icon-calendar'></span>
					</div>
					<span class="main-info-title first-letter-capitalize">{$element.POST_DATE}</span>
					<span class="main-info-desc">{$element.AGE}</span>
					<span class="main-info-desc">{$element.ADDED_BY}</span>
					<span class="main-info-desc">{$element.STATS}</span>
				</div>
			</div>
			<div class="info-container">
				<div class="half-line-info-box">
					<strong>{'Title'|@translate}</strong>
					<input type="text" name="name" id="name" value="{$element.NAME}">
				</div>
				<div class="calendar-box">
					<strong>{'Creation date'|@translate}</strong>
					<input type="hidden" id="date_creation" name="date_creation-{$element.id}" value="{$element.DATE_CREATION}">
					<label class="calendar-input">
						<i class="icon-calendar"></i>
						<input type="text" data-datepicker="date_creation-{$element.id}" data-datepicker-unset="date_creation_unset-{$element.id}" readonly>
						<a href="#" class="icon-cancel-circled unset datepickerDelete" id="date_creation_unset-{$element.id}"></a>
					</label>
				</div>
				<div class="half-line-info-box">
					<strong>{'Author'|@translate}</strong>
					<input type="text" name="author" id="author" value="{$element.AUTHOR}">
				</div>
				<div class="half-line-info-box">
					<div class="privacy-label-container">
						<strong>{'Who can see ?'|@translate}</strong>
						<i>{'level of confidentiality'|@translate}</i>
					</div>
					<select name="level" id="level" size="1">
						{html_options options=$level_options selected=$element.level_options_selected}
					</select>
				</div>
				<div class="full-line-tag-box" id="action_add_tags">
					<strong>{'Tags'|@translate}</strong>
					<select id="tags" data-selectize="tags" data-value="{$element.TAGS|@json_encode|escape:html}"placeholder="{'Type in a search term'|translate}"data-create="true" name="tags" id="tags-{$element.id}[]" multiple></select>
				</div>
				<div class="full-line-box" id="{$element.ID}">
					<strong>{'Linked albums'|@translate} <span class="linked-albums-badge {if $element.related_categories|@count < 1 } badge-red {/if}"> {$element.related_categories|@count} </span></strong>
					{if $element.related_categories|@count 
					< 1}
					<span class="orphan-photo">{'This photo is an orphan'|@translate}</span>
					{else}
					<span class="orphan-photo"></span>
					{/if}
					<div class="related-categories-container">
						{foreach from=$element.related_categories item=cat_path key=key}
						<div class="breadcrumb-item">
							<span class="link-path">{$cat_path['name']}</span>
							{if $cat_path['unlinkable']}
							<span id={$key} class="icon-cancel-circled remove-item"></span>
							{else}
							<span id={$key} class="icon-help-circled help-item tiptip" title="{'This picture is physically linked to this album, you can\'t dissociate them'|translate}"></span>
							{/if}
						</div>
						{/foreach}
					</div>
					<div class="breadcrumb-item linked-albums add-item {if $element.related_categories|@count < 1 } highlight {/if}">
						<span class="icon-plus-circled"></span>
						{'Add'|translate}
					</div>
				</div>
				<div class="full-line-description-box">
					<strong>{'Description'|@translate}</strong>
					<textarea cols="50" rows="4" name="description" class="description-box" id="description">{$element.DESCRIPTION}</textarea>
				</div>
{if isset($PLUGINS_BATCH_MANAGER_UNIT_ELEMENT_SUBTEMPLATE)}
{foreach from=$PLUGINS_BATCH_MANAGER_UNIT_ELEMENT_SUBTEMPLATE item=PATH}
	{include file=$PATH}
{/foreach} 
{/if} 
				{* Plugins anchor 1 *}
				<div class="validation-container">
					<div class="save-button-container">
						<div class="buttonLike action-save-picture buttonSubmitLocal">
							<i class="icon-floppy"></i>
							{'Save'|@translate}
						</div>
					</div>
					<div class="local-unsaved-badge badge-container" style="display: none;">
						<div class="badge-unsaved">
							<i class="icon-attention"></i>
							{'You have unsaved changes'|@translate}
						</div>
					</div>
					<div class="local-success-badge badge-container" style="display: none;">
						<div class="badge-succes">
							<i class="icon-ok"></i>
							{'Changes saved'|@translate}
						</div>
					</div>
					<div class="local-error-badge badge-container" style="display: none;">
						<div class="badge-error">
							<i class="icon-cancel"></i>
							{'An error has occured'|@translate}
						</div>
					</div>
				</div>
			</div>
		</fieldset>
		{/foreach}
		<div style="margin: 10px 0; display: flex; justify-content: space-between; padding: 1em; height: 33px;">
			<div style="margin-right: 21px;" class="pagination-per-page">
				<span style="font-weight: bold;color: unset;">{'photos per page'|@translate} :</span>
		  		<a href="{$U_ELEMENTS_PAGE}&amp;display=5" class="{if $per_page == 5}selected-pagination{/if}">5</a>
		  		<a href="{$U_ELEMENTS_PAGE}&amp;display=10" class="{if $per_page == 10}selected-pagination{/if}">10</a>
		  		<a href="{$U_ELEMENTS_PAGE}&amp;display=50" class="{if $per_page == 50}selected-pagination{/if}">50</a>
	  		</div>

	  		<div style="margin-left: 22px;">
		  		<div class="pagination-reload">
		{if !empty($navbar) }
			  		<a class="button-reload tiptip" title="{'Pagination has changed and needs to be reloaded !'|@translate}" style="display: none;" href="{$F_ACTION}"><i class="icon-cw"></i></a>
		{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}
		  		</div>
	  		</div>
  		</div>
		{/if}
		<div class="bottom-save-bar">
			<input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
			<div class="badge-container global-unsaved-badge" style="display: none;">
				<div class="badge-unsaved">
					<i class="icon-attention"></i>
					<span id="unsaved-count"></span>
					 {'image(s) contains unsaved changes'|@translate}
				</div>
			</div>
			<div class="badge-container global-succes-badge" style="display: none;">
				<div class="badge-succes">
					<i class="icon-ok"></i>
					{'Changes saved'|@translate}
				</div>
			</div>
			<div class="badge-container global-error-badge" style="display: none;">
				<div class="badge-error">
					<i class="icon-cancel"></i>
					{'An error has occured'|@translate}
				</div>
			</div>
			<div class="buttonLike action-save-global">
				<i class="icon-floppy"></i>
				{'Save all photos'|translate}
			</div>
		</div>
	</div>


{include file='include/album_selector.inc.tpl'}

{footer_script}
var pluginFunctionNames = pluginFunctionMapInit(activePlugins);
const all_related_categories_ids = {$all_selected_album|json_encode};
{/footer_script}

<style>
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

/* .selectize-input.items.not-full.has-options,
.selectize-input.items.not-full.has-options.focus.input-active.dropdown-active,
.selectize-input.items.not-full, 
.selectize-input.items.full{
  border: 1px solid #D3D3D3 !important;
} */

.breadcrumb-item{
  margin: 5px 0 5px 0 !important;
}

</style>
