<!DOCTYPE html>
<html lang={$lang_info.code} dir={$lang_info.direction}>
<head>
<title>{if $PAGE_TITLE=='Home'|@translate}{$GALLERY_TITLE}{else}{$PAGE_TITLE}{/if}</title>
<link rel="shortcut icon" type="image/x-icon" href="{$ROOT_URL}{$themeconf.icon_dir}/favicon.ico">
<link rel="stylesheet" type="text/css" href="{$ROOT_URL}themes/{$themeconf.id}/css/open-sans/open-sans.css"> {* cannot be loaded by combine_css because it contains relative urls *}
{strip}{get_combined_css}
{combine_css path="themes/`$themeconf.id`/css/base.css.tpl" version=$MODUS_CSS_VERSION template=true order=-10}
{combine_css path="themes/`$themeconf.id`/css/iconfontello.css.tpl" version=$MODUS_CSS_VERSION template=true order=-10}
{combine_css path="themes/`$themeconf.id`/css/menuh.css.tpl" version=$MODUS_CSS_VERSION template=true order=-10}
{combine_css path="themes/`$themeconf.id`/css/index.css.tpl" version=$MODUS_CSS_VERSION template=true order=-10}
{combine_css path="themes/`$themeconf.id`/css/picture.css.tpl" version=$MODUS_CSS_VERSION template=true order=-10}
{combine_css path="themes/`$themeconf.id`/css/tags.css" order=-10}
{combine_css path="themes/`$themeconf.id`/css/print.css" order=-10}
{combine_css path="themes/`$themeconf.id`/css/plugin_compatibility.css" order=-10}

{if isset($MODUS_CSS_SKIN)}
  {combine_css path="themes/`$themeconf.id`/css/hf_base.css" order=-10} {* load base CSS for skins designed by Hannah *}
  {combine_css path="themes/`$themeconf.id`/skins/`$MODUS_CSS_SKIN`.css" order=-10}
{/if}
{/strip}
{if isset($U_PREFETCH)}<link rel=prefetch href="{$U_PREFETCH}">{/if}
{if isset($U_CANONICAL)}<link rel=canonical href="{$U_CANONICAL}">{/if}
{if not empty($page_refresh)}<meta http-equiv="refresh" content="{$page_refresh.TIME};url={$page_refresh.U_REFRESH}">{/if}
{get_combined_scripts load='header'}
{if empty($smarty.server.HTTP_USER_AGENT) || strpos($smarty.server.HTTP_USER_AGENT, 'MSIE')}
<meta http-equiv=x-ua-compatible content="IE=edge">
<!--[if lt IE 9]><script type="text/javascript" src="{$ROOT_URL}themes/{$themeconf.id}/html5shiv.js"></script><![endif]-->
{/if}
<meta name=viewport content="width=device-width,initial-scale=1">
{*combine_script id='jquery' load='footer'*}
{if not empty($head_elements)}{foreach from=$head_elements item=elt}{$elt}
{/foreach}{/if}

<meta name="generator" content="Piwigo (aka PWG), see piwigo.org">

{if isset($meta_ref)}
{if isset($INFO_AUTHOR)}
<meta name="author" content="{$INFO_AUTHOR|strip_tags:false|replace:'"':' '}">
{/if}
{if isset($related_tags)}
<meta name="keywords" content="{foreach from=$related_tags item=tag name=tag_loop}{if !$smarty.foreach.tag_loop.first}, {/if}{$tag.name}{/foreach}">
{/if}
{if isset($COMMENT_IMG)}
<meta name="description" content="{$COMMENT_IMG|strip_tags:false|replace:'"':' '}{if isset($INFO_FILE)} - {$INFO_FILE}{/if}">
{else}
<meta name="description" content="{$PAGE_TITLE}{if isset($INFO_FILE)} - {$INFO_FILE}{/if}">
{/if}
{/if}


</head>

<body id={$BODY_ID} class="{foreach from=$BODY_CLASSES item=class}{$class} {/foreach}{if !empty($PAGE_BANNER) && $MODUS_DISPLAY_PAGE_BANNER} modus-withPageBanner{/if}" data-infos='{$BODY_DATA}'>
{if not empty($header_msgs) or not empty($header_notes)}
<div class="header_msgs">
{if not empty($header_msgs)}
	{foreach from=$header_msgs item=elt}
	<p>{$elt}</p>
	{/foreach}
{/if}
{if not empty($header_notes)}
	{foreach from=$header_notes item=elt}
	<p>{$elt}</p>
  {/foreach}
{/if}
</div>
{/if}
{if !empty($PAGE_BANNER) && $MODUS_DISPLAY_PAGE_BANNER}<div id="theHeader">{$PAGE_BANNER}</div>{/if}
