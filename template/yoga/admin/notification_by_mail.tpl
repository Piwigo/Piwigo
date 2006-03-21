<!-- $Id: notification_by_mail.tpl Ruben ARNAUD -->
<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="{themeconf:icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{lang:nbm_Send mail to users} [{U_TABSHEET_TITLE}]</h2>
  <h3>
    <p style="text-align:center;">
      <a href="{U_PARAM_MODE}">{lang:nbm_param_mode}</a> | 
      <a href="{U_SUBSCRIBE_MODE}">{lang:nbm_subscribe_mode}</a> | 
      <a href="{U_SEND_MODE}">{lang:nbm_send_mode}</a>
    </p>
  </h3>
</div>

<form method="post" name="notification_by_mail" action="{F_ACTION}">

  <!-- BEGIN param -->
  <fieldset>
    <legend><strong>{lang:nbm_title_param}</strong></legend>

    <table>
      <tr>
        <td><label for="send_detailed_content">{lang:nbm_send_detailed_content} </label></td>
        <td><input type="checkbox" name="send_detailed_content" id="send_detailed_content" value="true" {param.SEND_DETAILED_CONTENT}/></td>
      </tr>
     <tr>
        <td><label for="complementary_mail_content">{lang:nbm_complementary_mail_content} </label></td>
        <td><textarea cols="50" rows="5" name="complementary_mail_content" id="complementary_mail_content">{param.COMPLEMENTARY_MAIL_CONTENT}</textarea></td>
      </tr>
    </table>
  </fieldset>

  <p>
    <input type="submit" value="{lang:Submit}" name="param_submit" {TAG_INPUT_ENABLED} />
    <input type="reset" value="{lang:Reset}" name="param_reset" />
  </p>
  <!-- END param -->

  <!-- BEGIN subscribe -->
  <fieldset>
    <legend><strong>{lang:nbm_title_subscribe}</strong></legend>
    <legend><center><i>{lang:nbm_warning_subscribe_unsubcribe}</i></center></legend>
    {DOUBLE_SELECT}
  </fieldset>
  <!-- END subscribe -->

  <!-- BEGIN send -->
  <fieldset>
    <legend><strong>{lang:nbm_title_send}</strong></legend>
    {DOUBLE_SELECT}
  </fieldset>

  <p>
    <input type="submit" value="{lang:nbm_send_submit}" name="sene_submit" {TAG_INPUT_ENABLED} />
    <input type="reset" value="{lang:Reset}" name="send_reset" />
  </p>
  <!-- END send -->

</form>
