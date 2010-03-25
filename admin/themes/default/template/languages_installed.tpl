<div class="titrePage">
  <h2>{'Languages'|@translate}</h2>
</div>

<table class="table2 languages">
<thead>
  <tr class="throw">
    <td>{'Language'|@translate}</td>
    <td>{'Actions'|@translate}</td>
  </tr>
</thead>

{foreach from=$languages item=language name=languages_loop}
  <tr class="{if $smarty.foreach.languages_loop.index is odd}row1{else}row2{/if}">
    <td class="{$language.STATE}" style="text-align: left;">
      {$language.NAME}
      {if $language.IS_DEFAULT}<i>({'Default'|@translate})</i>{/if}
    </td>
    <td>
    {if !$language.IS_DEFAULT}
      {if $language.STATE == 'active' or $language.STATE == 'missing'}
        <a href="{$language.U_ACTION}&amp;action=deactivate">{'Deactivate'|@translate}</a> |
        <a href="{$language.U_ACTION}&amp;action=set_default">{'Default'|@translate}</a>
      {else}
        <a href="{$language.U_ACTION}&amp;action=activate">{'Activate'|@translate}</a> |
        <a href="{$language.U_ACTION}&amp;action=delete" onclick="return confirm('{'Are you sure?'|@translate|@escape:'javascript'}');">{'Delete'|@translate}</a>
      {/if}
    {else}
      ---
    {/if}
    </td>
  </tr>
{/foreach}
</table>