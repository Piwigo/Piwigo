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
{/footer_script}

<div id="menubar">
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
</div> <!-- menubar -->

<div id="content" class="content">

  <h1>{$ADMIN_PAGE_TITLE}</h1>

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
