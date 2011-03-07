{combine_script id='jquery.effects' load='async' path='themes/default/js/ui/minified/jquery.effects.core.min.js' }
{combine_script id='jquery.effects.blind' load='async' require='jquery.effects' path='themes/default/js/ui/minified/jquery.effects.blind.min.js' }

{footer_script require='jquery.effects.blind'}
jQuery(document).ready(function(){ldelim}
	jQuery("td[id^='desc_'], p[id^='revdesc_']").click(function() {ldelim}
		id = this.id.split('_');
		jQuery("#revdesc_"+id[1]).toggle('blind');
    jQuery(".button_"+id[1]).toggle();
		return false;
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
<div class="pluginBox">
  <table>
    <tr>
      <td class="pluginBoxNameCell">
        {$plugin.EXT_NAME}
      </td>
      <td>
        <a href="{$plugin.URL_UPDATE}" onclick="return confirm('{'Are you sure to install this upgrade? You must verify if this version does not need uninstallation.'|@translate|@escape:javascript}');">{'Install'|@translate}</a>
        | <a href="{$plugin.URL_DOWNLOAD}">{'Download'|@translate}</a>
        | <a class="externalLink" href="{$plugin.EXT_URL}">{'Visit plugin site'|@translate}</a>
      </td>
    </tr>
    <tr>
      <td>
        {'Version'|@translate} {$plugin.CURRENT_VERSION}
      </td>
      <td class="pluginDesc" id="desc_{$plugin.ID}">
        <em>{'Downloads'|@translate}: {$plugin.DOWNLOADS}</em>
        <img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/plus.gif" alt="" class="button_{$plugin.ID}">
        <img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/minus.gif" alt="" class="button_{$plugin.ID}" style="display:none;">
        {'New Version'|@translate} : {$plugin.NEW_VERSION}
        | {'By %s'|@translate|@sprintf:$plugin.AUTHOR}
      </td>
    </tr>
    <tr>
      <td></td>
      <td class="pluginDesc">
        <p id="revdesc_{$plugin.ID}" style="display:none;">{$plugin.REV_DESC|htmlspecialchars|nl2br}</p>
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
