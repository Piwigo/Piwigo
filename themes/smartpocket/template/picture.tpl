{if isset($errors) or not empty($infos)}
{include file='infos_errors.tpl'}
{/if}
<div data-role="content" id="picture_page">
<ul data-role="listview" data-inset="true">
	<li data-role="list-divider">{$SECTION_TITLE}<span class="browsePathSeparator">{$LEVEL_SEPARATOR}</span>{$current.TITLE}</li>
</ul>
{$ELEMENT_CONTENT}

{include file='picture_nav_buttons.tpl'|@get_extent:'picture_nav_buttons'}

{if isset($COMMENT_IMG)}
<p class="imageComment">{$COMMENT_IMG}</p>
{/if}

<ul data-role="listview" data-inset="true" id="PictureInfo">
{strip}
	{if $display_info.author and isset($INFO_AUTHOR)}
	<li id="Author" class="imageInfo">
		<dt>{'Author'|@translate}</dt>
		<dd>{$INFO_AUTHOR}</dd>
	</li>
	{/if}
	{if $display_info.created_on and isset($INFO_CREATION_DATE)}
	<li id="datecreate" class="imageInfo">
		<dt>{'Created on'|@translate}</dt>
		<dd>{$INFO_CREATION_DATE}</dd>
	</li>
	{/if}
	{if $display_info.posted_on}
	<li id="datepost" class="imageInfo">
		<dt>{'Posted on'|@translate}</dt>
		<dd>{$INFO_POSTED_DATE}</dd>
	</li>
	{/if}
	{if $display_info.dimensions and isset($INFO_DIMENSIONS)}
	<li id="Dimensions" class="imageInfo">
		<dt>{'Dimensions'|@translate}</dt>
		<dd>{$INFO_DIMENSIONS}</dd>
	</li>
	{/if}
	{if $display_info.file}
	<li id="File" class="imageInfo">
		<dt>{'File'|@translate}</dt>
		<dd>{$INFO_FILE}</dd>
	</li>
	{/if}
	{if $display_info.filesize and isset($INFO_FILESIZE)}
	<li id="Filesize" class="imageInfo">
		<dt>{'Filesize'|@translate}</dt>
		<dd>{$INFO_FILESIZE}</dd>
	</li>
	{/if}
	{if $display_info.tags and isset($related_tags)}
	<li id="Tags" class="imageInfo">
		<dt>{'Tags'|@translate}</dt>
		<dd>
		{foreach from=$related_tags item=tag name=tag_loop}{if !$smarty.foreach.tag_loop.first}, {/if}<a href="{$tag.URL}">{$tag.name}</a>{/foreach}
		</dd>
	</li>
	{/if}
	{if $display_info.categories and isset($related_categories)}
	<li id="Categories" class="imageInfo">
		<dt>{'Albums'|@translate}</dt>
		<dd>
			<ul>
				{foreach from=$related_categories item=cat}
				<li>{$cat}</li>
				{/foreach}
			</ul>
		</dd>
	</li>
	{/if}
	{if $display_info.visits}
	<li id="Visits" class="imageInfo">
		<dt>{'Visits'|@translate}</dt>
		<dd>{$INFO_VISITS}</dd>
	</li>
	{/if}

{if $display_info.rating_score and isset($rate_summary)}
	<li id="Average" class="imageInfo">
		<dt>{'Rating score'|@translate}</dt>
		<dd>
		{if $rate_summary.count}
			<span id="ratingScore">{$rate_summary.score}</span> <span id="ratingCount">({$rate_summary.count|@translate_dec:'%d rate':'%d rates'})</span>
		{else}
			<span id="ratingScore">{'no rate'|@translate}</span> <span id="ratingCount"></span>
		{/if}
		</dd>
	</li>
{/if}

{if isset($rating)}
	<li id="rating" class="imageInfo">
		<dt>
			<span id="updateRate">{if isset($rating.USER_RATE)}{'Update your rating'|@translate}{else}{'Rate this photo'|@translate}{/if}</span>
		</dt>
			<form action="{$rating.F_ACTION}" method="post" id="rateForm" style="margin:0;">
			<div data-role="controlgroup" data-type="horizontal" align="center">
			{foreach from=$rating.marks item=mark name=rate_loop}
			{if isset($rating.USER_RATE) && $mark==$rating.USER_RATE}
				<input type="button" name="rate" value="{$mark}" class="rateButtonSelected" title="{$mark}">
			{else}
				<input type="submit" name="rate" value="{$mark}" class="rateButton" title="{$mark}">
			{/if}
			{/foreach}
			</div>
			</form>
	</li>
{/if}
</ul>

{if isset($metadata)}
<ul data-role="listview" data-inset="true">
{foreach from=$metadata item=meta}
	<li><h3>{$meta.TITLE}</h3>
	{foreach from=$meta.lines item=value key=label}
		<div class="imageInfo">
			<dt>{$label}</dt>
			<dd>{$value}</dd>
		</div>
	{/foreach}</li>
{/foreach}
</dl>
{/if}
</ul>

{if isset($COMMENT_COUNT)}
<ul data-role="listview" data-inset="true">
	<h3>{$COMMENT_COUNT|@translate_dec:'%d comment':'%d comments'}</h3>

	<div id="pictureComments">
		{if isset($comment_add)}
		<div data-role="collapsible">
			<h3>{'Add a comment'|@translate}</h3>
			<form method="post" action="{$comment_add.F_ACTION}" id="addComment">
				{if $comment_add.SHOW_AUTHOR}
					<p><label for="author">{'Author'|@translate}{if $comment_add.AUTHOR_MANDATORY} ({'mandatory'|@translate}){/if} :</label></p>
					<p><input type="text" name="author" id="author" value="{$comment_add.AUTHOR}"></p>
				{/if}
				{if $comment_add.SHOW_EMAIL}
					<p><label for="email">{'Email address'|@translate}{if $comment_add.EMAIL_MANDATORY} ({'mandatory'|@translate}){/if} :</label></p>
					<p><input type="text" name="email" id="email" value="{$comment_add.EMAIL}"></p>
				{/if}
				<p><label for="website_url">{'Website'|@translate} :</label></p>
				<p><input type="text" name="website_url" id="website_url" value="{$comment_add.WEBSITE_URL}"></p>
				<p><label for="contentid">{'Comment'|@translate} ({'mandatory'|@translate}) :</label></p>
				<p><textarea name="content" id="contentid" rows="5" cols="50">{$comment_add.CONTENT}</textarea></p>
				<p><input type="hidden" name="key" value="{$comment_add.KEY}">
					<input type="submit" value="{'Submit'|@translate}"></p>
			</form>
		</div>
		{/if}
		{if isset($comments)}
		<ul data-role="listview" data-inset="true">
			{if (($COMMENT_COUNT > 2) || !empty($navbar))}
				<div id="pictureCommentNavBar">
					{if $COMMENT_COUNT > 2}
						<a href="{$COMMENTS_ORDER_URL}#comments" rel="nofollow" class="commentsOrder">{$COMMENTS_ORDER_TITLE}</a>
					{/if}
					{if !empty($navbar) }{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}
				</div>
			{/if}
			{include file='comment_list.tpl' from="picture"}
		</ul>
		{/if}
	</div>

</ul>
{/if}{*comments*}

{include file='picture_nav_buttons.tpl'|@get_extent:'picture_nav_buttons'}

</div>
