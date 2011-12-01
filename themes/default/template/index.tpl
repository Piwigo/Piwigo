{$MENUBAR}
{if !empty($PLUGIN_INDEX_CONTENT_BEFORE)}{$PLUGIN_INDEX_CONTENT_BEFORE}{/if}
<div id="content" class="content">
<div class="titrePage">
	<ul class="categoryActions">
{if !empty($image_orders)}
		<li>{'Sort order'|@translate}: {strip}
		<select onchange="document.location = this.options[this.selectedIndex].value;">
			{foreach from=$image_orders item=image_order}
			<option value="{$image_order.URL}"{if $image_order.SELECTED} selected="selected"{/if}>{$image_order.DISPLAY}</option>
			{/foreach}
		</select>
{/strip}</li>
{/if}
{if isset($favorite)}
		<li><a href="{$favorite.U_FAVORITE}" title="{'delete all photos from your favorites'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-favorite-del">&nbsp;</span><span class="pwg-button-text">{'delete all photos from your favorites'|@translate}</span>
		</a></li>
{/if}
{if isset($U_CADDIE)}
		<li><a href="{$U_CADDIE}" title="{'Add to caddie'|@translate}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-caddie-add">&nbsp;</span><span class="pwg-button-text">{'Caddie'|@translate}</span>
		</a></li>
{/if}
{if isset($U_EDIT)}
		<li><a href="{$U_EDIT}" title="{'Edit album'|@translate}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-category-edit">&nbsp;</span><span class="pwg-button-text">{'Edit'|@translate}</span>
		</a></li>
{/if}
{if isset($U_SEARCH_RULES)}
		{combine_script id='core.scripts' load='async' path='themes/default/js/scripts.js'}
		<li><a href="{$U_SEARCH_RULES}" onclick="popuphelp(this.href); return false;" title="{'Search rules'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-help">&nbsp;</span><span class="pwg-button-text">(?)</span>
		</a></li>
{/if}
{if isset($U_SLIDESHOW)}
		<li>{strip}<a href="{$U_SLIDESHOW}" title="{'slideshow'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-slideshow">&nbsp;</span><span class="pwg-button-text">{'slideshow'|@translate}</span>
		</a>{/strip}</li>
{/if}
{if isset($U_MODE_FLAT)}
		<li>{strip}<a href="{$U_MODE_FLAT}" title="{'display all photos in all sub-albums'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-category-view-flat">&nbsp;</span><span class="pwg-button-text">{'display all photos in all sub-albums'|@translate}</span>
		</a>{/strip}</li>
{/if}
{if isset($U_MODE_NORMAL)}
		<li>{strip}<a href="{$U_MODE_NORMAL}" title="{'return to normal view mode'|@translate}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-category-view-normal">&nbsp;</span><span class="pwg-button-text">{'return to normal view mode'|@translate}</span>
		</a>{/strip}</li>
{/if}
{if isset($U_MODE_POSTED)}
		<li>{strip}<a href="{$U_MODE_POSTED}" title="{'display a calendar by posted date'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-calendar">&nbsp;</span><span class="pwg-button-text">{'Calendar'|@translate}</span>
		</a>{/strip}</li>
{/if}
{if isset($U_MODE_CREATED)}
		<li>{strip}<a href="{$U_MODE_CREATED}" title="{'display a calendar by creation date'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-camera-calendar">&nbsp;</span><span class="pwg-button-text">{'Calendar'|@translate}</span>
		</a>{/strip}</li>
{/if}
{if !empty($PLUGIN_INDEX_ACTIONS)}{$PLUGIN_INDEX_ACTIONS}{/if}
	</ul>

<h2>{$TITLE}</h2>

{if isset($chronology_views)}
<div class="calendarViews">{'View'|@translate}:
	<select onchange="document.location = this.options[this.selectedIndex].value;">
		{foreach from=$chronology_views item=view}
		<option value="{$view.VALUE}"{if $view.SELECTED} selected="selected"{/if}>{$view.CONTENT}</option>
		{/foreach}
	</select>
</div>
{/if}

{if isset($chronology.TITLE)}
<h2>{$chronology.TITLE}</h2>
{/if}

</div>{* <!-- titrePage --> *}

{if !empty($PLUGIN_INDEX_CONTENT_BEGIN)}{$PLUGIN_INDEX_CONTENT_BEGIN}{/if}

{if !empty($category_search_results)}
<div style="font-size:16px;margin:10px 16px">{'Album results for'|@translate} <strong>{$QUERY_SEARCH}</strong> :
	<em><strong>
	{foreach from=$category_search_results item=res name=res_loop}
	{if !$smarty.foreach.res_loop.first} &mdash; {/if}
	{$res}
	{/foreach}
	</strong></em>
</div>
{/if}

{if !empty($tag_search_results)}
<div style="font-size:16px;margin:10px 16px">{'Tag results for'|@translate} <strong>{$QUERY_SEARCH}</strong> :
	<em><strong>
	{foreach from=$tag_search_results item=res name=res_loop}
	{if !$smarty.foreach.res_loop.first} &mdash; {/if}
	{$res}
	{/foreach}
	</strong></em>
</div>
{/if}

{if isset($FILE_CHRONOLOGY_VIEW)}
{include file=$FILE_CHRONOLOGY_VIEW}
{/if}

{if !empty($CONTENT_DESCRIPTION)}
<div class="additional_info">
	{$CONTENT_DESCRIPTION}
</div>
{/if}

{if !empty($CATEGORIES)}{$CATEGORIES}{/if}
{if !empty($THUMBNAILS)}
<ul class="thumbnails" id="thumbnails">
{$THUMBNAILS}
</ul>
{/if}

{if !empty($navbar)}{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}

{if !empty($PLUGIN_INDEX_CONTENT_END)}{$PLUGIN_INDEX_CONTENT_END}{/if}
</div>{* <!-- content --> *}
{if !empty($PLUGIN_INDEX_CONTENT_AFTER)}{$PLUGIN_INDEX_CONTENT_AFTER}{/if}
