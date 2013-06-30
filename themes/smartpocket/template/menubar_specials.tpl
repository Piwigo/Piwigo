<h3>{'Specials'|@translate}</h3>
<ul data-role="listview">
		{foreach $block->data as $key=>$link}
    {if in_array($key, array("favorites","most_visited","best_rated","recent_pics","recent_cats","random")) }
		<li><a href="{$link.URL}" title="{$link.TITLE}"{if isset($link.REL)} {$link.REL}{/if}>{$link.NAME}</a></li>
    {/if}
		{/foreach}
</ul>
