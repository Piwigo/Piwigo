{* $Id$ *}
{if isset($tabsheet) and count($tabsheet)}
<ul class="tabsheet">
{foreach from=$tabsheet item=tab}
  <li class="{if isset($tab.selected) and $tab.selected}selected_tab{else}normal_tab{/if}"><a href="{$tab.url}">{$tab.caption}</a></li>
{/foreach}
</ul>
{/if}