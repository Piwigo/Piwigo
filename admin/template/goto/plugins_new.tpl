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
<span class="sort">
{'Sort order'|@translate} : 
  <select onchange="document.location = this.options[this.selectedIndex].value;">
        {html_options options=$order_options selected=$order_selected}
  </select>
</span>
  <h2>{'Plugins'|@translate}</h2>
</div>

{if isset($plugins)}
<br>
<table class="table2 plugins">
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
    <td><a href="{$plugin.EXT_URL}" onclick="window.open(this.href); return false;" class="cluetip" title="{$plugin.EXT_NAME}|{$plugin.EXT_DESC|htmlspecialchars|nl2br}">{$plugin.EXT_NAME}</a></td>
    <td style="text-align:center;"><a href="{$plugin.EXT_URL}" onclick="window.open(this.href); return false;" class="cluetip" title="{$plugin.EXT_NAME}|{$plugin.VER_DESC|htmlspecialchars|nl2br}">{$plugin.VERSION}</a></td>
    <td>{$plugin.DATE}</td>
    <td>{$plugin.AUTHOR}</td>
    <td style="text-align:center;"><a href="{$plugin.URL_INSTALL}" onclick="return confirm('{'Are you sure you want to install this plugin?'|@translate|@escape:javascript}');">{'Automatic installation'|@translate}</a>
      / <a href="{$plugin.URL_DOWNLOAD}">{'Download file'|@translate}</a>
    </td>
  </tr>
{/foreach}
</table>
{/if}
