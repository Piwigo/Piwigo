    <table style="width:100%;height:100%">
      <tr align="center" valign="middle">
        <td>
          {T_START}1px{T_BEGIN}
            <div class="titrePage">{L_TITLE}</div>
          {T_END}
          <div style="margin-bottom:5px;">&nbsp;</div>
          {T_START}50%{T_BEGIN}
            <div style="text-align:center;">
			<form action="{F_LOGIN_ACTION}" method="post">
			<table width="100%" cellpadding="4" cellspacing="1" border="0" align="center">
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
			  <input class="login" type="password" name="pass" size="25" maxlength="25" />
			</td>
		  </tr>
		  <tr align="center"> 
			<td colspan="2"><input type="submit" name="login" value="{L_LOGIN}" class="bouton" /></td>
		  </tr>

</table>
</form>
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
          {T_END}
        </td>
      </tr>
    </table>