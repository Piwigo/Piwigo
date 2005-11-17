<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="template/yoga/theme/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{lang:title_liste_users}</h2>
</div>

<form class="filter" method="post" name="add_user" action="{F_ADD_ACTION}">
  <fieldset>
    <legend>{lang:Add a user}</legend>
    <label>{lang:Username} <input type="text" name="login" maxlength="50" size="20" /></label>
    <label>{L_PASSWORD} <input type="text" name="password" /></label>
    <input type="submit" name="submit_add" value="{L_SUBMIT}" />
  </fieldset>
</form>

<form class="filter" method="get" name="filter" action="{F_FILTER_ACTION}">

  <input type="hidden" name="page" value="user_list" />
  
  <!-- BEGIN session -->
  <input type="hidden" name="id" value="{session.ID}" />
  <!-- END session -->

<fieldset>

  <legend>{lang:Filter}</legend>


  <label>{lang:Username} <input type="text" name="username" value="{F_USERNAME}" /></label>

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

  <input type="submit" name="submit_filter" value="{L_SUBMIT}" />

</fieldset>

</form>

<form method="post" name="preferences" action="{F_PREF_ACTION}">

<table class="table2">
  <tr class="throw">
    <th>&nbsp;</th>
    <th>{lang:Username}</th>
    <th>{L_STATUS}</th>
    <th>{L_EMAIL}</th>
    <th>{lang:Groups}</th>
    <th>{L_ACTIONS}</th>
  </tr>
  <!-- BEGIN user -->
  <tr class="{user.CLASS}">
    <td><input type="checkbox" name="selection[]" value="{user.ID}" {user.CHECKED} id="selection-{user.ID}" /></td>
    <td><label for="selection-{user.ID}">{user.USERNAME}</label></td>
    <td>{user.STATUS}</td>
    <td>{user.EMAIL}</td>
    <td>{user.GROUPS}</td>
    <td style="text-align:center;">
      <a href="{user.U_PERM}"><img src="./template/yoga/theme/permissions.png" class="button" style="border:none" alt="{L_PERMISSIONS}" title="{L_PERMISSIONS}" /></a>
    </td>
  </tr>
  <!-- END user -->
</table>

<div class="navigationBar">{NAVBAR}</div>

<!-- delete the selected users ? -->
<fieldset>
  <legend>{lang:Deletions}</legend>
  <label><input type="checkbox" name="confirm_deletion" value="1" /> {lang:confirm}</label>
  <input type="submit" value="{lang:Delete selected users}" name="delete" />
</fieldset>

<fieldset>
  <legend>{lang:Status}</legend>

  <table>
    <tr>
      <td>{L_STATUS}</td>
      <td>
        <label><input type="radio" name="status_action" value="leave" checked="checked" /> {lang:leave}</label>
        <label><input type="radio" name="status_action" value="set" id="status_action_set" /> {lang:set to}</label>
        <select onmousedown="document.getElementById('status_action_set').checked = true;" name="status" size="1">
          <!-- BEGIN pref_status_option -->
          <option {pref_status_option.SELECTED} value="{pref_status_option.VALUE}">{pref_status_option.CONTENT}</option>
          <!-- END pref_status_option -->
        </select>
      </td>
    </tr>
  </table>

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
      <label><input type="radio" name="nb_image_line_action" value="leave" checked="checked" /> {lang:leave}</label>
      <label><input type="radio" name="nb_image_line_action" value="set" id="nb_image_line_action_set" /> {lang:set to}</label>
      <input onmousedown="document.getElementById('nb_image_line_action_set').checked = true;"
             size="3" maxlength="2" type="text" name="nb_image_line" value="{NB_IMAGE_LINE}" />
    </td>
  </tr>

  <tr>
    <td>{L_NB_LINE_PAGE}</td>
    <td>
      <label><input type="radio" name="nb_line_page_action" value="leave" checked="checked" /> {lang:leave}</label>
      <label><input type="radio" name="nb_line_page_action" value="set" id="nb_line_page_action_set" /> {lang:set to}</label>
      <input onmousedown="document.getElementById('nb_line_page_action_set').checked = true;"
             size="3" maxlength="2" type="text" name="nb_line_page" value="{NB_LINE_PAGE}" />
    <td>
  </tr>

  <tr>
    <td>{L_TEMPLATE}</td>
    <td>
      <label><input type="radio" name="template_action" value="leave" checked="checked" /> {lang:leave}</label>
      <label><input type="radio" name="template_action" value="set" id="template_action_set" /> {lang:set to}</label>
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
      <label><input type="radio" name="language_action" value="leave" checked="checked" /> {lang:leave}</label>
      <label><input type="radio" name="language_action" value="set" id="language_action_set" /> {lang:set to}</label>
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
      <label><input type="radio" name="recent_period_action" value="leave" checked="checked" /> {lang:leave}</label>
      <label><input type="radio" name="recent_period_action" value="set" id="recent_period_action_set" /> {lang:set to}</label>
      <input onmousedown="document.getElementById('recent_period_action_set').checked = true;"
             type="text" size="3" maxlength="2" name="recent_period" value="{RECENT_PERIOD}" />
    </td>
  </tr>

  <tr>
    <td>{L_EXPAND}</td>
    <td>
      <label><input type="radio" name="expand" value="leave" checked="checked" /> {lang:leave}</label>
      / {lang:set to}
      <label><input type="radio" name="expand" value="true"  {EXPAND_YES} />{L_YES}</label>
      <label><input type="radio" name="expand" value="false" {EXPAND_NO}  />{L_NO}</label>
    </td>
  </tr>

  <tr>
    <td>{L_SHOW_NB_COMMENTS}</td>
    <td>
      <label><input type="radio" name="show_nb_comments" value="leave" checked="checked" /> {lang:leave}</label>
      / {lang:set to}
      <label><input type="radio" name="show_nb_comments" value="true" {SHOW_NB_COMMENTS_YES} />{L_YES}</label>
      <label><input type="radio" name="show_nb_comments" value="false" {SHOW_NB_COMMENTS_NO} />{L_NO}</label>
    </td>
  </tr>

  <tr>
    <td>{L_MAXWIDTH}</td>
    <td>
      <label><input type="radio" name="maxwidth_action" value="leave" checked="checked" /> {lang:leave}</label>
      <label><input type="radio" name="maxwidth_action" value="unset" /> {lang:unset}</label>
      <label><input type="radio" name="maxwidth_action" value="set" id="maxwidth_action_set" /> {lang:set to}</label>
      <input onmousedown="document.getElementById('maxwidth_action_set').checked = true;"
             type="text" size="4" maxlength="4" name="maxwidth" value="{MAXWIDTH}" />
    </td>
  </tr>


  <tr>
    <td>{L_MAXHEIGHT}</td>
    <td>
      <label><input type="radio" name="maxheight_action" value="leave" checked="checked" /> {lang:leave}</label>
      <label><input type="radio" name="maxheight_action" value="unset" /> {lang:unset}</label>
      <label><input type="radio" name="maxheight_action" value="set" id="maxheight_action_set" /> {lang:set to}</label>
      <input onmousedown="document.getElementById('maxheight_action_set').checked = true;"
             type="text" size="4" maxlength="4" name="maxheight" value="{MAXHEIGHT}" />
    </td>
  </tr>


</table>

</fieldset>

<p>
  {lang:target}
  <label><input type="radio" name="target" value="all" /> {lang:all}
  <label><input type="radio" name="target" value="selection" checked="checked" /> {lang:selection}
</p>

<p>
  <input type="submit" value="{L_SUBMIT}" name="pref_submit" />
  <input type="reset" value="{L_RESET}" name="pref_reset" />
</p>

</form>
