$(document).ready(() => {

  activateLineOptions();
  checkFilters();

  if (current_param.ip != "") {
    addIpFilter(current_param.ip);
  }
  if (current_param.image_id != "") {
    addImageFilter(current_param.image_id);
  }
  if (current_param.user_id != "-1") {
    addUserFilter(filter_user_name);
  }

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

  $('.date-start').on("change", function () {
    if (current_param.start != $('.date-start input[name="start"]').attr("value")) {
      current_param.start = $('.date-start input[name="start"]').attr("value");
      current_param.pageNumber = 0;
      fillHistoryResult(current_param);
    }
  });

  $('.date-end').on("change", function () {
    console.log($('.date-end input[name="end"]').attr("value"));
    if (current_param.end != $('.date-end input[name="end"]').attr("value")) {
      console.log("HERE");
      current_param.end = $('.date-end input[name="end"]').attr("value");
      current_param.pageNumber = 0;
      fillHistoryResult(current_param);
    }
  });

  $("#start_unset").on("click", function () {
    console.log("here" + current_param.start);
    if (!current_param.start == "") {
      
      current_param.pageNumber = 0;
      current_param.start = "";
      fillHistoryResult(current_param);
    }
  });

  $("#end_unset").on("click", function () {
    if (!current_param.start == today) {
      current_param.end = today;
      current_param.pageNumber = 0;
      fillHistoryResult(current_param);
    }
  });


  $('.pagination-arrow.rigth').on('click', () => {
    current_param.pageNumber += 1;
    fillHistoryResult(current_param);
  });
  
  $('.pagination-arrow.left').on('click', () => {
    current_param.pageNumber -= 1;
    fillHistoryResult(current_param);
  });

  $(".refresh-results").on("click", function () {
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

function fillSummaryResult(summary) {
  $(".user-list").empty();

  $(".summary-lines .summary-data").html(summary.NB_LINES);
  $(".summary-weight .summary-data").html(unit_MB.replace("%s", summary.FILESIZE));
  $(".summary-users .summary-data").html(summary.USERS);
  $(".summary-guests .summary-data").html(summary.GUESTS);

  if ((summary.GUESTS.split(" ")[0] != "0")) {
    $(".summary-guests .summary-data").addClass("icon-plus-circled").on("click", function () {
      if (current_param.user_id == "-1") {
        current_param.user_id = guest_id;
        addGuestFilter(str_guest);
        fillHistoryResult(current_param);
      }
    }).hover(function () {
      $(this).css({
        cursor : "pointer"
      })
    });

    $(".summary-guests").show();
  } else {
    $(".summary-guests").hide();
  }

  var id_of = [];
  var user_dot_title = "";

  // not sorted
  summary.MEMBERS.forEach(keyval => {
    for (const [key, value] of Object.entries(keyval)) {
      id_of[key] = value;
      user_dot_title += key + ", ";
    }
  });
  user_dot_title = user_dot_title.slice(0, -2);
  $(".user-dot").attr("title", user_dot_title).addClass("tiptip");

  var tmp = 0;
  $(".user-dot").hide();
  //sorted
  for (const [key, value] of Object.entries(summary.SORTED_MEMBERS)) {
    if (tmp < 5) {
      new_user_item = $("#-2").clone();

      new_user_item.removeClass("hide");
      new_user_item.find(".user-item-name").html(key);
      new_user_item.data("user-id", id_of[key]);
  
      new_user_item.on("click", function () {
        if (current_param.user_id != id_of[key]) {
          current_param.user_id = $(this).data("user-id");
          addUserFilter(key)
          fillHistoryResult(current_param);
        }
      })
      $(".user-list").append(new_user_item);
      tmp++;
    } else {
      $(".user-dot").show();
    }
  }
}

function showResults(doShow) {
  console.log("EMPTY");
  if (doShow) {
    $(".search-summary").show();
    $(".container").show();
  } else {
    $(".search-summary").hide();
    $(".container").hide();
  }
}

function fillHistoryResult(ajaxParam) {
  // console.log(current_param);
  // $(".tab .search-line").remove();
  $.ajax({
    url: API_METHOD,
    method: "POST",
    dataType: "JSON",
    data: ajaxParam,
    beforeSend: function () {
      showResults(false);
      $(".loading").removeClass("hide");
      $(".noResults").hide();
      $(".tab").empty();
    },
    success: function (raw_data) {
      
      data = raw_data.result["lines"];
      imageDisplay = raw_data.result["params"].display_thumbnail;
      maxPage = raw_data.result["maxPage"];
      summary = raw_data.result["summary"];
      // console.log(raw_data);

      //clear lines before refill
      
      if (data.length > 0) {
        var id = 0;
        data.forEach(line => {
          lineConstructor(line, id, imageDisplay)
          id++
        });
  
        fillSummaryResult(summary);
        showResults(true);
        $(".noResults").hide();
      } else {
        showResults(false);
        $(".noResults").show();
      }

    },
    error: function (e) {
      console.log(e);
    }
  }).done(() => {
    activateLineOptions();
    $(".loading").addClass("hide");
    updatePagination(maxPage);
    $('.tiptip').tipTip({
      delay: 0,
      fadeIn: 200,
      fadeOut: 200,
      edgeOffset: 3
    });
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
    "line-icon icon-clock icon-yellow",
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

  newLine.find(".user-name").html(line.USERNAME + '<i class="add-filter icon-plus-circled"></i>');

  newLine.find(".user-name").attr("id", line.USERID);
  if (current_param.user_id == "-1") {
    newLine.find(".user-name").on("click", function ()  {
      current_param.user_id = $(this).attr('id') + "";
      current_param.pageNumber = 0;
      addUserFilter($(this).html());
      fillHistoryResult(current_param);
    })
  }

  newLine.find(".user-ip").html(line.IP + '<i class="add-filter icon-plus-circled"></i>');
  newLine.find(".user-ip").data("ip", line.IP);
  if (current_param.ip == "") {
    newLine.find(".user-ip").on("click", function () {
      current_param.ip = $(this).data("ip");
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

  if (line.EDIT_IMAGE != "") {
    newLine.find(".edit-img").attr("href", line.EDIT_IMAGE);
  } else {
    newLine.find(".edit-img")
      .attr("href", "#")
      .addClass("notClickable tiptip")
      .attr('title', str_no_longer_exist_photo)
      .on("click", (e) => {
      e.preventDefault();
    });
  }

  switch (line.SECTION) {
    case "tags":
      if (line.TAGS.length > 1 && line.TAGS.length <= 2  ) {
        newLine.find(".type-name").html(line.TAGS[0] +", "+ line.TAGS[1] + ", ...");
        newLine.find(".type-id").html("#" + line.TAGIDS[0] +", "+ line.TAGIDS[1] + ", ...");
      } else if (line.TAGS.length > 2) {
        newLine.find(".type-name").html(line.TAGS[0] +", "+ line.TAGS[1] +", "+ line.TAGS[2]  + ", ...");
        newLine.find(".type-id").html("#" + line.TAGIDS[0] +", "+ line.TAGIDS[1] +", "+ line.TAGIDS[2] + ", ...");
      } else {
        newLine.find(".type-name").html(line.TAGS[0]);
        newLine.find(".type-id").html("#" + line.TAGIDS[0]);
      }
      
      let detail_str = "";
      line.TAGS.forEach(tag => {
        detail_str += tag + ", ";
      });
      detail_str = detail_str.slice(0, -2)
      newLine.find(".detail-item-1").html(detail_str);
      newLine.find(".detail-item-1").attr("title", detail_str).removeClass("hide").addClass('icon-tags');;
      break;
    
    case "most_visited":
      newLine.find(".type-name").html(str_most_visited);
      newLine.find(".detail-item-1").html(str_most_visited).addClass('icon-fire');
      newLine.find(".type-id").hide();
      break;
    case "best_rated":
      newLine.find(".type-name").html(str_best_rated);
      newLine.find(".detail-item-1").html(str_best_rated).addClass("icon-star");
      newLine.find(".type-id").hide();
      break;
    case "list":
      newLine.find(".type-name").html(str_list);
      newLine.find(".detail-item-1").html(str_list).addClass('icon-dice-solid');
      newLine.find(".type-id").hide();
      break;
    case "favorites":
      newLine.find(".type-name").html(str_favorites);
      newLine.find(".detail-item-1").html(str_favorites).addClass('icon-heart');
      newLine.find(".type-id").hide();
      break;
    case "recent_cats":
      newLine.find(".type-name").html(str_recent_cats);
      newLine.find(".detail-item-1").html(str_recent_cats).addClass('icon-clock');
      newLine.find(".type-id").hide();
      break;
    case "recent_pics":
      newLine.find(".type-name").html(str_recent_pics);
      newLine.find(".detail-item-1").html(str_recent_pics).addClass('icon-clock');
      newLine.find(".type-id").hide();
      break;
    case "categories":
      newLine.find(".type-name").html(line.CATEGORY);
      newLine.find(".detail-item-1").html(line.CATEGORY).addClass("icon-folder-open tiptip").attr("title", line.FULL_CATEGORY_PATH);
      if (line.IMAGE == "") {
        newLine.find(".type-id").hide();
      }
      break;
    case "memories-1-year-ago":
      newLine.find(".type-name").html(str_memories);
      newLine.find(".detail-item-1").html(str_memories).addClass('icon-clock');
      newLine.find(".type-id").hide();
    break;
    case "contact":
      newLine.find(".type-icon i").addClass("line-icon icon-mail-1 icon-yellow");
      newLine.find(".type-name").html(str_contact_form);
      newLine.find(".detail-item-1").html(str_contact_form);
      newLine.find(".type-id").hide();
    break;
    default:
      newLine.find(".type-icon i").addClass("line-icon icon-help-puzzle icon-grey");
      newLine.find(".type-name").html(line.SECTION);
      newLine.find(".type-id").hide();
    break;
  }

  if (line.IMAGE != "") {
    newLine.find(".type-name").html(line.IMAGENAME);
    newLine.find(".type-icon").html(line.IMAGE);
    newLine.find(".type-id").html("#" + line.IMAGEID);
    newLine.find(".type-id").show();
  } else {
    newLine.find(".type-icon .icon-file-image").removeClass("icon-file-image");
    newLine.find(".toggle-img-option").hide();

    if (sections.indexOf(line.SECTION) != -1) {
      var lineIconClass = icons[sections.indexOf(line.SECTION)];
      newLine.find(".type-icon i").addClass(lineIconClass)
    } else {
      console.log("Unhandled section : " + line.SECTION);
    }
  }

  newLine.find(".detail-item-1").removeClass("hide");
  if (line.TYPE == "high") {
    newLine.find(".detail-item-1").html(str_dwld).addClass("icon-blue").removeClass("detail-item-1").removeClass("hide");
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

    current_param.user_id = "-1";
    current_param.pageNumber = 0;
    fillHistoryResult(current_param);
    checkFilters();
    $(".summary-guests").show();
  })

  $(".summary-guests").hide();
  $(".filter-container").append(newFilter);
  checkFilters();
}

function addGuestFilter(username) {
  var newFilter = $("#default-filter").clone();
  newFilter.removeClass("hide");

  newFilter.find(".filter-title").html(username);
  newFilter.find(".filter-icon").addClass("icon-user-secret");

  newFilter.find(".remove-filter").on("click", function () {
    $(this).parent().remove();

    current_param.user_id = "-1";
    current_param.pageNumber = 0;
    fillHistoryResult(current_param);
    checkFilters();
  })

  $(".filter-container").append(newFilter);
  checkFilters();
}

function addIpFilter(ip) {
  var newFilter = $("#default-filter").clone();
  newFilter.removeClass("hide");

  newFilter.find(".filter-title").html(ip);
  newFilter.find(".filter-icon").html("IP ").addClass("bold");

  newFilter.find(".remove-filter").on("click", function () {
    $(this).parent().remove();

    current_param.ip = "";
    current_param.pageNumber = 0;
    fillHistoryResult(current_param);
    checkFilters();
  })

  $(".filter-container").append(newFilter);
  checkFilters();
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
    checkFilters();
  })

  $(".filter-container").append(newFilter);
  checkFilters();
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
    "<a class='actual'>"+ (current_param.pageNumber+1) + "/" + maxPage +"</a>"
  )
}

function checkFilters() {
  if ($(".filter-container")[0].childElementCount - 1 > 0) { //Check if there are filters
    $(".filter-tags label").show();
  } else {
    $(".filter-tags label").hide();
  }
}