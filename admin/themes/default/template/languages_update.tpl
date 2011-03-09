{footer_script require='jquery.effects.blind'}{literal}
jQuery(document).ready(function(){
	jQuery("td[id^='desc_'], p[id^='revdesc_']").click(function() {
		id = this.id.split('_');
		jQuery("#revdesc_"+id[1]).toggle('blind');
    jQuery(".button_"+id[1]).toggle();
		return false;
	});
});
{/literal}{/footer_script}

<div class="titrePage">
  <h2>{'Languages'|@translate}</h2>
</div>

{if not empty($update_languages)}
<div id="availablePlugins">
<fieldset>
<legend>{'Languages which need upgrade'|@translate}</legend>
{foreach from=$update_languages item=language name=languages_loop}
<div class="pluginBox">
  <table>
    <tr>
      <td class="pluginBoxNameCell">
        {$language.EXT_NAME}
      </td>
      <td>
        <a href="{$language.URL_UPDATE}" onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');">{'Install'|@translate}</a>
        | <a href="{$language.URL_DOWNLOAD}">{'Download'|@translate}</a>
        | <a class="externalLink" href="{$language.EXT_URL}">{'Visit language site'|@translate}</a>
      </td>
    </tr>
    <tr>
      <td>
        {'Version'|@translate} {$language.CURRENT_VERSION}
      </td>
      <td class="pluginDesc" id="desc_{$language.ID}">
        <em>{'Downloads'|@translate}: {$language.DOWNLOADS}</em>
        <img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/plus.gif" alt="" class="button_{$language.ID}">
        <img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/minus.gif" alt="" class="button_{$language.ID}" style="display:none;">
        {'New Version'|@translate} : {$language.NEW_VERSION}
        | {'By %s'|@translate|@sprintf:$language.AUTHOR}
      </td>
    </tr>
    <tr>
      <td></td>
      <td class="pluginDesc">
        <p id="revdesc_{$language.ID}" style="display:none;">{$language.REV_DESC|htmlspecialchars|nl2br}</p>
      </td>
    </tr>
  </table>
</div>
{/foreach}
</fieldset>
</div>
{elseif not isset($SERVER_ERROR)}
<p>{'All languages are up to date.'|@translate}</p>
{/if}
