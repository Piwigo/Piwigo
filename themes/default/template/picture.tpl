{combine_script id='core.switchbox' load='async' require='jquery' path='themes/default/js/switchbox.js'}
{if isset($MENUBAR)}{$MENUBAR}{/if}
<div id="content"{if isset($MENUBAR)} class="contentWithMenu"{/if}>

{if isset($errors) or not empty($infos)}
{include file='infos_errors.tpl'}
{/if}

{if !empty($PLUGIN_PICTURE_BEFORE)}{$PLUGIN_PICTURE_BEFORE}{/if}

<div id="imageHeaderBar">
	<div class="browsePath">
		{$SECTION_TITLE}<span class="browsePathSeparator">{$LEVEL_SEPARATOR}</span><h2>{$current.TITLE}</h2>
	</div>
</div>

<div id="imageToolBar">
<div class="imageNumber">{$PHOTO}</div>
{include file='picture_nav_buttons.tpl'|@get_extent:'picture_nav_buttons'}

<div class="actionButtons">
{if isset($current.unique_derivatives) && count($current.unique_derivatives)>1}
{footer_script require='jquery'}{literal}
function changeImgSrc(url,typeSave,typeMap)
{
	var theImg = document.getElementById("theMainImage");
	if (theImg)
	{
		theImg.removeAttribute("width");theImg.removeAttribute("height");
		theImg.src = url;
		theImg.useMap = "#map"+typeMap;
	}
	jQuery('#derivativeSwitchBox .switchCheck').css('visibility','hidden');
	jQuery('#derivativeChecked'+typeMap).css('visibility','visible');
	document.cookie = 'picture_deriv='+typeSave+';path={/literal}{$COOKIE_PATH}{literal}';
}
(window.SwitchBox=window.SwitchBox||[]).push("#derivativeSwitchLink", "#derivativeSwitchBox");
{/literal}{/footer_script}
{strip}<a id="derivativeSwitchLink" title="{'Photo sizes'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
  <span class="pwg-icon pwg-icon-sizes"></span><span class="pwg-button-text">{'Photo sizes'|@translate}</span>
</a>
<div id="derivativeSwitchBox" class="switchBox">
  <div class="switchBoxTitle">{'Photo sizes'|@translate}</div>
  {foreach from=$current.unique_derivatives item=derivative key=derivative_type}
  <span class="switchCheck" id="derivativeChecked{$derivative->get_type()}"{if $derivative->get_type() ne $current.selected_derivative->get_type()} style="visibility:hidden"{/if}>&#x2714; </span>
  <a href="javascript:changeImgSrc('{$derivative->get_url()|@escape:javascript}','{$derivative_type}','{$derivative->get_type()}')">
    {$derivative->get_type()|@translate}<span class="derivativeSizeDetails"> ({$derivative->get_size_hr()})</span>
  </a><br>
  {/foreach}
  {if isset($U_ORIGINAL)}
    {combine_script id='core.scripts' load='async' path='themes/default/js/scripts.js'}
  <a href="javascript:phpWGOpenWindow('{$U_ORIGINAL}','xxx','scrollbars=yes,toolbar=no,status=no,resizable=yes')" rel="nofollow">{'Original'|@translate}</a>
  {/if}
</div>
{/strip}
{/if}
{strip}{if isset($U_SLIDESHOW_START)}
	<a href="{$U_SLIDESHOW_START}" title="{'slideshow'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
		<span class="pwg-icon pwg-icon-slideshow"></span><span class="pwg-button-text">{'slideshow'|@translate}</span>
	</a>
{/if}{/strip}
{strip}{if isset($U_METADATA)}
	<a href="{$U_METADATA}" title="{'Show file metadata'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
		<span class="pwg-icon pwg-icon-camera-info"></span><span class="pwg-button-text">{'Show file metadata'|@translate}</span>
	</a>
{/if}{/strip}
{strip}{if isset($current.U_DOWNLOAD)}
	<a id="downloadSwitchLink" href="{$current.U_DOWNLOAD}" title="{'Download this file'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
		<span class="pwg-icon pwg-icon-save"></span><span class="pwg-button-text">{'Download'|@translate}</span>
	</a>

{if !empty($current.formats)}
{footer_script require='jquery'}{literal}
jQuery().ready(function() {
  jQuery("#downloadSwitchLink").removeAttr("href");

  (window.SwitchBox=window.SwitchBox||[]).push("#downloadSwitchLink", "#downloadSwitchBox");
});
{/literal}{/footer_script}

<div id="downloadSwitchBox" class="switchBox">
  <div class="switchBoxTitle">{'Download'|translate} - {'Formats'|translate}</div>
  <ul>
  {foreach from=$current.formats item=format}
    <li><a href="{$format.download_url}" rel="nofollow">{$format.label}<span class="downloadformatDetails"> ({$format.filesize})</span></a></li>
  {/foreach}
  </ul>
</div>
{/if} {* has formats *}
{/if}{/strip}
{if isset($PLUGIN_PICTURE_BUTTONS)}{foreach from=$PLUGIN_PICTURE_BUTTONS item=button}{$button}{/foreach}{/if}
{if isset($PLUGIN_PICTURE_ACTIONS)}{$PLUGIN_PICTURE_ACTIONS}{/if}
{strip}{if isset($favorite)}
	<a href="{$favorite.U_FAVORITE}" title="{if $favorite.IS_FAVORITE}{'delete this photo from your favorites'|@translate}{else}{'add this photo to your favorites'|@translate}{/if}" class="pwg-state-default pwg-button" rel="nofollow">
		<span class="pwg-icon pwg-icon-favorite-{if $favorite.IS_FAVORITE}del{else}add{/if}"></span><span class="pwg-button-text">{'Favorites'|@translate}</span>
	</a>
{/if}{/strip}
{strip}{if isset($U_SET_AS_REPRESENTATIVE)}
	<a id="cmdSetRepresentative" href="{$U_SET_AS_REPRESENTATIVE}" title="{'set as album representative'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
		<span class="pwg-icon pwg-icon-representative"></span><span class="pwg-button-text">{'representative'|@translate}</span>
	</a>
{/if}{/strip}
{strip}{if isset($U_PHOTO_ADMIN)}
	<a id="cmdEditPhoto" href="{$U_PHOTO_ADMIN}" title="{'Edit photo'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
		<span class="pwg-icon pwg-icon-edit"></span><span class="pwg-button-text">{'Edit'|@translate}</span>
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
</div>{*<!-- imageToolBar -->*}

<div id="theImageAndInfos">
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

</div>{*<!-- no significant white space for elegant-->
*}<div id="infoSwitcher"></div>{*<!-- no significant white space for elegant-->
*}<div id="imageInfos">
{if $DISPLAY_NAV_THUMB}
	<div class="navThumbs">
		{if isset($previous)}
			<a class="navThumb" id="linkPrev" href="{$previous.U_IMG}" title="{'Previous'|@translate} : {$previous.TITLE_ESC}" rel="prev">
				<span class="thumbHover prevThumbHover"></span>
				<img src="{$previous.derivatives.square->get_url()}" alt="{$previous.TITLE_ESC}">
			</a>
		{elseif isset($U_UP)}
			<a class="navThumb" id="linkPrev" href="{$U_UP}" title="{'Thumbnails'|@translate}">
				<div class="thumbHover">{'First Page'|@translate}<br><br>{'Go back to the album'|@translate}</div>
			</a>
		{/if}
		{if isset($next)}
			<a class="navThumb" id="linkNext" href="{$next.U_IMG}" title="{'Next'|@translate} : {$next.TITLE_ESC}" rel="next">
				<span class="thumbHover nextThumbHover"></span>
				<img src="{$next.derivatives.square->get_url()}" alt="{$next.TITLE_ESC}">
			</a>
		{elseif isset($U_UP)}
			<a class="navThumb" id="linkNext"  href="{$U_UP}"  title="{'Thumbnails'|@translate}">
				<div class="thumbHover">{'Last Page'|@translate}<br><br>{'Go back to the album'|@translate}</div>
			</a>
		{/if}
	</div>
{/if}

<dl id="standard" class="imageInfoTable">
{strip}
	{if $display_info.author and isset($INFO_AUTHOR)}
	<div id="Author" class="imageInfo">
		<dt>{'Author'|@translate}</dt>
		<dd>{$INFO_AUTHOR}</dd>
	</div>
	{/if}
	{if $display_info.created_on and isset($INFO_CREATION_DATE)}
	<div id="datecreate" class="imageInfo">
		<dt>{'Created on'|@translate}</dt>
		<dd>{$INFO_CREATION_DATE}</dd>
	</div>
	{/if}
	{if $display_info.posted_on}
	<div id="datepost" class="imageInfo">
		<dt>{'Posted on'|@translate}</dt>
		<dd>{$INFO_POSTED_DATE}</dd>
	</div>
	{/if}
	{if $display_info.dimensions and isset($INFO_DIMENSIONS)}
	<div id="Dimensions" class="imageInfo">
		<dt>{'Dimensions'|@translate}</dt>
		<dd>{$INFO_DIMENSIONS}</dd>
	</div>
	{/if}
	{if $display_info.file}
	<div id="File" class="imageInfo">
		<dt>{'File'|@translate}</dt>
		<dd>{$INFO_FILE}</dd>
	</div>
	{/if}
	{if $display_info.filesize and isset($INFO_FILESIZE)}
	<div id="Filesize" class="imageInfo">
		<dt>{'Filesize'|@translate}</dt>
		<dd>{$INFO_FILESIZE}</dd>
	</div>
	{/if}
	{if $display_info.tags and isset($related_tags)}
	<div id="Tags" class="imageInfo">
		<dt>{'Tags'|@translate}</dt>
		<dd>
		{foreach from=$related_tags item=tag name=tag_loop}{if !$smarty.foreach.tag_loop.first}, {/if}<a href="{$tag.URL}">{$tag.name}</a>{/foreach}
		</dd>
	</div>
	{/if}
	{if $display_info.categories and isset($related_categories)}
	<div id="Categories" class="imageInfo">
		<dt>{'Albums'|@translate}</dt>
		<dd>
			<ul>
				{foreach from=$related_categories item=cat}
				<li>{$cat}</li>
				{/foreach}
			</ul>
		</dd>
	</div>
	{/if}
	{if $display_info.visits}
	<div id="Visits" class="imageInfo">
		<dt>{'Visits'|@translate}</dt>
		<dd>{$INFO_VISITS}</dd>
	</div>
	{/if}

{if $display_info.rating_score and isset($rate_summary)}
	<div id="Average" class="imageInfo">
		<dt>{'Rating score'|@translate}</dt>
		<dd>
		{if $rate_summary.count}
			<span id="ratingScore">{$rate_summary.score}</span> <span id="ratingCount">({$rate_summary.count|@translate_dec:'%d rate':'%d rates'})</span>
		{else}
			<span id="ratingScore">{'no rate'|@translate}</span> <span id="ratingCount"></span>
		{/if}
		</dd>
	</div>
{/if}

{if isset($rating)}
	<div id="rating" class="imageInfo">
		<dt>
			<span id="updateRate">{if isset($rating.USER_RATE)}{'Update your rating'|@translate}{else}{'Rate this photo'|@translate}{/if}</span>
		</dt>
		<dd>
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
						if (e) {ldelim}
							if (rating.count == 1) {ldelim}
								e.innerHTML = "({'%d rate'|@translate|@escape:'javascript'})".replace( "%d", rating.count);
							} else {ldelim}
								e.innerHTML = "({'%d rates'|@translate|@escape:'javascript'})".replace( "%d", rating.count);
              }
						{rdelim}
					{rdelim}{rdelim} );
			{/footer_script}
			{/strip}
			</div>
			</form>
		</dd>
	</div>
{/if}

{if $display_info.privacy_level and isset($available_permission_levels)}
	<div id="Privacy" class="imageInfo">
		<dt>{'Who can see this photo?'|@translate}</dt>
		<dd>
			<div>
				<a id="privacyLevelLink" href>{$available_permission_levels[$current.level]}</a>
			</div>
{combine_script id='core.scripts' load='async' path='themes/default/js/scripts.js'}
{footer_script require='jquery'}{strip}
function setPrivacyLevel(id, level){
(new PwgWS('{$ROOT_URL}')).callService(
	"pwg.images.setPrivacyLevel", { image_id:id, level:level},
	{
		method: "POST",
		onFailure: function(num, text) { alert(num + " " + text); },
		onSuccess: function(result) {
			  jQuery('#privacyLevelBox .switchCheck').css('visibility','hidden');
				jQuery('#switchLevel'+level).prev('.switchCheck').css('visibility','visible');
				jQuery('#privacyLevelLink').text(jQuery('#switchLevel'+level).text());
		}
	}
	);
}
(window.SwitchBox=window.SwitchBox||[]).push("#privacyLevelLink", "#privacyLevelBox");
{/strip}{/footer_script}
			<div id="privacyLevelBox" class="switchBox" style="display:none">
				{foreach from=$available_permission_levels item=label key=level}
					<span class="switchCheck"{if $level != $current.level} style="visibility:hidden"{/if}>&#x2714; </span>
					<a id="switchLevel{$level}" href="javascript:setPrivacyLevel({$current.id},{$level})">{$label}</a><br>
				{/foreach}
			</div>
		</dd>
	</div>
{/if}
{/strip}
</dl>

{if isset($metadata)}
<dl id="Metadata" class="imageInfoTable">
{foreach from=$metadata item=meta}
	<h3>{$meta.TITLE}</h3>
	{foreach from=$meta.lines item=value key=label}
		<div class="imageInfo">
			<dt>{$label}</dt>
			<dd>{$value}</dd>
		</div>
	{/foreach}
{/foreach}
</dl>
{/if}
</div>
</div>

{if isset($COMMENT_COUNT)}
<div id="comments" {if (!isset($comment_add) && ($COMMENT_COUNT == 0))}class="noCommentContent"{else}class="commentContent"{/if}><div id="commentsSwitcher"></div>
	<h3>{$COMMENT_COUNT|@translate_dec:'%d comment':'%d comments'}</h3>

	<div id="pictureComments">
		{if isset($comment_add)}
		<div id="commentAdd">
			<h4>{'Add a comment'|@translate}</h4>
			<form method="post" action="{$comment_add.F_ACTION}" id="addComment">
				{if $comment_add.SHOW_AUTHOR}
					<p><label for="author">{'Author'|@translate}{if $comment_add.AUTHOR_MANDATORY} ({'mandatory'|@translate}){/if} :</label></p>
					<p><input type="text" name="author" id="author" value="{$comment_add.AUTHOR}"></p>
				{/if}
				{if $comment_add.SHOW_EMAIL}
					<p><label for="email">{'Email address'|@translate}{if $comment_add.EMAIL_MANDATORY} ({'mandatory'|@translate}){/if} :</label></p>
					<p><input type="text" name="email" id="email" value="{$comment_add.EMAIL}"></p>
				{/if}
        {if $comment_add.SHOW_WEBSITE}
          <p><label for="website_url">{'Website'|@translate} :</label></p>
          <p><input type="text" name="website_url" id="website_url" value="{$comment_add.WEBSITE_URL}"></p>
        {/if}
				<p><label for="contentid">{'Comment'|@translate} ({'mandatory'|@translate}) :</label></p>
				<p><textarea name="content" id="contentid" rows="5" cols="50">{$comment_add.CONTENT}</textarea></p>
				<p><input type="hidden" name="key" value="{$comment_add.KEY}">
					<input type="submit" value="{'Submit'|@translate}"></p>
			</form>
		</div>
		{/if}
		{if isset($comments)}
		<div id="pictureCommentList">
			{if (($COMMENT_COUNT > 2) || !empty($navbar))}
				<div id="pictureCommentNavBar">
					{if $COMMENT_COUNT > 2}
						<a href="{$COMMENTS_ORDER_URL}#comments" rel="nofollow" class="commentsOrder">{$COMMENTS_ORDER_TITLE}</a>
					{/if}
					{if !empty($navbar) }{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}
				</div>
			{/if}
			{include file='comment_list.tpl'}
		</div>
		{/if}
		<div style="clear:both"></div>
	</div>

</div>
{/if}{*comments*}

{if !empty($PLUGIN_PICTURE_AFTER)}{$PLUGIN_PICTURE_AFTER}{/if}

</div>
