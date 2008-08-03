
<!-- special menu bar -->
<dt>{$section.NAME|@translate}</dt>
<dd>
  <ul>
    {foreach from=$section.ITEMS item=cat}
    <li><a href="{$cat.URL}" title="{$cat.TITLE}" {if isset($cat.REL)}{$cat.REL}{/if}>{$cat.NAME}</a></li>
    {/foreach}
  </ul>
</dd>
