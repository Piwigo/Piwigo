<!-- $Id$ -->
<div id="content">

  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{U_REGISTER}" title="{lang:Create a new account}"><img src="./template/cclear/theme/register.png" alt="{lang:register"/></a></li>
      <li><a href="{U_HOME}" title="{lang:Go through the gallery as a visitor}"><img src="./template/cclear/theme/home.png" alt="{lang:home}"/></a></li>
    </ul>
    <h2>{lang:Identification}</h2>
  </div>

<div class="formbox">
<form action="{F_LOGIN_ACTION}" method="post">
  <!-- BEGIN errors -->
  <div class="errors">
    <ul>
      <!-- BEGIN error -->
      <li>{errors.error.ERROR}</li>
      <!-- END error -->
    </ul>
  </div>
  <!-- END errors -->
  <dl> 
    <dt>{L_USERNAME}:</dt>
    <dd><input class="login" type="text" name="username" size="25" maxlength="40" value="{USERNAME}" /></dd>
    <dt>{L_PASSWORD}:</dt>
    <dd><input class="login" type="password" name="password" size="25" maxlength="25" /></dd>
  <!-- BEGIN remember_me -->
    <dt>{L_REMEMBER_ME}:</dt>
    <dd><input type="checkbox" name="remember_me" value="1" /></dd>
  <!-- END remember_me -->
  </dl>
  <p><input type="submit" name="login" value="{L_LOGIN}"></p>
</form>
  <a href="{U_REGISTER}"><img src="template/cclear/theme/register.png" alt=""> {L_REGISTER}</a>
</div> <!--formbox-->

</div> <!-- content -->
