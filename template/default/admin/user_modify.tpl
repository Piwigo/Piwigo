<!--VTP_errors-->
<div class="errors">
  <ul>
    <!--VTP_li-->
    <li>{#li}</li>
    <!--/VTP_li-->
  </ul>
</div>
<!--/VTP_errors-->
<!--VTP_confirmation-->
<div class="info">
  {#adduser_info_message} "{#username}"
  <!--VTP_password_updated-->
  {#adduser_info_password_updated}
  <!--/VTP_password_updated-->
  [ <a href="{#url}">{#adduser_info_back}</a> ]
</div>
<!--/VTP_confirmation-->
<!--VTP_form-->
<form method="post" action="{#form_action}">
  <table style="width:100%;">
    <tr align="center" valign="middle">
      <td>
        <table style="margin-left:auto;margin-right:auto;">
          <tr>
            <th colspan="2">{#adduser_fill_form}</th>
          </tr>
          <tr>
            <td colspan="2"><div style="margin-bottom:0px;">&nbsp;</div></td>
          </tr>
          <tr>
            <td>{#login}</td>
            <td style="color:red;text-align:center;">{#user:username}</td>
          </tr>
          <tr>
            <td>{#new} {#password}<input type="checkbox" name="use_new_pwd" value="1" /></td>
            <td><input type="text" name="password" value="" /></td>
          </tr>
          <tr>
            <td>{#mail_address}</td>
            <td><input type="text" name="mail_address" value="{#user:mail_address}" /></td>
          </tr>

          <!--VTP_status-->
          <tr>
            <td>{#adduser_status}</td>
            <td>
              <select name="status">
                <!--VTP_status_option-->
                <option value="{#value}"{#selected}>{#option}</option>
                <!--/VTP_status_option-->
              </select>
            </td>
          </tr>
          <!--/VTP_status-->

          <!--VTP_groups-->
          <tr>
            <td valign="top">{#menu_groups}</td>
            <td>
              <table>
                <!--VTP_group-->
                <tr>
                  <td>{#name}</td>
                  <td><input type="checkbox" name="dissociate-{#dissociate_id}" value="1" /> {#dissociate}</td>
                </tr>
                <!--/VTP_group-->
              </table>
            </td>
          </tr>
          <!--/VTP_groups-->
          <tr>
            <td>{#adduser_associate}</td>
            <td>
              <select name="associate">
                <!--VTP_associate_group-->
                <option value="{#value}">{#option}</option>
                <!--/VTP_associate_group-->
              </select>
            </td>
          </tr>
          <tr>
            <td colspan="2" align="center">
              <input type="submit" name="submit" value="{#submit}" />
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>
<div class="info">
  [ <a href="{#url_back}">{#adduser_info_back}</a> ]
</div>
<!--/VTP_form-->