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

<ul>
{if (isset($U_MAINT_LOCK_GALLERY))}
  <li><a href="{$U_MAINT_LOCK_GALLERY}" class="lock-gallery-button">{'Lock gallery'|@translate}</a></li>
{else}
  <li><a href="{$U_MAINT_UNLOCK_GALLERY}">{'Unlock gallery'|@translate}</a></li>
{/if}
</ul>

<ul>
  {foreach from=$advanced_features item=feature}
    <li><a href="{$feature.URL}">{$feature.CAPTION}</a></li>
  {/foreach}
</ul>

<ul>
	<li><a href="{$U_MAINT_CATEGORIES}">{'Update albums informations'|@translate}</a></li>
	<li><a href="{$U_MAINT_IMAGES}">{'Update photos information'|@translate}</a></li>
</ul>

<ul>
	<li><a href="{$U_MAINT_DATABASE}">{'Repair and optimize database'|@translate}</a></li>
	<li><a href="{$U_MAINT_C13Y}">{'Reinitialize check integrity'|@translate}</a></li>
</ul>

<ul>
	<li><a href="{$U_MAINT_USER_CACHE}">{'Purge user cache'|@translate}</a></li>
	<li><a href="{$U_MAINT_ORPHAN_TAGS}">{'Delete orphan tags'|@translate}</a></li>
	<li><a href="{$U_MAINT_HISTORY_DETAIL}" class="purge-history-detail-button">{'Purge history detail'|@translate}</a></li>
	<li><a href="{$U_MAINT_HISTORY_SUMMARY}" class="purge-history-summary-button">{'Purge history summary'|@translate}</a></li>
	<li><a href="{$U_MAINT_SESSIONS}">{'Purge sessions'|@translate}</a></li>
	<li><a href="{$U_MAINT_FEEDS}">{'Purge never used notification feeds'|@translate}</a></li>
	<li><a href="{$U_MAINT_SEARCH}" class="purge-search-history-button">{'Purge search history'|@translate}</a></li>
	<li><a href="{$U_MAINT_COMPILED_TEMPLATES}">{'Purge compiled templates'|@translate}</a></li>
	<li>{'Delete multiple size images'|@translate}: 
	{foreach from=$purge_derivatives key=name item=url name=loop}
    <a href="{$url}"{if $smarty.foreach.loop.first} class="delete-all-sizes-button"{/if}>{$name}</a>{if !$smarty.foreach.loop.last}, {/if}{/foreach}
	</li>
</ul>

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
