<h3>{'Albums'|@translate}</h3>
<ul data-role="listview">
{foreach from=$block->data.MENU_CATEGORIES item=cat}
  <li><a href="{$cat.URL}" {if $cat.IS_UPPERCAT}rel="up"{/if} title="{$cat.TITLE}">{$cat.NAME}</a>
  {if $cat.count_images > 0}<span class="ui-li-count">{$cat.count_images}</span>{/if}
  </li>
{/foreach}
</ul>
