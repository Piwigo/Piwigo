$(document).ready(function () {

  $(".linked-albums.add-item").on("click", function () {
    linked_albums_open();
    set_up_popin();
  });

  $(".limitReached").html(str_no_search_in_progress);
  $(".search-cancel-linked-album").hide();
  $(".linkedAlbumPopInContainer .searching").hide();
  $("#linkedAlbumSearch .search-input").on('input', function () {
    if ($(this).val() != 0) {
      $("#linkedAlbumSearch .search-cancel-linked-album").show()
    } else {
      $("#linkedAlbumSearch .search-cancel-linked-album").hide();
    }

    // Search input value length required to start searching
    if ($(this).val().length > 0) {
      linked_albums_search($(this).val());
    } else {
      $(".limitReached").html(str_no_search_in_progress);
      $("#searchResult").empty();
    }
  })

  $(".search-cancel-linked-album").on("click", function () {
    $("#linkedAlbumSearch .search-input").val("");
    $("#linkedAlbumSearch .search-input").trigger("input");
  })

  $(".related-categories-container .breadcrumb-item .remove-item").on("click", function () {
    remove_related_category($(this).attr("id"));
  })
})

function set_up_popin() {
  $(".ClosePopIn").on('click', function () {
    linked_albums_close();
  });
}

function linked_albums_close() {
  $("#addLinkedAlbum").fadeOut();
}
function linked_albums_open() {
  $("#addLinkedAlbum").fadeIn();
  $(".search-input").val("");
  $(".search-input").focus();
  $("#searchResult").empty();
  $(".limitReached").html(str_no_search_in_progress);
}
function linked_albums_search(searchText) {
  $(".linkedAlbumPopInContainer .searching").show();
  $.ajax({
    url: "ws.php?format=json&method=pwg.categories.getAdminList",
    type: "POST",
    dataType: "json",
    data : {
      search: searchText,
      additional_output: "full_name_with_admin_links",
    },
    before: function () {
      
    },
    success: function (raw_data) {
      $(".linkedAlbumPopInContainer .searching").hide();

      categories = raw_data.result.categories;
      fill_results(categories);

      if (raw_data.result.limit_reached) {
        $(".limitReached").html(str_result_limit.replace("%d", categories.length));
      } else {
        if (categories.length == 1) {
          $(".limitReached").html(str_album_found);
        } else {
          $(".limitReached").html(str_albums_found.replace("%d", categories.length));
        }
      }
    },
    error: function (e) {
      $(".linkedAlbumPopInContainer .searching").hide();
      console.log(e.message);
    }
  })
}

function fill_results(cats) {
  $("#searchResult").empty();
  cats.forEach(cat => {
    $("#searchResult").append(
    "<div class='search-result-item' id="+ cat.id + ">" +
      "<span class='search-result-path'>" + cat.fullname +"</span><span id="+ cat.id + " class='icon-plus-circled item-add'></span>" +
    "</div>"
    );

    if (related_categories_ids.includes(cat.id)) {
      $(".search-result-item #"+ cat.id +".item-add").addClass("notClickable").attr("title", str_already_in_related_cats).on("click", function (event) {
        event.preventDefault();
      });
      $(".search-result-item").addClass("notClickable").attr("title", str_already_in_related_cats).on("click", function (event) {
        event.preventDefault();
      });
    } else {
      $(".search-result-item#"+ cat.id).on("click", function () {
        add_related_category(cat.id, cat.full_name_with_admin_links);
      });
    }
  });
}

function remove_related_category(cat_id) {
  $(".invisible-related-categories-select option[value="+ cat_id +"]").remove();
  $("#" + cat_id).parent().remove();

  cat_to_remove_index = related_categories_ids.indexOf(cat_id);
  if (cat_to_remove_index > -1) {
    related_categories_ids.splice(cat_to_remove_index, 1);
  }

  check_related_categories();
}

function add_related_category(cat_id, cat_link_path) {
  if (!related_categories_ids.includes(cat_id)) {
    $(".related-categories-container").append(
      "<div class='breadcrumb-item'>" +
        "<span class='link-path'>" + cat_link_path + "</span><span id="+ cat_id + " class='icon-cancel-circled remove-item'></span>" +
      "</div>"
    );

    $(".search-result-item #" + cat_id).addClass("notClickable");
    related_categories_ids.push(cat_id);
    $(".invisible-related-categories-select").append("<option selected value="+ cat_id +"></option>");

    $("#"+ cat_id).on("click", function () {
      remove_related_category($(this).attr("id"))
    })

    linked_albums_close();
  }

  check_related_categories();
}

function check_related_categories() {

  $(".linked-albums-badge").html(related_categories_ids.length);

  if (related_categories_ids.length == 0) {
    $(".linked-albums-badge").addClass("badge-red");
    $(".add-item").addClass("highlight");
    $(".orphan-photo").html(str_orphan).show();
  } else {
    $(".linked-albums-badge.badge-red").removeClass("badge-red");
    $(".add-item.highlight").removeClass("highlight");
    $(".orphan-photo").hide();
  }
}