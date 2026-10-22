{if !$smarty.capture.inc_picture_selector}
{capture name="inc_picture_selector"}1{/capture}
{combine_css path="admin/themes/default/css/components/picture_selector.css"}
{if 'dark' == $themeconf.colorscheme}{combine_css path="admin/themes/`$themeconf.name`/css/components/picture_selector.css" order=1}{/if}
{combine_script id='picture_selector' load=$load_mode path='admin/themes/default/js/picture_selector.js'}
{footer_script}
  let str_picture_selector_error = '{'An error has occured'|translate|escape:javascript}';
{/footer_script}

<div class="bg-modal" id="picture_selector_modal" style="display: none;">
  <div class="modal-content picture-selector-content">
    <a class="close-modal icon-cancel" id="picture_selector_close"></a>

    <p class="picture-selector-title" id="picture_selector_title"></p>

    <div class="picture-selector-grid" id="picture_selector_grid"></div>
    <p class="picture-selector-empty" id="picture_selector_empty" style="display: none;"></p>
    <i class="picture-selector-loading gallery-icon-spin6 animate-spin" id="picture_selector_loading" style="display: none;"></i>

    <div class="picture-selector-footer">
      <div class="picture-selector-footer-start">
        <p class="buttonLike" id="picture_selector_save"><i class="icon-floppy"></i>{'Save'|translate}</p>
        <p class="modal-footer-close" id="picture_selector_cancel">{'Cancel'|translate}</p>
        <p class="modal-footer-error icon-cancel-circled" id="picture_selector_error" style="display: none;"></p>
      </div>
      <p class="head-button-2 icon-cw" id="picture_selector_random" style="display: none;">{'Random'|translate}</p>
    </div>
  </div>
</div>
{/if}
