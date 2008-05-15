{* $Id$ *}
<h2>{'title_default'|@translate}</h2>
<dl>
  <dt>{'Piwigo version'|@translate}</dt>
  <dd>
    <ul>
      <li>Piwigo {$PWG_VERSION}</li>
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
