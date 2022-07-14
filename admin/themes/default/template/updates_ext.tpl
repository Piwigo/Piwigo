{combine_script id='jquery.ajaxmanager' load='footer' require='jquery' path='themes/default/js/plugins/jquery.ajaxmanager.js'}
{combine_script id='jquery.jgrowl' load='footer' require='jquery' path='themes/default/js/plugins/jquery.jgrowl_minimized.js'}
{combine_css path="themes/default/js/plugins/jquery.jgrowl.css"}

{footer_script require='jquery.ui.effect-blind,jquery.ajaxmanager,jquery.jgrowl'}
var pwg_token = '{$PWG_TOKEN}';
var extType = '{$EXT_TYPE}';
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
  jQuery('.updateExtension').each( function() {
    if (jQuery(this).parents('div').css('display') == 'block')
      jQuery(this).click();
  });
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
    data: { method: 'pwg.extensions.ignoreUpdate', reset: true, type: extType, pwg_token: pwg_token, format: 'json' },
    success: function(data) {
      if (data['stat'] == 'ok') {
        jQuery(".pluginBox, fieldset").show();
        jQuery(".pluginBox").attr('data-ignored', 'false')
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
    jQuery("fieldset[data-type="+types[i]+"] .pluginBox").each(function(index) {
      if (jQuery(this).attr('data-ignored')== 'true')
        ignored++;
      else
        nbExtensions++;
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
        jQuery("#"+type+"_"+id).attr('data-ignored', 'true')
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

checkFieldsets();
{/literal}
{/footer_script}
{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}
{footer_script}

const are_you_sure_msg  = '{'Are you sure?'|@translate|@escape:'javascript'}';
const confirm_msg = '{"Yes, I am sure"|@translate}';
const cancel_msg = "{"No, I have changed my mind"|@translate}";
$("#update_all").click(function() {
  const title_msg = "{'Are you sure you want to update all extensions?'|@translate}";
  $.confirm({
      title: title_msg,
      buttons: {
        confirm: {
          text: confirm_msg,
          btnClass: 'btn-red',
          action: function () {
            updateAll();
          }
        },
        cancel: {
          text: cancel_msg
        }
      },
      ...jConfirm_confirm_options
    });
})
{/footer_script}

{if $isWebmaster == 1}

<div class="autoupdate_bar">
<div class="head-button-1 icon-ok-circled" id="update_all">{'Update All'|@translate}</div>
<div class="head-button-2 icon-block" id="ignore_all" onClick="ignoreAll(); return false;">{'Ignore All'|@translate}</div>
<div class="head-button-2 icon-ccw" id="reset_ignore" onClick="resetIgnored(); return false;" {if !$SHOW_RESET}style="display:none;"{/if}>{'Reset ignored updates'|@translate}</div>
</div>
<div class="autoupdate_bar" style="display:none;">
{'Please wait...'|@translate}<br><img src="admin/themes/default/images/ajax-loader-bar.gif">
</div>

<p id="up_to_date" style="display:none; text-align:left; margin-left:20px;">{'All %s are up to date.'|@sprintf:$EXT_TYPE|@translate}</p>

{foreach from=$UPDATES_EXTENSION key=type item=updates}
  {if not empty($updates)}
    <fieldset id="{$type}" class="pluginContainer pluginUpdateContainer line-form" data-type="{$type}">
    <legend>
    {if $type=='plugins'}
      <span class="icon-puzzle icon-green"></span>{'Plugins'|@translate}
    {elseif $type=='themes'}
      <span class="icon-brush icon-blue"></span>{'Themes'|@translate}
    {elseif $type=='languages'}
      <span class="icon-language icon-purple"></span>{'Languages'|@translate}
    {/if}
    </legend>
    
    {foreach from=$updates item=extension}
      <div class="pluginBox pluginMiniBox" id="{$type}_{$extension.EXT_ID}" {if $extension.IGNORED}data-ignored="true" style="display:none;"{/if}>
        <div class="pluginContent">
          <div class="pluginName">
            {$extension.EXT_NAME}
          </div>
          <div class="pluginDesc" id="desc_{$extension.ID}">
            <span class="plugin-version plugin-version-old icon-flow-branch" title="{"Current Version"|@translate}">{$extension.CURRENT_VERSION}</span> <i class="icon-right"></i> <span class="plugin-version icon-flow-branch" title="{"New Version"|@translate}">{$extension.NEW_VERSION}</span>
            <div class="plugin-revision-info"><span>{$extension.REV_DESC}</span></div>
            <a href='{$extension.EXT_URL}' target="_blank" class="plugin-update-link icon-info-circled-1">{'More information'|@translate}</a>
          </div>
          <div class="pluginActions">
            <a href="#" onClick="updateExtension('{$type}', '{$extension.EXT_ID}', {$extension.REVISION_ID});" class="updateExtension pluginActionLevel1"> <i class="icon-ok-circled"></i> {'Install'|@translate}</a>
            <a href="{$extension.URL_DOWNLOAD}" class="pluginActionLevel2"> <i class="icon-download"></i> {'Download'|@translate}</a>
            <a href="#" onClick="ignoreExtension('{$type}', '{$extension.EXT_ID}'); return false;" class="ignoreExtension pluginActionLevel2"><i class="icon-block"></i>{'Ignore this update'|@translate}</a>
          </div>
        </div>
      </div>
    {/foreach}
    </fieldset>
  {/if}
{/foreach}
{/if}