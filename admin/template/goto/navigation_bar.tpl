{* $Id: navigation_bar.tpl 3145 2009-02-14 02:24:10Z patdenice $ *}
<div class="navigationBar">

{if isset($navbar.URL_FIRST)}
<a href="{$navbar.URL_FIRST}" rel="first">{'first_page'|@translate}</a>
{else}
{'first_page'|@translate}
{/if}
|
{if isset($navbar.URL_PREV)}
<a href="{$navbar.URL_PREV}" rel="prev">{'previous_page'|@translate}</a>
{else}
{'previous_page'|@translate}
{/if}
|
{assign var='prev_page' value=0}
{foreach from=$navbar.pages key=page item=url}
  {if $page > $prev_page+1} ... {/if}
  {if $page == $navbar.CURRENT_PAGE}
    <span class="pageNumberSelected">{$page}</span>
  {else}
    <a href="{$url}">{$page}</a>
  {/if}
{assign var='prev_page' value=$page}
{/foreach}
|
{if isset($navbar.URL_NEXT)}
<a href="{$navbar.URL_NEXT}" rel="next">{'next_page'|@translate}</a>
{else}
{'next_page'|@translate}
{/if}
|
{if isset($navbar.URL_LAST)}
<a href="{$navbar.URL_LAST}" rel="last">{'last_page'|@translate}</a>
{else}
{'last_page'|@translate}
{/if}

</div>