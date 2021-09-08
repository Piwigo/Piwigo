$(document).ready(() => {

  fillHistoryResult();
  
  $(".filter").submit(function (e) {
    e.preventDefault();

    var dataArray = $(this).serializeArray()
    console.log(dataArray);
    dataObj = {};

    dataObj["types"] = [];
    $(dataArray).each(function(i, field){
      if (field.name == "types[]") {
        dataObj["types"].push(field.value);
      } else {
        dataObj[field.name] = field.value;
      }
    });

    $.ajax({
      url: API_METHOD,
      method: "POST",
      dataType: "JSON",
      data: {
        start: dataObj['start'],
        end: dataObj['end'],
        types: dataObj['types'],
        user: dataObj['user'],
        image_id: dataObj['image_id'],
        filename: dataObj['filename'],
        ip: dataObj['ip'],
        display_thumbnail: dataObj['display_thumbnail'],
      },
      success: function (raw_data) {
        data = raw_data.result[0];
        imageDisplay = raw_data.result[1].display_thumbnail;

        $(".tab .search-line").remove();

        console.log("RESULTS");
        console.log(data);

        var id = 0;

        data.reverse().forEach(line => {
          lineConstructor(line, id, imageDisplay)
          id++
        });
      },
      error: function (e) {
        console.log("Something went wrong: " + e);
      }
    }).done( () => {
      activateLineOptions();
    }
      
    )

    console.log(dataObj);
  });

  activateLineOptions();

})

function activateLineOptions() {
  $(".search-line").find(".img-option").hide();

  /* Display the option on the click on "..." */
  $(".search-line").find(".toggle-img-option").on("click", function () {
    $(this).find(".img-option").toggle();
  })

  /* Hide img options and rename field on click on the screen */

  $(document).mouseup(function (e) {
    e.stopPropagation();
    let option_is_clicked = false
    $(".img-option span").each(function () {
      if (!($(this).has(e.target).length === 0)) {
        option_is_clicked = true;
      }
    })
    if (!option_is_clicked) {
      $(".search-line").find(".img-option").hide();
    }
  });
}

function fillHistoryResult() {

  var dateObj = new Date();
  var month = dateObj.getUTCMonth() + 1; //months from 1-12
  var day = dateObj.getUTCDate();
  var year = dateObj.getUTCFullYear();

  if (month < 10) month = "0" + month;
  if (day < 10) day = "0" + day;

  today = year + "-" + month + "-" + day;

  var dataSend = {
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
    display_thumbnail: "no_display_thumbnail",
  }

  $.ajax({
    url: API_METHOD,
    method: "POST",
    dataType: "JSON",
    data: dataSend,
    success: function (raw_data) {
      $(".loading").removeClass("hide");
      console.log(raw_data);
      data = raw_data.result[0];
      imageDisplay = raw_data.result[1].display_thumbnail;
      // console.log("RESULTS");
      // console.log(data);

      var id = 0;

      data.reverse().forEach(line => {
        lineConstructor(line, id, imageDisplay)
        id++
      });
    },
    error: function (e) {
      console.log(e);
    }
  }).done(() => {
    activateLineOptions();
    $(".loading").addClass("hide");
  })
}

function lineConstructor(line, id, imageDisplay) {
  let newLine = $("#-1").clone();

  newLine.removeClass("hide");

  /* console log to help debug */
  console.log(line);
  newLine.attr("id", id);
  // console.log(id);

  newLine.find(".date-day").html(line.DATE);
  newLine.find(".date-hour").html(line.TIME);

  newLine.find(".user-name").html(line.USERNAME);
  newLine.find(".user-ip").html(line.IP);

  console.log(line.EDIT_IMAGE);
  newLine.find(".edit-img").attr("href", line.EDIT_IMAGE)

  if (line.IMAGE != "") {
    newLine.find(".type-name").html(line.IMAGENAME);
    if (imageDisplay !== "no_display_thumbnail") {
      newLine.find(".type-icon").html(line.IMAGE);
    }
  } else {
    newLine.find(".type-icon").hide();
    newLine.find(".toggle-img-option").hide();
  }
  
  displayLine(newLine);
}

function displayLine(line) {
  $(".tab").append(line);
}