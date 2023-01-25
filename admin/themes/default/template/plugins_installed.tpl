{combine_script id='jquery.ajaxmanager' load='footer' require='jquery' path='themes/default/js/plugins/jquery.ajaxmanager.js' }
{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='jquery.cookie' path='themes/default/js/jquery.cookie.js' load='footer'}

{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}
{combine_script id='tiptip' load='header' path='themes/default/js/plugins/jquery.tipTip.minified.js'}

{combine_script id='pluginInstallated' load='footer' require='jquery.ajaxmanager' path='admin/themes/default/js/plugins_installated.js'}

{footer_script}
/* incompatible message */
var incompatible_msg = '{'WARNING! This plugin does not seem to be compatible with this version of Piwigo.'|@translate|@escape:'javascript'}';
var activate_msg = '\n{'Do you want to activate anyway?'|@translate|@escape:'javascript'}';
var deactivate_all_msg = '{'Deactivate all'|@translate}';

/* group action */
const pwg_token = '{$PWG_TOKEN}';
const nb_plugin = {
  'all' : {$count_types_plugins["active"]} + {$count_types_plugins["inactive"]} + {$count_types_plugins["missing"]} + {$count_types_plugins["merged"]},
  'active' : {$count_types_plugins["active"]},
  'inactive' : {$count_types_plugins["inactive"]},
  'other' : {$count_types_plugins["missing"]} + {$count_types_plugins["merged"]},
};
const are_you_sure_msg  = '{'Are you sure?'|@translate|@escape:'javascript'}';
const confirm_msg = '{"Yes, I am sure"|@translate}';
const cancel_msg = "{"No, I have changed my mind"|@translate}";
let delete_plugin_msg = '{'Are you sure you want to delete the plugin "%s"?'|@translate|@escape:'javascript'}';
let deleted_plugin_msg = '{'Plugin "%s" deleted!'|@translate|@escape:'javascript'}';
let restore_plugin_msg = '{'Are you sure you want to restore the plugin "%s"?'|@translate|@escape:'javascript'}';
let uninstall_plugin_msg = '{'Are you sure you want to uninstall the plugin "%s"?'|@translate|@escape:'javascript'}';
const restore_tip_msg = "{'Restore default configuration. You will lose your plugin settings!'|@translate|@escape:'javascript'}";
const plugin_added_str = '{'Activated'|@translate|@escape:'javascript'}';
const plugin_deactivated_str = '{'Deactivated'|@translate|@escape:'javascript'}';
const plugin_restored_str = '{'Restored'|@translate|@escape:'javascript'}';
const plugin_action_error = '{'an error happened'|@translate|@escape:'javascript'}';
const not_webmaster = '{'Webmaster status required'|@translate|@escape:'javascript'}';
const nothing_found = '{'No plugins found'|@translate|@escape:'javascript'}';
const x_plugins_found = '{'%s plugins found'|@translate|@escape:'javascript'}';
const plugin_found = '{'%s plugin found'|@translate|@escape:'javascript'}';
const isWebmaster = {$isWebmaster};
const view_selector = '{$view_selector}';
const str_restore_def = '{'While restoring this plugin, it will be reset to its original parameters and associated data is going to be reset'|@translate|@escape:'javascript'}';

const show_details = {if $show_details} true {else} false {/if};

let searchParams = new URLSearchParams(window.location.search);
let plugin_filter = searchParams.get('filter');
{/footer_script}

{if isset($plugins)}

{assign var='field_name' value='null'} {* <!-- 'counter' for fieldset management --> *}
{counter start=0 assign=i} {* <!-- counter for 'deactivate all' link --> *}

<div class="pluginTypeFilter">
<input type="radio" name="p-filter" class="filter" id="seeAll" {if $count_types_plugins["active"] <= 0} checked {/if}><label for="seeAll">{'All'|@translate}<span class="filter-badge">X</span></label><input type="radio" name="p-filter" class="filter" id="seeActive" {if $count_types_plugins["active"] > 0} checked {/if}><label class="filterLabel" for="seeActive">{'Activated'|@translate}<span class="filter-badge">X</span></label><input type="radio" name="p-filter" class="filter" id="seeInactive"><label class="filterLabel" for="seeInactive">{'Deactivated'|@translate}<span class="filter-badge">X</span></label><input type="radio" name="p-filter" class="filter" id="seeOther"><label class="filterLabel" for="seeOther">{'Other'|@translate}<span class="filter-badge">X</span></label>
</div>

<div class="nbPluginsSearch"></div>

<div class="pluginFilter"> 
  <span class="icon-search search-icon"></span>
  <span class="icon-cancel search-cancel"></span>
  <input class='search-input' type="text" placeholder="{'Search'|@translate}">
</div>

<div class="AlbumViewSelector">
    <input type="radio" name="layout" class="switchLayout" id="displayClassic" {if $view_selector == 'classic'}checked{/if}/><label for="displayClassic"><span class="icon-pause firstIcon tiptip" title="{'Classic View'|translate}"></span></label><input type="radio" name="layout" class="switchLayout" id="displayLine" {if $view_selector== 'line'}checked{/if}/><label for="displayLine"><span class="icon-th-list tiptip" title="{'Line View'|translate}"></span></label><input type="radio" name="layout" class="switchLayout" id="displayCompact" {if $view_selector == 'compact'}checked{/if}/><label for="displayCompact"><span class="icon-th-large lastIcon tiptip" title="{'Compact View'|translate}"></span></label>
</div>  

<div class="pluginContainer {if $view_selector == 'classic'} classic-form {elseif $view_selector == 'line'} line-form {elseif $view_selector == 'compact'} compact-form {else} {/if}">

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

<div id="{$plugin.ID}" class="pluginBox pluginMiniBox {$plugin.STATE} plugin-{$plugin.STATE}">

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
  {if $plugin.STATE == 'active' || $plugin.STATE == 'inactive'}
    <label class="switch">
      <input type="checkbox" id="toggleSelectionMode" {if {$plugin.STATE} === "active"}checked{/if}>
      <span class="slider round"></span>
    </label>
  {/if}

      <div class="pluginActionsSmallIcons">
        {if $plugin.STATE == 'active'}
          {if $plugin.SETTINGS_URL != ''}
            <div class="tiptip" title="{'Settings'|@translate}"> 
              <a href="{$plugin.SETTINGS_URL}"><span class="icon-cog"></span></a>
            </div>
          {else}
            <div class="tiptip" title="{'N/A'|translate}">
              <a><i class="icon-cog"></i></a>
            </div>
          {/if}
        {elseif $plugin.STATE == 'inactive'}
          {if $plugin.SETTINGS_URL != ''}
              <div class="tiptip" title="{'Settings'|@translate}"> 
                  <a href="{$plugin.SETTINGS_URL}"><span class="icon-cog"></span></a>
              </div>
          {else}
              <div class="tiptip" title="{'N/A'|@translate}"> 
                  <a><i class="icon-cog"></i></a>
              </div>
          {/if}
        {elseif $plugin.STATE == 'missing'}
          <div class="tiptip" title="{'Uninstall'|@translate}">
            <a class="uninstall-plugin-button">{'Uninstall'|@translate}</a>
          </div>
        {elseif $plugin.STATE == 'merged' and $CONF_ENABLE_EXTENSIONS_INSTALL}
          <div class="tiptip" title="{'Delete'|@translate}">
            <a class="" href="{$plugin.U_ACTION}&amp;action=delete">{'Delete'|@translate}</a>
          </div>
        {/if}                     
      </div>
      
      <div class="PluginOptionsBlock dropdown">
        <div class="dropdown-option-content"> {if !empty($author)}{'By %s'|@translate:$author} | {/if}{'Version'|@translate} {$version}</div>
        <div class="pluginDescCompact">
          {$plugin.DESC}
        </div>
          <a class="dropdown-option icon-back-in-time plugin-restore separator-top tiptip" title="{'While restoring this plugin, it will be reset to its original parameters and associated data is going to be reset'|@translate}">{'Restore'|@translate}</a>
  {if $CONF_ENABLE_EXTENSIONS_INSTALL}
          <a class="dropdown-option icon-trash delete-plugin-button separator-top">{'Delete'|@translate}</a>
  {/if}
      </div>
      <div class="pluginName" data-title="{$plugin.NAME}">
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
          <a class="pluginActionLevel3 uninstall-plugin-button">{'Uninstall'|@translate}</a>
        {elseif $plugin.STATE == 'merged' and $CONF_ENABLE_EXTENSIONS_INSTALL}
          <a class="pluginActionLevel3" href="{$plugin.U_ACTION}&amp;action=delete">{'Delete'|@translate}</a>
        {/if}                     
      </div>
    </div>
    
  </div> 
{/foreach}
</div>
{/if}