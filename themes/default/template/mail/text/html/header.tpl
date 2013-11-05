<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="{$lang_info.code}" dir="{$lang_info.direction}">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset={$CONTENT_ENCODING}"/>
    <title>Piwigo Mail</title>

    <style type="text/css">
    {if isset($GLOBAL_MAIL_CSS)}{$GLOBAL_MAIL_CSS}{/if}
    {if isset($MAIL_CSS)}{$MAIL_CSS}{/if}
    </style>
  </head>

  <body>
    <table id="bodyTable" cellspacing="0" cellpadding="10" border="0">
      <tr><td align="center" valign="top">

        <table id="contentTable" cellspacing="0" cellpadding="0" border="0">
          <tr><td id="header">
            {* <!-- begin HEADER --> *}
            <div id="title">{$MAIL_TITLE}</div>
            {if not empty($MAIL_SUBTITLE)}<div id="subtitle">{$MAIL_SUBTITLE}</div>{/if}
            {* <!-- end HEADER --> *}
          </td></tr>

          <tr><td id="content">
{* <!-- use an invisible div with a bottom margin to force the browser 
to merge the margin with the element bellow (typically h1/h2/...) --> *}
            <div id="topSpacer"></div>
            {* <!-- begin CONTENT --> *}