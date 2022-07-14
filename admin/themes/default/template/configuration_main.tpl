{include file='include/colorbox.inc.tpl'}
{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

{footer_script require='jquery'}
(function(){
  var targets = {
    'input[name="rate"]' : '#rate_anonymous',
    'input[name="allow_user_registration"]' : '#email_admin_on_new_user'
  };

  for (selector in targets) {
    var target = targets[selector];

    jQuery(target).toggle(jQuery(selector).is(':checked'));

    (function(target){
      jQuery(selector).on('change', function() {
        jQuery(target).toggle($(this).is(':checked'));
      });
    })(target);
  };

  jQuery('.tiptip-with-img').tipTip({
    maxWidth: "300px",
    delay: 0,
    fadeIn: 200,
    fadeOut: 200
  });
}());

{if !isset($ORDER_BY_IS_CUSTOM)}
(function(){
  var max_fields = Math.ceil({$main.order_by_options|@count}/2);

  function updateFilters() {
    var $selects = jQuery('#order_filters select');

    jQuery('#order_filters .addFilter').toggle($selects.length <= max_fields);
    jQuery('#order_filters .removeFilter').css('display', '').filter(':first').css('display', 'none');

    $selects.find('option').removeAttr('disabled');
    $selects.each(function() {
      $selects.not(this).find('option[value="'+ jQuery(this).val() +'"]').attr('disabled', 'disabled');
    });
  }

  jQuery('#order_filters').on('click', '.removeFilter', function() {
    jQuery(this).parent('span.filter').remove();
    updateFilters();
  });

  jQuery('#order_filters').on('change', 'select', updateFilters);

  jQuery('#order_filters .addFilter').click(function() {
    jQuery(this).prev('span.filter').clone().insertBefore(jQuery(this));
    jQuery(this).prev('span.filter').children('select').val('');
    updateFilters();
  });

  updateFilters();
}());
{/if}

jQuery(".themeBoxes a").colorbox();

jQuery("input[name='mail_theme']").change(function() {
  jQuery("input[name='mail_theme']").parents(".themeSelect").removeClass("themeDefault");
  jQuery(this).parents(".themeSelect").addClass("themeDefault");
});
{/footer_script}

<form method="post" action="{$F_ACTION}" class="properties">

<div id="configContent">

  <fieldset class="mainConf">
    <legend><span class="icon-cog icon-purple"></span>{'Basic settings'|translate}</legend>
    <ul>
      <li>
        <label for="gallery_title">{'Gallery title'|translate}</label>
        <br>
        <input type="text" maxlength="255" size="50" name="gallery_title" id="gallery_title" value="{$main.CONF_GALLERY_TITLE}">
      </li>

      <li>
        <label for="page_banner">{'Page banner'|translate}</label>
        <br>
        <textarea rows="5" cols="50" class="description" name="page_banner" id="page_banner">{$main.CONF_PAGE_BANNER}</textarea>
      </li>

     <li id="order_filters">
        <label>{'Default photos order'|translate}</label>

      {foreach from=$main.order_by item=order}
        <span class="filter {if isset($ORDER_BY_IS_CUSTOM)}transparent{/if}">
          <select name="order_by[]" {if isset($ORDER_BY_IS_CUSTOM)}disabled{/if}>
            {html_options options=$main.order_by_options selected=$order}
          </select>
          <a class="removeFilter">{'delete'|translate}</a>
        </span>
      {/foreach}

      {if !isset($ORDER_BY_IS_CUSTOM)}
        <a class="addFilter">{'Add a criteria'|translate}</a>
      {else}
        <span class="order_by_is_custom">{'You can\'t define a default photo order because you have a custom setting in your local configuration.'|translate}</span>
      {/if}
      </li>
    </ul>
  </fieldset>

  <fieldset class="mainConf">
    <legend><span class="icon-lock icon-yellow"></span>{'Permissions'|translate}</legend>
    <ul>
      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="rate" {if ($main.rate)}checked="checked"{/if}>
          {'Allow rating'|translate}
        </label>

        <label id="rate_anonymous" class="font-checkbox no-bold">
          <span class="icon-check"></span>
          <input type="checkbox" name="rate_anonymous" {if ($main.rate_anonymous)}checked="checked"{/if}>
          {'Rating by guests'|translate}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="allow_user_registration" {if ($main.allow_user_registration)}checked="checked"{/if}>
          {'Allow user registration'|translate}
        </label>

        <label id="email_admin_on_new_user" class="font-checkbox no-bold">
          <span class="icon-check"></span>
          <input type="checkbox" name="email_admin_on_new_user" {if ($main.email_admin_on_new_user)}checked="checked"{/if}>
          {'Email admins when a new user registers'|translate}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="allow_user_customization" {if ($main.allow_user_customization)}checked="checked"{/if}>
          {'Allow user customization'|translate}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="obligatory_user_mail_address" {if ($main.obligatory_user_mail_address)}checked="checked"{/if}>
          {'Mail address is mandatory for registration'|translate}
        </label>
      </li>
    </ul>
  </fieldset>

  <fieldset class="mainConf">
    <legend><span class="icon-wrench icon-blue"></span>{'Miscellaneous'|translate}</legend>
    <ul>
      <li>
        <label>{'Week starts on'|translate}
        {html_options name="week_starts_on" options=$main.week_starts_on_options selected=$main.week_starts_on_options_selected}</label>
      </li>

      <li>
        <strong>{'Save visits in history for'|translate}</strong>

        <label class="font-checkbox no-bold">
          <span class="icon-check"></span>
          <input type="checkbox" name="history_guest" {if ($main.history_guest)}checked="checked"{/if}>
          {'simple visitors'|translate}
        </label>

        <label class="font-checkbox no-bold">
          <span class="icon-check"></span>
          <input type="checkbox" name="log" {if ($main.log)}checked="checked"{/if}>
          {'registered users'|translate}
        </label>

        <label class="font-checkbox no-bold">
          <span class="icon-check"></span>
          <input type="checkbox" name="history_admin" {if ($main.history_admin)}checked="checked"{/if}>
          {'administrators'|translate}
        </label>
      </li>

      <li>
        <strong>{'Promote mobile app on mobile devices'|translate}</strong>

        <label class="font-checkbox no-bold">
          <span class="icon-check"></span>
          <input type="checkbox" name="show_mobile_app_banner_in_gallery" {if ($main.show_mobile_app_banner_in_gallery)}checked="checked"{/if}>
          {'in gallery'|translate}
        </label>

        <label class="font-checkbox no-bold">
          <span class="icon-check"></span>
          <input type="checkbox" name="show_mobile_app_banner_in_admin" {if ($main.show_mobile_app_banner_in_admin)}checked="checked"{/if}>
          {'in administration'|translate}
        </label>

        <span class="icon-help-circled tiptip-with-img show-mobile-app-banner-tooltip" title="{'Displays a banner to install or open the official Piwigo app'|translate}<br><img src='admin/themes/default/images/piwigo_app_banner.jpg' style='width:100%;margin-top:5px;'>" style="cursor:help"></span>
      </li>

      <li>
        <label>{'Mail theme'|translate}</label>

        <div class="themeBoxes font-checkbox">
        {foreach from=$main.mail_theme_options item=name key=theme}
          <div class="themeSelect {if $main.mail_theme==$theme}themeDefault{/if}">
            <label class="font-checkbox">
              <div class="themeName">
                <span class="icon-dot-circled"></span>
                <input type="radio" name="mail_theme" value="{$theme}" {if $main.mail_theme==$theme}checked{/if}>
                {$name}
              </div>
              <div class="themeShot">
                <img src="{$ROOT_URL}themes/default/template/mail/screenshot-{$theme}.png" width="150"/>
              </div>
            </label>
            <a href="{$ROOT_URL}themes/default/template/mail/screenshot-{$theme}.png" class="icon-zoom-in">{'Preview'|translate}</a>
          </div>
        {/foreach}
        </div>
      </li>

    </ul>
  </fieldset>

</div> <!-- configContent -->

<p class="formButtons">
  <button name="submit" type="submit" class="buttonLike" {if $isWebmaster != 1}disabled{/if}>
    <i class="icon-floppy"></i> {'Save Settings'|@translate}
  </button>
</p>

<input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
</form>