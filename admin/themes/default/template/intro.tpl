{combine_script id='jquery.cluetip' load='async' require='jquery' path='themes/default/js/plugins/jquery.cluetip.js'}

{footer_script require='jquery.cluetip'}
var piwigo_need_update_msg = '<a href="admin.php?page=updates">{'A new version of Piwigo is available.'|@translate|@escape:"javascript"}</a>';
var ext_need_update_msg = '<a href="admin.php?page=updates&amp;tab=ext">{'Some upgrades are available for extensions.'|@translate|@escape:"javascript"}</a>';

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
        jQuery("#content").prepend('<div class="warnings"><ul></ul></div>');
      if (piwigo_update)
        jQuery(".warnings ul").append('<li>'+piwigo_need_update_msg+'</li>');
      if (ext_update)
        jQuery(".warnings ul").append('<li>'+ext_need_update_msg+'</li>');
    }
  });
  jQuery('.tips').cluetip({
    multiple: true,
		width: 300,
		splitTitle: '|',
		positionBy: 'bottomTop',
    attribute:'data-help'
	});
});
{/literal}
{/footer_script}

<h2>{'Piwigo Administration'|@translate}</h2>
<dl style="padding-top: 30px;">
  <dt>{'Piwigo version'|@translate}</dt>
  <dd>
    <ul>
      <li><a href="{$PHPWG_URL}" class="externalLink">Piwigo</a> {$PWG_VERSION}</li>
      <li><a href="{$U_CHECK_UPGRADE}">{'Check for upgrade'|@translate}</a></li>
{if isset($SUBSCRIBE_BASE_URL)}
      <li><a href="{$SUBSCRIBE_BASE_URL}{$EMAIL}" class="externalLink cluetip" title="{'Piwigo Announcements Newsletter'|@translate}|{'Keep in touch with Piwigo project, subscribe to Piwigo Announcement Newsletter. You will receive emails when a new release is available (sometimes including a security bug fix, it\'s important to know and upgrade) and when major events happen to the project. Only a few emails a year.'|@translate|@htmlspecialchars|@nl2br}">{'Subscribe %s to Piwigo Announcements Newsletter'|@translate|@sprintf:$EMAIL}</a></li>
{/if}
    </ul>
  </dd>
<a href="javascript:void(0)" class="externalLink tips" data-help="{'Piwigo Announcements Newsletter'|@translate}|{'Keep in touch with Piwigo project, subscribe to Piwigo Announcement Newsletter. You will receive emails when a new release is available (sometimes including a security bug fix, it\'s important to know and upgrade) and when major events happen to the project. Only a few emails a year.'|@translate|@htmlspecialchars|@nl2br}">TRIGGER</a>
<span data-help="TITLE|BLABLA">TEXT</span>
  <dt>{'Environment'|@translate}</dt>
  <dd>
    <ul>
      <li>{'Operating system'|@translate}: {$OS}</li>
      <li>PHP: {$PHP_VERSION} (<a href="{$U_PHPINFO}" class="externalLink">{'Show info'|@translate}</a>)  [{$PHP_DATATIME}]</li>
      <li>{$DB_ENGINE}: {$DB_VERSION} [{$DB_DATATIME}]</li>
      {if isset($GRAPHICS_LIBRARY)}
      <li>{'Graphics Library'|@translate}: {$GRAPHICS_LIBRARY}</li>
      {/if}
    </ul>
  </dd>

  <dt>{'Database'|@translate}</dt>
  <dd>
    <ul>
      <li>
        {$DB_ELEMENTS}
        {if isset($first_added)}
        ({$first_added.DB_DATE})
        {/if}
      </li>
      <li>{$DB_CATEGORIES} ({$DB_IMAGE_CATEGORY})</li>
      <li>{$DB_TAGS} ({$DB_IMAGE_TAG})</li>
      <li>{$DB_USERS}</li>
      <li>{$DB_GROUPS}</li>
    {if isset($DB_COMMENTS)}
      <li>
        {$DB_COMMENTS}
        {if isset($unvalidated)}
        (<a href="{$unvalidated.URL}">{$unvalidated.INFO}</a>)
        {/if}
      </li>
    {/if}
			<li>{$DB_RATES}</li>
    </ul>
  </dd>
</dl>

{if $ENABLE_SYNCHRONIZATION}
<form name="QuickSynchro" action="{$U_CAT_UPDATE}" method="post" id="QuickSynchro" style="display: block; text-align:right;">
<div>
<input type="hidden" name="sync" value="files" checked="checked">
<input type="hidden" name="display_info" value="1" checked="checked">
<input type="hidden" name="add_to_caddie" value="1" checked="checked">
<input type="hidden" name="privacy_level" value="0" checked="checked">
<input type="hidden" name="sync_meta" checked="checked">
<input type="hidden" name="simulate" value="0">
<input type="hidden" name="subcats-included" value="1" checked="checked">
</div>
<div class="bigbutton">
<span class="bigtext">{'Quick Local Synchronization'|@translate}</span>
<input type="submit" value="{'Quick Local Synchronization'|@translate}" name="submit">
</div>
</form>
{/if}