
{include file='include/datepicker.inc.tpl'}

{footer_script}
jQuery(function(){ {* <!-- onLoad needed to wait localization loads --> *}
  jQuery('[data-datepicker]').pwgDatepicker();
});

var dateObj = new Date();
var month = dateObj.getUTCMonth() + 1; //months from 1-12
var day = dateObj.getUTCDate();
var year = dateObj.getUTCFullYear();

var filter_user_name = "{$USER_NAME}";

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
  user_id: {$USER_ID},
  image_id: {if isset($IMAGE_ID)}"{$IMAGE_ID}"{else}""{/if},
  filename: "",
  ip: {if isset($IP)}"{$IP}"{else}""{/if},
  display_thumbnail: "display_thumbnail_classic",
  pageNumber: 0 {* fetch lines from line 0 to line 100*}
}

const API_METHOD = "{$API_METHOD}";
const str_dwld = "{'Downloaded'|translate}";
const str_most_visited = "{'Most visited'|translate}";
const str_best_rated = "{'Best rated'|translate}";
const str_list = "{'Random photo'|translate}";
const str_favorites = "{'Your favorites'|translate}";
const str_recent_cats = "{'Recent albums'|translate}";
const str_recent_pics = "{'Recent photos'|translate}";
const str_memories = "{'Memories'|translate}";
const str_no_longer_exist_photo = "{'This photo no longer exists'|@translate}";
const str_tags = "{'Tags'|translate}";
const unit_MB = "{"%s MB"|@translate}";
const str_guest = '{'guest'|@translate}';
const str_contact_form = '{'Contact Form'|@translate}';
const guest_id = {$guest_id};
{/footer_script}

{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='history' load='footer' path='admin/themes/default/js/history.js'}

{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="admin/themes/default/fontello/css/animation.css" order=10} {* order 10 is required, see issue 1080 *}

<form class="filter" method="post" name="filter" action="">
<fieldset class="history-filter">
  <div class="selectable-filter">
    <div class="filter-part date-start">
      <label>{'Start-Date'|@translate}</label>
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
        {'Action'|@translate}
        <select name="types[]" class="elem-type-select user-action-select advanced-filter-select">
          <option value=""></option>
          <option value="visited">{'Visited'|@translate} </option>
          <option value="downloaded">{'Downloaded'|@translate} </option>
        </select>
      </label>
    </div>
  </div>
  <div class="filter-tags">
    <label>{'Additional filters'|translate}</label>
    <div class="filter-container">
      <div id="default-filter" class="filter-item hide">
        <span class="filter-icon"></span><span class="filter-title"> test </span><span class="remove-filter icon-cancel"></span>
      </div>
    </div>
  </div>
  <div class="refresh-results icon-arrows-cw tiptip" title="{'Refresh'|translate}">
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

{* Used to be copied in JS *}
<span id="-2" class="icon-green summary-user-item hide">
  <i class="icon-user-1"> </i>
  <i class="icon-plus-circled"> </i> 
  <span class="user-item-name"> User test </span>
</span>
{*  *}

<div class="search-summary">
  <div class="summary-lines">
    <span class="icon-yellow icon-menu summary-icons"></span>
    <span class="summary-data"> </span>
  </div>
  <div class="summary-weight">
    <span class="icon-purple icon-download summary-icons"></span>
    <span class="summary-data"> </span>
  </div>
  <div class="summary-users">
    <span class="icon-green icon-user-1 summary-icons"></span>
    <span class="summary-data"> </span>
    <div class="user-list">
    </div>
    <span class="user-dot icon-green summary-user-item">...</span>
  </div>
  <div class="summary-guests">
    <span class="icon-blue icon-user-secret summary-icons"></span>
    <span class="summary-data"> </span>
    <span class="addGuestFilter"> </span>
  </div>
</div>

{if !empty($navbar) }{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}

<div class="loading hide"> 
  <span class="icon-spin6 animate-spin"> </span>
</div>
<div class="noResults">
  {'No results'|@translate}
</div>
<div class="container">
  <div class="tab-title">
    <div class="date-title">
        {'Date'|translate}
    </div>
    <div class="user-title">
        {'User'|translate}
    </div>
    <div class="type-title">
        {'Object'|translate}
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
        <span class="user-name bold" title="{'Add as filter'|translate}"> Zac le boss <i class="add-filter icon-plus-circled"></i></span>
        <span class="user-ip" title="{'Add as filter'|translate}"> 192.168.0.0 <i class="add-filter icon-plus-circled"></i></span>
      </div>

      <div class="type-section">
        <span class="type-icon"> <i class="icon-file-image"> </i> </span>
        <span class="icon-ellipsis-vert toggle-img-option">
          <div class="img-option">
            <a class="add-img-as-filter icon-filter"> {'Add as filter'|translate} </a>
            <a class="edit-img icon-pencil" href="" target="_blank">{'Edit'|@translate}</a>
          </div>
        </span>

        <div class="type-desc">
          <span class="type-name bold"> WIP </span>
          <span class="type-id"> tag #99 </span>
        </div>
        
      </div>

      <div class="detail-section">
        <div class="detail-item detail-item-1 hide">
          detail 1
        </div>
        <div class="detail-item detail-item-2 hide">
          detail 2
        </div>
        <div class="detail-item detail-item-3 hide">
          detail 3
        </div>
      </div>
    </div>

  <div class="tab">
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

.notClickable {
  opacity: 0.5;
}

.container {
  padding: 0 20px;
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

.noResults {
  font-size: 14px;
  display: none;
  font-weight: bold;
  color: #777;
  margin-top: 20px;
}

.bold {
  font-weight: bold;
}

.refresh-results {
  margin-left: auto;
  display: flex;
  align-items: center;
  cursor: pointer;
  font-size: 17px;
}

.history-filter {
  display: flex;
  flex-direction: row;
}

.hasDatepicker {
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

.filter-tags {
  display: flex;
  flex-direction: column;
}

.filter-container {
  display: flex;
  flex-direction: row;
}

.filter-item {
  margin: 10px 5px 0 0;
  white-space: nowrap;
  cursor: default;
}

.filter-title {
  padding-right: 2px;
}

.remove-filter {
  border-bottom-right-radius: 25px;
  border-top-right-radius: 25px;
  padding-right: 10px;
  padding-left: 5px;
}
.remove-filter:hover {
  cursor: pointer;
}

.filter-icon {
  padding-left: 10px;
  border-bottom-left-radius: 25px;;
  border-top-left-radius: 25px;;
}

.user-name, .user-ip {
  cursor: pointer;
  max-width: fit-content;
}

.search-line {
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
  white-space: nowrap;
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
  text-align: left;
  padding-left: 10px;
}
.date-title {
  width: 300px;
}

.user-title,
.user-section {
  text-align: left;
  padding-left: 10px;
}
.user-title {
  width: 200px;
}

.type-title,
.type-section {
  text-align: left;
  padding-left: 10px;
}
.type-title {
  width: 250px;
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
.detail-item::before {
  margin: 0 5px 0 0px;
}

.add-filter {
  display: none;
}

.user-name:hover .add-filter {
  display: inline;
  margin-left: 4px;
}

.user-ip:hover .add-filter {
  display: inline;
  margin-left: 4px;
}

.user-section {
  display: flex;
  flex-direction: column;
  justify-content: center;
  height: 60%;
  width: 220px;
}

.date-section {
  display: flex;
  flex-direction: row;
  height: 60%;
  width: 320px;
  align-items: center;
}

.date-infos {
  display: flex;
  flex-direction: column;
  text-align: left;
  justify-content: center;
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
  height: 60%;
  width: 270px;
}

.type-desc {
  display: flex;
  flex-direction: column;
  margin-left: 20px;
}

.type-name {
  overflow: hidden;
  max-height: 80px;
  max-width: 190px;
  vertical-align: middle;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
}

.toggle-img-option {
  cursor: pointer;
  position: absolute;
  margin-left: 51px;
}

.toggle-img-option::before{
  transform: scale(1.3);
}

.img-option {
  position: absolute;
  display: flex;
  flex-direction: column;
  background: linear-gradient(130deg, #ff7700 0%, #ffa744 100%);
  left: 20px;
  top: -80%;
  width: max-content;
  border-radius: 10px;
}

.img-option span, .img-option a {
  padding: 5px 10px;
  text-decoration: none;
  color: white;
  font-weight: 600;
}

.img-option:after {
  content: " ";
  position: absolute;
  top: 35%;
  left: -10px;
  transform: rotate(270deg);
  border-width: 5px;
  border-style: solid;
  border-color: transparent transparent #ff7700 transparent;
}

.img-option a:first-child::before {
  margin-right: -1px;
}

.img-option a:hover:first-child {
  color: white;
  background-color: #00000012;
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
}

.img-option a:hover:last-child {
  color: white;
  background-color: #00000012;
  border-bottom-left-radius: 10px;
  border-bottom-right-radius: 10px;
}

.type-icon img {
  width: 50px;
  height: 50px;
  margin-bottom: -5px;
}

/* Summary */

.search-summary {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: center;

  width: 100%;
  height: 100px;
}

.summary-lines,
.summary-weight,
.summary-users,
.summary-guests {
  white-space: nowrap;
}

.summary-icons {
  padding: 10px;
  border-radius: 50%;
  margin: 0 5px 0 15px;
}

.summary-lines .summary-icons {
  margin-left: 5px;
}

.summary-users {
  display: flex;
  flex-direction: row;
  align-items: center;
}

.summary-user-item {
  padding: 3px 6px;
  border-radius: 20px;
  margin-left: 5px;
  cursor: pointer;
}

.summary-user-item .icon-plus-circled {
  display: none;
}

.summary-user-item:hover .icon-plus-circled {
  display: inline-block;
}

.summary-user-item:hover .icon-user-1 {
  display: none;
}

.summary-users .summary-data {
  margin: 0 5px 0 0 !important;
}

.summary-data {
  font-weight: bold;
}

{* .user-list {
  margin-right: 10px;
} *}

#start_unset, #end_unset {
  text-decoration: none;
}

#start_unset:hover, #end_unset:hover {
  color: #ffa500;
}

.loading {
  font-size: 25px;
}
</style>