{combine_script id='jquery.ui' load='async' require='jquery' path='themes/default/js/jquery.ui.min.js' }
{combine_script id='jquery.ui.effects' load='async' require='jquery.ui' path='themes/default/js/ui/minified/jquery.effects.core.min.js' }
{combine_script id='jquery.ui.effects.blind' load='async' require='jquery.ui.effects' path='themes/default/js/ui/minified/jquery.effects.blind.min.js' }

{footer_script require='jquery.ui.effects.blind'}
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
  <h2>{'Themes'|@translate}</h2>
</div>

{if not empty($update_themes)}
<div id="availablePlugins">
<fieldset>
<legend>{'Themes which need upgrade'|@translate}</legend>
{foreach from=$update_themes item=theme name=themes_loop}
<div class="pluginBox">
  <table>
    <tr>
      <td class="pluginBoxNameCell">
        {$theme.EXT_NAME}
      </td>
      <td>
        <a href="{$theme.URL_UPDATE}" onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');">{'Install'|@translate}</a>
        | <a href="{$theme.URL_DOWNLOAD}">{'Download'|@translate}</a>
        | <a class="externalLink" href="{$theme.EXT_URL}">{'Visit theme site'|@translate}</a>
      </td>
    </tr>
    <tr>
      <td>
        {'Version'|@translate} {$theme.CURRENT_VERSION}
      </td>
      <td class="pluginDesc" id="desc_{$theme.ID}">
        <em>{'Downloads'|@translate}: {$theme.DOWNLOADS}</em>
        <img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/plus.gif" alt="" class="button_{$theme.ID}">
        <img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/minus.gif" alt="" class="button_{$theme.ID}" style="display:none;">
        {'New Version'|@translate} : {$theme.NEW_VERSION}
        | {'By %s'|@translate|@sprintf:$theme.AUTHOR}
      </td>
    </tr>
    <tr>
      <td></td>
      <td class="pluginDesc">
        <p id="revdesc_{$theme.ID}" style="display:none;">{$theme.REV_DESC|htmlspecialchars|nl2br}</p>
      </td>
    </tr>
  </table>
</div>
{/foreach}
</fieldset>
</div>
{elseif not isset($SERVER_ERROR)}
<p>{'All themes are up to date.'|@translate}</p>
{/if}
