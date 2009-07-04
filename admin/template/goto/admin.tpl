{* $Id$ *}
{known_script id="jquery.ui" src=$ROOT_URL|@cat:"template-common/lib/ui/ui.core.packed.js" }
{known_script id="jquery.ui.accordion" src=$ROOT_URL|@cat:"template-common/lib/ui/ui.accordion.packed.js" }

<script type="text/javascript">
jQuery().ready(function(){ldelim}
  jQuery('#menubar').accordion({ldelim}
    header: "dt.rdion",
    event: "click",
    active: {$U_ACTIVE_MENU|default:$themeconf.selected_admin_menu}
  });
});
</script>

<div id="menubar">
  <dl class="first">
    <dt class="rdion"><span>{'Links'|@translate}&nbsp;</span></dt>
    <dd>
      <ul>
        <li><a href="{$U_RETURN}">{'home'|@translate}</a></li>
        <li><a href="{$U_FAQ}">{'instructions'|@translate}</a></li>
        <li><a href="{$U_ADMIN}" title="{'hint_admin'|@translate}">{'admin'|@translate}</a></li>
        <li><a href="{$U_LOGOUT}">{'logout'|@translate}</a></li>
{if isset($pwgmenu)}
        <li class="external"><a class="external" href="{$pwgmenu.WIKI}" onclick="window.open(this.href, ''); 
          return false;">{'WIKI / DOC'|@translate}</a></li>
        <li class="external"><a class="external" href="{$pwgmenu.FORUM}" onclick="window.open(this.href, '');
          return false;">{'FORUM'|@translate}</a></li>
{/if}
        
      </ul>
    </dd>
  </dl>
  <dl>
    <dt class="rdion"><span>{'config'|@translate}&nbsp;</span></dt>
    <dd>
      <ul>
        <li><a href="{$U_CONFIG_GENERAL}">{'conf_general'|@translate}</a></li>
        <li><a href="{$U_CONFIG_DISPLAY}">{'conf_display'|@translate}</a></li>
        <li><a href="{$U_CONFIG_MENUBAR}">{'title_menu'|@translate}</a></li>
        <li><a href="{$U_CONFIG_EXTENTS}">{'conf_extents'|@translate}</a></li>
      </ul>
    </dd>
  </dl>
  <dl>
    <dt class="rdion"><span>{'Categories'|@translate}&nbsp;</span></dt>
    <dd>
      <ul>
        <li><a href="{$U_SITE_MANAGER}">{'Site manager'|@translate}</a></li>
        <li><a href="{$U_CAT_UPDATE}">{'update'|@translate}</a></li>
        <li><a href="{$U_CATEGORIES}">{'manage'|@translate}</a></li>
        <li><a href="{$U_MOVE}">{'Move'|@translate}</a></li>
        <li><a href="{$U_CAT_OPTIONS}">{'cat_options_title'|@translate}</a></li>
        <li><a href="{$U_PERMALINKS}">{'Permalinks'|@translate}</a></li>
      </ul>
    </dd>
  </dl>
  <dl>
    <dt class="rdion"><span>{'Pictures'|@translate}&nbsp;</span></dt>
    <dd>
      <ul>
        <li><a href="{$U_WAITING}">{'waiting'|@translate}</a></li>
        <li><a href="{$U_THUMBNAILS}">{'thumbnails'|@translate}</a></li>
        <li><a href="{$U_RATING}">{'Rating'|@translate}</a></li>
        <li><a href="{$U_TAGS}">{'Tags'|@translate}</a></li>
        <li><a href="{$U_CADDIE}">{'Caddie'|@translate}</a></li>
        <li><a href="{$U_RECENT_SET}">{'recent_pics_cat'|@translate}</a></li>
      </ul>
    </dd>
  </dl>
  <dl>
    <dt class="rdion"><span>{'identification'|@translate}&nbsp;</span></dt>
    <dd>
      <ul>
        <li><a href="{$U_USERS}">{'users'|@translate}</a></li>
        <li><a href="{$U_GROUPS}">{'groups'|@translate}</a></li>
        <li><a href="{$U_NOTIFICATION_BY_MAIL}">{'nbm_item_notification'|@translate}</a></li>
      </ul>
    </dd>
  </dl>
  <dl>
    <dt class="rdion"><span>{'special_admin_menu'|@translate}&nbsp;</span></dt>
    <dd>
      <ul>
        <li><a href="{$U_HISTORY_STAT}">{'History'|@translate}</a></li>
        <li><a href="{$U_MAINTENANCE}">{'Maintenance'|@translate}</a></li>
        <li><a href="{$U_ADVANCED_FEATURE}">{'Advanced_features'|@translate}</a></li>
        <li><a href="{$U_PLUGINS}">{'Plugins'|@translate}</a>
        {if !empty($plugin_menu_items)}
          <ul class="scroll">
          {foreach from=$plugin_menu_items item=menu_item}
            <li><a href="{$menu_item.URL}">{$menu_item.NAME}</a></li>
          {/foreach}
          </ul>
        {/if}
        </li>
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
