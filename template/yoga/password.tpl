<!-- $Id$ -->
<div id="content">

  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{U_HOME}" title="{lang:Go through the gallery as a visitor}"><img src="./template/yoga/theme/home.png" class="button" alt="{lang:home}"/></a></li>
    </ul>
    <h2>{lang:Password forgotten}</h2>
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

  <!-- BEGIN infos -->
  <div class="infos">
    <ul>
      <!-- BEGIN info -->
      <li>{infos.info.INFO}</li>
      <!-- END info -->
    </ul>
  </div>
  <!-- END infos -->

<form action="{F_ACTION}" method="post" class="properties">
  <fieldset>
    <legend>{lang:Retrieve password}</legend>

    <ul>
      <li> 
        <label for="mail_address">{lang:Email address}</label>
        <input class="login" type="text" name="mail_address" id="mail_address" size="40" maxlength="40">
      </li>

      <li>
        <label>{lang:No email address}</label>
        <input type="checkbox" name="no_mail_address" value="1">
      </li>
    </ul>
  </fieldset>

  <p><input type="submit" name="submit" value="{lang:Send new password}"></p>
</form>

</div> <!-- content -->
