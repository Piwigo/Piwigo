<!-- BEGIN confirmation -->
<div style="color:red;text-align:center;">
  {L_EDIT_CONFIRM} <a href="{U_PERMISSIONS}">{L_HERE}</a></div>
<!-- END confirmation -->
<div class="admin">{CATEGORIES_NAV}</div>
<form action="{F_ACTION}" method="POST">
<table style="width:100%;">
    <!-- BEGIN server -->
    <tr>
      <td style="width:50%;">{L_REMOTE_SITE}</td>
      <td class="row1">{SITE_URL}</td>
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
      <td class="row1">{CATEGORY_DIR}</td>
    </tr>
	<!-- END storage -->
    <tr>
      <td><strong>{L_EDIT_COMMENT}</strong></td>
      <td class="row1">
        <textarea name="comment" rows="3" cols="50" >{CAT_COMMENT}</textarea>
      </td>
    </tr>
    <tr>
      <td><strong>{L_EDIT_STATUS}&nbsp;:</strong><br /><span class="small">{L_EDIT_STATUS_INFO}</span></td>
      <td class="row1">
	  <input type="radio" class="radio" name="status" value="public" {ACCESS_FREE} />{L_ACCESS_FREE}&nbsp;&nbsp;
	  <input type="radio" class="radio" name="status" value="private" {ACCESS_RESTRICTED} />{L_ACCESS_RESTRICTED}
      </td>
    </tr>
	<tr>
      <td><strong>{L_EDIT_LOCK}&nbsp;:</strong><br /><span class="small">{L_EDIT_LOCK_INFO}</span></td>
      <td class="row1">
	  <input type="radio" class="radio" name="visible" value="false" {LOCKED} />{L_YES}&nbsp;&nbsp;
	  <input type="radio" class="radio" name="visible" value="true" {UNLOCKED} />{L_NO}
      </td>
    </tr>
    <!-- BEGIN parent -->
    <tr>
      <td>{#cat_parent}</td>
      <td class="row1">
        <!-- BEGIN associate_LOV -->
        <select name="associate">
          <!-- BEGIN associate_cat -->
          <option value="{#value}">{#content}</option>
          <!-- END associate_cat -->
        </select>
        <!-- END associate_LOV -->
        <!-- BEGIN associate_text -->
        <input type="text" name="associate" value="{#value}" />
        <!-- END associate_text -->
      </td>
    </tr>
    <!-- END parent -->
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