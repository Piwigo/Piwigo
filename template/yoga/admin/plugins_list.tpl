<div class="titrePage">
{$TABSHEET}
  <h2>{'Plugins'|@translate}</h2>
</div>

{'Sort order'|@translate} : 
  <select onchange="document.location = this.options[this.selectedIndex].value;" style="width:100px">
        {html_options options=$order_options selected=$order_selected}
  </select>

{if isset($plugins)}
<table class="table2">
<thead>
  <tr class="throw">
    <td>{'Name'|@translate}</td>
    <td>{'Version'|@translate}</td>
    <td>{'Description'|@translate}</td>
    <td>{'Actions'|@translate}</td>
  </tr>
</thead>
{html_head} {*add the style to html head for strict standard compliance*}
<style type="text/css">
TABLE.table2 TR TD.pluginState {ldelim}
  padding-left:16px;
}
TABLE.table2 TR TD.active {ldelim}
  background: url({$ROOT_URL}{$themeconf.admin_icon_dir}/plugin_active.gif) no-repeat center left
}
TABLE.table2 TR TD.inactive {ldelim}
  background: url({$ROOT_URL}{$themeconf.admin_icon_dir}/plugin_inactive.gif) no-repeat center left
}
</style>
{/html_head}
{foreach from=$plugins item=plugin name=plugins_loop}
	<tr class="{if $smarty.foreach.plugins_loop.index is odd}row1{else}row2{/if}">
	<td class="pluginState{if not empty($plugin.STATE)} {$plugin.STATE}{/if}">
		{$plugin.NAME}
	</td>
	<td>{$plugin.VERSION}</td>
	<td>{$plugin.DESCRIPTION}</td>
	<td>
	{foreach from=$plugin.actions item=action}
		<a href="{$action.U_ACTION}"
		{if isset($action.CONFIRM)} onclick="return confirm('{$action.CONFIRM|@escape:'javascript'}');"{/if}
		{$TAG_INPUT_ENABLED}>{$action.L_ACTION}</a>
	{/foreach}
	</td>
  </tr>
{/foreach}
</table>
{/if}
