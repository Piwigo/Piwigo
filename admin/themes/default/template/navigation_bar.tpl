<div class="pagination-container">
  {if isset($navbar.URL_FIRST)}
  <a href="{$navbar.URL_PREV}" class='pagination-arrow left' rel="prev">
    <span class="icon-left-open"></span>
  </a>
  {else}
  <a class='pagination-arrow left unavailable'>
    <span class="icon-left-open"></span>
  </a>
  {/if}

  {assign var='prev_page' value=0}
  <div class="pagination-item-container">
  {foreach from=$navbar.pages key=page item=url}
    {if $page > $prev_page+1}<span>...</span>{/if}
    {if $page == $navbar.CURRENT_PAGE}
    <a class="actual">{$page}</a>
    {else}
    <a href="{$url}">{$page}</a>
    {/if}
    {assign var='prev_page' value=$page}
  {/foreach}
  </div>

  {if isset($navbar.URL_NEXT)}
  <a href="{$navbar.URL_NEXT}" class='pagination-arrow rigth' rel="next">
    <span class="icon-left-open"></span>
  </a>
  {else}
  <a class='pagination-arrow rigth unavailable'>
    <span class="icon-left-open"></span>
  </a>
  {/if}
</div>
