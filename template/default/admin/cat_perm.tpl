<h1>{lang:Manage permissions for a category}</h1>

<h2>{CATEGORIES_NAV}</h2>

<form action="{F_ACTION}" method="post" id="categoryPermissions">

  <h2>{lang:Groups}</h2>

  <fieldset>
    <legend>{lang:Permission granted}</legend>
    <ul>
      <!-- BEGIN group_granted -->
      <li><label><input type="checkbox" name="deny_groups[]" value="{group_granted.ID}" /> {group_granted.NAME}</label></li>
      <!-- END group_granted -->
    </ul>
    <input type="submit" name="deny_groups_submit" value="{lang:Deny selected groups}" />
  </fieldset>

  <fieldset>
    <legend>{lang:Permission denied}</legend>
    <ul>
      <!-- BEGIN group_denied -->
      <li><label><input type="checkbox" name="grant_groups[]" value="{group_denied.ID}"> {group_denied.NAME}</label></li>
      <!-- END group_denied -->
    </ul>
    <input type="submit" name="grant_groups_submit" value="{lang:Grant selected groups}" />
  </fieldset>

  <h2>{lang:Users}</h2>
  
  <fieldset>
    <legend>{lang:Permission granted}</legend>
    <ul>
      <!-- BEGIN user_granted -->
      <li><label><input type="checkbox" name="deny_users[]" value="{user_granted.ID}" /> {user_granted.NAME}</label></li>
      <!-- END user_granted -->
    </ul>
    <input type="submit" name="deny_users_submit" value="{lang:Deny selected users}" />
  </fieldset>

  <fieldset>
    <legend>{lang:Permission granted thanks to a group}</legend>
    <ul>
      <!-- BEGIN user_granted_indirect -->
      <li>{user_granted_indirect.NAME} ({user_granted_indirect.GROUP})</li>
      <!-- END user_granted_indirect -->
    </ul>
  </fieldset>

  <fieldset>
    <legend>{lang:Permission denied}</legend>
    <ul>
      <!-- BEGIN user_denied -->
      <li><label><input type="checkbox" name="grant_users[]" value="{user_denied.ID}"> {user_denied.NAME}</label></li>
      <!-- END user_denied -->
    </ul>
    <input type="submit" name="grant_users_submit" value="{lang:Grant selected users}" />
  </fieldset>

</form>
