{combine_css id='standard_pages_css' path="themes/standard_pages/css/standard_pages.css" order=100}
{combine_css path="themes/default/vendor/fontello/css/gallery-icon.css" order=-10}
{combine_css path="admin/themes/default/fontello/css/fontello.css" order=-11}

<script>
  var selected_language = `{$language_options[$language_selection]}`;
  var url_logo_light = `{$ROOT_URL}themes/standard_pages/images/piwigo_logo.svg`;
  var url_logo_dark = `{$ROOT_URL}themes/standard_pages/images/piwigo_logo_dark.svg`;
</script>
{combine_script id='standard_pages_js' load='async' require='jquery' path='themes/standard_pages/js/standard_pages.js'}
{combine_script id='standard_profile_js' load='footer' require='jquery' path='themes/standard_pages/js/profile.js'}
{combine_script id='common' load='footer' require='jquery' path='admin/themes/default/js/common.js'}
{footer_script}
let user = {
  username: "{$USERNAME}",
  email: "{$EMAIL}",
  nb_image_page: $('input[name="nb_image_page"]').val(),
  theme: $('select[name="theme"]').val(),
  language: $('select[name="language"]').val(),
  recent_period: $('input[name="recent_period"]').val(),
  opt_album: $('#opt_album').is(':checked'),
  opt_comment: $('#opt_comment').is(':checked'),
  opt_hits: $('#opt_hits').is(':checked')
}

const canUpdatePreferences = {if $ALLOW_USER_CUSTOMIZATION}true{else}false{/if};
const canUpdatePassword = {if not $SPECIAL_USER}true{else}false{/if};
const standardSaveSelector = [];
const preferencesDefaultValues = {
nb_image_page: {$DEFAULT_USER_VALUES['nb_image_page']},
recent_period: {$DEFAULT_USER_VALUES['recent_period']},
opt_album: {$DEFAULT_USER_VALUES['expand']},
opt_comment: {$DEFAULT_USER_VALUES['show_nb_comments']},
opt_hits: {$DEFAULT_USER_VALUES['show_nb_hits']},
};
const selected_date = "{$API_SELECTED_EXPIRATION}";
const can_manage_api = {($API_CAN_MANAGE) ? "true" : "false"};

const str_copy_key_id = "{"ID copied."|translate|escape:javascript}";
const str_copy_key_secret = "{"Secret copied. Keep it in a safe place."|translate|escape:javascript}";
const str_cant_copy = "{"Impossible to copy automatically. Please copy manually."|translate|escape:javascript}";
const str_api_added = "{"The api key has been successfully created."|translate|escape:javascript}";
const str_revoked = "{"Revoked"|translate|escape:javascript}";
const str_show_expired = "{"Show expired keys"|translate|escape:javascript}";
const str_hide_expired = "{"Hide expired keys"|translate|escape:javascript}";
const str_handle_error = "{"An error has occured"|translate|escape:javascript}";
const str_expires_in = "{"Expires in"|translate|escape:javascript}";
const str_expires_on = "{"Expired on"|translate|escape:javascript}";
const str_revoke_key = "{'Do you really want to revoke the "%s" API key?'|translate|escape:javascript}";
const str_api_revoked = "{"API Key has been successfully revoked."|translate|escape:javascript}";
const str_api_edited = "{"API Key has been successfully edited."|translate|escape:javascript}";
const no_time_elapsed = "{"right now"|translate|escape:javascript}";
const str_must_not_empty = "{'must not be empty'|translate|escape:javascript}";
{/footer_script}

<container id="mode" class="light">
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

  <a href="{$U_HOME}" id="return-to-gallery"><i class="gallery-icon-left"></i> {'Return to the gallery'|translate}</a>

  {* ACCOUNT *}
  <section id="account-section" class="profile-section">
    <div class="title display-section" data-display="account-display">
      <div class="column-flex">
        <h1>{'Account'|translate}</h1>
        <p>{'Manage your account'|translate}</p>
      </div>
      <i class="gallery-icon-up-open display-btn close"></i>
    </div>
    <div class="form" id="account-display">
      <div class="column-flex first">
        <label>{'Username'|translate}</label>
        <div class="row-flex input-container username">
          <i class="gallery-icon-user"></i>
          <p id="username">{$USERNAME}</p>
          <input id="pwg_token" type="hidden" value="{$PWG_TOKEN}" />
        </div>
      </div>
      <div class="column-flex">
        <label for="email">{'Email address'|translate}</label>
        <div class="row-flex input-container">
          <i class="icon-mail-alt"></i>
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
      <div class="title display-section" data-display="preferences-display">
        <div class="column-flex">
          <h1>{'Preferences'|translate}</h1>
          <p>{'Choose how you want to see your gallery'|translate}</p>
        </div>
        <i class="gallery-icon-up-open display-btn close" ></i>
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
          <label>{'Theme'|translate}</label>
          <div class="row-flex input-container">
            <i class="icon-brush"></i>
            {html_options name=theme options=$template_options selected=$template_selection}
          </div>
          <p class="error-message"><i class="gallery-icon-attention-circled"></i> {'must not be empty'|translate}</p>
        </div>

        <div class="column-flex">
          <label>{'Language'|translate}</label>
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
      <div class="title display-section" data-display="password-display">
        <div class="column-flex">
          <h1>{'Password'|translate}</h1>
          <p>{'Change your password'|translate}</p>
        </div>
        <i class="gallery-icon-up-open display-btn close" ></i>
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

  {* API KEY *}
  <section id="apikey-section" class="profile-section">
    <div class="title display-section" data-display="apikey-display">
      <div class="column-flex">
        <h1>{'API Keys'|translate}</h1>
        <p>{'Create API Keys to secure your acount'|translate}</p>
      </div>
      <i class="gallery-icon-up-open display-btn close"></i>
    </div>

    <div class="form" id="apikey-display">
      <div class="api-cant-manage" id="cant_manage_api">
        <p>{'To manage your API keys, please log in with your username/password.'|translate|escape:html}</p>
      </div>

      <div class="new-apikey can-manage">
        <button class="btn btn-main" id="new_apikey">{'New API Key'|translate}</button>
      </div>
      <div class="api-list can-manage">
        <div class="api-list-head api-tab">
          <div aria-hidden="true"></div>
          <p>{'API Key name'|translate}</p>
          <p>{'Created at'|translate}</p>
          <p>{'Last use'|translate}</p>
          <p id="api_expires_in">{'Expires in'|translate}</p>
          <div aria-hidden="true"></div>
        </div>
        <div class="api-list-body" id="api_key_list">

          <div class="api-tab-line border-line template-api" id="api_line">
            <div class="api-icon-collapse">
              <i class="gallery-icon-up-open icon-collapse close" data-api=""></i>
            </div>
            <p class="api_name"></p>
            <p class="api_creation"></p>
            <p class="api_last_use"></p>
            <p class="api_expiration"></p>
            <div class="api-icon-action row-flex" data-api="" data-pkid="">
              <i class="icon-pencil edit-mode"></i>
              <i class="icon-trash-1 delete-mode"></i>
            </div>
          </div>
          <div class="api-tab-collapse border-line template-api" style="display: none;" id="api_collapse">
            <div aria-hidden="true"></div>
            <div class="keys">
              <div class="row-flex key">
                <i class="gallery-icon-hash"></i>
                <p class="api_key"></p>
                <i class="icon-clone" data-copy="" data-success=""></i>
                <p id="" class="api-copy api-hide success-message">{"ID copied."|translate|escape:html}</p>
              </div>
              <div class="row-flex key">
                <i class="icon-user-secret"></i>
                <p>{"The secret key can no longer be displayed."|translate}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="new-apikey">
          <button class="btn btn-link" id="show_expired_list" data-show="false">{'Show expired keys'|translate}</button>
        </div>
        <div class="api-list-body" id="api_key_list_expired">
        </div>

      </div>
    </div>

    {* API KEY MODAL *}
    <div class="bg-modal" id="api_modal">
      <div class="body-modal">
        <a class="icon-cancel close-modal" id="close_api_modal"></a>

        <div id="generate_keyapi">
          <div class="head-modal">
            <p class="title-modal">{'Generate API Key'|translate}</p>
            <p class="subtitle-modal">{'Create a new API key to secure your account.'|translate}</p>
          </div>

          <div>
            <div class="column-flex first">
              <label for="api_key_name">{'API Key name'|translate}</label>
              <div class="row-flex input-container">
                <i class="icon-key"></i>
                <input type="text" id="api_key_name" />
              </div>
              <p id="error_api_key_name" class="error-message"><i class="gallery-icon-attention-circled"></i>
                {'must not be empty'|translate}</p>
            </div>

            <div class="row-flex section-expiration">
              <div class="column-flex">
                <label>{'Duration'|translate}</label>
                <div class="row-flex input-container api-expiration">
                  <i class="gallery-icon-calendar"></i>
                  {html_options name=api_expiration options=$API_EXPIRATION}
                </div>
                <p id="error_api_key_date" class="error-message"><i class="gallery-icon-attention-circled"></i>
                  {'you must choose a date'|translate}</p>
              </div>

              <div class="column-flex" id="api_custom_date">
                <label for="api_expiration_date">{'Custom date'|translate}</label>
                <div class="row-flex input-container api-expiration">
                  <input type="date" id="api_expiration_date" name="api_expiration_custom" min="{$API_CURRENT_DATE}" />
                </div>
              </div>
            </div>

            <p class="api-mail-infos">{$API_EMAIL_INFOS}</p>

            <div class="save">
              <button class="btn btn-cancel" id="cancel_apikey">{'Cancel'|translate}</button>
              <button class="btn btn-main" id="save_apikey">{'Generate key'|translate}</button>
            </div>
          </div>
        </div>

        <div id="retrieves_keyapi">
          <div class="head-modal">
            <p class="title-modal">{'Generate API Key'|translate}</p>
            <p class="subtitle-modal">{'Save your ID and secret'|translate}</p>
            <p class="modal-secret">{'The secret will no longer be displayed. You must copy it to continue.'|translate}
            <p>
          </div>

          <div class="modal-input-keys">
            <p id="api_id_copy_success" class="api-copy api-hide success-message">
              {"ID copied."|translate|escape:html}</p>
          </div>
          <div class="input-modal input-modal-id row-flex">
            <i class="gallery-icon-hash"></i>
            <input type="text" id="api_id_key" />
            <i class="icon-clone" id="api_id_copy"></i>
          </div>

          <div class="modal-input-keys">
            <p id="api_key_copy_success" class="modal-input-key api-copy api-hide success-message">
              {"Secret copied. Keep it in a safe place."|translate|escape:html}</p>
          </div>
          <div class="input-modal input-modal-key row-flex">
            <i class="icon-user-secret"></i>
            <input type="text" id="api_secret_key" />
            <i class="icon-clone" id="api_secret_copy"></i>
          </div>

          <div class="save">
            <button class="btn btn-main" id="done_apikey" disabled>{'Done'|translate}</button>
          </div>
        </div>

      </div>
    </div>

    {* API KEY MODAL EDIT *}
    <div class="bg-modal" id="api_modal_edit">
      <div class="body-modal">
        <a class="icon-cancel close-modal" id="close_api_modal_edit"></a>

        <div>
          <div class="head-modal">
            <p class="title-modal">{'Edit API Key'|translate}</p>
          </div>

          <div class="column-flex first">
            <label for="api_key_edit">{'API Key name'|translate}</label>
            <div class="row-flex input-container">
              <i class="icon-key"></i>
              <input type="text" id="api_key_edit" />
            </div>
            <p id="error_api_key_edit" class="error-message"><i class="gallery-icon-attention-circled"></i>
              {'must not be empty'|translate}</p>
          </div>

          <div class="save">
            <button class="btn btn-main" id="save_api_edit">{'Save'|translate}</button>
          </div>
        </div>
      </div>
    </div>
    {* API KEY MODAL REVOKE *}
    <div class="bg-modal" id="api_modal_revoke">
      <div class="body-modal">
        <a class="icon-cancel close-modal" id="close_api_modal_revoke"></a>

        <div>
          <div class="head-modal">
            <p class="title-modal" id="api_modal_revoke_title"></p>
          </div>

          <div class="save">
            <button class="btn btn-cancel" id="cancel_api_revoke">{'Cancel'|translate}</button>
            <button class="btn btn-main btn-revoked" id="revoke_api_key">{'Revoke'|translate}</button>
          </div>
        </div>
      </div>
    </div>
  </section>

  {if isset($PLUGINS_PROFILE)}
    {foreach from=$PLUGINS_PROFILE item=plugin_block key=k_block}
      <section id="{$k_block}-section" class="profile-section">
        <div class="title display-section" data-display="{$k_block}-display">
          <div class="column-flex">
            <h1>{$plugin_block.name}</h1>
            <p>{$plugin_block.desc}</p>
          </div>
          <i class="gallery-icon-up-open display-btn close" ></i>
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
          {foreach from=$language_options key=code item=lang}
            <span id="lang={$code}" onclick="setCookie('lang','{$code}',30)">{$lang}</span>
          {/foreach}
        </span>
        <div id="selected-language-container">
          <i class="gallery-icon-left-chevron"></i><span
            id="selected-language">{$language_options[$language_selection]}</span>
        </div>
      </div>
    </section>
  {/if}
  {include file='toaster.tpl'}
</container>