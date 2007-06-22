<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="{themeconf:icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{lang:title_liste_users}</h2>
</div>

<form class="filter" method="post" name="add_user" action="{F_ADD_ACTION}">
  <fieldset>
    <legend>{lang:Add a user}</legend>
    <label>{lang:Username} <input type="text" name="login" maxlength="50" size="20" /></label>
    <label>{lang:Password} <input type="text" name="password" /></label>
    <label>{lang:Email address} <input type="text" name="email" /></label>
    <input class="submit" type="submit" name="submit_add" value="{lang:submit}" {TAG_INPUT_ENABLED} />
  </fieldset>
</form>

<form class="filter" method="get" name="filter" action="{F_FILTER_ACTION}">
<fieldset>
  <legend>{lang:Filter}</legend>
  <input type="hidden" name="page" value="user_list" />

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

  <input class="submit" type="submit" name="submit_filter" value="{lang:submit}" />

</fieldset>

</form>

<form method="post" name="preferences" action="{F_PREF_ACTION}">

<table class="table2">
  <tr class="throw">
    <th>&nbsp;</th>
    <th>{lang:Username}</th>
    <th>{lang:user_status}</th>
    <th>{lang:Email address}</th>
    <th>{lang:Groups}</th>
    <th>{lang:properties}</th>
    <!-- BEGIN cpl_title_user -->
    <th>{cpl_title_user.CAPTION}</th>
    <!-- END cpl_title_user -->
    <th>{lang:actions}</th>
  </tr>
  <!-- BEGIN user -->
  <tr class="{user.CLASS}">
    <td><input type="checkbox" name="selection[]" value="{user.ID}" {user.CHECKED} id="selection-{user.ID}" /></td>
    <td><label for="selection-{user.ID}">{user.USERNAME}</label></td>
    <td>{user.STATUS}</td>
    <td>{user.EMAIL}</td>
    <td>{user.GROUPS}</td>
    <td>{user.PROPERTIES}</td>
    <!-- BEGIN cpl_user -->
    <td>{user.cpl_user.DATA}</td>
    <!-- END cpl_user -->
    <td style="text-align:center;">
      <a href="{user.U_PERM}"><img src="{themeconf:icon_dir}/permissions.png" class="button" style="border:none" alt="{lang:permissions}" title="{lang:permissions}" /></a>
      <a href="{user.U_PROFILE}"><img src="{themeconf:icon_dir}/edit_s.png" class="button" style="border:none" alt="{lang:Profile}" title="{lang:Profile}" /></a>
      <!-- BEGIN cpl_link_user -->
      {user.cpl_link_user.DATA}
      <!-- END cpl_link_user -->
    </td>
  </tr>
  <!-- END user -->
</table>

<div class="navigationBar">{NAVBAR}</div>

<!-- delete the selected users ? -->
<fieldset>
  <legend>{lang:Deletions}</legend>
  <label><input type="checkbox" name="confirm_deletion" value="1" /> {lang:confirm}</label>
  <input class="submit" type="submit" value="{lang:Delete selected users}" name="delete" {TAG_INPUT_ENABLED}/>
</fieldset>

<fieldset>
  <legend>{lang:Status}</legend>

  <table>
    <tr>
      <td>{lang:Status}</td>
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

    <!-- BEGIN adviser -->
    <tr>
      <td>{lang:adviser}</td>
      <td>
        <label><input type="radio" name="adviser" value="leave" checked="checked" /> {lang:leave}</label>
        / {lang:set to}
        <label><input type="radio" name="adviser" value="true"  {ADVISER_YES} />{lang:yes}</label>
        <label><input type="radio" name="adviser" value="false" {ADVISER_NO}  />{lang:no}</label>
      </td>
    </tr>
    <!-- END adviser -->

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

<!-- Properties -->
<fieldset>
  <legend>{lang:properties}</legend>

  <table>

    <tr>
      <td>{lang:enabled_high}</td>
      <td>
        <label><input type="radio" name="enabled_high" value="leave" checked="checked" /> {lang:leave}</label>
        / {lang:set to}
        <label><input type="radio" name="enabled_high" value="true"  {ENABLED_HIGH_YES} />{lang:yes}</label>
        <label><input type="radio" name="enabled_high" value="false" {ENABLED_HIGH_NO}  />{lang:no}</label>
      </td>
    </tr>

  </table>

</fieldset>

<!-- preference -->
<fieldset>
  <legend>{lang:Preferences}</legend>

<table>

  <tr>
    <td>{lang:nb_image_per_row}</td>
    <td>
      <label><input type="radio" name="nb_image_line_action" value="leave" checked="checked" /> {lang:leave}</label>
      <label><input type="radio" name="nb_image_line_action" value="set" id="nb_image_line_action_set" /> {lang:set to}</label>
      <input onmousedown="document.getElementById('nb_image_line_action_set').checked = true;"
             size="3" maxlength="2" type="text" name="nb_image_line" value="{NB_IMAGE_LINE}" />
    </td>
  </tr>

  <tr>
    <td>{lang:nb_row_per_page}</td>
    <td>
      <label><input type="radio" name="nb_line_page_action" value="leave" checked="checked" /> {lang:leave}</label>
      <label><input type="radio" name="nb_line_page_action" value="set" id="nb_line_page_action_set" /> {lang:set to}</label>
      <input onmousedown="document.getElementById('nb_line_page_action_set').checked = true;"
             size="3" maxlength="2" type="text" name="nb_line_page" value="{NB_LINE_PAGE}" />
    <td>
  </tr>

  <tr>
    <td>{lang:theme}</td>
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
    <td>{lang:language}</td>
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
    <td>{lang:recent_period}</td>
    <td>
      <label><input type="radio" name="recent_period_action" value="leave" checked="checked" /> {lang:leave}</label>
      <label><input type="radio" name="recent_period_action" value="set" id="recent_period_action_set" /> {lang:set to}</label>
      <input onmousedown="document.getElementById('recent_period_action_set').checked = true;"
             type="text" size="3" maxlength="2" name="recent_period" value="{RECENT_PERIOD}" />
    </td>
  </tr>

  <tr>
    <td>{lang:auto_expand}</td>
    <td>
      <label><input type="radio" name="expand" value="leave" checked="checked" /> {lang:leave}</label>
      / {lang:set to}
      <label><input type="radio" name="expand" value="true"  {EXPAND_YES} />{lang:yes}</label>
      <label><input type="radio" name="expand" value="false" {EXPAND_NO}  />{lang:no}</label>
    </td>
  </tr>

  <tr>
    <td>{lang:show_nb_comments}</td>
    <td>
      <label><input type="radio" name="show_nb_comments" value="leave" checked="checked" /> {lang:leave}</label>
      / {lang:set to}
      <label><input type="radio" name="show_nb_comments" value="true" {SHOW_NB_COMMENTS_YES} />{lang:yes}</label>
      <label><input type="radio" name="show_nb_comments" value="false" {SHOW_NB_COMMENTS_NO} />{lang:no}</label>
    </td>
  </tr>

  <tr>
    <td>{lang:show_nb_hits}</td>
    <td>
      <label><input type="radio" name="show_nb_hits" value="leave" checked="checked" /> {lang:leave}</label>
      / {lang:set to}
      <label><input type="radio" name="show_nb_hits" value="true" {SHOW_NB_HITS_YES} />{lang:yes}</label>
      <label><input type="radio" name="show_nb_hits" value="false" {SHOW_NB_HITS_NO} />{lang:no}</label>
    </td>
  </tr>

  <tr>
    <td>{lang:maxwidth}</td>
    <td>
      <label><input type="radio" name="maxwidth_action" value="leave" checked="checked" /> {lang:leave}</label>
      <label><input type="radio" name="maxwidth_action" value="unset" /> {lang:unset}</label>
      <label><input type="radio" name="maxwidth_action" value="set" id="maxwidth_action_set" /> {lang:set to}</label>
      <input onmousedown="document.getElementById('maxwidth_action_set').checked = true;"
             type="text" size="4" maxlength="4" name="maxwidth" value="{MAXWIDTH}" />
    </td>
  </tr>


  <tr>
    <td>{lang:maxheight}</td>
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
  <label><input type="radio" name="target" value="all" /> {lang:all}</label>
  <label><input type="radio" name="target" value="selection" checked="checked" /> {lang:selection}</label>
</p>

<p>
  <input class="submit" type="submit" value="{lang:submit}" name="pref_submit" {TAG_INPUT_ENABLED} />
  <input class="submit" type="reset" value="{lang:reset}" name="pref_reset" />
</p>

</form>
