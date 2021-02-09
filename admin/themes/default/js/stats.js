/*-------
Data Get
-------*/
data = {};
data["hours"] = $("#data").data("hours");
data["days"] = $("#data").data("days");
data["months"] = $("#data").data("months");
data["years"] = $("#data").data("years");
data["compare-years"] = $("#data").data("compare-years");
data["month-stats"] = $("#data").data("month-stats");

data_unit = {
  "hours":"day",
  "days":"month",
  "months": "year",
  "years": "year"
}

compareMode = false;

/*-------
Creating graph
-------*/
var ctx = document.getElementById('stat-graph').getContext('2d');
//Create the gradient under the curve
function gradient(r, g, b) {
  let gradient = ctx.createLinearGradient(0,400, 0,0);
  gradient.addColorStop(0, 'rgba('+r+','+g+','+b+',0)');
  gradient.addColorStop(1, 'rgba('+r+','+g+','+b+',1)');
  return gradient;
}

//Setup the graph
Chart.defaults.global.elements.point.radius = 0.1;
Chart.defaults.global.elements.point.hitRadius = 10
Chart.defaults.global.defaultFontSize = 14;
Chart.defaults.global.defaultFontColor = '#888';
Chart.defaults.global.tooltips.intersect = false;
Chart.defaults.global.legend.onClick = null;

var statGraph = new Chart(ctx, {
  type: 'line',
  maintainAspectRatio: false,
});

//Line options
var displayOptions = {
  backgroundColor: gradient(255, 119, 0),
  borderColor: 'rgba(255,119,0,1)',
  lineTension : 0.2
}

function changeData(dataType, options = displayOptions) {
  if (!compareMode) {
    statGraph.data = {
      datasets: [{
      label: str_number_page_visited,
      data: getValues(data[dataType]),
      ...options
      }]
    }
    statGraph.options = {
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
        yAxes: [{
          ticks: {
            min: 0
          }
        }]
      },
      legend: {
        display:false
      },
      tooltips: {
        mode: 'index'
      },
      hover :
      {
        intersect : false,
      }
    }
    statGraph.options.scales.xAxes.forEach(axe => {
      axe.time.tooltipFormat = str_tooltip_format[dataType];
      axe.time.unit = data_unit[dataType];
      axe.time.displayFormats = str_unit_format;
    })
    statGraph.update();
  } else {
    statGraph.options.legend.display = true;
    statGraph.options.hover = {
      intersect : true
    }
    statGraph.options.tooltips = {
      mode : 'nearest'
    }
    if (dataType == "years") {
      statGraph.data = {
        datasets: getComparedYearDataset()
      }
      statGraph.options.scales = {
        xAxes: [{
          type: 'category',
          labels: str_months,
          gridLines: {
            display: false
          }
        }],
        yAxes: [{
          scaleLabel: {
            display: true,
            labelString: str_number_page_visited
          },
          tick: {
            min: 0
          }
        }]
      }
    } else if (dataType == "months") {
      days = [];
      for (let i = 1; i<=31; i++) {
        days.push(i);
      }
      statGraph.data = {
        datasets: getMonthStatsDataset()
      }
      statGraph.options.scales = {
        xAxes: [{
          type: 'category',
          labels : days,
          gridLines: {
            display: false
          }
        }],
        yAxes: [{
          scaleLabel: {
            display: true,
            labelString: str_number_page_visited
          }
        }]
      }
    }
    statGraph.update();
  }
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

function getComparedYearDataset() {
  colors = ["#ffa744", "#ff5252", "#896af3", "#2883c3", "#6ece5e"]
  values = {};
  dataset = [];

  Object.keys(data["compare-years"]).forEach(function(key) {
    date = new Date(key)
    if (values[date.getFullYear()] == undefined) {
      values[date.getFullYear()] = [];
    }
    values[date.getFullYear()][parseInt(date.getMonth())] = data["compare-years"][key];
  });

  Object.keys(values).forEach(function(key) {
    dataset.push({
      label : key,
      data : values[key],
      lineTension : 0.2,
      borderColor : colors[parseInt(key) % colors.length],
      backgroundColor: "rgba(0,0,0,0)"
    })
  });

  return dataset;
}

function getMonthStatsDataset() {
  colors = ["#ffa744", "#ff5252", "#896af3", "#2883c3", "#6ece5e"]
  dataset = [];
  colorIndice = 0;
  let date;

  data["month-stats"]["month"].forEach(values => {
    let days_data = [];
    Object.keys(values).forEach(function(key) {
      date = new Date(key)
      days_data[parseInt(date.getUTCDate()) - 1] = values[key];
    });
    dataset.push({
      label : str_months[date.getMonth()]+" "+date.getFullYear(),
      data : days_data,
      lineTension : 0.2,
      borderColor : colors[colorIndice % colors.length],
      backgroundColor: "rgba(0,0,0,0)"
    })
    colorIndice++
  });

  averageTab = [];
  for (let i = 0; i < 31; i++) {
    averageTab[i] = data["month-stats"]["avg"];
  }
  dataset.push({
    label : str_avg,
    data : averageTab,
    lineTension : 0.2,
    borderColor : colors[4],
    backgroundColor: "rgba(0,0,0,0)"
  })

  return dataset;
}

//Event listener
$(".stat-data-selector label").on("click", function(){
  dataType = $(this).data("value");
  changeData(dataType);
})

$(".stat-compare-mode input").on("change", function(){
  compareMode = $(this)[0].checked;

  if (compareMode) {
    $("#hours-selector + label, #days-selector + label").addClass('unavailable');
    if ($("#hours-selector").prop('checked')||$("#days-selector").prop('checked')) {
      $("#years-selector").prop('checked', true);
      $("#hours-selector, #days-selector").prop('checked', false);
      changeData("years");
    } else {
      changeData($(".stat-data-selector input:checked + label").data("value"))
    }
  } else {
    $("#hours-selector + label, #days-selector + label").removeClass('unavailable');
    changeData($(".stat-data-selector input:checked + label").data("value"));
  }
})

/*-------
Initialize the page
-------*/
$(function() {
  changeData($(".stat-data-selector input:checked + label").data("value"));
})
