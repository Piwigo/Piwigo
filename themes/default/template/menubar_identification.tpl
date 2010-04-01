<dt>{'Identification'|@translate}</dt>
<dd>
	{if isset($USERNAME)}
	<p>{'Hello'|@translate}&nbsp;{$USERNAME}&nbsp;!</p>
	{/if}

	<ul>
	{if isset($U_REGISTER)}
	<li><a href="{$U_REGISTER}" title="{'Create a new account'|@translate}" rel="nofollow">{'Register'|@translate}</a></li>
	{/if}

	{if isset($U_LOGIN)}
	<li><a href="{$U_LOGIN}" rel="nofollow">{'Login'|@translate}</a></li>
	{/if}

	{if isset($U_LOGOUT)}
	<li><a href="{$U_LOGOUT}">{'Logout'|@translate}</a></li>
	{/if}

	{if isset($U_PROFILE)}
	<li><a href="{$U_PROFILE}" title="{'customize the appareance of the gallery'|@translate}">{'Customize'|@translate}</a></li>
	{/if}

	{if isset($U_ADMIN)}
	<li><a href="{$U_ADMIN}" title="{'available for administrators only'|@translate}">{'Administration'|@translate}</a></li>
	{/if}
	</ul>

	{if isset($U_LOGIN)}
	<form method="post" action="{$U_LOGIN}" id="quickconnect">
	<fieldset>
	<legend>{'Quick connect'|@translate}</legend>
	<div>
	<label for="username">{'Username'|@translate}</label><br>
	<input type="text" name="username" id="username" value="" style="width:99%">
	</div>

	<div><label for="password">{'Password'|@translate}</label>
	<br>
	<input type="password" name="password" id="password" style="width:99%">
	</div>

	{if $AUTHORIZE_REMEMBERING}
	<div><label for="remember_me">
	{'Auto login'|@translate}
	<input type="checkbox" name="remember_me" id="remember_me" value="1">
	</label></div>
	{/if}

	<div>
	<input class="submit" type="submit" name="login" value="{'Submit'|@translate}">
	<ul class="actions">
		<li><a href="{$U_LOST_PASSWORD}" title="{'Forgot your password?'|@translate}" rel="nofollow"><img src="{$ROOT_URL}{$themeconf.icon_dir}/lost_password.png" class="button" alt="{'Forgot your password?'|@translate}"></a></li>
		{if isset($U_REGISTER)}
		<li><a href="{$U_REGISTER}" title="{'Create a new account'|@translate}" rel="nofollow"><img src="{$ROOT_URL}{$themeconf.icon_dir}/register.png" class="button" alt="{'Register'|@translate}"></a></li>
		{/if}
	</ul>
	</div>

	</fieldset>
	</form>
	{/if}
</dd>

