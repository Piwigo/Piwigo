
{include file='include/datepicker.inc.tpl'}

{footer_script}{literal}
	pwg_initialization_datepicker("#start_day", "#start_month", "#start_year", "#start_linked_date", null, null, "#end_linked_date");
	pwg_initialization_datepicker("#end_day", "#end_month", "#end_year", "#end_linked_date", null, "#start_linked_date", null);
{/literal}{/footer_script}

<h2>{'History'|@translate} {$TABSHEET_TITLE}</h2>

<form class="filter" method="post" name="filter" action="{$F_ACTION}">
<fieldset>
  <legend>{'Filter'|@translate}</legend>
  <ul>
    <li><label>{'Date'|@translate}</label></li>
    <li>
      <select id="start_day" name="start_day">
        <option value="0">--</option>
        {section name=day start=1 loop=32}
        <option value="{$smarty.section.day.index}" {if $smarty.section.day.index==$START_DAY_SELECTED}selected="selected"{/if}>{$smarty.section.day.index}</option>
        {/section}
      </select>
      <select id="start_month" name="start_month">
      {html_options options=$month_list selected=$START_MONTH_SELECTED}
      </select>
      <input id="start_year" name="start_year" value="{$START_YEAR}" type="text" size="4" maxlength="4" >
      <input id="start_linked_date" name="start_linked_date" type="hidden" size="10" disabled="disabled">
    </li>
  </ul>
  <ul>
    <li><label>{'End-Date'|@translate}</label></li>
    <li>
      <select id="end_day" name="end_day">
        <option value="0">--</option>
        {section name=day start=1 loop=32}
        <option value="{$smarty.section.day.index}" {if $smarty.section.day.index==$END_DAY_SELECTED}selected="selected"{/if}>{$smarty.section.day.index}</option>
        {/section}
      </select>
      <select id="end_month" name="end_month">
      {html_options options=$month_list selected=$END_MONTH_SELECTED}
      </select>
      <input id="end_year" name="end_year" value="{$END_YEAR}" type="text" size="4" maxlength="4" >
      <input id="end_linked_date" name="end_linked_date" type="hidden" size="10" disabled="disabled">
    </li>
  </ul>

  <label>
    {'Element type'|@translate}
    <select name="types[]" multiple="multiple" size="4">
      {html_options values=$type_option_values output=$type_option_values|translate selected=$type_option_selected}
    </select>
  </label>

  <label>
    {'User'|@translate}
    <select name="user">
      <option value="-1">------------</option>
      {html_options options=$user_options selected=$user_options_selected}
    </select>
  </label>

  <label>
    {'Image id'|@translate}
    <input name="image_id" value="{$IMAGE_ID}" type="text" size="5">
  </label>

  <label>
    {'File name'|@translate}
		<input name="filename" value="{$FILENAME}" type="text" size="12">
  </label>

	<label>
		{'IP'|@translate}
		<input name="ip" value="{$IP}" type="text" size="12">
	</label>

  <label>
    {'Thumbnails'|@translate}
    <select name="display_thumbnail">
      {html_options options=$display_thumbnails selected=$display_thumbnail_selected}
    </select>
  </label>

  <input type="submit" name="submit" value="{'Submit'|@translate}">
</fieldset>
</form>

{if isset($search_summary)}
<fieldset>
  <legend>{'Summary'|@translate}</legend>

  <ul>
    <li>{$search_summary.NB_LINES}, {$search_summary.FILESIZE}</li>
    <li>
      {$search_summary.USERS}
      <ul>
        <li>{$search_summary.MEMBERS}</li>
        <li>{$search_summary.GUESTS}</li>
      </ul>
    </li>
  </ul>
</fieldset>
{/if}

{if !empty($navbar) }{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}

<table class="table2" id="detailedStats">
<tr class="throw">
  <th>{'Date'|@translate}</th>
  <th>{'Time'|@translate}</th>
  <th>{'User'|@translate}</th>
  <th>{'IP'|@translate}</th>
  <th>{'Element'|@translate}</th>
  <th>{'Element type'|@translate}</th>
  <th>{'Section'|@translate}</th>
  <th>{'Album'|@translate}</th>
  <th>{'Tags'|@translate}</th>
</tr>
{if !empty($search_results) }
{foreach from=$search_results item=detail name=res_loop}
<tr class="{if $smarty.foreach.res_loop.index is odd}row1{else}row2{/if}">
  <td class="hour">{$detail.DATE}</td>
  <td class="hour">{$detail.TIME}</td>
  <td>{$detail.USER}</td>
  <td class="IP">{$detail.IP}</td>
  <td>{$detail.IMAGE}</td>
  <td>{$detail.TYPE}</td>
  <td>{$detail.SECTION}</td>
  <td>{$detail.CATEGORY}</td>
  <td>{$detail.TAGS}</td>
</tr>
{/foreach}
{/if}
</table>

{if !empty($navbar) }{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}

{combine_script id='jquery.geoip' load='async' path='admin/themes/default/js/jquery.geoip.js'}

{footer_script}{literal}
jQuery(".IP").one( "mouseenter", function(){
	var that = $(this);
	that
		.data("isOver", true)
		.one("mouseleave", function() {
			that.removeData("isOver");
		});
	GeoIp.get( that.text(), function(data) {
		if (!data.fullName) return;

    var content = data.fullName;
    if (data.latitude && data.region_name) {
      content += '<br><a class="ipGeoOpen" data-lat="'+data.latitude+'" data-lon="'+data.longitude+'"';
      content += ' href="#">show on a Google Map</a>';
    }

		that.tipTip( {
			content: content,
      keepAlive: true,
      defaultPosition: "right",
      maxWidth: 320,
			}	);
		if (that.data("isOver"))
			that.trigger("mouseenter");
	});
} );

jQuery(document).on('click', '.ipGeoOpen',  function() {
  var lat = jQuery(this).data("lat");
  var lon = jQuery(this).data("lon");
  var parent = jQuery(this).parent();
  jQuery(this).remove();

  var append = '<br><img width=300 height=220 src="http://maps.googleapis.com/maps/api/staticmap';
  append += '?sensor=false&size=300x220&zoom=6&markers=size:tiny%7C' + lat + ',' + lon + '">';

  jQuery(parent).append(append);
  return false;
});
{/literal}{/footer_script}