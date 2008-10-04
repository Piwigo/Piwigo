<dt>
  {if isset($U_START_FILTER)}
  <a href="{$U_START_FILTER}" title="{'start_filter_hint'|@translate}" rel="nofollow"><img src="{$ROOT_URL}{$themeconf.icon_dir}/start_filter.png" class="button" alt="start filter"></a>
  {/if}
  {if isset($U_STOP_FILTER)}
  <a href="{$U_STOP_FILTER}" title="{'stop_filter_hint'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/stop_filter.png" class="button" alt="stop filter"></a>
  {/if}
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

