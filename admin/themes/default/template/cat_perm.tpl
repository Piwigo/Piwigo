{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.default.css"}

{footer_script}
(function(){
{* <!-- GROUPS --> *}
var groupsCache = new LocalStorageCache('groupsAdminList', 5*60, function(callback) {
  jQuery.getJSON('{$ROOT_URL}ws.php?format=json&method=pwg.groups.getList&per_page=99999', function(data) {
    callback(data.result.groups);
  });
});

jQuery('[data-selectize=groups]').selectize({
  valueField: 'id',
  labelField: 'name',
  searchField: ['name'],
  plugins: ['remove_button']
});

groupsCache.get(function(groups) {
  jQuery('[data-selectize=groups]').each(function() {
    this.selectize.load(function(callback) {
      callback(groups);
    });

    jQuery.each(jQuery(this).data('value'), jQuery.proxy(function(i, id) {
      this.selectize.addItem(id);
    }, this));
  });
});

{* <!-- USERS --> *}
var usersCache = new LocalStorageCache('usersAdminList', 5*60, function(callback) {
  var page = 0,
      users = [];
   
  (function load(page){
    jQuery.getJSON('{$ROOT_URL}ws.php?format=json&method=pwg.users.getList&display=username&per_page=99999&page='+ page, function(data) {
      users = users.concat(data.result.users);
      
      if (data.result.paging.count == data.result.paging.per_page) {
        load(++page);
      }
      else {
        callback(users);
      }
    });
  }(page));
});

jQuery('[data-selectize=users]').selectize({
  valueField: 'id',
  labelField: 'username',
  searchField: ['username'],
  plugins: ['remove_button']
});

usersCache.get(function(users) {
  jQuery('[data-selectize=users]').each(function() {
    this.selectize.load(function(callback) {
      callback(users);
    });

    jQuery.each(jQuery(this).data('value'), jQuery.proxy(function(i, id) {
      this.selectize.addItem(id);
    }, this));
  });
});
}());
{/footer_script}

<div class="titrePage">
  <h2><span style="letter-spacing:0">{$CATEGORIES_NAV}</span> &#8250; {'Edit album'|@translate} {$TABSHEET_TITLE}</h2>
</div>

<form action="{$F_ACTION}" method="post" id="categoryPermissions">

<fieldset>
  <legend>{'Access type'|@translate}</legend>

  <p id="selectStatus">
    <label><input type="radio" name="status" value="public" {if not $private}checked="checked"{/if}> <strong>{'public'|@translate}</strong> : <em>{'any visitor can see this album'|@translate}</em></label>
    <br>
    <label><input type="radio" name="status" value="private" {if $private}checked="checked"{/if}> <strong>{'private'|@translate}</strong> : <em>{'visitors need to login and have the appropriate permissions to see this album'|@translate}</em></label>
  </p>
</fieldset>

<fieldset id="privateOptions">
  <legend>{'Groups and users'|@translate}</legend>

  <p>
{if count($groups) > 0}
    <strong>{'Permission granted for groups'|@translate}</strong>
    <br>
    <select data-selectize="groups" data-value="{$groups_selected|@json_encode|escape:html}"
        name="groups[]" multiple style="width:600px;" ></select>
{else}
    {'There is no group in this gallery.'|@translate} <a href="admin.php?page=group_list" class="externalLink">{'Group management'|@translate}</a>
{/if}
  </p>

  <p>
    <strong>{'Permission granted for users'|@translate}</strong>
    <br>
    <select data-selectize="users" data-value="{$users_selected|@json_encode|escape:html}"
        name="users[]" multiple style="width:600px;" ></select>
  </p>

{if isset($nb_users_granted_indirect) && $nb_users_granted_indirect>0}
  <p>
    {'%u users have automatic permission because they belong to a granted group.'|@translate:$nb_users_granted_indirect}
    <a href="#" id="indirectPermissionsDetailsHide" style="display:none">{'hide details'|@translate}</a>
    <a href="#" id="indirectPermissionsDetailsShow">{'show details'|@translate}</a>

    <ul id="indirectPermissionsDetails" style="display:none">
  {foreach from=$user_granted_indirect_groups item=group_details}
      <li><strong>{$group_details.group_name}</strong> : {$group_details.group_users}</li>
  {/foreach}
    </ul>
  </p>
{/if}

{*
  <h4>{'Groups'|@translate}</h4>

  <fieldset>
    <legend>{'Permission granted'|@translate}</legend>
    <ul>
      {foreach from=$group_granted_ids item=id}
      <li><label><input type="checkbox" name="deny_groups[]" value="{$id}"> {$all_groups[$id]}</label></li>
      {/foreach}
    </ul>
    <input class="submit" type="submit" name="deny_groups_submit" value="{'Deny selected groups'|@translate}">
  </fieldset>

  <fieldset>
    <legend>{'Permission denied'|@translate}</legend>
    <ul>
      {foreach from=$group_denied_ids item=id}
      <li><label><input type="checkbox" name="grant_groups[]" value="{$id}"> {$all_groups[$id]}</label></li>
      {/foreach}
    </ul>
    <input class="submit" type="submit" name="grant_groups_submit" value="{'Grant selected groups'|@translate}">
    <label><input type="checkbox" name="apply_on_sub">{'Apply to sub-albums'|@translate}</label>
  </fieldset>

  <h4>{'Users'|@translate}</h4>

  <fieldset>
    <legend>{'Permission granted'|@translate}</legend>
    <ul>
      {foreach from=$user_granted_direct_ids item=id}
      <li><label><input type="checkbox" name="deny_users[]" value="{$id}"> {$all_users[$id]}</label></li>
      {/foreach}
    </ul>
    <input class="submit" type="submit" name="deny_users_submit" value="{'Deny selected users'|@translate}">
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
    <input class="submit" type="submit" name="grant_users_submit" value="{'Grant selected users'|@translate}">
    <label><input type="checkbox" name="apply_on_sub">{'Apply to sub-albums'|@translate}</label>
  </fieldset>
*}
</fieldset>

  <p style="margin:12px;text-align:left;">
    <input class="submit" type="submit" value="{'Save Settings'|@translate}" name="submit">
    <label id="applytoSubAction" style="display:none;"><input type="checkbox" name="apply_on_sub" {if $INHERIT}checked="checked"{/if}>{'Apply to sub-albums'|@translate}</label>
  </p>

<input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
</form>
