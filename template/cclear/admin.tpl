<!-- $Id$ -->

<div id="menubar">
  <dl>
    <dt>{lang:links}</dt>
    <dd>
      <ul>
	<li><a href="{U_RETURN}">{lang:home}</a></li>
      </ul>
    </dd>

    <dt>{lang:general}</dt>
    <dd>
      <ul>
	<li><a href="{U_FAQ}">{lang:instructions}</a></li>
	<li><a href="{U_SITES}">{lang:remote_sites}</a></li>
	<li><a href="{U_HISTORY}">{lang:history}</a></li>
	<li><a href="{U_CAT_UPDATE}">{lang:update}</a></li>
	<li><a href="{U_MAINTENANCE}">{lang:Maintenance}</a></li>
      </ul>
    </dd>

    <dt>{lang:config}</dt>
    <dd>
      <ul>
	<li><a href="{U_CONFIG_GENERAL}">{lang:general}</a></li>
	<li><a href="{U_CONFIG_COMMENTS}">{lang:comments}</a></li>
	<li><a href="{U_CONFIG_DISPLAY}">{lang:conf_default}</a></li>
	<li><a href="{U_CONFIG_UPLOAD}">{lang:upload}</a></li>
	<li><a href="{U_CONFIG_SESSION}">{lang:conf_cookie}</a></li>
	<li><a href="{U_CONFIG_METADATA}">{lang:metadata}</a></li>
      </ul>
    </dd>

    <dt>{lang:categories}</dt>
    <dd>
      <ul>
	<li><a href="{U_CATEGORIES}">{lang:manage}</a></li>
	<li><a href="{U_CAT_UPLOAD}">{lang:upload}</a></li>
	<li><a href="{U_CAT_COMMENTS}">{lang:comments}</a></li>
	<li><a href="{U_CAT_VISIBLE}">{lang:lock}</a></li>
	<li><a href="{U_CAT_STATUS}">{lang:cat_security}</a></li>
	<!-- BEGIN representative -->
	<li><a href="{representative.URL}">{lang:Representative}</a></li>
	<!-- END representative -->
      </ul>
    </dd>

    <dt>{lang:pictures}</dt>
    <dd>
      <ul>
	<li><a href="{U_WAITING}">{lang:waiting}</a></li>
	<li><a href="{U_THUMBNAILS}">{lang:thumbnails}</a></li>
	<li><a href="{U_COMMENTS}">{lang:comments}</a></li>
	<li><a href="{U_CADDIE}">{lang:Caddie}</a></li>
      </ul>
    </dd>

    <dt>{lang:identification}</dt>
    <dd>
      <ul>
	<li><a href="{U_USERS}">{lang:users}</a></li>
	<li><a href="{U_GROUPS}">{lang:groups}</a></li>
      </ul>
    </dd>
  </dl>
</div> <!-- menubar -->

<div id="content">
  <!-- BEGIN errors -->
  <div id="errors">
    <ul>
      <!-- BEGIN error -->
      <li>{errors.error.ERROR}</li>
      <!-- END error -->
    </ul>
  </div>
  <!-- END errors -->

  <!-- BEGIN infos -->
  <div id="infos">
    <ul>
      <!-- BEGIN info -->
      <li>{infos.info.INFO}</li>
      <!-- END info -->
    </ul>
  </div>
  <!-- END infos -->

  {ADMIN_CONTENT}
</div>
