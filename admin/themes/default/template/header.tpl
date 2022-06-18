{* 
          Warning : This is the admin pages header only 
          don't confuse with the public page header
*}
<!DOCTYPE html>
<html lang="{$lang_info.code}" dir="{$lang_info.direction}">
<head>
{if $SHOW_MOBILE_APP_BANNER}
<meta name="apple-itunes-app" content="app-id=472225196">
{/if}
<meta charset="{$CONTENT_ENCODING}">
<title>{$GALLERY_TITLE} :: {$PAGE_TITLE}</title>
<link rel="icon" type="image/svg+xml" href="{$ROOT_URL}{$themeconf.icon_dir}/piwigo.org-icon 1.svg">
<link rel="stylesheet" type="text/css" href="{$ROOT_URL}admin/themes/default/fonts/open-sans/open-sans.css">

{strip}
{combine_css path="admin/themes/default/fontello/css/fontello.css" order=-10}
{assign "theme_id" ""}
{foreach from=$themes item=theme}
  {assign "theme_id" $theme.id}

  {if $theme.load_css}
  {combine_css path="admin/themes/`$theme.id`/theme.css" order=-10}
  {combine_css path="admin/themes/`$theme.id`/css/components/general.css" order=-9} {* Temporary solution *}
  {/if}
  {if !empty($theme.local_head)}
  {include file=$theme.local_head load_css=$theme.load_css}
  {/if}
{/foreach}

{combine_script id='jquery' path='themes/default/js/jquery.min.js'}
{/strip}

<!-- BEGIN get_combined -->
{get_combined_css}

{get_combined_scripts load='header'}
<!-- END get_combined -->

{if not empty($head_elements)}
{foreach from=$head_elements item=elt}
  {$elt}
{/foreach}
{/if}
</head>

<body id="{$BODY_ID}">

<div id="the_page">

{if not empty($header_msgs)}
<div class="header_msgs">
  {foreach from=$header_msgs item=elt}
  {$elt}
  {/foreach}
</div>
{/if}

<div id="pwgHead">
  <a href="{$U_RETURN}" class="visit-gallery tiptip" title="{'Visit Gallery'|translate}"><i class="icon-left-open"></i>{'Visit'|translate}</a>
  <div class="pwgHead-gallery-title">{$GALLERY_TITLE}</div>

  <div id="headActions">
    <span class="admin-head-username"><i class="icon-user"></i>{$USERNAME}</span>
{*
    <a href="{$U_RETURN}" title="{'Visit Gallery'|translate}"><i class="icon-eye"></i><span>{'Visit Gallery'|translate}</span></a>
*}

{strip}
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
</div>

<div style="clear:both;"></div>

{if not empty($header_notes)}
<div class="header_notes">
  {foreach from=$header_notes item=elt}
  {$elt}
  {/foreach}
</div>
{/if}

<div id="pwgMain">
