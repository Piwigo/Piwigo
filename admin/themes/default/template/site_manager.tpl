{footer_script}{literal}
jQuery(document).ready(function(){
  jQuery("#showCreateSite a").click(function(){
    jQuery("#showCreateSite").hide();
    jQuery("#createSite").show();
  });
});
{/literal}{/footer_script}
{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}
{footer_script}
const title_msg = '{'Are you sure you want to delete this site?'|@translate|@escape:'javascript'}';
const confirm_msg = '{"Yes, I am sure"|@translate}';
const cancel_msg = "{"No, I have changed my mind"|@translate}";
$(".delete-site-button").each(function() {
  $(this).pwg_jconfirm_follow_href({
    alert_title: title_msg,
    alert_confirm: confirm_msg,
    alert_cancel: cancel_msg
  });
});
{/footer_script}

{if not empty($remote_output)}
<div class="remoteOutput">
  <ul>
    {foreach from=$remote_output item=remote_line}
    <li class="{$remote_line.CLASS}">{$remote_line.CONTENT}</li>
    {/foreach}
  </ul>
</div>
{/if}

{if not empty($sites)}
<table class="table2">
	<tr class="throw">
		<td>{'Directory'|@translate}</td>
		<td>{'Actions'|@translate}</td>
	</tr>
  {foreach from=$sites item=site name=site}
  <tr style="text-align:left" class="{if $smarty.foreach.site.index is odd}row1{else}row2{/if}"><td>
    <a href="{$site.NAME}">{$site.NAME}</a><br>({$site.TYPE}, {$site.CATEGORIES} {'Albums'|@translate}, {$pwg->l10n_dec('%d photo','%d photos',$site.IMAGES)})
  </td><td>
    [<a href="{$site.U_SYNCHRONIZE}" title="{'update the database from files'|@translate}">{'Synchronize'|@translate}</a>]
    {if isset($site.U_DELETE)}
      [<a class="delete-site-button" href="{$site.U_DELETE}" title="{'delete this site and all its attached elements'|@translate}">{'delete'|@translate}</a>]
    {/if}
    {if not empty($site.plugin_links)}
        <br>
      {foreach from=$site.plugin_links item=plugin_link}
        [<a href="{$plugin_link.U_HREF}" title='{$plugin_link.U_HINT}'>{$plugin_link.U_CAPTION}</a>]
      {/foreach}
    {/if}
  </td></tr>
  {/foreach}
</table>
{/if}

<p id="showCreateSite" style="text-align:left;margin-left:1em;">
  <a href="#">{'create a new site'|@translate}</a>
</p>

<form action="{$F_ACTION}" method="post" id="createSite" style="display:none">
  <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
  <fieldset>
    <legend>{'create a new site'|@translate}</legend>

  <p style="margin-top:0;"><strong>{'Directory'|@translate}</strong>
    <br><input type="text" name="galleries_url" id="galleries_url">
  </p>

  <p class="actionButtons">
    <input class="submit" type="submit" name="submit" value="{'Submit'|@translate}">
  </p>
</form>
