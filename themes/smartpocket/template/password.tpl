{include file='infos_errors.tpl'}
<div data-role="content">
<h3>{'Forgot your password?'|translate}</h3>
{if $action ne 'none'}
<form id="lostPassword" action="{$form_action}?action={$action}{if isset($key)}&amp;key={$key}{/if}" method="post">
  <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">

  {if $action eq 'lost'}
  <div data-role="fieldcontain">
  <label for="username">{'Username or email'|@translate}</label>
  <input type="text" id="username_or_email" name="username_or_email" size="40" maxlength="40"{if isset($username_or_email)} value="{$username_or_email}"{/if}>
  <input type="submit" name="submit" value="{'Change my password'|@translate}">
  </div>

  {elseif $action eq 'reset'}
  <div data-role="fieldcontain">
  <label for="password">{'New password'|@translate}</label>
   <input type="password" name="use_new_pwd" id="use_new_pwd" value="">
  <label for="passwordConf">{'Confirm Password'|@translate}</label>
  <input type="password" name="passwordConf" id="passwordConf" value="">
  <input type="submit" name="submit" value="{'Submit'|@translate}">
  </div>
  {/if}

</form>
{/if} {* $action ne 'none' *}
</div>
