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
	padding: 10px 0 10px 20px
}
.dtBar DIV{
	display:inline;
	padding-right: 5px;
}

.dataTables_paginate A {
	padding-left: 3px;
}

.ui-tooltip {
	padding: 8px;
	position: absolute;
	z-index: 9999;
	max-width: {3*$TN_WIDTH}px;
	-webkit-box-shadow: 0 0 5px #aaa;
	box-shadow: 0 0 5px #aaa;
}
body .ui-tooltip {
	border-width: 2px;
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

  <div style="clear:both"></div>

  <p style="margin:10px 0 0 0">
    <button name="submit" type="submit" class="buttonLike">
      <i class="icon-filter"></i> {'Submit'|translate}
    </button>
  </p>
	<input type="hidden" name="page" value="rating_user">
</fieldset>
</form>

{combine_script id='core.scripts' load='async' path='themes/default/js/scripts.js'}
{combine_script id='jquery.geoip' load='async' path='admin/themes/default/js/jquery.geoip.js'}
{footer_script}
jQuery('#rateTable').dataTable({
	dom : '<"dtBar"filp>rt<"dtBar"ilp>',
	pageLength: 100,
	lengthMenu: [ [25, 50, 100, 500, -1], [25, 50, 100, 500, "All"]],
	sorting: [], //[[1,'desc']],
	autoWidth: false,
	sortClasses: false,
	columnDefs: [
		{
			aTargets: ["dtc_user"],
			sType: "string",
			sClass: null
		},
		{
			aTargets: ["dtc_date"],
			asSorting: ["desc","asc"],
			sType: "string",
			sClass: null
		},
		{
			aTargets: ["dtc_stat"],
			asSorting: ["desc","asc"],
			bSearchable: false,
			sType: "numeric",
			sClass: null
		},
		{
			aTargets: ["dtc_rate"],
			asSorting: ["desc","asc"],
			bSearchable: false,
			sType: "html",
			sClass: null
		},
		{
			aTargets: ["dtc_del"],
			bSortable: false,
			bSearchable: false,
			sType: "string",
			sClass: null
		}
	]
});

var oTable = jQuery('#rateTable').DataTable();

function uidFromCell(cell){
	var tr = cell;
	while ( tr.nodeName != "TR") tr = tr.parentNode;
	return $(tr).data("usr");
}

{* -----DELETE----- *}
$(document).ready( function(){
	$("#rateTable").on( "click", ".del", function(e) {
		e.preventDefault();
		if (!confirm('{'Are you sure?'|@translate|@escape:'javascript'}'))
			return;
		var cell = e.target.parentNode,
			tr = cell;
		while ( tr.nodeName != "TR") tr = tr.parentNode;
		tr = jQuery(tr).fadeTo(1000, 0.4);

		var data=uidFromCell(cell);
		
		(new PwgWS('{$ROOT_URL|@escape:javascript}')).callService(
			'pwg.rates.delete', { user_id:data.uid, anonymous_id:data.aid},
			{
				method: 'POST',
				onFailure: function(num, text) { tr.stop(); tr.fadeTo(0,1); alert(num + " " + text); },
				onSuccess: function(result){
					if (result)
						oTable.row(tr[0]).remove().draw();
					else 
						alert(result); 
				}
			}
		);

	});
});

{/footer_script}
<table id="rateTable">
<thead>
<tr class="throw">
	<th class="dtc_user">{'Username'|@translate}</th>
	<th class="dtc_date">{'Last'|@translate}</th>
	<th class="dtc_stat">{'Number of rates'|@translate}</th>
	<th class="dtc_stat">{'Average rate'|@translate}</th>
	<th class="dtc_stat">{'Variation'|@translate}</th>
	<th class="dtc_stat">{'Consensus deviation'|@translate|@replace:' ':'<br>'}</th>
	<th class="dtc_stat">{'Consensus deviation'|@translate|@replace:' ':'<br>'} {$CONSENSUS_TOP_NUMBER}</th>
{foreach from=$available_rates item=rate}
	<th class="dtc_rate">{$rate}</th>
{/foreach}
	<th class="dtc_del"></th>
</tr>
</thead>
{foreach from=$ratings item=rating key=user}
<tr data-usr='{ldelim}"uid":{$rating.uid},"aid":"{$rating.aid}"}'>
{strip}
<td class=usr>{$user}</td><td title="First: {$rating.first_date}">{$rating.last_date}</td>
<td>{$rating.count}</td><td>{$rating.avg|@number_format:2}</td>
<td>{$rating.cv|@number_format:3}</td><td>{$rating.cd|@number_format:3}</td><td>{if !empty($rating.cdtop)}{$rating.cdtop|@number_format:3}{/if}</td>
{foreach from=$rating.rates item=rates key=rate}
<td>{if !empty($rates)}
{capture assign=rate_over}{foreach $rates as $rate_arr}{if $rate_arr@index>29}{break}{/if}<img src="{$image_urls[$rate_arr.id].tn}" alt="thumb-{$rate_arr.id}" width="{$TN_WIDTH}" height="{$TN_WIDTH}">{/foreach}{/capture}
<a title="{$rate_over|@htmlspecialchars}">{$rates|@count}</a>
{/if}</td>
{/foreach}
<td><a class="del icon-trash"></a></td>
</tr>
{/strip}
{/foreach}
</table>

{combine_script id='jquery.ui.tooltip' load='footer'}
{footer_script require='jquery.ui.tooltip'}
jQuery(document).ready(function(){
	jQuery("#rateTable").tooltip({
		items: ".usr,[title]",
		content: function(callback) {
			var t = $(this).attr("title");
			if (t)
				return t;
			var that = $(this),
				udata = uidFromCell(this);
			if (!udata.aid)
				return;
			that
				.data("isOver", true)
				.one("mouseleave", function() {
					that.removeData("isOver");
				});

			GeoIp.get( udata.aid + ".1", function(data) {
				if (!data.fullName) return;
				var content = data.fullName;
				if (data.latitude && data.region_name) {
					content += "<br><img width=300 height=220 src=\"http://maps.googleapis.com/maps/api/staticmap?sensor=false&size=300x220&zoom=6"
						+ "&markers=size:tiny%7C" + data.latitude + "," + data.longitude
						+ "\">";
				}
				if (that.data("isOver"))
					callback(content);
			});
		}
	});
})
{/footer_script}