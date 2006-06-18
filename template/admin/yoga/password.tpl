<!-- $Id: password.tpl 1129 2006-04-05 21:01:05Z plg $ -->
<div id="content">

  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{U_HOME}" title="{lang:Go through the gallery as a visitor}"><img src="{themeconf:icon_dir}/home.png" class="button" alt="{lang:home}"/></a></li>
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
        <span class="property">
          <label for="mail_address">{lang:Email address}</label>
        </span>
        <input class="login" type="text" name="mail_address" id="mail_address" size="40" maxlength="40" onfocus="this.className='focus';" onblur="this.className='nofocus';">
      </li>

      <li>
        <span class="property">
          <label for="no_mail_address">{lang:No email address}</label>
        </span>
        <input type="checkbox" id="no_mail_address" name="no_mail_address" value="1">
      </li>
    </ul>
  </fieldset>

  <p><input type="submit" name="submit" value="{lang:Send new password}"></p>
</form>

</div> <!-- content -->
