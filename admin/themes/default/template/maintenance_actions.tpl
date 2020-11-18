{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}
{footer_script}
const confirm_msg = '{"Yes, I am sure"|@translate}';
const cancel_msg = "{"No, I have changed my mind"|@translate}";
let selected = [];
$(".lock-gallery-button").each(function() {
  const gallery_tip = '{"A locked gallery is only visible to administrators"|@translate|@escape:'javascript'}';
  let title = '{"Are you sure you want to lock the gallery?"|@translate}';
  let confirm_msg_gallery = '{"Yes, I want to lock the gallery"|@translate}';
  let cancel_msg_gallery = '{"Keep it unlocked"|@translate}';
  $(this).pwg_jconfirm_follow_href({
    alert_title: title,
    alert_confirm: confirm_msg,
    alert_cancel: cancel_msg,
    alert_content: gallery_tip
  });
});
$(".purge-history-detail-button").each(function() {
  const title = '{"Purge history detail"|@translate|@escape:'javascript'}';
  $(this).pwg_jconfirm_follow_href({
    alert_title: title,
    alert_confirm: confirm_msg,
    alert_cancel: cancel_msg
  });
});
$(".purge-history-summary-button").each(function() {
  const title = '{"Purge history summary"|@translate|@escape:'javascript'}';
  $(this).pwg_jconfirm_follow_href({
    alert_title: title,
    alert_confirm: confirm_msg,
    alert_cancel: cancel_msg
  });
});
$(".purge-search-history-button").each(function() {
  const title = '{"Purge search history"|@translate|@escape:'javascript'}';
  $(this).pwg_jconfirm_follow_href({
    alert_title: title,
    alert_confirm: confirm_msg,
    alert_cancel: cancel_msg
  });
});
$(".delete-all-sizes-button").each(function() {
  const title = '{"Are you sure you want to delete all sizes?"|@translate|@escape:'javascript'}';
  $(this).pwg_jconfirm_follow_href({
    alert_title: title,
    alert_confirm: confirm_msg,
    alert_cancel: cancel_msg
  });
});

$(".delete-size-check").click(function () {
  if ($(this).attr('data-selected') == '1') {
    $(this).attr('data-selected', '0');
    removeSelectedItem($(this).attr('data-id'));
    $(this).find("i").hide();
  } else {
    $(this).attr('data-selected', '1');
    addSelectedItem($(this).attr('data-id'));
    $(this).find("i").show();
  }
  //updateSelectionSize();
});
{/footer_script}

<div class="titrePage">
  <h2>{'Maintenance'|@translate}</h2>
</div>

<fieldset class="">
  <legend><span class="icon-globe icon-blue"></span>Global Gallery Actions</legend>
  <div style="display:flex;flex-wrap: wrap;">
    {if (isset($U_MAINT_LOCK_GALLERY))}
      <a href="{$U_MAINT_LOCK_GALLERY}" class="lock-gallery-button icon-lock maintenance-action">{'Lock gallery'|@translate}</a>
    {else}
      <a href="{$U_MAINT_UNLOCK_GALLERY}" class="lock-gallery-button icon-lock maintenance-action">{'Unlock gallery'|@translate}</a>
    {/if}
    <a href="{$U_MAINT_CATEGORIES}" class="icon-folder-open maintenance-action">{'Update albums informations'|@translate}</a>
    <a href="{$U_MAINT_IMAGES}" class="icon-info-circled-1 maintenance-action">{'Update photos information'|@translate}</a>
    <a href="{$U_MAINT_DATABASE}" class="icon-ok maintenance-action">{'Repair and optimize database'|@translate}</a>
    <a href="{$U_MAINT_C13Y}" class="icon-ok maintenance-action">{'Reinitialize check integrity'|@translate}</a>
  </div>
</fieldset>
<fieldset class="">
  <legend><span class="icon-trash-1 icon-green"></span>Purge Actions</legend>
  <div style="display:flex;flex-wrap: wrap;">
    <a href="{$U_MAINT_USER_CACHE}" class="icon-user-1 maintenance-action">{'Purge user cache'|@translate}</a>
    <a href="{$U_MAINT_ORPHAN_TAGS}" class="icon-tags maintenance-action">{'Delete orphan tags'|@translate}</a>
    <a href="{$U_MAINT_HISTORY_DETAIL}" class="icon-back-in-time maintenance-action purge-history-detail-button">{'Purge history detail'|@translate}</a>
    <a href="{$U_MAINT_HISTORY_SUMMARY}" class="icon-back-in-time maintenance-action purge-history-summary-button">{'Purge history summary'|@translate}</a>
    <a href="{$U_MAINT_SESSIONS}" class="icon-th-list maintenance-action">{'Purge sessions'|@translate}</a>
    <a href="{$U_MAINT_FEEDS}" class="maintenance-action">{'Purge never used notification feeds'|@translate}</a>
    <a href="{$U_MAINT_SEARCH}" class="icon-search maintenance-action purge-search-history-button">{'Purge search history'|@translate}</a>
    <a href="{$U_MAINT_COMPILED_TEMPLATES}" class="icon-file maintenance-action">{'Purge compiled templates'|@translate}</a>
  <div style="display:flex;flex-wrap: wrap;">
</fieldset>

<div class="delete-size-checks">
  <span style="font-weight:bold">{'Delete multiple size images'|@translate}</span>
  <div style="display:flex;flex-wrap:wrap">
    {foreach from=$purge_derivatives key=name item=url name=loop}
    <div class="delete-size-check" style="margin-left:15px;margin-bottom:10px;display:flex">
      <span class="select-checkbox" style="display:inline-block"><i class="icon-ok" style="margin-left:8px"></i></span><span style="font-size:14px;margin-left:5px;padding-top:2px;">{$name}</span>
    </div>
    {/foreach}
  </div>
</div>
<a class="icon-ok maintenance-action" style="display:block;width:max-content;text-align:left;margin-left:20px">Delete these sizes</a>
<!--
{foreach from=$purge_derivatives key=name item=url name=loop}
  <a href="{$url}"{if $smarty.foreach.loop.first} class="delete-all-sizes-button"{/if}>{$name}</a>{if !$smarty.foreach.loop.last}, {/if}{/foreach}
-->

<style>
.maintenance-action {
  border:solid 1px;
  padding:8px 30px;
  margin-right: 50px;
  margin-bottom: 20px;
}

.delete-size-checks {
  display:flex;
  text-align:left;
  margin-left:20px;
  margin-bottom:20px;
}
</style>