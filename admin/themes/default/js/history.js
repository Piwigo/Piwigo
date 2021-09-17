$(document).ready(() => {

  fillHistoryResult(current_param);

  activateLineOptions();

  $(".elem-type-select").on("change", function (e) {
    console.log($(".elem-type-select option:selected").attr("value"));

    if ($(".elem-type-select option:selected").attr("value") == "visited") {
      current_param.types = {
        0: "none",
        1: "picture"
      }
    } else if ($(".elem-type-select option:selected").attr("value") == "downloaded"){
      current_param.types = {
        0: "high",
        1: "other"
      }
    } else {
      current_param.types = {
        0: "none",
        1: "picture",
        2: "high",
        3: "other"
      }
    }

    fillHistoryResult(current_param)
  });

  //TODO
  $('.date-start [data-datepicker]').on("toggle", function () {
    console.log("CLOSED");
  })

  $('.date-start .hasDatepicker').on("change", function () {
    console.log($('.date-start input[name="start"]').attr("value"));
    console.log("HELLO START");
    current_param.start = $('.date-start input[name="start"]').attr("value");
  });

  $('.date-end').on("change", function () {
    console.log($('.date-end input[name="end"]').attr("value"));
    console.log("HELLO END");
    current_param.end = $('.date-start input[name="start"]').attr("value");
  });

  /**
   * Pagination
   */

   $('.pagination-arrow.rigth').on('click', () => {
    current_param.pageNumber += 1;
    fillHistoryResult(current_param);
  })
  
  $('.pagination-arrow.left').on('click', () => {
    current_param.pageNumber -= 1;
    fillHistoryResult(current_param);
  })
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
  // console.log(current_param);

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
      console.log("RESULTS");
      console.log(data);
      console.log(raw_data);
      maxPage = raw_data.result[2];

      //clear lines before refill
      $(".tab .search-line").remove();
      
      var id = 0;
      data.forEach(line => {
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
    updatePagination(maxPage);
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
  if (current_param.user == "-1") {
    newLine.find(".user-name").on("click", function ()  {
      current_param.user = $(this).attr('id') + "";
      current_param.pageNumber = 0;
      addUserFilter($(this).html());
      fillHistoryResult(current_param);
    })
  }

  newLine.find(".user-ip").html(line.IP);
  if (current_param.ip == "") {
    newLine.find(".user-ip").on("click", function () {
      current_param.ip = $(this).html();
      current_param.pageNumber = 0;
      addIpFilter($(this).html());
      fillHistoryResult(current_param);
    })
  }

  newLine.find(".add-img-as-filter").data("img-id", line.IMAGEID);
  if (current_param.image_id == "") {
    newLine.find(".add-img-as-filter").on("click", function () {
      current_param.image_id = $(this).data("img-id");
      current_param.pageNumber = 0;
      addImageFilter($(this).data("img-id"));
      fillHistoryResult(current_param);
    });
  }
  newLine.find(".edit-img").attr("href", line.EDIT_IMAGE)

  switch (line.SECTION) {
    case "tags":
      newLine.find(".type-name").html(line.TAGS[0]);
      newLine.find(".type-id").html("#" + line.TAGIDS[0]);
      let detail_str = "";
      line.TAGS.forEach(tag => {
        detail_str += tag + ", ";
      });
      detail_str = detail_str.slice(0, -2)
      newLine.find(".detail-item-2").html(detail_str);
      newLine.find(".detail-item-2").attr("title", detail_str);
      break;
    
    case "most_visited":
      newLine.find(".type-name").html(str_most_visited);
      newLine.find(".type-id").remove();
      break;
    case "best_rated":
      newLine.find(".type-name").html(str_best_rated);
      newLine.find(".type-id").remove();
      break;
    case "list":
      newLine.find(".type-name").html(str_list);
      newLine.find(".type-id").remove();
      break;
    case "favorites":
      newLine.find(".type-name").html(str_favorites);
      newLine.find(".type-id").remove();
      break;

    default:
      break;
  }

  if (line.IMAGE != "") {
    newLine.find(".type-name").html(line.IMAGENAME);
    newLine.find(".type-icon").html(line.IMAGE);
    newLine.find(".type-id").html("#" + line.IMAGEID)
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
  if (line.TYPE == "high") {
    newLine.find(".detail-item-1").html(str_dwld).addClass("icon-blue").removeClass("detail-item-1");
    newLine.find(".date-dwld-icon").addClass("icon-blue icon-floppy")
  } else {
    newLine.find(".date-dwld-icon").remove();
  }
  displayLine(newLine);
}

function displayLine(line) {
  $(".tab").append(line);
}

function addUserFilter(username) {
  var newFilter = $("#default-filter").clone();
  newFilter.removeClass("hide");

  newFilter.find(".filter-title").html(username);
  newFilter.find(".filter-icon").addClass("icon-user");

  newFilter.find(".remove-filter").on("click", function () {
    $(this).parent().remove();

    current_param.user = "-1";
    current_param.pageNumber = 0;
    fillHistoryResult(current_param);

  })

  $(".filter-container").append(newFilter);
}

function addIpFilter(ip) {
  var newFilter = $("#default-filter").clone();
  newFilter.removeClass("hide");

  newFilter.find(".filter-title").html(ip);
  newFilter.find(".filter-icon").addClass("icon-code");

  newFilter.find(".remove-filter").on("click", function () {
    $(this).parent().remove();

    current_param.ip = "";
    current_param.pageNumber = 0;
    fillHistoryResult(current_param);

  })

  $(".filter-container").append(newFilter);
}

function addImageFilter(img_id) {
  var newFilter = $("#default-filter").clone();
  newFilter.removeClass("hide");

  newFilter.find(".filter-title").html("Image #" + img_id);
  newFilter.find(".filter-icon").addClass("icon-picture");

  newFilter.find(".remove-filter").on("click", function () {
    $(this).parent().remove();

    current_param.image_id = "";
    current_param.pageNumber = 0;
    fillHistoryResult(current_param);

  })

  $(".filter-container").append(newFilter);
}

function updateArrows(actualPage, maxPage) {
  if (actualPage == 0) {
    $('.pagination-arrow.left').addClass('unavailable');
  } else {
    $('.pagination-arrow.left').removeClass('unavailable');
  }

  if (actualPage == maxPage-1) {
    $('.pagination-arrow.rigth').addClass('unavailable');
  } else {
    $('.pagination-arrow.rigth').removeClass('unavailable');
  }
}

function updatePagination(maxPage) {
  updateArrows(current_param.pageNumber, maxPage);

  $(".pagination-item-container").empty();
  $(".pagination-item-container").append(
    "<a class='actual'>"+ (current_param.pageNumber+1) +"</a>"
  )
}
