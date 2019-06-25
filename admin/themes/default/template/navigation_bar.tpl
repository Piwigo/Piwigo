<div class="navigationBar">
  {if isset($navbar.URL_FIRST)}
  <a href="{$navbar.URL_FIRST}" rel="first">{'First'|@translate}</a>
  <a href="{$navbar.URL_PREV}" rel="prev">{'Previous'|@translate}</a>
  {else}
  <span>{'First'|@translate}</span>
  <span>{'Previous'|@translate}</span>
  {/if}
  {assign var='prev_page' value=0}
  {foreach from=$navbar.pages key=page item=url}
    {if $page > $prev_page+1}<span class="navigationBarSeparator">...</span>{/if}
    {if $page == $navbar.CURRENT_PAGE}
    <span class="pageNumberSelected">{$page}</span>
    {else}
    <a href="{$url}">{$page}</a>
    {/if}
    {assign var='prev_page' value=$page}
  {/foreach}
  {if isset($navbar.URL_NEXT)}
  <a href="{$navbar.URL_NEXT}" rel="next">{'Next'|@translate}</a>
  <a href="{$navbar.URL_LAST}" rel="last">{'Last'|@translate}</a>
  {else}
  <span>{'Next'|@translate}</span>
  <span>{'Last'|@translate}</span>
  {/if}
</div>
