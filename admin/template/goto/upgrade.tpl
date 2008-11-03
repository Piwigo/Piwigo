{* $Id$ *}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html lang="{$lang_info.code}" dir="{$lang_info.direction}">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-script-type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="shortcut icon" type="image/x-icon" href="{$ROOT_URL}template-common/favicon.ico">
<link rel="stylesheet" type="text/css" href="{$ROOT_URL}admin/template/{$themeconf.template}/layout.css">
<link rel="stylesheet" type="text/css" href="{$ROOT_URL}admin/template/{$themeconf.template}/default-colors.css">
<link rel="stylesheet" type="text/css" href="{$ROOT_URL}admin/template/{$themeconf.template}/theme/{$themeconf.theme}/theme.css">
{literal}
<style type="text/css">
#theHeader { height: 105px; }

.content {
 width: 800px;
 min-height: 0px !important;
 margin: auto;
 padding: 25px;
 text-align: left;
}

h2 { width: 770px !important; }
</style>
{/literal}
<title>Piwigo {$RELEASE} - {'Upgrade'|@translate}</title>
</head>

<body>
<div id="headbranch"></div> {* Dummy block for double background management *}
<div id="theHeader"></div>
<div id="content" class="content">

{if isset($introduction)}
<h2>Piwigo {$RELEASE} - {'Upgrade'|@translate}</h2>

<p>{'language'|@translate} &nbsp;
<select name="language" onchange="document.location = 'upgrade.php?language='+this.options[this.selectedIndex].value;">
  {html_options options=$language_options selected=$language_selection}
</select>
</p>

<p>{'introduction message'|@translate|@sprintf:$introduction.CURRENT_RELEASE}</p>

<p style="text-align: center;">
<a href="{$introduction.RUN_UPGRADE_URL}">{'Upgrade from %s to %s'|@translate|@sprintf:$introduction.CURRENT_RELEASE:$RELEASE}</b>
</p>
{/if}

{if isset($upgrade)}
<h2>{'Upgrade from %s to %s'|@translate|@sprintf:$upgrade.VERSION:$RELEASE}</h2>

<p><b>{'Statistics'|@translate}</b></p>
<ul>
  <li>{'total upgrade time'|@translate} : {$upgrade.TOTAL_TIME}</li>
  <li>{'total SQL time'|@translate} : {$upgrade.SQL_TIME}</li>
  <li>{'SQL queries'|@translate} : {$upgrade.NB_QUERIES}</li>
</ul>

<p><b>{'Upgrade informations'|@translate}</b></p>
<ul>
  {foreach from=$infos item=info}
  <li>{$info}</li>
  {/foreach}
</ul>
{/if}

</div> {* content *}
</body>
</html>
