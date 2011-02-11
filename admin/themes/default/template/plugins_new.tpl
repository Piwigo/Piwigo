{combine_script id='jquery.ui' load='async' require='jquery' path='themes/default/js/jquery.ui.min.js' }
{combine_script id='jquery.ui.effects' load='async' require='jquery.ui' path='themes/default/js/ui/minified/jquery.effects.core.min.js' }
{combine_script id='jquery.ui.effects.blind' load='async' require='jquery.ui.effects' path='themes/default/js/ui/minified/jquery.effects.blind.min.js' }

{footer_script require='jquery.ui.effects.blind'}
jQuery(document).ready(function(){ldelim}
	jQuery("td[id^='desc_']").click(function() {ldelim}
		id = this.id.split('_');
		nb_lines = jQuery("#bigdesc_"+id[1]).html().split('<br>').length;

		jQuery("#smalldesc_"+id[1]).toggle('blind', 1);
		if (jQuery(this).hasClass('bigdesc')) {ldelim}
			jQuery("#bigdesc_"+id[1]).toggle('blind', 1);
		} else {ldelim}
			jQuery("#bigdesc_"+id[1]).toggle('blind', 50 + (nb_lines * 30));
		}
		jQuery(this).toggleClass('bigdesc');
		return false;
	});
});
{/footer_script}

<div class="titrePage">
<span class="sort">
{'Sort order'|@translate} : 
  <select onchange="document.location = this.options[this.selectedIndex].value;">
        {html_options options=$order_options selected=$order_selected}
  </select>
</span>
  <h2>{'Plugins'|@translate}</h2>
</div>

{if not empty($plugins)}
<div id="availablePlugins">
<fieldset>
<legend></legend>
{foreach from=$plugins item=plugin name=plugins_loop}
<div class="pluginBox" id="plugin_{$plugin.ID}">
  <table>
    <tr>
      <td class="pluginBoxNameCell">{$plugin.EXT_NAME}</td>
{if $plugin.BIG_DESC != $plugin.SMALL_DESC}
      <td id="desc_{$plugin.ID}" class="pluginDesc">
        <span id="smalldesc_{$plugin.ID}">
          <img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/plus.gif" alt="">{$plugin.SMALL_DESC}...
        </span>
        <span id="bigdesc_{$plugin.ID}" style="display:none;">
          <img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/minus.gif" alt="">{$plugin.BIG_DESC|@nl2br}<br>&nbsp;
        </span>
      </td>
{else}
      <td>{$plugin.BIG_DESC|@nl2br}</td>
{/if}
    </tr>
    <tr>
      <td>
        <a href="{$plugin.URL_INSTALL}" onclick="return confirm('{'Are you sure you want to install this plugin?'|@translate|@escape:javascript}');">{'Install'|@translate}</a>
        |  <a href="{$plugin.URL_DOWNLOAD}">{'Download'|@translate}</a>
      </td>
      <td>
        <em>{'Downloads'|@translate}: {$plugin.DOWNLOADS}</em>
        {'Version'|@translate} {$plugin.VERSION}
        | {'By %s'|@translate|@sprintf:$plugin.AUTHOR}
        | <a class="externalLink" href="{$plugin.EXT_URL}">{'Visit plugin site'|@translate}</a>
      </td>
    </tr>
  </table>
</div>
{/foreach}
</fieldset>
</div>
{else}
<p>{'There is no other plugin available.'|@translate}</p>
{/if}