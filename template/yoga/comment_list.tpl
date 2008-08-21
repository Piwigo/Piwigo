{* $Id$ *}
{foreach from=$comments item=comment}
	<div class="comment" >
	  {if isset($comment.TN_SRC)}
	  	<a class="illustration" href="{$comment.U_PICTURE}"><img src="{$comment.TN_SRC}" alt="{$comment.ALT}" /></a>
	  {/if}
		<div class="commentHeader">
			{if isset($comment.U_DELETE) or isset($comment.U_VALIDATE) }
			<ul class="actions" style="float:right">
				{if isset($comment.U_DELETE)}
				  <li>
					<a href="{$comment.U_DELETE}" title="{'comments_del'|@translate}">
						<img src="{$ROOT_URL}{$themeconf.icon_dir}/delete.png" class="button" alt="[{'delete'|@translate}]"/>
					</a>
					</li>{/if}

				{if isset($comment.U_VALIDATE)}
					<li>
					<a href="{$comment.U_VALIDATE}" title="validate this comment">
						<img src="{$ROOT_URL}{$themeconf.icon_dir}/validate_s.png" class="button" alt="[validate]"/>
					</a>
					</li>{/if}
			</ul>
			{/if}
			<span class="author">{$comment.AUTHOR}</span> - <span class="date">{$comment.DATE}</span>
		</div>

		<blockquote>{$comment.CONTENT}</blockquote>
	</div>
	<hr/>
{/foreach}

