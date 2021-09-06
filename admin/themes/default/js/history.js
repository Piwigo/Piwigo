$(document).ready(() => {

  fillHistoryResult()
  
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
        data = raw_data.result;
        console.log("RESULTS");
        console.log(data);

        var id = 0;

        data.forEach(line => {
          lineConstructor(line, id)
          id++
        });
      },
      error: function (e) {
        console.log("Something went wrong: " + e);
      }
    })

    console.log(dataObj);
  });

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
        data = raw_data.result;
        console.log("RESULTS");
        console.log(data);

        var id = 0;

        data.forEach(line => {
          lineConstructor(line, id)
          id++
        });
      },
      error: function (e) {
        console.log("Something went wrong: " + e);
      }
    }).done(() => {
      $(".loading").addClass("hide");
    })
  }

  function lineConstructor(line, id) {
    let newLine = $("#-1").clone();

    newLine.removeClass("hide");

    /* console log to help debug */
    console.log(line);
    newLine.attr("id", id);
    console.log(id);

    newLine.find(".date-day").html(line.DATE);
    newLine.find(".date-hour").html(line.TIME);

    newLine.find(".user-name").html(line.USERNAME);
    newLine.find(".user-ip").html(line.IP);

    displayLine(newLine);
  }

  function displayLine(line) {
    $(".tab").append(line);
}

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

})