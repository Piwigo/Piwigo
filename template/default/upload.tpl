<div class="titrePage">{L_TITLE}</div>
<!-- BEGIN upload_not_successful -->
<form enctype="multipart/form-data" method="post" action="{F_ACTION}">
  <table style="width:80%;margin-left:auto;margin-right:auto;">
	<!-- BEGIN errors -->
		  <tr>
			<td colspan="2">
			  <div class="errors">
				<ul>
				  <!-- BEGIN error -->
				  <li>{upload_not_successful.errors.error.ERROR}</li>
				  <!-- END error -->
				</ul>
			  </div>
			</td>
		  </tr>
		  <!-- END errors -->
	<tr>
	  <td colspan="2" class="menu">
		<div style="text-align:center;">{ADVISE_TITLE}</div>
		<ul>
		  <!-- BEGIN advise -->
		  <li>{upload_not_successful.advise.ADVISE}</li>
		  <!-- END advise -->
		</ul>
	  </td>
	</tr>
	<tr>
	  <td colspan="2" align="center" style="padding:10px;">
		<input name="picture" type="file" value="" />
	  </td>
	</tr>
	<!-- BEGIN fields -->
	<!-- username  -->
	<tr>
	  <td class="menu">{L_USERNAME} <span style="color:red;">*</span></td>
	  <td align="center" style="padding:10px;">
		<input name="username" type="text" value="{NAME}" />
	  </td>
	</tr>
	<!-- mail address  -->
	<tr>
	  <td class="menu">{L_EMAIL} <span style="color:red;">*</span></td>
	  <td align="center" style="padding:10px;">
		<input name="mail_address" type="text" value="{EMAIL}" />
	  </td>
	</tr>
	<!-- name of the picture  -->
	<tr>
	  <td class="menu">{L_NAME_IMG}</td>
	  <td align="center" style="padding:10px;">
		<input name="name" type="text" value="{NAME_IMG}" />
	  </td>
	</tr>
	<!-- author  -->
	<tr>
	  <td class="menu">{L_AUTHOR}</td>
	  <td align="center" style="padding:10px;">
		<input name="author" type="text" value="{AUTHOR_IMG}" />
	  </td>
	</tr>
	<!-- date of creation  -->
	<tr>
	  <td class="menu">{L_CREATION_DATE}</td>
	  <td align="center" style="padding:10px;">
		<input name="date_creation" type="text" value="{DATE_IMG}" />
	  </td>
	</tr>
	<!-- comment  -->
	<tr>
	  <td class="menu">{L_COMMENT}</td>
	  <td align="center" style="padding:10px;">
	   <textarea name="comment" rows="3" cols="40" style="overflow:auto">{COMMENT_IMG}</textarea>
	  </td>
	</tr>
	<!-- END fields -->
	<tr>
	  <td colspan="2" align="center">
		<input name="submit" type="submit" value="{L_SUBMIT}" class="bouton" />
	  </td>
	</tr>
  </table>
</form>
<!-- END upload_not_successful -->
<!-- BEGIN upload_successful -->
{L_UPLOAD_DONE}<br />
<!-- END upload_successful -->
<div style="text-align:center;">
  <a href="{U_RETURN}">[ {L_RETURN} ]</a>
</div>
<!-- BEGIN note -->
<div style="text-align:left;"><span style="color:red;">*</span> : {L_MANDATORY}</div>
<!-- END note -->
