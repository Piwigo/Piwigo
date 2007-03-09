<!-- $Id$ -->
<h2>{lang:title_default}</h2>
<!-- BEGIN pwgmenu -->
<ul class="pwgmenu">
  <!-- Keep Doctype XHTML Strict acceptable even in Admin -->
  <!-- New window is open if Js available (Webmasters accept it usually) --> 
  <li><a href="{pwgmenu.HOME}" onclick="window.open(this.href, ''); return false;">{lang:HOME}</a></li>
  <li><a href="{pwgmenu.WIKI}" onclick="window.open(this.href, ''); return false;">{lang:WIKI / DOC}</a></li>
  <li><a href="{pwgmenu.FORUM}" onclick="window.open(this.href, ''); return false;">{lang:FORUM}</a></li>
  <li><a href="{pwgmenu.BUGS}" onclick="window.open(this.href, ''); return false;">{lang:BUGS}</a></li>
  <li><a href="{pwgmenu.EXTENSIONS}" onclick="window.open(this.href, ''); return false;">{lang:EXTENSIONS}</a></li>
</ul>   
<!-- END pwgmenu -->
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
      <li>PHP: {PHP_VERSION} (<a href="{U_PHPINFO}">{lang:Show info}</a>)  [{PHP_DATATIME}]</li>
      <li>MySQL: {MYSQL_VERSION} [{DB_DATATIME}]</li>
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

        <!-- BEGIN first_added -->
        ({first_added.DB_DATE})
        <!-- END first_added -->
      </li>
      <li>{DB_CATEGORIES} ({DB_IMAGE_CATEGORY})</li>
      <li>{DB_TAGS} ({DB_IMAGE_TAG})</li>
      <li>{DB_USERS}</li>
      <li>{DB_GROUPS}</li>
      <li>
        {DB_COMMENTS}
        <!-- BEGIN unvalidated -->
        (<a href="{unvalidated.URL}">{unvalidated.INFO}</a>)
        <!-- END unvalidated -->
      </li>
    </ul>
  </dd>

  <dt>{lang:Clock}</dt>
  <dd>
    <ul>
    </ul>
  </dd>

</dl>
