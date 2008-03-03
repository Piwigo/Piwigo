{* $Id$ *}
<div id="registerPage">

<div id="content">

  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{$U_HOME}" title="{'return to homepage'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/home.png" class="button" alt="{'home'|@translate}"/></a></li>
    </ul>
    <h2>{'Registration'|@translate}</h2>
  </div>

{if isset($errors)}
  <div class="errors">
    <ul>
      {foreach from=$errors item=error}
      <li>{$error}</li>
      {/foreach}
    </ul>
  </div>
{/if}

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
          <label for="mail_address">{'Mail address'|@translate}</label>
        </span>
        <input type="text" name="mail_address" id="mail_address" value="{$F_EMAIL}" >
        ({'useful when password forgotten'|@translate})
      </li>
    </ul>

  </fieldset>

  <p class="bottomButtons">
    <input class="submit" type="submit" name="submit" value="{'Register'|@translate}">
    <input class="submit" type="reset" value="{'Reset'|@translate}">
  </p>

</form>

<script type="text/javascript"><!--
document.register_form.login.focus();
//--></script>

</div> <!-- content -->
</div> <!-- registerPage -->
