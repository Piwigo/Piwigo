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
      <th colspan="2">{'Initial_config'|@translate}</th>
    </tr>
    <tr>
      <td style="width: 30%">{'Default_lang'|@translate}</td>
      <td>
    <select name="language" onchange="document.location = 'install.php?language='+this.options[this.selectedIndex].value;">
    {html_options options=$language_options selected=$language_selection}
    </select>
      </td>
    </tr>
  </table>
  <table class="table2">
    <tr class="throw">
      <th colspan="3">{'step1_title'|@translate}</th>
    </tr>
    {if count($F_DB_ENGINES)>1}
    <tr>
      <td style="width: 30%;">{'step1_dbengine'|@translate}</td>
      <td>
	<select name="dblayer">
	  {html_options options=$F_DB_ENGINES selected=$F_DB_LAYER}
	</select>    
      </td>
      <td>{'step1_dbengine_info'|@translate}</td>
    {else}
    <td colspan="3">
    <input type="hidden" name="dbengine" value="{$F_DB_LAYER}">
    </td>
    {/if}
    </tr>
    <tr>
      <td style="width: 30%;">{'step1_host'|@translate}</td>
      <td align=center><input type="text" name="dbhost" value="{$F_DB_HOST}"></td>
      <td>{'step1_host_info'|@translate}</td>
    </tr>
    <tr>
      <td>{'step1_user'|@translate}</td>
      <td align=center><input type="text" name="dbuser" value="{$F_DB_USER}"></td>
      <td>{'step1_user_info'|@translate}</td>
    </tr>
    <tr>
      <td>{'step1_pass'|@translate}</td>
      <td align=center><input type="password" name="dbpasswd" value=""></td>
      <td>{'step1_pass_info'|@translate}</td>
    </tr>
    <tr>
      <td>{'step1_database'|@translate}</td>
      <td align=center><input type="text" name="dbname" value="{$F_DB_NAME}"></td>
      <td>{'step1_database_info'|@translate}</td>
    </tr>
    <tr>
      <td>{'step1_prefix'|@translate}</td>
      <td align=center><input type="text" name="prefix" value="{$F_DB_PREFIX}"></td>
      <td>{'step1_prefix_info'|@translate}</td>
    </tr>
  </table>

  <table class="table2">
    <tr class="throw">
      <th colspan="3">{'step2_title'|@translate}</th>
    </tr>
    <tr>
      <td style="width: 30%;">{'install_webmaster'|@translate}</td>
      <td align="center"><input type="text" name="admin_name" value="{$F_ADMIN}"></td>
      <td>{'install_webmaster_info'|@translate}</td>
    </tr>
    <tr>
      <td>{'step2_pwd'|@translate}</td>
      <td align="center"><input type="password" name="admin_pass1" value=""></td>
      <td>{'step2_pwd_info'|@translate}</td>
    </tr>
    <tr>
      <td>{'step2_pwd_conf'|@translate}</td>
      <td align="center"><input type="password" name="admin_pass2" value=""></td>
      <td>{'step2_pwd_conf_info'|@translate}</td>
    </tr>
    <tr>
      <td>{'conf_mail_webmaster'|@translate}</td>
      <td align="center"><input type="text" name="admin_mail" value="{$F_ADMIN_EMAIL}"></td>
      <td>{'conf_mail_webmaster_info'|@translate}</td>
    </tr>
  </table>

  <table>
    <tr>
      <td style="text-align: center;">
        <input class="submit" type="submit" name="install" value="{'Start_Install'|@translate}">
      </td>
    </tr>
  </table>
</form>
{else}
<p>
  <br />
  <input type="button" name="home" value="{'home'|@translate}" onClick="window.open('index.php');"/>
  <input type="button" name="admin" value="{'admin'|@translate}" onClick="window.open('admin.php');"/>
</p>

<div class="infos">
  <ul>
    <li>{'Subscribe to Piwigo Announcements Newsletter'|@translate}</li>
  </ul>
</div>

<p>
  <br />
  <input type="button" name="subscribe" value="{'Subscribe %s'|@translate|@sprintf:$F_ADMIN_EMAIL}" onClick="window.open('{$SUBSCRIBE_BASE_URL}{$F_ADMIN_EMAIL}');"/>
</p>
{/if}
</div> {* content *}
<div style="text-align: center">{$L_INSTALL_HELP}</div>
</div> {* the_page *}
</body>
</html>
