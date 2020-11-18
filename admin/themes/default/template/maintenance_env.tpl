{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}
{footer_script}
const confirm_msg = '{"Yes, I am sure"|@translate}';
const cancel_msg = "{"No, I have changed my mind"|@translate}";
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
{/footer_script}

<div class="titrePage">
  <h2>{'Maintenance'|@translate}</h2>
</div>

<fieldset id="environment">
  <legend><i class="icon-cog"></i> {'Environment'|@translate}</legend>
  <ul>
    <li><a href="{$PHPWG_URL}" class="externalLink">Piwigo</a> {$PWG_VERSION} <a href="{$U_CHECK_UPGRADE}" class="icon-arrows-cw">{'Check for upgrade'|@translate}</a></li>
    <li>{'Operating system'|@translate}: {$OS}</li>
    <li>PHP: {$PHP_VERSION} (<a href="{$U_PHPINFO}" class="externalLink">{'Show info'|@translate}</a>)  [{$PHP_DATATIME}]</li>
    <li>{$DB_ENGINE}: {$DB_VERSION} [{$DB_DATATIME}]</li>
    {if isset($GRAPHICS_LIBRARY)}
    <li>{'Graphics Library'|@translate}: {$GRAPHICS_LIBRARY}</li>
    {/if}
  </ul>
</fieldset>