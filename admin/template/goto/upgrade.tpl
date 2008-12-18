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
.content {
  width: 800px;
  min-height: 0px !important;
  margin: auto;
  padding: 25px;
  text-align: left;
}

table { margin: 0px; }
td {  padding: 3px 10px; }
textarea { margin-left: 20px; }
</style>
{/literal}
<title>Piwigo {$RELEASE} - {'Upgrade'|@translate}</title>
</head>

<body>
<div id="headbranch"></div> {* Dummy block for double background management *}
<div id="the_page">
<div id="theHeader"></div>
<div id="content" class="content">

{if isset($introduction)}
<h2>Piwigo {$RELEASE} - {'Upgrade'|@translate}</h2>

{if isset($errors)}
<div class="errors">
  <ul>
    {foreach from=$errors item=error}
    <li>{$error}</li>
    {/foreach}
  </ul>
</div>
{/if}

<table>
  <tr>
    <td>{'language'|@translate}</td>
    <td>
      <select name="language" onchange="document.location = 'upgrade.php?language='+this.options[this.selectedIndex].value;">
        {html_options options=$language_options selected=$language_selection}
      </select>
    </td>
  </tr>
</table>

<p>{'introduction message'|@translate|@sprintf:$introduction.CURRENT_RELEASE}</p>
{if isset($login)}
<p>{'upgrade login message'|@translate}</p>
{/if}

<form method="POST" action="{$introduction.F_ACTION}" name="upgrade_form">
{if isset($login)}
<table>
  <tr>
    <td>{'Username'|@translate}</td>
    <td><input type="text" name="username" id="username" size="25" maxlength="40" style="width: 150px;" /></td>
  </tr>
  <tr>
    <td>{'Password'|@translate}</td>
    <td><input type="password" name="password" id="password" size="25" maxlength="25" style="width: 150px;" /></td>
  </tr>
</table>
{/if}

<p style="text-align: center;">
<input class="submit" type="submit" name="submit" value="{'Upgrade from %s to %s'|@translate|@sprintf:$introduction.CURRENT_RELEASE:$RELEASE}"/>
</p>
</form>
<!--
<p style="text-align: center;">
<a href="{$introduction.RUN_UPGRADE_URL}">{'Upgrade from %s to %s'|@translate|@sprintf:$introduction.CURRENT_RELEASE:$RELEASE}</a>
</p>
-->

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

<form action="index.php" method="post">
<p><input type="submit" name="submit" value="{'home'|@translate}"/></p>
</form>
{/if}

</div> {* content *}
</div> {* the_page *}
</body>
</html>
