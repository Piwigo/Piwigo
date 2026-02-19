{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

<section class="std_pgs">
  <form method="post" action="{$F_ACTION}" class="properties" enctype="multipart/form-data">

    <fieldset class="std_pgs_conf">
      <legend><span class="icon-cog icon-purple"></span>{'Basic settings'|translate}</legend>
      <ul>
        <li>
          <label class="font-checkbox">
            <span class="icon-check"></span>
            <input type="checkbox" name="use_standard_pages" {if $use_standard_pages }checked="checked"{/if}>
              {'Use standard Piwigo template for common pages.'|translate}
          </label>

          <span class="icon-help-circled tiptip" title="{'When enabled, a common template is used for the login, registration and forgotten password pages, regardless of the theme. Some themes might use these templates even if you uncheck this option'|translate}" style="cursor:help"></span>
        </li>
      </ul>
    </fieldset>

{if $is_standard_pages_used and !$use_standard_pages}
      <div class="std_pgs_theme_info warnings">
        <p class="">{'Standard pages aren\'t activated, however you have %d active themes that will still use them. These themes are:'|translate:count($standard_pages_used_by)} </p>
        <ul>
  {foreach $standard_pages_used_by as $theme_name}
          <li>{$theme_name}</li>
  {/foreach}
        </ul>
      </div>
{/if}

    <fieldset class="std_pgs_personnalisation_settings">
      <legend><span class="icon-dice-solid icon-green"></span>{'Personalization settings'|translate}</legend>
      <ul>
        <li>
          <div class="std_pgs_header_options">
            <strong>{'Standard pages header'|translate}</strong>
            <br>
            <label class="font-checkbox no-bold">
              <span class="icon-dot-circled"></span>
              <input type="radio" name="std_pgs_display_logo" value="piwigo_logo" {if "piwigo_logo" == $std_pgs_selected_logo}checked="checked"{/if}>
              {'Use Piwigo logo'|translate}
            </label>

            <label class="font-checkbox no-bold" id="custom_logo_option">
              <span class="icon-dot-circled"></span>
              <input type="radio" name="std_pgs_display_logo" value="custom_logo" {if "custom_logo" == $std_pgs_selected_logo}checked="checked"{/if}>
              {'Use custom logo (png, jpeg or svg)'|translate}
              <div class="custom_logo_preview {if "custom_logo" == $std_pgs_selected_logo}show{else}hide{/if}">
  {if isset($std_pgs_selected_logo_path)}
                <div class="change_logo_container">
                  <img src="{$std_pgs_selected_logo_path}">
                  <a href="#" id="change_logo">{'Change logo'|translate}</a>
                </div>
  {/if}
                <div class="use_existing_logo_container {if isset($std_pgs_selected_logo_path)}hide{/if}">
                  <input type="file" size="60" id="std_pgs_logo" name="std_pgs_logo" accept="image/*" />
                  <a href="#" id="use_existing_logo">{'Cancel'|translate}</a>
                </div>
              </div>
            </label>

            <label class="font-checkbox no-bold">
              <span class="icon-dot-circled"></span>
              <input type="radio" name="std_pgs_display_logo" value="gallery_title" {if "gallery_title" == $std_pgs_selected_logo}checked="checked"{/if}>
              {'Display Gallery title'|translate}
            </label>

            <label class="font-checkbox no-bold">
              <span class="icon-dot-circled"></span>
              <input type="radio" name="std_pgs_display_logo" value="none" {if "none" == $std_pgs_selected_logo}checked="checked"{/if}>
              {'None'|translate}
            </label>


          </div>
        </li>

        <li>
          <div class="skin_choice">
            <strong>{'Select a color theme for standard pages'|translate}</strong>

            <div class="std_pgs_previews">
              <input type="hidden" name="std_pgs_selected_skin" value="{$std_pgs_selected_skin}">
              <div class="std_pgs_mini_previews">
              {foreach $std_pgs_skin_options as $std_pgs_skin_option}
                <img class="{if $std_pgs_selected_skin == $std_pgs_skin_option}selected{/if}" id="{$std_pgs_skin_option}" src="themes/standard_pages/skins/light-{$std_pgs_skin_option}.jpg">
              {/foreach}
              </div>
              <div class="std_pgs_selected_preview">
                <div class="std_pgs_selected_preview_container">
                  <h5>{'Light mode'|translate}</h5>
                  <img id="preview-light" src="themes/standard_pages/skins/light-{$std_pgs_selected_skin}.jpg">
                </div>
                <div class="std_pgs_selected_preview_container">
                  <h5>{'Dark mode'|translate}</h5>
                  <img id="preview-dark" src="themes/standard_pages/skins/dark-{$std_pgs_selected_skin}.jpg">
                </div>
              </div>
            </div>

          </div>
        </li>

      </ul>
    </fieldset>

    <div class="savebar-footer">
      <div class="savebar-footer-start">
      </div>
      <div class="savebar-footer-end">
    {if isset($save_success)}
        <div class="savebar-footer-block">
          <div class="badge info-message">
            <i class="icon-ok-circled"></i>{$save_success}
          </div>
        </div>
    {/if}
    {if isset($save_error)}
        <div class="savebar-footer-block">
          <div class="badge info-warning">
            <i class="icon-warning-circled"></i>{$save_error}
          </div>
        </div>
    {/if}
        <div class="savebar-footer-block">
          <button class="buttonLike"  type="submit" name="submit" {if $isWebmaster != 1}disabled{/if}><i class="icon-floppy"></i> {'Save Settings'|@translate}</button>
        </div>
      </div>
      <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
    </div>

  </form>
</section>

{footer_script}


// Update preview when user clicks on mini previews
jQuery(".std_pgs_mini_previews img").click(function () {

  //Make selected skin outlined
  jQuery(".std_pgs_mini_previews img").removeClass('selected');
  jQuery(this).addClass('selected');

  //Update preview when useer clicks on mini
  jQuery('input[name=std_pgs_selected_skin]').val(jQuery(this).attr('id'));

  let preview_light_path = "themes/standard_pages/skins/light-"+$(this).attr('id')+".jpg";
  let preview_dark_path = "themes/standard_pages/skins/dark-"+$(this).attr('id')+".jpg";
  
  jQuery('.std_pgs_selected_preview img#preview-light').attr("src", preview_light_path);
  jQuery('.std_pgs_selected_preview img#preview-dark').attr("src", preview_dark_path);
});

jQuery("input[name=std_pgs_display_logo]").click(function () {
  if('custom_logo' == jQuery(this).val())
  {
    // jQuery('#std_pgs_logo').addClass('show').removeClass('hide');
    jQuery('.custom_logo_preview').addClass('show').removeClass('hide');
  }
  else
  {
    // jQuery('#std_pgs_logo').addClass('hide').removeClass('show');
    jQuery('.custom_logo_preview').addClass('hide').removeClass('show');
  }
});

// Scroll mini to show the selected one
jQuery(document).ready(function () {
  const std_pgs_mini_previews = jQuery('.std_pgs_mini_previews');
  const selected_mini = std_pgs_mini_previews.find('.selected');

  if (selected_mini.length) {
    std_pgs_mini_previews.scrollTop(
      selected_mini.position().top + std_pgs_mini_previews.scrollTop()
    );
  }
});

//Switch between change logo and use existing logo

  jQuery('#change_logo').click(function () {
    jQuery('.use_existing_logo_container').show();
    jQuery('.change_logo_container').hide();
  });
  jQuery('#use_existing_logo').click(function () {
    jQuery('.change_logo_container').show();
    jQuery('.use_existing_logo_container').hide();
    jQuery('#std_pgs_logo').val('');
  });



{/footer_script}