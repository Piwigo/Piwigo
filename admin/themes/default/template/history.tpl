
{include file='include/datepicker.inc.tpl'}

{footer_script}
jQuery(function(){ {* <!-- onLoad needed to wait localization loads --> *}
  jQuery('[data-datepicker]').pwgDatepicker();
});

var dateObj = new Date();
var month = dateObj.getUTCMonth() + 1; //months from 1-12
var day = dateObj.getUTCDate();
var year = dateObj.getUTCFullYear();

if (month < 10) month = "0" + month;
if (day < 10) day = "0" + day;

today = year + "-" + month + "-" + day;
var current_param = {
  start: "",
  end: today,
  types: {
    0: "none",
    1: "picture",
    2: "high",
    3: "other"
  },
  user: "-1",
  image_id: "",
  filename: "",
  ip: "",
  display_thumbnail: "display_thumbnail_classic",
  pageNumber: 0 {* fetch lines from line 0 to line 100*}
}

const API_METHOD = "{$API_METHOD}";
const str_dwld = "{'Downloaded'|translate}";
const str_most_visited = "{'Most visited'|translate}";
const str_best_rated = "{'Best rated'|translate}";
const str_list = "{'Random'|translate}";
const str_favorites = "{'Favorites'|translate}";
{/footer_script}

{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='history' load='footer' path='admin/themes/default/js/history.js'}

{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="admin/themes/default/fontello/css/animation.css" order=10} {* order 10 is required, see issue 1080 *}

{combine_css path="admin/themes/default/css/components/general.css"}

<h2>{'History'|@translate} {$TABSHEET_TITLE}</h2>

<form class="filter" method="post" name="filter" action="">
<fieldset class="history-filter">
  <div class="selectable-filter">
    <div class="filter-part date-start">
      <label>{'Date'|@translate}</label>
        <input type="hidden" name="start" value="{$START}">
        <label>
          <input type="text" data-datepicker="start" data-datepicker-end="end" data-datepicker-unset="start_unset" readonly>
        </label>
        <a href="#" class="icon-cancel-circled" id="start_unset">{'unset'|translate}</a>
    </div>
    <div class="filter-part date-end">
      <label>{'End-Date'|@translate}</label>
        <input type="hidden" name="end" value="{$END}">
        <label>
          <input type="text" data-datepicker="end" data-datepicker-start="start" data-datepicker-unset="end_unset" readonly>
        </label>
        <a href="#" class="icon-cancel-circled" id="end_unset">{'unset'|translate}</a>
    </div>
    <div class="filter-part elem-type advanced-filter-select-container">
      <label>
        {'Element type'|@translate}
        <select name="types[]" class="elem-type-select user-action-select advanced-filter-select">
          {* {html_options values=$type_option_values output=$type_option_values|translate selected=$type_option_selected} *}
          <option value=""></option>
          <option value="visited">{'Visited'|@translate} </option>
          <option value="downloaded">{'Downloaded'|@translate} </option>
        </select>
      </label>
    </div>
  </div>
  <div class="filter-tags">
    <label> Personnalized filters</label>
    <div class="filter-container">
      <div id="default-filter" class="filter-item hide">
        <i class="filter-icon"> </i>
        <span class="filter-title"> test </span><span class="remove-filter">x</span>
      </div>
    </div>
  </div>
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

  {* Used to be copied in js *}
  <div class="search-line hide" id="-1">
      <div class="date-section">
        <i class="date-dwld-icon"> </i>
        <div class="date-infos">
          <span class="date-day bold"> July 4th, 2042 </span>
          <span> at <span class="date-hour">23:59:59</span> </span>
        </div>
      </div>

      <div class="user-section">
        <span class="user-name bold" title="{'Add a filter'}"> Zac le boss </span>
        <span class="user-ip" title="{'Add a filter'}"> 192.168.0.0</span>
      </div>

      <div class="type-section">
        <span class="type-icon"> <i class="icon-file-image"> </i> </span>
        <span class="icon-ellipsis-vert toggle-img-option">
          <div class="img-option">
            <span class="add-img-as-filter"> Add as filter </span>
            <a class="edit-img" href="">{'Edit'|@translate}</a>
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

  <div class="tab">
    <div class="loading hide"> 
      <span class="icon-spin6 animate-spin"> </span>
    </div>
  </div>
  <div class="pagination-container">
    <div class="pagination-arrow left">
      <span class="icon-left-open"></span>
    </div>
    <div class="pagination-item-container">
    
    </div>
    <div class="pagination-arrow rigth">
      <span class="icon-left-open"></span>
    </div>
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

.history-filter {
  background: #f3f3f3;
  display: flex;
  flex-direction: row;
}

.hasDatepicker {
  background: white !important;
  border: solid 1px #D4D4D4;
  padding: 5px 10px;
  max-width: 180px;
}

.filter-part {
  display: flex;
  flex-direction: column;
  margin: 0 10px;
}

.filter-part.elem-type label {
  display: flex;
  flex-direction: column;
}

.elem-type-select{
  background: white !important;
  border: solid 1px #D4D4D4;
  padding: 5px 10px;
  margin-bottom: 5px;
}

.selectable-filter {
  width: calc(50%-20px);
  display: flex;
  flex-direction: row;
  margin-right: 20px;
}

.selectable-filter label {
  white-space: nowrap;
}

.filter-container {
  display: flex;
  flex-direction: row;
}

.filter-item {
  margin: 0 5px;
  white-space: nowrap;

}

.filter-title, .remove-filter, .filter-icon {
  font-weight: bold;
  color: black;
  background: orange;
  padding: 2px 0;
}

.filter-title {
  padding-right: 2px;
}

.remove-filter {
  border-bottom-right-radius: 15px;
  border-top-right-radius: 15px;
  padding-right: 6px;
  padding-left: 4px;
}
.remove-filter:hover {
  background: #ff7700;
  cursor: pointer;
}

.filter-icon {
  padding-left: 2px;
  border-bottom-left-radius: 5px;
  border-top-left-radius: 5px;
}

.user-name, .user-ip {
  cursor: pointer;
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

.line-icon {
  padding: 10px;
  border-radius: 50%;
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
  width: 300px;
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
  width: 250px;
  text-align: left;
  padding-left: 10px;
}

.detail-title,
.detail-section {
  max-width: 500px;
  text-align: left;
  padding-left: 10px;
}

.detail-item {
  margin: 0 10px 0 0;
  padding: 4px 8px;
  border-radius: 5px;

  max-width: 250px;
  height: 20px;

  text-align: center;

  overflow: hidden;
  text-overflow: ellipsis;
  cursor: default;

  white-space: nowrap;
}

.detail-item-1, .detail-item-2, .detail-item-3 {
  background: #f0f0f0f0;
}

.user-section {
  display: flex;
  flex-direction: column;
  justify-content: center;
  margin-right: 20px;
  height: 60%;
  border-right: 1px solid #bbb;
}

.date-section {
  display: flex;
  flex-direction: row;
  margin-right: 20px;
  height: 60%;
  border-right: 1px solid #bbb;
}

.date-infos {
  display: flex;
  flex-direction: column;
  text-align: left;
}

.date-dwld-icon {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 10px;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  margin-right: 10px;
}

.date-dwld-icon::before {
  transform: scale(1.2);
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
  margin-left: 71px;
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

.img-option span, .img-option a {
  padding: 5px 10px;
  text-decoration: none;
}

.img-option a:hover {
  color: #3a3a3a;
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
.img-option a:hover:last-child {
  background: linear-gradient(130deg, #bbbbbb 0%, #e5e5e5 100%);
  border-bottom-left-radius: 10px;
  border-bottom-right-radius: 10px;
}

.type-icon img {
  width: 70px;
  height: 70px;
}
</style>