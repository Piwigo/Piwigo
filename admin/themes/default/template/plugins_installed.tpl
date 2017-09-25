{combine_script id='jquery.ajaxmanager' load='footer' require='jquery' path='themes/default/js/plugins/jquery.ajaxmanager.js' }

{footer_script require='jquery.ajaxmanager'}
/* incompatible message */
var incompatible_msg = '{'WARNING! This plugin does not seem to be compatible with this version of Piwigo.'|@translate|@escape:'javascript'}';
var activate_msg = '\n{'Do you want to activate anyway?'|@translate|@escape:'javascript'}';

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

  /* Warnings message - plugins deactivated */
  jQuery('div.deleteMessage').click(function() {
    jQuery.ajax({
      type: 'GET',
      dataType: 'json',
      url: 'ws.php',
      data: { method: 'pwg.plugins.previouslyActivated', action: 'deactivate_all', pwg_token: pwg_token, format: 'json' },
      success: function(data) {
        location.reload();
      }
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
});
{/literal}
{/footer_script}

{if $deactivated_msg == 'true'}
<div class="deleteMessage">
  <div class="warnings">
    <i class="eiw-icon icon-attention"></i>
      <div class="deactivatedAfterUpdate">
        {'%s plugin(s) have been deactivated during upgrade: %s'|@translate:$nbr_deactivated:$deactivated_plugins}
        <a class="icon-eye-off">Hide this message</a>
      </div>
    </div>
</div>
{/if}

<div class="titrePage">
  <h2>{'Plugins'|@translate}</h2>
</div>

<div class="showDetails">
  {if $show_details}
  <a href="{$base_url}&amp;show_details=0">{'hide details'|@translate}</a>
  {else}
  <a href="{$base_url}&amp;show_details=1">{'show details'|@translate}</a>
  {/if}
</div>

{if isset($plugins)}

{assign var='field_name' value='null'} {* <!-- 'counter' for fieldset management --> *}
{counter start=0 assign=i} {* <!-- counter for 'deactivate all' link --> *}
{foreach from=$plugins item=plugin name=plugins_loop}
    
{if $field_name != $plugin.STATE}
  {if $field_name != 'null'}
  </fieldset>
  {/if}
  
  <fieldset class="pluginBoxes">
    <legend>
    {if $plugin.STATE == 'active'}
      {'Active Plugins'|@translate}
    {elseif $plugin.STATE == 'inactive'}
      {'Inactive Plugins'|@translate}
    {elseif $plugin.STATE == 'missing'}
      {'Missing Plugins'|@translate}
    {elseif $plugin.STATE == 'merged'}
      {'Obsolete Plugins'|@translate}
    {/if}
    </legend>
  {assign var='field_name' value=$plugin.STATE}
{/if}
  
  {if not empty($plugin.AUTHOR)}
    {if not empty($plugin.AUTHOR_URL)}
      {assign var='author' value="<a href='%s'>%s</a>"|@sprintf:$plugin.AUTHOR_URL:$plugin.AUTHOR}
    {else}
      {assign var='author' value='<u>'|cat:$plugin.AUTHOR|cat:'</u>'}
    {/if}
  {/if}
   
  {if $show_details}
    <div id="{$plugin.ID}" class="pluginBox {$plugin.STATE} {if $plugin.DEACTIVATED == 'true'}previouslyActivated{/if}">
      <table>
        <tr>
          <td class="pluginBoxNameCell">
            {$plugin.NAME}
          </td>
          <td>{$plugin.DESC}</td>
        </tr>
        <tr class="pluginActions">
          <td>
          {if $plugin.STATE == 'active'}
            <a href="{$plugin.U_ACTION}&amp;action=deactivate">{'Deactivate'|@translate}</a>
            | <a href="{$plugin.U_ACTION}&amp;action=restore" class="plugin-restore" title="{'Restore default configuration. You will lose your plugin settings!'|@translate}" onclick="return confirm(confirmMsg);">{'Restore'|@translate}</a>

          {elseif $plugin.STATE == 'inactive'}
            <a href="{$plugin.U_ACTION}&amp;action=activate" class="activate">{'Activate'|@translate}</a>
            | <a href="{$plugin.U_ACTION}&amp;action=delete" onclick="return confirm(confirmMsg);">{'Delete'|@translate}</a>

          {elseif $plugin.STATE == 'missing'}
            <a href="{$plugin.U_ACTION}&amp;action=uninstall" onclick="return confirm(confirmMsg);">{'Uninstall'|@translate}</a>

          {elseif $plugin.STATE == 'merged'}
            <a href="{$plugin.U_ACTION}&amp;action=delete">{'Delete'|@translate}</a>
          {/if}
          </td>
          <td>
            {'Version'|@translate} {$plugin.VERSION}
            
          {if not empty($author)}
            | {'By %s'|@translate:$author}
          {/if}

          {if not empty($plugin.VISIT_URL)}
            | <a class="externalLink" href="{$plugin.VISIT_URL}">{'Visit plugin site'|@translate}</a>
          {/if}
          </td>
        </tr>
      </table>
    </div> {*<!-- pluginBox -->*}
    
  {else}
    {if not empty($plugin.VISIT_URL)}
      {assign var='version' value="<a class='externalLink' href='"|cat:$plugin.VISIT_URL|cat:"'>"|cat:$plugin.VERSION|cat:"</a>"}
    {else}
      {assign var='version' value=$plugin.VERSION}
    {/if}
          
    <div id="{$plugin.ID}" class="pluginMiniBox {$plugin.STATE} {if $plugin.DEACTIVATED == 'true'}previouslyActivated{/if}">
      <div class="pluginMiniBoxNameCell">
        {$plugin.NAME}
        <a class="icon-info-circled-1 showInfo" title="{if !empty($author)}{'By %s'|@translate:$author} | {/if}{'Version'|@translate} {$version}<br/>{$plugin.DESC|@escape:'html'}"></a>
      </div>
      <div class="pluginActions">
        <div>
        {if $plugin.STATE == 'active'}
          <a href="{$plugin.U_ACTION}&amp;action=deactivate">{'Deactivate'|@translate}</a>
          | <a href="{$plugin.U_ACTION}&amp;action=restore" class="plugin-restore" title="{'Restore default configuration. You will lose your plugin settings!'|@translate}" onclick="return confirm(confirmMsg);">{'Restore'|@translate}</a>

        {elseif $plugin.STATE == 'inactive'}
          <a href="{$plugin.U_ACTION}&amp;action=activate" class="activate">{'Activate'|@translate}</a>
          | <a href="{$plugin.U_ACTION}&amp;action=delete" onclick="return confirm(confirmMsg);">{'Delete'|@translate}</a>

        {elseif $plugin.STATE == 'missing'}
          <a href="{$plugin.U_ACTION}&amp;action=uninstall" onclick="return confirm(confirmMsg);">{'Uninstall'|@translate}</a>

        {elseif $plugin.STATE == 'merged'}
          <a href="{$plugin.U_ACTION}&amp;action=delete">{'Delete'|@translate}</a>
        {/if}
        </div>
      </div>
    </div> {*<!-- pluginMiniBox -->*}
    
  {/if}
  
{if $plugin.STATE == 'active'}
  {counter}
  {if $active_plugins == $i}
    <div class="deactivate_all"><a>{'Deactivate all'|@translate}</a></div>
    {counter}
  {/if}
{/if}
  
{/foreach}
  </fieldset>

{/if}
