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
<table width="100%" align="center">
  <tr class="admin">
    <th colspan="2">{L_CONF_GENERAL}</th>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td width="50%" ><strong>{L_ADMIN_NAME} &nbsp;:</strong><br /><span class="small">{L_ADMIN_NAME_INFO}</span></td>
	<td class="row1"><input type="text" size="25" name="webmaster" value="{ADMIN_NAME}" /></td>
  </tr>
  <tr>
    <td><strong>{L_ADMIN_MAIL}&nbsp;:</strong><br /><span class="small">{L_ADMIN_MAIL_INFO}</span></td>
	<td class="row1"><input type="text" size="25" maxlength="100" name="mail_webmaster" value="{ADMIN_MAIL}" /></td>
  </tr>
  <tr>
    <td><strong>{L_THUMBNAIL_PREFIX}&nbsp;:</strong><br /><span class="small">{L_THUMBNAIL_PREFIX_INFO}</span></td>
	<td class="row1"><input type="text" size="3" maxlength="4" name="prefix_thumbnail" value="{THUMBNAIL_PREFIX}" /></td>
  </tr>
  <tr>
  <td><strong>{L_ACCESS}&nbsp;:</strong><br /><span class="small">{L_ACCESS_INFO}</span></td>
	<td class="row1"><input type="radio" class="radio" name="access" value="free" {ACCESS_FREE} />{L_ACCESS_FREE}&nbsp;&nbsp;
	<input type="radio" class="radio" name="access" value="restricted" {ACCESS_RESTRICTED} />{L_ACCESS_RESTRICTED}</td>
  </tr>
  <tr>
    <td><strong>{L_CONF_HISTORY}&nbsp;:</strong><br /><span class="small">{L_CONF_HISTORY_INFO}</span></td>
	<td class="row1"><input type="radio" class="radio" name="log" value="true" {HISTORY_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="log" value="false" {HISTORY_NO} />{L_NO}</td>
  </tr>
  <tr>
    <td><strong>{L_MAIL_NOTIFICATION}&nbsp;:</strong><br /><span class="small">{L_MAIL_NOTIFICATION_INFO}</span></td>
	<td class="row1"><input type="radio" class="radio" name="mail_notification" value="true" {MAIL_NOTIFICATION_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="mail_notification" value="false" {MAIL_NOTIFICATION_NO} />{L_NO}</td>
  </tr>
    <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr class="admin">
    <th colspan="2">{L_CONF_COMMENTS}</th>
  </tr>
    <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td><strong>{L_SHOW_COMMENTS}&nbsp;:</strong><br /><span class="small">{L_SHOW_COMMENTS_INFO}</span></td>
	<td class="row1"><input type="radio" class="radio" name="show_comments" value="true" {SHOW_COMMENTS_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="show_comments" value="false" {SHOW_COMMENTS_NO} />{L_NO}</td>
  </tr>
  <tr>
    <td><strong>{L_COMMENTS_ALL}&nbsp;:</strong><br /><span class="small">{L_NB_COMMENTS_PAGE_INFO}</span></td>
	<td class="row1"><input type="radio" class="radio" name="comments_forall" value="true" {COMMENTS_ALL_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="comments_forall" value="false" {COMMENTS_ALL_NO} />{L_NO}</td>
  </tr>
  <tr>
    <td><strong>{L_NB_COMMENTS_PAGE}&nbsp;:</strong><br /><span class="small">{L_NB_COMMENTS_PAGE_INFO}</span></td>
	<td class="row1"><input type="text" size="3" maxlength="4" name="nb_comment_page" value="{NB_COMMENTS_PAGE}" /></td>
  </tr>
  <tr>
    <td><strong>{L_VALIDATE_COMMENTS}&nbsp;:</strong><br /><span class="small">{L_VALIDATE_COMMENTS_INFO}</span></td>
	<td class="row1"><input type="radio" class="radio" name="comments_validation" value="true" {VALIDATE_COMMENTS_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="comments_validation" value="false" {VALIDATE_COMMENTS_NO} />{L_NO}</td>
  </tr>
    <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr class="admin">
    <th colspan="2">{L_ABILITIES_SETTINGS}</th>
  </tr>
    <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td><strong>{L_LANG_SELECT}&nbsp;:</strong><br /><span class="small">{L_LANG_SELECT_INFO}</span></td>
	<td class="row1">{LANG_SELECT}</td>
  </tr>
  <tr>
    <td><strong>{L_NB_IMAGE_LINE}&nbsp;:</strong><br /><span class="small">{L_NB_IMAGE_LINE_INFO}</span></td>
	<td class="row1"><input type="text" size="3" maxlength="2" name="nb_image_line" value="{NB_IMAGE_LINE}" /></td>
  </tr>
  <tr>
    <td><strong>{L_NB_ROW_PAGE}&nbsp;:</strong><br /><span class="small">{L_NB_ROW_PAGE_INFO}</span></td>
	<td class="row1"><input type="text" size="3" maxlength="2" name="nb_line_page" value="{NB_ROW_PAGE}" /></td>
  </tr>
  <tr>
    <td><strong>{L_STYLE_SELECT}&nbsp;:</strong><br /><span class="small">{L_STYLE_SELECT_INFO}</span></td>
	<td class="row1">{STYLE_SELECT}</td>
  </tr>
  <tr>
    <td><strong>{L_RECENT_PERIOD}&nbsp;:</strong><br /><span class="small">{L_RECENT_PERIOD_INFO}</span></td>
	<td class="row1"><input type="text" size="3" maxlength="2" name="recent_period" value="{RECENT_PERIOD}" /></td>
  </tr>
  <tr>
    <td><strong>{L_EXPAND_TREE}&nbsp;:</strong><br /><span class="small">{L_EXPAND_TREE_INFO}</span></td>
	<td class="row1"><input type="radio" class="radio" name="auto_expand" value="true" {EXPAND_TREE_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="auto_expand" value="false" {EXPAND_TREE_NO} />{L_NO}</td>
  </tr>
  <tr>
    <td><strong>{L_NB_COMMENTS}&nbsp;:</strong><br /><span class="small">{L_NB_COMMENTS_INFO}</span></td>
	<td class="row1"><input type="radio" class="radio" name="show_nb_comments" value="true" {NB_COMMENTS_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="show_nb_comments" value="false" {NB_COMMENTS_NO} />{L_NO}</td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr class="admin">
    <th colspan="2">{L_CONF_UPLOAD}</th>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td><strong>{L_AUTH_UPLOAD}&nbsp;:</strong><br /><span class="small">{L_AUTH_UPLOAD_INFO}</span></td>
	<td class="row1"><input type="radio" class="radio" name="upload_available" value="true" {UPLOAD_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="upload_available" value="false" {UPLOAD_NO} />{L_NO}</td>
  </tr>
  <tr>
    <td><strong>{L_UPLOAD_MAXSIZE}&nbsp;:</strong><br /><span class="small">{L_UPLOAD_MAXSIZE_INFO}</span></td>
	<td class="row1"><input type="text" size="4" maxlength="4" name="upload_maxfilesize" value="{UPLOAD_MAXSIZE}" /></td>
  </tr>
  <tr>
    <td><strong>{L_UPLOAD_MAXWIDTH}&nbsp;:</strong><br /><span class="small">{L_UPLOAD_MAXWIDTH_INFO}</span></td>
	<td class="row1"><input type="text" size="4" maxlength="4" name="upload_maxwidth" value="{UPLOAD_MAXWIDTH}" /></td>
  </tr>
  <tr>
    <td><strong>{L_UPLOAD_MAXHEIGHT}&nbsp;:</strong><br /><span class="small">{L_UPLOAD_MAXHEIGHT_INFO}</span></td>
	<td class="row1"><input type="text" size="4" maxlength="4" name="upload_maxheight" value="{UPLOAD_MAXHEIGHT}" /></td>
  </tr>
    <tr>
    <td><strong>{L_TN_UPLOAD_MAXWIDTH}&nbsp;:</strong><br /><span class="small">{L_TN_UPLOAD_MAXWIDTH_INFO}</span></td>
	<td class="row1"><input type="text" size="4" maxlength="4" name="upload_maxwidth_thumbnail" value="{TN_UPLOAD_MAXWIDTH}" /></td>
  </tr>
  <tr>
    <td><strong>{L_TN_UPLOAD_MAXHEIGHT}&nbsp;:</strong><br /><span class="small">{L_TN_UPLOAD_MAXHEIGHT_INFO}</span></td>
	<td class="row1"><input type="text" size="4" maxlength="4" name="upload_maxheight_thumbnail" value="{TN_UPLOAD_MAXHEIGHT}" /></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr class="admin">
    <th colspan="2">{L_CONF_SESSION}</th>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
    <tr>
    <td><strong>{L_COOKIE}&nbsp;:</strong><br /><span class="small">{L_COOKIE_INFO}</span></td>
	<td class="row1"><input type="radio" class="radio" name="authorize_cookies" value="true" {COOKIE_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="authorize_cookies" value="false" {COOKIE_NO} />{L_NO}</td>
  </tr>
  <tr>
    <td><strong>{L_SESSION_LENGTH}&nbsp;:</strong><br /><span class="small">{L_SESSION_LENGTH_INFO}</span></td>
	<td class="row1"><input type="text" size="4" maxlength="6" name="session_time" value="{SESSION_LENGTH}" /></td>
  </tr>
    <tr>
    <td><strong>{L_SESSION_ID_SIZE}&nbsp;:</strong><br /><span class="small">{L_SESSION_ID_SIZE_INFO}</span></td>
	<td class="row1"><input type="text" size="2" maxlength="3" name="session_id_size" value="{SESSION_ID_SIZE}" /></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr class="admin">
    <th colspan="2">{L_CONF_METADATA}</th>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td>
      <strong>{L_USE_EXIF}&nbsp;:</strong>
      <br /><span class="small">{L_USE_EXIF_INFO}</span>
    </td>
    <td class="row1">
      <input type="radio" class="radio" name="use_exif" value="true" {USE_EXIF_YES} />{L_YES}&nbsp;&nbsp;
      <input type="radio" class="radio" name="use_exif" value="false" {USE_EXIF_NO} />{L_NO}
    </td>
  </tr>
  <tr>
    <td>
      <strong>{L_USE_IPTC}&nbsp;:</strong>
      <br /><span class="small">{L_USE_IPTC_INFO}</span>
    </td>
    <td class="row1">
      <input type="radio" class="radio" name="use_iptc" value="true" {USE_IPTC_YES} />{L_YES}&nbsp;&nbsp;
      <input type="radio" class="radio" name="use_iptc" value="false" {USE_IPTC_NO} />{L_NO}
    </td>
  </tr>
  <tr>
    <td>
      <strong>{L_SHOW_EXIF}&nbsp;:</strong>
      <br /><span class="small">{L_SHOW_EXIF_INFO}</span>
    </td>
    <td class="row1">
      <input type="radio" class="radio" name="show_exif" value="true" {SHOW_EXIF_YES} />{L_YES}&nbsp;&nbsp;
      <input type="radio" class="radio" name="show_exif" value="false" {SHOW_EXIF_NO} />{L_NO}
    </td>
  </tr>
  <tr>
    <td>
      <strong>{L_SHOW_IPTC}&nbsp;:</strong>
      <br /><span class="small">{L_SHOW_IPTC_INFO}</span>
    </td>
    <td class="row1">
      <input type="radio" class="radio" name="show_iptc" value="true" {SHOW_IPTC_YES} />{L_YES}&nbsp;&nbsp;
      <input type="radio" class="radio" name="show_iptc" value="false" {SHOW_IPTC_NO} />{L_NO}
    </td>
  </tr>
    <!-- BEGIN remote_sites -->
    <tr>
      <th colspan="2" align="center">{#remote_site}</th>
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
	  <td class="row2" rowspan="{#nb_sites}">{#conf_remote_site_delete_INFO}</span></td>
	  <!-- END rowspan -->
    </tr>
    <!-- END site -->
    <tr>
      <td colspan=3><div style='margin-bottom:0px'>&nbsp;</div></td>
    </tr>
    <!-- END remote_sites -->
    <tr>
      <td colspan="2" align="center">
        <input type="submit" name="submit" class="bouton" value="{L_SUBMIT}">
      </td>
    </tr>
  </table>
</form>
