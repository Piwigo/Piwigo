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

  if (api_method == 'pwg.categories.getList') {
    api_params = {
      cat_id: 0,
      recursive: true,
      fullname: true,
      search: searchText,
    }
  } else {
    api_params = {
      search: searchText,
      additional_output: "full_name_with_admin_links",
    }
  }

  console.log('lalalal');
  console.log(api_method);

  $(".linkedAlbumPopInContainer .searching").show();
  $.ajax({
    url: "ws.php?format=json&method=" + api_method,
    type: "POST",
    dataType: "json",
    data : api_params,
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
