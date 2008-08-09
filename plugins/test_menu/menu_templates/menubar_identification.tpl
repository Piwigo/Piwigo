
<!-- identification menu bar -->
<dt>{$section.NAME|@translate}</dt>
<dd>
  {if isset($section.ITEMS.USERNAME)}
  <p>{'hello'|@translate}&nbsp;{$section.ITEMS.USERNAME}&nbsp;!</p>
  {/if}

<ul>
  {if isset($section.ITEMS.U_REGISTER)}
  <li><a href="{$section.ITEMS.U_REGISTER}" title="{'Create a new account'|@translate}" rel="nofollow">{'Register'|@translate}</a></li>
  {/if}

  {if isset($section.ITEMS.U_IDENTIFY)}
  <li><a href="{$section.ITEMS.U_IDENTIFY}" rel="nofollow">{'Connection'|@translate}</a></li>
  {/if}

  {if isset($section.ITEMS.U_LOGOUT)}
  <li><a href="{$section.ITEMS.U_LOGOUT}">{'logout'|@translate}</a></li>
  {/if}

  {if isset($section.ITEMS.U_PROFILE)}
  <li><a href="{$section.ITEMS.U_PROFILE}" title="{'hint_customize'|@translate}">{'customize'|@translate}</a></li>
  {/if}

  {if isset($section.ITEMS.U_ADMIN)}
  <li><a href="{$section.ITEMS.U_ADMIN}" title="{'hint_admin'|@translate}">{'admin'|@translate}</a></li>
  {/if}
</ul>

{if isset($section.ITEMS.U_IDENTIFY)}
<form method="post" action="{$section.ITEMS.U_IDENTIFY}" class="filter" id="quickconnect">
<fieldset>
  <legend>{'Quick connect'|@translate}</legend>

  <label>
    {'Username'|@translate}
    <input type="text" name="username" size="15" value="" id="iusername">
  </label>

  <label>
    {'Password'|@translate}
    <input type="password" name="password" size="15" id="ipassword">
  </label>

  {if $section.ITEMS.AUTHORIZE_REMEMBERING}
  <label>
    {'remember_me'|@translate}
    <input type="checkbox" name="remember_me" value="1" id="iremember_me">
  </label>
  {/if}
  <p>
    <input class="submit" type="submit" name="login" value="{'Submit'|@translate}">
  </p>

  <ul class="actions">
    <li><a href="{$section.ITEMS.U_LOST_PASSWORD}" title="{'Forgot your password?'|@translate}" rel="nofollow"><img src="{$ROOT_URL}{$themeconf.icon_dir}/lost_password.png" class="button" alt="{'Forgot your password?'|@translate}"></a></li>
    {if isset($section.ITEMS.U_REGISTER)}
    <li><a href="{$section.ITEMS.U_REGISTER}" title="{'Create a new account'|@translate}" rel="nofollow"><img src="{$ROOT_URL}{$themeconf.icon_dir}/register.png" class="button" alt="{'Register'|@translate}"/></a></li>
    {/if}
  </ul>

</fieldset>
</form>
  {/if}

</dd>
