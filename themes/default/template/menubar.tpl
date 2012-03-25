{if !empty($blocks) }
<div id="menubar">
	{foreach from=$blocks key=id item=block}
	<dl id="{$id}">
		{if not empty($block->template)}
		{include file=$block->template|@get_extent:$id }
		{else}
		{$block->raw_content}
		{/if}
	</dl>
	{/foreach}
</div><div id="menuSwitcher"></div>
{/if}