    <table style="width:100%;height:100%">
      <tr align="center" valign="middle">
        <td>
          {T_START}1px{T_BEGIN}
            <div class="titrePage">{L_TITLE}</div>
          {T_END}
          <div style="margin-bottom:20px"></div>
          {T_START}50%{T_BEGIN}
            <form method="post" action="{F_ACTION}">
              <table style="width:80%;margin-top:10px;margin-bottom:10px;margin-left:auto;margin-right:auto;">
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
                  <td class="menu">{L_USERNAME}</td>
                  <td class="menu">
				    <input type="text" name="login" value="{F_LOGIN}" />
				  </td>
				</tr>
				<tr>
                  <td class="menu">{L_PASSWORD}</td>
                  <td class="menu">
				    <input type="password" name="password" />
				  </td>
				</tr>
				<tr>
                  <td class="menu">{L_CONFIRM_PASSWORD}</td>
                  <td class="menu">
				    <input type="password" name="password_conf" />
				  </td>
				</tr>
				<tr>
                  <td class="menu">{L_EMAIL}</td>
                  <td class="menu">
				    <input type="text" name="mail_address" value="{F_EMAIL}" />
				  </td>
				</tr>
                <tr>
                  <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                <tr>
                  <td colspan="2" align="center">
                    <input type="submit" name="submit" value="{L_SUBMIT}" style="margin:5px;"/>
                  </td>
                </tr>
                <tr>
                  <td colspan="2" align="center">
                    <a href="./category.php">[ {L_GUEST} ]</a>
                  </td>
                </tr>
              </table>
            </form>
          {T_END}
        </td>
      </tr>
    </table>