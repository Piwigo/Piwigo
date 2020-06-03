/*-------
Data Get
-------*/
data = {};
data["hours"] = $("#data").data("hours")[0];
data["days"] = $("#data").data("days")[0];
data["months"] = $("#data").data("months")[0];
data["years"] = $("#data").data("years")[0];

data_unit = {
  "hours":"day",
  "days":"month",
  "months": "year",
  "years": "year"
}

/*-------
Creating graph
-------*/
var ctx = document.getElementById('stat-graph').getContext('2d');
//Create the gradient under the curve
var gradient = ctx.createLinearGradient(0,400, 0,0);
gradient.addColorStop(0, 'rgba(255,119,0,0)');
gradient.addColorStop(1, 'rgba(255,119,0,1)');

//Setup the graph
Chart.defaults.global.legend.display = false; 
Chart.defaults.global.elements.point.radius = 0.1;
Chart.defaults.global.elements.point.hitRadius = 10
Chart.defaults.global.defaultFontSize = 14;
Chart.defaults.global.defaultFontColor = '#888';
Chart.defaults.global.tooltips.mode = 'index';
Chart.defaults.global.tooltips.intersect = false;
Chart.defaults.global.legend.onClick = null;
var statGraph = new Chart(ctx, {
  type: 'line',
  maintainAspectRatio: false,
  options: {
    scales: {
        xAxes: [{
        type: 'time',
        time: {
        tooltipFormat: 'll'
      },
      gridLines: {
      display: false
      }
      }],
    }
  }
});

//Line options
var displayOptions = {
  backgroundColor: gradient,
  borderColor: 'rgba(255,119,0,1)',
  lineTension : 0.2
}

function changeData(dataType, label, options = displayOptions) {
  statGraph.data = {
  datasets: [{
  label: label,
  data: getValues(data[dataType]),
  ...options
  }]
  }
  statGraph.options.scales.xAxes.forEach(axe => {
  axe.time.tooltipFormat = str_tooltip_format[dataType];
  axe.time.unit = data_unit[dataType];
  axe.time.displayFormats = str_unit_format;
  })
  statGraph.update();
}

//Make Data readable by Chart.js
function getValues(data) {
  values = [];
  Object.keys(data).forEach(function(key) {
  var newPoint = {
  x:new Date(key),
  y:data[key]
  }
  values.push(newPoint)
  });
  return values;
}

//Event listener
$(".stat-data-selector label").on("click", function(){
  let dataType = $(this).data("value");
  changeData(dataType, str_number_page_visited)
})

/*-------
Initialize the page
-------*/
$(function() {
  let dataType = $(".stat-data-selector input:checked + label").data("value");
  changeData(dataType, str_number_page_visited)
})
