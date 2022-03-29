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
var nb_plugin = {
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
{literal}

jQuery(document).ready(function() {
  
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

      var searchActive = 0;
      var searchInactive = 0;
      var searchOther = 0;
      
        $(".pluginMiniBox").each(function() {
          if (text == "") {
            jQuery(".nbPluginsSearch").hide();
            if ($("#seeAll").is(":checked")) {
              jQuery(this).show();
            }
            if ($("#seeActive").is(":checked") && jQuery(this).hasClass("plugin-active")) {
              jQuery(this).show();
            }
            if ($("#seeInactive").is(":checked") && jQuery(this).hasClass("plugin-inactive")) {
              jQuery(this).show();
            }
            if ($("#seeOther").is(":checked") && (jQuery(this).hasClass("plugin-merged") || jQuery(this).hasClass("plugin-missing"))) {
              jQuery(this).show();
            }

            if ($(this).hasClass("plugin-active")) {
              searchActive++;
            }
            if ($(this).hasClass("plugin-inactive")) {
              searchInactive++;
            }
            if (($(this).hasClass("plugin-merged") || $(this).hasClass("plugin-missing"))) {
              searchOther++;
            }
            searchNumber++

            nb_plugin.all = searchNumber;
            nb_plugin.active = searchActive;
            nb_plugin.inactive = searchInactive;
            nb_plugin.other = searchOther;

          } else {
            jQuery(".nbPluginsSearch").show();
            let name = jQuery(this).find(".pluginMiniBoxNameCell").text().toLowerCase();
            let description = jQuery(this).find(".pluginDesc").text().toLowerCase();
            if (name.search(text) != -1 || description.search(text) != -1){
              searchNumber++;

              if ($("#seeAll").is(":checked")) {
                jQuery(this).show();
              }
              if ($("#seeActive").is(":checked") && jQuery(this).hasClass("plugin-active")) {
                jQuery(this).show();
              }
              if ($("#seeInactive").is(":checked") && jQuery(this).hasClass("plugin-inactive")) {
                jQuery(this).show();
              }
              if ($("#seeOther").is(":checked") && (jQuery(this).hasClass("plugin-merged") || jQuery(this).hasClass("plugin-missing"))) {
                jQuery(this).show();
              }

              if ($(this).hasClass("plugin-active")) {
                searchActive++;
              }
              if ($(this).hasClass("plugin-inactive")) {
                searchInactive++;
              }
              if (($(this).hasClass("plugin-merged") || $(this).hasClass("plugin-missing"))) {
                searchOther++;
              }

              nb_plugin.all = searchNumber;
              nb_plugin.active = searchActive;
              nb_plugin.inactive = searchInactive;
              nb_plugin.other = searchOther;
            } else {
              jQuery(this).hide();

              nb_plugin.all = searchNumber;
              nb_plugin.active = searchActive;
              nb_plugin.inactive = searchInactive;
              nb_plugin.other = searchOther;
            }
          }
        })

      actualizeFilter();
        
      if (searchNumber == 0) {
        jQuery(".nbPluginsSearch").html(nothing_found);
      } else if (searchNumber == 1) {
        jQuery(".nbPluginsSearch").html(plugin_found.replace("%s", searchNumber));
      } else {
        jQuery(".nbPluginsSearch").html(x_plugins_found.replace("%s", searchNumber));
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

function actualizeFilter() {

    $("label[for='seeAll'] .filter-badge").html(nb_plugin.all);
    $("label[for='seeActive'] .filter-badge").html(nb_plugin.active);
    $("label[for='seeInactive'] .filter-badge").html(nb_plugin.inactive);
    $("label[for='seeOther'] .filter-badge").html(nb_plugin.other);

    //console.log(nb_plugin)

    $(".filterLabel").show();
    $(".pluginMiniBox").each(function () {
        if (nb_plugin.active == 0) {
            $("label[for='seeActive']").hide();
            if ($("#seeActive").is(":checked")) {
              $("#seeAll").trigger("click")
            }
        }
        if (nb_plugin.inactive == 0) {
            $("label[for='seeInactive']").hide();
            if ($("#seeInactive").is(":checked")) {
              $("#seeAll").trigger("click")
            }
        }
        if (nb_plugin.other == 0) {
            $("label[for='seeOther']").hide();
            if ($("#seeOther").is(":checked")) {
              $("#seeAll").trigger("click")
            }
        }
    })
}

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
<input type="radio" name="p-filter" class="filter" id="seeAll" {if $count_types_plugins["active"] <= 0} checked {/if}><label for="seeAll">{'All'|@translate}<span class="filter-badge">X</span></label><input type="radio" name="p-filter" class="filter" id="seeActive" {if $count_types_plugins["active"] > 0} checked {/if}><label class="filterLabel" for="seeActive">{'Activated'|@translate}<span class="filter-badge">X</span></label><input type="radio" name="p-filter" class="filter" id="seeInactive"><label class="filterLabel" for="seeInactive">{'Deactivated'|@translate}<span class="filter-badge">X</span></label><input type="radio" name="p-filter" class="filter" id="seeOther"><label class="filterLabel" for="seeOther">{'Other'|@translate}<span class="filter-badge">X</span></label>
</div>

<div class="nbPluginsSearch"></div>

<div class="pluginFilter"> 
  <span class="icon-search search-icon"></span>
  <span class="icon-cancel search-cancel"></span>
  <input class='search-input' type="text" placeholder="{'Search'|@translate}">
</div>

<div class="AlbumViewSelector">
    <input type="radio" name="layout" class="switchLayout" id="displayClassic" {if $smarty.cookies.pwg_plugin_manager_view == 'classic' || !$smarty.cookies.pwg_plugin_manager_view}checked{/if}/><label for="displayClassic"><span class="icon-pause firstIcon tiptip" title="{'Classic View'|translate}"></span></label><input type="radio" name="layout" class="switchLayout" id="displayLine" {if $smarty.cookies.pwg_plugin_manager_view == 'line'}checked{/if}/><label for="displayLine"><span class="icon-th-list tiptip" title="{'Line View'|translate}"></span></label><input type="radio" name="layout" class="switchLayout" id="displayCompact" {if $smarty.cookies.pwg_plugin_manager_view == 'compact'}checked{/if}/><label for="displayCompact"><span class="icon-th-large lastIcon tiptip" title="{'Compact View'|translate}"></span></label>
</div>  

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
        {elseif $plugin.STATE == 'merged'}
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
          <a class="pluginActionLevel3 uninstall-plugin-button">{'Uninstall'|@translate}</a>
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
  border-radius: 5px;
}

.pluginMiniBox.active .pluginActionsSmallIcons a span:hover {
  display: flex;
  align-items: center;
  justify-content: center;

  padding: 5px 2px;
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
  color: #3a3a3a !important;
}

.pluginUnavailableAction {
  text-decoration: none !important;
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

.AddPluginSuccess label,
.DeactivatePluginSuccess label,
.RestorePluginSuccess label {
  padding: 10px;
  cursor: default;
  border-radius: 30px;
}

.PluginActionError label {
  padding: 10px;
  cursor: default;
  border-radius: 30px;
}

/* Line view */

.pluginContainer.line {
  display: flex;
  flex-direction: column;
  box-shadow: none;
  background: transparent;
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

.filter-badge {
  border-radius: 20px;
  text-align: center;
  padding: 0px 7px;
  margin-left: 10px;
}

.pluginMiniBox.incompatible .pluginMiniBoxNameCell i {
  color:#c64444;
}

.nbPluginsSearch {
  position: absolute;
  right: 415px;
  transform: translateY(18px);
}
</style>