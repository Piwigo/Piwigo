<h1>{lang:title_default}</h1>

<dl>
  <dt>{lang:PhpWebGallery version}</dt>
  <dd>
    <ul>
      <li>PhpWebGallery {PWG_VERSION}</li>
      <li><a href="{U_CHECK_UPGRADE}">{lang:Check for upgrade}</a></li>
    </ul>
  </dd>

  <dt>{lang:Environment}</dt>
  <dd>
    <ul>
      <li>{lang:Operating system}: {OS}</li>
      <li>PHP: {PHP_VERSION} (<a href="{U_PHPINFO}">{lang:Show info}</a>)</li>
      <li>MySQL: {MYSQL_VERSION}</li>
    </ul>
  </dd>

  <dt>{lang:Database}</dt>
  <dd>
    <ul>
      <li>
        {DB_ELEMENTS}
        <!-- BEGIN waiting -->
        (<a href="{waiting.URL}">{waiting.INFO}</a>)
        <!-- END waiting -->
      </li>
      <li>{DB_CATEGORIES}</li>
      <li>{DB_USERS}</li>
      <li>{DB_GROUPS}</li>
      <li>
        {DB_COMMENTS}
        <!-- BEGIN unvalidated -->
        (<a href="{unvalidated.URL}">{unvalidated.INFO}</a>)
        <!-- END unvalidated -->
      </li>
      <li>{DB_DATE}</li>
    </ul>
  </dd>

</dl>
