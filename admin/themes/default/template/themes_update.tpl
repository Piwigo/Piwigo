{combine_script id='jquery.cluetip' load='async' require='jquery' path='themes/default/js/plugins/jquery.cluetip.packed.js'}
{footer_script require='jquery.cluetip'}
jQuery().ready(function(){ldelim}
	jQuery('.cluetip').cluetip({ldelim}
		width: 300,
		splitTitle: '|'
	});
});
{/footer_script}

<div class="titrePage">
  <h2>{'Check for updates'|@translate}</h2>
</div>

{if isset($themes_not_uptodate)}
<br>
<b>{'Themes which need upgrade'|@translate}</b>
<table class="table2 themes">
<thead>
  <tr class="throw">
    <td>{'Name'|@translate}</td>
    <td>{'Current<br>version'|@translate}</td>
    <td>{'Available<br>version'|@translate}</td>
    <td>{'Actions'|@translate}</td>
  </tr>
</thead>
{foreach from=$themes_not_uptodate item=theme name=themes_loop}
  <tr class="{if $smarty.foreach.themes_loop.index is odd}row1{else}row2{/if}">
    <td><a href="{$theme.EXT_URL}" class="externalLink cluetip" title="{$theme.EXT_NAME}|{$theme.EXT_DESC|htmlspecialchars|nl2br}">{$theme.EXT_NAME}</a></td>
    <td style="text-align:center;">{$theme.VERSION}</td>
    <td style="text-align:center;"><a href="{$theme.EXT_URL}" class="externalLink cluetip" title="{$theme.EXT_NAME}|{$theme.NEW_VER_DESC|htmlspecialchars|nl2br}">{$theme.NEW_VERSION}</a></td>
    <td style="text-align:center;"><a href="{$theme.URL_UPDATE}" onclick="return confirm('{'Are you sure to install this upgrade? You must verify if this version does not need uninstallation.'|@translate|@escape:javascript}');">{'Automatic upgrade'|@translate}</a>
      / <a href="{$theme.URL_DOWNLOAD}">{'Download file'|@translate}</a></td>
  </tr>
{/foreach}
</table>
{/if}


{if isset($themes_uptodate)}
<br>
<b>{'Themes up to date'|@translate}</b>
<table class="table2 plugins">
<thead>
  <tr class="throw">
    <td>{'Name'|@translate}</td>
    <td>{'Version'|@translate}</td>
  </tr>
</thead>
{foreach from=$themes_uptodate item=theme name=themes_loop}
  <tr class="{if $smarty.foreach.themes_loop.index is odd}row1{else}row2{/if}">
    <td><a href="{$theme.URL}" class="externalLink cluetip" title="{$theme.NAME}|{$theme.EXT_DESC|htmlspecialchars|nl2br}">{$theme.NAME}</a></td>
    <td style="text-align:center;"><a href="{$theme.URL}" class="externalLink cluetip" title="{$theme.NAME}|{$theme.VER_DESC|htmlspecialchars|nl2br}">{$theme.VERSION}</a></td>
  </tr>
{/foreach}
</table>
{/if}


{if isset($themes_cant_check)}
<br>
<b>{'Theme versions can\'t be checked'|@translate}</b>
<table class="table2 plugins">
<thead>
  <tr class="throw">
    <td>{'Name'|@translate}</td>
    <td>{'Version'|@translate}</td>
  </tr>
</thead>
{foreach from=$themes_cant_check item=theme name=themes_loop}
  <tr class="{if $smarty.foreach.themes_loop.index is odd}row1{else}row2{/if}">
    <td>{$theme.NAME}</td>
    <td style="text-align:center;">{$theme.VERSION}</td>
  </tr>
{/foreach}
</table>
{/if}
