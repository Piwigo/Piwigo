<div class="titrePage">
  <h2>{'Manage permissions for a category'|@translate}</h2>
</div>

<h3>{$CATEGORIES_NAV}</h3>

<form action="{$F_ACTION}" method="post" id="categoryPermissions">

  <h4>{'Groups'|@translate}</h4>

  <fieldset>
    <legend>{'Permission granted'|@translate}</legend>
    <ul>
      {foreach from=$group_granted_ids item=id}
      <li><label><input type="checkbox" name="deny_groups[]" value="{$id}"> {$all_groups[$id]}</label></li>
      {/foreach}
    </ul>
    <input class="submit" type="submit" name="deny_groups_submit" value="{'Deny selected groups'|@translate}" {$TAG_INPUT_ENABLED}>
  </fieldset>

  <fieldset>
    <legend>{'Permission denied'|@translate}</legend>
    <ul>
      {foreach from=$group_denied_ids item=id}
      <li><label><input type="checkbox" name="grant_groups[]" value="{$id}"> {$all_groups[$id]}</label></li>
      {/foreach}
    </ul>
    <input class="submit" type="submit" name="grant_groups_submit" value="{'Grant selected groups'|@translate}" {$TAG_INPUT_ENABLED}>
  </fieldset>

  <h4>{'Users'|@translate}</h4>

  <fieldset>
    <legend>{'Permission granted'|@translate}</legend>
    <ul>
      {foreach from=$user_granted_direct_ids item=id}
      <li><label><input type="checkbox" name="deny_users[]" value="{$id}"> {$all_users[$id]}</label></li>
      {/foreach}
    </ul>
    <input class="submit" type="submit" name="deny_users_submit" value="{'Deny selected users'|@translate}" {$TAG_INPUT_ENABLED}>
  </fieldset>

  <fieldset>
    <legend>{'Permission granted thanks to a group'|@translate}</legend>
    {if isset($user_granted_indirects) }
    <ul>
      {foreach from=$user_granted_indirects item=user_group}
      <li>{$user_group.USER} ({$user_group.GROUP})</li>
      {/foreach}
    </ul>
    {/if}
  </fieldset>

  <fieldset>
    <legend>{'Permission denied'|@translate}</legend>
    <ul>
      {foreach from=$user_denied_ids item=id}
      <li><label><input type="checkbox" name="grant_users[]" value="{$id}"> {$all_users[$id]}</label></li>
      {/foreach}
    </ul>
    <input class="submit" type="submit" name="grant_users_submit" value="{'Grant selected users'|@translate}" {$TAG_INPUT_ENABLED}>
  </fieldset>

</form>
