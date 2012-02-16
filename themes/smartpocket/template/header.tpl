<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="{$lang_info.code}" dir="{$lang_info.direction}">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$CONTENT_ENCODING}">
<meta name="generator" content="Piwigo (aka PWG), see piwigo.org">
{if isset($meta_ref) } 
{if isset($INFO_AUTHOR)}
<meta name="author" content="{$INFO_AUTHOR|@strip_tags:false|@replace:'"':' '}">
{/if}
{if isset($related_tags)}
<meta name="keywords" content="{foreach from=$related_tags item=tag name=tag_loop}{if !$smarty.foreach.tag_loop.first}, {/if}{$tag.name}{/foreach}">
{/if}
{if isset($COMMENT_IMG)}
<meta name="description" content="{$COMMENT_IMG|@strip_tags:false|@replace:'"':' '}{if isset($INFO_FILE)} - {$INFO_FILE}{/if}">
{else}
<meta name="description" content="{$PAGE_TITLE}{if isset($INFO_FILE)} - {$INFO_FILE}{/if}">
{/if}
{/if}
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;" name="viewport" />
<meta name="apple-mobile-web-app-capable" content="yes" />

{if (isset($REVERSE) and $REVERSE and $PAGE_TITLE == l10n('Home'))}
<title>{$GALLERY_TITLE} | {$PAGE_TITLE}</title>{else}
<title>{$PAGE_TITLE} | {$GALLERY_TITLE}</title>{/if}
<link rel="shortcut icon" type="image/x-icon" href="{$ROOT_URL}{$themeconf.icon_dir}/favicon.ico">
<link rel="start" title="{'Home'|@translate}" href="{$U_HOME}" >

{get_combined_css}
{foreach from=$themes item=theme}
{if $theme.load_css}
{combine_css path="themes/`$theme.id`/theme.css" order=-10}
{/if}
{if !empty($theme.local_head)}{include file=$theme.local_head load_css=$theme.load_css}{/if}
{/foreach}

{if isset($U_CANONICAL)}<link rel="canonical" href="{$U_CANONICAL}">{/if}

{if not empty($page_refresh)    }<meta http-equiv="refresh" content="{$page_refresh.TIME};url={$page_refresh.U_REFRESH}">{/if}

{get_combined_scripts load='header'}

{combine_script id='jquery' path='themes/smartpocket/js/jquery-1.6.4.min.js'}
{combine_script id='config' path='themes/smartpocket/js/config.js' require='jquery'}
{combine_script id='jquery.mobile' path='themes/smartpocket/js/jquery.mobile.min.js' require='jquery,config'}

</head>

<body>
<div data-role="page">

<div data-role="header" data-theme="c">
  <div class="title">
    <a href="{$U_HOME}" class="home_button" data-icon="home" data-iconpos="notext" data-role="button"></a>
    {$GALLERY_TITLE}
    <span class="menubar">{$MENUBAR}</span>
  </div>
</div>

