{* $Id$ *}
{if isset($tabsheet) and count($tabsheet)}
<ul class="tabsheet">
{foreach from=$tabsheet key=name item=sheet}
  <li class="{if ($name == $tabsheet_selected)}selected_tab{else}normal_tab{/if} png">
    <a href="{$sheet.url}"><span class="png">{$sheet.caption}</span></a>
  </li>
{/foreach}
</ul>
{/if}