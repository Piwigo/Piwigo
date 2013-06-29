{include file='infos_errors.tpl'}
<div data-role="content">
<h3>{'Register'|@translate}</h3>
<form method="post" action="{$F_ACTION}" class="properties" name="register_form">

  <div data-role="fieldcontain">
  <label for="login">* {'Username'|@translate}</label>
  <input type="text" name="login" id="login" value="{$F_LOGIN}">
  </div>

  <div data-role="fieldcontain">
  <label for="password">* {'Password'|@translate}</label>
  <input type="password" name="password" id="password">
  </div>

  <div data-role="fieldcontain">
  <label for="password_conf">* {'Confirm Password'|@translate}</label>
  <input type="password" name="password_conf" id="password_conf">
  </div>

  <div data-role="fieldcontain">
  <label for="mail_address">{if $obligatory_user_mail_address}* {/if}{'Email address'|@translate}</label>
  <input type="text" name="mail_address" id="mail_address" value="{$F_EMAIL}" >
  </div>

  <div data-role="fieldcontain">
  <label for="send_password_by_mail">{'Send my connection settings by email'|@translate}</label>
  <input type="checkbox" name="send_password_by_mail" id="send_password_by_mail" value="1" checked="checked">
  </div>

  <div data-role="fieldcontain">
		<input type="hidden" name="key" value="{$F_KEY}" >
    <input class="submit" type="submit" name="submit" value="{'Register'|@translate}">
    <input class="submit" type="reset" value="{'Reset'|@translate}">
  </div>

</form>
</div>
