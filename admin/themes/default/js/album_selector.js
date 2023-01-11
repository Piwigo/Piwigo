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