{combine_script id='jquery.ui.accordion' load='header'}{*we load in the header because the accordion is on every admin page and usually all admin pages use the same header combined script but not the same footer script*}
{footer_script require='jquery.ui.accordion'}
jQuery(document).ready(function(){ldelim}
	jQuery('#menubar').accordion({ldelim}
		header: "dt.rdion",
		event: "click",
		heightStyle: "content",
		active: {$ACTIVE_MENU}
	});
});
{/footer_script}

{combine_script id='raphael' load='async' path='themes/default/js/raphael.js'}
{combine_script id='raphael.menu_icons' require='raphael' load='async' path='admin/themes/default/js/menu_icons.js'}

<div id="menubar">
  <div id="adminHome"><a href="{$U_ADMIN}">{'Administration Home'|@translate}</a></div>
  <dl class="first">
    <dt class="rdion"><span id="menubarPhotos">&nbsp;{'Photos'|@translate}&nbsp;</span></dt>
    <dd>
      <ul>
        <li><a href="{$U_ADD_PHOTOS}">{'Add'|@translate}</a></li>
        <li><a href="{$U_RATING}">{'Rating'|@translate}</a></li>
        <li><a href="{$U_TAGS}">{'Tags'|@translate}</a></li>
        <li><a href="{$U_RECENT_SET}">{'Recent photos'|@translate}</a></li>
        <li><a href="{$U_BATCH}">{'Batch Manager'|@translate}</a></li>
{if $NB_PHOTOS_IN_CADDIE > 0}
        <li><a href="{$U_CADDIE}">{'Caddie'|@translate}<span class="adminMenubarCounter">{$NB_PHOTOS_IN_CADDIE}</span></a></li>
{/if}
      </ul>
    </dd>
  </dl>
  <dl>
    <dt class="rdion"><span id="menubarAlbums">&nbsp;{'Albums'|@translate}&nbsp;</span></dt>
    <dd>
      <ul>
        <li><a href="{$U_CATEGORIES}">{'Manage'|@translate}</a></li>
        <li><a href="{$U_CAT_OPTIONS}">{'Properties'|@translate}</a></li>
      </ul>
    </dd>
  </dl>
  <dl>
    <dt class="rdion"><span id="menubarUsers">&nbsp;{'Users'|@translate}&nbsp;</span></dt>
    <dd>
      <ul>
        <li><a href="{$U_USERS}">{'Manage'|@translate}</a></li>
        <li><a href="{$U_GROUPS}">{'Groups'|@translate}</a></li>
        <li><a href="{$U_NOTIFICATION_BY_MAIL}">{'Notification'|@translate}</a></li>
      </ul>
    </dd>
  </dl>
  <dl>
    <dt class="rdion"><span id="menubarPlugins">&nbsp;{'Plugins'|@translate}&nbsp;</span></dt>
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
    <dt class="rdion"><span id="menubarTools">&nbsp;{'Tools'|@translate}&nbsp;</span></dt>
    <dd>
      <ul>
{if $ENABLE_SYNCHRONIZATION}
        <li><a href="{$U_CAT_UPDATE}">{'Synchronize'|@translate}</a></li>
        <li><a href="{$U_SITE_MANAGER}">{'Site manager'|@translate}</a></li>
{/if}
        <li><a href="{$U_HISTORY_STAT}">{'History'|@translate}</a></li>
        <li><a href="{$U_MAINTENANCE}">{'Maintenance'|@translate}</a></li>
{if isset($U_PENDING_COMMENTS)}
        <li><a href="{$U_PENDING_COMMENTS}">{'Pending Comments'|@translate}</a></li>
{/if}
        <li><a href="{$U_UPDATES}">{'Updates'|@translate}</a></li>
      </ul>
    </dd>
  </dl>
  <dl class="last">
    <dt class="rdion"><span id="menubarConfiguration">&nbsp;{'Configuration'|@translate}&nbsp;</span></dt>
    <dd>
      <ul>
        <li><a href="{$U_CONFIG_GENERAL}">{'Options'|@translate}</a></li>
        <li><a href="{$U_CONFIG_MENUBAR}">{'Menu Management'|@translate}</a></li>
        <li><a href="{$U_CONFIG_EXTENTS}">{'Templates'|@translate}</a></li>
        <li><a href="{$U_CONFIG_LANGUAGES}">{'Languages'|@translate}</a></li>
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
	{combine_script id='core.scripts' load='async' path='themes/default/js/scripts.js'}
  <ul class="HelpActions">
    <li><a href="{$U_HELP}" onclick="popuphelp(this.href); return false;" title="{'Help'|@translate}"><img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/help.png" alt="(?)"></a></li>
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

  {if isset($warnings)}
  <div class="warnings">
    <ul>
      {foreach from=$warnings item=warning}
      <li>{$warning}</li>
      {/foreach}
    </ul>
  </div>
  {/if}

  {$ADMIN_CONTENT}
</div>
