{combine_script id='jquery.ajaxmanager' load='footer' require='jquery' path='themes/default/js/plugins/jquery.ajaxmanager.js' }
{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

{footer_script require='jquery.ajaxmanager'}
/* incompatible message */
var incompatible_msg = '{'WARNING! This plugin does not seem to be compatible with this version of Piwigo.'|@translate|@escape:'javascript'}';
var activate_msg = '\n{'Do you want to activate anyway?'|@translate|@escape:'javascript'}';
var deactivate_all_msg = '{'Deactivate all'|@translate}';

var showInactivePlugins = function() {
    jQuery(".showInactivePlugins").fadeOut(complete=function(){
          jQuery(".plugin-inactive").fadeIn();
        })
  }

/* group action */
const pwg_token = '{$PWG_TOKEN}';
const are_you_sure_msg  = '{'Are you sure?'|@translate|@escape:'javascript'}';
const confirm_msg = '{"Yes, I am sure"|@translate}';
const cancel_msg = "{"No, I have changed my mind"|@translate}";
let delete_plugin_msg = '{'Are you sure you want to delete the plugin "%s"?'|@translate|@escape:'javascript'}';
let restore_plugin_msg = '{'Are you sure you want to restore the plugin "%s"?'|@translate|@escape:'javascript'}';
const restore_tip_msg = "{'Restore default configuration. You will lose your plugin settings!'|@translate}";
{literal}
var queuedManager = jQuery.manageAjax.create('queued', { 
  queue: true,  
  maxRequests: 1
});
var nb_plugins = jQuery('div.active').size();
var done = 0;
/* group action */

jQuery(document).ready(function() {
  $(".delete-plugin-button").each(function() {
    let plugin_name = $(this).closest(".pluginContent").find(".pluginMiniBoxNameCell").html().trim();
    $(this).pwg_jconfirm_follow_href({
      alert_title: delete_plugin_msg.replace('%s', plugin_name),
      alert_confirm: confirm_msg,
      alert_cancel: cancel_msg
    });
  });
  $(".plugin-restore").each(function() {
    let plugin_name = $(this).closest(".pluginContent").find(".pluginMiniBoxNameCell").html().trim();
    $(this).pwg_jconfirm_follow_href({
      alert_title: restore_plugin_msg.replace('%s', plugin_name),
      alert_confirm: confirm_msg,
      alert_cancel: cancel_msg,
      alert_content: restore_tip_msg,
    });
  });
  $(".uninstall-plugin-button").each(function() {
    $(this).pwg_jconfirm_follow_href({
      alert_title: are_you_sure_msg,
      alert_confirm: confirm_msg,
      alert_cancel: cancel_msg
    });
  });
  jQuery('div.deactivate_all a').click(function() {
    $.confirm({
      title: deactivate_all_msg,
      buttons: {
        confirm: {
          text: confirm_msg,
          btnClass: 'btn-red',
          action: function () {
            jQuery('div.active').each(function() {
              performPluginDeactivate(jQuery(this).attr('id'));
            })
          }
        },
        cancel: {
          text: cancel_msg
        }
      },
      ...jConfirm_confirm_options
    });
  });

  function performPluginDeactivate(id) {
    queuedManager.add({
      type: 'GET',
      dataType: 'json',
      url: 'ws.php',
      data: { method: 'pwg.plugins.performAction', action: 'deactivate', plugin: id, pwg_token: pwg_token, format: 'json' },
      success: function(data) {
        if (data['stat'] == 'ok') jQuery("#"+id).removeClass('active').addClass('inactive');
        done++;
        if (done == nb_plugins) location.reload();
      }
    });
  };

  /* incompatible plugins */
  jQuery(document).ready(function() {
    jQuery.ajax({
      method: 'GET',
      url: 'admin.php',
      data: { page: 'plugins_installed', incompatible_plugins: true },
      dataType: 'json',
      success: function(data) {
        for (i=0;i<data.length;i++) {
          {/literal}
          {if $show_details}
            jQuery('#'+data[i]+' .pluginBoxNameCell').prepend('<a class="warning" title="'+incompatible_msg+'"></a>')
          {else}
            jQuery('#'+data[i]+' .pluginMiniBoxNameCell').prepend('<span class="warning" title="'+incompatible_msg+'"></span>')
          {/if}
          {literal}
          jQuery('#'+data[i]).addClass('incompatible');
          jQuery('#'+data[i]+' .activate').each(function () {
            $(this).pwg_jconfirm_follow_href({
              alert_title: incompatible_msg + activate_msg,
              alert_confirm: confirm_msg,
              alert_cancel: cancel_msg
            });
          });
        }
        jQuery('.warning').tipTip({
          'delay' : 0,
          'fadeIn' : 200,
          'fadeOut' : 200,
          'maxWidth':'250px'
        });
      }
    });
  });
  jQuery('.fullInfo').tipTip({
    'delay' : 500,
    'fadeIn' : 200,
    'fadeOut' : 200,
    'maxWidth':'300px',
    'keepAlive':false,
  });

  /* Add the '...' for the overflow of the description line*/
  jQuery( document ).ready(function () {
    jQuery('.pluginDesc').each(function () {
      var el = jQuery(this).context;
      var wordArray = el.innerHTML.split(' ');
      if (el.scrollHeight > el.offsetHeight) {
        jQuery(this).attr('title', jQuery(this).html())
      }
      while(el.scrollHeight > el.offsetHeight) {
          wordArray.pop();
          el.innerHTML = wordArray.join(' ') + '...';
      }
    })
  });

  /*Add the filter research*/
  jQuery( document ).ready(function () {
    document.onkeydown = function(e) {
      if (e.keyCode == 58) {
        jQuery(".pluginFilter input.search-input").focus();
        return false;
      }
    }

    jQuery(".pluginFilter input").on("input", function() {
      let text = jQuery(this).val().toLowerCase();
      var searchNumber = 0;
      jQuery('.pluginBoxes').each(function () {
        let searchNumberInBox = 0;
        let pluginBoxes = jQuery(this);
        pluginBoxes.find(".pluginMiniBox").each(function() {
          if (text == "") {
            jQuery(this).fadeIn()
            searchNumberInBox++;
          } else {
            let name = jQuery(this).find(".pluginMiniBoxNameCell").text().toLowerCase();
            let description = jQuery(this).find(".pluginDesc").text().toLowerCase();
            if (name.search(text) != -1 || description.search(text) != -1){
              jQuery(this).fadeIn()
              searchNumberInBox++;
            } else {
              jQuery(this).fadeOut()
            }
          }
        })
        if (searchNumberInBox == 0) {
          pluginBoxes.fadeOut();
        } else {
          if (pluginBoxes.hasClass("plugin-inactive")) {
            showInactivePlugins()
          } else {
            pluginBoxes.fadeIn();
          }
        }
        searchNumber += searchNumberInBox;
      });
      if (searchNumber == 0) {
          jQuery(".emptyResearch").fadeIn();
        } else {
          jQuery(".emptyResearch").fadeOut();
        }
    });
  });

  /* Show Inactive plugins or button to show them*/
  jQuery( document ).ready(function () {
    jQuery(".showInactivePlugins button").on('click', showInactivePlugins)
  });
});

$(document).mouseup(function (e) {
  e.stopPropagation();
  $(".pluginMiniBox").each(function() {  
    if ($(this).find(".showOptions").has(e.target).length === 0) {
      $(this).find(".PluginOptionsBlock").hide();
    }
  })
});

jQuery(".pluginMiniBox").each(function(index){
  let myplugin = jQuery(this);
  myplugin.find(".showOptions").click(function(){
    myplugin.find(".PluginOptionsBlock").toggle();
  });
})

{/literal}
{/footer_script}

{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}
{combine_script id='tiptip' load='header' path='themes/default/js/plugins/jquery.tipTip.minified.js'}

<div class="titrePage">
  <h2>{'Plugins'|@translate}</h2>
</div>

{if isset($plugins)}

{assign var='field_name' value='null'} {* <!-- 'counter' for fieldset management --> *}
{counter start=0 assign=i} {* <!-- counter for 'deactivate all' link --> *}

<div class="pluginFilter"> 
  <span class="icon-filter search-icon"></span>
  <span class="icon-cancel search-cancel"></span>
  <input class='search-input' type="text" placeholder="{'Filter'|@translate}">
</div>

<div class="emptyResearch"> {'No plugins found'|@translate} </div>

{foreach from=$plugins item=plugin name=plugins_loop}
    
{if $field_name != $plugin.STATE}
  {if $field_name != 'null'}
  </div> {* PluginBoxes Container*}
  </div> {* PluginBoxes*}
      {/if}

  <div class="pluginBoxes plugin-{$plugin.STATE}" {if $plugin.STATE == 'inactive'}{if $count_types_plugins["inactive"]>$max_inactive_before_hide}style="display:none"{/if}{/if}>
  {assign var='field_name' value=$plugin.STATE}

  <div class="pluginBoxesHead">
      <div class="pluginBoxesTitle">
        <p>
        {if $plugin.STATE == 'active'}
          <span class="icon-purple icon-toggle-on"></span>{'Active Plugins'|@translate}
        {elseif $plugin.STATE == 'inactive'}
          <span class="icon-red icon-toggle-off"></span>{'Inactive Plugins'|@translate}
        {elseif $plugin.STATE == 'missing'}
          <span class="icon-green icon-toggle-off"></span>{'Missing Plugins'|@translate}
        {elseif $plugin.STATE == 'merged'}
          <span class="icon-yellow icon-toggle-off"></span>{'Obsolete Plugins'|@translate}
        {/if}
        </p>
        <div class="pluginBoxesCount">{$count_types_plugins[$plugin.STATE]}</div>
      </div>

      {if $plugin.STATE == 'active'}
        <div class="deactivate_all"><a>{'Deactivate all'|@translate}</a></div>
      {/if}
    </div>

  <div class="pluginBoxesContainer">
{/if}
  
  {if not empty($plugin.AUTHOR)}
    {if not empty($plugin.AUTHOR_URL)}
      {assign var='author' value="<a href='%s'>%s</a>"|@sprintf:$plugin.AUTHOR_URL:$plugin.AUTHOR}
    {else}
      {assign var='author' value='<u>'|cat:$plugin.AUTHOR|cat:'</u>'}
    {/if}
  {/if}

  {if not empty($plugin.VISIT_URL)}
    {assign var='version' value="<a class='externalLink' href='"|cat:$plugin.VISIT_URL|cat:"'>"|cat:$plugin.VERSION|cat:"</a>"}
  {else}
    {assign var='version' value=$plugin.VERSION}
  {/if}
              
  <div id="{$plugin.ID}" class="pluginMiniBox {$plugin.STATE}">
    <div class="pluginContent">
      <div class="PluginOptionsIcons">
        {if $plugin.STATE == 'active' || $plugin.STATE == 'inactive'}
          <a class="icon-ellipsis-vert showOptions showInfo" ></a>
        {/if}
      </div>
      
      <div class="PluginOptionsBlock dropdown">
        <div class="dropdown-option-content"> {if !empty($author)}{'By %s'|@translate:$author}<br>{/if}{'Version'|@translate} {$version}</div>
        {if $plugin.STATE == 'active'}
          <a class="dropdown-option icon-cancel-circled" href="{$plugin.U_ACTION}&amp;action=deactivate">{'Deactivate'|@translate}</a>
          <a class="dropdown-option icon-back-in-time plugin-restore" href="{$plugin.U_ACTION}&amp;action=restore">{'Restore'|@translate}</a>   
        {elseif $plugin.STATE == 'inactive'}
          <a class="dropdown-option icon-trash delete-plugin-button" href="{$plugin.U_ACTION}&amp;action=delete">{'Delete'|@translate}</a>
        {/if}      
      </div>
      <div class="pluginMiniBoxNameCell">
        {$plugin.NAME}
      </div>
      <div class="pluginDesc">
        {$plugin.DESC}
      </div>
      <div class="pluginActions">
        {if $plugin.STATE == 'active'}
          {if $plugin.SETTINGS_URL != ''}
            <a href="{$plugin.SETTINGS_URL}" class="pluginActionLevel1 icon-cog">{'Settings'|@translate}</a>
          {else}
            <div class="pluginUnavailableAction icon-cog tiptip" title="{'N/A'|translate}">{'Settings'|@translate}</div>
          {/if}
        {elseif $plugin.STATE == 'inactive'}
          <a class="pluginActionLevel1 icon-plus" href="{$plugin.U_ACTION}&amp;action=activate" class="activate">{'Activate'|@translate}</a>
        {elseif $plugin.STATE == 'missing'}
          <a class="pluginActionLevel3 uninstall-plugin-button" href="{$plugin.U_ACTION}&amp;action=uninstall">{'Uninstall'|@translate}</a>
        {elseif $plugin.STATE == 'merged'}
          <a class="pluginActionLevel3" href="{$plugin.U_ACTION}&amp;action=delete">{'Delete'|@translate}</a>
        {/if}                     
      </div>
    </div>
    
  </div> {*<!-- pluginMiniBox -->*}
    

    
  {/foreach}
  </div> {* PluginBoxes Container*}
  </div> {* PluginBoxes*}

  <div class="showInactivePlugins" {if $count_types_plugins["inactive"]<=$max_inactive_before_hide}style="display:none"{/if} >
      <div class="showInactivePluginsInfo">
        {assign var='badge_inactive' value='<span class="pluginBoxesCount">%s</span>'|@sprintf:$count_types_plugins["inactive"]}
        <div>{'You have %s inactive plugins'|translate:$badge_inactive}</div>
      </div>
      <button class="buttonLike" id="showInactivePluginsAction">{'Show inactive plugins'|@translate}</button>
  </div>

{/if}
