{combine_script id='core.switchbox' load='async' require='jquery' path='themes/default/js/switchbox.js'}
{if isset($MENUBAR)}{$MENUBAR}{/if}
<div id="content" {if isset($MENUBAR)} class="contentWithMenu" {/if}>

	{if isset($errors) or not empty($infos)}
		{include file='infos_errors.tpl'}
	{/if}

	{if !empty($PLUGIN_PICTURE_BEFORE)}{$PLUGIN_PICTURE_BEFORE}{/if}

	<div id="imageHeaderBar">
		<div class="browsePath">
			{$SECTION_TITLE}<span class="browsePathSeparator">{$LEVEL_SEPARATOR}</span>
			<h2>{$current.TITLE}</h2>
		</div>
	</div>

	<div id="imageToolBar">
		<div class="imageNumber">{$PHOTO}</div>
		{include file='picture_nav_buttons.tpl'|@get_extent:'picture_nav_buttons'}

		<div class="actionButtons">
			{if isset($current.unique_derivatives) && count($current.unique_derivatives)>1}
				{footer_script require='jquery'}
				{literal}
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
					document.cookie = 'picture_deriv='+typeSave+';path=
				{/literal}{$COOKIE_PATH}
				{literal}	';
					}
					(window.SwitchBox=window.SwitchBox||[]).push("#derivativeSwitchLink", "#derivativeSwitchBox");
				{/literal}{/footer_script}
				{strip}<a id="derivativeSwitchLink" title="{'Photo sizes'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
						<span class="pwg-icon pwg-icon-sizes"></span><span class="pwg-button-text">{'Photo sizes'|@translate}</span>
					</a>
					<div id="derivativeSwitchBox" class="switchBox">
						<div class="switchBoxTitle">{'Photo sizes'|@translate}</div>
						{foreach from=$current.unique_derivatives item=derivative key=derivative_type}
							<span class="switchCheck" id="derivativeChecked{$derivative->get_type()}" {if $derivative->get_type() ne $current.selected_derivative->get_type()} style="visibility:hidden"
				{/if}>&#x2714; </span>
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
		{strip}
			{if isset($U_SLIDESHOW_START)}
				<a href="{$U_SLIDESHOW_START}" title="{'slideshow'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
					<span class="pwg-icon pwg-icon-slideshow"></span><span class="pwg-button-text">{'slideshow'|@translate}</span>
				</a>
			{/if}
		{/strip}
		{strip}
			{if isset($U_METADATA)}
				<a href="{$U_METADATA}" title="{'Show file metadata'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
					<span class="pwg-icon pwg-icon-camera-info"></span><span class="pwg-button-text">{'Show file metadata'|@translate}</span>
				</a>
			{/if}
		{/strip}
		{strip}
			{if isset($current.U_DOWNLOAD)}
				<a id="downloadSwitchLink" href="{$current.U_DOWNLOAD}" title="{'Download this file'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
					<span class="pwg-icon pwg-icon-save"></span><span class="pwg-button-text">{'Download'|@translate}</span>
				</a>

				{if !empty($current.formats)}
					{footer_script require='jquery'}
					{literal}
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
			{/if}
		{/strip}
		{if isset($PLUGIN_PICTURE_BUTTONS)}
			{foreach from=$PLUGIN_PICTURE_BUTTONS item=button}{$button}{/foreach}
		{/if}
		{if isset($PLUGIN_PICTURE_ACTIONS)}{$PLUGIN_PICTURE_ACTIONS}{/if}
		{strip}
			{if isset($favorite)}
				<a href="{$favorite.U_FAVORITE}" title="{if $favorite.IS_FAVORITE}{'delete this photo from your favorites'|@translate}{else}{'add this photo to your favorites'|@translate}{/if}" class="pwg-state-default pwg-button" rel="nofollow">
					<span class="pwg-icon pwg-icon-favorite-{if $favorite.IS_FAVORITE}del{else}add{/if}"></span><span class="pwg-button-text">{'Favorites'|@translate}</span>
				</a>
			{/if}
		{/strip}
		{strip}
			{if isset($U_SET_AS_REPRESENTATIVE)}
				<a id="cmdSetRepresentative" href="{$U_SET_AS_REPRESENTATIVE}" title="{'set as album representative'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
					<span class="pwg-icon pwg-icon-representative"></span><span class="pwg-button-text">{'representative'|@translate}</span>
				</a>
			{/if}
		{/strip}
		{strip}
			{if isset($U_PHOTO_ADMIN)}
				<a id="cmdEditPhoto" href="{$U_PHOTO_ADMIN}" title="{'Edit photo'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
					<span class="pwg-icon pwg-icon-edit"></span><span class="pwg-button-text">{'Edit'|@translate}</span>
				</a>
			{/if}
		{/strip}
		{strip}
			{if isset($U_CADDIE)}{*caddie management BEGIN*}
				{footer_script}
				{literal}	function addToCadie(aElement, rootUrl, id)
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
					}
				{/literal}
				{/footer_script}
				<a href="{$U_CADDIE}" onclick="addToCadie(this, '{$ROOT_URL}', {$current.id}); return false;" title="{'Add to caddie'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
					<span class="pwg-icon pwg-icon-caddie-add"> </span><span class="pwg-button-text">{'Caddie'|@translate}</span>
				</a>
			{/if}
		{/strip}{*caddie management END*}
	</div>
</div>{*
<!-- imageToolBar -->*}

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

	</div>{*
	<!-- no significant white space for elegant-->
	*}<div id="infoSwitcher"></div>{*
	<!-- no significant white space for elegant-->
	*}<div id="imageInfos">
		{* {if $DISPLAY_NAV_THUMB}
			<div class="navThumbs">
				{if isset($previous)}
					<a class="navThumb" id="linkPrev" href="{$previous.U_IMG}" title="{'Previous'|@translate} : {$previous.TITLE_ESC}" rel="prev">
						<span class="thumbHover prevThumbHover"></span>
						<img class="{if (isset($previous.path_ext) and $previous.path_ext == 'svg')}svgImg{/if}" src="{if (isset($previous.path_ext) and $previous.path_ext == 'svg')}{$previous.path}{else}{$previous.derivatives.square->get_url()}{/if}" alt="{$previous.TITLE_ESC}">
					</a>
				{elseif isset($U_UP)}
					<a class="navThumb" id="linkPrev" href="{$U_UP}" title="{'Thumbnails'|@translate}">
						<div class="thumbHover">{'First Page'|@translate}<br><br>{'Go back to the album'|@translate}</div>
					</a>
				{/if}
				{if isset($next)}
					<a class="navThumb" id="linkNext" href="{$next.U_IMG}" title="{'Next'|@translate} : {$next.TITLE_ESC}" rel="next">
						<span class="thumbHover nextThumbHover"></span>
						<img class="{if (isset($next.path_ext) and $next.path_ext == 'svg')}svgImg{/if}" src="{if (isset($next.path_ext) and $next.path_ext == 'svg')}{$next.path}{else}{$next.derivatives.square->get_url()}{/if}" alt="{$next.TITLE_ESC}">
					</a>
				{elseif isset($U_UP)}
					<a class="navThumb" id="linkNext" href="{$U_UP}" title="{'Thumbnails'|@translate}">
						<div class="thumbHover">{'Last Page'|@translate}<br><br>{'Go back to the album'|@translate}</div>
					</a>
				{/if}
			</div>
		{/if} *}

		<dl id="standard" class="imageInfoTable">
			{strip}
				<div id="col-left" class="imageInfoColumn">
					{if $display_info.posted_on}
						<div id="datepost" class="imageInfo">
							{* <dt>{'Posted on'|@translate}</dt> *}
							<dt><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#777777" viewBox="0 0 256 256"><path d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32ZM84,184a12,12,0,1,1,12-12A12,12,0,0,1,84,184Zm44,0a12,12,0,1,1,12-12A12,12,0,0,1,128,184Zm0-40a12,12,0,1,1,12-12A12,12,0,0,1,128,144Zm44,40a12,12,0,1,1,12-12A12,12,0,0,1,172,184Zm0-40a12,12,0,1,1,12-12A12,12,0,0,1,172,144Zm36-64H48V48H72v8a8,8,0,0,0,16,0V48h80v8a8,8,0,0,0,16,0V48h24Z"></path></svg></dt>
							<dd>{$INFO_POSTED_DATE}</dd>
						</div>
					{/if}

					{if $display_info.filesize and isset($INFO_FILESIZE)}
						<div id="Filesize" class="imageInfo">
							{* <dt>{'Filesize'|@translate}</dt> *}
							<dt><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#777777" viewBox="0 0 256 256"><path d="M224,64H32A16,16,0,0,0,16,80v96a16,16,0,0,0,16,16H224a16,16,0,0,0,16-16V80A16,16,0,0,0,224,64Zm-36,76a12,12,0,1,1,12-12A12,12,0,0,1,188,140Z"></path></svg></dt>
							<dd>{$INFO_FILESIZE}</dd>
						</div>
					{/if}

					{if $display_info.author and isset($INFO_AUTHOR)}
						<div id="Author" class="imageInfo">
							{* <dt>{'Author'|@translate}</dt> *}
							<dt><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#777777" viewBox="0 0 256 256"><path d="M230.93,220a8,8,0,0,1-6.93,4H32a8,8,0,0,1-6.92-12c15.23-26.33,38.7-45.21,66.09-54.16a72,72,0,1,1,73.66,0c27.39,8.95,50.86,27.83,66.09,54.16A8,8,0,0,1,230.93,220Z"></path></svg></dt>
							<dd>{$INFO_AUTHOR}</dd>
						</div>
					{/if}

					{if $display_info.visits}
						<div id="Visits" class="imageInfo">
							{* <dt>{'Visits'|@translate}</dt> *}
							<dt><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#777777" viewBox="0 0 256 256"><path d="M247.31,124.76c-.35-.79-8.82-19.58-27.65-38.41C194.57,61.26,162.88,48,128,48S61.43,61.26,36.34,86.35C17.51,105.18,9,124,8.69,124.76a8,8,0,0,0,0,6.5c.35.79,8.82,19.57,27.65,38.4C61.43,194.74,93.12,208,128,208s66.57-13.26,91.66-38.34c18.83-18.83,27.3-37.61,27.65-38.4A8,8,0,0,0,247.31,124.76ZM128,168a40,40,0,1,1,40-40A40,40,0,0,1,128,168Z"></path></svg></dt>
							<dd>{$INFO_VISITS}</dd>
						</div>
					{/if}

					{if $display_info.rating_score and isset($rate_summary)}
						<div id="Average" class="imageInfo">
							{* <dt>{'Rating score'|@translate}</dt> *}
							<dt><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#777777" viewBox="0 0 256 256"><path d="M234.29,114.85l-45,38.83L203,211.75a16.4,16.4,0,0,1-24.5,17.82L128,198.49,77.47,229.57A16.4,16.4,0,0,1,53,211.75l13.76-58.07-45-38.83A16.46,16.46,0,0,1,31.08,86l59-4.76,22.76-55.08a16.36,16.36,0,0,1,30.27,0l22.75,55.08,59,4.76a16.46,16.46,0,0,1,9.37,28.86Z"></path></svg></dt>
							<dd>
								{if $rate_summary.count}
									<span id="ratingScore">{$rate_summary.score}</span> <span id="ratingCount">({$rate_summary.count|@translate_dec:'%d rate':'%d rates'})</span>
								{else}
									<span id="ratingScore">{'no rate'|@translate}</span> <span id="ratingCount"></span>
								{/if}
							</dd>
						</div>
					{/if}

					<div id="PostedBy" class="imageInfo">
						{* <dt>{'Posted by'|@translate}</dt> *}
						<dt><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#777777" viewBox="0 0 256 256"><path d="M227.31,73.37,182.63,28.68a16,16,0,0,0-22.63,0L36.69,152A15.86,15.86,0,0,0,32,163.31V208a16,16,0,0,0,16,16H92.69A15.86,15.86,0,0,0,104,219.31L227.31,96a16,16,0,0,0,0-22.63ZM192,108.68,147.31,64l24-24L216,84.68Z"></path></svg></dt>
						<dd>
							{if $display_info.posted_by and isset($INFO_POSTED_BY)}  
							{* Didn't find this info, so I just put it this way, if it's not set, it will display "ABCD Studio" as default value. *}
								{$INFO_POSTED_BY}
							{* {else}
								{'ABCD Studio'|@translate} *}
							{/if}
						</dd>
					</div>

					{* {if $display_info.created_on and isset($INFO_CREATION_DATE)}
						<div id="datecreate" class="imageInfo">
							<dt>{'Created on'|@translate}</dt>
							<dd>{$INFO_CREATION_DATE}</dd>
						</div>
					{/if} *}
					
					{* {if $display_info.dimensions and isset($INFO_DIMENSIONS)}
						<div id="Dimensions" class="imageInfo">
							<dt>{'Dimensions'|@translate}</dt>
							<dd>{$INFO_DIMENSIONS}</dd>
						</div>
					{/if} *}
				</div>
				<div id="col-right" class="imageInfoColumn">
					{if $display_info.file}
						<div id="File" class="imageInfo">
							{* <dt>{'File'|@translate}</dt> *}
							<dt><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#777777" viewBox="0 0 256 256"><path d="M216,40H40A16,16,0,0,0,24,56V200a16,16,0,0,0,16,16H216a16,16,0,0,0,16-16V56A16,16,0,0,0,216,40ZM156,88a12,12,0,1,1-12,12A12,12,0,0,1,156,88Zm60,112H40V160.69l46.34-46.35a8,8,0,0,1,11.32,0h0L165,181.66a8,8,0,0,0,11.32-11.32l-17.66-17.65L173,138.34a8,8,0,0,1,11.31,0L216,170.07V200Z"></path></svg></dt>
							<dd>{$INFO_FILE}</dd>
						</div>
					{/if}
					
					{if $display_info.categories and isset($related_categories)}
						<div id="Categories" class="imageInfo">
							{* <dt>{'Albums'|@translate}</dt> *}
							<dt><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#777777" viewBox="0 0 256 256"><path d="M245,110.64A16,16,0,0,0,232,104H216V88a16,16,0,0,0-16-16H130.67L102.94,51.2a16.14,16.14,0,0,0-9.6-3.2H40A16,16,0,0,0,24,64V208h0a8,8,0,0,0,8,8H211.1a8,8,0,0,0,7.59-5.47l28.49-85.47A16.05,16.05,0,0,0,245,110.64ZM93.34,64,123.2,86.4A8,8,0,0,0,128,88h72v16H69.77a16,16,0,0,0-15.18,10.94L40,158.7V64Z"></path></svg></dt>
							<dd>
								<ul>
									{foreach from=$related_categories item=cat}
										<li>{$cat}</li>
									{/foreach}
								</ul>
							</dd>
						</div>
					{/if}

					{if ($display_info.tags and isset($related_tags))}
						<div id="Tags" class="imageInfo">
							{* <dt>{'Tags'|@translate}</dt> *}
							<dt><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#777777" viewBox="0 0 256 256"><path d="M243.31,136,144,36.69A15.86,15.86,0,0,0,132.69,32H40a8,8,0,0,0-8,8v92.69A15.86,15.86,0,0,0,36.69,144L136,243.31a16,16,0,0,0,22.63,0l84.68-84.68a16,16,0,0,0,0-22.63ZM84,96A12,12,0,1,1,96,84,12,12,0,0,1,84,96Z"></path></svg></dt>
							<dd>
								{foreach from=$related_tags item=tag name=tag_loop}
									{if !$smarty.foreach.tag_loop.first} {/if}<a href="{$tag.URL}" class="tagBorder">{$tag.name}</a>
								{/foreach}
							</dd>
						</div>
					{/if}
					
					

					{* {if isset($PDF_NB_PAGES) and $current.path_ext=="pdf" }
						<div id="Pages" class="imageInfo">
							<dt>{'Pages'|@translate}</dt>
							<dd>{$PDF_NB_PAGES}</dd>
						</div>

					{/if} *}

					
				</div>

				{* {if isset($rating)}
					<div id="rating" class="imageInfo">
						<dt>
							<span id="updateRate">
								{if isset($rating.USER_RATE)}{'Update your rating'|@translate}{else}{'Rate this photo'|@translate}{/if}
							</span>
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
				{/if} *}

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
	<div id="comments" {if (!isset($comment_add) && ($COMMENT_COUNT == 0))}class="noCommentContent" {else}class="commentContent" {/if}>
		<div id="commentsSwitcher"></div>
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
							<input type="submit" value="{'Submit'|@translate}">
						</p>
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
					{$COMMENT_LIST}
				</div>
			{/if}
			<div style="clear:both"></div>
		</div>

	</div>
{/if}{*comments*}

{if !empty($PLUGIN_PICTURE_AFTER)}{$PLUGIN_PICTURE_AFTER}{/if}

</div>