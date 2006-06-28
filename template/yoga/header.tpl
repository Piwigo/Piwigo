<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html lang="{LANG}" dir="{DIR}">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={CONTENT_ENCODING}">
<link rel="shortcut icon" type="image/x-icon" href="{pwg_root}template-common/favicon.ico">
<link rel="stylesheet" type="text/css" href="{pwg_root}template/{themeconf:template}/layout.css">
<!-- the next css is used to fix khtml (Konqueror/Safari) issue
the "text/nonsense" prevents gecko based browsers to load it -->
<link rel="stylesheet" type="text/nonsense" href="{pwg_root}template/{themeconf:template}/fix-khtml.css">
<!--[if lt IE 7]>
  <link rel="stylesheet" type="text/css" href="{pwg_root}template/{themeconf:template}/fix-ie5-ie6.css">
<![endif]-->
<link rel="stylesheet" type="text/css" media="print" href="{pwg_root}template/{themeconf:template}/print.css">
<link rel="stylesheet" type="text/css" href="{pwg_root}template/{themeconf:template}/default-colors.css">
<link rel="stylesheet" type="text/css" href="{pwg_root}template/{themeconf:template}/theme/{themeconf:theme}/theme.css">
{themeconf:local_head}
<!-- BEGIN next -->
<link rel="prefetch" href="{next.U_IMG_SRC}">
<!-- END next -->
<!-- BEGIN refresh -->
<meta http-equiv="refresh" content="{REFRESH_TIME};url={U_REFRESH}">
<!-- END refresh -->
<title>{GALLERY_TITLE}:{PAGE_TITLE}</title>
<script type="text/javascript" src="{pwg_root}include/scripts.js"></script>
<!--[if lt IE 7]>
  <script type="text/javascript" src="{pwg_root}include/pngfix.js"></script>
<![endif]-->
</head>

<body id="{BODY_ID}">
<div id="the_page">
<!-- BEGIN header_msgs -->
<div class="header_msgs">
  <table>
    <!-- BEGIN header_msg -->
    <tr><td>{header_msgs.header_msg.HEADER_MSG}</td></tr>
    <!-- END header_msg -->
  </table>
</div>
<!-- END header_msgs -->
{PAGE_BANNER}
