<h3>{'Menu'|@translate}</h3>
<ul data-role="listview">
  {foreach from=$block->data item=link}
		{if is_array($link)}
			<li><a href="{$link.URL}" title="{$link.TITLE}"{if isset($link.REL)} {$link.REL}{/if}>{$link.NAME}</a>{if isset($link.COUNTER)}<span class="ui-li-count">{$link.COUNTER}</span>{/if}</li>
		{/if}
	{/foreach}
</ul>