{* $Id$ *}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$T_CONTENT_ENCODING}">
<meta http-equiv="Content-script-type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<title>Piwigo {$RELEASE}</title>
{literal}
<style type="text/css">

body, input, select {
  background-color:#cde;
}

body {
  margin: 5px;
  padding: 0;
  font-size: 0.8em;
  font-family:  Univers, Helvetica, Optima, "Bitstream Vera Sans", sans-serif;
}

body, table, input, form, select {
  color:#369;
  text-align:left;
}

a {
  text-decoration: none;
  color: #c60;
}

a:hover {
  color: #f92;
}

table {
  border-collapse:separate;
}

.title {
  letter-spacing: 0.2em;
  text-align : center;
  font-size: 150%;
  font-weight: bold;
  padding: 0;
  margin: 0.5em 0 1em 0;
}

.contenucellule {
  border-color: #69c;
  background-color:#eee;
  margin:12px 20px;
  border-width: 3px;
  border-style: solid ;
}

.error_copy {
  color: #900;
}

th, .submit {
  text-align: center;
  font-weight: bold;
  background-color: #369;
}

th {
  font-size: 120%;
  margin-bottom:10px;
  color:#fff;
}

td.row {
  font-size: 90%;
}

.submit {
  color: #cde
}

.header {
  font-weight: normal;
  text-align: center;
  margin: 20px;
}

.infos {
  padding: 15px;
  font-weight: normal;
  text-align: left;
}

.infos_title {
  font-size: 150%;
  padding: 15px;
  font-weight: bold;
  text-align: left;
}

.errors {
  text-align: left;
  margin: 25px;
  color: #900;
  background-color: #ffe1e1;
  border:1px solid red;
}
</style>
{/literal}
</head>
<body>
  <table style="width:100%;height:100%">
    <tr align="center" valign="middle">
    <td>
    <div class="title">Piwigo {$RELEASE}</div>
      <table class="table1">
      {if isset($errors)}
      <tr>
      <td class="contenucellule" colspan="3">
        <div class="errors">
        <ul>
          {foreach from=$errors item=error}
          <li>{$error}</li>
          {/foreach}
        </ul>
        </div>
      </td>
      </tr>
      {/if}
      {if isset($infos)}
      <tr>
      <td class="contenucellule" colspan="3">
        <div class="infos">
        <ul>
          {foreach from=$infos item=info}
          <li>{$info}</li>
          {/foreach}
        </ul>
        </div>
      </td>
      </tr>
      {/if}
    <tr>
    <td class="contenucellule">

{if isset($error_copy)}
{'step1_err_copy'|@translate} :
<br />-----------------------------------------------------<br />
<div class="error_copy">{$error_copy}</div>
-----------------------------------------------------<br />
{/if}

{if isset($install)}
<form method="POST" action="{$F_ACTION}" name="install_form">
  <table>
    <tr>
      <th colspan="3">{'Initial_config'|@translate}</th>
    </tr>
    <tr>
      <td style="width:30%;">{'Default_lang'|@translate}</td>
      <td colspan="2" align="left">
    <select name="language" onchange="document.location = 'install.php?language='+this.options[this.selectedIndex].value;">
    {html_options options=$language_options selected=$language_selection}
    </select>
      </td>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <th colspan="3">{'step1_title'|@translate}</th>
    </tr>
    <tr>
      <td>{'step1_host'|@translate}</td>
      <td align=center><input type="text" name="dbhost" value="{$F_DB_HOST}" /></td>
      <td class="row">{'step1_host_info'|@translate}</td>
    </tr>
    <tr>
      <td>{'step1_user'|@translate}</td>
      <td align=center><input type="text" name="dbuser" value="{$F_DB_USER}" /></td>
      <td class="row">{'step1_user_info'|@translate}</td>
    </tr>
    <tr>
      <td>{'step1_pass'|@translate}</td>
      <td align=center><input type="password" name="dbpasswd" value="" /></td>
      <td class="row">{'step1_pass_info'|@translate}</td>
    </tr>
    <tr>
      <td>{'step1_database'|@translate}</td>
      <td align=center><input type="text" name="dbname" value="{$F_DB_NAME}" /></td>
      <td class="row">{'step1_database_info'|@translate}</td>
    </tr>
    <tr>
      <td>{'step1_prefix'|@translate}</td>
      <td align=center><input type="text" name="prefix" value="{$F_DB_PREFIX}" /></td>
      <td class="row">{'step1_prefix_info'|@translate}</td>
    </tr>
    <tr>
     <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <th colspan="3">{'step2_title'|@translate}</th>
    </tr>
    <tr>
      <td>{'install_webmaster'|@translate}</td>
      <td align="center"><input type="text" name="admin_name" value="{$F_ADMIN}" /></td>
      <td class="row">{'install_webmaster_info'|@translate}</td>
    </tr>
    <tr>
      <td>{'step2_pwd'|@translate}</td>
      <td align="center"><input type="password" name="admin_pass1" value="" /></td>
      <td class="row">{'step2_pwd_info'|@translate}</td>
    </tr>
    <tr>
      <td>{'step2_pwd_conf'|@translate}</td>
      <td align="center"><input type="password" name="admin_pass2" value="" /></td>
      <td class="row">{'step2_pwd_conf_info'|@translate}</td>
    </tr>
    <tr>
      <td>{'conf_mail_webmaster'|@translate}</td>
      <td align="center"><input type="text" name="admin_mail" value="{$F_ADMIN_EMAIL}" /></td>
      <td class="row">{'conf_mail_webmaster_info'|@translate}</td>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3" align="center">
        <input class="submit" type="submit" name="install" value="{'Start_Install'|@translate}" />
      </td>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
  </table>
</form>
{else}
<div class="infos_title">
{'install_end_title'|@translate}
</div>
<div class="infos">
{'install_end_message'|@translate}
</div>
{/if}

              </td>
            </tr>
          </table>
          <div class="header">{$L_INSTALL_HELP}</div>
        </td>
      </tr>
    </table>
  </body>
</html>
