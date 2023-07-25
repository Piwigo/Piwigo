{combine_script id='core.switchbox' load='async' require='jquery' path='themes/default/js/switchbox.js'}
{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css path="admin/themes/default/fontello/css/animation.css" order=10} {* order 10 is required, see issue 1080 *}
{combine_script id='jquery.tipTip' load='header' path='themes/default/js/plugins/jquery.tipTip.minified.js'}

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

str_word_widget_label = "{'Search for words'|@translate|escape:javascript}";
str_tags_widget_label = "{'Tag'|@translate|escape:javascript}";
str_album_widget_label = "{'Album'|@translate|escape:javascript}";
str_author_widget_label = "{'Author'|@translate|escape:javascript}";
str_added_by_widget_label = "{'Added by'|@translate|escape:javascript}";
str_filetypes_widget_label = "{'File type'|@translate|escape:javascript}";

str_date_post_7d = "{'last 7 days'|@translate|escape:javascript}";
str_date_post_30d = "{'last 30 days'|@translate|escape:javascript}";
str_date_post_6m = "{'last 6 months'|@translate|escape:javascript}";
str_date_post_1y = "{'last year'|@translate|escape:javascript}";
str_date_post_u = "{'Unknown time period'|@translate|escape:javascript}";

str_empty_search_top_alt = "{'Fill in the filters to start a search'|@translate|escape:javascript}";
str_empty_search_bot_alt = "{'Pre-established filters are proposed, but you can add or remove them using the "Choose filters" button.'|@translate|escape:javascript}";

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

      <div class="mcs-popin-title">{'Choose filters'|@translate}</div>

      <div class="filter-manager-controller-container">
        <label>
          <input data-wid='word' class="filter-manager-controller word" type="checkbox"/>
          <span class="mcs-icon pwg-icon-search">{'Search for words'|@translate}</span>
        </label>
        <label>
          <input data-wid='tag' class="filter-manager-controller tags" type="checkbox"/>
          <span class="mcs-icon pwg-icon-tag">{'Tag'|@translate}</span>
        </label>
        <label>
          <input data-wid='date_posted' class="filter-manager-controller date_post" type="checkbox"/>
          <span class="mcs-icon pwg-icon-calendar-plus">{'Post date'|@translate}</span>
        </label>
        <label>
          <input data-wid='album' class="filter-manager-controller album" type="checkbox"/>
          <span class="mcs-icon pwg-icon-album">{'Album'|@translate}</span>
        </label>
        <label>
          <input data-wid='authors' class="filter-manager-controller author" type="checkbox"/>
          <span class="mcs-icon pwg-icon-user-edit">{'Author'|@translate}</span>
        </label>
        <label>
          <input data-wid='added_by' class="filter-manager-controller added_by" type="checkbox"/>
          <span class="mcs-icon pwg-icon-user">{'Added by'|@translate}</span>
        </label>
        <label>
          <input data-wid='filetypes' class="filter-manager-controller filetypes" type="checkbox"/>
          <span class="mcs-icon pwg-icon-file-image">{'File type'|@translate}</span>
        </label>
      </div>

      <div class="filter-manager-actions">
        <div class="filter-cancel">
          {'Cancel'|@translate}
        </div>
        <div class="filter-validate">
          <i class="loading pwg-icon-spin6 animate-spin"></i>
          <span class="validate-text">{'Validate'|@translate}</span>
        </div>
      </div>
    </div>
  </div>
  <div class="filter-manager">
    <span class="mcs-icon pwg-icon-selecters"></span>{'Choose filters'|@translate}
  </div>
  <i class="filter-spinner pwg-icon-spin6 animate-spin"></i>

  <div class="filter filter-word">
   <span class="mcs-icon pwg-icon-search filter-icon"></span>
   <span class="search-words"></span>
   <span class="filter-arrow pwg-icon-up-open"></span>

   <div class="filter-form filter-word-form">
    <div class="filter-form-title">{'Search for words'|@translate}</div>
    <div class="filter-actions"> 
      <span class="delete mcs-icon pwg-icon-trash">{'Delete'|@translate}</span>
      <span class="clear mcs-icon pwg-icon-broom">{'Clear'|@translate}</span>
    </div>
    {* <span class="word-help"><i class="pwg-icon-help-circled"></i>Conseils de recherche</span> *}
    <div class="word-search-options">
      <label><input type="radio" name="mode" value="AND" checked> {'Search for all terms'|@translate}</label>
      <label><input type="radio" name="mode" value="OR"> {'Search for any term'|@translate}</label>
    </div>

    <input type="text" id="word-search" name="word">
    <span class="search-params-title">{'Search in :'|@translate}</span>
    <div class="search-params"> 
      <div>
        <input type="checkbox" id="name" name="name">
        <label for="name">{'Photo title'|@translate}</label>
      </div>
      <div>
        <input type="checkbox" id="file" name="file">
        <label for="file">{'File name'|@translate}</label>
      </div>
      <div>
        <input type="checkbox" id="comment" name="comment">
        <label for="comment">{'Photo description'|@translate}</label>
      </div>
      <div>
        <input type="checkbox" id="tags" name="tags">
        <label for="tags">{'Tags'|@translate}</label>
      </div>
      <div>
        <input type="checkbox" id="cat-title" name="cat-title">
        <label for="cat-title">{'Album title'|@translate}</label>
      </div>
      <div>
        <input type="checkbox" id="cat-desc" name="cat-desc">
        <label for="cat-desc">{'Album description'|@translate}</label>
      </div>
    </div>
    <div class="filter-validate">
      <i class="loading pwg-icon-spin6 animate-spin"></i>
      <span class="validate-text">{'Validate'|@translate}</span>
    </div>
   </div>
  </div>
  <div class="filter filter-tag">
    <span class="mcs-icon pwg-icon-tag filter-icon"></span>
    <span class="search-words"></span>
    <span class="filter-arrow pwg-icon-up-open"></span>

    <div class="filter-form filter-tag-form">
      <div class="filter-form-title">{'Tag'|@translate}</div>
      <div class="filter-actions"> 
        <span class="delete mcs-icon pwg-icon-trash">{'Delete'|@translate}</span>
        <span class="clear mcs-icon pwg-icon-broom">{'Clear'|@translate}</span>
      </div>
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
      <div class="form-container">
        <select id="tag-search" placeholder="{'Type in a search term'|translate}" name="tags[]" multiple>
      {foreach from=$TAGS item=tag}
          <option value="{$tag.id}">{$tag.name} ({$tag.counter|translate_dec:'%d photo':'%d photos'})</option>
      {/foreach}
        </select>
        <div class="filter-validate">
          <i class="loading pwg-icon-spin6 animate-spin"></i>
          <span class="validate-text">{'Validate'|@translate}</span>
        </div>
      </div>
    </div>
  </div>
  <div class="filter filter-date_post">
    <span class="mcs-icon pwg-icon-calendar-plus filter-icon"></span>
    <span class="search-words">{'Post date'|@translate}</span>
    <span class="filter-arrow pwg-icon-up-open"></span>

    <div class="filter-form filter-date_post-form">
      <div class="filter-form-title">{'Post date'|@translate}</div>
      <div class="filter-actions"> 
        <span class="delete mcs-icon pwg-icon-trash" title="{'Delete'|@translate}"></span>
        <span class="clear mcs-icon pwg-icon-broom" title="{'Clear'|@translate}"></span>
      </div>

      <div class="date_post-option-container">
        <div class="date_post-option">
          <input type="radio" id="date_post-7d" value="7d" name="date_post-period">
          <label for="date_post-7d">
            <span class="mcs-icon pwg-icon-checkmark checked-icon"></span>
            <span class="date-period">{'last 7 days'|@translate}</span>
          </label>
        </div>
        <div class="date_post-option">
          <input type="radio" id="date_post-30d" value="30d" name="date_post-period">
          <label for="date_post-30d">
            <span class="mcs-icon pwg-icon-checkmark checked-icon"></span>
            <span class="date-period">{'last 30 days'|@translate}</span>
          </label>
        </div>
        <div class="date_post-option">
          <input type="radio" id="date_post-6m" value="6m" name="date_post-period">
          <label for="date_post-6m">
            <span class="mcs-icon pwg-icon-checkmark checked-icon"></span>
            <span class="date-period">{'last 6 months'|@translate}</span>
          </label>
        </div>
        <div class="date_post-option">
          <input type="radio" id="date_post-1y" value="1y" name="date_post-period">
          <label for="date_post-1y">
            <span class="mcs-icon pwg-icon-checkmark checked-icon"></span>
            <span class="date-period">{'last year'|@translate}</span>
          </label>
        </div>
      </div>
      <div class="filter-validate">
        <i class="loading pwg-icon-spin6 animate-spin"></i>
        <span class="validate-text">{'Validate'|@translate}</span>
      </div>
    </div>
  </div>
  <div class="filter filter-album">
    <span class="mcs-icon pwg-icon-album filter-icon"></span>
    <span class="search-words"></span>
    <span class="filter-arrow pwg-icon-up-open"></span>

    <div class="filter-form filter-album-form">
    <div class="filter-form-title">{'Album'|@translate}</div>
    <div class="filter-actions"> 
      <span class="delete mcs-icon pwg-icon-trash">{'Delete'|@translate}</span>
      <span class="clear mcs-icon pwg-icon-broom">{'Clear'|@translate}</span>
    </div>
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
          <span class="validate-text">{'Validate'|@translate}</span>
        </div>
    </div>
  </div>
  {include file='admin/themes/default/template/include/album_selector.inc.tpl' 
    title={'Search in albums'|@translate}
    searchPlaceholder={'Search'|@translate}
    show_root_btn=false
    api_method='pwg.categories.getList'
  }
{if isset($AUTHORS)}
  <div class="filter filter-authors">
    <span class="mcs-icon pwg-icon-user-edit filter-icon"></span>
    <span class="search-words"></span>
    <span class="filter-arrow pwg-icon-up-open"></span>
    
    <div class="filter-form filter-author-form">
      <div class="filter-form-title">{'Author'|@translate}</div>
      <div class="filter-actions"> 
        <span class="delete mcs-icon pwg-icon-trash">{'Delete'|@translate}</span>
        <span class="clear mcs-icon pwg-icon-broom">{'Clear'|@translate}</span>
      </div>
      <div class="form-container">
        <select id="authors" placeholder="{'Type in a search term'|translate}" name="authors[]" multiple>
        {foreach from=$AUTHORS item=author}
          <option value="{$author.author|strip_tags:false|escape:html}">{$author.author|strip_tags:false} ({$author.counter|translate_dec:'%d photo':'%d photos'})</option>
        {/foreach}
        </select>

        <div class="filter-validate">
          <i class="loading pwg-icon-spin6 animate-spin"></i>
          <span class="validate-text">{'Validate'|@translate}</span>
        </div>
      </div>
    </div>
  </div>
{/if}

{if isset($ADDED_BY)}
  <div class="filter filter-added_by">
    <span class="mcs-icon pwg-icon-user filter-icon"></span>
    </span><span class="search-words"></span>
    <span class="filter-arrow pwg-icon-up-open"></span>

    <div class="filter-form filter-added_by-form">
      <div class="filter-form-title">{'Added by'|@translate}</div>
      <div class="filter-actions"> 
        <span class="delete mcs-icon pwg-icon-trash">{'Delete'|@translate}</span>
        <span class="clear mcs-icon pwg-icon-broom">{'Clear'|@translate}</span>
      </div>
      <div class="form-container">
        <select id="added_by" placeholder="{'Type in a search term'|translate}" name="added_by[]" multiple>
        {foreach from=$ADDED_BY item=added_by}
          <option value="{$added_by.added_by_id|strip_tags:false|escape:html}">{$added_by.added_by_name|strip_tags:false}<span class="badge">({$added_by.counter|translate_dec:'%d photo':'%d photos'})</span></option>
        {/foreach}
        </select>
        <div class="filter-validate">
          <i class="loading pwg-icon-spin6 animate-spin"></i>
          <span class="validate-text">{'Validate'|@translate}</span>
        </div>
      </div>
    </div>
  </div>
{/if}

{if isset($FILETYPES)}
  <div class="filter filter-filetypes">
    <span class="mcs-icon pwg-icon-file-image filter-icon"></span>
    </span><span class="search-words"></span>
    <span class="filter-arrow pwg-icon-up-open"></span>

    <div class="filter-form filter-filetypes-form">
      <div class="filter-form-title">{'File type'|@translate}</div>
      <div class="filter-actions"> 
        <span class="delete mcs-icon pwg-icon-trash tiptip" title="{'Delete'|@translate}"></span>
        <span class="clear mcs-icon pwg-icon-broom tiptip" title="{'Clear'|@translate}"></span>
      </div>
      <div class="form-container">
        <div class="filetypes-option-container">
        {foreach from=$FILETYPES item=filetypes key=k}
          <div class="filetypes-option">
              <input type="checkbox" id="filetype-{$k}" name="{$k}">
              <label for="filetype-{$k}">
                <span class="mcs-icon pwg-icon-checkmark checked-icon"></span>
                <span class="ext-name">{$k}</span>
                <span class="ext-badge">{$filetypes}</span>
              </label>
            </div>
        {/foreach}
        </div>
      </div>
      <div class="filter-validate">
        <i class="loading pwg-icon-spin6 animate-spin"></i>
        <span class="validate-text">{'Validate'|@translate}</span>
      </div>
    </div>
  </div>
{/if}

<div>
  <span class="mcs-icon pwg-icon-broom clear-all">{'Empty filters'|@translate}</span>
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
    <span class="top">{'No results are available.'|@translate}</span>
    <span class="bot">{'You can try to edit your filters and perform a new search.'|translate}</span>
  </div>
</div>
{/if}
{if !empty($thumb_navbar)}
	{include file='navigation_bar.tpl'|@get_extent:'navbar' navbar=$thumb_navbar}
{/if}

{if !empty($PLUGIN_INDEX_CONTENT_END)}{$PLUGIN_INDEX_CONTENT_END}{/if}
</div>{* <!-- content --> *}
{if !empty($PLUGIN_INDEX_CONTENT_AFTER)}{$PLUGIN_INDEX_CONTENT_AFTER}{/if}
