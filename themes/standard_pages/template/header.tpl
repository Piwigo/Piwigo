<!DOCTYPE html>
<html lang="{$lang_info.code}" dir="{$lang_info.direction}">
<head>
{if $SHOW_MOBILE_APP_BANNER}
<meta name="apple-itunes-app" content="app-id=472225196">
{/if}
<meta charset="{$CONTENT_ENCODING}">
<meta name="generator" content="Piwigo (aka PWG), see piwigo.org">

<title>{if $PAGE_TITLE!=l10n('Home') && $PAGE_TITLE!=$GALLERY_TITLE}{$PAGE_TITLE} | {/if}{$GALLERY_TITLE}</title>
<link rel="shortcut icon" type="image/x-icon" href="{$ROOT_URL}{$themeconf.icon_dir}/favicon.ico">

{strip}
{foreach from=$themes item=theme}
  {if $theme.load_css}
  {combine_css path="themes/`$theme.id`/theme.css" order=-10}
  {/if}
  {if !empty($theme.local_head)}
  {include file=$theme.local_head load_css=$theme.load_css}
  {/if}
{/foreach}

{combine_script id="jquery" load="header"}
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

<body id="{$BODY_ID}" class="{foreach from=$BODY_CLASSES item=class}{$class} {/foreach}"  data-infos='{$BODY_DATA}'>

<div id="the_page">