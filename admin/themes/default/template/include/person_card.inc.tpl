{if !$smarty.capture.inc_person_card}
{capture name="inc_person_card"}1{/capture}
{combine_css path="admin/themes/default/css/components/person_card.css"}
{combine_css path="themes/default/vendor/fontello/css/gallery-icon.css" order=-10}
{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='person_card' load=$load_mode path='admin/themes/default/js/person_card.js'}
{footer_script}
const str_x_photos = "{"%d photos"|translate|escape:javascript}";
{/footer_script}

<div class="template people-card people-hidden">
  <i class="people-card-header people-card-lock icon-lock" title="{"Don't show in the gallery"|translate}" style="left: 2px;"></i>
  
  {if isset($options)}
    <i class="people-card-header people-card-menu icon-ellipsis-vert" style="right: 0;"></i>
    <div class="people-card-dropdown dropdown" style="display: none;">
        <div class="people-dropdown-header">
          <b class="people-dropdown-name">Name</b>
          <i class="people-dropdown-photos">x photos</i>
        </div>
      {foreach from=$options item=option}
        <p class="dropdown-option {$option.class}">{"{$option.text}"|translate}</p>
      {/foreach}
      {* Example dropdown option : 
        <a class="dropdown-option icon-eye view" href="index.php?/tags/307-2012">{"View in gallery"|translate}</a>
        <a class="dropdown-option icon-picture manage" href="admin.php?page=batch_manager&amp;filter=tag-307">{"Manage photos"|translate}</a>
        <p class="dropdown-option icon-lock show">{"Show in gallery"|translate}</p>
        <p class="dropdown-option icon-lock hide">{"Don't show in gallery"|translate}</p>
        <p class="dropdown-option icon-pencil edit">{"Edit"|translate}</p>
        <p class="dropdown-option icon-trash delete">{"Delete"|translate}</p> *}
    </div>
  {/if}

  <div class="people-card-picture">
    <i class="icon-user-1"></i>
  </div>
  <p class="people-card-name">Name</p>
</div>

{/if}