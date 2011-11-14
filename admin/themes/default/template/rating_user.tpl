<form action="{$F_ACTION}" method="GET">
<fieldset>
	<label>{'Sort by'|@translate}
		<select name="order_by">
			{html_options options=$order_by_options selected=$order_by_options_selected}
		</select>
	</label>
	<label>{'Number of rates'|@translate}&gt;
	<input type="text" size="5" name="f_min_rates" value="{$F_MIN_RATES}">
	</label>
	<input type="submit" value="{'Submit'|@translate}">
	</label>
	<input type="hidden" name="page" value="rating_user">
</fieldset>
</form>

{combine_script id='core.scripts' load='async' path='themes/default/js/scripts.js'}
{footer_script}{literal}
function del(elt,uid,aid)
{
	if (!confirm('{'Are you sure?'|@translate|@escape:'javascript'}'))
		return false;
	var tr = elt;
	while ( tr.nodeName != "TR") tr = tr.parentNode;
	tr = jQuery(tr).fadeTo(1000, 0.4);
	var ws = new PwgWS({/literal}'{$ROOT_URL|@escape:javascript}'{literal});
	ws.callService(
		'pwg.rates.delete', {user_id:uid, anonymous_id:aid},
		{
			method: 'POST',
			onFailure: function(num, text) { tr.stop(); tr.fadeTo(0,1); alert(num + " " + text); },
			onSuccess: function(result) { if (result) {tr.remove();} else alert(result); }
		}
	);
	return false;
}
{/literal}{/footer_script}
<table>
<tr class="throw">
	<td>{'Username'|@translate}</td>
	<td>{'Number of rates'|@translate}</td>
	<td>{'Average rate'|@translate}</td>
	<td>StDev</td>
	<td>CV</td>
{foreach from=$available_rates item=rate}
	<td>{$rate}</td>
{/foreach}
	<td></td>
</tr>
{foreach from=$ratings item=rating key=user}
<tr>
	<td>{$user}</td>
	<td>{$rating.count}</td>
	<td>{$rating.avg|@number_format:2}</td>
	<td>{$rating.std|@number_format:3}</td>
	<td>{$rating.cv|@number_format:3}</td>
	{foreach from=$rating.rates item=rates key=rate}
	<td>{if !empty($rates)}
		{capture assign=rate_over}{foreach from=$rates item=rate_arr}<img src="{$image_urls[$rate_arr.id].tn}" alt="thumb-{$rate_arr.id}" title="{$rate_arr.date}"></img>
		{/foreach}{/capture}
		<a class="cluetip" title="{$rate_over|@htmlspecialchars}">{$rates|@count}</a>
		{/if}</td>
	{/foreach}
	<td><a onclick="return del(this,{$rating.uid},'{$rating.aid}');"><img src="{$themeconf.admin_icon_dir}/delete.png" alt="[{'Delete'|@translate}]"></a></td>
</tr>
{/foreach}
</table>

{combine_script id='jquery.cluetip' load='footer' require='jquery' path='themes/default/js/plugins/jquery.cluetip.js'}
{footer_script require='jquery.cluetip'}
{literal}
	jQuery('.cluetip').cluetip({
		width: 350,	splitTitle: '|'
	});
{/literal}
{/footer_script}