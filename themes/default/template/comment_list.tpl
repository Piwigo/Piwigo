{if isset($comment_derivative_params)}
{strip}{html_style}
.commentElement DIV.illustration{ldelim}
	width: {$comment_derivative_params->max_width()+5}px;
}

.content .commentElement .description{ldelim}
	height: {$comment_derivative_params->max_height()+5}px;
}
{/html_style}{/strip}
{/if}
<ul class="commentsList">
{foreach from=$comments item=comment name=comment_loop}
<li>
	<div class="commentElement {if $smarty.foreach.comment_loop.index is odd}odd{else}even{/if}">
	{if isset($comment.src_image)}
	<div class="illustration">
		<a href="{$comment.U_PICTURE}">
		<img src="{$pwg->derivative_url($derivative_params, $comment.src_image)}" alt="{$comment.ALT}">
		</a>
	</div>
	{/if}
	<div class="description"{if isset($comment.IN_EDIT)} style="height:200px"{/if}>
		{if isset($comment.U_DELETE) or isset($comment.U_VALIDATE) or isset($comment.U_EDIT)}
		<div class="actions" style="float:right;font-size:90%">
		{if isset($comment.U_DELETE)}
			<a href="{$comment.U_DELETE}" onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');">
				{'Delete'|@translate}
			</a>{if isset($comment.U_VALIDATE) or isset($comment.U_EDIT)} | {/if}
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

		<span class="author">{$comment.AUTHOR}</span> - <span class="date">{$comment.DATE}</span>
		{if isset($comment.IN_EDIT)}
		<a name="edit_comment"></a>
		<form  method="post" action="{$comment.U_EDIT}" class="filter" id="editComment">
			<fieldset>
				<legend>{'Edit a comment'|@translate}</legend>
				<label>{'Comment'|@translate}<textarea name="content" id="contenteditid" rows="5" cols="80">{$comment.CONTENT|@escape}</textarea></label>
				<input type="hidden" name="key" value="{$comment.KEY}">
				<input type="hidden" name="image_id" value="{$comment.IMAGE_ID|@default:$current.id}">
				<input type="submit" value="{'Submit'|@translate}">
			</fieldset>
		</form>
		{else}
		<blockquote><div>{$comment.CONTENT}</div></blockquote>
		{/if}
	</div>
	</div>
</li>
{/foreach}
</ul>
