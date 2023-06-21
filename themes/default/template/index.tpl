{combine_script id='core.switchbox' load='async' require='jquery' path='themes/default/js/switchbox.js'}
{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css path="admin/themes/default/fontello/css/animation.css" order=10} {* order 10 is required, see issue 1080 *}

{$MENUBAR}

{footer_script}
{if isset($GP)}
  global_params = {$GP};
{/if}

{if isset($fullname_of)}
fullname_of_cat = {$fullname_of};
{/if}

{if isset($SEARCH_ID)}
search_id = {$SEARCH_ID};
{/if}

str_word_widget_label = "{'Search for words'|@translate}";
str_tags_widget_label = "{'Search tags'|@translate}";
str_album_widget_label = "{'Search in albums'|@translate}";
str_author_widget_label = "{'Search for Author'|@translate}";

{/footer_script}

{if isset($errors) or isset($infos)}
<div class="content messages{if isset($MENUBAR)} contentWithMenu{/if}">
{include file='infos_errors.tpl'}
</div>
{/if}

{if !empty($PLUGIN_INDEX_CONTENT_BEFORE)}{$PLUGIN_INDEX_CONTENT_BEFORE}{/if}
<div id="content" class="content{if isset($MENUBAR)} contentWithMenu{/if}">
<div class="titrePage{if isset($chronology.TITLE)} calendarTitleBar{/if}">
	<ul class="categoryActions">
{if !empty($image_orders)}
		<li>{strip}<a id="sortOrderLink" title="{'Sort order'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-sort"></span><span class="pwg-button-text">{'Sort order'|@translate}</span>
		</a>
		<div id="sortOrderBox" class="switchBox">
			<div class="switchBoxTitle">{'Sort order'|@translate}</div>
			{foreach from=$image_orders item=image_order name=loop}{if !$smarty.foreach.loop.first}<br>{/if}
			{if $image_order.SELECTED}
			<span>&#x2714; </span>{$image_order.DISPLAY}
			{else}
			<span style="visibility:hidden">&#x2714; </span><a href="{$image_order.URL}" rel="nofollow">{$image_order.DISPLAY}</a>
			{/if}
			{/foreach}
		</div>
		{footer_script}(window.SwitchBox=window.SwitchBox||[]).push("#sortOrderLink", "#sortOrderBox");{/footer_script}
		{/strip}</li>
{/if}
{if !empty($image_derivatives)}
		<li>{strip}<a id="derivativeSwitchLink" title="{'Photo sizes'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-sizes"></span><span class="pwg-button-text">{'Photo sizes'|@translate}</span>
		</a>
		<div id="derivativeSwitchBox" class="switchBox">
			<div class="switchBoxTitle">{'Photo sizes'|@translate}</div>
			{foreach from=$image_derivatives item=image_derivative name=loop}{if !$smarty.foreach.loop.first}<br>{/if}
			{if $image_derivative.SELECTED}
			<span>&#x2714; </span>{$image_derivative.DISPLAY}
			{else}
			<span style="visibility:hidden">&#x2714; </span><a href="{$image_derivative.URL}" rel="nofollow">{$image_derivative.DISPLAY}</a>
			{/if}
			{/foreach}
		</div>
		{footer_script}(window.SwitchBox=window.SwitchBox||[]).push("#derivativeSwitchLink", "#derivativeSwitchBox");{/footer_script}
		{/strip}</li>
{/if}

{if isset($favorite)}
		<li id="cmdFavorite"><a href="{$favorite.U_FAVORITE}" title="{'delete all photos from your favorites'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-favorite-del"></span><span class="pwg-button-text">{'delete all photos from your favorites'|@translate}</span>
		</a></li>
{/if}
{if isset($U_CADDIE)}
		<li id="cmdCaddie"><a href="{$U_CADDIE}" title="{'Add to caddie'|@translate}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-caddie-add"></span><span class="pwg-button-text">{'Caddie'|@translate}</span>
		</a></li>
{/if}
{if isset($U_EDIT)}
		<li id="cmdEditAlbum"><a href="{$U_EDIT}" title="{'Edit album'|@translate}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-category-edit"></span><span class="pwg-button-text">{'Edit'|@translate}</span>
		</a></li>
{/if}
{if isset($U_SEARCH_RULES)}
		{combine_script id='core.scripts' load='async' path='themes/default/js/scripts.js'}
		<li><a href="{$U_SEARCH_RULES}" onclick="popuphelp(this.href); return false;" title="{'Search rules'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-help"></span><span class="pwg-button-text">(?)</span>
		</a></li>
{/if}
{if isset($U_SLIDESHOW)}
		<li id="cmdSlideshow">{strip}<a href="{$U_SLIDESHOW}" title="{'slideshow'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-slideshow"></span><span class="pwg-button-text">{'slideshow'|@translate}</span>
		</a>{/strip}</li>
{/if}
{if isset($U_MODE_FLAT)}
		<li>{strip}<a href="{$U_MODE_FLAT}" title="{'display all photos in all sub-albums'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-category-view-flat"></span><span class="pwg-button-text">{'display all photos in all sub-albums'|@translate}</span>
		</a>{/strip}</li>
{/if}
{if isset($U_MODE_NORMAL)}
		<li>{strip}<a href="{$U_MODE_NORMAL}" title="{'return to normal view mode'|@translate}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-category-view-normal"></span><span class="pwg-button-text">{'return to normal view mode'|@translate}</span>
		</a>{/strip}</li>
{/if}
{if isset($U_MODE_POSTED)}
		<li>{strip}<a href="{$U_MODE_POSTED}" title="{'display a calendar by posted date'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-calendar"></span><span class="pwg-button-text">{'Calendar'|@translate}</span>
		</a>{/strip}</li>
{/if}
{if isset($U_MODE_CREATED)}
		<li>{strip}<a href="{$U_MODE_CREATED}" title="{'display a calendar by creation date'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-camera-calendar"></span><span class="pwg-button-text">{'Calendar'|@translate}</span>
		</a>{/strip}</li>
{/if}
{if !empty($PLUGIN_INDEX_BUTTONS)}{foreach from=$PLUGIN_INDEX_BUTTONS item=button}<li>{$button}</li>{/foreach}{/if}
{if !empty($PLUGIN_INDEX_ACTIONS)}{$PLUGIN_INDEX_ACTIONS}{/if}
	</ul>

<h2>{$TITLE} {if $NB_ITEMS > 0}<span class="badge nb_items">{$NB_ITEMS}</span>{/if}</h2>

{if isset($chronology_views)}
<div class="calendarViews">{'View'|@translate}:
	<a id="calendarViewSwitchLink" href="#">
	{foreach from=$chronology_views item=view}{if $view.SELECTED}{$view.CONTENT}{/if}{/foreach}
	</a>
	<div id="calendarViewSwitchBox" class="switchBox">
		{foreach from=$chronology_views item=view name=loop}{if !$smarty.foreach.loop.first}<br>{/if}
		<span{if !$view.SELECTED} style="visibility:hidden"{/if}>&#x2714; </span><a href="{$view.VALUE}">{$view.CONTENT}</a>
		{/foreach}
	</div>
	{footer_script}(window.SwitchBox=window.SwitchBox||[]).push("#calendarViewSwitchLink", "#calendarViewSwitchBox");{/footer_script}
</div>
{/if}

{if isset($chronology.TITLE)}
<h2 class="calendarTitle">{$chronology.TITLE}</h2>
{/if}

</div>{* <!-- titrePage --> *}

{if !empty($PLUGIN_INDEX_CONTENT_BEGIN)}{$PLUGIN_INDEX_CONTENT_BEGIN}{/if}

{if !empty($no_search_results)}
<p class="search_results">{'No results for'|@translate} :
	<em><strong>
	{foreach $no_search_results as $res}
	{if !$res@first} &mdash; {/if}
	{$res}
	{/foreach}
	</strong></em>
</p>
{/if}

{if !empty($category_search_results)}
<p class="search_results">{'Album results for'|@translate} <strong>{$QUERY_SEARCH}</strong> :
	<em><strong>
	{foreach from=$category_search_results item=res name=res_loop}
	{if !$smarty.foreach.res_loop.first} &mdash; {/if}
	{$res}
	{/foreach}
	</strong></em>
</p>
{/if}

{if !empty($tag_search_results)}
<p class="search_results">{'Tag results for'|@translate} <strong>{$QUERY_SEARCH}</strong> :
	<em><strong>
	{foreach from=$tag_search_results item=tag name=res_loop}
	{if !$smarty.foreach.res_loop.first} &mdash; {/if} <a href="{$tag.URL}">{$tag.name}</a>
	{/foreach}
	</strong></em>
</p>
{/if}

{if isset($FILE_CHRONOLOGY_VIEW)}
{include file=$FILE_CHRONOLOGY_VIEW}
{/if}

{if !empty($CONTENT_DESCRIPTION)}
<div class="additional_info">
	{$CONTENT_DESCRIPTION}
</div>
{/if}

{if !empty($CONTENT)}{$CONTENT}{/if}

{if !empty($CATEGORIES)}{$CATEGORIES}{/if}

{if !empty($cats_navbar)}
	{include file='navigation_bar.tpl'|@get_extent:'navbar' navbar=$cats_navbar}
{/if}

{if !empty($SEARCH_ID)}
{combine_script id='mcs' load='async' require='jquery' path='themes/default/js/mcs.js'}
{* Recherche multicritère *}
<div class="mcs-container">
  <div class="filter-manager-popin">
    <div class="filter-manager-popin-container">
      <span class="pwg-icon-cancel filter-manager-close"></span>

      <div class="mcs-popin-title">Filtres</div>

      <div class="filter-manager-controller-container">
        <label>
          <input data-wid='word' class="filter-manager-controller word" type="checkbox"/>
          <span class="mcs-icon pwg-icon-search">{'Search for words'|@translate}</span>
        </label>
        <label>
          <input data-wid='tag' class="filter-manager-controller tags" type="checkbox"/>
          <span class="mcs-icon pwg-icon-tag">{'Search tags'|@translate}</span>
        </label>
        <label>
          <input data-wid='album' class="filter-manager-controller album" type="checkbox"/>
          <span class="mcs-icon pwg-icon-album">{'Search in albums'|@translate}</span>
        </label>
        <label>
          <input data-wid='authors' class="filter-manager-controller author" type="checkbox"/>
          <span class="mcs-icon pwg-icon-user-edit">{'Search for Author'|@translate}</span>
        </label>
        <label>
          <input data-wid='added_by' class="filter-manager-controller added_by" type="checkbox"/>
          <span class="mcs-icon pwg-icon-user">Added by</span>
        </label>
      </div>

      <div class="filter-manager-actions">
        <div class="filter-cancel">
          Cancel
        </div>
        <div class="filter-validate">
          <i class="loading pwg-icon-spin6 animate-spin"></i>
          <span class="validate-text">Validate</span>
        </div>
      </div>
    </div>
  </div>
  <div class="filter-manager">
    <span class="mcs-icon pwg-icon-cog"></span>Filters
  </div>
  <i class="filter-spinner pwg-icon-spin6 animate-spin"></i>

  <div class="filter filter-word">
   <span class="mcs-icon pwg-icon-search filter-icon"></span>
   <span class="mcs-icon pwg-icon-cancel remove-filter"></span>
   <span class="search-words"></span>
   <span class="filter-arrow pwg-icon-up-open"></span>

   <div class="filter-form filter-word-form">
    <div class="filter-form-title">{'Search for words'|@translate}</div>
    {* <span class="word-help"><i class="pwg-icon-help-circled"></i>Conseils de recherche</span> *}
    <div class="word-search-options">
      <label><input type="radio" name="mode" value="AND" checked> {'Search for all terms'|@translate}</label>
      <label><input type="radio" name="mode" value="OR"> {'Search for any term'|@translate}</label>
    </div>

    <input type="text" id="word-search" name="word">
    <span class="search-params-title">Search in :</span>
    <div class="search-params"> 
      <div>
        <input type="checkbox" id="cat-title" name="cat-title">
        <label for="cat-title">Album title</label>
      </div>
      <div>
        <input type="checkbox" id="tags" name="tags">
        <label for="tags">Tags</label>
      </div>
      <div>
        <input type="checkbox" id="file" name="file">
        <label for="file">File name</label>
      </div>
      <div>
        <input type="checkbox" id="name" name="name">
        <label for="name">Photo title</label>
      </div>
      <div>
        <input type="checkbox" id="comment" name="comment">
        <label for="comment">Photo description</label>
      </div>
      <div>
        <input type="checkbox" id="cat-desc" name="cat-desc">
        <label for="cat-desc">Album description</label>
      </div>
    </div>
    <div class="filter-validate">
      <i class="loading pwg-icon-spin6 animate-spin"></i>
      <span class="validate-text">Validate</span>
    </div>
   </div>
  </div>
  <div class="filter filter-tag">
    <span class="mcs-icon pwg-icon-tag filter-icon"></span>
    <span class="mcs-icon pwg-icon-cancel remove-filter"></span>
    <span class="search-words"></span>
    <span class="filter-arrow pwg-icon-up-open"></span>

    <div class="filter-form filter-tag-form">
      <div class="filter-form-title">{'Search tags'|@translate}</div>
      <div class="search-params"> 
        <div>
          <input type="radio" id="tag-all" name="tag_mode" value="AND" checked>
          <label for="tag-all">{'All tags'|@translate}</label>
        </div>
        <div>
          <input type="radio" id="tag-one" name="tag_mode" value="OR">
          <label for="tag-one">{'Any tag'|@translate}</label>
        </div>
      </div>
      <select id="tag-search" placeholder="{'Type in a search term'|translate}" name="tags[]" multiple>
    {foreach from=$TAGS item=tag}
        <option value="{$tag.id}">{$tag.name} ({$tag.counter|translate_dec:'%d photo':'%d photos'})</option>
    {/foreach}
      </select>
      <div class="filter-validate">
        <i class="loading pwg-icon-spin6 animate-spin"></i>
        <span class="validate-text">Validate</span>
      </div>
    </div>
  </div>
  {* <div class="filter filter-date">
    <span class="mcs-icon pwg-icon-calendar"></span>Date: <span class="search-words">Balloon</span><span class="filter-arrow pwg-icon-up-open"></span>

    <div class="filter-form filter-date-form">
    // Still in porgress
      <div class="row">
        <div class="col-sm-12" id="htmlTarget">
          <label for="datetimepicker1Input" class="form-label">Picker</label>
          <div class="input-group log-event" id="datetimepicker1" data-td-target-input="nearest" data-td-target-toggle="nearest">
            <input id="datetimepicker1Input" type="text" class="form-control" data-td-target="#datetimepicker1">
            <span class="input-group-text" data-td-target="#datetimepicker1" data-td-toggle="datetimepicker">
              <i class="fas fa-calendar">blavvla</i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div> *}
  <div class="filter filter-album">
    <span class="mcs-icon pwg-icon-album filter-icon"></span>
    <span class="mcs-icon pwg-icon-cancel remove-filter"></span>
    <span class="search-words"></span>
    <span class="filter-arrow pwg-icon-up-open"></span>

    <div class="filter-form filter-album-form">
      <div class="filter-form-title">{'Search in albums'|@translate}</div>
      <div class="search-params"> 
        {* <div>
          <input type="radio" id="album-all" name="album_mode" value="ALL" checked>
          <label for="album-all">{'All albums'|@translate}</label>
        </div>
        <div>
          <input type="radio" id="album-any" name="album_mode" value="ANY">
          <label for="album-any">{'Any album'|@translate}</label>
        </div> *}
        </div>
        <div class="selected-categories-container">
        </div>
        <div class="add-album-button">
          <label class="head-button-2 icon-add-album">
            <p class="mcs-icon pwg-icon-plus-circled">{'Add Album'|@translate}</p>
          </label>
        </div>
        <div class="search-sub-cats">
          <input type="checkbox" id="search-sub-cats" name="search-sub-cats">
          <label for="search-sub-cats">Search in sub-albums</label>
        </div>
        <div class="filter-validate">
          <i class="loading pwg-icon-spin6 animate-spin"></i>
          <span class="validate-text">Validate</span>
        </div>
    </div>
  </div>
  {include file='admin/themes/default/template/include/album_selector.inc.tpl' 
    title={'Search in album'|@translate}
    searchPlaceholder={'Search'|@translate}
    show_root_btn=false
    api_method='pwg.categories.getList'
  }
  <div class="filter filter-author">
    <span class="mcs-icon pwg-icon-user-edit filter-icon"></span>
    <span class="mcs-icon pwg-icon-cancel remove-filter"></span>
    <span class="search-words"></span>
    <span class="filter-arrow pwg-icon-up-open"></span>
    
    <div class="filter-form filter-author-form">
      <div class="filter-form-title">{'Search for Author'|@translate}</div>
      <select id="authors" placeholder="{'Type in a search term'|translate}" name="authors[]" multiple>
      {foreach from=$AUTHORS item=author}
        <option value="{$author.author|strip_tags:false|escape:html}">{$author.author|strip_tags:false} ({$author.counter|translate_dec:'%d photo':'%d photos'})</option>
      {/foreach}
      </select>

      <div class="filter-validate">
        <i class="loading pwg-icon-spin6 animate-spin"></i>
        <span class="validate-text">Validate</span>
      </div>
    </div>
  </div>

  <div class="filter filter-added_by">
    <span class="mcs-icon pwg-icon-user filter-icon"></span>
    <span class="mcs-icon pwg-icon-cancel remove-filter"></span>
    </span><span class="search-words"></span>
    <span class="filter-arrow pwg-icon-up-open"></span>

    <div class="filter-form filter-added_by-form">
      <div class="filter-form-title">Added by</div>
      <select id="added_by" placeholder="{'Type in a search term'|translate}" name="added_by[]" multiple>
      {foreach from=$ADDED_BY item=added_by}
        <option value="{$added_by.added_by_id|strip_tags:false|escape:html}">{$added_by.added_by_name|strip_tags:false}<span class="badge">({$added_by.counter|translate_dec:'%d photo':'%d photos'})</span></option>
      {/foreach}
      </select>
      <div class="filter-validate">
        <i class="loading pwg-icon-spin6 animate-spin"></i>
        <span class="validate-text">Validate</span>
      </div>
    </div>
  </div>
  {* <div class="filter filter-note">
   Note div
   <div class="filter-form filter-note-form">

   </div>
  </div>
  <div class="filter filter-height">
    Height div
    <div class="filter-form filter-height-form">

    </div>
  </div>
  <div class="filter filter-width">
    Width div
    <div class="filter-form filter-width-form">

    </div>
  </div>
  <div class="filter filter-file-type">
    File type div
    <div class="filter-form filter-file-type-form">

    </div>
  </div>
  <div class="filter filter-file-size">
    File size div
    <div class="filter-form filter-file-size-form">

    </div>
  </div> *}
</div>
{/if}

{if !empty($THUMBNAILS)}
<div class="loader"><img src="{$ROOT_URL}{$themeconf.img_dir}/ajax_loader.gif"></div>

<ul class="thumbnails" id="thumbnails">
  {$THUMBNAILS}
</ul>

{else if !empty($SEARCH_ID)}
<div class="mcs-no-result">
  <div class="text">
    <span>No results are available.</span>
    <span>You can try to edit your filters and perform a new search.</span>
  </div>
</div>
{/if}
{if !empty($thumb_navbar)}
	{include file='navigation_bar.tpl'|@get_extent:'navbar' navbar=$thumb_navbar}
{/if}

{if !empty($PLUGIN_INDEX_CONTENT_END)}{$PLUGIN_INDEX_CONTENT_END}{/if}
</div>{* <!-- content --> *}
{if !empty($PLUGIN_INDEX_CONTENT_AFTER)}{$PLUGIN_INDEX_CONTENT_AFTER}{/if}
