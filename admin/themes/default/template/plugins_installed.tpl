{combine_script id='jquery.ajaxmanager' load='footer' require='jquery' path='themes/default/js/plugins/jquery.ajaxmanager.js' }

{footer_script require='jquery.ajaxmanager'}
/* incompatible message */
var incompatible_msg = '{'WARNING! This plugin does not seem to be compatible with this version of Piwigo.'|@translate|@escape:'javascript'}';
var activate_msg = '\n{'Do you want to activate anyway?'|@translate|@escape:'javascript'}';

var showInactivePlugins = function() {
    jQuery(".showInactivePlugins").fadeOut(complete=function(){
          jQuery(".plugin-inactive").fadeIn();
        })
  }

/* group action */
var pwg_token = '{$PWG_TOKEN}';
var confirmMsg  = '{'Are you sure?'|@translate|@escape:'javascript'}';
{literal}
var queuedManager = jQuery.manageAjax.create('queued', { 
  queue: true,  
  maxRequests: 1
});
var nb_plugins = jQuery('div.active').size();
var done = 0;

jQuery(document).ready(function() {
  /* group action */
  jQuery('div.deactivate_all a').click(function() {
    if (confirm(confirmMsg)) {
      jQuery('div.active').each(function() {
        performPluginDeactivate(jQuery(this).attr('id'));
      });
    }
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
          jQuery('#'+data[i]+' .activate').attr('onClick', 'return confirm(incompatible_msg + activate_msg);');
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
  
  /* TipTips */
  jQuery('.plugin-restore').tipTip({
    'delay' : 0,
    'fadeIn' : 200,
    'fadeOut' : 200
  });
  jQuery('.showInfo').tipTip({
    'delay' : 0,
    'fadeIn' : 200,
    'fadeOut' : 200,
    'maxWidth':'300px',
    'keepAlive':true,
    'activation':'click'
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
      console.log(searchNumber);
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

<div class="titrePage">
  <h2>{'Plugins'|@translate}</h2>
</div>

{if isset($plugins)}

{assign var='field_name' value='null'} {* <!-- 'counter' for fieldset management --> *}
{counter start=0 assign=i} {* <!-- counter for 'deactivate all' link --> *}

<div class="pluginFilter"> 
  <p class="icon-filter">{'Filter'|@translate}</p>
  <input type="text" placeholder="{'Name'|@translate}, {'Description'|@translate}">
</div>

<div class="emptyResearch"> {'No plugins found'|@translate} </div>

{foreach from=$plugins item=plugin name=plugins_loop}
    
{if $field_name != $plugin.STATE}
  {if $field_name != 'null'}
  </div> {* PluginBoxes Container*}
  </div> {* PluginBoxes*}
      {/if}

  <div class="pluginBoxes plugin-{$plugin.STATE}" {if $plugin.STATE == 'inactive'}{if $count_types_plugins["inactive"]>8}style="display:none"{/if}{/if}>
  {assign var='field_name' value=$plugin.STATE}

  <div class="pluginBoxesHead">
      <div class="pluginBoxesTitle">
        <p>
        {if $plugin.STATE == 'active'}
          {'Active Plugins'|@translate}
        {elseif $plugin.STATE == 'inactive'}
          {'Inactive Plugins'|@translate}
        {elseif $plugin.STATE == 'missing'}
          {'Missing Plugins'|@translate}
        {elseif $plugin.STATE == 'merged'}
          {'Obsolete Plugins'|@translate}
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
        <a class="icon-info-circled-1 showInfo" title="{if !empty($author)}{'By %s'|@translate:$author} | {/if}{'Version'|@translate} {$version}"></a>
        {if $plugin.STATE == 'active' || $plugin.STATE == 'inactive'}
          <a class="icon-ellipsis-vert showOptions" ></a>
        {/if}
      </div>
      
      <div class="PluginOptionsBlock">
        {if $plugin.STATE == 'active'}
          <a class="plugin-dropdown-action icon-cancel-circled" href="{$plugin.U_ACTION}&amp;action=deactivate">{'Deactivate'|@translate}</a>
          <a class="plugin-dropdown-action icon-back-in-time" href="{$plugin.U_ACTION}&amp;action=restore" class="plugin-restore" title="{'Restore default configuration. You will lose your plugin settings!'|@translate}" onclick="return confirm(confirmMsg);">{'Restore'|@translate}</a>   
        {elseif $plugin.STATE == 'inactive'}
          <a class="plugin-dropdown-action icon-trash" href="{$plugin.U_ACTION}&amp;action=delete" onclick="return confirm(confirmMsg);">{'Delete'|@translate}</a>
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
            <div class="pluginUnavailableAction icon-cog">{'Settings'|@translate}</div>
          {/if}
        {elseif $plugin.STATE == 'inactive'}
          <a class="pluginActionLevel1 icon-plus" href="{$plugin.U_ACTION}&amp;action=activate" class="activate">{'Activate'|@translate}</a>
        {elseif $plugin.STATE == 'missing'}
          <a class="pluginActionLevel3" href="{$plugin.U_ACTION}&amp;action=uninstall" onclick="return confirm(confirmMsg);">{'Uninstall'|@translate}</a>
        {elseif $plugin.STATE == 'merged'}
          <a class="pluginActionLevel3" href="{$plugin.U_ACTION}&amp;action=delete">{'Delete'|@translate}</a>
        {/if}                     
      </div>
    </div>
    
  </div> {*<!-- pluginMiniBox -->*}
    

    
  {/foreach}
  </div> {* PluginBoxes Container*}
  </div> {* PluginBoxes*}

  <div class="showInactivePlugins" {if $count_types_plugins["inactive"]<=8}style="display:none"{/if} >
      <div class="showInactivePluginsInfo">
        {assign var='badge_inactive' value='<span class="pluginBoxesCount">%s</span>'|@sprintf:$count_types_plugins["inactive"]}
        <div>{'You have %s inactive plugins'|translate:$badge_inactive}</div>
      </div>
      <button class="buttonLike" id="showInactivePluginsAction">{'Show inactive plugins'|@translate}</button>
  </div>

{/if}
