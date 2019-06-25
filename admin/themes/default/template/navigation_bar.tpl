<div class="navigationBar">
  {if isset($navbar.URL_FIRST)}
  <span class="navFirstLast"><a href="{$navbar.URL_FIRST}" rel="first">{'First'|@translate}</a> |</span>
  <span class="navPrevNext"><a href="{$navbar.URL_PREV}" rel="prev">{'Previous'|@translate}</a> |</span>
  {else}
  <span class="navFirstLast">{'First'|@translate} |</span>
  <span class="navPrevNext">{'Previous'|@translate} |</span>
  {/if}

  {assign var='prev_page' value=0}
  {foreach from=$navbar.pages key=page item=url}
    {if $page > $prev_page+1}...{/if}
    {if $page == $navbar.CURRENT_PAGE}
    <span class="pageNumberSelected">{$page}</span>
    {else}
    <a href="{$url}">{$page}</a>
    {/if}
    {assign var='prev_page' value=$page}
  {/foreach}

  {if isset($navbar.URL_NEXT)}
  <span class="navPrevNext">| <a href="{$navbar.URL_NEXT}" rel="next">{'Next'|@translate}</a></span>
  <span class="navFirstLast">| <a href="{$navbar.URL_LAST}" rel="last">{'Last'|@translate}</a></span>
  {else}
  <span class="navPrevNext">| {'Next'|@translate}</span>
  <span class="navFirstLast">| {'Last'|@translate}</span>
  {/if}
</div>
