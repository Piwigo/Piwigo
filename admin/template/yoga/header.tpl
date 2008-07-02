{* $Id$ *}
{* 


          Warning : This is the admin pages header only 
          don't be confusing with the public page header

*}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html lang="{$LANG}" dir="{$DIR}">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$CONTENT_ENCODING}">
<meta name="generator" content="Piwigo (aka PWG), see piwigo.org">
<title>{$GALLERY_TITLE} :: {$PAGE_TITLE}</title>
<link rel="shortcut icon" type="image/x-icon" href="{$ROOT_URL}template-common/favicon.ico">

<link rel="stylesheet" type="text/css" href="{$ROOT_URL}admin/template/{$themeconf.template}/layout.css">
<!--[if lt IE 7]>
	<link rel="stylesheet" type="text/css" href="{$ROOT_URL}admin/template/{$themeconf.template}/fix-ie5-ie6.css">
<![endif]-->
<link rel="stylesheet" type="text/css" media="print" href="{$ROOT_URL}template/{$themeconf.template}/print.css">
<link rel="stylesheet" type="text/css" href="{$ROOT_URL}admin/template/{$themeconf.template}/default-colors.css">
<link rel="stylesheet" type="text/css" href="{$ROOT_URL}admin/template/{$themeconf.template}/theme/{$themeconf.theme}/theme.css">
{$themeconf.local_head}
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
<div id="headbranch"></div> {* Dummy block for double background management *}
<div id="the_page">

{if not empty($header_msgs)}
<div class="header_msgs">
	{foreach from=$header_msgs item=elt}
	<p>{$elt}</p>
	{/foreach}
</div>
{/if}

<div id="theHeader">{*$PAGE_BANNER*}</div>
{if not empty($header_notes)}
<div class="header_notes">
	{foreach from=$header_notes item=elt}
	<p>{$elt}</p>
  	{/foreach}
</div>
{/if}
