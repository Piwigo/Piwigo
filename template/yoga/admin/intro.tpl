{* $Id$ *}
<h2>{'title_default'|@translate}</h2>
{if isset($pwgmenu)}
<ul class="pwgmenu">
  <!-- Keep Doctype XHTML Strict acceptable even in Admin -->
  <!-- New window is open if Js available (Webmasters accept it usually) --> 
  <li><a href="{$pwgmenu.HOME}" onclick="window.open(this.href, ''); return false;">{'HOME'|@translate}</a></li>
  <li><a href="{$pwgmenu.WIKI}" onclick="window.open(this.href, ''); return false;">{'WIKI / DOC'|@translate}</a></li>
  <li><a href="{$pwgmenu.FORUM}" onclick="window.open(this.href, ''); return false;">{'FORUM'|@translate}</a></li>
  <li><a href="{$pwgmenu.BUGS}" onclick="window.open(this.href, ''); return false;">{'BUGS'|@translate}</a></li>
  <li><a href="{$pwgmenu.EXTENSIONS}" onclick="window.open(this.href, ''); return false;">{'EXTENSIONS'|@translate}</a></li>
</ul>   
{/if}
<dl>
  <dt>{'PhpWebGallery version'|@translate}</dt>
  <dd>
    <ul>
      <li>PhpWebGallery {$PWG_VERSION}</li>
      <li><a href="{$U_CHECK_UPGRADE}">{'Check for upgrade'|@translate}</a></li>
    </ul>
  </dd>

  <dt>{'Environment'|@translate}</dt>
  <dd>
    <ul>
      <li>{'Operating system'|@translate}: {$OS}</li>
      <li>PHP: {$PHP_VERSION} (<a href="{$U_PHPINFO}">{'Show info'|@translate}</a>)  [{$PHP_DATATIME}]</li>
      <li>MySQL: {$MYSQL_VERSION} [{$DB_DATATIME}]</li>
    </ul>
  </dd>

  <dt>{'Database'|@translate}</dt>
  <dd>
    <ul>
      <li>
        {$DB_ELEMENTS}
        {if isset($waiting)}
        (<a href="{$waiting.URL}">{$waiting.INFO}</a>)
        {/if}

        {if isset($first_added)}
        ({$first_added.DB_DATE})
        {/if}
      </li>
      <li>{$DB_CATEGORIES} ({$DB_IMAGE_CATEGORY})</li>
      <li>{$DB_TAGS} ({$DB_IMAGE_TAG})</li>
      <li>{$DB_USERS}</li>
      <li>{$DB_GROUPS}</li>
      <li>
        {$DB_COMMENTS}
        {if isset($unvalidated)}
        (<a href="{$unvalidated.URL}">{$unvalidated.INFO}</a>)
        {/if}
      </li>
    </ul>
  </dd>
</dl>
