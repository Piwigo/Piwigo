{combine_css id='standard_pages_css' path="themes/standard_pages/css/standard_pages.css" order=100}
{combine_css path="themes/default/vendor/fontello/css/gallery-icon.css" order=-10}

<script>
  var selected_language = `{$language_options[$current_language]}`;
  var url_logo_light = `{$ROOT_URL}themes/standard_pages/images/piwigo_logo.svg`;
  var url_logo_dark = `{$ROOT_URL}themes/standard_pages/images/piwigo_logo_dark.svg`;
</script>
{combine_script id='standard_pages_js' load='async' require='jquery' path='themes/standard_pages/js/standard_pages.js'}

<container id="mode" class="light">

  <section id="header-options">
    <div>
      <i class="gallery-icon-moon toggle-mode" id="toggle_mode_light" onclick="toggle_mode('dark')"></i>
      <i class="gallery-icon-sun toggle-mode" id="toggle_mode_dark" onclick="toggle_mode('light')"></i>
    </div>
    <div>
      <a href="{$HELP_LINK}" target="_blank">{'Help'|translate}</a>
{if isset($errors['password_page_error'])}
      <div class="error_block_container">
  {foreach from=$errors['password_page_error'] item=error}
        <div class="error_block"> 
          <i class="gallery-icon-cancel"></i>
          <p>{$error}</p>
        </div>
  {/foreach}
      </div>
{/if}
  </section>

  <section id="logo-section">
    <img id="piwigo-logo" src="{$ROOT_URL}themes/standard_pages/images/piwigo_logo.svg">
  </section>


  <section id="password-form">
    <div class="">

{if $action eq 'lost' or $action eq 'reset' or $action eq 'lost_code'}
<h1 class="">{if !isset($is_first_login)}{'Forgot your password?'|translate}{else}{'Welcome !'|translate}<br>{'It\'s your first login !'|translate}{/if}</h1>
    <form id="lostPassword" class="properties" action="{$form_action}?action={$action}{if isset($key)}&amp;key={$key}{/if}" method="post">

      <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
  {if $action eq 'lost'}

      <p class="form-instructions">{'Please enter your username or email address.'|@translate} {'You will receive a link to create a new password via email.'|@translate}</p>

      <div class="column-flex">
        <label for="username">{'Username or email'|@translate}</label>
        <div class="row-flex input-container">
          <i class="gallery-icon-user-2"></i>
          <input type="text" id="username_or_email" name="username_or_email" size="100" maxlength="100"{if isset($username_or_email)} value="{$username_or_email}"{/if} autofocus data-required="true">
        </div>
        <p class="error-message"><i class="gallery-icon-attention-circled"></i> {'must not be empty'|translate}</p>
      </div>

      <div class="column-flex">
        <input tabindex="4" type="submit" name="submit" value="{'Change my password'|@translate}" class="btn btn-main ">
    {if isset($errors['password_form_error'])}
        <p class="error-message" style="display:block;bottom:-20px;"><i class="gallery-icon-attention-circled"></i> {$errors['password_form_error']}</p>
    {/if}
      </div>

 {elseif $action eq 'reset'}
    <p class="intro-paragraph">
    {if !isset($is_first_login)}
      {'Hello <em>%s</em>, enter your new password below.'|@translate:$username}
    {else}
      {'Let\'s set your password below.'|translate}
    {/if}
    </p>

    {if isset($is_first_login)}
    <div class="column-flex">
      <label for="username">{'Username'|translate}</label>
      <div class="row-flex input-container">
        <i class="gallery-icon-user-2"></i>
        <input type="text" class="" type="text" name="username" id="username" size="25" value={$username} disabled  >
      </div>
    </div>
    {/if}
      
    <div class="column-flex">
      <label for="password">{if isset($is_first_login)}{'Password'|translate}{else}{'New password'|translate}{/if}</label>
      <div class="row-flex input-container">
        <i class="gallery-icon-lock"></i>
        <input type="password" class="" name="use_new_pwd" id="use_new_pwd" size="25" autofocus >
        <i class="gallery-icon-eye togglePassword"></i>
      </div>
      <p class="error-message"><i class="gallery-icon-attention-circled"></i> {'must not be empty'|translate}</p>
    </div>

    <div class="column-flex">
      <label for="passwordConf">{if isset($is_first_login)}{'Confirm Password'|translate}{else}{'Confirm new password'|translate}{/if}</label>
      <div class="row-flex input-container">
        <i class="gallery-icon-lock"></i>
        <input type="password" name="passwordConf" id="passwordConf" >
        <i class="gallery-icon-eye togglePassword"></i>
      </div>
      <p class="error-message"><i class="gallery-icon-attention-circled"></i> {'must not be empty'|translate}</p>
    </div>

    <div class="column-flex">
      <input tabindex="4" type="submit" name="submit" {if !isset($is_first_login)}value="{'Confirm my new password'|@translate}"{else}value="{'Set my password'|@translate}"{/if} class="btn btn-main ">
    </div>

  {elseif $action eq 'lost_code'}
    <span class="success-message"><i class="gallery-icon-ok-circled"></i>{'An email has been sent with a verification code'|translate}</span>
    <div class="column-flex">
      <label for="user_code">{'Verification code'|@translate}</label>
      <div class="row-flex input-container">
        <i class="gallery-icon-user-2"></i>
        <input type="text" id="user_code" name="user_code" size="100" maxlength="100" autofocus>
      </div>
      <p class="error-message"><i class="gallery-icon-attention-circled"></i> {'must not be empty'|translate}</p>
    </div>

    <div class="column-flex">
      <input tabindex="4" type="submit" name="submit" value="{'Verify'|@translate}" class="btn btn-main">
      {if isset($errors['password_form_error'])}
      <p class="error-message" style="display:block;bottom:-20px;"><i class="gallery-icon-attention-circled"></i> {$errors['password_form_error']}</p>
      {/if}
      <p style="font-size: 12px;">{"If you do not receive the email, please contact your webmaster."|translate}</p>
    </div>
  {/if}
      </form>

      <div class="secondary-links">
        <p>{'Return to <a href="identification.php" title="Sign in">Sign in</a>'|translate}</p>
      </div>
{else}
    {if $action eq 'lost_end'}
      <span class="success-message"><i class="gallery-icon-ok-circled"></i>{'An email has been sent with a link to reset your password'|translate}</span>
      <p>{'Check your inbox.'|translate}</p>
    {elseif $action eq 'reset_end'}
      {if isset($is_first_login)}{assign var="string" value=""}{else}{assign var="string" value="reset"}{/if}
      <span class="success-message"><i class="gallery-icon-ok-circled"></i>
        {if isset($is_first_login)}
          {'Your password was successfully set'|translate}
        {else}
          {'Your password was successfully reset'|translate}
        {/if}
      </span>
      <a href="identification.php" title="{'Login'|translate}" class="btn-main">{'Login'|translate}</a>
    {else}
      <p>{'An error has occured please got back to <a href="identification.php" title="Sign in">Sign in</a> or <a href="register.php">Register</a>'|translate}</p>
    {/if}
{/if}
            
    </div>
  </section>

  <a href="index.php" id="return-to-gallery"><i class="gallery-icon-left-chevron"></i> {'Return to the gallery'|translate}</a>

{if count($language_options) > 1}
  <section id="language-switch">
    <div id="lang-select">
      <span id="other-languages">
  {foreach from=$language_options key=code item=lang}
          <span id="lang={$code}" onclick="setCookie('lang','{$code}',30)">{$lang}</span>
  {/foreach}
      </span>
      <div id="selected-language-container">
        <i class="gallery-icon-left-chevron"></i><span id="selected-language">{$language_options[$current_language]}</span>
      </div>

    </div>
  </section>
{/if}
  

</container>
