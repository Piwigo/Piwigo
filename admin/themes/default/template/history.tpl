
{include file='include/datepicker.inc.tpl'}

{footer_script}
jQuery(function(){ {* <!-- onLoad needed to wait localization loads --> *}
  jQuery('[data-datepicker]').pwgDatepicker();
});

const API_METHOD = "{$API_METHOD}";
{/footer_script}

{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='history' load='footer' path='admin/themes/default/js/history.js'}

{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="admin/themes/default/fontello/css/animation.css" order=10} {* order 10 is required, see issue 1080 *}

<h2>{'History'|@translate} {$TABSHEET_TITLE}</h2>

<form class="filter" method="post" name="filter" action="">
<fieldset class="with-border">
  <legend><span class="icon-filter icon-green"></span>{'Filter'|@translate}</legend>
  <ul>
    <li><label>{'Date'|@translate}</label></li>
    <li>
      <input type="hidden" name="start" value="{$START}">
      <label>
        <i class="icon-calendar"></i>
        <input type="text" data-datepicker="start" data-datepicker-end="end" data-datepicker-unset="start_unset" readonly>
      </label>
      <br>
      <a href="#" class="icon-cancel-circled" id="start_unset">{'unset'|translate}</a>
    </li>
  </ul>
  <ul>
    <li><label>{'End-Date'|@translate}</label></li>
    <li>
      <input type="hidden" name="end" value="{$END}">
      <label>
        <i class="icon-calendar"></i>
        <input type="text" data-datepicker="end" data-datepicker-start="start" data-datepicker-unset="end_unset" readonly>
      </label>
      <br>
      <a href="#" class="icon-cancel-circled" id="end_unset">{'unset'|translate}</a>
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

{* <table class="table2" id="detailedStats">
<thead>
<tr class="throw">
  <th>{'Date'|@translate}</th>
  <th>{'Time'|@translate}</th>
  <th>{'User'|@translate}</th>
  <th>{'IP'|@translate}</th>
  <th>{'Element'|@translate}</th>
  <th>{'Element type'|@translate}</th>
  <th>{'Section'|@translate}</th>
	<th>{'Album'|@translate} / {'Tags'|@translate}</th>
</tr>
</thead>
{if !empty($search_results)}
{foreach from=$search_results item=detail name=res_loop}
<tr class="{if $smarty.foreach.res_loop.index is odd}row1{else}row2{/if}">
  <td class="hour">{$detail.DATE}</td>
  <td class="hour">{$detail.TIME}</td>
  <td>{$detail.USER}</td>
  <td class="IP">{$detail.IP}</td>
  <td>{$detail.IMAGE}</td>
  <td>{$detail.TYPE}</td>
  <td>{$detail.SECTION}</td>
	<td>{$detail.CATEGORY}{$detail.TAGS}</td>
</tr>
{/foreach}
{/if}
</table> *}

<div class="container">
  <div class="tab-title">
    <div class="date-title">
        {'Date'|translate}
    </div>
    <div class="user-title">
        {'User'|translate}
    </div>
    <div class="type-title">
        {'Type'|translate}
    </div>
    <div class="detail-title">
        {'Details'|translate}
    </div>
  </div>

  <div class="tab">
    <div class="loading hide"> 
      <span class="icon-spin6 animate-spin"> </span>
    </div>

    <div class="search-line hide" id="-1">
      <div class="date-section">
        <span class="date-day bold"> July 4th, 2042 </span>
        <span> at <span class="date-hour">23:59:59</span> </span>
      </div>

      <div class="user-section">
        <span class="user-name bold"> Zac le boss </span>
        <span class="user-ip"> 192.168.0.0</span>
      </div>

      <div class="type-section">
        <span class="type-icon"> <i class="icon-file-image"> </i> </span>
        <span class="icon-ellipsis-vert toggle-img-option">
          <div class="img-option">
            <span> info 2</span>
            <span> info 1</span>
          </div>
        </span>

        <div class="type-desc">
          <span class="type-name bold"> WIP </span>
          <span class="type-id"> tag #99 </span>
        </div>
        
      </div>

      <div class="detail-section">
        <div class="detail-item detail-item-1">
          detail 1
        </div>
        <div class="detail-item detail-item-2">
          detail 2
        </div>
        <div class="detail-item detail-item-3">
          detail 3
        </div>
      </div>
    </div>

    {* <div class="search-line" id="-2">
      <div class="date-section">
        <span class="date-day bold"> July 4th, 2042 </span>
        <span class="date-hour"> at 23:59:59</span>
      </div>

      <div class="user-section">
        <span class="user-name bold"> Zac le boss </span>
        <span class="user-ip"> 127.0.0.1 </span>
      </div>

      <div class="type-section">
        <span class="type-icon"> <i class="icon-file-image"> </i> </span>
        <span class="icon-ellipsis-vert toggle-img-option">
          <div class="img-option">
            <span> info 2</span>
            <span> info 1</span>
          </div>
        </span>

        <div class="type-desc">
          <span class="type-name bold"> WIP </span>
          <span class="type-id"> tag #99 </span>
        </div>
        
      </div>

      <div class="detail-section">
        <div class="detail-item detail-item-1">
          detail 1
        </div>
        <div class="detail-item detail-item-2">
          detail 2
        </div>
        <div class="detail-item detail-item-3">
          detail 3
        </div>
      </div>
    </div> *}
  </div>
</div>

{if !empty($navbar) }{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}

{combine_script id='jquery.geoip' load='async' path='admin/themes/default/js/jquery.geoip.js'}

{footer_script}{literal}
jQuery(document).ready( function() {
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
});
{/literal}{/footer_script}

<style>

.container {
  padding: 0 30px;
}

.container,
.tab {
  display: flex;
  flex-direction: column;
}

.tab-title {
  display: flex;
  flex-direction: row;
}

.hide {
  display: none !important;
}

.bold {
  font-weight: bold;
}
.search-line {
  background: #fafafa;
  box-shadow: 0px 2px 4px #00000024;

  display: flex;
  flex-direction: row;

  height: 80px;

  align-items: center;

  margin-bottom: 10px;
}

.tab-title div {
  text-align: left;
  font-size: 1.1em;
  font-weight: bold;

  margin: 0 20px 10px 0px;

  color: #9e9e9e;

  padding-bottom: 5px;
}

.date-title,
.date-section {
  width: 200px;
  text-align: left;
  padding-left: 10px;
}

.user-title,
.user-section {
  width: 200px;
  text-align: left;
  padding-left: 10px;
}

.type-title,
.type-section {
  width: 200px;
  text-align: left;
  padding-left: 10px;
}

.detail-title,
.detail-section {
  width: 200px;
  text-align: left;
  padding-left: 10px;
}

.detail-item {
  background: #f0f0f0f0;
  margin: 0 10px 0 0;
  padding: 3px 6px;
  border-radius: 20px;

  min-width: 50px; 
  max-width: 150px;
  height: 20px;

  text-align: center;

  overflow: hidden;
  text-overflow: ellipsis;
  cursor: default;

  white-space: nowrap;
}

.date-section,
.user-section,
.detail-section {
  display: flex;
  flex-direction: column;
  justify-content: center;
  margin-right: 20px;
  height: 60%;
  border-right: 1px solid #bbb;
}

.detail-section {
  display: flex;
  flex-direction: row;
  align-items: center;
  border-right: none;
}

.type-section {
  display: flex;
  flex-direction: row;
  align-items: center;
  margin-right: 20px;
  height: 60%;
  border-right: 1px solid #bbb;
}

.type-desc {
  display: flex;
  flex-direction: column;
  margin-left: 20px;
}

.toggle-img-option {
  cursor: pointer;
  position: absolute;
  margin-left: 15px;
}

.toggle-img-option::before{
  transform: scale(1.3);
}

.img-option {
  position: absolute;
  display: flex;
  flex-direction: column;
  background: linear-gradient(130deg, #dddddd 0%, #f5f5f5 100%);
  left: 20px;
  top: -80%;
  width: max-content;
  border-radius: 10px;
}

.img-option span {
  padding: 5px 10px;
}

.img-option:after {
  content: " ";
  position: absolute;
  top: 35%;
  left: -10px;
  transform: rotate(270deg);
  border-width: 5px;
  border-style: solid;
  border-color: transparent transparent #dddddd transparent;
}

.img-option span:hover:first-child {
  background: linear-gradient(130deg, #bbbbbb 0%, #e5e5e5 100%);
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
}
.img-option span:hover:last-child {
  background: linear-gradient(130deg, #bbbbbb 0%, #e5e5e5 100%);
  border-bottom-left-radius: 10px;
  border-bottom-right-radius: 10px;
}
</style>