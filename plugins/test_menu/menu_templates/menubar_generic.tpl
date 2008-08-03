
<!-- generic menu bar -->
<dt>{$section.NAME|@translate}</dt>
<dd>
  <ul>
    {foreach from=$section.ITEMS item=item}
      <li><a href="{$item.URL}" title="{$item.TITLE}">{$item.NAME}</a></li>
    {/foreach}
  </ul>
</dd>
