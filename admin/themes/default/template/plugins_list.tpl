<div class="titrePage">
<span class="sort">
{'Sort order'|@translate} : 
  <select onchange="document.location = this.options[this.selectedIndex].value;" style="width:200px">
        {html_options options=$order_options selected=$order_selected}
  </select>
</span>
  <h2>{'Plugins'|@translate}</h2>
</div>


{if isset($plugins)}
<table class="table2 plugins">
<thead>
  <tr class="throw">
    <td>{'Name'|@translate}</td>
    <td>{'Actions'|@translate}</td>
    <td>{'Version'|@translate}</td>
    <td>{'Description'|@translate}</td>
  </tr>
</thead>

{foreach from=$plugins item=plugin name=plugins_loop}
  <tr class="{if $smarty.foreach.plugins_loop.index is odd}row1{else}row2{/if}">
  <td class="pluginState{if $plugin.STATE != 'uninstalled'} {$plugin.STATE}{/if}">
    {$plugin.NAME}
  </td>
  <td>
    <ul class="pluginsActions">
    {if $plugin.STATE == 'active'}
      <li>
        <a href="{$plugin.U_ACTION}&amp;action=deactivate">
          <img src="{$themeconf.admin_icon_dir}/plug_deactivate.png" alt="{'Deactivate'|@translate}" title="{'Deactivate'|@translate}">
        </a>
      </li>
      <li>
          <img src="{$themeconf.admin_icon_dir}/plug_uninstall_grey.png" alt="{'Uninstall'|@translate}" title="{'Uninstall'|@translate}">
      </li>
    {/if}
    {if $plugin.STATE == 'inactive'}
      <li>
        <a href="{$plugin.U_ACTION}&amp;action=activate">
          <img src="{$themeconf.admin_icon_dir}/plug_activate.png" alt="{'Activate'|@translate}" title="{'Activate'|@translate}">
        </a>
      </li>
      <li>
        <a href="{$plugin.U_ACTION}&amp;action=uninstall" onclick="return confirm('{'Are you sure?'|@translate|@escape:'javascript'}');">
          <img src="{$themeconf.admin_icon_dir}/plug_uninstall.png" alt="{'Uninstall'|@translate}" title="{'Uninstall'|@translate}">
        </a>
      </li>
    {/if}
    {if $plugin.STATE == 'missing'}
      <li>
          <img src="{$themeconf.admin_icon_dir}/plug_deactivate_grey.png" alt="{'Deactivate'|@translate}" title="{'Deactivate'|@translate}">
      </li>
      <li>
        <a href="{$plugin.U_ACTION}&amp;action=uninstall" onclick="return confirm('{'Are you sure?'|@translate|@escape:'javascript'}');">
          <img src="{$themeconf.admin_icon_dir}/plug_uninstall.png" alt="{'Uninstall'|@translate}" title="{'Uninstall'|@translate}">
        </a>
      </li>
    {/if}
    {if $plugin.STATE == 'uninstalled'}
      <li>
          <img src="{$themeconf.admin_icon_dir}/plug_activate_grey.png" alt="{'Activate'|@translate}" title="{'Activate'|@translate}">
      </li>
      <li>
        <a href="{$plugin.U_ACTION}&amp;action=install" onclick="return confirm('{'Are you sure?'|@translate|@escape:'javascript'}');">
          <img src="{$themeconf.admin_icon_dir}/plug_install.png" alt="{'Install'|@translate}" title="{'Install'|@translate}">
        </a>
      </li>
      <li>
        <a href="{$plugin.U_ACTION}&amp;action=delete" onclick="return confirm('{'plugins_confirm_delete'|@translate|@escape:'javascript'}');">
          <img src="{$themeconf.admin_icon_dir}/plug_delete.png" alt="{'Delete'|@translate}" title="{'Delete'|@translate}">
        </a>
      </li>
    {else}
      <li>
        <img src="{$themeconf.admin_icon_dir}/plug_delete_grey.png" alt="{'Delete'|@translate}" title="{'Delete'|@translate}">
      </li>
    {/if}
    </ul>
  </td>
  <td style="text-align:center;">{$plugin.VERSION}</td>
  <td>{$plugin.DESCRIPTION}</td>
  </tr>
{/foreach}
</table>
{/if}
