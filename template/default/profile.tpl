    <table style="width:100%;height:100%">
      <tr align="center" valign="middle">
        <td>
          {T_START}1px{T_BEGIN}
            <div class="titrePage">{L_TITLE}</div>
          {T_END}
          <div style="margin-bottom:20px"></div>
          {T_START}50%{T_BEGIN}
            <form method="post" action="{F_ACTION}">
              <table style="width:100%;margin-top:10px;margin-bottom:10px;">
                <tr align="center" valign="middle">
                  <td>
                    <table width="80%">
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
					  <!-- BEGIN select -->
                      <tr>
                        <td class="menu">{select.F_LABEL}</td>
                        <td class="menu">
                          <select name="{select.F_NAME}">
                            {select.F_OPTIONS}
                          </select>
						</td>
					  </tr>
					  <!-- END select -->
					  <!-- BEGIN text -->
                      <tr>
                        <td class="menu">{text.F_LABEL}</td>
                        <td class="menu">
                          <input type="text" name="{text.F_NAME}" value="{text.F_VALUE}" />
						</td>
					  </tr>
					  <!-- END text -->
					  <!-- BEGIN radio -->
                      <tr>
                        <td class="menu">{radio.F_LABEL}</td>
                        <td class="menu">
  						  {radio.F_OPTIONS}
						</td>
					  </tr>
					  <!-- END radio -->
                      <tr>
                        <td colspan="2">&nbsp;</td>
                      </tr>
                      <tr>
                        <td class="menu">{L_NEW} {L_PASSWORD} <input type="checkbox" name="use_new_pwd" value="1" /></td>
                        <td class="menu"><input type="password" name="password" value="" /></td>
                      </tr>
                      <tr>
                        <td class="menu">{L_CONFIRM}</td>
                        <td class="menu"><input type="password" name="passwordConf" value="" /></td>
                      </tr>
                      <!-- BEGIN cookie -->
                      <tr>
                        <td colspan="2">&nbsp;</td>
                      </tr>
                      <tr>
                        <td class="menu">{L_COOKIE} <input type="checkbox" name="create_cookie" value="1" /></td>
                        <td class="menu">
                          <select name="cookie_expiration">
                          <!-- BEGIN expiration_option -->
                            <option value="{#value}">{#option}</option>
                          <!-- END expiration_option -->
                          </select>
                        </td>
                      </tr>
                      <!-- END cookie -->
                      <tr>
                        <td colspan="2">&nbsp;</td>
                      </tr>
                      <tr>
                        <td colspan="2" align="center">
                          <input type="submit" name="submit" value="{L_SUBMIT}" style="margin:5px;"/>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </form>
          {T_END}
</td>
      </tr>
    </table>