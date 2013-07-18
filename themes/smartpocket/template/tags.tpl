{include file='infos_errors.tpl'}
<div data-role="content">{if isset($tags)}
<ul data-role="listview" data-inset="true" data-filter="true">
	<li data-role="list-divider">{'Tags'|@translate}</li>
  {foreach from=$tags item=tag}
	<li><a href="{$tag.URL}" title="{$tag.counter|@translate_dec:'%d photo':'%d photos'}">{$tag.name}</a><span class="ui-li-count">{$tag.counter|@translate_dec:'%d photo':'%d photos'}</span></li>
	{/foreach}
</ul>
{elseif isset($letters)}
<ul data-role="listview" data-inset="true">
	{foreach from=$letters item=letter}{foreach from=$letter.tags item=tag}
	<li><a href="{$tag.URL}" title="{$tag.counter|@translate_dec:'%d photo':'%d photos'}">{$tag.name}</a><span class="ui-li-count">{$tag.counter|@translate_dec:'%d photo':'%d photos'}</span></li>
	{/foreach}{/foreach}
</ul>{/if}
</div>
