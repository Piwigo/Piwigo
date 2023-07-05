<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html lang="{$lang_info.code}" dir="{$lang_info.direction}">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-script-type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="shortcut icon" type="image/x-icon" href="{$ROOT_URL}{$themeconf.icon_dir}/favicon.ico">

{get_combined_css}
{foreach from=$themes item=theme}
{if $theme.load_css}
{combine_css path="admin/themes/`$theme.id`/theme.css" order=-10}
{/if}
{/foreach}

<!--[if IE 7]>
  <link rel="stylesheet" type="text/css" href="{$ROOT_URL}admin/themes/default/fix-ie7.css">
<![endif]-->

<!-- BEGIN get_combined_scripts -->
{get_combined_scripts load='header'}
<!-- END get_combined_scripts -->

{literal}
<style type="text/css">
body {
  font-size:12px;
}

.content {
 width: 800px;
 margin: auto;
 text-align: center;
 padding:0;
 background-color:transparent !important;
 border:none;
}

#content {
  min-height:0;
  border:none;
  margin:1em auto;
}

#theHeader {
  display: block;
  background:url("admin/themes/default/images/piwigo-orange.svg") no-repeat scroll center 20px transparent;
  height:100px;
  background-size: 300px;
}

fieldset {
  margin-top:20px;
  background-color:#f1f1f1;
}

legend {
  font-weight:bold;
  letter-spacing:2px;
}

form fieldset p {
  text-align:left;
  margin:10px;
}

.content h2 {
  display:block;
  font-size:20px;
  text-align:center;
  /* margin-top:5px; */
}

table.table2 {
  width: 100%;
  border:0;
}

table.table2 td {
  text-align: left;
  padding: 5px 2px;
}

table.table2 td.fieldname {
  font-weight:normal;
}

table.table2 td.fielddesc {
  padding-left:10px;
  font-style:italic;
}

input[type="submit"], input[type="button"], a.bigButton {
  font-size:14px;
  font-weight:bold;
  letter-spacing:2px;
  border:none;
  background-color:#666666;
  color:#fff;
  padding:5px;
  -moz-border-radius:5px;
}

input[type="submit"]:hover, input[type="button"]:hover, a.bigButton:hover {
  background-color:#ff7700;
  color:white;
  text-decoration:none;
}

input[type="text"], input[type="password"], select {
  background-color:#ddd;
  border:2px solid #ccc;
  -moz-border-radius:5px;
  padding:2px;
}

input[type="text"]:focus, input[type="password"]:focus, select:focus {
  background-color:#fff;
  border:2px solid #ff7700;
}

.sql_content, .infos a {
  color: #ff3363;
}

.errors {
  padding-bottom:5px;
}

</style>
{/literal}
<title>Piwigo {$RELEASE} - {'Upgrade'|@translate}</title>
</head>

<body>
<div id="the_page">
<div id="theHeader"></div>
<div id="content" class="content">

{if isset($introduction)}
<h2>{'Version'|@translate} {$RELEASE} - {'Upgrade'|@translate}</h2>

{if isset($errors)}
<div class="errors">
  <ul>
    {foreach from=$errors item=error}
    <li>{$error}</li>
    {/foreach}
  </ul>
</div>
{/if}

<form method="POST" action="{$introduction.F_ACTION}" name="upgrade_form">

<fieldset>
<table>
  <tr>
    <td>{'Language'|@translate}</td>
    <td>
      <select name="language" onchange="document.location = 'upgrade.php?language='+this.options[this.selectedIndex].value;">
        {html_options options=$language_options selected=$language_selection}
      </select>
    </td>
  </tr>
</table>

<p>{'This page proposes to upgrade your database corresponding to your old version of Piwigo to the current version. The upgrade assistant thinks you are currently running a <strong>release %s</strong> (or equivalent).'|@translate:$introduction.CURRENT_RELEASE}</p>
{if isset($login)}
<p>{'Only administrator can run upgrade: please sign in below.'|@translate}</p>
{/if}

{if isset($login)}
<table>
  <tr>
    <td>{'Username'|@translate}</td>
    <td><input type="text" name="username" id="username" size="20" maxlength="50" style="width: 150px;"></td>
  </tr>
  <tr>
    <td>{'Password'|@translate}</td>
    <td><input type="password" name="password" id="password" style="width: 150px;"></td>
  </tr>
</table>
{/if}
</fieldset>
<p style="text-align: center;">
<input class="submit" type="submit" name="submit" value="{'Upgrade from version %s to %s'|@translate:$introduction.CURRENT_RELEASE:$RELEASE}">
</p>
</form>
<!--
<p style="text-align: center;">
<a href="{$introduction.RUN_UPGRADE_URL}">{'Upgrade from version %s to %s'|@translate:$introduction.CURRENT_RELEASE:$RELEASE}</a>
</p>
-->

{/if}

{if isset($upgrade)}
<h2>{'Upgrade from version %s to %s'|@translate:$upgrade.VERSION:$RELEASE}</h2>

<fieldset>
<legend>{'Statistics'|@translate}</legend>
<ul>
  <li>{'total upgrade time'|@translate} : {$upgrade.TOTAL_TIME}</li>
  <li>{'total SQL time'|@translate} : {$upgrade.SQL_TIME}</li>
  <li>{'SQL queries'|@translate} : {$upgrade.NB_QUERIES}</li>
</ul>
</fieldset>

<fieldset>
<legend>{'Upgrade informations'|@translate}</legend>
<ul>
  {foreach from=$infos item=info}
  <li>{$info}</li>
  {/foreach}
</ul>
</fieldset>

<p>
  <a class="bigButton" href="{$button_link}">{$button_label}</a>
</p>
{/if}

</div> {* content *}
<div>{$L_UPGRADE_HELP}</div>
</div> {* the_page *}
</body>
</html>
