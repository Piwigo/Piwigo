{combine_script id='jquery.dataTables' load='footer' path='themes/default/js/plugins/jquery.dataTables.js'}
{html_style}
.sorting { background: url({$ROOT_URL}themes/default/js/plugins/datatables/images/sort_both.png) no-repeat center right; cursor:pointer; }
.sorting_asc { background: url({$ROOT_URL}themes/default/js/plugins/datatables/images/sort_asc.png) no-repeat center right; }
.sorting_desc { background: url({$ROOT_URL}themes/default/js/plugins/datatables/images/sort_desc.png) no-repeat center right; }

.sorting, .sorting_asc, .sorting_desc { 
	padding: 3px 18px 3px 10px;
}
.sorting_asc_disabled { background: url({$ROOT_URL}themes/default/js/plugins/datatables/images/sort_asc_disabled.png) no-repeat center right; }
.sorting_desc_disabled { background: url({$ROOT_URL}themes/default/js/plugins/datatables/images/sort_desc_disabled.png) no-repeat center right; }

.dtBar {
	text-align:left;
	padding-left: 20px;
}
{/html_style}

<h2>{$ratings|@count} {'Users'|@translate}</h2>

<form action="{$F_ACTION}" method="GET">
<fieldset>
<noscript>
	<label>{'Sort by'|@translate}
		<select name="order_by">
			{html_options options=$order_by_options selected=$order_by_options_selected}
		</select>
	</label>
</noscript>
	<label>{'Number of rates'|@translate}&gt;
	<input type="text" size="5" name="f_min_rates" value="{$F_MIN_RATES}">
	</label>
	<label>{'Consensus deviation'|@translate}
	<input type="text" size="5" name="consensus_top_number" value="{$CONSENSUS_TOP_NUMBER}">
	{'Best rated'|@translate}
	</label>

	<input type="submit" value="{'Submit'|@translate}">
	</label>
	<input type="hidden" name="page" value="rating_user">
</fieldset>
</form>

{combine_script id='core.scripts' load='async' path='themes/default/js/scripts.js'}
{footer_script}
var oTable = jQuery('#rateTable').dataTable({
	sDom : '<"dtBar"f>rt',
	bPaginate: false,
	aaSorting: [[5,'desc']],
	aoColumnDefs: [
		/*{
			aTargets: ["dtc_user"]
		},*/
		{
			aTargets: ["dtc_date"],
			asSorting: ["desc","asc"]
		},
		{
			aTargets: ["dtc_stat"],
			asSorting: ["desc","asc"],
			bSearchable: false
		},
		{
			aTargets: ["dtc_rate"],
			asSorting: ["desc","asc"],
			bSearchable: false
		},
		{
			aTargets: ["dtc_del"],
			bSortable: false,
			bSearchable: false
		}
	]
});

function del(elt,uid,aid){
	if (!confirm('{'Are you sure?'|@translate|@escape:'javascript'}'))
		return false;
	var tr = elt;
	while ( tr.nodeName != "TR") tr = tr.parentNode;
	tr = jQuery(tr).fadeTo(1000, 0.4);

	(new PwgWS('{$ROOT_URL|@escape:javascript}')).callService(
		'pwg.rates.delete', { user_id:uid, anonymous_id:aid},
		{
			method: 'POST',
			onFailure: function(num, text) { tr.stop(); tr.fadeTo(0,1); alert(num + " " + text); },
			onSuccess: function(result){
				if (result)
					oTable.fnDeleteRow(tr[0]);
				else 
					alert(result); 
			}
		}
	);
	
	return false;
}
{/footer_script}
<table id="rateTable">
<thead>
<tr class="throw">
	<td class="dtc_user">{'Username'|@translate}</td>
	<td class="dtc_date">{'Last'|@translate}</td>
	<td class="dtc_stat">{'Number of rates'|@translate}</td>
	<td class="dtc_stat">{'Average rate'|@translate}</td>
	<td class="dtc_stat">{'Variation'|@translate}</td>
	<td class="dtc_stat">{'Consensus deviation'|@translate|@replace:' ':'<br>'}</td>
	<td class="dtc_stat">{'Consensus deviation'|@translate|@replace:' ':'<br>'} {$CONSENSUS_TOP_NUMBER}</td>
{foreach from=$available_rates item=rate}
	<td class="dtc_rate">{$rate}</td>
{/foreach}
	<td class="dtc_del"></td>
</tr>
</thead>
{foreach from=$ratings item=rating key=user}
<tr>
	<td>{$user}</td>
	<td>{$rating.last_date}</td>
	<td>{$rating.count}</td>
	<td>{$rating.avg|@number_format:2}</td>
	<td>{$rating.cv|@number_format:3}</td>
	<td>{$rating.cd|@number_format:3}</td>
	<td>{if !empty($rating.cdtop)}{$rating.cdtop|@number_format:3}{/if}</td>
	{foreach from=$rating.rates item=rates key=rate}
	<td>{if !empty($rates)}
		{capture assign=rate_over}{foreach from=$rates item=rate_arr}<img src="{$image_urls[$rate_arr.id].tn}" alt="thumb-{$rate_arr.id}" title="{$rate_arr.date}"></img>
		{/foreach}{/capture}
		<a class="cluetip" title="|{$rate_over|@htmlspecialchars}">{$rates|@count}</a>
		{/if}</td>
	{/foreach}
	<td><a onclick="return del(this,{$rating.uid},'{$rating.aid}');" class="icon-trash"></a></td>
</tr>
{/foreach}
</table>

{combine_script id='jquery.cluetip' load='footer' require='jquery' path='themes/default/js/plugins/jquery.cluetip.js'}
{footer_script require='jquery.cluetip'}
jQuery(document).ready(function(){ldelim}
	jQuery('.cluetip').cluetip({ldelim}
		width: {$TN_WIDTH}, showTitle:false, splitTitle: '|'
	});
})
{/footer_script}