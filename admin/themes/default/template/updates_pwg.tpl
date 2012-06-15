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
</style>
{/literal}
{/html_head}

<div class="titrePage">
<h2>{'Updates'|@translate}</h2>
</div>

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
<h4>{'Two updates are available'|@translate}:</h4>
<p>
<ul>
  <li><a href="admin.php?page=updates&amp;step=2&amp;to={$MINOR_VERSION}"><strong>{'Update to Piwigo %s'|@translate|@sprintf:$MINOR_VERSION}</strong></a>: {'This is a minor update, with only bug corrections.'|@translate}</li>
  <li><a href="admin.php?page=updates&amp;step=3&amp;to={$MAJOR_VERSION}"><strong>{'Update to Piwigo %s'|@translate|@sprintf:$MAJOR_VERSION}</strong></a>: {'This is a major update, with <a href="%s">new exciting features</a>.'|@translate|@sprintf:$RELEASE_URL} {'Some themes and plugins may be not available yet.'|@translate}</li>
</ul>
</p>
<p>{'You can update to Piwigo %s directly, without upgrading to Piwigo %s (recommended).'|@translate|@sprintf:$MAJOR_VERSION:$MINOR_VERSION}</p>
{/if}

{if $STEP == 2}
<p>
  {'A new version of Piwigo is available.'|@translate}<br>
  {'This is a minor update, with only bug corrections.'|@translate}
</p>
<form action="" method="post">
<p><input type="submit" name="submit" value="{'Update to Piwigo %s'|@translate|@sprintf:$UPGRADE_TO}"></p>
<p class="autoupdate_bar" style="display:none;">&nbsp; {'Update in progress...'|@translate}<br><img src="admin/themes/default/images/ajax-loader-bar.gif"></p>
<p><input type="hidden" name="upgrade_to" value="{$UPGRADE_TO}"></p>
</form>
{/if}

{if $STEP == 3}
<p>
  {'A new version of Piwigo is available.'|@translate}<br>
  {'This is a major update, with <a href="%s">new exciting features</a>.'|@translate|@sprintf:$RELEASE_URL} {'Some themes and plugins may be not available yet.'|@translate}
</p>
<form action="" method="post">

{counter assign=i}
<fieldset>
  <legend>{'Dump Database'|@translate}</legend>
  <p><input type="checkbox" name="includeHistory"> &nbsp; {'Include history data (Warning: server memory limit may be exceeded)'|@translate}</p>
  <p><input type="submit" name="dumpDatabase" value="{'Dump Database'|@translate}"></p>
</fieldset>

{counter assign=i}
<fieldset>
  <legend>{'Update to Piwigo %s'|@translate|@sprintf:$UPGRADE_TO}</legend>
  {if !empty($missing.plugins)}
  <p><i>{'Following plugins may not be compatible with the new version of Piwigo:'|@translate}</i></p>
  <p><ul>{foreach from=$missing.plugins item=plugin}<li><a href="{$plugin.uri}" class="externalLink">{$plugin.name}</a></li>{/foreach}</ul><br></p>
  {/if}
  {if !empty($missing.themes)}
  <p><i>{'Following themes may not be compatible with the new version of Piwigo:'|@translate}</i></p>
  <p><ul>{foreach from=$missing.themes item=theme}<li><a href="{$theme.uri}" class="externalLink">{$theme.name}</a></li>{/foreach}</ul><br></p>
  {/if}
  <p>

{if isset($forbid_upgrade_message)}
  {$forbid_upgrade_message}
{else}
  {if !empty($missing.plugins) or !empty($missing.themes)}
  <p><label><input type="checkbox" name="understand"> &nbsp;{'I decide to update anyway'|@translate}</label></p>
  {/if}
  <p><input type="submit" name="submit" value="{'Update to Piwigo %s'|@translate|@sprintf:$UPGRADE_TO}" {if !empty($missing.plugins) or !empty($missing.themes)}disabled="disabled"{/if}>
  </p>
  <p class="autoupdate_bar" style="display:none;">&nbsp; {'Update in progress...'|@translate}<br><img src="admin/themes/default/images/ajax-loader-bar.gif"></p>
{/if}
</fieldset>

<p><input type="hidden" name="upgrade_to" value="{$UPGRADE_TO}"></p>
</form>
{/if}