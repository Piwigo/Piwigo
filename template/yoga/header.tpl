<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html lang="{LANG}" dir="{DIR}">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={CONTENT_ENCODING}">
<meta name="generator" content="PhpWebGallery (aka PWG), see www.phpwebgallery.net">
<!-- BEGIN header_meta -->
<meta name="author" content="{header_meta.INFO_AUTHOR}">
<meta name="keywords" content="{header_meta.INFO_TAGS}">
<meta name="description" content="{header_meta.COMMENT}">
<!-- END header_meta -->
<title>{GALLERY_TITLE} :: {PAGE_TITLE}</title>
<link rel="shortcut icon" type="image/x-icon" href="{pwg_root}template-common/favicon.ico">
<link rel="start" title="{lang:home}" href="{U_HOME}" >
<link rel="search" title="{lang:search}" href="{pwg_root}search.php" >
<!-- BEGIN first -->
<link rel="first" title="{lang:first_page}" href="{first.U_IMG}" >
<link rel="up" title="{lang:thumbnails}" href="{U_UP}" >
<!-- END first -->
<!-- BEGIN previous -->
<link rel="prev" title="{lang:previous_page}" href="{previous.U_IMG}" >
<!-- END previous -->
<!-- BEGIN next -->
<link rel="next" title="{lang:next_page}" href="{next.U_IMG}" >
<!-- END next -->
<!-- BEGIN last -->
<link rel="last" title="{lang:last_page}" href="{last.U_IMG}" >
<link rel="up" title="{lang:thumbnails}" href="{U_UP}" >
<!-- END last -->
<link rel="stylesheet" type="text/css" href="{pwg_root}template/{themeconf:template}/layout.css">
<!-- the next css is used to fix khtml (Konqueror/Safari) issue
the "text/nonsense" prevents gecko based browsers to load it -->
<link rel="stylesheet" type="text/nonsense" href="{pwg_root}template/{themeconf:template}/fix-khtml.css">
<!--[if lt IE 7]>
  <link rel="stylesheet" type="text/css" href="{pwg_root}template/{themeconf:template}/fix-ie5-ie6.css">
<![endif]-->
<!--[if gt IE 6]>
  <link rel="stylesheet" type="text/css" href="{pwg_root}template/{themeconf:template}/fix-ie7.css">
<![endif]-->
<!--[if !IE]> <-->
   <link rel="stylesheet" href="{pwg_root}template/{themeconf:template}/not-ie.css" type="text/css">
<!--> <![endif]-->
<link rel="stylesheet" type="text/css" media="print" href="{pwg_root}template/{themeconf:template}/print.css">
<link rel="stylesheet" type="text/css" href="{pwg_root}template/{themeconf:template}/default-colors.css">
<link rel="stylesheet" type="text/css" href="{pwg_root}template/{themeconf:template}/theme/{themeconf:theme}/theme.css">
{themeconf:local_head}
<!-- BEGIN prefetch -->
<link rel="prefetch" href="{prefetch.URL}">
<!-- END prefetch -->
<!-- BEGIN refresh -->
<meta http-equiv="refresh" content="{REFRESH_TIME};url={U_REFRESH}">
<!-- END refresh -->
<script type="text/javascript" src="{pwg_root}template-common/scripts.js"></script>
<!--[if lt IE 7]>
  <style>
    /* only because we need \{pwg_root\} otherwise use fix-ie5-ie6.css */
    BODY { behavior:url("{pwg_root}template-common/csshover.htc"); }
    A IMG, .button, .icon {
      behavior:url("{pwg_root}template-common/tooltipfix.htc");
    }
    FORM { behavior: url("{pwg_root}template-common/inputfix.htc"); }
  </style>
  <script type="text/javascript" src="{pwg_root}template-common/pngfix.js"></script>
<![endif]-->
<!-- BEGIN head_element -->
{head_element.CONTENT}
<!-- END head_element -->
</head>

<body id="{BODY_ID}">
<div id="the_page">
<!-- BEGIN header_msgs -->
<div class="header_msgs">
  <!-- BEGIN header_msg -->
  <P>{header_msgs.header_msg.HEADER_MSG}</p>
  <!-- END header_msg -->
</div>
<!-- END header_msgs -->
<div id="theHeader">{PAGE_BANNER}</div>
<!-- BEGIN header_notes -->
<div class="header_notes">
  <!-- BEGIN header_note -->
  <P>{header_notes.header_note.HEADER_NOTE}</p>
  <!-- END header_note -->
</div>
<!-- END header_notes -->
