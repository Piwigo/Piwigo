{combine_script id='ajax' load='footer' path='admin/themes/default/js/maintenance.js'}
{combine_script id='activated_plugin_list' load='footer' path='admin/themes/default/js/maintenance_env.js'}
{combine_css path="admin/themes/default/fontello/css/animation.css" order=10}
 {* order 10 is required, see issue 1080 *}
{footer_script}
const no_time_elapsed = "{"right now"|@translate}";
const no_active_plugin = "{"No plugin activated"|@translate}";
const error_occured = "{"an error happened"|@translate}";
const unit_MB = "{"%s MB"|@translate}"
{/footer_script}

<fieldset id="environment">
  <legend><span class="icon-television icon-red"></span> {'Environment'|@translate}</legend>
  <ul style="font-weight:bold">
    <li><a href="{$PHPWG_URL}" class="externalLink">Piwigo</a> {$PWG_VERSION} <a href="{$U_CHECK_UPGRADE}&tab=env" class="icon-arrows-cw">{'Check for upgrade'|@translate}</a></li>
{if isset($INSTALLED_ON)}
    <li>{'Installed on %s, %s'|translate:$INSTALLED_ON:$INSTALLED_SINCE}</li>
{/if}
    <li>{'Operating system'|@translate}: {$OS}</li>
    <li>PHP: {$PHP_VERSION} (<a href="{$U_PHPINFO}" class="externalLink">{'Show info'|@translate}</a>)  [{$PHP_DATATIME}]</li>
    <li>{$DB_ENGINE}: {$DB_VERSION} [{$DB_DATATIME}]</li>
    {if isset($GRAPHICS_LIBRARY)}
    <li>{'Graphics Library'|@translate}: {$GRAPHICS_LIBRARY}</li>
    {/if}
    <li>     
      <span class="cache-size-text">{'Cache size'|@translate}</span>
      <span class="cache-size-value">
      {if isset($cache_sizes)}
        {round($cache_sizes[0]['value']/1024/1024, 2)} Mo
      {else}
        {'N/A'|translate}
      {/if}
      </span>
      <span class="cache-lastCalculated-text">{if $time_elapsed_since_last_calc}&ThickSpace;{'calculated'|@translate}{/if}</span>
      <span class="cache-lastCalculated-value">{if $time_elapsed_since_last_calc} {$time_elapsed_since_last_calc} {else} &ThickSpace;{"never calculated"|@translate} {/if}</span>
      <a class="refresh-cache-size"><span class="refresh-icon icon-arrows-cw"></span>{'Refresh'|@translate}</a>
    </li>
  </ul>
</fieldset>

<fieldset id="pluginList">
  <legend><span class="icon-puzzle icon-green"></span> {'Activated plugin list'|@translate} <span class="badge-number"></span></legend>
  <ul style="font-weight:bold">
    <i class="icon-spin6 animate-spin"></i>
  </ul>
</fieldset>

<style>

.cache-size-value {
  background: transparent;
  padding: 0;
}

.cache-size-text, .cache-size-value {
  font-size: 13px;
}

.cache-lastCalculated-text, .cache-lastCalculated-value {
  font-size: 10px;
}

.badge-number {
  color:white;
}
</style>