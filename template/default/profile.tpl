<div class="titrePage">{L_TITLE}</div>
<form method="post" action="{F_ACTION}">
<!-- BEGIN errors -->
  <div class="errors">
	<ul>
	  <!-- BEGIN error -->
	  <li>{errors.error.ERROR}</li>
	  <!-- END error -->
	</ul>
  </div>
<!-- END errors -->
<table width="70%" align="center">
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
  <!-- BEGIN text -->
  <tr>
	<td>{text.F_LABEL}</td>
	<td>
	  <input type="text" name="{text.F_NAME}" value="{text.F_VALUE}" />
	</td>
  </tr>
  <!-- END text -->
  <tr>
	<td colspan="2">&nbsp;</td>
  </tr>
  <tr>
	<td>{L_NEW} {L_PASSWORD} <input type="checkbox" name="use_new_pwd" value="1" /></td>
	<td><input type="password" name="password" value="" /></td>
  </tr>
  <tr>
	<td>{L_CONFIRM}</td>
	<td><input type="password" name="passwordConf" value="" /></td>
  </tr>
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
