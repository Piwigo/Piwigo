{combine_css path="admin/themes/default/fontello/css/animation.css" order=10} {* order 10 is required, see issue 1080 *}
{combine_css path="themes/default/js/plugins/cropper.min.css"}
{combine_css path="admin/themes/default/css/components/picture_people.css"}
{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='cropper' load='footer' path='themes/default/js/plugins/cropper.min.js'}
{combine_script id='picture_people' load='footer' require='jquery' path='admin/themes/default/js/picture_people.js'}
{footer_script}
const PWG_TOKEN = "{$PWG_TOKEN}";
const people_picture_image_id = {$IMAGE_ID};
const people_picture_medium_src = "{$MEDIUM_SRC|escape:javascript}";
const people_picture_image_width = {$IMG_WIDTH};
const people_picture_image_height = {$IMG_HEIGHT};
const people_picture_people = {$IMAGE_PERSONS|@json_encode};
const str_people_identify = "{'Identify'|translate|escape:javascript}";
const str_people_identify_person = "{'Identify a person'|translate|escape:javascript}";
const str_people_picture_error = "{'An error has occured'|translate|escape:javascript}";
{/footer_script}

<div class="people-picture">
  <div class="people-picture-preview" id="picture-preview">
    <img src="{$MEDIUM_SRC}" alt="{'Thumbnail'|translate}" class="other-image-format" style="{if $FORMAT}width:100%; max-height:100%;{else}max-width:100%; height:100%;{/if} object-fit:contain;" />
  </div>
  <div class="people-picture-infos">
    <legend><span class="icon-th-1 icon-green"></span>{"People"|translate}</legend>
    <p class="associate-people head-button-2 icon-plus-circled" id="people_picture_associate">{"Associate people"|translate}</p>

    <p class="people-picture-empty" id="people_picture_empty"{if !empty($IMAGE_PERSONS)} style="display: none;"{/if}>{'No people associated with this photo.'|translate}</p>
    <div class="people-picture-list" id="people_picture_list"{if empty($IMAGE_PERSONS)} style="display: none;"{/if}></div>
  </div>
</div>

<div class="savebar-footer">
  <div class="savebar-footer-start"></div>
  <div class="savebar-footer-end">
    <div class="savebar-footer-block" id="people_picture_error" style="display: none;">
      <div class="badge-unsaved">
        <i class="icon-cancel-circled"></i><span id="people_picture_error_text"></span>
      </div>
    </div>
    <div class="savebar-footer-block" id="people_picture_unsaved" style="display: none;">
      <div class="badge-unsaved">
        <i class="icon-attention"></i>{'You have unsaved changes'|translate}
      </div>
    </div>
    <div class="savebar-footer-block" id="people_picture_saved" style="display: none;">
      <div class="badge info-message">
        <i class="icon-ok-circled"></i>{'Changes saved'|translate}
      </div>
    </div>
    <div class="savebar-footer-block">
      <button class="buttonLike" type="button" id="people_picture_save"><i class="icon-floppy"></i> {'Save Settings'|translate}</button>
    </div>
  </div>
</div>

{* IDENTIFY MODAL *}
<div class="bg-modal" id="people_identify_modal" style="display: none;">
  <div class="modal-content">
    <a class="close-modal icon-cancel" id="people_identify_close"></a>

    <div class="modal-icon-container">
      <span class="modal-icon-header icon-blue icon-user-1"></span>
    </div>
    <div class="modal-title-header">
      <p id="people_identify_title"></p>
    </div>

    <div class="modal-body">
      <p class="people-identify-help">{'Adjust the frame on the face'|translate}</p>
      <div class="people-identify-image">
        <img id="people_identify_img" src="{$MEDIUM_SRC}" alt="{'Thumbnail'|translate}" />
      </div>
    </div>

    <div class="modal-footer">
      <p class="modal-footer-close" id="people_identify_cancel">{'Cancel'|translate}</p>
      <div class="modal-footer-group">
        <p class="people-identify-skip" id="people_identify_skip">{'Associate without identifying'|translate}</p>
        <p class="buttonLike" id="people_identify_save"><span class="icon-ok"></span>{'Identify'|translate}</p>
      </div>
    </div>
  </div>
</div>

{* TEMPLATE, included before the selector so the cards of the tab get their menu *}
{assign var="options" value=[
  'identify' => [ 'class' => 'icon-crop identify', 'text' => 'Identify'],
  'dissociate' => [ 'class' => 'icon-cancel dissociate', 'text' => 'Dissociate'],
]}
{include file='include/person_card.inc.tpl' options=$options load_mode='footer'}

{* PEOPLE SELECTOR *}
{include file='include/people_selector.inc.tpl' load_mode='footer'}
