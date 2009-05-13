{* $Id: /piwigo/trunk/plugins/add_index/admin/main_page.tpl 6509 2008-10-05T21:07:41.181634Z rub  $ *}

<div class="titrePage">
  <h2>{'Add_Index'|@translate}</h2>
</div>

{if isset($add_index_results)}
<div>
  <ul>
    {foreach from=$add_index_results item=result}
    <li>{$result}</li>
    {/foreach}
  </ul>
</div>
{/if}
