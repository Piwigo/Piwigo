<!-- BEGIN errors -->
<div class="errors">
<ul>
  <!-- BEGIN error -->
  <li>{errors.error.ERROR}</li>
  <!-- END error -->
</ul>
</div>
<!-- END errors -->
<!-- BEGIN confirmation -->
<div class="info">{L_CONFIRM}</div>
<!-- END confirmation -->
<form method="post" action="{F_ACTION}">
<table width="100%">
  <tr class="admin">
    <th colspan="3">{L_CONF_GENERAL}</th>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
  </tr>
  <tr>
    <td width="20%" >{L_ADMIN_NAME}</td>
	<td><input type="text" size="25" name="webmaster" value="{ADMIN_NAME}" /></td>
	<td width="50%" >{L_ADMIN_NAME_INFO}</td>
  </tr>
  <tr>
    <td>{L_ADMIN_MAIL}</td>
	<td><input type="text" size="25" maxlength="100" name="mail_webmaster" value="{ADMIN_MAIL}" /></td>
	<td>{L_ADMIN_MAIL_INFO}</td>
  </tr>
  <tr>
    <td>{L_THUMBNAIL_PREFIX}</td>
	<td><input type="text" size="3" maxlength="4" name="prefix_thumbnail" value="{THUMBNAIL_PREFIX}" /></td>
	<td>{L_THUMBNAIL_PREFIX_INFO}</td>
  </tr>
  <tr>
  <td>{L_ACCESS}</td>
	<td><input type="radio" class="radio" name="access" value="free" {ACCESS_FREE} />{L_ACCESS_FREE}&nbsp;&nbsp;
	<input type="radio" class="radio" name="access" value="restricted" {ACCESS_RESTRICTED} />{L_ACCESS_RESTRICTED}</td>
	<td>{L_ACCESS_INFO}</td>
  </tr>
  <tr>
    <td>{L_HISTORY}</td>
	<td><input type="radio" class="radio" name="log" value="true" {HISTORY_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="log" value="false" {HISTORY_NO} />{L_NO}</td>
	<td>{L_HISTORY_INFO}</td>
  </tr>
  <tr>
    <td>{L_MAIL_NOTIFICATION}</td>
	<td><input type="radio" class="radio" name="mail_notification" value="true" {MAIL_NOTIFICATION_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="mail_notification" value="false" {MAIL_NOTIFICATION_NO} />{L_NO}</td>
	<td>{L_MAIL_NOTIFICATION_INFO}</td>
  </tr>
    <tr>
    <td colspan="3">&nbsp;</td>
  </tr>
  <tr class="admin">
    <th colspan="3">{L_CONF_COMMENTS}</th>
  </tr>
    <tr>
    <td colspan="3">&nbsp;</td>
  </tr>
  <tr>
    <td>{L_SHOW_COMMENTS}</td>
	<td><input type="radio" class="radio" name="show_comments" value="true" {SHOW_COMMENTS_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="show_comments" value="false" {SHOW_COMMENTS_NO} />{L_NO}</td>
	<td>{L_SHOW_COMMENTS_INFO}</td>
  </tr>
  <tr>
    <td>{L_COMMENTS_ALL}</td>
	<td><input type="radio" class="radio" name="comments_forall" value="true" {COMMENTS_ALL_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="comments_forall" value="false" {COMMENTS_ALL_NO} />{L_NO}</td>
	<td>{L_COMMENTS_ALL_INFO}</td>
  </tr>
  <tr>
    <td>{L_NB_COMMENTS_PAGE}</td>
	<td><input type="text" size="3" maxlength="4" name="nb_comment_page" value="{NB_COMMENTS_PAGE}" /></td>
	<td>{L_NB_COMMENTS_PAGE_INFO}</td>
  </tr>
  <tr>
    <td>{L_VALIDATE_COMMENTS}</td>
	<td><input type="radio" class="radio" name="comments_validation" value="true" {VALIDATE_COMMENTS_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="comments_validation" value="false" {VALIDATE_COMMENTS_NO} />{L_NO}</td>
	<td>{L_VALIDATE_COMMENTS_INFO}</td>
  </tr>
    <tr>
    <td colspan="3">&nbsp;</td>
  </tr>
  <tr class="admin">
    <th colspan="3">{L_ABILITIES_SETTINGS}</th>
  </tr>
    <tr>
    <td colspan="3">&nbsp;</td>
  </tr>
  <tr>
    <td>{L_LANG_SELECT}</td>
	<td>{LANG_SELECT}</td>
	<td>{L_LANG_SELECT_INFO}</td>
  </tr>
  <tr>
    <td>{L_NB_IMAGE_LINE}</td>
	<td><input type="text" size="3" maxlength="2" name="nb_image_line" value="{NB_IMAGE_LINE}" /></td>
	<td>{L_NB_IMAGE_LINE_INFO}</td>
  </tr>
  <tr>
    <td>{L_NB_ROW_PAGE}</td>
	<td><input type="text" size="3" maxlength="2" name="nb_line_page" value="{NB_ROW_PAGE}" /></td>
	<td>{L_NB_ROW_PAGE_INFO}</td>
  </tr>
  <tr>
    <td>{L_STYLE_SELECT}</td>
	<td>{STYLE_SELECT}</td>
	<td>{L_STYLE_SELECT_INFO}</td>
  </tr>
  <tr>
    <td>{L_SHORT_PERIOD}</td>
	<td><input type="text" size="3" maxlength="2" name="short_period" value="{SHORT_PERIOD}" /></td>
	<td>{L_SHORT_PERIOD_INFO}</td>
  </tr>
  <tr>
    <td>{L_LONG_PERIOD}</td>
	<td><input type="text" size="3" maxlength="2" name="long_period" value="{LONG_PERIOD}" /></td>
	<td>{L_LONG_PERIOD_INFO}</td>
  </tr>
  <tr>
    <td>{L_EXPAND_TREE}</td>
	<td><input type="radio" class="radio" name="auto_expand" value="true" {EXPAND_TREE_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="auto_expand" value="false" {EXPAND_TREE_NO} />{L_NO}</td>
	<td>{L_EXPAND_TREE_INFO}</td>
  </tr>
  <tr>
    <td>{L_NB_COMMENTS}</td>
	<td><input type="radio" class="radio" name="show_nb_comments" value="true" {NB_COMMENTS_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="show_nb_comments" value="false" {NB_COMMENTS_NO} />{L_NO}</td>
	<td>{L_NB_COMMENTS_INFO}</td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
  </tr>
  <tr class="admin">
    <th colspan="3">{L_CONF_UPLOAD}</th>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
  </tr>
  <tr>
    <td>{L_UPLOAD}</td>
	<td><input type="radio" class="radio" name="upload_available" value="true" {UPLOAD_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="upload_available" value="false" {UPLOAD_NO} />{L_NO}</td>
	<td>{L_UPLOAD_INFO}</td>
  </tr>
  <tr>
    <td>{L_UPLOAD_MAXSIZE}</td>
	<td><input type="text" size="4" maxlength="4" name="upload_maxfilesize" value="{UPLOAD_MAXSIZE}" /></td>
	<td>{L_UPLOAD_MAXSIZE_INFO}</td>
  </tr>
  <tr>
    <td>{L_UPLOAD_MAXWIDTH}</td>
	<td><input type="text" size="4" maxlength="4" name="upload_maxwidth" value="{UPLOAD_MAXWIDTH}" /></td>
	<td>{L_UPLOAD_MAXWIDTH_INFO}</td>
  </tr>
  <tr>
    <td>{L_UPLOAD_MAXHEIGHT}</td>
	<td><input type="text" size="4" maxlength="4" name="upload_maxheight" value="{UPLOAD_MAXHEIGHT}" /></td>
	<td>{L_UPLOAD_MAXHEIGHT_INFO}</td>
  </tr>
    <tr>
    <td>{L_TN_UPLOAD_MAXWIDTH}</td>
	<td><input type="text" size="4" maxlength="4" name="upload_maxwidth_thumbnail" value="{TN_UPLOAD_MAXWIDTH}" /></td>
	<td>{L_TN_UPLOAD_MAXWIDTH_INFO}</td>
  </tr>
  <tr>
    <td>{L_TN_UPLOAD_MAXHEIGHT}</td>
	<td><input type="text" size="4" maxlength="4" name="upload_maxheight_thumbnail" value="{TN_UPLOAD_MAXHEIGHT}" /></td>
	<td>{L_TN_UPLOAD_MAXHEIGHT_INFO}</td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
  </tr>
  <tr class="admin">
    <th colspan="3">{L_CONF_SESSION}</th>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
  </tr>
    <tr>
    <td>{L_COOKIE}</td>
	<td><input type="radio" class="radio" name="authorize_cookies" value="true" {COOKIE_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="authorize_cookies" value="false" {COOKIE_NO} />{L_NO}</td>
	<td>{L_COOKIE_INFO}</td>
  </tr>
  <tr>
    <td>{L_SESSION_LENGTH}</td>
	<td><input type="text" size="4" maxlength="6" name="session_time" value="{SESSION_LENGTH}" /></td>
	<td>{L_SESSION_LENGTH_INFO}</td>
  </tr>
    <tr>
    <td>{L_SESSION_ID_SIZE}</td>
	<td><input type="text" size="2" maxlength="3" name="session_id_size" value="{SESSION_ID_SIZE}" /></td>
	<td>{L_SESSION_ID_SIZE_INFO}</td>
  </tr>
    <!-- BEGIN remote_sites -->
    <tr>
      <th colspan="3" align="center">{#remote_site}</th>
    </tr>
    <tr>
      <td colspan=3><div style='margin-bottom:0px'>&nbsp;</div></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="center">{#delete}</td>
      <td>&nbsp;</td>
    </tr>
    <!-- BEGIN site -->
    <tr>
      <td>{#url}</td>
      <td align="center"><input type="checkbox" name="delete_site_{#id}" value="1" /></td>
      <!-- BEGIN rowspan -->
	  <td class="row2" rowspan="{#nb_sites}">{#conf_remote_site_delete_info}</td>
	  <!-- END rowspan -->
    </tr>
    <!-- END site -->
    <tr>
      <td colspan=3><div style='margin-bottom:0px'>&nbsp;</div></td>
    </tr>
    <!-- END remote_sites -->
    <tr>
      <td colspan="3" align="center">
        <input type="submit" name="submit" class="bouton" value="{L_SUBMIT}">
      </td>
    </tr>
  </table>
</form>