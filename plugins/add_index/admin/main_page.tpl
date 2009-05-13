
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
