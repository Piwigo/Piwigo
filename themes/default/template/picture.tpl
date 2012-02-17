{* Example of resizeable
{include file='include/autosize.inc.tpl'}
*}
{if isset($MENUBAR)}
{$MENUBAR}
<div id="content" class="contentWithMenu">
{/if}
{if isset($errors) or not empty($infos)}
{include file='infos_errors.tpl'}
{/if}
{if !empty($PLUGIN_PICTURE_BEFORE)}{$PLUGIN_PICTURE_BEFORE}{/if}

<div id="imageHeaderBar">
	<div class="browsePath">
		{$SECTION_TITLE} {$LEVEL_SEPARATOR} <h2>{$current.TITLE}</h2>
	</div>
	<div class="imageNumber">{$PHOTO}</div>
</div>

<div id="imageToolBar">
<div class="actionButtons">
{strip}{if isset($U_SLIDESHOW_START)}
	<a href="{$U_SLIDESHOW_START}" title="{'slideshow'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
		<span class="pwg-icon pwg-icon-slideshow"> </span><span class="pwg-button-text">{'slideshow'|@translate}</span>
	</a>
{/if}{/strip}
{strip}{if isset($U_METADATA)}
	<a href="{$U_METADATA}" title="{'Show file metadata'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
		<span class="pwg-icon pwg-icon-camera-info"> </span><span class="pwg-button-text">{'Show file metadata'|@translate}</span>
	</a>
{/if}{/strip}
{strip}{if isset($current.U_DOWNLOAD)}
	<a href="{$current.U_DOWNLOAD}" title="{'Download this file'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
		<span class="pwg-icon pwg-icon-save"> </span><span class="pwg-button-text">{'Download'|@translate}</span>
	</a>
{/if}{/strip}
{if isset($PLUGIN_PICTURE_ACTIONS)}{$PLUGIN_PICTURE_ACTIONS}{/if}
{strip}{if isset($favorite)}
	<a href="{$favorite.U_FAVORITE}" title="{if $favorite.IS_FAVORITE}{'delete this photo from your favorites'|@translate}{else}{'add this photo to your favorites'|@translate}{/if}" class="pwg-state-default pwg-button" rel="nofollow">
		<span class="pwg-icon pwg-icon-favorite-{if $favorite.IS_FAVORITE}del{else}add{/if}"> </span><span class="pwg-button-text">{'Favorites'|@translate}</span>
	</a>
{/if}{/strip}
{strip}{if isset($U_SET_AS_REPRESENTATIVE)}
	<a href="{$U_SET_AS_REPRESENTATIVE}" title="{'set as album representative'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
		<span class="pwg-icon pwg-icon-representative"> </span><span class="pwg-button-text">{'representative'|@translate}</span>
	</a>
{/if}{/strip}
{strip}{if isset($U_ADMIN)}
	<a href="{$U_ADMIN}" title="{'Modify information'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
		<span class="pwg-icon pwg-icon-edit"> </span><span class="pwg-button-text">{'Edit'|@translate}</span>
	</a>
{/if}{/strip}
{strip}{if isset($U_CADDIE)}{*caddie management BEGIN*}
{footer_script}
{literal}function addToCadie(aElement, rootUrl, id)
{
if (aElement.disabled) return;
aElement.disabled=true;
var y = new PwgWS(rootUrl);
y.callService(
	"pwg.caddie.add", {image_id: id} ,
	{
		onFailure: function(num, text) { alert(num + " " + text); document.location=aElement.href; },
		onSuccess: function(result) { aElement.disabled = false; }
	}
	);
}{/literal}
{/footer_script}
	<a href="{$U_CADDIE}" onclick="addToCadie(this, '{$ROOT_URL}', {$current.id}); return false;" title="{'Add to caddie'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
		<span class="pwg-icon pwg-icon-caddie-add"> </span><span class="pwg-button-text">{'Caddie'|@translate}</span>
	</a>
{/if}{/strip}{*caddie management END*}
</div>

	{include file='picture_nav_buttons.tpl'|@get_extent:'picture_nav_buttons'}
</div>{*<!-- imageToolBar -->*}

<div id="theImage">
{$ELEMENT_CONTENT}

{if isset($COMMENT_IMG)}
<p class="imageComment">{$COMMENT_IMG}</p>
{/if}

{if isset($U_SLIDESHOW_STOP)}
<p>
	[ <a href="{$U_SLIDESHOW_STOP}">{'stop the slideshow'|@translate}</a> ]
</p>
{/if}

</div>

<div id="imageInfos">
{if $DISPLAY_NAV_THUMB}
{if isset($previous)}
<a class="navThumb" id="linkPrev" href="{$previous.U_IMG}" title="{'Previous'|@translate} : {$previous.TITLE}" rel="prev">
	<img src="{$previous.THUMB_SRC}" alt="{$previous.TITLE}">
</a>
{/if}
{if isset($next)}
<a class="navThumb" id="linkNext" href="{$next.U_IMG}" title="{'Next'|@translate} : {$next.TITLE}" rel="next">
	<img src="{$next.THUMB_SRC}" alt="{$next.TITLE}">
</a>
{/if}
{/if}

<table id="standard" class="infoTable">
{strip}
	{if $display_info.author}
	<tr id="Author">
		<td class="label">{'Author'|@translate}</td>
		<td class="value">{if isset($INFO_AUTHOR)}{$INFO_AUTHOR}{else}{'N/A'|@translate}{/if}</td>
	</tr>
	{/if}
	{if $display_info.created_on}
	<tr id="datecreate">
		<td class="label">{'Created on'|@translate}</td>
		<td class="value">{if isset($INFO_CREATION_DATE)}{$INFO_CREATION_DATE}{else}{'N/A'|@translate}{/if}</td>
	</tr>
	{/if}
	{if $display_info.posted_on}
	<tr id="datepost">
		<td class="label">{'Posted on'|@translate}</td>
		<td class="value">{$INFO_POSTED_DATE}</td>
	</tr>
	{/if}
	{if $display_info.dimensions}
	<tr id="Dimensions">
		<td class="label">{'Dimensions'|@translate}</td>
		<td class="value">{if isset($INFO_DIMENSIONS)}{$INFO_DIMENSIONS}{else}{'N/A'|@translate}{/if}</td>
	</tr>
	{/if}
	{if $display_info.file}
	<tr id="File">
		<td class="label">{'File'|@translate}</td>
		<td class="value">{$INFO_FILE}</td>
	</tr>
	{/if}
	{if $display_info.filesize}
	<tr id="Filesize">
		<td class="label">{'Filesize'|@translate}</td>
		<td class="value">{if isset($INFO_FILESIZE)}{$INFO_FILESIZE}{else}{'N/A'|@translate}{/if}</td>
	</tr>
	{/if}
	{if $display_info.tags}
	<tr id="Tags">
		<td class="label">{'Tags'|@translate}</td>
		<td class="value">
			{if isset($related_tags)}
				{foreach from=$related_tags item=tag name=tag_loop}{if !$smarty.foreach.tag_loop.first}, {/if}<a href="{$tag.URL}">{$tag.name}</a>{/foreach}
			{/if}
		</td>
	</tr>
	{/if}
	{if $display_info.categories}
	<tr id="Categories">
		<td class="label">{'Albums'|@translate}</td>
		<td class="value">
			{if isset($related_categories)}
			<ul>
				{foreach from=$related_categories item=cat}
				<li>{$cat}</li>
				{/foreach}
			</ul>
			{/if}
		</td>
	</tr>
	{/if}
	{if $display_info.visits}
	<tr id="Visits">
		<td class="label">{'Visits'|@translate}</td>
		<td class="value">{$INFO_VISITS}</td>
	</tr>
	{/if}

{if $display_info.rating_score and isset($rate_summary)}
	<tr id="Average">
		<td class="label">{'Rating score'|@translate}</td>
		<td class="value">
		{if $rate_summary.count}
			<span id="ratingScore">{$rate_summary.score}</span> <span id="ratingCount">({assign var='rate_text' value='%d rates'|@translate}{$pwg->sprintf($rate_text, $rate_summary.count)})</span>
		{else}
			<span id="ratingScore">{'no rate'|@translate}</span> <span id="ratingCount"></span>
		{/if}
		</td>
	</tr>
{/if}

{if isset($rating)}
	<tr id="rating">
		<td class="label">
			<span id="updateRate">{if isset($rating.USER_RATE)}{'Update your rating'|@translate}{else}{'Rate this photo'|@translate}{/if}</span>
		</td>
		<td class="value">
			<form action="{$rating.F_ACTION}" method="post" id="rateForm" style="margin:0;">
			<div>
			{foreach from=$rating.marks item=mark name=rate_loop}
			{if isset($rating.USER_RATE) && $mark==$rating.USER_RATE}
				<input type="button" name="rate" value="{$mark}" class="rateButtonSelected" title="{$mark}">
			{else}
				<input type="submit" name="rate" value="{$mark}" class="rateButton" title="{$mark}">
			{/if}
			{/foreach}
			{strip}{combine_script id='core.scripts' load='async' path='themes/default/js/scripts.js'}
			{combine_script id='rating' load='async' require='core.scripts' path='themes/default/js/rating.js'}
			{footer_script}
				var _pwgRatingAutoQueue = _pwgRatingAutoQueue||[];
				_pwgRatingAutoQueue.push( {ldelim}rootUrl: '{$ROOT_URL}', image_id: {$current.id},
					onSuccess : function(rating) {ldelim}
						var e = document.getElementById("updateRate");
						if (e) e.innerHTML = "{'Update your rating'|@translate|@escape:'javascript'}";
						e = document.getElementById("ratingScore");
						if (e) e.innerHTML = rating.score;
						e = document.getElementById("ratingCount");
						if (e) e.innerHTML = "({'%d rates'|@translate|@escape:'javascript'})".replace( "%d", rating.count);
					{rdelim}{rdelim} );
			{/footer_script}
			{/strip}
			</div>
			</form>
		</td>
	</tr>
{/if}

{if $display_info.privacy_level and isset($available_permission_levels)}
	<tr id="Privacy">
		<td class="label">{'Who can see this photo?'|@translate}</td>
		<td class="value">
{combine_script id='core.scripts' load='async' path='themes/default/js/scripts.js'}
{footer_script}
{literal}function setPrivacyLevel(selectElement, rootUrl, id, level)
{
selectElement.disabled = true;
var y = new PwgWS(rootUrl);
y.callService(
	"pwg.images.setPrivacyLevel", {image_id: id, level:level} ,
	{
		method: "POST",
		onFailure: function(num, text) { selectElement.disabled = false; alert(num + " " + text); },
		onSuccess: function(result) { selectElement.disabled = false; }
	}
	);
}{/literal}
{/footer_script}
	<select onchange="setPrivacyLevel(this, '{$ROOT_URL}', {$current.id}, this.options[selectedIndex].value)">
		{foreach from=$available_permission_levels item=label key=level}
		<option label="{$label}" value="{$level}"{if $level == $current.level} selected="selected"{/if}>{$label}</option>
		{/foreach}
	</select>
	</td></tr>
{/if}
{/strip}
</table>

{if isset($metadata)}
<table id="Metadata" class="infoTable2">
{foreach from=$metadata item=meta}
	<tr>
		<th colspan="2">{$meta.TITLE}</th>
	</tr>
	{foreach from=$meta.lines item=value key=label}
	<tr>
		<td class="label">{$label}</td>
		<td class="value">{$value}</td>
	</tr>
	{/foreach}
{/foreach}
</table>
{/if}
</div>

{if isset($COMMENT_COUNT)}
<div id="comments">
	{if $COMMENT_COUNT > 0}
		<h3>{$pwg->l10n_dec('%d comment', '%d comments',$COMMENT_COUNT)}</h3>
	{if $COMMENT_COUNT > 2}
		<a href="{$COMMENTS_ORDER_URL}#comments" rel="nofollow">{$COMMENTS_ORDER_TITLE}</a>
	{/if}
	{/if}
	{if !empty($navbar)}{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}

	{if isset($comments)}
		{include file='comment_list.tpl'}
	{/if}

	{if isset($comment_add)}
	<form method="post" action="{$comment_add.F_ACTION}" class="filter" id="addComment">
	<fieldset>
		<legend>{'Add a comment'|@translate}</legend>
		{if $comment_add.SHOW_AUTHOR}
		<label>{'Author'|@translate}<input type="text" name="author"></label>
		{/if}
		<label>{'Comment'|@translate}<textarea name="content" id="contentid" rows="5" cols="80">{$comment_add.CONTENT}</textarea></label>
		<input type="hidden" name="key" value="{$comment_add.KEY}">
		<input type="submit" value="{'Submit'|@translate}">
	</fieldset>
	</form>
	{/if}
</div>
{/if}{*comments*}

{if !empty($PLUGIN_PICTURE_AFTER)}{$PLUGIN_PICTURE_AFTER}{/if}

{if isset($MENUBAR)}
</div>
{/if}
