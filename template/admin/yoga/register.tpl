<div id="registerPage">

<div id="content">

  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{U_HOME}" title="{lang:return to homepage}"><img src="{themeconf:icon_dir}/home.png" class="button" alt="{lang:home}"/></a></li>
    </ul>
    <h2>{lang:Registration}</h2>
  </div>

  <!-- BEGIN errors -->
  <div class="errors">
    <ul>
      <!-- BEGIN error -->
      <li>{errors.error.ERROR}</li>
      <!-- END error -->
    </ul>
  </div>
  <!-- END errors -->

<form method="post" action="{F_ACTION}" class="properties" name="register_form">
  <fieldset>
    <legend>{lang:Enter your personnal informations}</legend>

    <ul>
      <li>
        <span class="property">
          <label for="login">* {lang:Username}</label>
        </span>
        <input type="text" name="login" id="login" value="{F_LOGIN}" onfocus="this.className='focus';" onblur="this.className='nofocus';">
      </li>

      <li>
        <span class="property">
          <label for="password">* {lang:Password}</label>
        </span>
        <input type="password" name="password" id="password" onfocus="this.className='focus';" onblur="this.className='nofocus';">
      </li>

      <li>
        <span class="property">
          <label for="password_conf">* {lang:Confirm Password}</label>
        </span>
        <input type="password" name="password_conf" id="password_conf" onfocus="this.className='focus';" onblur="this.className='nofocus';">
      </li>

      <li>
        <span class="property">
          <label for="mail_address">{lang:Mail address}</label>
        </span>
        <input type="text" name="mail_address" id="mail_address" value="{F_EMAIL}" onfocus="this.className='focus';" onblur="this.className='nofocus';">
        ({lang:useful when password forgotten})
      </li>
    </ul>

  </fieldset>

  <p class="bottomButtons">
    <input type="submit" name="submit" value="{lang:Register}">
    <input type="reset" value="{lang:Reset}">
  </p>

</form>

<script type="text/javascript"><!--
document.register_form.login.focus();
//--></script>

</div> <!-- content -->
</div> <!-- registerPage -->
