{known_script id="jquery.ui" src=$ROOT_URL|@cat:"themes/default/js/ui/packed/ui.core.packed.js" }
{known_script id="jquery.ui.effects" src=$ROOT_URL|@cat:"themes/default/js/ui/packed/effects.core.packed.js" }
{known_script id="jquery.ui.blind" src=$ROOT_URL|@cat:"themes/default/js/ui/packed/effects.blind.packed.js" }

<script type="text/javascript">
jQuery().ready(function(){ldelim}
  jQuery("td[id^='desc_']").click(function() {ldelim}
    id = this.id.split('_');
    if ($(this).hasClass('bigdesc')) {ldelim}
      $("#bigdesc_"+id[1]).toggle('blind', 1);
      $(this).removeClass('bigdesc');
    } else {ldelim}
      $("#bigdesc_"+id[1]).toggle('blind', 50);
      $(this).addClass('bigdesc');
    }
    $("#smalldesc_"+id[1]).toggle('blind', 1);
    return false;
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

<fieldset>
<legend></legend>
{foreach from=$plugins item=plugin name=plugins_loop}
<div class="pluginBox" id="plugin_{$plugin.ID}"}>
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