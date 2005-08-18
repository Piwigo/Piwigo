<h2>{L_TITLE}</h2>
<!-- TO DO -->
<!-- It's easy, just lok at identification.tpl ;-) -->

<form method="post" action="{F_ACTION}">
  <table style="width:60%;" align="center">
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
	  <td width="60%">{L_USERNAME}</td>
	  <td width="40%">
		<input type="text" name="login" value="{F_LOGIN}" />
	  </td>
	</tr>
	<tr>
	  <td >{L_PASSWORD}</td>
	  <td >
		<input type="password" name="password" />
	  </td>
	</tr>
	<tr>
	  <td >{L_CONFIRM_PASSWORD}</td>
	  <td >
		<input type="password" name="password_conf" />
	  </td>
	</tr>
	<tr>
	  <td >{L_EMAIL}</td>
	  <td >
		<input type="text" name="mail_address" value="{F_EMAIL}" />
	  </td>
	</tr>
	<tr>
	  <td colspan="2">&nbsp;</td>
	</tr>
	<tr>
	<tr>
	  <td colspan="2" align="center">
		<input type="submit" name="submit" value="{L_SUBMIT}" class="bouton" />
	  </td>
	</tr>
	<tr>
	  <td colspan="2" align="center">
		<a href="./category.php">[ {L_GUEST} ]</a>
	  </td>
	</tr>
  </table>
</form>
