<!-- $Id$ -->
<div id="content">

  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{U_REGISTER}" title="{lang:Create a new account}"><img src="./template/cclear/theme/register.png" alt="{lang:register}"/></a></li>
      <li><a href="{U_HOME}" title="{lang:Go through the gallery as a visitor}"><img src="./template/cclear/theme/home.png" alt="{lang:home}"/></a></li>
    </ul>
    <h2>{lang:Identification}</h2>
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

<form action="{F_LOGIN_ACTION}" method="post" class="properties">
  <fieldset>
    <legend>{lang:Connection settings}</legend>

    <ul>
      <li> 
        <label for="username">{L_USERNAME}</label>
        <input class="login" type="text" name="username" id="username" size="25" maxlength="40" value="{USERNAME}">
      </li>
  
      <li>
        <label for="password">{L_PASSWORD}</label>
        <input class="login" type="password" name="password" id="password" size="25" maxlength="25">
      </li>
  
      <!-- BEGIN remember_me -->
      <li>
        <label>{L_REMEMBER_ME}</label>
        <input type="checkbox" name="remember_me" value="1">
      </li>
      <!-- END remember_me -->
    </ul>
  </fieldset>

  <p><input type="submit" name="login" value="{L_LOGIN}"></p>
</form>
 
  <p>
    <a href="{U_REGISTER}"><img src="template/cclear/theme/register.png" alt=""> {L_REGISTER}</a>
  </p>

</div> <!-- content -->
