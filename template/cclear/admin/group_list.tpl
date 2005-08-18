<!-- BEGIN errors -->
<div class="errors">
<ul>
  <!-- BEGIN error -->
  <li>{errors.error.ERROR}</li>
  <!-- END error -->
</ul>
</div>
<!-- END errors -->
<form method="post" name="post" action="{S_GROUP_ACTION}">
<div class="admin">{L_GROUP_SELECT}</div>
<!-- BEGIN select_box -->
{S_GROUP_SELECT}&nbsp;&nbsp;<input type="submit" name="edit" value="{L_LOOK_UP}" class="bouton" />
<input type="submit" name="delete" value="{L_GROUP_DELETE}" class="bouton" />
<div style="vertical-align:middle;">
<input type="checkbox" name="confirm_delete" value="1">{L_GROUP_CONFIRM}
</div>
<!-- END select_box -->
<input type="text" name="newgroup">
<input type="submit" class="bouton" name="new" value="{L_CREATE_NEW_GROUP}" />
<br />
<br />
<!-- BEGIN edit_group -->
<div class="admin">{L_GROUP_EDIT} [{edit_group.GROUP_NAME}]</div>
<table class="table2" width="60%" style="margin-left:20%;">
<tr class="throw">
    <th width="25%">{L_USER_NAME}</th>
	  <th width="25%">{L_USER_EMAIL}</th>
    <th width="10%">{L_USER_SELECT}</th>
</tr>
<!-- BEGIN user -->
  <tr class="{edit_group.user.T_CLASS}">
    <td>{edit_group.user.NAME}</td>
    <td>{edit_group.user.EMAIL}</td>
	  <td align="center"><input name="members[]" type="checkbox" value="{edit_group.user.ID}"></td>
  </tr>
<!-- END user -->
<tr>
  <td colspan="3" align="right" valign="middle">
    <input type="submit" name="deny_user" value="{L_DENY_SELECTED}" class="bouton" />
  </td>
</tr>
</table>
<div align="left" style="margin-left:20%;">
<input type="text" name="username" maxlength="50" size="20" />
<input type="submit" name="add" value="{L_ADD_MEMBER}" class="bouton" />
<input name="edit_group_id" type="hidden" value="{edit_group.GROUP_ID}">
<input type="submit" name="usersubmit" value="{L_FIND_USERNAME}" class="bouton" onClick="window.open('{U_SEARCH_USER}', '_phpbbsearch', 'HEIGHT=250,resizable=yes,WIDTH=400');return false;" />
</div>
<br />
<!-- END edit_group -->
</form>