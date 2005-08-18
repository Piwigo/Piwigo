<!-- BEGIN errors -->
<div class="errors">
  <ul>
    <!-- BEGIN error -->
    <li>{errors.error.ERROR}</li>
    <!-- END error -->
  </ul>
</div>
<!-- END errors -->

<!-- BEGIN add_user -->
<h3>{L_GROUP_ADD_USER}</h3>
<form method="post" name="post" action="{add_user.F_ACTION}">
  <p>{L_USERNAME} <input type="text" name="login" maxlength="50" size="20" />
  {L_PASSWORD} <input type="text" name="password" />
  <input type="submit" name="submit_add" value="{L_SUBMIT}" class="bouton" /></p>
</form>
<!-- END add_user -->

<!-- BEGIN select_user -->
<h3>{L_SELECT_USERNAME}</h3>
<form method="post" name="post" action="{F_SEARCH_USER_ACTION}">
  <p>
  <input type="text" name="username" maxlength="50" size="20" />
  <input type="submit" name="submituser" value="{L_LOOKUP_USER}" class="bouton" /> 
  <input type="submit" name="usersubmit" value="{L_FIND_USERNAME}" class="bouton" onClick="window.open('{U_SEARCH_USER}', '_phpwgsearch', 'HEIGHT=250,resizable=yes,WIDTH=400');return false;" />
  </p>
</form>

<h3>{L_USERS_LIST}</h3>
<form method="get" action="{F_ORDER_ACTION}">
<p><input type="hidden" name="page" value="profile" /></p>
<div style="text-align:center">
  {L_ORDER_BY}
  <select name="order_by">
    <!-- BEGIN order_by -->
    <option value="{select_user.order_by.VALUE}" {select_user.order_by.SELECTED} >{select_user.order_by.CONTENT}</option>
    <!-- END order_by -->
  </select>
  <select name="direction">
    <!-- BEGIN direction -->
    <option value="{select_user.direction.VALUE}" {select_user.direction.SELECTED} >{select_user.direction.CONTENT}</option>
    <!-- END direction -->
  </select>
  <input type="submit" value="{L_SUBMIT}" class="bouton" />
</div>
</form>

<table>
  <tr class="throw">
    <th style="width:20%;">{L_USERNAME}</th>
    <th style="width:20%;">{L_STATUS}</th>
    <th style="width:30%;">{L_EMAIL}</th>
    <th style="width:30%;">{L_GROUPS}</th>
    <th style="width:1%;">{L_ACTIONS}</th>
  </tr>
  <!-- BEGIN user -->
  <tr>
    <td><a href="{select_user.user.U_MOD}">{select_user.user.USERNAME}</a></td>
    <td>{select_user.user.STATUS}</td>
    <td>{select_user.user.EMAIL}</td>
    <td>{select_user.user.GROUPS}</td>
<!-- [<a href="{select_user.user.U_MOD}">{L_MODIFY}</a>] -->
    <td>[<a href="{select_user.user.U_PERM}">{L_PERMISSIONS}</a>]</td>
  </tr>
  <!-- END user -->
</table>
<div class="navigationBar">{NAVBAR}</div>
<!-- END select_user -->
<!-- BEGIN modify -->
<!-- BEGIN profile -->
<h2>{L_TITLE}</h2>
<!-- END profile -->
<div class="formbox">
  <form method="post" action="{F_ACTION}">
    <h3>{L_REGISTRATION_INFO}</h3>
    <dl>
      <dt>{L_USERNAME}</dt>
      <dd><input type="text" name="username" value="{USERNAME}" />
		<input type="hidden" name="userid" value="{USERID}" /></dd>
      <dt>{L_EMAIL}</dt>
      <dd><input type="text" name="mail_address" value="{EMAIL}" /></dd>
    </dl>
    <!-- BEGIN profile -->
    <dl class="biglabel">
      <dt>{L_CURRENT_PASSWORD} : <br /><span class="small">{L_CURRENT_PASSWORD_HINT}</span></dt>
      <dd><input type="password" name="password" value="" /></dd>
    </dl>
    <!-- END profile -->
    <dl class="biglabel">
      <dt>{L_NEW_PASSWORD} : <br /><span class="small">{L_NEW_PASSWORD_HINT}</span></dt>
      <dd><input type="password" name="use_new_pwd" value="" /></dd>
    </dl>
    <dl class="biglabel">
      <dt>{L_CONFIRM_PASSWORD} : <br /><span class="small">{L_CONFIRM_PASSWORD_HINT}</span></dt>
      <dd><input type="password" name="passwordConf" value="" /></dd>
    </dl>
    <h3>{L_PREFERENCES}</h3>
    <dl>
      <dt>{L_NB_IMAGE_LINE}</dt>
      <dd><input type="text" size="3" maxlength="2" name="nb_image_line" value="{NB_IMAGE_LINE}" /></dd>
      <dt>{L_NB_ROW_PAGE}</dt>
      <dd><input type="text" size="3" maxlength="2" name="nb_line_page" value="{NB_ROW_PAGE}" /></dd>
      <dt>{L_STYLE_SELECT}</dt>
      <dd>{STYLE_SELECT}</dd>
      <dt>{L_LANG_SELECT}</dt>
      <dd>{LANG_SELECT}</dd>
      <dt>{L_RECENT_PERIOD}</dt>
      <dd><input type="text" size="3" maxlength="2" name="recent_period" value="{RECENT_PERIOD}" /></dd>
      <dt>{L_EXPAND_TREE}</dt>
      <dd><input type="radio" class="radio" name="expand" value="true" {EXPAND_TREE_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="expand" value="false" {EXPAND_TREE_NO} />{L_NO}</dd>
      <dt>{L_NB_COMMENTS}</dt>
      <dd><input type="radio" class="radio" name="show_nb_comments" value="true" {NB_COMMENTS_YES} />{L_YES}&nbsp;&nbsp;
	<input type="radio" class="radio" name="show_nb_comments" value="false" {NB_COMMENTS_NO} />{L_NO}</dd>
      <dt>{L_MAXWIDTH}</dt>
      <dd><input type="text" size="4" maxlength="4" name="maxwidth" value="{MAXWIDTH}" /></dd>
      <dt>{L_MAXHEIGHT}</dt>
      <dd><input type="text" size="4" maxlength="4" name="maxheight" value="{MAXHEIGHT}" /></dd>
    </dl>
    <!-- BEGIN admin -->
    <h3>{modify.admin.L_ADMIN_USER}</h3>
    <dl>
      <dt>{modify.admin.L_STATUS}</dt>
      <dd>{modify.admin.STATUS}</dd>
    </dl>
    <dl class="biglabel">
      <dt>{modify.admin.L_DELETE}<br />
		<span class="small">{modify.admin.L_DELETE_HINT}</span></dt>
      <dd><input name="user_delete" type="checkbox" value="1"></dd>
    </dl>
    <!-- END admin -->
    <p>
      <input type="submit" name="submit" value="{L_SUBMIT}" class="bouton" />
      <input type="reset" name="reset" value="{L_RESET}" class="bouton" />
    </p>
  </form>
  <!-- BEGIN profile -->
  <div style="text-align:center;margin:5px;">
    <a href="{U_RETURN}" title="{L_RETURN_HINT}">[{L_RETURN}]</a>
  </div>
  <!-- END profile -->
</div><!--formbox-->
<!-- END modify -->
