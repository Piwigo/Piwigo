<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html lang="{$lang_info.code}" dir="{$lang_info.direction}">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$T_CONTENT_ENCODING}">
<meta http-equiv="Content-script-type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="shortcut icon" type="image/x-icon" href="{$ROOT_URL}template-common/favicon.ico">
<link rel="stylesheet" type="text/css" href="{$ROOT_URL}admin/template/{$themeconf.template}/layout.css">
<link rel="stylesheet" type="text/css" href="{$ROOT_URL}admin/template/{$themeconf.template}/default-colors.css">
<link rel="stylesheet" type="text/css" href="{$ROOT_URL}admin/template/{$themeconf.template}/theme/{$themeconf.theme}/theme.css">
{include file="include/install.inc.tpl"}
{literal}
<style type="text/css">
.content {
 width: 800px;
 margin: auto;
 text-align: center;
}

.table2 {
  width: 100%;
  margin-bottom: 1em !important;
}

TD {
  text-align: left;
  padding: 0.1em 0.5em;
  height: 2.5em;
}

.sql_content, .infos a {
  color: #ff3363;
}
</style>
{/literal}
<title>Piwigo {$RELEASE} - {'Installation'|@translate}</title>
</head>

<body>
<div id="headbranch"></div> {* Dummy block for double background management *}
<div id="the_page">
<div id="theHeader"></div>
<div id="content" class="content">

<h2>Piwigo {$RELEASE} - {'Installation'|@translate}</h2>

{if isset($errors)}
<div class="errors">
  <ul>
    {foreach from=$errors item=error}
    <li>{$error}</li>
    {/foreach}
  </ul>
</div>
{/if}

{if isset($infos)}
<div class="infos">
  <ul>
    {foreach from=$infos item=info}
    <li>{$info}</li>
    {/foreach}
  </ul>
</div>
{/if}

{if isset($install)}
<form method="POST" action="{$F_ACTION}" name="install_form">

  <table class="table2">
    <tr class="throw">
      <th colspan="2">{'Basic configuration'|@translate}</th>
    </tr>
    <tr>
      <td style="width: 30%">{'Default gallery language'|@translate}</td>
      <td>
    <select name="language" onchange="document.location = 'install.php?language='+this.options[this.selectedIndex].value;">
    {html_options options=$language_options selected=$language_selection}
    </select>
      </td>
    </tr>
  </table>
  <table class="table2">
    <tr class="throw">
      <th colspan="3">{'Database configuration'|@translate}</th>
    </tr>
    {if count($F_DB_ENGINES)>1}
    <tr>
      <td style="width: 30%;">{'Database type'|@translate}</td>
      <td>
	<select name="dblayer" id="dblayer">
	  {foreach from=$F_DB_ENGINES key=k item=v}
	  <option value="{$k}"
		  {if $k==$F_DB_LAYER or $v.selected} selected="selected"{/if}
		  {if $v.available!=1} disabled="disabled"{/if}
		  >{$v.label}</option>
	  {/foreach}
	</select>    
      </td>
      <td>{'The type of database your piwigo data will be store in'|@translate}</td>
    {else}
    <td colspan="3">
    <input type="hidden" name="dbengine" value="{$F_DB_LAYER}">
    </td>
    {/if}
    </tr>
    <tr>
      <td style="width: 30%;">{'Host'|@translate}</td>
      <td align=center><input type="text" name="dbhost" value="{$F_DB_HOST}"></td>
      <td>{'localhost, sql.multimania.com, toto.freesurf.fr'|@translate}</td>
    </tr>
    <tr>
      <td>{'User'|@translate}</td>
      <td align=center><input type="text" name="dbuser" value="{$F_DB_USER}"></td>
      <td>{'user login given by your host provider'|@translate}</td>
    </tr>
    <tr>
      <td>{'Password'|@translate}</td>
      <td align=center><input type="password" name="dbpasswd" value=""></td>
      <td>{'user password given by your host provider'|@translate}</td>
    </tr>
    <tr>
      <td>{'Database name'|@translate}</td>
      <td align=center><input type="text" name="dbname" value="{$F_DB_NAME}"></td>
      <td>{'also given by your host provider'|@translate}</td>
    </tr>
    <tr>
      <td>{'Database table prefix'|@translate}</td>
      <td align=center><input type="text" name="prefix" value="{$F_DB_PREFIX}"></td>
      <td>{'database tables names will be prefixed with it (enables you to manage better your tables)'|@translate}</td>
    </tr>
  </table>

  <table class="table2">
    <tr class="throw">
      <th colspan="3">{'Admin configuration'|@translate}</th>
    </tr>
    <tr>
      <td style="width: 30%;">{'Webmaster login'|@translate}</td>
      <td align="center"><input type="text" name="admin_name" value="{$F_ADMIN}"></td>
      <td>{'It will be shown to the visitors. It is necessary for website administration'|@translate}</td>
    </tr>
    <tr>
      <td>{'Webmaster password'|@translate}</td>
      <td align="center"><input type="password" name="admin_pass1" value=""></td>
      <td>{'Keep it confidential, it enables you to access administration panel'|@translate}</td>
    </tr>
    <tr>
      <td>{'Password [confirm]'|@translate}</td>
      <td align="center"><input type="password" name="admin_pass2" value=""></td>
      <td>{'verification'|@translate}</td>
    </tr>
    <tr>
      <td>{'Webmaster mail address'|@translate}</td>
      <td align="center"><input type="text" name="admin_mail" value="{$F_ADMIN_EMAIL}"></td>
      <td>{'Visitors will be able to contact site administrator with this mail'|@translate}</td>
    </tr>
  </table>

  <table>
    <tr>
      <td style="text-align: center;">
        <input class="submit" type="submit" name="install" value="{'Start Install'|@translate}">
      </td>
    </tr>
  </table>
</form>
{else}
<p>
  <input type="button" name="Home" value="{'Home'|@translate}" onClick="window.open('index.php');">
  <input type="button" name="Administration" value="{'Administration'|@translate}" onClick="window.open('admin.php');">
</p>

{if !isset($migration)}
<div class="infos">
  <ul>
    <li>{'Keep in touch with Piwigo project, subscribe to Piwigo Announcement Newsletter. You will receive emails when a new release is available (sometimes including a security bug fix, it\'s important to know and upgrade) and when major events happen to the project. Only a few emails a year.'|@translate}</li>
  </ul>
</div>

<p>
  <input type="button" name="subscribe" value="{'Subscribe %s'|@translate|@sprintf:$F_ADMIN_EMAIL}" onClick="window.open('{$SUBSCRIBE_BASE_URL}{$F_ADMIN_EMAIL}');">
</p>
{/if}
{/if}
</div> {* content *}
<div style="text-align: center">{$L_INSTALL_HELP}</div>
</div> {* the_page *}
</body>
</html>
