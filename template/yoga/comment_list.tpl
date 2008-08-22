{* $Id$ *}
{foreach from=$comments item=comment}
	<div class="thumbnailCategory">
	{if isset($comment.TN_SRC)}
		<div class="illustration">
			<a href="{$comment.U_PICTURE}">
				<img src="{$comment.TN_SRC}" alt="{$comment.ALT}" />
			</a>
		</div>
	{/if}
		<div class="description">
			{if isset($comment.U_DELETE) or isset($comment.U_VALIDATE) }
			<ul class="actions" style="float:right">
				{if isset($comment.U_DELETE)}
				<li>
					<a href="{$comment.U_DELETE}" title="{'comments_del'|@translate}">
						<img src="{$ROOT_URL}{$themeconf.icon_dir}/delete.png" class="button" alt="[delete]" />
					</a>
				</li>
				{/if}
				{if isset($comment.U_VALIDATE)}
				<li>
					<a href="{$comment.U_VALIDATE}" title="validate this comment">
						<img src="{$ROOT_URL}{$themeconf.icon_dir}/validate_s.png" class="button" alt="[validate]" />
					</a>
				</li>
				{/if}
			</ul>
			{/if}
			<span class="author">{$comment.AUTHOR}</span> - <span class="date">{$comment.DATE}</span>
			<blockquote>{$comment.CONTENT}</blockquote>
		</div>
	</div>
	{if isset($comment_separator)}
	<hr/>
	{/if}
{/foreach}
