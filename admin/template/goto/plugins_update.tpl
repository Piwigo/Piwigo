{known_script id="jquery" src=$ROOT_URL|@cat:"template-common/lib/jquery.packed.js"}
{known_script id="jquery.cluetip" src=$ROOT_URL|@cat:"template-common/lib/plugins/jquery.cluetip.packed.js"}

<script type="text/javascript">
jQuery().ready(function(){ldelim}
  jQuery('.cluetip').cluetip({ldelim}
    width: 300,
    splitTitle: '|'
  });
});
</script>

<div class="titrePage">
  <h2>{'Plugins'|@translate}</h2>
</div>

{if isset($plugins_not_uptodate)}
<br>
<b>{'Plugins which need upgrade'|@translate}</b>
<table class="table2 plugins">
<thead>
  <tr class="throw">
    <td>{'Name'|@translate}</td>
    <td>{'Current<br>version'|@translate}</td>
    <td>{'Available<br>version'|@translate}</td>
    <td>{'Actions'|@translate}</td>
  </tr>
</thead>
{foreach from=$plugins_not_uptodate item=plugin name=plugins_loop}
  <tr class="{if $smarty.foreach.plugins_loop.index is odd}row1{else}row2{/if}">
    <td><a href="{$plugin.EXT_URL}" onclick="window.open(this.href); return false;" class="cluetip" title="{$plugin.EXT_NAME}|{$plugin.EXT_DESC|htmlspecialchars|nl2br}">{$plugin.EXT_NAME}</a></td>
    <td style="text-align:center;">{$plugin.VERSION}</td>
    <td style="text-align:center;"><a href="{$plugin.EXT_URL}" onclick="window.open(this.href); return false;" class="cluetip" title="{$plugin.EXT_NAME}|{$plugin.NEW_VER_DESC|htmlspecialchars|nl2br}">{$plugin.NEW_VERSION}</a></td>
    <td style="text-align:center;"><a href="{$plugin.URL_UPDATE}" onclick="return confirm('{'Are you sure to install this upgrade? You must verify if this version does not need uninstallation.'|@translate|@escape:javascript}');">{'Automatic upgrade'|@translate}</a>
      / <a href="{$plugin.URL_DOWNLOAD}">{'Download file'|@translate}</a></td>
  </tr>
{/foreach}
</table>
{/if}


{if isset($plugins_uptodate)}
<br>
<b>{'Plugins up to date'|@translate}</b>
<table class="table2 plugins">
<thead>
  <tr class="throw">
    <td>{'Name'|@translate}</td>
    <td>{'Version'|@translate}</td>
  </tr>
</thead>
{foreach from=$plugins_uptodate item=plugin name=plugins_loop}
  <tr class="{if $smarty.foreach.plugins_loop.index is odd}row1{else}row2{/if}">
    <td><a href="{$plugin.URL}" onclick="window.open(this.href); return false;" class="cluetip" title="{$plugin.NAME}|{$plugin.EXT_DESC|htmlspecialchars|nl2br}">{$plugin.NAME}</a></td>
    <td style="text-align:center;"><a href="{$plugin.URL}" onclick="window.open(this.href); return false;" class="cluetip" title="{$plugin.NAME}|{$plugin.VER_DESC|htmlspecialchars|nl2br}">{$plugin.VERSION}</a></td>
  </tr>
{/foreach}
</table>
{/if}


{if isset($plugins_cant_check)}
<br>
<b>{'Plugin versions can\'t be checked'|@translate}</b>
<table class="table2 plugins">
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
