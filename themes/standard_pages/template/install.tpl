{combine_css id='standard_pages_css' path="themes/standard_pages/skins/{$STD_PGS_SELECTED_SKIN}.css" order=100}
{combine_css path="themes/default/vendor/fontello/css/gallery-icon.css" order=-10}
{combine_css path="admin/themes/default/fontello/css/fontello.css" order=-11}

<script>
  var selected_language = `{$language_options[$language_selection]}`;
  var url_logo_light = `{$ROOT_URL}themes/standard_pages/images/piwigo_logo.svg`;
  var url_logo_dark = `{$ROOT_URL}themes/standard_pages/images/piwigo_logo_dark.svg`;
  var cookie_path = '{$COOKIE_PATH|@escape:'javascript'}';
</script>
{combine_script id='standard_pages_js' load='async' require='jquery' path='themes/standard_pages/js/standard_pages.js'}

<container id="mode" class="light{if $IS_FIRST_DISPLAY} install-animate{/if}">

  <section id="header-options">
    <div>
      <i class="gallery-icon-moon toggle-mode" id="toggle_mode_light" onclick="toggle_mode('dark')"></i>
      <i class="gallery-icon-sun toggle-mode" id="toggle_mode_dark" onclick="toggle_mode('light')"></i>
    </div>
    <div>
      <a href="{$HELP_LINK}" target="_blank">{'Help'|translate}</a>
    </div>
  </section>

  <section id="logo-section">
    <img id="piwigo-logo" src="{$ROOT_URL}themes/standard_pages/images/piwigo_logo.svg">
  </section>

  <section class="install-section">
    <h1>{'Version'|translate} {$RELEASE} - {'Installation'|translate}</h1>
{if isset($errors.config)}
    <div class="error_block install-error">
      <i class="gallery-icon-cancel"></i>
      <p>{$errors.config|escape:'html'}</p>
    </div>
{/if}
  </section>

  <form id="install-form" class="properties" method="post" action="{$F_ACTION}" name="install_form">

    <section class="install-section">
      <h1>{'Basic configuration'|translate}</h1>

      <div class="column-flex">
        <label for="language">{'Default gallery language'|translate}</label>
        <div class="row-flex input-container">
          <i class="icon-language"></i>
          <select name="language" id="language" onchange="document.location = 'install.php?language='+this.value;">
            {html_options options=$language_options selected=$language_selection}
          </select>
        </div>
      </div>

      <div class="column-flex">
        <label>{'Technical requirements'|translate}</label>
        <ul class="install-requirements">
{foreach from=$requirements item=requirement}
          <li class="requirement-{$requirement.status}">
            <i class="{if 'ok' == $requirement.status}gallery-icon-ok-circled{elseif 'warn' == $requirement.status}gallery-icon-attention-circled{else}gallery-icon-cancel{/if}"></i>
            <span class="requirement-label">{$requirement.label}</span>
    {if !empty($requirement.detail)}
            <span class="requirement-detail">{$requirement.detail}</span>
    {/if}
          </li>
{/foreach}
        </ul>
      </div>
    </section>

    <section class="install-section">
      <h1>{'Database configuration'|translate}</h1>

{if isset($errors.database)}
      <div class="error_block install-error">
        <i class="gallery-icon-cancel"></i>
        <p>{$errors.database|escape:'html'}</p>
      </div>
{/if}

      <div class="column-flex">
        <label for="dbhost">{'Host'|translate}<span class="field-description">{'localhost or other, supplied by your host provider'|translate}</span></label>
        <div class="row-flex input-container">
          <i class="icon-home"></i>
          <input type="text" name="dbhost" id="dbhost" value="{$F_DB_HOST|escape:'html'}" data-required="true">
        </div>
        <p class="error-message"{if isset($errors.dbhost)} style="display:block"{/if}><i class="gallery-icon-attention-circled"></i> {if isset($errors.dbhost)}{$errors.dbhost|escape:'html'}{else}{'must not be empty'|translate}{/if}</p>
      </div>

      <div class="column-flex">
        <label for="dbuser">{'User'|translate}<span class="field-description">{'user login given by your host provider'|translate}</span></label>
        <div class="row-flex input-container">
          <i class="gallery-icon-user"></i>
          <input type="text" name="dbuser" id="dbuser" value="{$F_DB_USER|escape:'html'}" data-required="true">
        </div>
        <p class="error-message"{if isset($errors.dbuser)} style="display:block"{/if}><i class="gallery-icon-attention-circled"></i> {if isset($errors.dbuser)}{$errors.dbuser|escape:'html'}{else}{'must not be empty'|translate}{/if}</p>
      </div>

      <div class="column-flex">
        <label for="dbpasswd">{'Password'|translate}<span class="field-description">{'user password given by your host provider'|translate}</span></label>
        <div class="row-flex input-container">
          <i class="gallery-icon-lock"></i>
          <input type="password" name="dbpasswd" id="dbpasswd" value="">
          <i class="gallery-icon-eye togglePassword"></i>
        </div>
{if isset($errors.dbpasswd)}
        <p class="error-message" style="display:block"><i class="gallery-icon-attention-circled"></i> {$errors.dbpasswd|escape:'html'}</p>
{/if}
      </div>

      <div class="column-flex">
        <label for="dbname">{'Database name'|translate}<span class="field-description">{'also given by your host provider'|translate}</span></label>
        <div class="row-flex input-container">
          <i class="icon-database"></i>
          <input type="text" name="dbname" id="dbname" value="{$F_DB_NAME|escape:'html'}" data-required="true">
        </div>
        <p class="error-message"{if isset($errors.dbname)} style="display:block"{/if}><i class="gallery-icon-attention-circled"></i> {if isset($errors.dbname)}{$errors.dbname|escape:'html'}{else}{'must not be empty'|translate}{/if}</p>
      </div>

      <div class="column-flex">
        <label for="prefix">{'Database table prefix'|translate}<span class="field-description">{'database tables names will be prefixed with it (enables you to manage better your tables)'|translate}</span></label>
        <div class="row-flex input-container">
          <i class="icon-database"></i>
          <input type="text" name="prefix" id="prefix" value="{$F_DB_PREFIX|escape:'html'}">
        </div>
{if isset($errors.prefix)}
        <p class="error-message" style="display:block"><i class="gallery-icon-attention-circled"></i> {$errors.prefix|escape:'html'}</p>
{/if}
      </div>
    </section>

    <section class="install-section">
      <h1>{'Admin configuration'|translate}</h1>

      <div class="column-flex">
        <label for="admin_name">{'Username'|translate}<span class="field-description">{'It will be shown to the visitors. It is necessary for website administration'|translate}</span></label>
        <div class="row-flex input-container">
          <i class="gallery-icon-user"></i>
          <input type="text" name="admin_name" id="admin_name" value="{$F_ADMIN|escape:'html'}" data-required="true">
        </div>
        <p class="error-message"{if isset($errors.admin_name)} style="display:block"{/if}><i class="gallery-icon-attention-circled"></i> {if isset($errors.admin_name)}{$errors.admin_name|escape:'html'}{else}{'must not be empty'|translate}{/if}</p>
      </div>

      <div class="column-flex">
        <label for="admin_pass1">{'Password'|translate}<span class="field-description">{'Keep it confidential, it enables you to access administration panel'|translate}</span></label>
        <div class="row-flex input-container">
          <i class="gallery-icon-lock"></i>
          <input type="password" name="admin_pass1" id="admin_pass1" value="" data-required="true">
          <i class="gallery-icon-eye togglePassword"></i>
        </div>
        <p class="error-message"{if isset($errors.admin_pass1)} style="display:block"{/if}><i class="gallery-icon-attention-circled"></i> {if isset($errors.admin_pass1)}{$errors.admin_pass1|escape:'html'}{else}{'must not be empty'|translate}{/if}</p>
      </div>

      <div class="column-flex">
        <label for="admin_pass2">{'Confirm Password'|translate}</label>
        <div class="row-flex input-container">
          <i class="gallery-icon-lock"></i>
          <input type="password" name="admin_pass2" id="admin_pass2" value="" data-required="true">
          <i class="gallery-icon-eye togglePassword"></i>
        </div>
        <p class="error-message"{if isset($errors.admin_pass2)} style="display:block"{/if}><i class="gallery-icon-attention-circled"></i> {if isset($errors.admin_pass2)}{$errors.admin_pass2|escape:'html'}{else}{'must not be empty'|translate}{/if}</p>
      </div>

      <div class="column-flex">
        <label for="admin_mail">{'Email address'|translate}<span class="field-description">{'Visitors will be able to contact site administrator with this mail'|translate}</span></label>
        <div class="row-flex input-container">
          <i class="icon-mail-alt"></i>
          <input type="email" name="admin_mail" id="admin_mail" value="{$F_ADMIN_EMAIL|escape:'html'}" data-required="true">
        </div>
        <p class="error-message"{if isset($errors.admin_mail)} style="display:block"{/if}><i class="gallery-icon-attention-circled"></i> {if isset($errors.admin_mail)}{$errors.admin_mail|escape:'html'}{else}{'must not be empty'|translate}{/if}</p>
      </div>

      <div class="install-options">
        <label>{'Options'|translate}</label>
        <div class="row-flex option">
          <label class="switch">
            <input type="checkbox" name="newsletter_subscribe" id="newsletter_subscribe"{if $F_NEWSLETTER_SUBSCRIBE} checked{/if}>
            <span class="slider round"></span>
          </label>
          <label for="newsletter_subscribe" class="option-label">{'Subscribe %s to Piwigo Announcements Newsletter'|translate:$EMAIL}</label>
          <i class="gallery-icon-help-circled install-tooltip" data-tooltip="{'Keep in touch with Piwigo project, subscribe to Piwigo Announcement Newsletter. You will receive emails when a new release is available (sometimes including a security bug fix, it\'s important to know and upgrade) and when major events happen to the project. Only a few emails a year.'|translate|escape:'html'}"></i>
        </div>
        <div class="row-flex option">
          <label class="switch">
            <input type="checkbox" name="send_credentials_by_mail" id="send_credentials_by_mail"{if $F_SEND_CREDENTIALS} checked{/if}>
            <span class="slider round"></span>
          </label>
          <label for="send_credentials_by_mail" class="option-label">{'Send my connection settings by email'|translate}</label>
        </div>
      </div>

      <div class="install-submit">
        <button type="submit" name="install" value="1" class="btn btn-main"{if !$CAN_INSTALL} disabled{/if}>
          <span>{'Start Install'|translate}</span>
          <i class="gallery-icon-spin6 install-submit-loader"></i>
        </button>
      </div>
    </section>

  </form>

  <p class="install-help">{$L_INSTALL_HELP|replace:'<a ':'<a target="_blank" '}</p>

</container>

{footer_script require='jquery'}
jQuery('#admin_mail').on('input', function() {
  jQuery('.adminEmail').text(jQuery(this).val());
});

jQuery('#install-form').on('submit', function(e) {
  const button = jQuery(this).find('button[name=install]');
  setTimeout(function() {
    if (!e.isDefaultPrevented() && !e.originalEvent.defaultPrevented) {
      button.addClass('loading');
    }
  }, 0);
});
{/footer_script}
