$(document).ready(() => {

  fillHistoryResult(current_data);
  
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

        // console.log("RESULTS");
        // console.log(data);

        current_data = raw_data.result[1];

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
    })

    // console.log(dataObj);
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

function fillHistoryResult(ajaxParam) {
  // console.log(current_data);

  $.ajax({
    url: API_METHOD,
    method: "POST",
    dataType: "JSON",
    data: ajaxParam,
    success: function (raw_data) {
      $(".loading").removeClass("hide");
      // console.log(ajaxParam.user);
      // console.log(raw_data);
      data = raw_data.result[0];
      imageDisplay = raw_data.result[1].display_thumbnail;
      // console.log("RESULTS");
      // console.log(data);

      //clear lines before refill
      $(".tab .search-line").remove();
      
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

  let sections = [
    "categories",
    "tags",
    "best_rated",
    "memories-1-year-ago",
    "list",
    "search",
    "most_visited",
    "recent_pics",
    "recent_cats",
    "favorites"
  ]

  let icons = [
    "line-icon icon-folder-open icon-yellow",
    "line-icon icon-tags icon-blue",
    "line-icon icon-star icon-green",
    "line-icon icon-wrench",
    "line-icon icon-dice-solid icon-purple",
    "line-icon icon-search icon-purple",
    "line-icon icon-fire icon-red",
    "line-icon icon-clock icon-yellow",
    "line-icon icon-clock icon-yellow",
    "line-icon icon-heart icon-red"
  ];

  newLine.removeClass("hide");

  /* console log to help debug */
  // console.log(line);
  newLine.attr("id", id);
  // console.log(id);

  newLine.find(".date-day").html(line.DATE);
  newLine.find(".date-hour").html(line.TIME);

  newLine.find(".user-name").html(line.USERNAME);
  newLine.find(".user-name").attr("id", line.USERID);
  newLine.find(".user-name").on("click", function ()  {
    current_data.user = $(this).attr('id') + "";
    addUserFilter($(this).html());
    fillHistoryResult(current_data);
  })
  newLine.find(".user-ip").html(line.IP);
  newLine.find(".edit-img").attr("href", line.EDIT_IMAGE)

  if (line.IMAGE != "") {
    newLine.find(".type-name").html(line.IMAGENAME);
    if (imageDisplay !== "no_display_thumbnail") {
      newLine.find(".type-icon").html(line.IMAGE);
    } else {
      newLine.find(".type-icon").addClass("line-icon icon-picture icon-yellow");
      newLine.find(".type-icon .icon-file-image").removeClass("icon-file-image");
    }
  } else {
    newLine.find(".type-icon .icon-file-image").removeClass("icon-file-image");
    newLine.find(".toggle-img-option").hide();

    if (sections.indexOf(line.SECTION) != -1) {
      var lineIconClass = icons[sections.indexOf(line.SECTION)];
      newLine.find(".type-icon i").addClass(lineIconClass)
    } else {
      console.log("ERROR ON THIS : " + line.SECTION);
    }
  }

  newLine.find(".detail-item-1").html(line.SECTION);
  
  displayLine(newLine);
}

function displayLine(line) {
  $(".tab").append(line);
}

function addUserFilter(username) {
  console.log(username);
  var newFilter = $("#default-filter").clone();
  console.log(newFilter);

  newFilter.removeClass("hide");

  newFilter.find(".filter-title").html(username);
  newFilter.find(".filter-icon").addClass("icon-user");

  newFilter.find(".remove-filter").on("click", function () {
    $(this).parent().remove();

    current_data.user = "-1";
    fillHistoryResult(current_data);

  })

  $(".filter-container").append(newFilter);
}