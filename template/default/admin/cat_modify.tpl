<!-- BEGIN confirmation -->
<div style="color:red;text-align:center;">
  {L_EDIT_CONFIRM} <a href="{U_PERMISSIONS}">{L_HERE}</a></div>
<!-- END confirmation -->
<div class="admin">{CATEGORIES_NAV}</div>
<form action="{F_ACTION}" method="POST">
<table style="width:100%;">
    <!-- BEGIN representant -->
    <tr>
      <td style="width:50%;" align="center">
        <a href="{representant.URL}"><img src="{representant.SRC}" alt="" class="miniature" /></a>
      </td>
      <td class="row1"><input type="submit" name="set_random_representant" value="{L_SET_RANDOM_REPRESENTANT}" class="bouton" /></td>
    </tr>
    <!-- END representant -->
  </table>
</form>
<form action="{F_ACTION}" method="POST">
<table style="width:100%;">
    <!-- BEGIN server -->
    <tr>
      <td style="width:50%;"><strong>{L_REMOTE_SITE}</strong></td>
      <td class="row1">{server.SITE_URL}</td>
    </tr>
    <!-- END server -->
    <tr>
      <td style="width:50%;"><strong>{L_EDIT_NAME}</strong></td>
      <td class="row1">
        <input type="text" name="name" value="{CAT_NAME}" maxlength="60"/>
      </td>
    </tr>
	<!-- BEGIN storage -->
	<tr>
      <td><strong>{L_STORAGE}</strong></td>
      <td class="row1">{storage.CATEGORY_DIR}</td>
    </tr>
	<!-- END storage -->
    <tr>
      <td><strong>{L_EDIT_COMMENT}</strong></td>
      <td class="row1">
        <textarea name="comment" rows="3" cols="50" >{CAT_COMMENT}</textarea>
      </td>
    </tr>
	<tr class="admin">
	  <th colspan="2">{L_EDIT_CAT_OPTIONS}</th>
	</tr>
    <tr>
      <td><strong>{L_EDIT_STATUS}&nbsp;:</strong><br /><span class="small">{L_EDIT_STATUS_INFO}</span></td>
      <td class="row1">
	  <input type="radio" class="radio" name="status" value="public" {STATUS_PUBLIC} />{L_STATUS_PUBLIC}&nbsp;&nbsp;
	  <input type="radio" class="radio" name="status" value="private" {STATUS_PRIVATE} />{L_STATUS_PRIVATE}
      </td>
    </tr>
    <tr>
      <td><strong>{L_EDIT_LOCK}&nbsp;:</strong><br /><span class="small">{L_EDIT_LOCK_INFO}</span></td>
      <td class="row1">
	  <input type="radio" class="radio" name="visible" value="false" {LOCKED} />{L_YES}&nbsp;&nbsp;
	  <input type="radio" class="radio" name="visible" value="true" {UNLOCKED} />{L_NO}
      </td>
    </tr>
    <tr>
      <td><strong>{L_EDIT_COMMENTABLE}&nbsp;:</strong><br /><span class="small">{L_EDIT_COMMENTABLE_INFO}</span></td>
      <td class="row1">
          <input type="radio" class="radio" name="commentable" value="true" {COMMENTABLE_TRUE} />{L_YES}&nbsp;&nbsp;
          <input type="radio" class="radio" name="commentable" value="false" {COMMENTABLE_FALSE} />{L_NO}
      </td>
    </tr>
    <!-- BEGIN upload -->
    <tr>
      <td><strong>{L_EDIT_UPLOADABLE}&nbsp;:</strong><br /><span class="small">{L_EDIT_UPLOADABLE_INFO}</span></td>
      <td class="row1">
          <input type="radio" class="radio" name="uploadable" value="true" {UPLOADABLE_TRUE} />{L_YES}&nbsp;&nbsp;
          <input type="radio" class="radio" name="uploadable" value="false" {UPLOADABLE_FALSE} />{L_NO}
      </td>
    </tr>
    <!-- END upload -->
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" align="center">
        <input type="submit" name="submit" value="{L_SUBMIT}" class="bouton" />
      </td>
    </tr>
  </table>
</form>
