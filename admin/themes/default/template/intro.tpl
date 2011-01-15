{combine_script id='jquery.cluetip' load='async' require='jquery' path='themes/default/js/plugins/jquery.cluetip.packed.js'}

{footer_script require='jquery.cluetip'}
jQuery().ready(function(){ldelim}
	jQuery('.cluetip').cluetip({ldelim}
		width: 300,
		splitTitle: '|',
		positionBy: 'bottomTop'
	});
});
{/footer_script}

<h2>{'Piwigo Administration'|@translate}</h2>
<dl style="padding-top: 30px;">
  <dt>{'Piwigo version'|@translate}</dt>
  <dd>
    <ul>
      <li><a href="{$PHPWG_URL}" class="externalLink">Piwigo</a> {$PWG_VERSION}</li>
      <li><a href="{$U_CHECK_UPGRADE}">{'Check for upgrade'|@translate}</a></li>
{if isset($SUBSCRIBE_BASE_URL)}
      <li><a href="{$SUBSCRIBE_BASE_URL}{$EMAIL}" class="externalLink cluetip" title="{'Piwigo Announcements Newsletter'|@translate}|{'Keep in touch with Piwigo project, subscribe to Piwigo Announcement Newsletter. You will receive emails when a new release is available (sometimes including a security bug fix, it\'s important to know and upgrade) and when major events happen to the project. Only a few emails a year.'|@translate|htmlspecialchars|nl2br}">{'Subscribe %s to Piwigo Announcements Newsletter'|@translate|@sprintf:$EMAIL}</a></li>
{/if}
    </ul>
  </dd>

  <dt>{'Environment'|@translate}</dt>
  <dd>
    <ul>
      <li>{'Operating system'|@translate}: {$OS}</li>
      <li>PHP: {$PHP_VERSION} (<a href="{$U_PHPINFO}" class="externalLink">{'Show info'|@translate}</a>)  [{$PHP_DATATIME}]</li>
      <li>{$DB_ENGINE}: {$DB_VERSION} [{$DB_DATATIME}]</li>
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
      <li>
        {$DB_COMMENTS}
        {if isset($unvalidated)}
        (<a href="{$unvalidated.URL}">{$unvalidated.INFO}</a>)
        {/if}
      </li>
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
<input type="submit" value="" name="submit">
</div>
</form>
{/if}