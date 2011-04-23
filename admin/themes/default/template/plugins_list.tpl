{combine_script id='jquery.ajaxmanager' load='footer' require='jquery' path='themes/default/js/plugins/jquery.ajaxmanager.js' }

{footer_script require='jquery.ajaxmanager'}
/* incompatible message */
var incompatible_msg = '{'WARNING! This plugin does not seem to be compatible with this version of Piwigo.'|@translate|@escape:'javascript'}';
incompatible_msg += '\n';
incompatible_msg += '{'Do you want to activate anyway?'|@translate|@escape:'javascript'}';

/* group action */
var pwg_token = '{$PWG_TOKEN}';
var confirmMsg  = '{'Are you sure?'|@translate|@escape:'javascript'}';
{literal}
var queuedManager = jQuery.manageAjax.create('queued', { 
  queue: true,  
  maxRequests: 1,
  complete: function() { location.reload(); }
});

jQuery(document).ready(function() {
  /* group action */
  jQuery('a.deactivate_all').click(function() {
    if (confirm(confirmMsg)) {
      jQuery('div.active').each(function() {
        performPluginAction(jQuery(this).attr('id'), 'deactivate');
      });
    }
  });
  jQuery('a.activate_all').click(function() {
    if (confirm(confirmMsg)) {
      jQuery('div.inactive').each(function() {
        performPluginAction(jQuery(this).attr('id'), 'activate');
      });
    }
  });
  function performPluginAction(id, action) {
   queuedManager.add({
      type: 'GET',
      dataType: 'json',
      url: 'ws.php',
      data: { method: 'pwg.plugins.performAction', action: action, plugin: id, pwg_token: pwg_token, format: 'json' },
      success: function(data) {
        if (data['stat'] == 'ok') {
          if (action == 'deactivate')
            jQuery("#"+id).removeClass('active').addClass('inactive');
          else
            jQuery("#"+id).removeClass('inactive').addClass('active');
        }
      }
    });
  };

  /* incompatible message */
  jQuery('.incompatible a.incompatible').click(function() {
    return confirm(incompatible_msg);
  });
  
  /* TipTips */
  jQuery('.warning').tipTip({
    'delay' : 0,
    'fadeIn' : 200,
    'fadeOut' : 200,
    'maxWidth':'250px'
  });
  jQuery('.plugin-restore').tipTip({
    'delay' : 0,
    'fadeIn' : 200,
    'fadeOut' : 200
  });
  jQuery('.pluginMiniBoxDesc').tipTip({
    'delay' : 0,
    'fadeIn' : 200,
    'fadeOut' : 200,
    'maxWidth':'300px', /* not effective, TipTip bug */
    'defaultPosition':'left',
    'keepAlive':true
  });
});
{/literal}
{/footer_script}

<div class="titrePage">
  <span class="sort">
    <form action="" method="get" name="change_order">
      <input type="hidden" name="page" value="plugins"/>
      {'Sort order'|@translate} : 
      <select name="plugin_order" onchange="this.form.submit();">
        <option value="status" {if $plugin_order=='state'}selected="selected"{/if}>{'Status'|@translate}</option>
        <option value="name" {if $plugin_order=='name'}selected="selected"{/if}>{'Name'|@translate}</option>
      </select>
      &nbsp;&nbsp;|&nbsp;&nbsp;
      {'Display'|@translate} : 
      <select name="plugin_display" onchange="this.form.submit();">
        <option value="compact" {if $plugin_diplay=='compact'}selected="selected"{/if}>{'Compact'|@translate}</option>
        <option value="complete" {if $plugin_display=='complete'}selected="selected"{/if}>{'Complete'|@translate}</option>
      </select>
      &nbsp;&nbsp;|&nbsp;&nbsp;
      <a class="deactivate_all">{'Deactivate'|@translate} {'all'|@translate}</a>
      {* &nbsp;&nbsp;|&nbsp;&nbsp;
      <a class="activate_all">{'Activate'|@translate} {'all'|@translate}</a> *}
    </form>
  </span>
  <h2>{'Plugins'|@translate}</h2>
</div>

{if isset($plugins)}

{assign var='field_name' value='null'}
{foreach from=$plugins item=plugin name=plugins_loop}
    
{if $plugin_order == 'state' AND $field_name != $plugin.STATE}
  {if $field_name != 'null'}</fieldset>{/if}
  <fieldset class="pluginBoxes pluginsByState">
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

{elseif $field_name == 'null'}
  <fieldset class="pluginBoxes pluginsByName">
  {assign var='field_name' value='not_null'}

{/if}

  {if not empty($plugin.AUTHOR)}
    {if not empty($plugin.AUTHOR_URL)}
      {assign var='author' value="<a href='%s'>%s</a>"|@sprintf:$plugin.AUTHOR_URL:$plugin.AUTHOR}
    {else}
      {assign var='author' value='<u>'|cat:$plugin.AUTHOR|cat:'</u>'}
    {/if}
  {/if}
   
  {if $plugin_display == 'complete'}
    <div id="{$plugin.ID}" class="pluginBox {$plugin.STATE}{if $plugin.INCOMPATIBLE} incompatible{/if}">
      <table>
        <tr>
          <td class="pluginBoxNameCell">
            {if $plugin.INCOMPATIBLE}<a class="warning" title="{'WARNING! This plugin does not seem to be compatible with this version of Piwigo.'|@translate|@escape:'html'}"></a>{/if}
            {$plugin.NAME}
          </td>
          <td>{$plugin.DESC}</td>
        </tr>
        <tr>
          <td>
          {if $plugin.STATE == 'active'}
            <a href="{$plugin.U_ACTION}&amp;action=deactivate">{'Deactivate'|@translate}</a>
            | <a href="{$plugin.U_ACTION}&amp;action=restore" class="plugin-restore" title="{'Restore default configuration. You will lost your plugin settings!'|@translate}" onclick="return confirm('{'Are you sure?'|@translate|@escape:'javascript'}');">{'Restore'|@translate}</a>

          {elseif $plugin.STATE == 'inactive'}
            <a href="{$plugin.U_ACTION}&amp;action=activate" {if $plugin.INCOMPATIBLE}class="incompatible"{/if}>{'Activate'|@translate}</a>
            | <a href="{$plugin.U_ACTION}&amp;action=delete" class="plugin-delete" onclick="return confirm('{'Are you sure?'|@translate|@escape:'javascript'}');">{'Delete'|@translate}</a>

          {elseif $plugin.STATE == 'missing'}
            <a href="{$plugin.U_ACTION}&amp;action=uninstall" class="plugin-delete" onclick="return confirm('{'Are you sure?'|@translate|@escape:'javascript'}');">{'Uninstall'|@translate}</a>

          {elseif $plugin.STATE == 'merged'}
            <a href="{$plugin.U_ACTION}&amp;action=delete" class="plugin-delete">{'Delete'|@translate}</a>
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
          
    <div id="{$plugin.ID}" class="pluginMiniBox {$plugin.STATE}{if $plugin.INCOMPATIBLE} incompatible{/if}">
      <div class="pluginMiniBoxNameCell">
        {if $plugin.INCOMPATIBLE}<span class="warning" title="{'WARNING! This plugin does not seem to be compatible with this version of Piwigo.'|@translate|@escape:'html'}"></span>{/if}
        <span class="pluginMiniBoxDesc" title="{if !empty($author)}{'By %s'|@translate|@sprintf:$author} | {/if}{'Version'|@translate} {$version}<br/>{$plugin.DESC|@escape:'html'}">{$plugin.NAME}</span>
      </div>
      <div class="pluginActions">
        <div>
        {if $plugin.STATE == 'active'}
          <a href="{$plugin.U_ACTION}&amp;action=deactivate">{'Deactivate'|@translate}</a>
          | <a href="{$plugin.U_ACTION}&amp;action=restore" class="plugin-restore" title="{'Restore default configuration. You will lost all your settings !'|@translate}" onclick="return confirm('{'Are you sure?'|@translate|@escape:'javascript'}');">{'Restore'|@translate}</a>

        {elseif $plugin.STATE == 'inactive'}
          <a href="{$plugin.U_ACTION}&amp;action=activate" {if $plugin.INCOMPATIBLE}class="incompatible"{/if}>{'Activate'|@translate}</a>
          | <a href="{$plugin.U_ACTION}&amp;action=delete"  class="plugin-delete"onclick="return confirm('{'Are you sure?'|@translate|@escape:'javascript'}');">{'Delete'|@translate}</a>

        {elseif $plugin.STATE == 'missing'}
          <a href="{$plugin.U_ACTION}&amp;action=uninstall" class="plugin-delete" onclick="return confirm('{'Are you sure?'|@translate|@escape:'javascript'}');">{'Uninstall'|@translate}</a>

        {elseif $plugin.STATE == 'merged'}
          <a href="{$plugin.U_ACTION}&amp;action=delete" class="plugin-delete">{'Delete'|@translate}</a>
        {/if}
        </div>
      </div>
    </div> {*<!-- pluginMiniBox -->*}
    
  {/if}
  
{/foreach}
  </fieldset>

{/if}
