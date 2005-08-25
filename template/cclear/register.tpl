<div id="registerPage">

<div id="content">

  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{U_HOME}" title="{lang:return to homepage}"><img src="./template/cclear/theme/home.png" alt="{lang:home}"/></a></li>
    </ul>
    <h2>{lang:Registration}</h2>
  </div>

<!-- TO DO -->
<!-- It's easy, just lok at identification.tpl ;-) -->

  <!-- BEGIN errors -->
  <div class="errors">
    <ul>
      <!-- BEGIN error -->
      <li>{errors.error.ERROR}</li>
      <!-- END error -->
    </ul>
  </div>
  <!-- END errors -->

<form method="post" action="{F_ACTION}" class="filter">
  <fieldset>
    <legend>{lang:Enter your personnal informations}</legend>

    <label>* {lang:Username}<input type="text" name="login" value="{F_LOGIN}"></label>
    <label>* {lang:Password}<input type="password" name="password"></label>
    <label>* {lang:Confirm Password}<input type="password" name="password_conf"></label>
    <label>{lang:Mail address}<input type="text" name="mail_address" value="{F_EMAIL}"></label>
  </fieldset>

  <p class="bottomButtons">
    <input type="submit" name="submit" value="{lang:Register}">
  </p>

</form>

</div> <!-- content -->
</div> <!-- registerPage -->
