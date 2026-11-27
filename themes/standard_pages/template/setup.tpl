{combine_css id='standard_pages_css' path="themes/standard_pages/skins/{$STD_PGS_SELECTED_SKIN}.css" order=100}
{combine_css path="themes/default/vendor/fontello/css/gallery-icon.css" order=-10}
{combine_css path="admin/themes/default/fontello/css/fontello.css" order=-11}

<script>
  var selected_language = '';
  var url_logo_light = `{$ROOT_URL}themes/standard_pages/images/piwigo_logo.svg`;
  var url_logo_dark = `{$ROOT_URL}themes/standard_pages/images/piwigo_logo_dark.svg`;
  var cookie_path = '{$COOKIE_PATH|@escape:'javascript'}';
</script>
{combine_script id='standard_pages_js' load='async' require='jquery' path='themes/standard_pages/js/standard_pages.js'}
{combine_script id='setup_js' load='footer' require='jquery' path='themes/standard_pages/js/setup.js'}

{footer_script}
const setup_str_error = "{'an error happened'|translate|escape:'javascript'}";
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

{if $IS_COMPLETE}
  {include file='setup_complete.tpl'}
{else}
  <section class="install-section">
    <h1>{'Version'|translate} {$RELEASE} - {'Configuration'|translate}</h1>
  </section>

  <form id="setup-form" method="post" action="{$F_ACTION}">
    <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">

{if 'extensions' == $STEP_ID}
    <section class="install-section">
      <h1>{'Themes'|translate}</h1>

  {if !$THEMES_WRITABLE}
      <div class="error_block install-error">
        <i class="gallery-icon-cancel"></i>
        <p>{'Add write access to the "%s" directory'|translate:'themes'}</p>
      </div>
  {/if}

      <div class="setup-cards setup-cards-themes">
  {foreach from=$setup_themes item=card}
    {assign var=is_locked value=('remote' == $card.state && (!$THEMES_WRITABLE || !$CAN_DOWNLOAD))}
        <label class="setup-card setup-card-theme{if $is_locked} setup-card-locked{/if}" data-type="themes" data-id="{$card.id}" data-state="{$card.state}">
          <input type="radio" name="setup_theme" value="{$card.id}"{if 'default' == $card.state} checked{/if}{if $is_locked} disabled{/if}>
          <span class="setup-card-select round"></span>
          <span class="setup-card-screenshot">
    {if !empty($card.screenshot)}
            <img src="{$card.screenshot|escape:'html'}" alt="" loading="lazy">
    {else}
            <i class="icon-brush"></i>
    {/if}
          </span>
          <span class="setup-card-info">
            <span class="setup-card-name">{$card.name|escape:'html'}</span>
            <span class="setup-card-state">{if 'default' == $card.state || 'active' == $card.state}<i class="gallery-icon-ok-circled"></i> {'Already installed and activated'|translate}{elseif 'inactive' == $card.state}{'Already installed'|translate}{/if}</span>
            <span class="setup-card-error"></span>
          </span>
        </label>
  {/foreach}
      </div>
    </section>

    <section class="install-section">
      <h1>{'Plugins'|translate}</h1>

  {if !$PLUGINS_WRITABLE}
      <div class="error_block install-error">
        <i class="gallery-icon-cancel"></i>
        <p>{'Add write access to the "%s" directory'|translate:'plugins'}</p>
      </div>
  {/if}

      <div class="setup-cards">
  {foreach from=$setup_plugins item=card}
    {assign var=is_locked value=('remote' == $card.state && (!$PLUGINS_WRITABLE || !$CAN_DOWNLOAD))}
        <label class="setup-card{if $is_locked} setup-card-locked{/if}" data-type="plugins" data-id="{$card.id}" data-state="{$card.state}">
          <input type="checkbox" name="setup_plugins[]" value="{$card.id}"{if 'active' == $card.state} checked disabled{elseif $is_locked} disabled{/if}>
          <span class="setup-card-select"></span>
          <span class="setup-card-screenshot">
    {if !empty($card.screenshot)}
            <img src="{$card.screenshot|escape:'html'}" alt="" loading="lazy">
    {else}
            <i class="icon-puzzle"></i>
    {/if}
          </span>
          <span class="setup-card-info">
            <span class="setup-card-name">{$card.name|escape:'html'}</span>
            <span class="setup-card-state">{if 'default' == $card.state || 'active' == $card.state}<i class="gallery-icon-ok-circled"></i> {'Already installed and activated'|translate}{elseif 'inactive' == $card.state}{'Already installed'|translate}{/if}</span>
            <span class="setup-card-error"></span>
          </span>
          <span class="setup-card-description"><span class="setup-card-description-text">{$card.description|strip_tags|escape:'html'}</span></span>
        </label>
  {/foreach}
      </div>
    </section>
{else}
    <section class="install-section" data-step="{$STEP_ID}">
      <h1>{$STEP_TITLE}</h1>
    </section>
{/if}

    <div class="setup-actions">
      <div class="setup-progress-bar">
        <progress id="setup-progress" value="0" max="1"></progress>
        <span id="setup-progress-label"></span>
      </div>

      <div class="setup-buttons">
        <input type="submit" name="skip" value="{'Skip'|translate}" class="btn btn-main btn-secondary">
        <input type="submit" name="next" value="{'Continue'|translate}" class="btn btn-main">
      </div>
    </div>

{if $NB_STEPS_LEFT > 1}
    <p class="install-help">
      <input type="submit" name="skip_all" value="{'Skip the whole setup'|translate}" class="btn-link">
    </p>
{/if}
  </form>
{/if}

</container>
