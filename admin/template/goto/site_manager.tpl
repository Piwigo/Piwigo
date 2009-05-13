{* $Id: /piwigo/trunk/admin/template/goto/site_manager.tpl 7025 2009-03-09T19:41:45.898712Z nikrou  $ *}
<div class="titrePage">
  <h2>{'Site manager'|@translate}</h2>
</div>

{if not empty($remote_output)}
<div class="remoteOutput">
  <ul>
    {foreach from=$remote_output item=remote_line}
    <li class="{$remote_line.CLASS}">{$remote_line.CONTENT}</li>
    {/foreach}
  </ul>
</div>
{/if}

{if isset($local_listing)}
{'remote_site_local_found'|@translate} {$local_listing.URL}
{if isset($local_listing.CREATE)}
<form action="{$F_ACTION}" method="post">
  <p>
    {'remote_site_local_create'|@translate}:
    <input type="hidden" name="no_check" value="1">
    <input type="hidden" name="galleries_url" value="{$local_listing.URL}">
    <input type="submit" name="submit" value="{'Submit'|@translate}" {$TAG_INPUT_ENABLED}>
  </p>
</form>
{/if}
{if isset($local_listing.U_SYNCHRONIZE)}
&nbsp;<a href="{$local_listing.U_SYNCHRONIZE}" title="{'remote_site_local_update'|@translate}">{'site_synchronize'|@translate}</a>
<br><br>
{/if}
{/if}

{if not empty($sites)}
<table class="table2">
	<tr class="throw">
		<td>{'site_local'|@translate} / {'site_remote'|@translate}</td>
		<td>{'Actions'|@translate}</td>
	</tr>
  {foreach from=$sites item=site name=site}
  <tr style="text-align:left" class="{if $smarty.foreach.site.index is odd}row1{else}row2{/if}"><td>
    <a href="{$site.NAME}">{$site.NAME}</a><br>({$site.TYPE}, {$site.CATEGORIES} {'Categories'|@translate}, {$pwg->l10n_dec('%d element','%d elements',$site.IMAGES)})
  </td><td>
    [<a href="{$site.U_SYNCHRONIZE}" title="{'site_synchronize_hint'|@translate}">{'site_synchronize'|@translate}</a>]
    {if isset($site.U_DELETE)}
      [<a href="{$site.U_DELETE}" onclick="return confirm('{'Are you sure?'|@translate|escape:'javascript'}');"
                title="{'site_delete_hint'|@translate}" {$TAG_INPUT_ENABLED}>{'site_delete'|@translate}</a>]
    {/if}
    {if isset($site.remote)}
      <br>
      [<a href="{$site.remote.U_TEST}" title="{'remote_site_test_hint'|@translate}" {$TAG_INPUT_ENABLED}>{'remote_site_test'|@translate}</a>]
      [<a href="{$site.remote.U_GENERATE}" title="{'remote_site_generate_hint'|@translate}" {$TAG_INPUT_ENABLED}>{'remote_site_generate'|@translate}</a>]
      [<a href="{$site.remote.U_CLEAN}" title="{'remote_site_clean_hint'|@translate}" {$TAG_INPUT_ENABLED}>{'remote_site_clean'|@translate}</a>]
    {/if}
    {if not empty($site.plugin_links)}
        <br>
      {foreach from=$site.plugin_links item=plugin_link}
        [<a href="{$plugin_link.U_HREF}" title='{$plugin_link.U_HINT}' {$TAG_INPUT_ENABLED}>{$plugin_link.U_CAPTION}</a>]
      {/foreach}
    {/if}
  </td></tr>
  {/foreach}
</table>
{/if}

<form action="{$F_ACTION}" method="post">
  <p>
    <label for="galleries_url" >{'site_create'|@translate}</label>
    <input type="text" name="galleries_url" id="galleries_url">
  </p>
  <p>
    <input class="submit" type="submit" name="submit" value="{'Submit'|@translate}" {$TAG_INPUT_ENABLED}>
  </p>
</form>
