{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}

{footer_script}
{* <!-- CATEGORIES --> *}
var categoriesCache = new CategoriesCache({
  serverKey: '{$CACHE_KEYS.categories}',
  serverId: '{$CACHE_KEYS._hash}',
  rootUrl: '{$ROOT_URL}'
});

categoriesCache.selectize(jQuery('[data-selectize=categories]'));

jQuery("#removeAlbumFilter").click(function() {
  jQuery("select[name=cat]")[0].selectize.setValue(null);
  return false;
});

function checkCatFilter() {
  if (jQuery("select[name=cat]").val() == "") {
    jQuery("#removeAlbumFilter").hide();
  }
  else {
    jQuery("#removeAlbumFilter").show();
  }
}

checkCatFilter();
jQuery("select[name=cat]").change(function(){
  checkCatFilter();
});
{/footer_script}

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

    <label>
      {'Album'|translate}<a href="#" id="removeAlbumFilter" class="icon-cancel-circled"></a>
      <select
        data-selectize="categories"
        data-value="{$category|@json_encode|escape:html}"
        placeholder="{'No filter on album. Select one or type to search'|translate}"
        name="cat"
        style="width:400px"
      ></select>
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
