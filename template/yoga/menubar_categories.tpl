<dt>
	<a href="{$block->data.U_CATEGORIES}">{'Categories'|@translate}</a>
</dt>
<dd>
	{$block->data.MENU_CATEGORIES_CONTENT}
	{if isset($block->data.U_UPLOAD)}
	<ul>
		<li>
			<a href="{$block->data.U_UPLOAD}">{'upload_picture'|@translate}</a>
		</li>
	</ul>
	{/if}
	<p class="totalImages">{$pwg->l10n_dec('%d element', '%d elements', $block->data.NB_PICTURE)}</p>
</dd>

