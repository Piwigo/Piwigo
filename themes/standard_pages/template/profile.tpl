{combine_css id='standard_pages_css' path="themes/standard_pages/css/standard_pages.css" order=100}
{combine_css path="themes/default/vendor/fontello/css/gallery-icon.css" order=-10}
{combine_css path="admin/themes/default/fontello/css/fontello.css" order=-11}

<script>
  var selected_language = `{$language_options[$current_language]}`;
  var url_logo_light = `{$ROOT_URL}themes/standard_pages/images/piwigo_logo.svg`;
  var url_logo_dark = `{$ROOT_URL}themes/standard_pages/images/piwigo_logo_dark.svg`;
</script>
{combine_script id='standard_pages_js' load='async' require='jquery' path='themes/standard_pages/js/standard_pages.js'}
{combine_script id='standard_profile_js' load='async' require='jquery' path='themes/standard_pages/js/profile.js'}
{footer_script}
const standardSaveSelector = [];
const preferencesDefaultValues = {
  nb_image_page: {$DEFAULT_USER_VALUES['nb_image_page']},
  recent_period: {$DEFAULT_USER_VALUES['recent_period']},
  opt_album: {$DEFAULT_USER_VALUES['expand']},
  opt_comment: {$DEFAULT_USER_VALUES['show_nb_comments']},
  opt_hits: {$DEFAULT_USER_VALUES['show_nb_hits']},
};
{/footer_script}

<container id="mode" class="light">
  <section id="header-options">
    <div>
      <i class="gallery-icon-moon toggle-mode" id="toggle_mode_light" onclick="toggle_mode('dark')"></i>
      <i class="gallery-icon-sun toggle-mode" id="toggle_mode_dark" onclick="toggle_mode('light')"></i>
    </div>
    <div>
      <a href="{$HELP_LINK}" target="_blank">{'Help'|translate}</a>
      {include file='toaster.tpl'}
    </div>
  </section>

  <section id="logo-section">
    <img id="piwigo-logo" src="{$ROOT_URL}themes/standard_pages/images/piwigo_logo.svg">
  </section>

  <a href="{$U_HOME}" id="return-to-gallery"><i class="gallery-icon-left"></i> {'Return to the gallery'|translate}</a>

  {* ACCOUNT *}
  <section id="account-section" class="profile-section">
    <div class="title">
      <div class="column-flex">
        <h1>{'Account'|translate}</h1>
        <p>{'Manage your account'|translate}</p>
      </div>
      <i class="gallery-icon-up-open display-btn close" data-display="account-display"></i>
    </div>
    <div class="form" id="account-display">
      <div class="column-flex first">
        <label for="username">{'Username'|translate}</label>
        <div class="row-flex input-container username">
          <i class="gallery-icon-user"></i>
          <p>{$USERNAME}</p>
          <input id="pwg_token" type="hidden" value="{$PWG_TOKEN}" />
        </div>
      </div>
      <div class="column-flex">
        <label for="mail_address">{'Email address'|translate}</label>
        <div class="row-flex input-container">
          <i class="gallery-icon-user"></i>
          <input type="email" name="mail_address" id="email" value="{$EMAIL}" />
        </div>
        <p id="email_error" class="error-message"><i class="gallery-icon-attention-circled"></i>
          {'must not be empty'|translate}</p>
      </div>
      <div class="save">
        <button class="btn btn-main" id="save_account">{'Submit'|translate}</button>
      </div>
    </div>
  </section>

  {* PREFERENCES *}
  {if $ALLOW_USER_CUSTOMIZATION}
    <section id="preferences-section" class="profile-section">
      <div class="title">
        <div class="column-flex">
          <h1>{'Preferences'|translate}</h1>
          <p>{'Choose how you want to see your gallery'|translate}</p>
        </div>
        <i class="gallery-icon-up-open display-btn close" data-display="preferences-display"></i>
      </div>
      <div class="form" id="preferences-display">
        <div class="column-flex first">
          <label for="nb_image_page">{'Number of photos per page'|translate}</label>
          <div class="row-flex input-container">
            <i class="icon-picture"></i>
            <input type="number" size="4" maxlength="3" name="nb_image_page" id="nb_image_page"
              value="{$NB_IMAGE_PAGE}" />
          </div>
          <p id="error_nb_image" class="error-message"><i class="gallery-icon-attention-circled"></i>
            {'must not be empty'|translate}</p>
        </div>

        <div class="column-flex">
          <label for="theme">{'Theme'|translate}</label>
          <div class="row-flex input-container">
            <i class="icon-brush"></i>
            {html_options name=theme options=$template_options selected=$template_selection}
          </div>
          <p class="error-message"><i class="gallery-icon-attention-circled"></i> {'must not be empty'|translate}</p>
        </div>

        <div class="column-flex">
          <label for="language">{'Language'|translate}</label>
          <div class="row-flex input-container">
            <i class="icon-language"></i>
            {html_options name=language options=$language_options selected=$language_selection}
          </div>
          <p class="error-message"><i class="gallery-icon-attention-circled"></i> {'must not be empty'|translate}</p>
        </div>

        <div class="column-flex">
          <label for="recent_period">{'Recent period'|translate}</label>
          <div class="row-flex input-container">
            <i class="icon-calendar"></i>
            <input type="number" size="3" maxlength="2" name="recent_period" id="recent_period"
              value="{$RECENT_PERIOD}" />
          </div>
          <p id="error_period" class="error-message"><i class="gallery-icon-attention-circled"></i>
            {'must not be empty'|translate}</p>
        </div>

        {* OPTIONS *}
        <label class="options-title">{'Options'|translate}</label>
        <div class="column-flex input-container preferences-options">
          <div class="row-flex option">
            <label class="switch">
              <input type="checkbox" id="opt_album" {if "true" === $EXPAND}checked{/if}>
              <span class="slider round"></span>
            </label>
            <p>{'Expand all albums'|@translate}</p>
          </div>

          {if $ACTIVATE_COMMENTS}
            <div class="row-flex option">
              <label class="switch">
                <input type="checkbox" id="opt_comment" {if "true" === $NB_COMMENTS}checked{/if}>
                <span class="slider round"></span>
              </label>
              <p>{'Show number of comments'|@translate}</p>
            </div>
          {/if}

          <div class="row-flex option">
            <label class="switch">
              <input type="checkbox" id="opt_hits" {if "true" === $NB_HITS}checked{/if}>
              <span class="slider round"></span>
            </label>
            <p>{'Show number of hits'|@translate}</p>
          </div>
        </div>

        <div class="reset">
          <button class="btn btn-main btn-secondary"
            id="default_preferences">{'Reset to default values'|translate}</button>
          <div class="save">
            <button class="btn btn-main btn-secondary" id="reset_preferences">{'Reset'|translate}</button>
            <button class="btn btn-main" id="save_preferences">{'Submit'|translate}</button>
          </div>
        </div>
      </div>
    </section>
  {/if}

  {* PASSWORD *}
  {if not $SPECIAL_USER}
    <section id="password-section" class="profile-section">
      <div class="title">
        <div class="column-flex">
          <h1>{'Password'|translate}</h1>
          <p>{'Change your password'|translate}</p>
        </div>
        <i class="gallery-icon-up-open display-btn close" data-display="password-display"></i>
      </div>
      <div class="form" id="password-display">
        <div class="column-flex">
          <label for="password">{'Password'|translate}</label>
          <div class="row-flex input-container">
            <i class="gallery-icon-lock"></i>
            <input type="password" class="" name="password" id="password" size="25" />
            <i class="gallery-icon-eye togglePassword"></i>
          </div>
          <p class="error-message"><i class="gallery-icon-attention-circled"></i> {'must not be empty'|translate}</p>
        </div>

        <div class="column-flex">
          <label for="password_new">{'New password'|translate}</label>
          <div class="row-flex input-container">
            <i class="gallery-icon-lock"></i>
            <input type="password" class="" name="new-password" id="password_new" size="25" />
            <i class="gallery-icon-eye togglePassword"></i>
          </div>
          <p class="error-message"><i class="gallery-icon-attention-circled"></i> {'must not be empty'|translate}</p>
        </div>

        <div class="column-flex">
          <label for="password_conf">{'Confirm my new password'|translate}</label>
          <div class="row-flex input-container">
            <i class="gallery-icon-lock"></i>
            <input type="password" class="" name="password_conf" id="password_conf" size="25" />
            <i class="gallery-icon-eye togglePassword"></i>
          </div>
          <p class="error-message"><i class="gallery-icon-attention-circled"></i> {'must not be empty'|translate}</p>
        </div>

        <div class="save">
          <button class="btn btn-main" id="save_password">{'Submit'|translate}</button>
        </div>
      </div>
    </section>
  {/if}

  {if isset($PLUGINS_PROFILE)}
    {foreach from=$PLUGINS_PROFILE item=plugin_block key=k_block}
      <section id="{$k_block}-section" class="profile-section">
        <div class="title">
          <div class="column-flex">
            <h1>{$plugin_block.name}</h1>
            <p>{$plugin_block.desc}</p>
          </div>
          <i class="gallery-icon-up-open display-btn close" data-display="{$k_block}-display"></i>
        </div>
        <div class="form plugins" id="{$k_block}-display">
          {include file=$plugin_block.template}
          {if $plugin_block.standard_show_save}
          <div class="save">
            <button class="btn btn-main" id="save_{$k_block}">{'Submit'|translate}</button>
          </div>
          {footer_script}
            standardSaveSelector.push('#save_{$k_block}');
          {/footer_script}
          {/if}
        </div>
      </section>
    {/foreach}
  {/if}

  {if count($language_options) > 1}
    <section id="language-switch">
      <div id="lang-select">
        <span id="other-languages">
          {foreach from=$language_options key=$code item=$lang}
            <span id="lang={$code}" onclick="setCookie('lang','{$code}',30)">{$lang}</span>
          {/foreach}
        </span>
        <div id="selected-language-container">
          <i class="gallery-icon-left-chevron"></i><span
            id="selected-language">{$language_options[$current_language]}</span>
        </div>
      </div>
    </section>
  {/if}
</container>