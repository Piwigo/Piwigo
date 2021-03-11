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
    $(this).find("i").hide();
  } else {
    $(this).attr('data-selected', '1');
    $(this).find("i").show();
  }
  $(this).trigger("change");
});
$(".delete-size-check:first").change(function() {
  if ($(this).attr('data-selected') == '1') {
    $(".delete-size-check").hide();
    $(".delete-size-check").attr("data-selected", "1");
    $(this).show();
  } else {
    $(".delete-size-check").show();
    $(".delete-size-check").attr("data-selected", "0");
  }
})
const delete_deriv_URL = "admin.php?page=maintenance&action=derivatives&";
$(".delete-size-check").change(function() {
  let delete_deriv_with_token = delete_deriv_URL + "pwg_token=" + "{$pwg_token}&";
  let types_str = '';
  let selected = []
  $(".delete-size-check").each(function () {
    if ($(this).attr("data-selected") == '1') {
      selected.push($(this).attr("name"));
    }
  })
  if (selected.length == 0) {
    $(".delete-sizes").attr("href", "");
  } else {
    if (selected[0] == "all") {
      types_str = "all";
    } else {
      types_str = selected.join("_");
    }
    console.log(selected);
    $(".delete-sizes").attr("href", delete_deriv_with_token + "type=" + types_str);
  }
})

{/footer_script}
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
    <a href="{$U_MAINT_DATABASE}" class="icon-database maintenance-action">{'Repair and optimize database'|@translate}</a>
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
    <a href="{$U_MAINT_FEEDS}" class="icon-bell maintenance-action">{'Purge never used notification feeds'|@translate}</a>
    <a href="{$U_MAINT_SEARCH}" class="icon-search maintenance-action purge-search-history-button">{'Purge search history'|@translate}</a>
  </div>
</fieldset>

<fieldset class="">
<legend><span class="icon-trash-1 icon-red"></span>Purge Cache</legend>

  <div class="template-purge"> 
    <div class="cache-infos"> 
      <span class="cache-size-text">Taille du cache</span>
      <span class="cache-size-value">999 Go</span>
      <span class="cache-lastCalculated-text">calculé il y a</span>
      <span class="cache-lastCalculated-value">42 mois</span>
      <a><span class="icon-arrows-cw"></span>Actulaliser</a>
    </div>
    <a href="{$U_MAINT_COMPILED_TEMPLATES}" class="icon-doc maintenance-action">{'Purge compiled templates'|@translate} <span class="multiple-compiledTemplate-sizes"> 999 Go </span></a> 
  </div>


  <div class="delete-size-checks">
  <span id="label-delete-size-checkbox">{'Delete multiple size images'|@translate} <span class="multiple-pictures-sizes"> 999 Go </span></span>
    <div class="delete-check-container">
      {foreach from=$purge_derivatives key=name item=url name=loop}
      <div class="delete-size-check" data-selected="0" name="{$url}">
        <span class="select-checkbox"><i class="icon-ok" style="margin-left:8px"></i></span><span class="picture-deletion-size" title="Poids : 999Go" style="font-size:14px;margin-left:5px;padding-top:2px;">{$name}</span>
      </div>
      {/foreach}
    </div>
  </div>

  <a class="icon-ok maintenance-action delete-sizes">Delete these sizes</a>
</fieldset>

<style>
#label-delete-size-checkbox {
  font-weight:bold;
  white-space:nowrap;
}

.maintenance-action:hover {
  background-color: #ffa744;
  color: #3c3c3c;
}

.maintenance-action {
  border:solid 1px;
  padding:8px 30px;
  margin-right: 20px;
  margin-bottom: 20px;
}

.delete-size-checks {
  display:flex;
  text-align:left;
  margin-bottom:20px;
}

.delete-check-container {
  display:flex;
  flex-wrap:wrap;
}

.delete-size-check {
  margin-left:15px;
  margin-bottom:10px;
  display:flex;
  cursor:pointer
}

.select-checkbox {
  display:inline-block;
}

.delete-sizes {
  display:block;
  width:max-content;
  text-align:left;
}
</style>