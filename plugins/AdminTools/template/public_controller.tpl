{strip}
{combine_css path=$ADMINTOOLS_PATH|cat:'template/public_style.css'}
{combine_css path='admin/themes/default/fontello/css/fontello.css'}
{combine_css path=$ADMINTOOLS_PATH|cat:'template/fontello/css/fontello-ato.css'}

{if isset($ato.QUICK_EDIT)}
  {*<!-- mousetrap -->*}
  {combine_script id='mousetrap' load='footer' path=$ADMINTOOLS_PATH|cat:'template/mousetrap.min.js'}
  
  {*<!-- colorbox -->*}
  {combine_script id='jquery.colorbox' load='footer' require='jquery' path='themes/default/js/plugins/jquery.colorbox.min.js'}
  {combine_css id='colorbox' path='themes/default/js/plugins/colorbox/style2/colorbox.css'}

  {if isset($ato.IS_PICTURE)}
    {*<!-- tokeninput -->*}
    {combine_script id='jquery.tokeninput' load='footer' require='jquery' path='themes/default/js/plugins/jquery.tokeninput.js'}
    {combine_css path='themes/default/js/plugins/jquery.tokeninput.css'}

    {*<!-- datepicker -->*}
    {combine_script id='jquery.ui.datepicker' load='footer' path='themes/default/js/ui/jquery.ui.datepicker.js'}

    {assign var=datepicker_language value='themes/default/js/ui/i18n/jquery.ui.datepicker-'|cat:$lang_info.code|cat:'.js'}
    {if 'PHPWG_ROOT_PATH'|constant|cat:$datepicker_language|file_exists}
    {combine_script id='jquery.ui.datepicker-'|cat:$lang_info.code load='footer' path=$datepicker_language}
    {/if}

    {combine_css path='themes/default/js/ui/theme/jquery.ui.core.css'}
    {combine_css path='themes/default/js/ui/theme/jquery.ui.theme.css'}
    {combine_css path='themes/default/js/ui/theme/jquery.ui.datepicker.css'}
  {/if}
{/if}

{combine_script id='admintools.controller' load='footer' require='jquery' path=$ADMINTOOLS_PATH|cat:'template/public_controller.js'}
{/strip}

{footer_script require='admintools.controller'}
AdminTools.urlWS = '{$ROOT_URL}ws.php?format=json&method=';
AdminTools.urlSelf = '{$ato.U_SELF}';

{if isset($ato.MULTIVIEW)}
AdminTools.multiView = {
  view_as: {$ato.MULTIVIEW.view_as},
  theme: '{$ato.MULTIVIEW.theme}',
  lang: '{$ato.MULTIVIEW.lang}'
};
{/if}

{if $ato.DELETE_CACHE}
  AdminTools.deleteCache();
{/if}
  AdminTools.init({intval($ato.DEFAULT_OPEN)});
{if isset($themeconf.mobile) and $themeconf.mobile}
  AdminTools.initMobile();
{/if}
{if isset($ato.U_SET_REPRESENTATIVE)}
  AdminTools.initRepresentative({$current.id}, {$ato.CATEGORY_ID});
{/if}
{if isset($ato.U_CADDIE) and isset($ato.IS_PICTURE)}
  AdminTools.initCaddie({$current.id});
{/if}
{if isset($ato.QUICK_EDIT)}
  AdminTools.initQuickEdit({intval(isset($ato.IS_PICTURE))}, {
    hintText: '{'Type in a search term'|translate|escape:javascript}',
    noResultsText: '{'No results'|translate|escape:javascript}',
    searchingText: '{'Searching...'|translate|escape:javascript}',
    newText: ' ({'new'|translate|escape:javascript})'
  });
{/if}
{/footer_script}

<div id="ato_header_closed"{if $ato.POSITION=='right'} class="right"{/if}><a href="#" class="icon-tools"></a></div>

<div id="ato_header">
  <ul>
    <li{if $ato.POSITION=='right'} class="right"{/if}><a href="#" class="icon-ato-cancel close-panel"></a></li>
  {if isset($ato.U_SITE_ADMIN)}
    <li class="parent"><a href="#" class="icon-menu ato-min-1">{'Administration'|translate}</a>
      <ul>
        <li><a class="icon-home" href="{$ato.U_SITE_ADMIN}intro">{'Home'|translate}</a></li>
        <li><a class="icon-picture" href="{$ato.U_SITE_ADMIN}batch_manager">{'Photos'|translate}</a></li>
        <li><a class="icon-sitemap" href="{$ato.U_SITE_ADMIN}cat_list">{'Albums'|translate}</a></li>
        <li><a class="icon-users" href="{$ato.U_SITE_ADMIN}user_list">{'Users'|translate}</a></li>
        <li><a class="icon-puzzle" href="{$ato.U_SITE_ADMIN}plugins">{'Plugins'|translate}</a></li>
        <li><a class="icon-wrench" href="{$ato.U_SITE_ADMIN}maintenance">{'Tools'|translate}</a></li>
        <li><a class="icon-cog" href="{$ato.U_SITE_ADMIN}configuration">{'Configuration'|translate}</a></li>
      </ul>
    </li>
  {/if}
  {if isset($ato.U_ADMIN_EDIT)}
    <li class="parent"><a href="#" class="icon-pencil ato-min-2">{'Edit'|translate}</a>
      <ul>
        <li><a href="#ato_quick_edit" class="icon-ato-flash edit-quick">{'Quick edit'|translate}</a></li>
        <li><a class="icon-ato-doc-text-inv" href="{$ato.U_ADMIN_EDIT}">{'Properties page'|translate}</a></li>
      {if isset($ato.U_DELETE)}
				<li style="margin-top:1em;"><a class="icon-ato-cancel" href="{$ato.U_DELETE}" onclick="return confirm('{'Are you sure?'|translate|escape:javascript}')">{'delete photo'|translate|ucfirst}</a></li>
      {/if}
      </ul>
    </li>
  {elseif isset($ato.QUICK_EDIT)}
    <li><a href="#ato_quick_edit" class="icon-pencil edit-quick ato-min-2">{'Edit'|translate}</a></li>
    {if isset($ato.U_DELETE)}
      <li><a class="icon-ato-cancel ato-min-2" href="{$ato.U_DELETE}" onclick="return confirm('{'Are you sure?'|translate|escape:javascript}')">{'delete photo'|translate|ucfirst}</a></li>
    {/if}
  {/if}
  {if isset($ato.U_SET_REPRESENTATIVE)}
    <li {if $ato.IS_REPRESENTATIVE}class="disabled"{/if}><a class="icon-ato-trophy set-representative ato-min-2" href="{$ato.U_SET_REPRESENTATIVE}">{'representative'|translate|ucfirst}</a></li>
  {/if}
  {if isset($ato.U_CADDIE)}
    <li {if $ato.IS_IN_CADDIE}class="disabled"{/if}><a class="icon-flag add-caddie ato-min-2" href="{$ato.U_CADDIE}">{'Add to caddie'|translate}</a></li>
  {/if}
  {if isset($ato.IS_CATEGORY)}
    <li><a class="icon-plus-circled ato-min-2" href="{$ato.U_SITE_ADMIN}photos_add&amp;album={$ato.CATEGORY_ID}">{'Add Photos'|translate}</a></li>
  {/if}
    <li class="saved"><span class="icon-ato-ok ato-min-1">{'Saved'|translate}</span></li>

  {if isset($ato.MULTIVIEW)}
    <li class="parent right multiview"><a class="icon-cog-alt ato-min-1" href="#">{'Tools'|translate}</a>
      <ul>
        <li><label>{'View as'|translate}</label>
          <select class="switcher" data-type="view_as"></select>
        </li>
        <li><label>{'Theme'|translate}</label>
          <select class="switcher" data-type="theme"></select>
        </li>
        <li><label>{'Language'|translate}</label>
          <select class="switcher" data-type="lang"></select>
        </li>
        <li><a class="icon-check{if !$ato.MULTIVIEW.show_queries}-empty{/if}" href="{$ato.U_SELF}ato_show_queries={(int)!$ato.MULTIVIEW.show_queries}">{'Show SQL queries'|translate}</a></li>
        <li><a class="icon-check{if !$ato.MULTIVIEW.debug_l10n}-empty{/if}" href="{$ato.U_SELF}ato_debug_l10n={(int)!$ato.MULTIVIEW.debug_l10n}">{'Debug languages'|translate}</a></li>
        <li><a class="icon-check{if !$ato.MULTIVIEW.debug_template}-empty{/if}" href="{$ato.U_SELF}ato_debug_template={(int)!$ato.MULTIVIEW.debug_template}">{'Debug template'|translate}</a></li>
        <li><a class="icon-check{if !$ato.MULTIVIEW.template_combine_files}-empty{/if}" href="{$ato.U_SELF}ato_template_combine_files={(int)!$ato.MULTIVIEW.template_combine_files}">{'Combine JS&CSS'|translate}</a></li>
        <li><a class="icon-check{if $ato.MULTIVIEW.no_history}-empty{/if}" href="{$ato.U_SELF}ato_no_history={(int)!$ato.MULTIVIEW.no_history}">{'Save visit in history'|translate}</a></li>
        <li><a class="icon-ato-null" href="{$ato.U_SELF}ato_purge_template=1">{'Purge compiled templates'|translate}</a></li>
      </ul>
    </li>
    {if $ato.USER.id != $ato.MULTIVIEW.view_as}
    <li class="right ato-hide-2"><span>
      {'Viewing as <b>%s</b>.'|translate:$ato.CURRENT_USERNAME}
      <a href="{$ato.U_SELF}ato_view_as={$ato.USER.id}">{'Revert'|translate}</a>
    </span></li>
    {/if}
  {/if}
  </ul>
</div>

{if isset($ato.QUICK_EDIT)}
<div style="display:none;">
  <div id="ato_quick_edit" title="{'Quick edit'|translate}">
    <form method="post" action="{$ato.U_SELF}">
      <fieldset class="left">
        {if isset($ato.QUICK_EDIT.img)}<img src="{$ato.QUICK_EDIT.img}" width="100" height="100">{/if}
        <input type="submit" value="{'Save'|translate}">
        <a href="#" class="icon-ato-cancel close-edit">{'Cancel'|translate}</a>
      </fieldset>

      <fieldset class="main">
        <label for="quick_edit_name">{'Name'|translate}</label>
        <input type="text" name="name" id="quick_edit_name" value="{$ato.QUICK_EDIT.name|escape:html}">

      {if isset($ato.IS_PICTURE)}
        <label for="quick_edit_author">{'Author'|translate}</label>
        <input type="text" name="author" id="quick_edit_author" value="{$ato.QUICK_EDIT.author|escape:html}">

        <label for="quick_edit_date_creation">{'Creation date'|translate}</label>
        <input type="text" name="date_creation" id="quick_edit_date_creation" class="datepicker" value="{$ato.QUICK_EDIT.date_creation}">
        <input type="hidden" name="date_creation_time" value="{$ato.QUICK_EDIT.date_creation_time}">

        <label for="quick_edit_tags">{'Tags'|translate}</label>
        <select name="tags" id="quick_edit_tags" class="tags">
        {foreach from=$ato.QUICK_EDIT.tag_selection item=tag}
          <option value="{$tag.id}" class="selected">{$tag.name}</option>
        {/foreach}
        </select>

        {if isset($available_permission_levels)}
        <label for="quick_edit_level">{'Who can see this photo?'|@translate}</label>
        <select name="level" size="1">
          {html_options options=$available_permission_levels selected=$ato.QUICK_EDIT.level}
        </select>
        {/if}
      {/if}

        <label for="quick_edit_comment">{'Description'|translate}</label>
        <textarea name="comment" id="quick_edit_comment">{$ato.QUICK_EDIT.comment}</textarea>
      </fieldset>

      <input type="hidden" name="action" value="quick_edit">
    </form>
  </div>
</div>
{/if}