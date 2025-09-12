{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

{footer_script}
jQuery.fn.lightAccordion = function(options) {
  var settings = $.extend({
    header: 'dt',
    content: 'dd',
    active: 0
  }, options);
  
  return this.each(function() {
    var self = jQuery(this);
    
    var contents = self.find(settings.content),
        headers = self.find(settings.header);
    
    contents.not(contents[settings.active]).hide();
  
    self.on('click', settings.header, function() {
        var content = jQuery(this).next(settings.content);
        content.slideDown();
        contents.not(content).slideUp();
    });
  });
};

$('#menubar').lightAccordion({
  active: {$ACTIVE_MENU}
});

/* in case we have several infos/errors/warnings display bullets */
jQuery(document).ready(function() {
  var eiw = ["infos","erros","warnings", "messages"];

  for (var i = 0; i < eiw.length; i++) {
    var boxType = eiw[i];

    if (jQuery("."+boxType+" ul li").length > 1) {
      jQuery("."+boxType+" ul li").css("list-style-type", "square");
      jQuery("."+boxType+" .eiw-icon").css("margin-right", "20px");
    }
  }

  if (jQuery('h2').length > 0) {
    jQuery('h1').html(jQuery('h2').html());
  }
});

{* Variables for menubar script *}
let username = '{$USERNAME}'

{/footer_script}

{strip}
{combine_css path="admin/themes/default/fontello/css/fontello.css" order=-10}
{assign "theme_id" ""}
{foreach from=$themes item=theme}
  {assign "theme_id" $theme.id}
{/foreach}

{combine_script id='jquery' path='themes/default/js/jquery.min.js'}
{/strip}


<div id="menubar" class="enlarged">
  <div>
  
    <div id="user-actions-container" class="menu-block">
      <div class="user-actions">
        <div class="username-container">
          <div class="user-container-initials-wrapper">
            <span class="icon-green"><!-- initials --></span>
          </div>
          <span id="menu-username" class="reduced-hidden">{$USERNAME}</span>

        </div>
                  <i class="icon-left-open reduced-hidden"></i>
     
        <div class="user-sub-link-container">
          <div class="user-infos">
            <p id="dropdown-username" class="username">{$USERNAME}</p>
            <p class="">{$USER_EMAIL}</p>
          </div>
          <p  class="tiptip" title="{'Switch to clear or dark colors for administration'|translate}">
            {if $theme_id eq "clear"}
                  <i class="icon-moon-inv"></i>
            {elseif $theme_id eq "roma"}
                  <i class="icon-sun-inv"></i>
            {/if}
              Apperance
          </p>
            <span>
              <div id="apperance-switch">

              <a href="{$U_CHANGE_THEME_AUTO}">
                <label class="font-checkbox no-bold {if $ADMIN_THEME=="auto"}active{/if}">
                  <span class="icon-circle-empty"></span>
                  <span type="radio" name="apperance" value="apperance-auto" checked="checked">
                    Automatic
                  </span>
                </label>
              </a>
              
              <a {if $ADMIN_THEME!="clear"}href="{$U_CHANGE_THEME_LIGHT}" {/if}>
                <label class="font-checkbox no-bold {if $ADMIN_THEME=="clear"}active{/if}">
                  <span class="icon-circle-empty"></span>
                  <span type="radio" name="apperance" value="apperance-light">
                    Light
                  </span>
                </label>
              </a>

              <a {if $ADMIN_THEME!="roma"}href="{$U_CHANGE_THEME_DARK}" {/if}>                
                <label class="font-checkbox no-bold {if $ADMIN_THEME=="roma"}active{/if}">
                  <span class="icon-circle-empty"></span>
                  <span type="radio" name="apperance" value="apperance-dark">
                    Dark
                  </span>
                </label>
              </a>

              </div>
            </span>
          <a href="{$U_PROFILE}"><i class="icon-brush"></i>Edit my profil</a>
          <a href="{$U_FAQ}"><i class="icon-help-circled"></i>Help me</a>
          <a id="log-out"><i class="icon-logout"></i>Log out</a>
        </div>
      </div>
      <div>
        <a class="gallery-link mobile-hidden" href="{$U_RETURN}"><i class="icon-home"></i><span class="reduced-hidden">{'Gallery'|@translate}&nbsp;</span></a>
      </div>
    </div>

    <div id="admin-pages" class="menu-block">
      <div id="adminHome" class="page-link {if "dashboard" == $ACTIVE_PAGE}active{/if}">
        <span class="active-border"></span>
        <a href="{$U_ADMIN}"><i class="icon-television"></i><span class="reduced-hidden">{'Dashboard'|@translate}&nbsp;</span></a>
        <span class="hover"></span>
      </div>
      
      {* Photos section *}
      <div class="page-link {if "photo" == $ACTIVE_PAGE}active{/if}" title="{'Photos'|translate}">
        <span>
          <i class="icon-picture"></i><span class="reduced-hidden">{'Photos'|@translate}&nbsp;</span>
          <span class="hover">
            <i class="icon-down-open"></i>
          </span>
        </span>
        <div class="sub-link-container">
          {* Photos sub-link prefilter start *}
          <a href="{$U_ADD_PHOTOS}" class="sub-link"><i class="icon-plus-circled"></i>{'Add'|@translate}</a>
{if $SHOW_RATING}
          <a href="{$U_RATING}" class="sub-link"><i class="icon-star"></i>{'Rating'|@translate}</a>
{/if}
          <a href="{$U_TAGS}" class="sub-link"><i class="icon-tags"></i>{'Tags'|@translate}</a>
          <a href="{$U_RECENT_SET}" class="sub-link"><i class="icon-clock"></i>{'Recent photos'|@translate}</a>
          <a href="{$U_BATCH}" class="sub-link"><i class="icon-th"></i>{'Batch Manager'|@translate}</a>
{if $NB_PHOTOS_IN_CADDIE > 0}
          <a href="{$U_CADDIE}" class="sub-link"><i class="icon-flag"></i>{'Pinned'|@translate}<span class="adminMenubarCounter">{$NB_PHOTOS_IN_CADDIE}</span></a>
{/if}
{if $NB_ORPHANS > 0}
          <a href="{$U_ORPHANS}" class="sub-link"><i class="icon-heart-broken"></i>{'Orphans'|@translate}<span class="adminMenubarCounter">{$NB_ORPHANS}</span></a>
{/if}
          {* Photos sub-link prefilter end *}
        </div>
      </div>

      {* Albums section *}
      <div class="page-link {if "album" == $ACTIVE_PAGE}active{/if}">
        <span>
          <i class="icon-sitemap"></i><span class="reduced-hidden">{'Albums'|@translate}&nbsp;</span>
          <span class="hover">
            <i class="icon-down-open"></i>
          </span>
        </span>
        <div class="sub-link-container">
          {* Albums sub-link prefilter start *}
          <a href="{$U_ALBUMS}" class="sub-link"><i class="icon-folder-open"></i>{'Manage'|@translate}</a>
          <a href="{$U_CAT_OPTIONS}" class="sub-link"><i class="icon-pencil"></i>{'Properties'|@translate}</a>
          <a href="{$U_PERMALINKS}" class="sub-link"><i class="icon-link-1"></i>{'Permalinks'|@translate}</a>
          {* Albums sub-link prefilter end *}
        </div>
      </div>

      {* Users section *}
      <div class="page-link {if "user" == $ACTIVE_PAGE}active{/if}">
        <span>
          <i class="icon-users"></i><span class="reduced-hidden">{'Users'|@translate}&nbsp;</span>
          <span class="hover">
            <i class="icon-down-open"></i>
          </span>
        </span>
        <div class="sub-link-container">
          {* Users sub-link prefilter start *}
          <a href="{$U_USERS}" class="sub-link"><i class="icon-user-add"></i>{'Manage'|@translate}</a>
          <a href="{$U_GROUPS}" class="sub-link"><i class="icon-group"></i>{'Groups'|@translate}</a>
          <a href="{$U_ACTIVITY}" class="sub-link"><i class="icon-pulse"></i>{'Activity'|@translate}</a>
          <a href="{$U_NOTIFICATION_BY_MAIL}" class="sub-link"><i class="icon-mail-1"></i>{'Notification'|@translate}</a>
          {* Users sub-link prefilter end *}
        </div>
      </div>

      {* Plugins section *}
      <div class="page-link {if "plugin" == $ACTIVE_PAGE}active{/if}">
        <span class="active-border"></span>
        <a href="{$U_PLUGINS}"><i class="icon-puzzle"></i><span class="reduced-hidden">{'Plugins'|@translate}&nbsp;</span></a>
        <span class="hover"></span>
      </div>

      {* Tools section *}
      <div class="page-link {if "tool" == $ACTIVE_PAGE}active{/if}">
        <span>
          <i class="icon-wrench"></i><span class="reduced-hidden">{'Tools'|@translate}&nbsp;</span>
          <span class="hover">
            <i class="icon-down-open"></i>
          </span>
        </span>
        <div class="sub-link-container">
          {* Tools sub-link prefilter start *}
{if $ENABLE_SYNCHRONIZATION}
          <a href="{$U_CAT_UPDATE}" class="sub-link"><i class="icon-exchange"></i>{'Synchronize'|@translate}</a>
{/if}
          <a href="{$U_HISTORY_STAT}" class="sub-link"><i class="icon-signal"></i>{'History'|@translate}</a>
          <a href="{$U_MAINTENANCE}" class="sub-link"><i class="icon-tools"></i>{'Maintenance'|@translate}</a>
{if isset($U_COMMENTS)}
          <a href="{$U_COMMENTS}" class="sub-link">
            <i class="icon-chat"></i>{'Comments'|@translate}
  {if isset($NB_PENDING_COMMENTS) and $NB_PENDING_COMMENTS > 0}
            <span class="adminMenubarCounter" title="{'%d waiting for validation'|translate:$NB_PENDING_COMMENTS}">{$NB_PENDING_COMMENTS}</span>
  {/if}
          </a>
{/if}
{if isset($U_UPDATES)}
          <a href="{$U_UPDATES}" class="sub-link"><i class="icon-arrows-cw"></i>{'Updates'|@translate}</a>
{/if}
          {* Tools sub-link prefilter end *}
        </div>
      </div>

      {* Configuration section *}
      <div class="page-link {if "configuration" == $ACTIVE_PAGE}active{/if}">
        <span>
          <i class="icon-cog"></i><span class="reduced-hidden">{'Configuration'|@translate}&nbsp;</span>
          <span class="hover">
            <i class="icon-down-open"></i>
          </span>
        </span>
        <div class="sub-link-container">
          {* Confguration sub-link prefilter start *}
          <a href="{$U_CONFIG_GENERAL}" class="sub-link"><i class="icon-cog-alt"></i>{'Options'|@translate}</a>
          <a href="{$U_CONFIG_MENUBAR}" class="sub-link"><i class="icon-menu"></i>{'Menu Management'|@translate}</a>
{if {$U_SHOW_TEMPLATE_TAB}}
          <a href="{$U_CONFIG_EXTENTS}" class="sub-link"><i class="icon-code"></i>{'Templates'|@translate}</a>
{/if}
          <a href="{$U_CONFIG_LANGUAGES}" class="sub-link"><i class="icon-language"></i>{'Languages'|@translate}</a>
          <a href="{$U_CONFIG_THEMES}" class="sub-link"><i class="icon-brush"></i>{'Themes'|@translate}</a>
          {* Confguration sub-link prefilter end *}
        </div>
      </div>

    </div>

    <div id="shortcut-links" class="menu-block">
    </div>

  </div>

  <div id="dekstop-menu-footer" class="menu-block">
    <div id="reduce-enlarge">
        <div class="reduced-hidden"><i class="icon-reduce"></i><span>{'Reduce'|translate}&nbsp;</span></div>
        <div class="enlarged-hidden"><i class="icon-enlarge"></i></div>
    </div>
        
    <div>
      <a class="externalLink tiptip piwigo-logo" href="{$PHPWG_URL}" title="{'Visit Piwigo project website'|translate}">
        <img class="reduced-hidden" src="admin/themes/default/images/piwigo-grey.svg">
        <img class="enlarged-hidden" src="admin/themes/default/images/piwigo-grey-icon.svg">
      </a>
      {if isset($DISPLAY_BELL) and $DISPLAY_BELL}
      <span id="whats_new_notification" class="icon-blue tiptip" onclick="show_user_whats_new()" title="{'What\'s new in version %s'|translate:$WHATS_NEW_MAJOR_VERSION}">
        <i class="icon-bell"></i>
      </span>
      {/if}
    </div>
  </div>

  <div id="mobile-menu-footer" class="menu-block">
    <a class="gallery-link" href="{$U_RETURN}"><i class="icon-home"></i></a>
    <div>
      <span class="mobile-reduced">
      <i class="icon-menu"></i>
      </span>
      <span class="mobile-reduced">
      <i class="icon-cross"></i>
      </span>
    </div>
  </div>

</div>

{* <div id="pwgHead">
  <a href="{$U_RETURN}" class="visit-gallery tiptip" title="{'Visit Gallery'|translate}"><i class="icon-left-open"></i>{'Visit'|translate}</a>
  <div class="pwgHead-gallery-title">{$GALLERY_TITLE}</div>

  <div id="headActions">
    <span class="admin-head-username"><i class="icon-user"></i>{$USERNAME}</span> *}
{*
    <a href="{$U_RETURN}" title="{'Visit Gallery'|translate}"><i class="icon-eye"></i><span>{'Visit Gallery'|translate}</span></a>
*}

{* {strip}
    <a href="{$U_CHANGE_THEME}" class="tiptip" title="{'Switch to clear or dark colors for administration'|translate}">
{if $theme_id eq "clear"}
      <i class="icon-moon-inv"></i><span>Dark</span>
{elseif $theme_id eq "roma"}
      <i class="icon-sun-inv"></i><span>Light</span>
{/if}
</a>
{/strip}

    <a class="tiptip" href="{$U_FAQ}" title="{'Instructions to use Piwigo'|@translate}"><i class="icon-help-circled"></i><span>{'Help Me'|translate}</span></a>
    <a href="{$U_LOGOUT}"><i class="icon-logout"></i><span>{'Logout'|translate}</span></a>
  </div>
</div> *}
{* <div id="menubar">
  <div id="adminHome"><a href="{$U_ADMIN}" class="admin-main"><i class="icon-television"></i> {'Dashboard'|@translate}</a></div>

	<dl>
		<dt><i class="icon-picture"> </i><span>{'Photos'|@translate}&nbsp;</span><i class="icon-down-open open-menu"></i></dt>
		<dd>
			<ul>
				<li><a href="{$U_ADD_PHOTOS}"><i class="icon-plus-circled"></i>{'Add'|@translate}</a></li>
{if $SHOW_RATING}
        <li><a href="{$U_RATING}"><i class="icon-star"></i>{'Rating'|@translate}</a></li>
{/if}
				<li><a href="{$U_TAGS}"><i class="icon-tags"></i>{'Tags'|@translate}</a></li>
				<li><a href="{$U_RECENT_SET}"><i class="icon-clock"></i>{'Recent photos'|@translate}</a></li>
				<li><a href="{$U_BATCH}"><i class="icon-th"></i>{'Batch Manager'|@translate}</a></li>
{if $NB_PHOTOS_IN_CADDIE > 0}
				<li><a href="{$U_CADDIE}"><i class="icon-flag"></i>{'Caddie'|@translate}<span class="adminMenubarCounter">{$NB_PHOTOS_IN_CADDIE}</span></a></li>
{/if}
{if $NB_ORPHANS > 0}
				<li><a href="{$U_ORPHANS}"><i class="icon-heart-broken"></i>{'Orphans'|@translate}<span class="adminMenubarCounter">{$NB_ORPHANS}</span></a></li>
{/if}
			</ul>
		</dd>
  </dl>
  <dl>
		<dt><i class="icon-sitemap"> </i><span>{'Albums'|@translate}&nbsp;</span><i class="icon-down-open open-menu"></i></dt>
    <dd>
      <ul>
        <li><a href="{$U_ALBUMS}"><i class="icon-folder-open"></i>{'Manage'|@translate}</a></li>
        <li><a href="{$U_CAT_OPTIONS}"><i class="icon-pencil"></i>{'Properties'|@translate}</a></li>
      </ul>
    </dd>
  </dl>
  <dl>
		<dt><i class="icon-users"> </i><span>{'Users'|@translate}&nbsp;</span><i class="icon-down-open open-menu"></i></dt>
		<dd>
      <ul>
        <li><a href="{$U_USERS}"><i class="icon-user-add"></i>{'Manage'|@translate}</a></li>
        <li><a href="{$U_GROUPS}"><i class="icon-group"></i>{'Groups'|@translate}</a></li>
				<li><a href="{$U_NOTIFICATION_BY_MAIL}"><i class="icon-mail-1"></i>{'Notification'|@translate}</a></li>
      </ul>
		</dd>
  </dl>
  <dl>
		<dt><a href="{$U_PLUGINS}" class="admin-main"><i class="icon-puzzle"> </i><span>{'Plugins'|@translate}&nbsp;</span></a></dt>
  </dl>
  <dl>
		<dt><i class="icon-wrench"> </i><span>{'Tools'|@translate}&nbsp;</span><i class="icon-down-open open-menu"></i></dt>
		<dd>
      <ul>
{if $ENABLE_SYNCHRONIZATION}
        <li><a href="{$U_CAT_UPDATE}"><i class="icon-exchange"></i>{'Synchronize'|@translate}</a></li>
{/if}
				<li><a href="{$U_HISTORY_STAT}"><i class="icon-signal"></i>{'History'|@translate}</a></li>
				<li><a href="{$U_MAINTENANCE}"><i class="icon-tools"></i>{'Maintenance'|@translate}</a></li>
{if isset($U_COMMENTS)}
				<li><a href="{$U_COMMENTS}"><i class="icon-chat"></i>{'Comments'|@translate}
        {if isset($NB_PENDING_COMMENTS) and $NB_PENDING_COMMENTS > 0}
          <span class="adminMenubarCounter" title="{'%d waiting for validation'|translate:$NB_PENDING_COMMENTS}">{$NB_PENDING_COMMENTS}</span>
        {/if}</a></li>
{/if}
{if isset($U_UPDATES)}
        <li><a href="{$U_UPDATES}"><i class="icon-arrows-cw"></i>{'Updates'|@translate}</a></li>
{/if}
      </ul>
		</dd>
  </dl>
  <dl>
		<dt><i class="icon-cog"> </i><span>{'Configuration'|@translate}&nbsp;</span><i class="icon-down-open open-menu"></i></dt>
		<dd>
      <ul>
        <li><a href="{$U_CONFIG_GENERAL}"><i class="icon-cog-alt"></i>{'Options'|@translate}</a></li>
        <li><a href="{$U_CONFIG_MENUBAR}"><i class="icon-menu"></i>{'Menu Management'|@translate}</a></li>
        {if {$U_SHOW_TEMPLATE_TAB}}
          <li><a href="{$U_CONFIG_EXTENTS}"><i class="icon-code"></i>{'Templates'|@translate}</a></li>
        {/if}
				<li><a href="{$U_CONFIG_LANGUAGES}"><i class="icon-language"></i>{'Languages'|@translate}</a></li>
        <li><a href="{$U_CONFIG_THEMES}"><i class="icon-brush"></i>{'Themes'|@translate}</a></li>
      </ul>
    </dd>
  </dl>
</div> <!-- menubar --> *}

<div id="content" class="content">

  <h1>{$ADMIN_PAGE_TITLE}<span class="admin-object-id">{$ADMIN_PAGE_OBJECT_ID}</span></h1>

  {if isset($TABSHEET)}
  {$TABSHEET}
  {/if}
  {if isset($U_HELP)}
  {include file='include/colorbox.inc.tpl'}
{footer_script}
  jQuery('.help-popin').colorbox({ width:"500px" });
{/footer_script}
  <ul class="HelpActions">
    <li><a href="{$U_HELP}&amp;output=content_only" title="{'Help'|@translate}" class="help-popin"><span class="icon-help-circled"></span></a></li>
  </ul>
  {/if}

<div class="eiw">
  {if isset($errors)}
  <div class="errors">
    <i class="eiw-icon icon-cancel"></i>
    <ul>
      {foreach from=$errors item=error}
      <li>{$error}</li>
      {/foreach}
    </ul>
  </div>
  {/if}

  {if isset($infos)}
  <div class="infos">
    <i class="eiw-icon icon-ok"></i>
    <ul>
      {foreach from=$infos item=info}
      <li>{$info}</li>
      {/foreach}
    </ul>
  </div>
  {/if}

  {if isset($warnings)}
  <div class="warnings">
    <i class="eiw-icon icon-attention"></i>
    <ul>
      {foreach from=$warnings item=warning}
      <li>{$warning}</li>
      {/foreach}
    </ul>
  </div>
  {/if}

  {if isset($messages)}
  <div class="messages">
    <i class="eiw-icon icon-info-circled-1"></i>
    <ul>
      {foreach from=$messages item=message}
      <li>{$message}</li>
      {/foreach}
    </ul>
  </div>
  {/if}

</div> {* .eiw *}

  {$ADMIN_CONTENT}
</div>
