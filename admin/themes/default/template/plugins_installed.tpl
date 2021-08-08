{combine_script id='jquery.ajaxmanager' load='footer' require='jquery' path='themes/default/js/plugins/jquery.ajaxmanager.js' }
{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='jquery.cookie' path='themes/default/js/jquery.cookie.js' load='footer'}

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
let deleted_plugin_msg = '{'Plugin "%s" deleted!'|@translate|@escape:'javascript'}';
let restore_plugin_msg = '{'Are you sure you want to restore the plugin "%s"?'|@translate|@escape:'javascript'}';
const restore_tip_msg = "{'Restore default configuration. You will lose your plugin settings!'|@translate}";
const plugin_added_str = '{'Activated'|@translate}';
const plugin_deactivated_str = '{'Deactivated'|@translate}';
const plugin_restored_str = '{'Restored'|@translate}';
const plugin_action_error = '{'an error happened'|@translate}';
{literal}
var queuedManager = jQuery.manageAjax.create('queued', { 
  queue: true,  
  maxRequests: 1
});
var nb_plugins = jQuery('div.active').size();
var done = 0;
/* group action */

jQuery(document).ready(function() {
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
      
        $(".pluginMiniBox").each(function() {
          if (text == "") {
            jQuery(this).fadeIn();
            searchNumber++
          } else {
            let name = jQuery(this).find(".pluginMiniBoxNameCell").text().toLowerCase();
            let description = jQuery(this).find(".pluginDesc").text().toLowerCase();
            if (name.search(text) != -1 || description.search(text) != -1){
              searchNumber++;

              if ($("#seeAll").is(":checked")) {
                jQuery(this).fadeIn();
              }
              if ($("#seeActive").is(":checked") && jQuery(this).hasClass("plugin-active")) {
                jQuery(this).fadeIn();
              }
              if ($("#seeInactive").is(":checked") && jQuery(this).hasClass("plugin-inactive")) {
                
                jQuery(this).fadeIn();
              }
              if ($("#seeOther").is(":checked") && (jQuery(this).hasClass("plugin-merged") || jQuery(this).hasClass("plugin-missing"))) {
                jQuery(this).fadeIn();
              }

            } else {
              jQuery(this).fadeOut();
            }
          }
        })

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

{combine_script id='pluginInstallated' load='footer' path='admin/themes/default/js/plugins_installated.js'}

<div class="titrePage">
  <h2>{'Plugins'|@translate}</h2>
</div>

{if isset($plugins)}

{assign var='field_name' value='null'} {* <!-- 'counter' for fieldset management --> *}
{counter start=0 assign=i} {* <!-- counter for 'deactivate all' link --> *}

<div class="pluginTypeFilter">
  <input type="radio" name="p-filter" class="filter" id="seeAll" checked><label for="seeAll">All</label><input type="radio" name="p-filter" class="filter" id="seeActive"><label class="filterLabel" for="seeActive">Active</label><input type="radio" name="p-filter" class="filter" id="seeInactive"><label class="filterLabel" for="seeInactive">Inactive</label><input type="radio" name="p-filter" class="filter" id="seeOther"><label class="filterLabel" for="seeOther">Other</label>
</div>

<div class="pluginFilter"> 
  <span class="icon-search search-icon"></span>
  <span class="icon-cancel search-cancel"></span>
  <input class='search-input' type="text" placeholder="{'Search'|@translate}">
</div>

<div class="AlbumViewSelector">
    <input type="radio" name="layout" class="switchLayout" id="displayClassic" {if $smarty.cookies.pwg_plugin_manager_view == 'classic' || !$smarty.cookies.pwg_plugin_manager_view}checked{/if}/><label for="displayClassic"><span class="icon-pause firstIcon tiptip" title="{'Classic View'|translate}"></span></label><input type="radio" name="layout" class="switchLayout" id="displayLine" {if $smarty.cookies.pwg_plugin_manager_view == 'line'}checked{/if}/><label for="displayLine"><span class="icon-th-list tiptip" title="{'Line View'|translate}"></span></label><input type="radio" name="layout" class="switchLayout" id="displayCompact" {if $smarty.cookies.pwg_plugin_manager_view == 'compact'}checked{/if}/><label for="displayCompact"><span class="icon-th-large lastIcon tiptip" title="{'Compact View'|translate}"></span></label>
</div>  

<div class="emptyResearch"> {'No plugins found'|@translate} </div>

    <div class="pluginContainer {if $smarty.cookies.pwg_plugin_manager_view == 'classic'} classic {elseif $smarty.cookies.pwg_plugin_manager_view == 'line'} line {elseif $smarty.cookies.pwg_plugin_manager_view == 'compact'} compact {else} {/if}">

{foreach from=$plugins item=plugin name=plugins_loop}

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

  <div id="{$plugin.ID}" class="pluginMiniBox {$plugin.STATE} plugin-{$plugin.STATE}">

    <div class="AddPluginSuccess pluginNotif">
      <label class="icon-ok">
        <span>{'Plugin activated'|@translate}</span>
      </label>
    </div>

    <div class="DeactivatePluginSuccess pluginNotif">
      <label class="icon-ok">
        <span>{'Plugin deactivated'|@translate}</span>
      </label>
    </div>

    <div class="RestorePluginSuccess pluginNotif">
      <label class="icon-ok">
        <span>{'Plugin deactivated'|@translate}</span>
      </label>
    </div>

    <div class="PluginActionError pluginNotif">
      <label class="icon-cancel">
        <span>{'Plugin deactivated'|@translate}</span>
      </label>
    </div>

    <div class="pluginContent">
      <div class="PluginOptionsIcons">
        {if $plugin.STATE == 'active' || $plugin.STATE == 'inactive'}
          <a class="icon-ellipsis-v showOptions showInfo" ></a>
        {/if}
      </div>

    <label class="switch">
      <input type="checkbox" id="toggleSelectionMode" {if {$plugin.STATE} === "active"}checked{/if}>
      <span class="slider round"></span>
    </label>

      <div class="pluginActionsSmallIcons">
        {if $plugin.STATE == 'active'}
          {if $plugin.SETTINGS_URL != ''}
            <div class="tiptip" title="{'Settings'|@translate}"> 
              <a href="{$plugin.SETTINGS_URL}"><span class="icon-cog"></span></a>
            </div>
          {else}
            <div class="tiptip" title="{'N/A'|translate}">
              <a class="icon-cog unavailablePlugin"></a>
            </div>
          {/if}
        {elseif $plugin.STATE == 'inactive'}
          {if $plugin.SETTINGS_URL != ''}
              <div class="tiptip" title="{'Settings'|@translate}"> 
                  <a href="{$plugin.SETTINGS_URL}"><span class="icon-cog"></span></a>
              </div>
          {else}
              <div class="tiptip" title="{'Settings'|@translate}"> 
                <a href="{$plugin.SETTINGS_URL}"><span class="icon-cog"></span></a>
              </div>
          {/if}
        {elseif $plugin.STATE == 'missing'}
          <div class="tiptip" title="{'Uninstall'|@translate}">
            <a class="uninstall-plugin-button" href="{$plugin.U_ACTION}&amp;action=uninstall"></a>
          </div>
        {elseif $plugin.STATE == 'merged'}
          <div class="tiptip" title="{'Delete'|@translate}">
            <a class="" href="{$plugin.U_ACTION}&amp;action=delete"></a>
          </div>
        {/if}                     
      </div>
      
      <div class="PluginOptionsBlock dropdown">
        <div class="dropdown-option-content"> {if !empty($author)}{'By %s'|@translate:$author} | {/if}{'Version'|@translate} {$version}</div>
        <div class="pluginDescCompact">
          {$plugin.DESC}
        </div>
          <a class="dropdown-option icon-back-in-time plugin-restore separator-top">{'Restore'|@translate}</a>
          <a class="dropdown-option icon-trash delete-plugin-button separator-top">{'Delete'|@translate}</a>
      </div>
      <div class="pluginMiniBoxNameCell" data-title="{$plugin.NAME}">
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
            <a class="pluginUnavailableAction icon-cog tiptip" title="{'N/A'|translate}">{'Settings'|@translate}</a>
          {/if}
        {elseif $plugin.STATE == 'inactive'}
          {if $plugin.SETTINGS_URL != ''}
            <a href="{$plugin.SETTINGS_URL}" class="pluginUnavailableAction icon-cog">{'Settings'|@translate}</a>
          {else}
            <a class="pluginUnavailableAction icon-cog tiptip" title="{'N/A'|translate}">{'Settings'|@translate}</a>
          {/if}
        {elseif $plugin.STATE == 'missing'}
          <a class="pluginActionLevel3 uninstall-plugin-button" href="{$plugin.U_ACTION}&amp;action=uninstall">{'Uninstall'|@translate}</a>
        {elseif $plugin.STATE == 'merged'}
          <a class="pluginActionLevel3" href="{$plugin.U_ACTION}&amp;action=delete">{'Delete'|@translate}</a>
        {/if}                     
      </div>
    </div>
    
  </div> 
{/foreach}
</div>
{/if}


<style>

.AlbumViewSelector {
  position: absolute;

  right: 20px;
  z-index: 2;

  height: 43px;
  display: flex;
  align-items: center;

  transform: translateY(6px);
}

.AlbumViewSelector {
  padding: 0px;
  margin-right: 0px;
  border-radius: 7px;
}

.AlbumViewSelector span {
  border-radius: 0;
  padding: 8px;
}

/* Should be done with :first-child and :last-child but doesn't work */

.AlbumViewSelector label span.firstIcon{
  border-radius: 7px 0 0 7px;
}

.AlbumViewSelector label span.lastIcon{
  border-radius: 0 7px 7px 0;
}

.icon-th-large, .icon-th-list, .icon-pause {
  padding: 10px;
  font-size: 19px;

  transition: 0.3s;
}

.switchLayout {
  display: none;
}

/****************************************/

.pluginActionsSmallIcons a, .PluginOptionsIcons a{
  width: 25px;
  height: 25px;

  display: flex;
  justify-content: center;
  align-items: center;

  color: #777;
}

.pluginActionsSmallIcons a:hover, .PluginOptionsIcons a:hover {
  color: #000000;
  transition: 0.2s;
}

.pluginActionsSmallIcons {
  position: absolute;
  right: 20px;
  padding: 13px;
  top: 0px;
  display: flex;
}

.pluginMiniBox.active .pluginActionsSmallIcons a span {
  display: flex;
  align-items: center;
  justify-content: center;

  padding: 5px 2px;
  background: #ffc17e;
  border-radius: 5px;
}

.pluginMiniBox.active .pluginActionsSmallIcons a span:hover {
  display: flex;
  align-items: center;
  justify-content: center;

  padding: 5px 2px;
  background: #ff7700;
  border-radius: 5px;
}

.pluginMiniBox.inactive .pluginActionsSmallIcons a span {
  display: flex;
  align-items: center;
  justify-content: center;

  padding: 5px 2px;
  background: #e0e0e0;
  border-radius: 5px;
}

.pluginActionsSmallIcons a:hover {
  text-decoration: none;
}

.pluginMiniBox {
  transition: 0.5s;
  position: relative;
}

.unavailablePlugin {
  cursor: default;
  opacity: 0.5;
}

.unavailablePlugin:hover {
  cursor: default;
  color: #7f7f7f !important;
  opacity: 0.5;
}

.pluginDescCompact {
  max-width: 200px;
  padding: 5px 10px;
}

.dropdown-option-content {
  font-weight: bold;
}

.separator-top {
  border-top: 1px solid #ffffff45;
}

.dropdown-option.icon-cancel-circled {
  margin-bottom: -5px;
}

.dropdown-option {
  font-weight: bold;
}

.pluginContainer {
  margin-top: 75px;
  padding: 0 20px;
}

.switch {
  margin: 0 10px 0 0;
}

.plugin-inactive .pluginActions a {
  pointer-events: none;
}

.plugin-active .dropdown .delete-plugin-button {
  display: none;
}
  
.plugin-inactive .dropdown .plugin-restore {
 display: none;
}

.plugin-inactive .dropdown .delete-plugin-button {
  display: block;
}
  
.plugin-active .dropdown .plugin-restore {
 display: block;
}

.plugin-inactive .pluginActionsSmallIcons {
  opacity: 0.5;
}

.pluginNotif {
  display:none;
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  top: -20px;
  font-weight:bold;
  z-index: 2;
  white-space: nowrap;
}

.AddPluginSuccess span,
.RestorePluginSuccess span,
.DeactivatePluginSuccess span {
  color: #0a0;
}

.AddPluginSuccess label,
.DeactivatePluginSuccess label,
.RestorePluginSuccess label {
  padding: 10px;
  background-color:  #c2f5c2;
  cursor: default;
  color: #0a0;
  border-radius: 30px;
}

.PluginActionError span {
  color: rgb(170, 0, 0);
}

.PluginActionError label {
  padding: 10px;
  background-color:  #f5c2c2;
  cursor: default;
  color: rgb(170, 0, 0);
  border-radius: 30px;
}

/* Line view */

.pluginContainer.line {
  display: flex;
  flex-direction: column;
}

.pluginContainer.line .pluginMiniBox {
  width: 100%;
  height: 50px;

  margin: 0 0 10px 0;
}

.pluginContainer.line .pluginMiniBox .pluginContent{
  display: flex;
  flex-direction: row;
  align-items: center;
  width: calc(100% - 35px);
}

.pluginContainer.line .pluginMiniBox .pluginActions{
  width: auto;
  margin: 0 25px 0 auto;
}

.pluginContainer.line .pluginMiniBox .PluginOptionsBlock{
  display:none;
  position:absolute;
  right: 30px;
  top: 0;
  z-index: 2;
  transform: translateY(calc(50% - 30px));
}

.pluginContainer.line .pluginMiniBox .dropdown::after {
  content: " ";
  position: absolute;
  bottom: 55%;
  left: calc(100% + 5px);
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: transparent transparent #ff7700 transparent;
  transform: rotate(90deg);
}


.pluginContainer.line .pluginMiniBox .pluginActions a,
.pluginContainer.classic .pluginMiniBox .pluginActions a{
  margin: 0;
  padding: 2px 10px;
  border-radius: 5px;
  color: #3c3c3c;
}

.pluginContainer.line .pluginMiniBox .pluginDesc{
  margin:  auto 10px auto 10px;
  display: block !important;
  align-items: center;

  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;

  max-width: 1000px;
  flex: 1;
}

/* Classic view */

.pluginContainer.classic {
  display: flex;
  flex-direction: row;

  flex-wrap: wrap;
}

.pluginContainer.classic .pluginMiniBoxNameCell {
  position: relative;
  margin-right: 10px;
}

.pluginContainer.classic .switch {
  position: absolute;
  top: 45px;
}

.pluginContainer.classic .pluginMiniBox .pluginActions {
  position: absolute;
  top: 47px;
  right: 17px;
}

/* Compact view */

.plugin-inactive .pluginActionsSmallIcons a {
  pointer-events: none;
}

.pluginContainer.compact {
  display: flex;
  flex-direction: row;

  flex-wrap: wrap;
}

.pluginContainer.compact .pluginMiniBox {
  width: 350px;

  margin: 15px 15px 0 0;
}

.pluginContainer.compact .pluginMiniBox .pluginContent {
  display: flex;
  flex-direction: row;

  align-items: center;
}
</style>