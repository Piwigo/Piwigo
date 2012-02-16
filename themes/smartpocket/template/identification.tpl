{include file='infos_errors.tpl'}
<div data-role="content">
<h3>{'Identification'|@translate}</h3>
<form action="{$F_LOGIN_ACTION}" method="post" name="login_form" class="properties">

  <div data-role="fieldcontain">
  <label for="username">{'Username'|@translate}</label>
  <input type="text" name="username" id="username">
  </div>
    

  <div data-role="fieldcontain">
  <label for="password">{'Password'|@translate}</label>
  <input type="password" name="password" id="password" value="">
  </div>

  {if $authorize_remembering }
  <div data-role="fieldcontain">
  <label for="remember_me">{'Auto login'|@translate}</label>
  <input type="checkbox" name="remember_me" id="remember_me" value="1">
  </div>
  {/if}

  <div data-role="fieldcontain">
    <input type="hidden" name="redirect" value="{$U_REDIRECT|@urlencode}">
    <input type="submit" name="login" value="{'Submit'|@translate}">
  </div>

</form>

	<div data-role="fieldcontain">
{if isset($U_REGISTER)}
		<a href="{$U_REGISTER}" data-role="button">{'Register'|@translate}</a>
{/if}
{*
{if isset($U_LOST_PASSWORD)}
		<a href="{$U_LOST_PASSWORD}" data-role="button">{'Forgot your password?'|@translate}</a>
{/if}
*}
	</div>
</div>
