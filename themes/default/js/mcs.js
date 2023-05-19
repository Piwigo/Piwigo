$(document).ready(function () {
  var url = window.location.href;
  var search_id = url.substring(url.lastIndexOf('/') + 1);

  related_categories_ids = [];

  $.ajax({
    url: "ws.php?format=json&method=pwg.gallery.getSearch",
    type: "POST",
    dataType: "JSON",
    data: {
      search_id: search_id,
    },
    success:function(data) {
      if (data.stat == 'fail') {
        console.log("search failed");
        return;
      } 
      if (data.stat == "ok") {
        global_params = data.result;
        global_params.search_id = search_id;
      }
      console.log("Global params after fetch");
      console.log(global_params);

      // Setup word filter
      if (global_params.fields.allwords) {
        console.log("there is a word in the search");
        $(".filter-word").css("display", "flex").addClass("filter-filled");

        $(".filter-manager-controller.word").prop("checked", true);

        word_search_str = "";
        word_search_words = global_params.fields.allwords.words
        word_search_words.forEach(word => {
          word_search_str += word + " ";
        });
        $("#word-search").val(word_search_str.slice(0, -1));
        $(".filter-word .search-words").html(word_search_str.slice(0, -1));

        word_search_fields = global_params.fields.allwords.fields;
        Object.keys(word_search_fields).forEach(field_key => {
          $("#"+word_search_fields[field_key]).prop("checked", true);
        });

        word_search_mode = global_params.fields.allwords.mode;
        $(".word-search-options input[value=" + word_search_mode + "]").prop("checked", true);

        if (global_params.fields.search_in_tags) {
          $("#tags").prop("checked", true);
        }
      }

      // Setup tag filter
      $("#tag-search").each(function() {
        $(this).selectize({
          plugins: ['remove_button'],
          maxOptions:$(this).find("option").length,
          items: global_params.fields.tags ? global_params.fields.tags.words : null,
        });
      });
      if (global_params.fields.tags) {
        $(".filter-tag").show();
        $(".filter-manager-controller tag").prop("checked", true);
        $(".filter-tag-form .search-params input[value=" + global_params.fields.tags.mode + "]").prop("checked", true);
      }

      // Setup album filter
      if (global_params.fields.cat) {
        console.log("there is an album in the search");
        $(".filter-album").css("display", "flex").addClass("filter-filled");;
        $(".filter-manager-controller.album").prop("checked", true);
      
        album_widget_value = "";
        global_params.fields.cat.words.forEach(cat_id => {
          add_related_category(cat_id, fullname_of_cat[cat_id]);
          album_widget_value += fullname_of_cat[cat_id] + ", ";
        });
        $(".filter-album .search-words").html(album_widget_value.slice(0, -2));

        if (global_params.fields.cat.sub_inc) {
          $("#search-sub-cats").prop("checked", true);
        }
      }
      
      // Setup author filter
      $("#authors").each(function() {
        $(this).selectize({
          plugins: ['remove_button'],
          maxOptions:$(this).find("option").length,
          items: global_params.fields.author ? global_params.fields.author.words : null,
        });
        if (global_params.fields.author) {
          $(".filter-author").show();
          $(".filter-manager-controller.author").prop("checked", true);
        }
      });

      // Setup added_by filter
      $("#added_by").each(function() {
        $(this).selectize({
          plugins: ['remove_button'],
          maxOptions:$(this).find("option").length,
          items: global_params.fields.added ? global_params.fields.added.words : null,
        });
        if (global_params.fields.added) {
          $(".filter-added").show();
          $(".filter-manager-controller.added").prop("checked", true);
        }
      });


      // What do we do if we can't fetch search params ?
    },
    error:function(e) {
      console.log(e);
    }
  });

  /**
   * Filter Manager
   */

  $(".filter-manager").on('click', function () {
    $(".filter-manager-popin").show();
  });
  $(document).on('keyup', function (e) {
    // 27 is 'Escape'
    if(e.keyCode === 27) {
      $(".filter-manager-popin").hide();
    }
  });
  $(".filter-manager-close").on('click', function () {
    $(".filter-manager-popin").hide();
  });

  $(".filter-manager-popin .filter-validate").on('click', function () {

    $(".filter").hide();
    $(".filter-manager-controller-container input:checked").each(function (e) {
      $(".filter.filter-" + $(this).data("wid")).show();
    });
    $(".filter-manager-popin").hide();
  })


  /**
   * Filter Word
   */
  $(".filter-word").on("click", function (e) {
    if ($(".filter-form").has(e.target).length != 0 || $(e.target).hasClass("filter-form")) {
      return
    }
    $(".filter-word-form").toggle(0, function () {
      
      if ($(this).is(':visible')) {
        $(".filter-word").addClass("show-filter-dropdown");
        $("#word-search").focus();
      } else {
        $(".filter-word").removeClass("show-filter-dropdown");

        global_params.fields.allwords = {};
        global_params.fields.allwords.words = $("#word-search").val().split(" ");
        global_params.fields.allwords.mode = $(".word-search-options input:checked").attr('value');

        new_fields = []
        $(".filter-word-form .search-params input:checked").each(function () {

          if ($(this).attr("name") == "tags") {
            global_params.fields.search_in_tags = true;
          } else {
            new_fields.push($(this).attr("name"));
          }
        });
        if ($(".filter-word-form .search-params input[name='tags']:checked").length == 0) {
          delete global_params.fields.search_in_tags;
        }
        global_params.fields.allwords.fields = new_fields;
        performSearch(global_params);
      }
    });
  });
  $(".filter-word .filter-validate").on("click", function () {
    // Update global params
    console.log(global_params);

    // Trigger search with click
    $(".filter-word").trigger("click");
  })

  /**
   * Filter Tag
   */
  $(".filter-tag").on("click", function (e) {
    if ($(".filter-form").has(e.target).length != 0 || $(e.target).hasClass("filter-form") || $(e.target).hasClass("remove")) {
      return
    }
    $(".filter-tag-form").toggle(0, function () {
      if ($(this).is(':visible')) {
        $(".filter-tag").addClass("show-filter-dropdown");
      } else {
        $(".filter-tag").removeClass("show-filter-dropdown");
        performSearch(global_params);
      }
    });
  });
  $(".filter-tag .filter-validate").on("click", function () {
    // Update global params
    global_params.fields.tags = {};
    global_params.fields.tags.mode = $(".filter-tag-form .search-params input:checked").val();
    global_params.fields.tags.words = $("#tag-search")[0].selectize.getValue();

    console.log(global_params);

    // Trigger search with click
    $(".filter-tag").trigger("click");
  })

  /**
   * Filter Date
   */

  $(".filter-date").on("click", function (e) {
    if ($(".filter-form").has(e.target).length != 0 || $(e.target).hasClass("filter-form")) {
      return
    }
    $(".filter-date-form").toggle(0, function () {
      
      if ($(this).is(':visible')) {
        $(".filter-date").addClass("show-filter-dropdown");
      } else {
        $(".filter-date").removeClass("show-filter-dropdown");
        performSearch(global_params);
      }
    });
  });

  /**
   * Filter Album
   */
  $(".filter-album").on("click", function (e) {
    if ($(".filter-form").has(e.target).length != 0 || $(e.target).hasClass("filter-form") || $(e.target).hasClass("remove-item")) {
      return
    }
    $(".filter-album-form").toggle(0, function () {
      if ($(this).is(':visible')) {
        $(".filter-album").addClass("show-filter-dropdown");
      } else {
        $(".filter-album").removeClass("show-filter-dropdown");
        global_params.fields.cat = {};
        global_params.fields.cat.words = related_categories_ids;
        global_params.fields.cat.search_params = $(".filter-form.filter-album-form .search-params input:checked").val().toLowerCase();
        global_params.fields.cat.sub_inc = $("input[name='search-sub-cats']:checked").length != 0;

        performSearch(global_params);
      }
    });
  });
  $(".filter-album .filter-validate").on("click", function () {
    // Update global params
    console.log(global_params);

    // Trigger search with click
    $(".filter-album").trigger("click");
  });

  $(".add-album-button").on("click", function () {
    linked_albums_open();
    set_up_popin();
  });

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

  /**
   * Author Widget
   */
  $(".filter-author").on("click", function (e) {
    if ($(".filter-form").has(e.target).length != 0 || $(e.target).hasClass("filter-form") || $(e.target).hasClass("remove")) {
      return
    }
    $(".filter-author-form").toggle(0, function () {
      if ($(this).is(':visible')) {
        $(".filter-author").addClass("show-filter-dropdown");
      } else {
        $(".filter-author").removeClass("show-filter-dropdown");
        performSearch(global_params);
      }
    });
  });
  $(".filter-author .filter-validate").on("click", function () {
    // Update global params
    global_params.fields.author = {};
    global_params.fields.author.mode = "OR";
    global_params.fields.author.words = $("#authors")[0].selectize.getValue();

    console.log(global_params);

    // Trigger search with click
    $(".filter-author").trigger("click");
  })

  /**
   * Added by Widget
   */
  $(".filter-added").on("click", function (e) {
    if ($(".filter-form").has(e.target).length != 0 || $(e.target).hasClass("filter-form") || $(e.target).hasClass("remove")) {
      return
    }
    $(".filter-added-form").toggle(0, function () {
      if ($(this).is(':visible')) {
        $(".filter-added").addClass("show-filter-dropdown");
      } else {
        $(".filter-added").removeClass("show-filter-dropdown");
        performSearch(global_params);
      }
    });
  });
  $(".filter-added .filter-validate").on("click", function () {
    // Update global params
    global_params.fields.added = {};
    global_params.fields.added.mode = "OR";
    global_params.fields.added.words = $("#added_by")[0].selectize.getValue();

    console.log(global_params);

    // Trigger search with click
    $(".filter-added").trigger("click");
  })

  /* Close dropdowns if you click on the screen */
  // $(document).mouseup(function (e) {
  //   e.stopPropagation();
  //   let option_is_clicked = false
  //   $(".mcs-container .filter").each(function () {
  //     console.log(($(this).hasClass("show-filter-dropdown")));
  //     if (!($(this).has(e.target).length === 0)) {
  //       option_is_clicked = true;
  //     }
  //   })
  //   if (!option_is_clicked) {
  //     $(".filter-form").hide();
  //     if ($(".show-filter-dropdown").length != 0) {
  //       $(".show-filter-dropdown").removeClass("show-filter-dropdown");
  //       performSearch();
  //     }
  //   }
  // });
})

function performSearch(params) {
  console.log("params sent to updatesearch");
  console.log(params);
  $.ajax({
    url: "ws.php?format=json&method=pwg.gallery.updateSearch",
    type:"POST",
    dataType: "json",
    data: {
      search_id: params.search_id,
      params: params,
    },
    success:function(data) {
      console.log("perform search");
      console.log(data);
    },
    error:function(e) {
      console.log(e);
    }
  });
}

function set_up_popin() {
  $(".ClosePopIn").on('click', function () {
    linked_albums_close();
  });

  $("#addLinkedAlbum").on('keyup', function (e) {
    // 27 is 'Escape'
    if(e.keyCode === 27) {
      linked_albums_close();
    }
  })
}

function linked_albums_close() {
  $("#addLinkedAlbum").fadeOut();
}

function fill_results(cats) {
  $("#searchResult").empty();
  cats.forEach(cat => {
    if (!related_categories_ids.includes(cat.id)) {
      $("#searchResult").append(
      "<div class='search-result-item' id="+ cat.id + ">" +
        "<span class='search-result-path'>" + cat.fullname +"</span><span id="+ cat.id + " class='icon-plus-circled item-add'></span>" +
      "</div>"
      );

      $(".search-result-item#"+ cat.id).on("click", function () {
        add_related_category(cat.id, cat.fullname);
      });
    }
  });
}

function add_related_category(cat_id, cat_link_path) {
    $(".selected-categories-container").append(
      "<div class='breadcrumb-item'>" +
        "<span class='link-path'>" + cat_link_path + "</span><span id="+ cat_id + " class='mcs-icon pwg-icon-close remove-item'></span>" +
      "</div>"
    );

    related_categories_ids.push(cat_id);
    $(".invisible-related-categories-select").append("<option selected value="+ cat_id +"></option>");

    $("#"+ cat_id).on("click", function () {
      remove_related_category($(this).attr("id"))
    })

    linked_albums_close();
}

function remove_related_category(cat_id) {
  $("#" + cat_id).parent().remove();

  cat_to_remove_index = related_categories_ids.indexOf(cat_id);
  if (cat_to_remove_index > -1) {
    related_categories_ids.splice(cat_to_remove_index, 1);
  }

}