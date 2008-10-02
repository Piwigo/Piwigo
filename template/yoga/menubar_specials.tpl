<dt>{'special_categories'|@translate}</dt>
<dd>
	<ul>
		{foreach from=$block->data item=link}
		<li><a href="{$link.URL}" title="{$link.TITLE}" {if isset($link.REL)}{$link.REL}{/if}>{$link.NAME}</a></li>
		{/foreach}
	</ul>
</dd>

