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
<!-- BEGIN general -->
  <tr class="admin">
    <th colspan="2">{general.L_CONF_TITLE}</th>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td width="70%"><strong>{general.L_CONF_MAIL}&nbsp;:</strong><br /><span class="small">{general.L_CONF_MAIL_INFO}</span></td>
	<td class="row1"><input type="text" size="25" maxlength="100" name="mail_webmaster" value="{general.ADMIN_MAIL}" /></td>
  </tr>
  <tr>
    <td><strong>{general.L_CONF_TN_PREFIX}&nbsp;:</strong><br /><span class="small">{general.L_CONF_TN_PREFIX_INFO}</span></td>
	<td class="row1"><input type="text" size="10" maxlength="10" name="prefix_thumbnail" value="{general.THUMBNAIL_PREFIX}" /></td>
  </tr>
  <tr>
    <td><strong>{general.L_CONF_HISTORY}&nbsp;:</strong><br /><span class="small">{general.L_CONF_HISTORY_INFO}</span></td>
	<td class="row1"><input type="radio" class="radio" name="log" value="true" {general.HISTORY_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="log" value="false" {general.HISTORY_NO} />{L_NO}</td>
  </tr>
  <tr>
    <td><strong>{general.L_CONF_NOTIFICATION}&nbsp;:</strong><br /><span class="small">{general.L_CONF_NOTIFICATION_INFO}</span></td>
	<td class="row1"><input type="radio" class="radio" name="mail_notification" value="true" {general.NOTIFICATION_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="mail_notification" value="false" {general.NOTIFICATION_NO} />{L_NO}</td>
  </tr>
  <tr>
    <td><strong>{general.L_CONF_GALLERY_LOCKED}&nbsp;:</strong><br /><span class="small">{general.L_CONF_GALLERY_LOCKED_INFO}</span></td>
        <td class="row1"><input type="radio" class="radio" name="gallery_locked" value="true" {general.GALLERY_LOCKED_YES} />{L_YES}&nbsp;&nbsp;
        <input type="radio" class="radio" name="gallery_locked" value="false" {general.GALLERY_LOCKED_NO} />{L_NO}</td>
  </tr>
<!-- END general -->
<!-- BEGIN comments -->
  <tr class="admin">
    <th colspan="2">{comments.L_CONF_TITLE}</th>
  </tr>
    <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td width="70%"><strong>{comments.L_CONF_COMMENTS_ALL}&nbsp;:</strong><br /><span class="small">{comments.L_CONF_COMMENTS_ALL_INFO}</span></td>
	<td class="row1"><input type="radio" class="radio" name="comments_forall" value="true" {comments.COMMENTS_ALL_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="comments_forall" value="false" {comments.COMMENTS_ALL_NO} />{L_NO}</td>
  </tr>
  <tr>
    <td><strong>{comments.L_CONF_NB_COMMENTS_PAGE}&nbsp;:</strong><br /><span class="small">{comments.L_CONF_NB_COMMENTS_PAGE_INFO}</span></td>
	<td class="row1"><input type="text" size="3" maxlength="4" name="nb_comment_page" value="{comments.NB_COMMENTS_PAGE}" /></td>
  </tr>
  <tr>
    <td><strong>{comments.L_CONF_VALIDATE}&nbsp;:</strong><br /><span class="small">{comments.L_CONF_VALIDATE_INFO}</span></td>
	<td class="row1"><input type="radio" class="radio" name="comments_validation" value="true" {comments.VALIDATE_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="comments_validation" value="false" {comments.VALIDATE_NO} />{L_NO}</td>
  </tr>
<!-- END comments -->
<!-- BEGIN default -->
  <tr class="admin">
    <th colspan="2">{default.L_CONF_TITLE}</th>
  </tr>
    <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td width="70%"><strong>{default.L_CONF_LANG}&nbsp;:</strong><br /><span class="small">{default.L_CONF_LANG_INFO}</span></td>
	<td class="row1">{default.CONF_LANG_SELECT}</td>
  </tr>
  <tr>
    <td><strong>{default.L_NB_IMAGE_LINE}&nbsp;:</strong><br /><span class="small">{default.L_NB_IMAGE_LINE_INFO}</span></td>
	<td class="row1"><input type="text" size="3" maxlength="2" name="nb_image_line" value="{default.NB_IMAGE_LINE}" /></td>
  </tr>
  <tr>
    <td><strong>{default.L_NB_ROW_PAGE}&nbsp;:</strong><br /><span class="small">{default.L_NB_ROW_PAGE_INFO}</span></td>
	<td class="row1"><input type="text" size="3" maxlength="2" name="nb_line_page" value="{default.NB_ROW_PAGE}" /></td>
  </tr>
  <tr>
    <td><strong>{default.L_CONF_STYLE}&nbsp;:</strong><br /><span class="small">{default.L_CONF_STYLE_INFO}</span></td>
	<td class="row1">{default.CONF_STYLE_SELECT}</td>
  </tr>
  <tr>
    <td><strong>{default.L_CONF_RECENT}&nbsp;:</strong><br /><span class="small">{default.L_CONF_RECENT_INFO}</span></td>
	<td class="row1"><input type="text" size="3" maxlength="2" name="recent_period" value="{default.CONF_RECENT}" /></td>
  </tr>
  <tr>
    <td><strong>{default.L_CONF_EXPAND}&nbsp;:</strong><br /><span class="small">{default.L_CONF_EXPAND_INFO}</span></td>
	<td class="row1"><input type="radio" class="radio" name="auto_expand" value="true" {default.EXPAND_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="auto_expand" value="false" {default.EXPAND_NO} />{L_NO}</td>
  </tr>
  <tr>
    <td><strong>{default.L_NB_COMMENTS}&nbsp;:</strong><br /><span class="small">{default.L_NB_COMMENTS_INFO}</span></td>
	<td class="row1"><input type="radio" class="radio" name="show_nb_comments" value="true" {default.SHOW_COMMENTS_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="show_nb_comments" value="false" {default.SHOW_COMMENTS_NO} />{L_NO}</td>
  </tr>
  <tr>
    <td><strong>{default.L_MAXWIDTH}&nbsp;:</strong></td>
	<td><input type="text" size="4" maxlength="4" name="default_maxwidth" value="{default.MAXWIDTH}" />
	</td>
  </tr>
	<tr>
    <td><strong>{default.L_MAXHEIGHT}&nbsp;:</strong></td>
	<td><input type="text" size="4" maxlength="4" name="default_maxheight" value="{default.MAXHEIGHT}" />
	</td>
  </tr>
<!-- END default -->
<!-- BEGIN upload -->
  <tr class="admin">
    <th colspan="2">{upload.L_CONF_TITLE}</th>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td width="70%"><strong>{upload.L_CONF_MAXSIZE}&nbsp;:</strong><br /><span class="small">{upload.L_CONF_MAXSIZE_INFO}</span></td>
	<td class="row1"><input type="text" size="4" maxlength="4" name="upload_maxfilesize" value="{upload.UPLOAD_MAXSIZE}" /></td>
  </tr>
  <tr>
    <td><strong>{upload.L_CONF_MAXWIDTH}&nbsp;:</strong><br /><span class="small">{upload.L_CONF_MAXWIDTH_INFO}</span></td>
	<td class="row1"><input type="text" size="4" maxlength="4" name="upload_maxwidth" value="{upload.UPLOAD_MAXWIDTH}" /></td>
  </tr>
  <tr>
    <td><strong>{upload.L_CONF_MAXHEIGHT}&nbsp;:</strong><br /><span class="small">{upload.L_CONF_MAXHEIGHT_INFO}</span></td>
	<td class="row1"><input type="text" size="4" maxlength="4" name="upload_maxheight" value="{upload.UPLOAD_MAXHEIGHT}" /></td>
  </tr>
    <tr>
    <td><strong>{upload.L_CONF_TN_MAXWIDTH}&nbsp;:</strong><br /><span class="small">{upload.L_CONF_TN_MAXWIDTH_INFO}</span></td>
	<td class="row1"><input type="text" size="4" maxlength="4" name="upload_maxwidth_thumbnail" value="{upload.TN_UPLOAD_MAXWIDTH}" /></td>
  </tr>
  <tr>
    <td><strong>{upload.L_CONF_TN_MAXHEIGHT}&nbsp;:</strong><br /><span class="small">{upload.L_CONF_TN_MAXHEIGHT_INFO}</span></td>
	<td class="row1"><input type="text" size="4" maxlength="4" name="upload_maxheight_thumbnail" value="{upload.TN_UPLOAD_MAXHEIGHT}" /></td>
  </tr>
<!-- END upload -->
<!-- BEGIN session -->
  <tr class="admin">
    <th colspan="2">{session.L_CONF_TITLE}</th>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
    <tr>
    <td width="70%"><strong>{session.L_CONF_AUTHORIZE_REMEMBERING}&nbsp;:</strong><br /><span class="small">{session.L_CONF_AUTHORIZE_REMEMBERING_INFO}</span></td>
        <td class="row1"><input type="radio" class="radio" name="authorize_remembering" value="true" {session.AUTHORIZE_REMEMBERING_YES} />{L_YES}&nbsp;&nbsp;
        <input type="radio" class="radio" name="authorize_remembering" value="false" {session.AUTHORIZE_REMEMBERING_NO} />{L_NO}</td>
  </tr>
<!-- END session -->
<!-- BEGIN metadata -->
  <tr class="admin">
    <th colspan="2">{metadata.L_CONF_TITLE}</th>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td width="70%">
      <strong>{metadata.L_CONF_EXIF}&nbsp;:</strong>
      <br /><span class="small">{metadata.L_CONF_EXIF_INFO}</span>
    </td>
    <td class="row1">
      <input type="radio" class="radio" name="use_exif" value="true" {metadata.USE_EXIF_YES} />{L_YES}&nbsp;&nbsp;
      <input type="radio" class="radio" name="use_exif" value="false" {metadata.USE_EXIF_NO} />{L_NO}
    </td>
  </tr>
  <tr>
    <td>
      <strong>{metadata.L_CONF_IPTC}&nbsp;:</strong>
      <br /><span class="small">{metadata.L_CONF_IPTC_INFO}</span>
    </td>
    <td class="row1">
      <input type="radio" class="radio" name="use_iptc" value="true" {metadata.USE_IPTC_YES} />{L_YES}&nbsp;&nbsp;
      <input type="radio" class="radio" name="use_iptc" value="false" {metadata.USE_IPTC_NO} />{L_NO}
    </td>
  </tr>
  <tr>
    <td>
      <strong>{metadata.L_CONF_SHOW_EXIF}&nbsp;:</strong>
      <br /><span class="small">{metadata.L_CONF_SHOW_EXIF_INFO}</span>
    </td>
    <td class="row1">
      <input type="radio" class="radio" name="show_exif" value="true" {metadata.SHOW_EXIF_YES} />{L_YES}&nbsp;&nbsp;
      <input type="radio" class="radio" name="show_exif" value="false" {metadata.SHOW_EXIF_NO} />{L_NO}
    </td>
  </tr>
  <tr>
    <td>
      <strong>{metadata.L_CONF_SHOW_IPTC}&nbsp;:</strong>
      <br /><span class="small">{metadata.L_CONF_SHOW_IPTC_INFO}</span>
    </td>
    <td class="row1">
      <input type="radio" class="radio" name="show_iptc" value="true" {metadata.SHOW_IPTC_YES} />{L_YES}&nbsp;&nbsp;
      <input type="radio" class="radio" name="show_iptc" value="false" {metadata.SHOW_IPTC_NO} />{L_NO}
    </td>
  </tr>
<!-- END metadata -->
    <tr>
      <td colspan="2" align="center">
        <input type="submit" name="submit" class="bouton" value="{L_SUBMIT}">
	<input type="reset" name="reset" class="bouton" value="{L_RESET}">
      </td>
    </tr>
  </table>
</form>
