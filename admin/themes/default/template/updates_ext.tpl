{combine_script id='jquery.ajaxmanager' load='footer' require='jquery' path='themes/default/js/plugins/jquery.ajaxmanager.js'}
{combine_script id='jquery.jgrowl' load='footer' require='jquery' path='themes/default/js/plugins/jquery.jgrowl_minimized.js'}
{combine_css path="admin/themes/default/uploadify.jGrowl.css"}

{footer_script require='jquery.effects.blind,jquery.ajaxmanager,jquery.jgrowl'}
var pwg_token = '{$PWG_TOKEN}';
var extList = new Array();
var confirmMsg  = '{'Are you sure?'|@translate|@escape:'javascript'}';
var errorHead   = '{'ERROR'|@translate|@escape:'javascript'}';
var successHead = '{'Update Complete'|@translate|@escape:'javascript'}';
var errorMsg    = '{'an error happened'|@translate|@escape:'javascript'}';
var restoreMsg  = '{'Reset ignored updates'|@translate|@escape:'javascript'}';

{literal}
var todo = 0;
var queuedManager = $.manageAjax.create('queued', { 
	queue: true,  
	maxRequests: 1,
  beforeSend: function() { autoupdate_bar_toggle(1); },
  complete: function() { autoupdate_bar_toggle(-1); }
});

function updateAll() {
  if (confirm(confirmMsg)) {
    jQuery('.updateExtension').each( function() {
      if (jQuery(this).parents('div').css('display') == 'block')
        jQuery(this).click();
    });
  }
};

function ignoreAll() {
  jQuery('.ignoreExtension').each( function() {
    if (jQuery(this).parents('div').css('display') == 'block')
      jQuery(this).click();
  });
};

function resetIgnored() {
  jQuery.ajax({
    type: 'GET',
    url: 'ws.php',
    dataType: 'json',
    data: { method: 'pwg.extensions.ignoreUpdate', reset: true, pwg_token: pwg_token, format: 'json' },
    success: function(data) {
      if (data['stat'] == 'ok') {
        jQuery(".pluginBox, fieldset").show();
        jQuery("#update_all").show();
        jQuery("#ignore_all").show();
        jQuery("#up_to_date").hide();
        jQuery("#reset_ignore").hide();
        jQuery("#ignored").hide();
        checkFieldsets();
      }
    }
  });
};

function checkFieldsets() {
  var types = new Array('plugins', 'themes', 'languages');
  var total = 0;
  var ignored = 0;
  for (i=0;i<3;i++) {
    nbExtensions = 0;
    jQuery("div[id^='"+types[i]+"_']").each(function(index) {
      if (jQuery(this).css('display') == 'block')
        nbExtensions++;
      else
        ignored++;
    });
    total = total + nbExtensions;
    if (nbExtensions == 0)
      jQuery("#"+types[i]).hide();
  }

  if (total == 0) {
    jQuery("#update_all").hide();
    jQuery("#ignore_all").hide();
    jQuery("#up_to_date").show();
  }
  if (ignored > 0) {
    jQuery("#reset_ignore").val(restoreMsg + ' (' + ignored + ')');
  }
};

function updateExtension(type, id, revision) {
  queuedManager.add({
    type: 'GET',
    dataType: 'json',
    url: 'ws.php',
    data: { method: 'pwg.extensions.update', type: type, id: id, revision: revision, pwg_token: pwg_token, format: 'json' },
    success: function(data) {
      if (data['stat'] == 'ok') {
        jQuery.jGrowl( data['result'], { theme: 'success', header: successHead, life: 4000, sticky: false });
        jQuery("#"+type+"_"+id).remove();
        checkFieldsets();
      } else {
        jQuery.jGrowl( data['result'], { theme: 'error', header: errorHead, sticky: true });
      }
    },
    error: function(data) {
      jQuery.jGrowl( errorMsg, { theme: 'error', header: errorHead, sticky: true });
    }
  });
};

function ignoreExtension(type, id) {
  queuedManager.add({
    type: 'GET',
    url: 'ws.php',
    dataType: 'json',
    data: { method: 'pwg.extensions.ignoreUpdate', type: type, id: id, pwg_token: pwg_token, format: 'json' },
    success: function(data) {
      if (data['stat'] == 'ok') {
        jQuery("#"+type+"_"+id).hide();
        jQuery("#reset_ignore").show();
        checkFieldsets();
      }
    }
  });
};

function autoupdate_bar_toggle(i) {
  todo = todo + i;
  if ((i == 1 && todo == 1) || (i == -1 && todo == 0))
    jQuery('.autoupdate_bar').toggle();
}

jQuery(document).ready(function() {
	jQuery("td[id^='desc_'], p[id^='revdesc_']").click(function() {
		id = this.id.split('_');
		jQuery("#revdesc_"+id[1]).toggle('blind');
    jQuery(".button_"+id[1]).toggle();
		return false;
	});
});

checkFieldsets();
{/literal}
{/footer_script}

<div class="titrePage">
  <h2>{'Updates'|@translate}</h2>
</div>

<div class="autoupdate_bar">
<br>
<input type="submit" id="update_all" value="{'Update All'|@translate}" onClick="updateAll(); return false;">
<input type="submit" id="ignore_all" value="{'Ignore All'|@translate}" onClick="ignoreAll(); return false;">
<input type="submit" id="reset_ignore" value="{'Reset ignored updates'|@translate}" onClick="resetIgnored(); return false;" {if !$SHOW_RESET}style="display:none;"{/if}>
</div>
<div class="autoupdate_bar" style="display:none;">
{'Please wait...'|@translate}<br><img src="admin/themes/default/images/ajax-loader-bar.gif">
</div>

<p id="up_to_date" style="display:none; text-align:left; margin-left:20px;">{'All extensions are up to date.'|@translate}</p>

{if not empty($update_plugins)}
<div>
<fieldset id="plugins">
<legend>{'Plugins'|@translate}</legend>
{foreach from=$update_plugins item=plugin name=plugins_loop}
<div class="pluginBox" id="plugins_{$plugin.EXT_ID}" {if $plugin.IGNORED}style="display:none;"{/if}>
  <table>
    <tr>
      <td class="pluginBoxNameCell">
        {$plugin.EXT_NAME}
      </td>
      <td>
        <a href="#" onClick="updateExtension('plugins', '{$plugin.EXT_ID}', {$plugin.REVISION_ID});" class="updateExtension">{'Install'|@translate}</a>
        | <a href="{$plugin.URL_DOWNLOAD}">{'Download'|@translate}</a>
        | <a href="#" onClick="ignoreExtension('plugins', '{$plugin.EXT_ID}'); return false;" class="ignoreExtension">{'Ignore this update'|@translate}</a>
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
{/if}

{if not empty($update_themes)}
<div>
<fieldset id="themes">
<legend>{'Themes'|@translate}</legend>
{foreach from=$update_themes item=theme name=themes_loop}
<div class="pluginBox" id="themes_{$theme.EXT_ID}" {if $theme.IGNORED}style="display:none;"{/if}>
  <table>
    <tr>
      <td class="pluginBoxNameCell">
        {$theme.EXT_NAME}
      </td>
      <td>
        <a href="#" onClick="updateExtension('themes', '{$theme.EXT_ID}', {$theme.REVISION_ID});" class="updateExtension">{'Install'|@translate}</a>
        | <a href="{$theme.URL_DOWNLOAD}">{'Download'|@translate}</a>
        | <a href="#" onClick="ignoreExtension('themes', '{$theme.EXT_ID}'); return false;" class="ignoreExtension">{'Ignore this update'|@translate}</a>
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
{/if}

{if not empty($update_languages)}
<div>
<fieldset id="languages">
<legend>{'Languages'|@translate}</legend>
{foreach from=$update_languages item=language name=languages_loop}
<div class="pluginBox" id="languages_{$language.EXT_ID}" {if $language.IGNORED}style="display:none;"{/if}>
  <table>
    <tr>
      <td class="pluginBoxNameCell">
        {$language.EXT_NAME}
      </td>
      <td>
        <a href="#" onClick="updateExtension('languages', '{$language.EXT_ID}', {$language.REVISION_ID});" class="updateExtension">{'Install'|@translate}</a>
        | <a href="{$language.URL_DOWNLOAD}">{'Download'|@translate}</a>
        | <a href="#" onClick="ignoreExtension('languages', '{$language.EXT_ID}'); return false;" class="ignoreExtension">{'Ignore this update'|@translate}</a>
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
{/if}
