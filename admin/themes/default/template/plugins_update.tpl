{combine_script id='jquery.ui' load='async' require='jquery' path='themes/default/js/jquery.ui.min.js' }
{combine_script id='jquery.cluetip' load='async' require='jquery' path='themes/default/js/plugins/jquery.cluetip.packed.js'}
{footer_script require='jquery.cluetip'}
jQuery().ready(function(){ldelim}
	jQuery('.cluetip').cluetip({ldelim}
		width: 300,
		splitTitle: '|'
	});
});
{/footer_script}

<div class="titrePage">
  <h2>{'Plugins'|@translate}</h2>
</div>

{if not empty($plugins)}
<div id="availablePlugins">
<fieldset>
<legend>{'Plugins which need upgrade'|@translate}</legend>
{foreach from=$plugins item=plugin name=plugins_loop}
<div class="pluginBox" id="plugin_{$plugin.ID}">
  <table>
    <tr>
      <td class="pluginBoxNameCell">
        <a href="{$plugin.EXT_URL}" class="externalLink cluetip" title="{$plugin.EXT_NAME}|{$plugin.EXT_DESC|htmlspecialchars|nl2br}">{$plugin.EXT_NAME}</a>
      </td>
      <td>
        <a href="{$plugin.URL_UPDATE}" onclick="return confirm('{'Are you sure to install this upgrade? You must verify if this version does not need uninstallation.'|@translate|@escape:javascript}');">{'Automatic upgrade'|@translate}</a>
        | <a href="{$plugin.URL_DOWNLOAD}">{'Download file'|@translate}</a>
      </td>
    </tr>
    <tr>
      <td>
        <a href="{$plugin.EXT_URL}" class="externalLink cluetip" title="{'Version'|@translate} {$plugin.VERSION}|{$plugin.REV_DESC|htmlspecialchars|nl2br}"> {'Version'|@translate} {$plugin.VERSION}</a>
      </td>
      <td>
        <em>{'Downloads'|@translate}: {$plugin.DOWNLOADS}</em>
        {'By %s'|@translate|@sprintf:$plugin.AUTHOR}
      </td>
    </tr>
  </table>
</div>
{/foreach}
</fieldset>
</div>
{elseif not isset($SERVER_ERROR)}
<p>{'All plugins are up to date.'|@translate}</p>
{/if}
