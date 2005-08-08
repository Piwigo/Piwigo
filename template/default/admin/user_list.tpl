<div class="admin">{L_GROUP_ADD_USER}</div>

<form method="post" name="add_user" action="{F_ADD_ACTION}">
<div style="text-align:center">
  {L_USERNAME} <input type="text" name="login" maxlength="50" size="20" />
  {L_PASSWORD} <input type="text" name="password" />
  <input type="submit" name="submit_add" value="{L_SUBMIT}" class="bouton" />
</div>
</form>

<div class="admin">{L_USERS_LIST}</div>

<form class="filter" method="get" name="filter" action="{F_FILTER_ACTION}">

  <input type="hidden" name="page" value="user_list" />
  
  <!-- BEGIN session -->
  <input type="hidden" name="id" value="{session.ID}" />
  <!-- END session -->

<fieldset>

  <legend>{lang:Filter}</legend>


  <label>{lang:username} <input type="text" name="username" value="{F_USERNAME}" /></label>

  <label>
  {lang:status}
  <select name="status">
    <!-- BEGIN status_option -->
    <option value="{status_option.VALUE}" {status_option.SELECTED} > {status_option.CONTENT}</option>
    <!-- END status_option -->
  </select>
  </label>

  <label>
  {lang:group}
  <select name="group">
    <!-- BEGIN group_option -->
    <option value="{group_option.VALUE}" {group_option.SELECTED} > {group_option.CONTENT}</option>
    <!-- END group_option -->
  </select>
  </label>

  <label>
  {lang:Order by}
  <select name="order_by">
    <!-- BEGIN order_by -->
    <option value="{order_by.VALUE}" {order_by.SELECTED} >{order_by.CONTENT}</option>
    <!-- END order_by -->
  </select>
  </label>

  <label>
  {lang:Sort order}
  <select name="direction">
    <!-- BEGIN direction -->
    <option value="{direction.VALUE}" {direction.SELECTED} >{direction.CONTENT}</option>
    <!-- END direction -->
  </select>
  </label>

  <input type="submit" name="submit_filter" value="{L_SUBMIT}" class="bouton" />

</fieldset>

</form>

<form method="post" name="preferences" action="{F_PREF_ACTION}">

<table class="table2" style="width:100%;" >
  <tr class="throw">
    <th style="width:1%;"></th>
    <th style="width:20%;">{L_USERNAME}</th>
    <th style="width:20%;">{L_STATUS}</th>
    <th style="width:30%;">{L_EMAIL}</th>
    <th style="width:30%;">{L_GROUPS}</th>
    <th style="width:1%;">{L_ACTIONS}</th>
  </tr>
  <!-- BEGIN user -->
  <tr class="{user.CLASS}">
    <td><input type="checkbox" name="selection[]" value="{user.ID}" {user.CHECKED} id="selection-{user.ID}" /></td>
    <td><label for="selection-{user.ID}">{user.USERNAME}</label></td>
    <td>{user.STATUS}</td>
    <td>{user.EMAIL}</td>
    <td>{user.GROUPS}</td>
    <td style="text-align:center;">
      <a href="{user.U_MOD}"><img src="./template/default/theme/profile.png" style="border:none" alt="profile" title="profile" /></a>
      <a href="{user.U_PERM}"><img src="./template/default/theme/permissions.png" style="border:none" alt="{L_PERMISSIONS}" title="{L_PERMISSIONS}" /></a>
    </td>
  </tr>
  <!-- END user -->
</table>
<div class="navigationBar">{NAVBAR}</div>

<!-- delete the selected users ? -->
<fieldset>
  <legend>{lang:Deletions}</legend>
  <input type="checkbox" name="confirm_deletion" value="1" /> {lang:confirm}
  <input type="submit" value="{lang:Delete selected users}" name="delete" class="bouton" />
</fieldset>

<!-- form to set properties for many users at once -->
<fieldset>
  <legend>{lang:Groups}</legend>

<table>

  <tr>
    <td>{lang:associate to group}</td>
    <td>
      <select name="associate" size="1">
        <!-- BEGIN associate_option -->
        <option {associate_option.SELECTED} value="{associate_option.VALUE}">{associate_option.CONTENT}</option>
        <!-- END associate_option -->
      </select>
    </td>
  </tr>

  <tr>
    <td>{lang:dissociate from group}</td>
    <td>
      <select name="dissociate" size="1">
        <!-- BEGIN dissociate_option -->
        <option {dissociate_option.SELECTED} value="{dissociate_option.VALUE}">{dissociate_option.CONTENT}</option>
        <!-- END dissociate_option -->
      </select>
    </td>
  </tr>

</table>

</fieldset>

<fieldset>
  <legend>{lang:Preferences}</legend>

<table>

  <tr>
    <td>{L_NB_IMAGE_LINE}</td>
    <td>
      <input type="radio" name="nb_image_line_action" value="leave" checked="checked" /> leave unchanged
      <input type="radio" name="nb_image_line_action" value="set" id="nb_image_line_action_set" /> set to
      <input onmousedown="document.getElementById('nb_image_line_action_set').checked = true;"
             size="3" maxlength="2" type="text" name="nb_image_line" value="{NB_IMAGE_LINE}" />
    </td>
  </tr>

  <tr>
    <td>{L_NB_LINE_PAGE}</td>
    <td>
      <input type="radio" name="nb_line_page_action" value="leave" checked="checked" /> leave unchanged
      <input type="radio" name="nb_line_page_action" value="set" id="nb_line_page_action_set" /> set to
      <input onmousedown="document.getElementById('nb_line_page_action_set').checked = true;"
             size="3" maxlength="2" type="text" name="nb_line_page" value="{NB_LINE_PAGE}" />
    <td>
  </tr>

  <tr>
    <td>{L_TEMPLATE}</td>
    <td>
      <input type="radio" name="template_action" value="leave" checked="checked" /> leave unchanged
      <input type="radio" name="template_action" value="set" id="template_action_set" /> set to
      <select onmousedown="document.getElementById('template_action_set').checked = true;" name="template" size="1">
        <!-- BEGIN template_option -->
        <option {template_option.SELECTED} value="{template_option.VALUE}">{template_option.CONTENT}</option>
        <!-- END template_option -->
      </select>
    </td>
  </tr>

  <tr>
    <td>{L_LANGUAGE}</td>
    <td>
      <input type="radio" name="language_action" value="leave" checked="checked" /> leave unchanged
      <input type="radio" name="language_action" value="set" id="language_action_set" /> set to
      <select onmousedown="document.getElementById('language_action_set').checked = true;" name="language" size="1">
        <!-- BEGIN language_option -->
        <option {language_option.SELECTED} value="{language_option.VALUE}">{language_option.CONTENT}</option>
        <!-- END language_option -->
      </select>
    </td>
  </tr>

  <tr>
    <td>{L_RECENT_PERIOD}</td>
    <td>
      <input type="radio" name="recent_period_action" value="leave" checked="checked" /> leave unchanged
      <input type="radio" name="recent_period_action" value="set" id="recent_period_action_set" /> set to
      <input onmousedown="document.getElementById('recent_period_action_set').checked = true;"
             type="text" size="3" maxlength="2" name="recent_period" value="{RECENT_PERIOD}" />
    </td>
  </tr>

  <tr>
    <td>{L_EXPAND}</td>
    <td>
      <input type="radio" name="expand_action" value="leave" checked="checked" /> leave unchanged
      <input type="radio" name="expand_action" value="set" id="expand_action_set" /> set to
      <input onmousedown="document.getElementById('expand_action_set').checked = true;" 
             type="radio" class="radio" name="expand" value="true"  {EXPAND_YES} />{L_YES}
      <input onmousedown="document.getElementById('expand_action_set').checked = true;"
             type="radio" class="radio" name="expand" value="false" {EXPAND_NO}  />{L_NO}
    </td>
  </tr>

  <tr>
    <td>{L_SHOW_NB_COMMENTS}</td>
    <td>
      <input type="radio" name="show_nb_comments_action" value="leave" checked="checked" /> leave unchanged
      <input type="radio" name="show_nb_comments_action" value="set" id="show_nb_comments_action_set" /> set to
      <input onmousedown="document.getElementById('show_nb_comments_action_set').checked = true;"
             type="radio" class="radio" name="show_nb_comments" value="true" {SHOW_NB_COMMENTS_YES} />{L_YES}
      <input onmousedown="document.getElementById('show_nb_comments_action_set').checked = true;"
             type="radio" class="radio" name="show_nb_comments" value="false" {SHOW_NB_COMMENTS_NO} />{L_NO}
    </td>
  </tr>

  <tr>
    <td>{L_MAXWIDTH}</td>
    <td>
      <input type="radio" name="maxwidth_action" value="leave" checked="checked" /> leave unchanged
      <input type="radio" name="maxwidth_action" value="unset" /> unset
      <input type="radio" name="maxwidth_action" value="set" id="maxwidth_action_set" /> set to 
      <input onmousedown="document.getElementById('maxwidth_action_set').checked = true;"
             type="text" size="4" maxlength="4" name="maxwidth" value="{MAXWIDTH}" />
    </td>
  </tr>


  <tr>
    <td>{L_MAXHEIGHT}</td>
    <td>
      <input type="radio" name="maxheight_action" value="leave" checked="checked" /> leave unchanged
      <input type="radio" name="maxheight_action" value="unset" /> unset
      <input type="radio" name="maxheight_action" value="set" id="maxheight_action_set" /> set to 
      <input onmousedown="document.getElementById('maxheight_action_set').checked = true;"
             type="text" size="4" maxlength="4" name="maxheight" value="{maxheight}" />
    </td>
  </tr>

  <tr>
    <td>{L_STATUS}</td>
    <td>
      <input type="radio" name="status_action" value="leave" checked="checked" /> leave unchanged
      <input type="radio" name="status_action" value="set" id="status_action_set" /> set to
      <select onmousedown="document.getElementById('status_action_set').checked = true;" name="status" size="1">
        <!-- BEGIN pref_status_option -->
        <option {pref_status_option.SELECTED} value="{pref_status_option.VALUE}">{pref_status_option.CONTENT}</option>
        <!-- END pref_status_option -->
      </select>
    </td>
  </tr>

</table>

</fieldset>

<p style="text-align:center;">
  target  
  <input type="radio" name="target" value="all" /> all
  <input type="radio" name="target" value="selection" checked="checked" /> selection
</p>

<p style="text-align:center;">
  <input type="submit" value="{L_SUBMIT}" name="pref_submit" class="bouton" />
  <input type="reset" value="{L_RESET}" name="pref_reset" class="bouton" />
</p>

</form>
