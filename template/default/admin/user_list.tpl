<div class="admin">{L_GROUP_ADD_USER}</div>

<form method="post" name="add_user" action="{F_ADD_ACTION}">
<div style="text-align:center">
  {L_USERNAME} <input type="text" name="login" maxlength="50" size="20" />
  {L_PASSWORD} <input type="text" name="password" />
  <input type="submit" name="submit_add" value="{L_SUBMIT}" class="bouton" />
</div>
</form>

<div class="admin">{L_USERS_LIST}</div>

<form method="get" name="filter" action="{F_FILTER_ACTION}">

<div style="text-align:center">

  <input type="hidden" name="page" value="user_list" />
  
  <!-- BEGIN session -->
  <input type="hidden" name="id" value="{session.ID}" />
  <!-- END session -->

  username <input type="text" name="username" value="{F_USERNAME}" />

  status
  <select name="status">
    <!-- BEGIN status_option -->
    <option value="{status_option.VALUE}" {status_option.SELECTED} > {status_option.CONTENT}</option>
    <!-- END status_option -->
  </select>

  group
  <select name="group">
    <!-- BEGIN group_option -->
    <option value="{group_option.VALUE}" {group_option.SELECTED} > {group_option.CONTENT}</option>
    <!-- END group_option -->
  </select>

  {L_ORDER_BY}
  <select name="order_by">
    <!-- BEGIN order_by -->
    <option value="{order_by.VALUE}" {order_by.SELECTED} >{order_by.CONTENT}</option>
    <!-- END order_by -->
  </select>
  <select name="direction">
    <!-- BEGIN direction -->
    <option value="{direction.VALUE}" {direction.SELECTED} >{direction.CONTENT}</option>
    <!-- END direction -->
  </select>
  <input type="submit" name="submit_filter" value="{L_SUBMIT}" class="bouton" />
</div>

</form>

<table style="width:100%;" >
  <tr class="throw">
    <th style="width:20%;">{L_USERNAME}</th>
    <th style="width:20%;">{L_STATUS}</th>
    <th style="width:30%;">{L_EMAIL}</th>
    <th style="width:30%;">{L_GROUPS}</th>
    <th style="width:1%;">{L_ACTIONS}</th>
  </tr>
  <!-- BEGIN user -->
  <tr>
    <td><a href="{user.U_MOD}">{user.USERNAME}</a></td>
    <td>{user.STATUS}</td>
    <td>{user.EMAIL}</td>
    <td>{user.GROUPS}</td>
    <td>[<a href="{user.U_PERM}">{L_PERMISSIONS}</a>]</td>
  </tr>
  <!-- END user -->
</table>
<div class="navigationBar">{NAVBAR}</div>
