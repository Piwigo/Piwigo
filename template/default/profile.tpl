<!-- BEGIN select_user -->
<div class="admin">{L_SELECT_USERNAME}</div>
<form method="post" name="post" action="{F_SEARCH_USER_ACTION}">
  <input type="text" name="username" maxlength="50" size="20" />
  <input type="submit" name="submituser" value="{L_LOOKUP_USER}" class="bouton" /> 
  <input type="submit" name="usersubmit" value="{L_FIND_USERNAME}" class="bouton" onClick="window.open('{U_SEARCH_USER}', '_phpwgsearch', 'HEIGHT=250,resizable=yes,WIDTH=400');return false;" />
</form>
<!-- END select_user -->
<!-- BEGIN modify -->
<!-- BEGIN profile -->
<div class="titrePage">{L_TITLE}</div>
<!-- END profile -->
<!-- BEGIN errors -->
<div class="errors">
	<ul>
	  <!-- BEGIN error -->
	  <li>{modify.errors.error.ERROR}</li>
	  <!-- END error -->
	</ul>
</div>
<!-- END errors -->
<form method="post" action="{F_ACTION}">
<table width="70%" align="center">
  <tr class="admin">
    <th colspan="2">{L_REGISTRATION_INFO}</th>
  </tr>
	<tr>
	  <td width="50%">{L_USERNAME}</td>
	  <td><input type="text" name="username" value="{USERNAME}" />
		<input type="hidden" name="userid" value="{USERID}" /></td>
  </tr>
	<tr>
	  <td>{L_EMAIL}</td>
	  <td><input type="text" name="mail_address" value="{EMAIL}" /></td>
  </tr>
	<!-- BEGIN profile -->
  <tr>
	<td>{L_CURRENT_PASSWORD} : <br /><span class="small">{L_CURRENT_PASSWORD_HINT}</span></td>
	<td><input type="password" name="password" value="" /></td>
  </tr>
	<!-- END profile -->
	<tr>
	<td>{L_NEW_PASSWORD} : <br /><span class="small">{L_NEW_PASSWORD_HINT}</span></td>
	<td><input type="password" name="use_new_pwd" value="" /></td>
  </tr>
  <tr>
	<td>{L_CONFIRM_PASSWORD} : <br /><span class="small">{L_CONFIRM_PASSWORD_HINT}</span></td>
	<td><input type="password" name="passwordConf" value="" /></td>
  </tr>
  <tr class="admin">
    <th colspan="2">{L_PREFERENCES}</th>
  </tr>
  <tr>
    <td width="60%">{L_NB_IMAGE_LINE}</td>
	<td width="40%"><input type="text" size="3" maxlength="2" name="nb_image_line" value="{NB_IMAGE_LINE}" /></td>
  </tr>
  <tr>
    <td>{L_NB_ROW_PAGE}</td>
	<td><input type="text" size="3" maxlength="2" name="nb_line_page" value="{NB_ROW_PAGE}" /></td>
  </tr>
  <tr>
    <td>{L_STYLE_SELECT}</td>
	<td>{STYLE_SELECT}</td>
  </tr>
  <tr>
    <td>{L_LANG_SELECT}</td>
	<td>{LANG_SELECT}</td>
  </tr>
   <tr>
    <td>{L_RECENT_PERIOD}</td>
	<td><input type="text" size="3" maxlength="2" name="recent_period" value="{RECENT_PERIOD}" /></td>
  </tr>
  <tr>
    <td>{L_EXPAND_TREE}</td>
	<td><input type="radio" class="radio" name="expand" value="true" {EXPAND_TREE_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="expand" value="false" {EXPAND_TREE_NO} />{L_NO}</td>
  </tr>
  <tr>
    <td>{L_NB_COMMENTS}</td>
	<td><input type="radio" class="radio" name="show_nb_comments" value="true" {NB_COMMENTS_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="show_nb_comments" value="false" {NB_COMMENTS_NO} />{L_NO}</td>
  </tr>
	<tr>
    <td>{L_MAXWIDTH}</td>
	<td><input type="text" size="4" maxlength="4" name="maxwidth" value="{MAXWIDTH}" />
	</td>
  </tr>
	<tr>
    <td>{L_MAXHEIGHT}</td>
	<td><input type="text" size="4" maxlength="4" name="maxheight" value="{MAXHEIGHT}" />
	</td>
  </tr>
<!-- BEGIN admin -->
	<tr class="admin">
    <th colspan="2">{modify.admin.L_ADMIN_USER}</th>
  </tr>
	<tr>
    <td>{modify.admin.L_STATUS}</td>
	<td>{modify.admin.STATUS}
	</td>
  </tr>
	<tr>
    <td>{modify.admin.L_DELETE}<br />
		<span class="small">{modify.admin.L_DELETE_HINT}</span></td>
	<td><input name="user_delete" type="checkbox" value="1">
	</td>
  </tr>
<!-- END admin -->
<tr>
	<td colspan="2" align="center">
	  <input type="submit" name="submit" value="{L_SUBMIT}" class="bouton" />
	</td>
  </tr>
</table>
</form>
<!-- BEGIN profile -->
<div style="text-align:center;margin:5px;">
<a href="{U_RETURN}" title="{L_RETURN_HINT}">[{L_RETURN}]</a>
</div>
<!-- END profile -->
<!-- END modify -->