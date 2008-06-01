<div class="titrePage">
{$TABSHEET}
  <h2>{'Plugins'|@translate}</h2>
</div>

{'Sort order'|@translate} : 
  <select onchange="document.location = this.options[this.selectedIndex].value;" style="width:150px">
        {html_options options=$order_options selected=$order_selected}
  </select>

{if isset($plugins)}
<br>
<table class="table2">
<thead>
  <tr class="throw">
    <td>{'Name'|@translate}</td>
    <td>{'Version'|@translate}</td>
    <td>{'Date'|@translate}</td>
    <td>{'Author'|@translate}</td>
    <td>{'Actions'|@translate}</td>
  </tr>
</thead>
{foreach from=$plugins item=plugin name=plugins_loop}
  <tr class="{if $smarty.foreach.plugins_loop.index is odd}row1{else}row2{/if}">
    <td><a href="{$plugin.EXT_URL}" onclick="window.open(this.href); return false;" class="tooltip">{$plugin.EXT_NAME}
      <span>{$plugin.EXT_DESC}</span></a></td>
    <td style="text-align:center;"><a href="{$plugin.VERSION_URL}" onclick="window.open(this.href); return false;" class="tooltip">{$plugin.VERSION}
      <span>{$plugin.VER_DESC}</span></a></td>
    <td>{$plugin.DATE}</td>
    <td>{$plugin.AUTHOR}</td>
    <td style="text-align:center;"><a href="{$plugin.URL_INSTALL}" onclick="return confirm('{'plugins_confirm_install'|@translate|@escape:javascript}');">{'plugins_auto_install'|@translate}</a>
      / <a href="{$plugin.URL_DOWNLOAD}">{'plugins_download'|@translate}</a>
    </td>
  </tr>
{/foreach}
</table>
{/if}
