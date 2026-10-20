{combine_css path="admin/themes/default/fontello/css/animation.css" order=10} {* order 10 is required, see issue 1080 *}
{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

<div class="people-picture">
  <div class="people-picture-preview" id="picture-preview">
    <img src="{$MEDIUM_SRC}" alt="{'Thumbnail'|translate}" class="other-image-format" style="{if $FORMAT}width:100%; max-height:100%;{else}max-width:100%; height:100%;{/if} object-fit:contain;" />
  </div>
  <div class="people-picture-infos">
    <legend><span class="icon-th-1 icon-green"></span>{"People"|translate}</legend>
    <p class="associate-people head-button-2 icon-plus-circled">{"Associate people"|translate}</p>
  </div>
</div>

{* TEMPLATE *}
{include file='include/person_card.inc.tpl'}

{* PEOPLE SELECTOR *}
{include file='include/people_selector.inc.tpl'}

<style>
.people-picture {
  margin: 30px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 30px;
}

.people-picture-preview {
  margin-left: unset !important;
  max-width: unset !important;
  /* height: 50vw !important; */
}

.people-picture-infos {
  display: flex;
  flex-direction: column;
}

.associate-people {
  width: fit-content;
}
</style>