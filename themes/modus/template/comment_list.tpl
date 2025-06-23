{if isset($comment_derivative_params)}
{strip}{html_style}
.commentElement .illustration{
	width:{$comment_derivative_params->max_width()+5}px
}

.content .commentElement .description{
	min-height:{$comment_derivative_params->max_height()+5}px
}

@media {cssResolution min=1.3}{
	.illustration IMG{
		max-width:{($comment_derivative_params->max_width()/2)|intval}px;
		max-height:{($comment_derivative_params->max_height()/2)|intval}px;
	}

	.commentElement .illustration{
		width:{($comment_derivative_params->max_width()/2+5)|intval}px
	}

	.content .commentElement .description{
		min-height:{($comment_derivative_params->max_height()/2+5)|intval}px
	}
}

{/html_style}{/strip}
{/if}
<ul class="commentsList">
{foreach from=$comments item=comment name=comment_loop}
<li class="commentElement {if $smarty.foreach.comment_loop.index is odd}odd{else}even{/if}">
	{if isset($comment.src_image)}
	{if isset($comment_derivative_params)}
	{assign var=derivative value=$pwg->derivative($comment_derivative_params, $comment.src_image)}
	{else}
	{assign var=derivative value=$pwg->derivative($derivative_params, $comment.src_image)}
	{/if}
	<div class="illustration">
		<a href="{$comment.U_PICTURE}">
		<img src="{$derivative->get_url()}" alt="{$comment.ALT}">
		</a>
	</div>
	{/if}
	<div class="description">
		{if isset($comment.U_DELETE) or isset($comment.U_VALIDATE) or isset($comment.U_EDIT)}
		<div class="actions" style="float:right;font-size:90%">
		{if isset($comment.U_DELETE)}
			<a href="{$comment.U_DELETE}" onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');">
				{'Delete'|@translate}
			</a>{if isset($comment.U_VALIDATE) or isset($comment.U_EDIT) or isset($comment.U_CANCEL)} | {/if}
		{/if}
		{if isset($comment.U_CANCEL)}
			<a href="{$comment.U_CANCEL}">
				{'Cancel'|@translate}
			</a>{if isset($comment.U_VALIDATE)} | {/if}
		{/if}
		{if isset($comment.U_EDIT) and !isset($comment.IN_EDIT)}
			<a class="editComment" href="{$comment.U_EDIT}#edit_comment">
				{'Edit'|@translate}
			</a>{if isset($comment.U_VALIDATE)} | {/if}
		{/if}
		{if isset($comment.U_VALIDATE)}
			<a href="{$comment.U_VALIDATE}">
				{'Validate'|@translate}
			</a>
		{/if}&nbsp;
		</div>
		{/if}

		<span class="commentAuthor">{if $comment.WEBSITE_URL}<a href="{$comment.WEBSITE_URL}" class="external" target="_blank" rel="nofollow">{$comment.AUTHOR}</a>{else}{$comment.AUTHOR}{/if}</span>
			{if isset($comment.EMAIL)}- <a href="mailto:{$comment.EMAIL}">{$comment.EMAIL}</a>{/if}
			- <span class="commentDate">{$comment.DATE}</span>
		{if isset($comment.IN_EDIT)}
		<a name="edit_comment"></a>
		<form method="post" action="{$comment.U_EDIT}" id="editComment">
			<p><label for="contenteditid">{'Edit a comment'|@translate} :</label></p>
			<p><textarea name="content" id="contenteditid" rows="5" cols="80">{$comment.CONTENT|@escape}</textarea></p>
			<p><label for="website_url">{'Website'|@translate} :</label></p>
			<p><input type="text" name="website_url" id="website_url" value="{$comment.WEBSITE_URL}" size="40"></p>
			<p><input type="hidden" name="key" value="{$comment.KEY}">
				<input type="hidden" name="pwg_token" value="{$comment.PWG_TOKEN}">
				<input type="hidden" name="image_id" value="{$comment.IMAGE_ID|@default:$current.id}">
				<input type="submit" value="{'Submit'|@translate}">
			</p>
		</form>
		{else}
		<blockquote><div>{$comment.CONTENT}</div></blockquote>
		{/if}
	</div>
</li>
{/foreach}
</ul>
