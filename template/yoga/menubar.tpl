{if !empty($blocks) }
<div id="menubar">
	{foreach from=$blocks key=id item=block}
	{if ( not empty($block->template) or not empty($block->raw_content) )}
  {if $id == 'mbCategories'}
    {if isset($U_START_FILTER)}
    <a href="{$U_START_FILTER}" title="{'start_filter_hint'|@translate}" rel="nofollow"><img src="{$ROOT_URL}{$themeconf.icon_dir}/start_filter.png" class="button" alt="start filter"></a>
    {/if}
    {if isset($U_STOP_FILTER)}
    <a href="{$U_STOP_FILTER}" title="{'stop_filter_hint'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/stop_filter.png" class="button" alt="stop filter"></a>
    {/if}
  {/if}
	<dl id="{$id}">
		{if not empty($block->template)}
		{include file=$block->template }
		{else}
		{$block->raw_content|@default}
		{/if}
	</dl>
	{/if}
	{/foreach}
</div>
{/if}