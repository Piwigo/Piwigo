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
{if isset($errors['login_page_error'])}
      <div class="error_block_container">
  {foreach from=$errors['login_page_error'] item=error}
        <div class="error_block"> 
          <i class="gallery-icon-cancel"></i>
          <p>{$error}</p>
        </div>
  {/foreach}
      </div>
{/if}
    </div>
  </section>

  <section id="logo-section">
    <img id="piwigo-logo" src="{$ROOT_URL}themes/standard_pages/images/piwigo_logo.svg">
  </section>

  <section id="login-form">
    <div class="">
      <h1 class="">
        {'Login'|translate}
      </h1>
        
      <form class="properties" action="{$F_LOGIN_ACTION}" method="post" name="login_form">

        <div class="column-flex">
          <label for="username">{'Username'|translate}</label>
          <div class="row-flex input-container">
            <i class="gallery-icon-user"></i>
            <input type="text" class="" type="text" name="username" id="username" size="25" autofocus data-required="true">
          </div>
          <p class="error-message"><i class="gallery-icon-attention-circled"></i> {'must not be empty'|translate}</p>
        </div>

        <div class="column-flex">
          <label for="password">{'Password'|translate}</label>
          <div class="row-flex input-container">
            <i class="gallery-icon-lock"></i>
            <input type="password" class="" name="password" id="password" size="25">
            <i class="gallery-icon-eye togglePassword"></i>
          </div>
          <p class="error-message"><i class="gallery-icon-attention-circled"></i> {'must not be empty'|translate}</p>
        </div>

{if $authorize_remembering }
        <div class="column-flex">
          <div class="row-flex remember-me-container">
          <label for="remember_me">
            <input tabindex="3" type="checkbox" name="remember_me" id="remember_me" value="1">
            <span class="gallery-icon-checkmark"></span>
            {'Auto login'|@translate}</label>
          </div>
        </div>
{/if}
            
        <div class="column-flex">
          <input type="hidden" name="redirect" value="{$U_REDIRECT|@urlencode}">
          <input tabindex="4" type="submit" name="login" value="{'Login'|translate}" class="btn btn-main ">
{if isset($errors['login_form_error'])}
          <p class="error-message" style="display:block;bottom:-20px;"><i class="gallery-icon-attention-circled"></i> {$errors['login_form_error']}</p>
{/if}
        </div>
      </form>

      <div class="secondary-links">
        
{if isset($U_REGISTER)}
        <p>{'Don\'t have an account yet ?'|translate} <a href="register.php" title="{'Register'|translate}">{'Create an account'|translate}</a></p>
        <span id="separator"></span>
{/if}
        <a href="password.php" title="{'Forgot your password?'|translate}">{'Forgot your password?'|translate}</a>

      </div>
            
    </div>
  </section>

  <a href="index.php" id="return-to-gallery"><i class="gallery-icon-left"></i> {'Return to the gallery'|translate}</a>

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