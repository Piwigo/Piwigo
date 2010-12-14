<div class="titrePage"><h2>{'Extend for templates'|@translate}</h2>
</div>
{if isset($extents)}
<h4>{'Replacement of original templates by customized templates from template-extension subfolder'|@translate}</h4>
<form method="post" name="extend_for_templates" id="extend_for_templates" action="">
  <table class="table2">
    <tr class="throw">
      <th>{'Replacers (customized templates)'|@translate}</th>
      <th>{'Original templates'|@translate}</th>
      <th>{'Optional URL keyword'|@translate}</th>
      <th>{'Bound Theme'|@translate}</th>
    </tr>
    {foreach from=$extents item=tpl name=extent_loop}
    <tr class="{if $smarty.foreach.extent_loop.index is odd}row1{else}row2{/if}">
      <td>
        <input type="hidden" name="reptpl[]" value="{$tpl.replacer}">
        {$tpl.replacer}
      </td>
      <td>
        {html_options name=original[] output=$tpl.original_tpl values=$tpl.original_tpl selected=$tpl.selected_tpl}
      </td>
      <td>
        {html_options name=url[] output=$tpl.url_parameter values=$tpl.url_parameter selected=$tpl.selected_url}
      </td>
      <td>
        {html_options name=bound[] output=$tpl.bound_tpl values=$tpl.bound_tpl selected=$tpl.selected_bound}
      </td>
    </tr>
    {/foreach}
  </table>
  <p>
    <input class="submit" type="submit" value="{'Submit'|@translate}" name="submit">
  </p>
</form>
{/if}
