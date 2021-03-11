<fieldset id="environment">
  <legend><span class="icon-television icon-red"></span> {'Environment'|@translate}</legend>
  <ul style="font-weight:bold">
    <li><a href="{$PHPWG_URL}" class="externalLink">Piwigo</a> {$PWG_VERSION} <a href="{$U_CHECK_UPGRADE}" class="icon-arrows-cw">{'Check for upgrade'|@translate}</a></li>
    <li>{'Operating system'|@translate}: {$OS}</li>
    <li>PHP: {$PHP_VERSION} (<a href="{$U_PHPINFO}" class="externalLink">{'Show info'|@translate}</a>)  [{$PHP_DATATIME}]</li>
    <li>{$DB_ENGINE}: {$DB_VERSION} [{$DB_DATATIME}]</li>
    {if isset($GRAPHICS_LIBRARY)}
    <li>{'Graphics Library'|@translate}: {$GRAPHICS_LIBRARY}</li>
    {/if}
    <li>     
      <span class="cache-size-text">Taille du cache: </span>
      <span class="cache-size-value">999 Go</span>
      <span class="cache-lastCalculated-text">calculé il y a</span>
      <span class="cache-lastCalculated-value">42 mois</span>
      <a><span class="icon-arrows-cw"></span>Actulaliser</a>
    </li>
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

</style>