<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={T_CONTENT_ENCODING}"  />
<meta http-equiv="Content-script-type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<title>PhpWebGallery {RELEASE}</title>
<link rel="stylesheet" href="{T_STYLE}" type="text/css" />
</head>
<body>
    <table style="width:100%;height:100%">
      <tr align="center" valign="middle">
        <td>
	  <div class="grostitre">PhpWebGallery {RELEASE}</div>
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
<div style="color:blue;">{error_copy.FILE_CONTENT}</div>
-----------------------------------------------------<br />
<!-- END error_copy -->
<!-- BEGIN install -->
<form method="POST" action="{F_ACTION}" name="install_form">
  <table width="100%">
  	<tr>
      <th colspan="3">{L_BASE_TITLE}</th>
    </tr>
    <tr>
  	  <td width="30%">{L_LANG_TITLE}</td>
      <td colspan="2" align="left">
        <select name="language" onchange="this.form.submit()">
		  {F_LANG_SELECT}
        </select>
      </td>
    </tr>
    <tr>
      <th colspan="3">{L_DB_TITLE}</th>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <td>{L_DB_HOST}</td>
      <td align=center><input type="text" name="dbhost" value="{F_DB_HOST}" /></td>
      <td class="row2">{L_DB_HOST_INFO}</td>
    </tr>
    <tr>
      <td>{L_DB_USER}</td>
      <td align=center><input type="text" name="dbuser" value="{F_DB_USER}" /></td>
      <td class="row2">{L_DB_USER_INFO}</td>
    </tr>
    <tr>
      <td>{L_DB_PASS}</td>
      <td align=center><input type="password" name="dbpasswd" value="" /></td>
      <td class="row2">{L_DB_PASS_INFO}</td>
    </tr>
    <tr>
      <td>{L_DB_NAME}</td>
      <td align=center><input type="text" name="dbname" value="{F_DB_NAME}" /></td>
      <td class="row2">{L_DB_NAME_INFO}</td>
    </tr>
    <tr>
      <td>{L_DB_PREFIX}</td>
      <td align=center><input type="text" name="prefix" value="{F_DB_PREFIX}" /></td>
      <td class="row2">{L_DB_PREFIX_INFO}</td>
    </tr>
    <tr>
     <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <th colspan="3">{L_ADMIN_TITLE}</th>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <td>{L_ADMIN}</td>
      <td align="center"><input type="text" name="admin_name" value="{F_ADMIN}" /></td>
      <td class="row2">{L_ADMIN_INFO}</td>
    </tr>
    <tr>
      <td>{L_ADMIN_PASSWORD}</td>
      <td align="center"><input type="password" name="admin_pass1" value="" /></td>
      <td class="row2">{L_ADMIN_PASSWORD_INFO}</td>
    </tr>
    <tr>
      <td>{L_ADMIN_CONFIRM_PASSWORD}</td>
      <td align="center"><input type="password" name="admin_pass2" value="" /></td>
      <td class="row2">{L_ADMIN_CONFIRM_PASSWORD_INFO}</td>
    </tr>
    <tr>
      <td>{L_ADMIN_EMAIL}</td>
      <td align="center"><input type="text" name="admin_mail" value="{F_ADMIN_EMAIL}" /></td>
      <td class="row2">{L_ADMIN_EMAIL_INFO}</td>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</th>
    </tr>
    <tr>
      <td colspan="3" align="center">
        <input type="submit" name="install" value="{L_SUBMIT}" />
      </td>
    </tr>
  </table>
</form>
<!-- END install -->

<!-- BEGIN install_end -->
<div class="infos_title">{L_END_TITLE}</div>
<div style="padding:5px;">{L_END_MESSAGE}</div>
<!-- END install_end -->

              </td>
            </tr>
          </table>
          <div style="text-align:center;margin:20px;">{L_INSTALL_HELP}</div>
        </td>
      </tr>
    </table>
  </body>
</html>