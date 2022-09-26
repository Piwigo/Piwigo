{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}

{footer_script}
const cat_nav = '{$CATEGORIES_NAV|escape:javascript}';
(function(){
{* <!-- GROUPS --> *}
var groupsCache = new GroupsCache({
  serverKey: '{$CACHE_KEYS.groups}',
  serverId: '{$CACHE_KEYS._hash}',
  rootUrl: '{$ROOT_URL}'
});

groupsCache.selectize(jQuery('[data-selectize=groups]'));

{* <!-- USERS --> *}
var usersCache = new UsersCache({
  serverKey: '{$CACHE_KEYS.users}',
  serverId: '{$CACHE_KEYS._hash}',
  rootUrl: '{$ROOT_URL}'
});

usersCache.selectize(jQuery('[data-selectize=users]'));

{* <!-- TOGGLES --> *}
function checkStatusOptions() {
  if (jQuery("input[name=status]:checked").val() == "private") {
    jQuery("#privateOptions").show();
  }
  else {
    jQuery("#privateOptions").hide();
  }
}

checkStatusOptions();
jQuery("#selectStatus").change(function() {
  checkStatusOptions();
});

{if isset($nb_users_granted_indirect) && $nb_users_granted_indirect>0}
  jQuery(".toggle-indirectPermissions").click(function(e){
    jQuery(".toggle-indirectPermissions").toggle();
    jQuery("#indirectPermissionsDetails").toggle();
    e.preventDefault();
  });
{/if}
}());

$(document).ready(function () {
  $("h1").append(' <span style="letter-spacing:0">'+cat_nav+'</span>');
})
{/footer_script}

<form action="{$F_ACTION}" method="post" id="categoryPermissions">

<fieldset>
  <legend><span class="icon-lock icon-yellow"></span>{'Access type'|@translate}</legend>

  <p id="selectStatus">
    <label class="font-checkbox">
      <span class="icon-dot-circled"></span>
      <input type="radio" name="status" value="public" {if not $private}checked="checked"{/if}>
      <strong>{'public'|@translate}</strong> : <em>{'any visitor can see this album'|@translate}</em>
    </label>
    <br>
    <label class="font-checkbox">
      <span class="icon-dot-circled"></span>
      <input type="radio" name="status" value="private" {if $private}checked="checked"{/if}>
      <strong>{'private'|@translate}</strong> : <em>{'visitors need to login and have the appropriate permissions to see this album'|@translate}</em>
    </label>
  </p>
</fieldset>

<fieldset id="privateOptions">
  <legend>{'Groups and users'|@translate}</legend>

  <p>
{if count($groups) > 0}
    <strong>{'Permission granted for groups'|@translate}</strong>
    <br>
    <select data-selectize="groups" data-value="{$groups_selected|@json_encode|escape:html}"
      placeholder="{'Type in a search term'|translate}"
      name="groups[]" multiple style="width:600px;"></select>
{else}
    {'There is no group in this gallery.'|@translate} <a href="admin.php?page=group_list" class="externalLink">{'Group management'|@translate}</a>
{/if}
  </p>

  <p>
    <strong>{'Permission granted for users'|@translate}</strong>
    <br>
    <select data-selectize="users" data-value="{$users_selected|@json_encode|escape:html}"
      placeholder="{'Type in a search term'|translate}"
      name="users[]" multiple style="width:600px;"></select>
  </p>

{if isset($nb_users_granted_indirect) && $nb_users_granted_indirect>0}
  <p>
    {'%u users have automatic permission because they belong to a granted group.'|@translate:$nb_users_granted_indirect}
    <a href="#" class="toggle-indirectPermissions" style="display:none">{'hide details'|@translate}</a>
    <a href="#" class="toggle-indirectPermissions">{'show details'|@translate}</a>

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
    <button name="submit" type="submit" class="buttonLike">
      <i class="icon-floppy"></i> {'Save Settings'|@translate}
    </button>

    <label id="applytoSubAction" class="font-checkbox">
      <span class="icon-check"></span>
      <input type="checkbox" name="apply_on_sub" {if $INHERIT}checked="checked"{/if}>
      {'Apply to sub-albums'|@translate}
    </label>
  </p>

<input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
</form>
