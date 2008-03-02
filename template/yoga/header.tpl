{* $Id$ *}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html lang="{$LANG}" dir="{$DIR}">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$CONTENT_ENCODING}">
<meta name="generator" content="PhpWebGallery (aka PWG), see www.phpwebgallery.net">
<title>{$GALLERY_TITLE} :: {$PAGE_TITLE}</title>
<link rel="shortcut icon" type="image/x-icon" href="{$ROOT_URL}template-common/favicon.ico">

<link rel="start" title="{'home'|@translate}" href="{$U_HOME}" >
<link rel="search" title="{'search'|@translate}" href="{$ROOT_URL}search.php" >
{if isset($first.U_IMG)   }<link rel="first" title="{'first_page'|@translate}" href="{$first.U_IMG}" >{/if}
{if isset($previous.U_IMG)}<link rel="prev" title="{'previous_page'|@translate}" href="{$previous.U_IMG}" >{/if}
{if isset($next.U_IMG)    }<link rel="next" title="{'next_page'|@translate}" href="{$next.U_IMG}" >{/if}
{if isset($last.U_IMG)    }<link rel="last" title="{'last_page'|@translate}" href="{$last.U_IMG}" >{/if}
{if isset($U_UP)          }<link rel="up" title="{'thumbnails'|@translate}" href="{$U_UP}" >{/if}

<link rel="stylesheet" type="text/css" href="{$ROOT_URL}template/{$themeconf.template}/layout.css">
{* the next css is used to fix khtml (Konqueror/Safari) issue the "text/nonsense" prevents gecko based browsers to load it *}
<link rel="stylesheet" type="text/nonsense" href="{$ROOT_URL}template/{$themeconf.template}/fix-khtml.css">
<!--[if lt IE 7]>
	<link rel="stylesheet" type="text/css" href="{$ROOT_URL}template/{$themeconf.template}/fix-ie5-ie6.css">
<![endif]-->
<!--[if gt IE 6]>
	<link rel="stylesheet" type="text/css" href="{$ROOT_URL}template/{$themeconf.template}/fix-ie7.css">
<![endif]-->
<!--[if !IE]> <-->
	<link rel="stylesheet" href="{$ROOT_URL}template/{$themeconf.template}/not-ie.css" type="text/css">
<!--> <![endif]-->
<link rel="stylesheet" type="text/css" media="print" href="{$ROOT_URL}template/{$themeconf.template}/print.css">
<link rel="stylesheet" type="text/css" href="{$ROOT_URL}template/{$themeconf.template}/default-colors.css">
<link rel="stylesheet" type="text/css" href="{$ROOT_URL}template/{$themeconf.template}/theme/{$themeconf.theme}/theme.css">
{$themeconf.local_head}
{if isset($U_PREFETCH)          }<link rel="prefetch" href="{$U_PREFETCH}">{/if}

{if not empty($page_refresh)    }<meta http-equiv="refresh" content="{$page_refresh.TIME};url={$page_refresh.U_REFRESH}">{/if}

<script type="text/javascript" src="{$ROOT_URL}template-common/scripts.js"></script>
<!--[if lt IE 7]>
<style>
	{* only because we need {$ROOT_URL} otherwise use fix-ie5-ie6.css *}
	BODY {ldelim} behavior:url("{$ROOT_URL}template-common/csshover.htc"); }
	A IMG, .button, .icon {ldelim}
		behavior:url("{$ROOT_URL}template-common/tooltipfix.htc");
	}
	FORM {ldelim} behavior: url("{$ROOT_URL}template-common/inputfix.htc"); }
</style>
<script type="text/javascript" src="{$ROOT_URL}template-common/pngfix.js"></script>
<![endif]-->

{if not empty($head_elements)}
	{foreach from=$head_elements item=elt}{$elt}{/foreach}
{/if}

</head>

<body id="{$BODY_ID}">
<div id="the_page">

{if not empty($header_msgs)}
<div class="header_msgs">
	{foreach from=$header_msgs item=elt}
	<p>{$elt}</p>
	{/foreach}
</div>
{/if}

<div id="theHeader">{$PAGE_BANNER}</div>

{if not empty($header_notes)}
<div class="header_notes">
	{foreach from=$header_notes item=elt}
	<p>{$elt}</p>
  	{/foreach}
</div>
{/if}
