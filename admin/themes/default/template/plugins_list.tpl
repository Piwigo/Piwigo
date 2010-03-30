<div class="titrePage">
  <h2>{'Plugins'|@translate}</h2>
</div>

{if isset($plugins)}

{foreach from=$plugin_states item=plugin_state}
<fieldset>
  <legend>
  {if $plugin_state == 'active'}
  Active Plugins

  {elseif $plugin_state == 'inactive'}
  Inactive Plugins

  {elseif $plugin_state == 'uninstalled'}
  Uninstalled Plugins

  {elseif $plugin_state == 'missing'}
  Missing Plugins

  {/if}
  </legend>
  {foreach from=$plugins item=plugin name=plugins_loop}
    {if $plugin.STATE == $plugin_state}
  <div class="pluginBox">
    <table>
      <tr>
        <td class="pluginBoxNameCell">{$plugin.NAME}</td>
        <td>{$plugin.DESC}</td>
      </tr>
      <tr>
        <td>
    {if $plugin.STATE == 'active'}
          <a href="{$plugin.U_ACTION}&amp;action=deactivate">{'Deactivate'|@translate}</a>

    {elseif $plugin_state == 'inactive'}
          <a href="{$plugin.U_ACTION}&amp;action=activate">{'Activate'|@translate}</a>
          | <a href="{$plugin.U_ACTION}&amp;action=uninstall" onclick="return confirm('{'Are you sure?'|@translate|@escape:'javascript'}');">{'Uninstall'|@translate}</a>

    {elseif $plugin_state == 'uninstalled'}
          <a href="{$plugin.U_ACTION}&amp;action=install">{'Install'|@translate}</a>
          | <a href="{$plugin.U_ACTION}&amp;action=delete" onclick="return confirm('{'Are you sure you want to delete this plugin?'|@translate|@escape:'javascript'}');">{'Delete'|@translate}</a>

    {elseif $plugin_state == 'missing'}
          <a href="{$plugin.U_ACTION}&amp;action=uninstall" onclick="return confirm('{'Are you sure?'|@translate|@escape:'javascript'}');">{'Uninstall'|@translate}</a>

    {/if}
        </td>
        <td>
          Version {$plugin.VERSION}
    {if not empty($plugin.AUTHOR)}
          | By 
      {if not empty($plugin.AUTHOR_URL)}
          <a href="{$plugin.AUTHOR_URL}">{$plugin.AUTHOR}</a>
      {else}
          {$plugin.AUTHOR}
      {/if}
    {/if}

    {if not empty($plugin.VISIT_URL)}
          | <a class="externalLink" href="{$plugin.VISIT_URL}">Visit plugin site</a>
    {/if}
        </td>
      </tr>
    </table>
  </div>
    {/if}
  {/foreach}
</fieldset>
{/foreach}

{/if}
