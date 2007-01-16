<!-- $Id$ -->
<div id="menubar">
  <dl>
    <dt>{lang:links}</dt>
    <dd>
      <ul>
        <li><a href="{U_RETURN}">{lang:home}</a></li>
        <li><a href="{U_ADMIN}" title="{L_ADMIN_HINT}">{L_ADMIN}</a></li>
      </ul>
    </dd>
  </dl>
  <dl>
    <dt>{lang:general}</dt>
    <dd>
      <ul>
        <li><a href="{U_FAQ}">{lang:instructions}</a></li>
        <li><a href="{U_SITE_MANAGER}">{lang:Site manager}</a></li>

        <li>
          {lang:history}
          <ul>
            <li><a href="{U_HISTORY_STAT}">{lang:Statistics}</a></li>
            <li><a href="{U_HISTORY_SEARCH}">{lang:Search}</a></li>
          </ul>
        </li>

        <li><a href="{U_CAT_UPDATE}">{lang:update}</a></li>
      </ul>
    </dd>
  </dl>
  <dl>
    <dt>{lang:config}</dt>
    <dd>
      <ul>
        <li><a href="{U_CONFIG_GENERAL}">{lang:general}</a></li>
        <li><a href="{U_CONFIG_COMMENTS}">{lang:comments}</a></li>
        <li><a href="{U_CONFIG_DISPLAY}">{lang:conf_default}</a></li>
      </ul>
    </dd>
  </dl>
  <dl>
    <dt>{lang:special_admin_menu}</dt>
    <dd>
      <ul>
        <li><a href="{U_MAINTENANCE}">{lang:Maintenance}</a></li>
        <li><a href="{U_ADVANCED_FEATURE}">{lang:Advanced_features}</a></li>
        <li><a href="{U_NOTIFICATION_BY_MAIL}">{lang:nbm_item_notification}</a></li>
        <!-- BEGIN web_services -->
        <li><a href="{web_services.U_WS_CHECKER}">{lang:web_services}</a></li>
        <!-- END web_services -->
      </ul>
    </dd>
  </dl>
  <dl>
    <dt>{lang:Categories}</dt>
    <dd>
      <ul>
        <li><a href="{U_CATEGORIES}">{lang:manage}</a></li>
        <li><a href="{U_MOVE}">{lang:Move}</a></li>
        <li><a href="{U_CAT_UPLOAD}">{lang:upload}</a></li>
        <li><a href="{U_CAT_COMMENTS}">{lang:comments}</a></li>
        <li><a href="{U_CAT_VISIBLE}">{lang:lock}</a></li>
        <li><a href="{U_CAT_STATUS}">{lang:cat_security}</a></li>
        <!-- BEGIN representative -->
        <li><a href="{representative.URL}">{lang:Representative}</a></li>
        <!-- END representative -->
      </ul>
    </dd>
  </dl>
  <dl>
    <dt>{lang:pictures_menu}</dt>
    <dd>
      <ul>
        <li><a href="{U_WAITING}">{lang:waiting}</a></li>
        <li><a href="{U_THUMBNAILS}">{lang:thumbnails}</a></li>
        <li><a href="{U_COMMENTS}">{lang:comments}</a></li>
        <li><a href="{U_RATING}">{lang:Rating}</a></li>
        <li><a href="{U_CADDIE}">{lang:Caddie}</a></li>
        <li><a href="{U_TAGS}">{lang:Tags}</a></li>
      </ul>
    </dd>
  </dl>
  <dl>
    <dt>{lang:identification}</dt>
    <dd>
      <ul>
        <li><a href="{U_USERS}">{lang:users}</a></li>
        <li><a href="{U_GROUPS}">{lang:groups}</a></li>
      </ul>
    </dd>
  </dl>
  <dl>
    <dt>{lang:Plugins}</dt>
    <dd>
      <ul>
<!-- BEGIN plugin_menu -->
<!-- BEGIN menu_item -->
      <li><a href="{plugin_menu.menu_item.URL}">{plugin_menu.menu_item.NAME}</a></li>
<!-- END menu_item -->
<!-- END plugin_menu -->
      </ul>
    </dd>
  </dl>
</div> <!-- menubar -->

<div id="content">
  <!-- BEGIN errors -->
  <div class="errors">
    <ul>
      <!-- BEGIN error -->
      <li>{errors.error.ERROR}</li>
      <!-- END error -->
    </ul>
  </div>
  <!-- END errors -->

  <!-- BEGIN infos -->
  <div class="infos">
    <ul>
      <!-- BEGIN info -->
      <li>{infos.info.INFO}</li>
      <!-- END info -->
    </ul>
  </div>
  <!-- END infos -->

  {ADMIN_CONTENT}
</div>
