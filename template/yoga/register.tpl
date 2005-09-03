<div id="registerPage">

<div id="content">

  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{U_HOME}" title="{lang:return to homepage}"><img src="./template/yoga/theme/home.png" alt="{lang:home}"/></a></li>
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

<form method="post" action="{F_ACTION}" class="properties">
  <fieldset>
    <legend>{lang:Enter your personnal informations}</legend>

    <ul>
      <li>
        <label for="login" class="mandatory">* {lang:Username}</label>
        <input type="text" name="login" id="login" value="{F_LOGIN}" onfocus="this.className='focus';" onblur="this.className='nofocus';">
      </li>

      <li>
        <label for="password" class="mandatory">* {lang:Password}</label>
        <input type="password" name="password" id="password" onfocus="this.className='focus';" onblur="this.className='nofocus';">
      </li>

      <li>
        <label for="password_conf" class="mandatory">* {lang:Confirm Password}</label>
        <input type="password" name="password_conf" id="password_conf" onfocus="this.className='focus';" onblur="this.className='nofocus';">
      </li>

      <li>
        <label for="mail_address">{lang:Mail address}</label>
        <input type="text" name="mail_address" id="mail_address" value="{F_EMAIL}" onfocus="this.className='focus';" onblur="this.className='nofocus';">
      </li>
    </ul>

  </fieldset>

  <p class="bottomButtons">
    <input type="submit" name="submit" value="{lang:Register}">
    <input type="reset" value="{lang:Reset}">
  </p>

</form>

</div> <!-- content -->
</div> <!-- registerPage -->
