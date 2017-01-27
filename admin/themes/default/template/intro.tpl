{include file='include/colorbox.inc.tpl'}

{combine_script id='jquery.cluetip' load='async' require='jquery' path='themes/default/js/plugins/jquery.cluetip.js'}

{footer_script require='jquery.cluetip'}
var piwigo_need_update_msg = '<a href="admin.php?page=updates">{'A new version of Piwigo is available.'|@translate|@escape:"javascript"} <i class="icon-right"></i></a>';
var ext_need_update_msg = '<a href="admin.php?page=updates&amp;tab=ext">{'Some upgrades are available for extensions.'|@translate|@escape:"javascript"} <i class="icon-right"></i></a>';

{literal}
jQuery().ready(function(){
	jQuery('.cluetip').cluetip({
		width: 300,
		splitTitle: '|',
		positionBy: 'bottomTop'
	});
  jQuery.ajax({
    type: 'GET',
    url: 'ws.php',
    dataType: 'json',
    data: { method: 'pwg.extensions.checkUpdates', format: 'json' },
    timeout: 5000,
    success: function (data) {
      if (data['stat'] != 'ok')
        return;
      piwigo_update = data['result']['piwigo_need_update'];
      ext_update = data['result']['ext_need_update']
      if ((piwigo_update || ext_update) && !jQuery(".warnings").is('div'))
        jQuery(".eiw").prepend('<div class="warnings"><i class="eiw-icon icon-attention"></i><ul></ul></div>');
      if (piwigo_update)
        jQuery(".warnings ul").append('<li>'+piwigo_need_update_msg+'</li>');
      if (ext_update)
        jQuery(".warnings ul").append('<li>'+ext_need_update_msg+'</li>');
    }
  });
});
{/literal}
{/footer_script}

{html_style}
.stat-boxes {
  text-align:left;
  margin:10px;
}

.stat-box {
  display:inline-block;
  width:200px;
  margin:10px;
  color:#777;
}

div.stat-box {
  cursor:help;
}

.stat-box:hover {
  color:#ff7700;
}

.stat-box i {
  font-size:50px;
  float:left;
  margin-right:5px;
}

.stat-box .number, .stat-box .caption {
  display:inline-block;
  width:120px;
  text-align:left;
}

.stat-box .number {
  margin-top:10px;
  font-size:20px;
}

.eiw .messages ul li {
  list-style-type:none !important;
}

.eiw .messages .eiw-icon {
  margin-right:10px !important;
}
{/html_style}

<h2>{'Piwigo Administration'|@translate}</h2>

<div class="stat-boxes">

{if $NB_PHOTOS > 1}
<a class="stat-box" href="{$U_ADD_PHOTOS}">
<i class="icon-picture"></i>
<span class="number">{$NB_PHOTOS}</span><span class="caption">{'Photos'|translate}</span>
</a>
{/if}

{if $NB_ALBUMS > 1}
<a class="stat-box" href="{$U_CATEGORIES}">
<i class="icon-sitemap"></i>
<span class="number">{$NB_ALBUMS}</span><span class="caption">{'Albums'|translate}</span>
</a>
{/if}

{if $NB_TAGS > 1}
<a class="stat-box" href="{$U_TAGS}">
<i class="icon-tags"></i>
<span class="number">{$NB_TAGS}</span><span class="caption" title="{'%d associations'|translate:$NB_IMAGE_TAG}">{'Tags'|translate}</span>
</a>
{/if}

{if $NB_USERS > 2}
<a class="stat-box" href="{$U_USERS}">
<i class="icon-users"></i>
<span class="number">{$NB_USERS}</span><span class="caption">{'Users'|translate}</span>
</a>
{/if}

{if $NB_GROUPS > 0}
<a class="stat-box" href="{$U_GROUPS}">
<i class="icon-group"></i>
<span class="number">{$NB_GROUPS}</span><span class="caption">{'Groups'|translate}</span>
</a>
{/if}

{if $NB_COMMENTS > 1}
<a class="stat-box" href="{$U_COMMENTS}">
<i class="icon-chat"></i>
<span class="number">{$NB_COMMENTS}</span><span class="caption">{'Comments'|translate}</span>
</a>
{/if}

{if $NB_RATES > 0}
<a class="stat-box" href="{$U_RATING}">
<i class="icon-star"></i>
<span class="number">{$NB_RATES}</span><span class="caption">{'Rating'|translate}</span>
</a>
{/if}

{if $NB_VIEWS > 0}
<a class="stat-box" href="{$U_HISTORY_STAT}">
<i class="icon-signal"></i>
<span class="number">{$NB_VIEWS}</span><span class="caption">{'Pages seen'|translate}</span>
</a>
{/if}

{if $NB_PLUGINS > 0}
<a class="stat-box" href="{$U_PLUGINS}">
<i class="icon-puzzle"></i>
<span class="number">{$NB_PLUGINS}</span><span class="caption">{'Plugins'|translate}</span>
</a>
{/if}

<div class="stat-box">
<i class="icon-hdd"></i>
<span class="number">{$STORAGE_USED}</span><span class="caption">{'Storage used'|translate}</span>
</div>

{if $NB_PHOTOS > 1}
<div class="stat-box">
<i class="icon-back-in-time"></i>
<span class="number">{$first_added_age}</span><span class="caption" title="{'first photo added on %s'|translate:$first_added_date}">{'First photo added'|translate}</span>
</div>
{/if}

</div> {* .stat-boxes *}

<p class="showCreateAlbum">
{if $ENABLE_SYNCHRONIZATION}
  <a href="{$U_QUICK_SYNC}" class="icon-exchange">{'Quick Local Synchronization'|translate}</a>
{/if}

  <br><a href="{$U_CHECK_UPGRADE}" class="icon-arrows-cw">{'Check for upgrade'|@translate}</a>

{if isset($SUBSCRIBE_BASE_URL)}
  <br><a href="{$SUBSCRIBE_BASE_URL}{$EMAIL}" class="externalLink cluetip icon-mail-alt" title="{'Piwigo Announcements Newsletter'|@translate}|{'Keep in touch with Piwigo project, subscribe to Piwigo Announcement Newsletter. You will receive emails when a new release is available (sometimes including a security bug fix, it\'s important to know and upgrade) and when major events happen to the project. Only a few emails a year.'|@translate|@htmlspecialchars|@nl2br}">{'Subscribe %s to Piwigo Announcements Newsletter'|@translate:$EMAIL}</a>
{/if}
</p>
