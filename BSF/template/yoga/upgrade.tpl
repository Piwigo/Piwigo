{* $Id$ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"  />
    <title>Piwigo : Upgrade to {$RELEASE}</title>
  </head>

  <body>
    {if isset($introduction)}
    <h1>Welcome to Piwigo upgrade page.</h1>

    <p>This page proposes to upgrade your database corresponding to your old
version of Piwigo to the current version. The upgrade assistant
thinks you are currently running a
<strong>release {$introduction.CURRENT_RELEASE}</strong> (or equivalent).</p>

    <p><a href="{$introduction.RUN_UPGRADE_URL}">Upgrade from release
{$introduction.CURRENT_RELEASE} to {$RELEASE}</a></p>
    {/if}

    {if isset($upgrade)}
    <h1>Upgrade from version {$upgrade.VERSION} to {$RELEASE}</h1>

    <p>Statistics</p>
    <ul>
      <li>total upgrade time : {$upgrade.TOTAL_TIME}</li>
      <li>total SQL time : {$upgrade.SQL_TIME}</li>
      <li>SQL queries : {$upgrade.NB_QUERIES}</li>
    </ul>

    <p>Upgrade informations</p>

    <ul>
      {foreach from=$infos item=info}
      <li>{$info}</li>
      {/foreach}
    </ul>

    {/if}
  </body>

</html>
