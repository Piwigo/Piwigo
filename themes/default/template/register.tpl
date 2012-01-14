{if isset($MENUBAR)}{$MENUBAR}{/if}
<div id="registerPage">

<div id="content" class="content">

<div class="titrePage">
	<ul class="categoryActions">
		<li><a href="{$U_HOME}" title="{'Home'|@translate}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-home">&nbsp;</span><span class="pwg-button-text">{'Home'|@translate}</span>
		</a></li>
  </ul>
	<h2>{'Registration'|@translate}</h2>
</div>

{include file='infos_errors.tpl'}

<form method="post" action="{$F_ACTION}" class="properties" name="register_form">
  <fieldset>
    <legend>{'Enter your personnal informations'|@translate}</legend>

    <ul>
      <li>
        <span class="property">
          <label for="login">* {'Username'|@translate}</label>
        </span>
        <input type="text" name="login" id="login" value="{$F_LOGIN}" >
      </li>
      <li>
        <span class="property">
          <label for="password">* {'Password'|@translate}</label>
        </span>
        <input type="password" name="password" id="password" >
      </li>
      <li>
        <span class="property">
          <label for="password_conf">* {'Confirm Password'|@translate}</label>
        </span>
        <input type="password" name="password_conf" id="password_conf" >
      </li>
      <li>
        <span class="property">
          <label for="mail_address">{if $obligatory_user_mail_address}* {/if}{'Email address'|@translate}</label>
        </span>
        <input type="text" name="mail_address" id="mail_address" value="{$F_EMAIL}" >
      {if not $obligatory_user_mail_address}
        ({'useful when password forgotten'|@translate})
      {/if}
      </li>
      <li>
        <span class="property">
          <label for="send_password_by_mail">{'Send my connection settings by email'|@translate}</label>
        </span>
        <input type="checkbox" name="send_password_by_mail" id="send_password_by_mail" value="1" checked="checked">
      </li>
    </ul>

  </fieldset>

  <p class="bottomButtons">
		<input type="hidden" name="key" value="{$F_KEY}" >
    <input class="submit" type="submit" name="submit" value="{'Register'|@translate}">
    <input class="submit" type="reset" value="{'Reset'|@translate}">
  </p>

</form>

<script type="text/javascript"><!--
document.register_form.login.focus();
//--></script>

</div> <!-- content -->
</div> <!-- registerPage -->
