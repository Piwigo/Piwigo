<form action="{F_ACTION}" method="post">
  <!-- BEGIN confirmation -->
  <div class="info">{L_WAITING_CONFIRMATION}</div>
  <!-- END confirmation -->
  <table style="width:100%;" >
    <tr class="throw">
      <th style="width:20%;">{L_CATEGORY}</th>
      <th style="width:20%;">{L_DATE}</th>
      <th style="width:20%;">{L_FILE}</th>
      <th style="width:20%;">{L_THUMBNAIL}</th>
      <th style="width:20%;">{L_AUTHOR}</th>
      <th style="width:1px;">&nbsp;</th>
    </tr>
    <!-- BEGIN picture -->
    <tr>
      <td class="{picture.WAITING_CLASS}" style="white-space:nowrap;">{picture.CATEGORY_IMG}</td>
      <td class="{picture.WAITING_CLASS}" style="white-space:nowrap;">{picture.DATE_IMG}</td>
      <td class="{picture.WAITING_CLASS}" style="white-space:nowrap;">
        <a target="_blank" href="{picture.PREVIEW_URL_IMG}">{picture.FILE_IMG}</a>
      </td>
      <td class="{picture.WAITING_CLASS}" style="white-space:nowrap;">
        <!-- BEGIN thumbnail -->
        <a target="_blank" href="{picture.thumbnail.PREVIEW_URL_TN_IMG}">{picture.thumbnail.FILE_TN_IMG}</a>
        <!-- END thumbnail -->
      </td>
      <td class="{picture.WAITING_CLASS}" style="white-space:nowrap;">
        <a href="mailto:{picture.UPLOAD_EMAIL}">{picture.UPLOAD_USERNAME}</a>
      </td>
      <td class="{picture.WAITING_CLASS}" style="white-space:nowrap;">
        <input type="radio" name="validate-{picture.ID_IMG}" value="true" />{L_SUBMIT}
        <input type="radio" name="validate-{picture.ID_IMG}" value="false" />{L_DELETE}
      </td>
    </tr>
    <!-- END picture -->
    <tr>
      <td colspan="6" align="center">
	  <input type="submit" name="submit" value="{L_SUBMIT}" class="bouton" />
	  </td>
    </tr>
  </table>
</form>
