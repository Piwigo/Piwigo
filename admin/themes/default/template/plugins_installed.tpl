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
            jQuery('#'+data[i]+' .pluginName').prepend('<a class="warning" title="'+incompatible_msg+'"></a>')
          {else}
            jQuery('#'+data[i]+' .pluginName').prepend('<span class="warning" title="'+incompatible_msg+'"></span>')
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
      
        $(".pluginBox").each(function() {
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
            let name = jQuery(this).find(".pluginName").text().toLowerCase();
            jQuery(".nbPluginsSearch").show();
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
  $(".pluginBox").each(function() {  
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

jQuery(".pluginBox").each(function(index){

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

<div class="pluginInstalledFilters">
  <div class="pluginTypeFilter">
    <input type="radio" name="p-filter" class="filter" id="seeAll" checked><label for="seeAll">All</label><input type="radio" name="p-filter" class="filter" id="seeActive"><label class="filterLabel" for="seeActive">Active</label><input type="radio" name="p-filter" class="filter" id="seeInactive"><label class="filterLabel" for="seeInactive">Inactive</label><input type="radio" name="p-filter" class="filter" id="seeOther"><label class="filterLabel" for="seeOther">Other</label>
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

</div>

<div class="emptyResearch"> {'No plugins found'|@translate} </div>

    <div class="pluginContainer {if $smarty.cookies.pwg_plugin_manager_view == 'classic'} classic-form {elseif $smarty.cookies.pwg_plugin_manager_view == 'line'} line-form {elseif $smarty.cookies.pwg_plugin_manager_view == 'compact'} compact-form {else} {/if}">

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
        {elseif $plugin.STATE == 'merged'}
          <a class="pluginActionLevel3" href="{$plugin.U_ACTION}&amp;action=delete">{'Delete'|@translate}</a>
        {/if}                     
      </div>
    </div>
    
  </div> 
{/foreach}
</div>
{/if}