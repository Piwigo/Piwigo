{* $Id$ *}
<div id="menubar">
  <dl class="first">
    <dt class="rdion">{'Links'|@translate}</dt>
    <dd>
      <ul>
        <li><a href="{$U_RETURN}">{'home'|@translate}</a></li>
        <li><a href="{$U_FAQ}">{'instructions'|@translate}</a></li>
        <li><a href="{$U_ADMIN}" title="{'hint_admin'|@translate}">{'admin'|@translate}</a></li>
{if isset($pwgmenu)}
        <li class="external"><a href="{$pwgmenu.WIKI}" onclick="window.open(this.href, ''); 
          return false;">{'WIKI / DOC'|@translate}</a></li>
        <li class="external"><a href="{$pwgmenu.FORUM}" onclick="window.open(this.href, '');
          return false;">{'FORUM'|@translate}</a></li>
{/if}
        
      </ul>
    </dd>
  </dl>
  <dl>
    <dt class="rdion">{'config'|@translate}</dt>
    <dd>
      <ul>
        <li><a href="{$U_CONFIG_GENERAL}">{'conf_general'|@translate}</a></li>
        <li><a href="{$U_CONFIG_DISPLAY}">{'conf_display'|@translate}</a></li>
      </ul>
    </dd>
  </dl>
  <dl>
    <dt class="rdion">{'Categories'|@translate}</dt>
    <dd>
      <ul>
        <li><a href="{$U_SITE_MANAGER}">{'Site manager'|@translate}</a></li>
        <li><a href="{$U_CAT_UPDATE}">{'update'|@translate}</a><br />&nbsp;</li>
        <li><a href="{$U_CATEGORIES}">{'manage'|@translate}</a></li>
        <li><a href="{$U_MOVE}">{'Move'|@translate}</a></li>
        <li><a href="{$U_CAT_OPTIONS}">{'cat_options_title'|@translate}</a></li>
        <li><a href="{$U_PERMALINKS}">{'Permalinks'|@translate}</a></li>
      </ul>
    </dd>
  </dl>
  <dl>
    <dt class="rdion">{'Pictures'|@translate}</dt>
    <dd>
      <ul>
        <li><a href="{$U_WAITING}">{'waiting'|@translate}</a></li>
        <li><a href="{$U_THUMBNAILS}">{'thumbnails'|@translate}</a></li>
        <li><a href="{$U_RATING}">{'Rating'|@translate}</a></li>
        <li><a href="{$U_TAGS}">{'Tags'|@translate}</a></li>
        <li><a href="{$U_CADDIE}">{'Caddie'|@translate}</a></li>
      </ul>
    </dd>
  </dl>
  <dl>
    <dt class="rdion">{'identification'|@translate}</dt>
    <dd>
      <ul>
        <li><a href="{$U_USERS}">{'users'|@translate}</a></li>
        <li><a href="{$U_GROUPS}">{'groups'|@translate}</a></li>
        <li><a href="{$U_NOTIFICATION_BY_MAIL}">{'nbm_item_notification'|@translate}</a></li>
      </ul>
    </dd>
  </dl>
  <dl>
    <dt class="rdion">{'special_admin_menu'|@translate}</dt>
    <dd>
      <ul>
        <li><a href="{$U_HISTORY_STAT}">{'History'|@translate}</a></li>
        <li><a href="{$U_MAINTENANCE}">{'Maintenance'|@translate}</a></li>
        <li><a href="{$U_ADVANCED_FEATURE}">{'Advanced_features'|@translate}</a></li>
        {if isset($U_WS_CHECKER) }
        <li><a href="{$U_WS_CHECKER}">{'web_services'|@translate}</a></li>
        {/if}
        <li>
          {'Plugins'|@translate}
          <ul>
{foreach from=$plugin_menu_items item=menu_item}
      <li><a href="{$menu_item.URL}">{$menu_item.NAME}</a></li>
{/foreach}
          </ul>
        </li>
      </ul>
    </dd>
  </dl>
</div> <!-- menubar -->

<div id="content" class="content">
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
