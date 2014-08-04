{strip}
{combine_css path=$ADMINTOOLS_PATH|cat:'template/admin_style.css'}
{combine_css path=$ADMINTOOLS_PATH|cat:'template/fontello/css/fontello-ato.css'}
{combine_script id='admintools.controller' load='footer' require='jquery' path=$ADMINTOOLS_PATH|cat:'template/admin_controller.js'}
{/strip}

{footer_script require='admintools.controller'}
AdminTools.urlWS = '{$ROOT_URL}ws.php?format=json&method=';
AdminTools.urlSelf = '{$ato.U_SELF}';

AdminTools.multiView = {
  view_as: {$ato.MULTIVIEW.view_as},
  theme: '{$themeconf.name}',
  lang: '{$ato.MULTIVIEW.lang}'
};

{if $ato.DELETE_CACHE}
  AdminTools.deleteCache();
{/if}
  AdminTools.init();
{/footer_script}

<ul class="multiview">
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