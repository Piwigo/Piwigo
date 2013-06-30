<h3>{'Links'|@translate}</h3>
<ul data-role="listview">{strip}
		{foreach from=$block->data item=link}
			<li>
				<a href="{$link.URL}" class="external"{if isset($link.new_window)} onclick="window.open(this.href, '{$link.new_window.NAME}','{$link.new_window.FEATURES}'); return false;"{/if}>
				{$link.LABEL}
				</a>
			</li>
		{/foreach}
	{/strip}
</ul>
