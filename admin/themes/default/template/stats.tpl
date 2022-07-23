{footer_script}
var str_number_page_visited = "{'Page Visited'|@translate}";
var str_number_page_visited_with_year = "{'Page Visited in %s'|@translate}";
var str_tooltip_format = {
  "years":"YYYY",
  "months":"MMMM YYYY",
  "days":"DD MMM",
  "hours":"LT"
};
var str_unit_format = {
  "day":"dddd",
  "month":"MMM YYYY"
}
var str_avg = "{'Average last 12 months'|@translate}";
var str_months_tosplit = "{$month_labels}";
var str_months = str_months_tosplit.split('~');
moment.locale("{$langCode}");
{/footer_script}

{combine_script id='chart.js' load='footer' path='themes/default/js/plugins/Chart.min.js'}
{combine_css path="themes/default/js/plugins/Chart.min.css"}

{combine_script id='moment-with-locales.js' path='themes/default/js/plugins/moment-with-locales.min.js'}

{combine_script id='stats' load='footer' path='admin/themes/default/js/stats.js'}

<div class="stat-compare-mode">
  <label class="switch">
      <input type="checkbox" id="toggleCompareMode">
      <span class="slider round" checked="false"></span>
  </label>
  {'Compare mode'|@translate}
</div>

<div id="data" 
  data-hours='{json_encode($lastHours)}' 
  data-days='{json_encode($lastDays)}' 
  data-months='{json_encode($lastMonths)}' 
  data-years='{json_encode($lastYears)}'
  data-compare-years='{json_encode($compareYears)}'
  data-month-stats='{json_encode($monthStats)}' 
  ></div>
<div class="stat-legend-container">
  <div class="stat-data-selector">
    <input type="radio" id="hours-selector" name="stat-data-type">
    <label for="hours-selector" data-value="hours">{"Hour"|@translate}</label>
    <input type="radio" id="days-selector" name="stat-data-type" checked>
    <label for="days-selector" data-value="days">{"Day"|@translate}</label>
    <input type="radio" id="months-selector" name="stat-data-type">
    <label for="months-selector" data-value="months">{"Month"|@translate}</label>
    <input type="radio" id="years-selector" name="stat-data-type">
    <label for="years-selector" data-value="years">{"Year"|@translate}</label>
  </div>
</div>

<div class="stat-graph-container">
  <canvas id="stat-graph" width="400" height="150" role="img"><p>Your browser does not support the canvas element.</p></canvas>
</div>
