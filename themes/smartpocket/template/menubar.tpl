<ul data-role="listview">
  <li data-icon="delete"><a href="#menubar" data-rel="close">{'Close'|@translate}</a></li>
</ul>
{if !empty($blocks) }
	{foreach from=$blocks key=id item=block}
		<div data-role="collapsible" data-inset="false" data-icon="false">
		{if not empty($block->template)}
		{include file=$block->template assign=the_block|@get_extent:$id}
    {$the_block|replace:'dt':'h3'|replace:'<dd>':''|replace:'</dd>':''}
		{else}
		{$block->raw_content|replace:'dt':'h3'|replace:'<dd>':''|replace:'</dd>':''}
		{/if}
    </div>
	{/foreach}
{/if}
<br>
<ul data-role="listview">
  <li data-role="list-divider">{'View in'|@translate}</li>
  <li><a href="{$TOGGLE_MOBILE_THEME_URL}">{'Desktop'|@translate}</a></li>
</ul>
