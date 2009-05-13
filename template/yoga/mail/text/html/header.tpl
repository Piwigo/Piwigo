{* $Id: /piwigo/trunk/template/yoga/mail/text/html/header.tpl 6491 2008-10-04T09:50:02.198862Z rub  $ *}
-----={$BOUNDARY_KEY}
Content-Type: {$CONTENT_TYPE}; charset="{$CONTENT_ENCODING}";
Content-Transfer-Encoding: 8bit

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="{$lang_info.code}" dir="{$lang_info.direction}">
<head>
<title>Piwigo Mail</title>
<meta http-equiv="Content-Type" content="text/html; charset={$CONTENT_ENCODING}">
<style><!-- /* Mini style for mails */
{if isset($GLOBAL_MAIL_CSS)}{$GLOBAL_MAIL_CSS}{/if}
{if isset($MAIL_CSS)}{$MAIL_CSS}{/if}
{if isset($LOCAL_MAIL_CSS)}{$LOCAL_MAIL_CSS}{/if}
--></style>
</head>
<body>
<div id="the_page">
<div id="content" class="content">
