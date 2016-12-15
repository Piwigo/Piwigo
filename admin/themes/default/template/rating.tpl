<h2>{$NB_ELEMENTS} {'Photos'|@translate}</h2>

<form action="{$F_ACTION}" method="GET" class="filter">
  <fieldset>
    <legend>{'Filter'|@translate}</legend>

    <label>
      {'Sort by'|@translate}
      <select name="order_by">
        {html_options options=$order_by_options selected=$order_by_options_selected}
      </select>
    </label>

    <label>
      {'Users'|@translate}
      <select name="users">
        {html_options options=$user_options selected=$user_options_selected}
      </select>
    </label>

    <label>
      {'Number of items'|@translate}
      <input type="text" name="display" size="2" value="{$DISPLAY}">
    </label>

    <div style="clear:both"></div>

    <p style="margin:10px 0 0 0">
      <button name="submit" type="submit" class="buttonLike">
        <i class="icon-filter"></i> {'Submit'|translate}
      </button>
    </p>
    <input type="hidden" name="page" value="rating">
  </fieldset>
</form>

{if !empty($navbar) }{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}

<table width="99%">
<tr class="throw">
  <td>{'File'|@translate}</td>
  <td>{'Number of rates'|@translate}</td>
	<td>{'Rating score'|@translate}</td>
  <td>{'Average rate'|@translate}</td>
  <td>{'Sum of rates'|@translate}</td>
  <td>{'Rate'|@translate}/{'Username'|@translate}/{'Rate date'|@translate}</td>
  <td></td>
</tr>
{foreach from=$images item=image name=image}
<tr valign="top" class="{if $smarty.foreach.image.index is odd}row1{else}row2{/if}">
	<td><a href="{$image.U_URL}"><img src="{$image.U_THUMB}" alt="{$image.FILE}" title="{$image.FILE}"></a></td>
	<td><strong>{$image.NB_RATES}/{$image.NB_RATES_TOTAL}</strong></td>
	<td><strong>{$image.SCORE_RATE}</strong></td>
	<td><strong>{$image.AVG_RATE}</strong></td>
	<td style="border-right:1px solid" ><strong>{$image.SUM_RATE}</strong></td>
	<td>
		<table style="width:100%">
{foreach from=$image.rates item=rate name=rate}
<tr>
	<td>{$rate.rate}</td>
	<td><b>{$rate.USER}</b></td>
	<td>{$rate.date}</td>
	<td><a onclick="return del(this,{$image.id},{$rate.user_id}{if !empty({$rate.anonymous_id})},'{$rate.anonymous_id}'{/if})" class="icon-trash"> </a></td>
</tr>
{/foreach}{*rates*}
		</table>
	</td>
</tr>
{/foreach}{*images*}
</table>
{combine_script id='core.scripts' load='async' path='themes/default/js/scripts.js'}
{footer_script}
function del(node,id,uid,aid){
	var tr = jQuery(node).parents("tr").first().fadeTo(1000, 0.4),
		data = {
			image_id: id,
			user_id: uid
		};
	if (aid)
		data.anonymous_id = aid;

	(new PwgWS('{$ROOT_URL|@escape:javascript}')).callService(
		'pwg.rates.delete', data,
		{
			method: 'POST',
			onFailure: function(num, text) { tr.stop(); tr.fadeTo(0,1); alert(num + " " + text); },
			onSuccess: function(result){
				if (result)
					tr.remove();
				else 
					alert(result); 
			}
		}
	);
	return false;
}
{/footer_script}

{if !empty($navbar)}{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}
