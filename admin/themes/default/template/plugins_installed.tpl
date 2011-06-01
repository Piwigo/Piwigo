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
          {if $plugin_display == 'complete'}
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
    'maxWidth':'300px', /* not effective, TipTip bug */
    'keepAlive':true,
    'activation':'click'
  });
});
{/literal}
{/footer_script}

<div class="titrePage">
  <span class="sort">
    <form action="" method="get" name="change_display">
      <input type="hidden" name="page" value="plugins"/>
      {'Display'|@translate} : 
      <select name="plugin_display" onchange="this.form.submit();">
        <option value="compact" {if $plugin_diplay=='compact'}selected="selected"{/if}>{'Compact'|@translate}</option>
        <option value="complete" {if $plugin_display=='complete'}selected="selected"{/if}>{'Complete'|@translate}</option>
      </select>
    </form>
  </span>
  <h2>{'Plugins'|@translate}</h2>
</div>

{if isset($plugins)}

{assign var='field_name' value='null'}
{foreach from=$plugins item=plugin name=plugins_loop}
    
{if $field_name != $plugin.STATE}
  {if $field_name != 'null'}
    {if $field_name == 'active'}<div class="deactivate_all"><a>{'Deactivate all'|@translate}</a></div>{/if}
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
   
  {if $plugin_display == 'complete'}
    <div id="{$plugin.ID}" class="pluginBox {$plugin.STATE}">
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
            | <a href="{$plugin.U_ACTION}&amp;action=restore" class="plugin-restore" title="{'Restore default configuration. You will lost your plugin settings!'|@translate}" onclick="return confirm('{'Are you sure?'|@translate|@escape:'javascript'}');">{'Restore'|@translate}</a>

          {elseif $plugin.STATE == 'inactive'}
            <a href="{$plugin.U_ACTION}&amp;action=activate" class="activate">{'Activate'|@translate}</a>
            | <a href="{$plugin.U_ACTION}&amp;action=delete" onclick="return confirm('{'Are you sure?'|@translate|@escape:'javascript'}');">{'Delete'|@translate}</a>

          {elseif $plugin.STATE == 'missing'}
            <a href="{$plugin.U_ACTION}&amp;action=uninstall" onclick="return confirm('{'Are you sure?'|@translate|@escape:'javascript'}');">{'Uninstall'|@translate}</a>

          {elseif $plugin.STATE == 'merged'}
            <a href="{$plugin.U_ACTION}&amp;action=delete">{'Delete'|@translate}</a>
          {/if}
          </td>
          <td>
            {'Version'|@translate} {$plugin.VERSION}
            
          {if not empty($author)}
            | {'By %s'|@translate|@sprintf:$author}
          {/if}

          {if not empty($plugin.VISIT_URL)}
            | <a class="externalLink" href="{$plugin.VISIT_URL}">{'Visit plugin site'|@translate}</a>
          {/if}
          </td>
        </tr>
      </table>
    </div> {*<!-- pluginBox -->*}
    
  {elseif $plugin_display == 'compact'}
    {if not empty($plugin.VISIT_URL)}
      {assign var='version' value="<a class='externalLink' href='"|cat:$plugin.VISIT_URL|cat:"'>"|cat:$plugin.VERSION|cat:"</a>"}
    {else
      {assign var='version' value=$plugin.VERSION}
    {/if}
          
    <div id="{$plugin.ID}" class="pluginMiniBox {$plugin.STATE}">
      <div class="pluginMiniBoxNameCell">
        {$plugin.NAME}
        <a class="showInfo" title="{if !empty($author)}{'By %s'|@translate|@sprintf:$author} | {/if}{'Version'|@translate} {$version}<br/>{$plugin.DESC|@escape:'html'}">i</a>
      </div>
      <div class="pluginActions">
        <div>
        {if $plugin.STATE == 'active'}
          <a href="{$plugin.U_ACTION}&amp;action=deactivate">{'Deactivate'|@translate}</a>
          | <a href="{$plugin.U_ACTION}&amp;action=restore" class="plugin-restore" title="{'Restore default configuration. You will lost all your settings !'|@translate}" onclick="return confirm('{'Are you sure?'|@translate|@escape:'javascript'}');">{'Restore'|@translate}</a>

        {elseif $plugin.STATE == 'inactive'}
          <a href="{$plugin.U_ACTION}&amp;action=activate" class="activate">{'Activate'|@translate}</a>
          | <a href="{$plugin.U_ACTION}&amp;action=delete" onclick="return confirm('{'Are you sure?'|@translate|@escape:'javascript'}');">{'Delete'|@translate}</a>

        {elseif $plugin.STATE == 'missing'}
          <a href="{$plugin.U_ACTION}&amp;action=uninstall" onclick="return confirm('{'Are you sure?'|@translate|@escape:'javascript'}');">{'Uninstall'|@translate}</a>

        {elseif $plugin.STATE == 'merged'}
          <a href="{$plugin.U_ACTION}&amp;action=delete">{'Delete'|@translate}</a>
        {/if}
        </div>
      </div>
    </div> {*<!-- pluginMiniBox -->*}
    
  {/if}
  
{/foreach}
  </fieldset>

{/if}
