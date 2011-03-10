{combine_script id='jquery.sort' path='themes/default/js/plugins/jquery.sort.js'}

{footer_script require='jquery.effects.blind,jquery.sort'}{literal}
var sortOrder = 'date';
var sortPlugins = (function(a, b) {
  if (sortOrder == 'downloads' || sortOrder == 'revision' || sortOrder == 'date')
    return parseInt($(a).find('input[name="'+sortOrder+'"]').val())
      < parseInt($(b).find('input[name="'+sortOrder+'"]').val()) ? 1 : -1;
  else
    return $(a).find('input[name="'+sortOrder+'"]').val().toLowerCase()
      > $(b).find('input[name="'+sortOrder+'"]').val().toLowerCase()  ? 1 : -1;
});

jQuery(document).ready(function(){
	jQuery("td[id^='desc_']").click(function() {
		id = this.id.split('_');
		nb_lines = jQuery("#bigdesc_"+id[1]).html().split('<br>').length;

		jQuery("#smalldesc_"+id[1]).toggle('blind', 1);
		if (jQuery(this).hasClass('bigdesc')) {
			jQuery("#bigdesc_"+id[1]).toggle('blind', 1);
		} else {
			jQuery("#bigdesc_"+id[1]).toggle('blind', 50 + (nb_lines * 30));
		}
		jQuery(this).toggleClass('bigdesc');
		return false;
	});

  jQuery('select[name="selectOrder"]').change(function() {
    sortOrder = this.value;
    $('.pluginBox').sortElements(sortPlugins);
  });
});
{/literal}{/footer_script}

<div class="titrePage">
<span class="sort">
{'Sort order'|@translate} : 
{html_options name="selectOrder" options=$order_options selected=$order_selected}
</span>
  <h2>{'Plugins'|@translate}</h2>
</div>

{if not empty($plugins)}
<div id="availablePlugins">
<fieldset>
<legend></legend>
{foreach from=$plugins item=plugin name=plugins_loop}
<div class="pluginBox" id="plugin_{$plugin.ID}">
<input type="hidden" name="date" value="{$plugin.ID}">
<input type="hidden" name="name" value="{$plugin.EXT_NAME}">
<input type="hidden" name="revision" value="{$plugin.REVISION_DATE}">
<input type="hidden" name="downloads" value="{$plugin.DOWNLOADS}">
<input type="hidden" name="author" value="{$plugin.AUTHOR}">
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