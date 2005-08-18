<h2>{L_TITLE}</h2>
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
  <p><input type="submit" name="login" value="{L_LOGIN}" class="bouton" /></p>
</form>
  <!-- BEGIN free_access -->
  <p><a href="./category.php">[ {L_GUEST} ]</a></p>
  <a href="register.php"><img src="template/clear/theme/register.gif" style="border:0;" alt=""/>&nbsp;{L_REGISTER}</a>&nbsp;&nbsp;-&nbsp;&nbsp;
  <!-- END free_access -->
  <a href="mailto:{MAIL_ADMIN}?subject=[PhpWebGallery] {L_FORGET}"><img src="template/clear/theme/lost.gif" style="border:0;" alt=""/>&nbsp;{L_FORGET}</a>
</div> <!--formbox-->
