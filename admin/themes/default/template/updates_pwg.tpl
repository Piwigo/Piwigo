{footer_script}
jQuery(document).ready(function() {ldelim}
	jQuery('input[name="submit"]').click(function() {ldelim}
    if(!confirm('{'Are you sure?'|@translate}'))
      return false;
    jQuery(this).hide();
    jQuery('.autoupdate_bar').show();
	});
  jQuery('[name="understand"]').click(function() {ldelim}
    jQuery('[name="submit"]').attr('disabled', !this.checked);
  });
});
{/footer_script}

{html_head}
{literal}
<style type="text/css">
form { width: 750px; }
fieldset { padding-bottom: 30px; }
p, form p { text-align: left; margin-left:20px; }
li { margin: 5px; }

.badge-release {
  padding:5px 10px;
  border-radius: 5px;
}

a.badge-release:hover {
  text-decoration:none;
  color: currentColor;
}

.goto-update-page {
  padding:5px 10px;
  font-weight:bold;
}

.goto-update-page:hover {
  text-decoration:none;
}

.update-recommendation {
  margin-top:30px;
}

p.release .errors {margin:0}
</style>
{/literal}
{/html_head}

{if isset($MINOR_RELEASE_PHP_REQUIRED) and isset($MAJOR_RELEASE_PHP_REQUIRED)}
<div class="warnings">
  <i class="eiw-icon icon-attention"></i>
    <ul>
      <li> {'Please upgrade your PHP version before any update.'|translate} </li>
    </ul>
</div>
{/if}

{if isset($PIWIGO_CURRENT_VERSION)}
<p><i class="icon-info-circled-1"></i> {'Currently running version %s'|translate:$PIWIGO_CURRENT_VERSION}</p>
{/if}

{if $STEP == 0}
  {if $CHECK_VERSION}
    <p>{'You are running the latest version of Piwigo.'|@translate}</p>
  {elseif $DEV_VERSION} 
    <p>{'You are running on development sources, no check possible.'|@translate}</p>
  {else}
    <p>{'Check for update failed for unknown reasons.'|@translate}</p>
  {/if}
{/if}

{if $STEP == 1}
<fieldset>
  <legend><span class="icon-ok icon-purple"></span>{'Two updates are available'|@translate}</legend>
<p class="release release-minor">
  <a href="{$MINOR_RELEASE_URL}" target="_blank" class="badge-release icon-green icon-tags">{$MINOR_VERSION}</a>
  {'This is a minor update, with only bug corrections.'|@translate}
{if isset($MINOR_RELEASE_PHP_REQUIRED)}
  <span class="errors icon-block">{'Requires PHP %s'|translate:$MINOR_RELEASE_PHP_REQUIRED}</span>
{else}
  <a href="admin.php?page=updates&amp;step=2&amp;to={$MINOR_VERSION}" class="icon-arrows-cw goto-update-page">{'Update to Piwigo %s'|@translate:$MINOR_VERSION}</a>
{/if}
</p>

<p class="release release-major">
  <a href="{$MAJOR_RELEASE_URL}" target="_blank" class="badge-release icon-blue icon-tags">{$MAJOR_VERSION}</a>
  {'This is a major update, with <a href="%s">new exciting features</a>.'|translate:$MAJOR_RELEASE_URL}
  {'Some themes and plugins may be not available yet.'|translate}
{if isset($MAJOR_RELEASE_PHP_REQUIRED)}
  <span class="errors icon-block">{'Requires PHP %s'|translate:$MAJOR_RELEASE_PHP_REQUIRED}</span>
{else}
  <a href="admin.php?page=updates&amp;step=3&amp;to={$MAJOR_VERSION}" class="icon-arrows-cw goto-update-page">{'Update to Piwigo %s'|@translate:$MAJOR_VERSION}</a>
{/if}
</p>
<p class="update-recommendation">
  <span class="icon-info-circled-1"></span>
  {'You can update to Piwigo %s directly, without upgrading to Piwigo %s (recommended).'|@translate:$MAJOR_VERSION:$MINOR_VERSION}
</p>
</fieldset>
{/if}

{if $STEP == 2}
<p>
  <a href="{$MINOR_RELEASE_URL}" target="_blank" class="badge-release icon-green icon-tags">{$MINOR_VERSION}</a>
</p>
<p>
  {'A new version of Piwigo is available.'|@translate}<br>
  {'This is a minor update, with only bug corrections.'|@translate}
</p>
<form action="" method="post">
<p>
  <input type="submit" name="submit" value="{'Update to Piwigo %s'|@translate:$UPGRADE_TO}"{if isset($MINOR_RELEASE_PHP_REQUIRED)} disabled{/if}>
{if isset($MINOR_RELEASE_PHP_REQUIRED)}
  <span class="errors icon-block">{'Requires PHP %s'|translate:$MINOR_RELEASE_PHP_REQUIRED}</span>
{/if}
</p>
<p class="autoupdate_bar" style="display:none;">&nbsp; {'Update in progress...'|@translate}<br><img src="admin/themes/default/images/ajax-loader-bar.gif"></p>
<p><input type="hidden" name="upgrade_to" value="{$UPGRADE_TO}"></p>
</form>
{/if}

{if $STEP == 3}
<p>
  <a href="{$MAJOR_RELEASE_URL}" target="_blank" class="badge-release icon-blue icon-tags">{$MAJOR_VERSION}</a>
</p>
<p>
  {'A new version of Piwigo is available.'|@translate}<br>
  {'This is a major update, with <a href="%s">new exciting features</a>.'|@translate:$MAJOR_RELEASE_URL} {'Some themes and plugins may be not available yet.'|@translate}
</p>
<form action="" method="post">

{counter assign=i}
<fieldset>
  <legend><span class="icon-floppy icon-red"></span>{'Backup'|@translate}</legend>
  <p>
    {'Always have a backup of your database and files.'|translate}
    {'The best is to have them made automatically on a regular basis.'|translate}
    {'If anything bad happens during the update, you would be able to restore a backup.'|translate}
  </p>
</fieldset>

{counter assign=i}
<fieldset>
  <legend><span class="icon-cog icon-purple"></span>{'Update to Piwigo %s'|@translate:$UPGRADE_TO}</legend>
  {if !empty($missing.plugins)}
  <p><i>{'Following plugins may not be compatible with the new version of Piwigo:'|@translate}</i></p>
  <p><ul>{foreach from=$missing.plugins item=plugin}<li><a href="{$plugin.uri}" class="externalLink">{$plugin.name}</a></li>{/foreach}</ul><br></p>
  {/if}
  {if !empty($missing.themes)}
  <p><i>{'Following themes may not be compatible with the new version of Piwigo:'|@translate}</i></p>
  <p><ul>{foreach from=$missing.themes item=theme}<li><a href="{$theme.uri}" class="externalLink">{$theme.name}</a></li>{/foreach}</ul><br></p>
  {/if}
  <p>
  {if !empty($missing.plugins) or !empty($missing.themes)}
  <p><label><input type="checkbox" name="understand"> &nbsp;{'I decide to update anyway'|@translate}</label></p>
  {/if}
  <p><input type="submit" name="submit" value="{'Update to Piwigo %s'|@translate:$UPGRADE_TO}" {if !empty($missing.plugins) or !empty($missing.themes) or isset($MAJOR_RELEASE_PHP_REQUIRED)}disabled="disabled"{/if}>
{if isset($MAJOR_RELEASE_PHP_REQUIRED)}
  <span class="errors icon-block">{'Requires PHP %s'|translate:$MAJOR_RELEASE_PHP_REQUIRED}</span>
{/if}
  </p>
  <p class="autoupdate_bar" style="display:none;">&nbsp; {'Update in progress...'|@translate}<br><img src="admin/themes/default/images/ajax-loader-bar.gif"></p>
</fieldset>

<p><input type="hidden" name="upgrade_to" value="{$UPGRADE_TO}"></p>
</form>
{/if}
