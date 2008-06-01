<div class="titrePage">
{$TABSHEET}
  <h2>{'Plugins'|@translate}</h2>
</div>

{if isset($plugins_not_uptodate)}
<br>
<b>{'plugins_need_update'|@translate}</b>
<table class="table2">
<thead>
  <tr class="throw">
    <td>{'Name'|@translate}</td>
    <td>{'plugins_actual_version'|@translate}</td>
    <td>{'plugins_new_version'|@translate}</td>
    <td>{'Actions'|@translate}</td>
  </tr>
</thead>
{foreach from=$plugins_not_uptodate item=plugin name=plugins_loop}
  <tr class="{if $smarty.foreach.plugins_loop.index is odd}row1{else}row2{/if}">
    <td><a href="{$plugin.EXT_URL}" onclick="window.open(this.href); return false;" class="tooltip">{$plugin.EXT_NAME}
        <span>{$plugin.EXT_DESC}</span></a></td>
    <td style="text-align:center;">{$plugin.VERSION}</td>
    <td style="text-align:center;"><a href="{$plugin.VERSION_URL}" onclick="window.open(this.href); return false;" class="tooltip">{$plugin.NEW_VERSION}
        <span>{$plugin.NEW_VER_DESC}</span></a></td>
    <td style="text-align:center;"><a href="{$plugin.URL_UPDATE}" onclick="return confirm('{'plugins_confirm_upgrade'|@translate|@escape:javascript}');">{'plugins_auto_update'|@translate}</a>
      / <a href="{$plugin.URL_DOWNLOAD}">{'plugins_download'|@translate}</a></td>
  </tr>
{/foreach}
</table>
{/if}


{if isset($plugins_uptodate)}
<br>
<b>{'plugins_dontneed_update'|@translate}</b>
<table class="table2">
<thead>
  <tr class="throw">
    <td>{'Name'|@translate}</td>
    <td>{'Version'|@translate}</td>
  </tr>
</thead>
{foreach from=$plugins_uptodate item=plugin name=plugins_loop}
  <tr class="{if $smarty.foreach.plugins_loop.index is odd}row1{else}row2{/if}">
    <td><a href="{$plugin.URL}" onclick="window.open(this.href); return false;" class="tooltip">{$plugin.NAME}
        <span>{$plugin.EXT_DESC}</span></a></td>
    <td style="text-align:center;">{$plugin.VERSION}</td>
  </tr>
{/foreach}
</table>
{/if}


{if isset($plugins_cant_check)}
<br>
<b>{'plugins_cant_check'|@translate}</b>
<table class="table2">
<thead>
  <tr class="throw">
    <td>{'Name'|@translate}</td>
    <td>{'Version'|@translate}</td>
  </tr>
</thead>
{foreach from=$plugins_cant_check item=plugin name=plugins_loop}
  <tr class="{if $smarty.foreach.plugins_loop.index is odd}row1{else}row2{/if}">
    <td>{$plugin.NAME}</td>
    <td style="text-align:center;">{$plugin.VERSION}</td>
  </tr>
{/foreach}
</table>
{/if}
