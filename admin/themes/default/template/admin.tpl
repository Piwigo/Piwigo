{known_script id="jquery.ui" src=$ROOT_URL|@cat:"themes/default/js/ui/packed/ui.core.packed.js" }
{known_script id="jquery.ui.accordion" src=$ROOT_URL|@cat:"themes/default/js/ui/packed/ui.accordion.packed.js" }
{known_script id="jquery.tipTip" src=$ROOT_URL|@cat:"themes/default/js/plugins/jquery.tipTip.minified.js" }

<script type="text/javascript">
jQuery().ready(function(){ldelim}
  jQuery('#menubar').accordion({ldelim}
    header: "dt.rdion",
    event: "click",
    active: {$ACTIVE_MENU}
  });
});
</script>

<div id="menubar">
  <div id="adminHome"><a href="{$U_ADMIN}">Administration Home</a></div>
  <dl class="first">
    <dt class="rdion"><span>{'Photos'|@translate}&nbsp;</span></dt>
    <dd>
      <ul>
        <li><a href="{$U_ADD_PHOTOS}">{'Add'|@translate}</a></li>
        <li><a href="{$U_WAITING}">{'Waiting'|@translate}</a></li>
        <li><a href="{$U_RATING}">{'Rating'|@translate}</a></li>
        <li><a href="{$U_TAGS}">{'Tags'|@translate}</a></li>
        <li><a href="{$U_CADDIE}">{'Caddie'|@translate}</a></li>
        <li><a href="{$U_RECENT_SET}">{'Recent pictures'|@translate}</a></li>
      </ul>
    </dd>
  </dl>
  <dl>
    <dt class="rdion"><span>{'Categories'|@translate}&nbsp;</span></dt>
    <dd>
      <ul>
        <li><a href="{$U_SITE_MANAGER}">{'Site manager'|@translate}</a></li>
        <li><a href="{$U_CAT_UPDATE}">{'Synchronize'|@translate}</a></li>
        <li><a href="{$U_CATEGORIES}">{'Manage'|@translate}</a></li>
        <li><a href="{$U_MOVE}">{'Move'|@translate}</a></li>
        <li><a href="{$U_CAT_OPTIONS}">{'Properties'|@translate}</a></li>
        <li><a href="{$U_PERMALINKS}">{'Permalinks'|@translate}</a></li>
      </ul>
    </dd>
  </dl>
  <dl>
    <dt class="rdion"><span>{'Users'|@translate}&nbsp;</span></dt>
    <dd>
      <ul>
        <li><a href="{$U_USERS}">{'Manage'|@translate}</a></li>
        <li><a href="{$U_GROUPS}">{'Groups'|@translate}</a></li>
        <li><a href="{$U_NOTIFICATION_BY_MAIL}">{'Notification'|@translate}</a></li>
      </ul>
    </dd>
  </dl>
  <dl>
    <dt class="rdion"><span>{'Plugins'|@translate}&nbsp;</span></dt>
    <dd>
      <ul>
        <li><a href="{$U_PLUGINS}">{'Manage'|@translate}</a></li>
      </ul>
      <div id="pluginsMenuSeparator"></div>
{if !empty($plugin_menu_items)}
      <ul class="scroll">
  {foreach from=$plugin_menu_items item=menu_item}
        <li><a href="{$menu_item.URL}">{$menu_item.NAME}</a></li>
  {/foreach}
      </ul>
{/if}
    </dd>
  </dl>
  <dl>
    <dt class="rdion"><span>{'Tools'|@translate}&nbsp;</span></dt>
    <dd>
      <ul>
        <li><a href="{$U_HISTORY_STAT}">{'History'|@translate}</a></li>
        <li><a href="{$U_THUMBNAILS}">{'Thumbnails'|@translate}</a></li>
        <li><a href="{$U_MAINTENANCE}">{'Maintenance'|@translate}</a></li>
        <li><a href="{$U_ADVANCED_FEATURE}">{'Advanced features'|@translate}</a></li>
      </ul>
    </dd>
  </dl>
  <dl class="last">
    <dt class="rdion"><span>{'Configuration'|@translate}&nbsp;</span></dt>
    <dd>
      <ul>
        <li><a href="{$U_CONFIG_GENERAL}">{'General'|@translate}</a></li>
        <li><a href="{$U_CONFIG_DISPLAY}">{'Default display'|@translate}</a></li>
        <li><a href="{$U_CONFIG_MENUBAR}">{'Menu'|@translate}</a></li>
        <li><a href="{$U_CONFIG_EXTENTS}">{'Templates'|@translate}</a></li>
        <li><a href="{$U_CONFIG_THEMES}">{'Themes'|@translate}</a></li>
      </ul>
    </dd>
  </dl>
</div> <!-- menubar -->

<div id="content" class="content">

  {if isset($TABSHEET)}
  {$TABSHEET}
  {/if}
  {if isset($U_HELP)}
  <ul class="HelpActions">
    <li><a href="{$U_HELP}" onclick="popuphelp(this.href); return false;" title="{'Help'|@translate}"><img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  {/if}
  
  {if isset($errors)}
  <div class="errors">
    <ul>
      {foreach from=$errors item=error}
      <li>{$error}</li>
      {/foreach}
    </ul>
  </div>
  {/if}

  {if isset($infos)}
  <div class="infos">
    <ul>
      {foreach from=$infos item=info}
      <li>{$info}</li>
      {/foreach}
    </ul>
  </div>
  {/if}

  {$ADMIN_CONTENT}
</div>
