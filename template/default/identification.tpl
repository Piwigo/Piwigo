<div class="titrePage">{L_TITLE}</div>
<div style="margin-top:15px;text-align:center;">
<table width="60%" cellpadding="4" cellspacing="1" border="0" align="center">
<form action="{F_LOGIN_ACTION}" method="post">
<!-- BEGIN errors -->
  <tr>
	<td colspan="2">
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
  <tr> 
	<td colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr> 
	<td width="45%" align="right"><span class="gentbl">{L_USERNAME}:</span></td>
	<td> 
	  <input class="login" type="text" name="username" size="25" maxlength="40" value="{USERNAME}" />
	</td>
  </tr>
  <tr> 
	<td align="right"><span class="gentbl">{L_PASSWORD}:</span></td>
	<td> 
	  <input class="login" type="password" name="password" size="25" maxlength="25" />
	</td>
  </tr>
  <tr align="center"> 
	<td colspan="2"><input type="submit" name="login" value="{L_LOGIN}" class="bouton" /></td>
  </tr>
</form>
</table>

<table style="width:80%;margin-left:auto;margin-right:auto;">
<tr>
  <td colspan="3" align="center">
	<!-- BEGIN free_access -->
	  <p><a href="./category.php">[ {L_GUEST} ]</a></p>
	  <a href="register.php"><img src="./template/{T_STYLE}/theme/register.gif" style="border:0;" alt=""/>&nbsp;{L_REGISTER}</a>&nbsp;&nbsp;-&nbsp;&nbsp;
	<!-- END free_access -->
	  <a href="mailto:{MAIL_ADMIN}?subject=[PhpWebGallery] {L_FORGET}"><img src="./template/{T_STYLE}/theme/lost.gif" style="border:0;" alt=""/>&nbsp;{L_FORGET}</a>
  </td>
</tr>
</table>
</div>