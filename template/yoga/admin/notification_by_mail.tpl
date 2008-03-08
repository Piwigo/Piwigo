<!-- DEV TAG: not smarty migrated -->
<!-- $Id: notification_by_mail.tpl Ruben ARNAUD -->
<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="{themeconf:icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{lang:nbm_send_mail_to_users} {TABSHEET_TITLE}</h2>
  {TABSHEET}
</div>

<form method="post" name="notification_by_mail" id="notification_by_mail" action="{F_ACTION}">
  <!-- BEGIN repost -->
  <fieldset>
    <div class="infos">
      <input class="submit" type="submit" value="{lang:nbm_repost_submit}" name="{repost.REPOST_SUBMIT_NAME}" {TAG_INPUT_ENABLED}/>
    </div>
  </fieldset>
  <!-- END repost -->

  <!-- BEGIN param -->
  <fieldset>
    <legend>{lang:nbm_title_param}</legend>
    <table>
      <tr>
        <td><label>{lang:nbm_send_html_mail}</label></td>
        <td>
          <label><input type="radio" name="nbm_send_html_mail" value="true"  {param.SEND_HTML_MAIL_YES}/>{lang:Yes}</label>
          <label><input type="radio" name="nbm_send_html_mail" value="false" {param.SEND_HTML_MAIL_NO}/>{lang:No}</label>
        </td>
      </tr>
      <tr>
        <td>
          <label for="send_mail_as">{lang:nbm_send_mail_as}</label>
          <br><i><small>{lang:nbm_info_send_mail_as}</small></i>
        </td>
        <td><input type="text" maxlength="35" size="35" name="nbm_send_mail_as" id="send_mail_as" value="{param.SEND_MAIL_AS}"/></td>
      </tr>
      <tr>
        <td><label>{lang:nbm_send_detailed_content}</label></td>
        <td>
          <label><input type="radio" name="nbm_send_detailed_content" value="true"  {param.SEND_DETAILED_CONTENT_YES}/>{lang:Yes}</label>
          <label><input type="radio" name="nbm_send_detailed_content" value="false" {param.SEND_DETAILED_CONTENT_NO}/>{lang:No}</label>
        </td>
      </tr>
     <tr>
        <td><label for="complementary_mail_content">{lang:nbm_complementary_mail_content}</label></td>
        <td><textarea cols="50" rows="5" name="nbm_complementary_mail_content" id="complementary_mail_content">{param.COMPLEMENTARY_MAIL_CONTENT}</textarea></td>
      </tr>
      <tr>
        <td>
          <label>{lang:nbm_send_recent_post_dates}</label>
          <br><i><small>{lang:nbm_info_send_recent_post_dates}</small></i>
        </td>
        <td>
          <label><input type="radio" name="nbm_send_recent_post_dates" value="true"  {param.SEND_RECENT_POST_DATES_YES}/>{lang:Yes}</label>
          <label><input type="radio" name="nbm_send_recent_post_dates" value="false" {param.SEND_RECENT_POST_DATES_NO}/>{lang:No}</label>
        </td>
      </tr>
    </table>
  </fieldset>

  <p>
    <input class="submit" type="submit" value="{lang:Submit}" name="param_submit" {TAG_INPUT_ENABLED}/>
    <input class="submit" type="reset" value="{lang:Reset}" name="param_reset"/>
  </p>
  <!-- END param -->

  <!-- BEGIN subscribe -->
  <fieldset>
    <legend>{lang:nbm_title_subscribe}</legend>
    <p><i>{lang:nbm_warning_subscribe_unsubscribe}</i></p>
    {DOUBLE_SELECT}
  </fieldset>
  <!-- END subscribe -->

  <!-- BEGIN send -->
    <!-- BEGIN send_empty -->
    <p>{lang:nbm_no_user_available_to_send_L1}</p>
    <p>
    {lang:nbm_no_user_available_to_send_L2}<br>
    {lang:nbm_no_user_available_to_send_L3}
    </p>
    <!-- END send_empty -->
    <!-- BEGIN send_data -->
    <fieldset>
      <legend>{lang:nbm_title_send}</legend>
      <table class="table2">
        <tr class="throw">
          <th>{lang:nbm_col_user}</th>
          <th>{lang:nbm_col_mail}</th>
          <th>{lang:nbm_col_last_send}</th>
          <th>{lang:nbm_col_check_user_send_mail}</th>
        </tr>
        <!-- BEGIN user_send_mail -->
        <tr class="{send.send_data.user_send_mail.CLASS}">
          <td><label for="send_selection-{send.send_data.user_send_mail.ID}">{send.send_data.user_send_mail.USERNAME}</label></td>
          <td><label for="send_selection-{send.send_data.user_send_mail.ID}">{send.send_data.user_send_mail.EMAIL}</label></td>
          <td><label for="send_selection-{send.send_data.user_send_mail.ID}">{send.send_data.user_send_mail.LAST_SEND}</label></td>
          <td><input type="checkbox" name="send_selection[]" value="{send.send_data.user_send_mail.ID}" {send.send_data.user_send_mail.CHECKED} id="send_selection-{send.send_data.user_send_mail.ID}"/></td>
        </tr>
        <!-- END user_send_mail -->
      </table>
      <p>
          <a href="#" onclick="SelectAll(document.getElementById('notification_by_mail')); return false;">{lang:Check all}</a>
        / <a href="#" onclick="DeselectAll(document.getElementById('notification_by_mail')); return false;">{lang:Uncheck all}</a>
      </p>
    </fieldset>

    <fieldset>
      <legend>{lang:nbm_send_options}</legend>
      <table>
       <tr>
          <td><label for="send_customize_mail_content">{lang:nbm_send_complementary_mail_content}</label></td>
          <td><textarea cols="50" rows="5" name="send_customize_mail_content" id="send_customize_mail_content">{send.send_data.CUSTOMIZE_MAIL_CONTENT}</textarea></td>
        </tr>
      </table>
    </fieldset>

    <p>
      <input class="submit" type="submit" value="{lang:nbm_send_submit}" name="send_submit" {TAG_INPUT_ENABLED}/>
    </p>
    <!-- END send_data -->
  <!-- END send -->

</form>
