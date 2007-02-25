<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={T_CONTENT_ENCODING}">
<meta http-equiv="Content-script-type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<title>PhpWebGallery {RELEASE}</title>
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
</head>
<body>
  <table style="width:100%;height:100%">
    <tr align="center" valign="middle">
    <td>
    <div class="title">PhpWebGallery {RELEASE}</div>
      <table class="table1">
      <!-- BEGIN errors -->
      <tr>
      <td class="contenucellule" colspan="3">
        <div class="errors">
        <ul>
          <!-- BEGIN error -->
          <li>{errors.error.ERROR}</li>
          <!-- END error -->
        </ul>
        </div>
      </td>
      </tr>
      <!-- END errors -->
      <!-- BEGIN infos -->
      <tr>
      <td class="contenucellule" colspan="3">
        <div class="infos">
        <ul>
          <!-- BEGIN info -->
          <li>{infos.info.INFO}</li>
          <!-- END info -->
        </ul>
        </div>
      </td>
      </tr>
      <!-- END infos -->
    <tr>
    <td class="contenucellule">

<!-- BEGIN error_copy -->
{L_ERR_COPY} :
<br />-----------------------------------------------------<br />
<div class="error_copy">{error_copy.FILE_CONTENT}</div>
-----------------------------------------------------<br />
<!-- END error_copy -->
<!-- BEGIN install -->
<form method="POST" action="{F_ACTION}" name="install_form">
  <table>
    <tr>
      <th colspan="3">{L_BASE_TITLE}</th>
    </tr>
    <tr>
      <td style="width:30%;">{L_LANG_TITLE}</td>
      <td colspan="2" align="left">
    {F_LANG_SELECT}
      </td>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <th colspan="3">{L_DB_TITLE}</th>
    </tr>
    <tr>
      <td>{L_DB_HOST}</td>
      <td align=center><input type="text" name="dbhost" value="{F_DB_HOST}" /></td>
      <td class="row">{L_DB_HOST_INFO}</td>
    </tr>
    <tr>
      <td>{L_DB_USER}</td>
      <td align=center><input type="text" name="dbuser" value="{F_DB_USER}" /></td>
      <td class="row">{L_DB_USER_INFO}</td>
    </tr>
    <tr>
      <td>{L_DB_PASS}</td>
      <td align=center><input type="password" name="dbpasswd" value="" /></td>
      <td class="row">{L_DB_PASS_INFO}</td>
    </tr>
    <tr>
      <td>{L_DB_NAME}</td>
      <td align=center><input type="text" name="dbname" value="{F_DB_NAME}" /></td>
      <td class="row">{L_DB_NAME_INFO}</td>
    </tr>
    <tr>
      <td>{L_DB_PREFIX}</td>
      <td align=center><input type="text" name="prefix" value="{F_DB_PREFIX}" /></td>
      <td class="row">{L_DB_PREFIX_INFO}</td>
    </tr>
    <tr>
     <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <th colspan="3">{L_ADMIN_TITLE}</th>
    </tr>
    <tr>
      <td>{L_ADMIN}</td>
      <td align="center"><input type="text" name="admin_name" value="{F_ADMIN}" /></td>
      <td class="row">{L_ADMIN_INFO}</td>
    </tr>
    <tr>
      <td>{L_ADMIN_PASSWORD}</td>
      <td align="center"><input type="password" name="admin_pass1" value="" /></td>
      <td class="row">{L_ADMIN_PASSWORD_INFO}</td>
    </tr>
    <tr>
      <td>{L_ADMIN_CONFIRM_PASSWORD}</td>
      <td align="center"><input type="password" name="admin_pass2" value="" /></td>
      <td class="row">{L_ADMIN_CONFIRM_PASSWORD_INFO}</td>
    </tr>
    <tr>
      <td>{L_ADMIN_EMAIL}</td>
      <td align="center"><input type="text" name="admin_mail" value="{F_ADMIN_EMAIL}" /></td>
      <td class="row">{L_ADMIN_EMAIL_INFO}</td>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3" align="center">
        <input class="submit" type="submit" name="install" value="{L_SUBMIT}" />
      </td>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
  </table>
</form>
<!-- END install -->

<!-- BEGIN install_end -->
<div class="infos_title">
{L_END_TITLE}
</div>
<div class="infos">
{L_END_MESSAGE}
</div>
<!-- END install_end -->

              </td>
            </tr>
          </table>
          <div class="header">{L_INSTALL_HELP}</div>
        </td>
      </tr>
    </table>
  </body>
</html>
