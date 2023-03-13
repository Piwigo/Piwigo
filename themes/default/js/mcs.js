$(document).ready(function () {
  var url = window.location.href;
  var search_id = url.substring(url.lastIndexOf('/') + 1);

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
        word_search_str = "";
        word_search_words = global_params.fields.allwords.words
        word_search_words.forEach(word => {
          word_search_str += word + " ";
        });
        $("#word-search").val(word_search_str.slice(0, -1));

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

      // What do we do if we can't fetch search params ?
    },
    error:function(e) {
      console.log(e);
    }
  });

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

        global_params.fields.allwords.words = $("#word-search").val().split(" ");
        global_params.mode = $(".word-search-options input:checked").attr('value');

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

  $(".filter-tag").on("click", function (e) {
    if ($(".filter-form").has(e.target).length != 0 || $(e.target).hasClass("filter-form")) {
      return
    }
    $(".filter-tag-form").toggle(0, function () {

      if ($(this).is(':visible')) {
        $(".filter-tag").addClass("show-filter-dropdown");
        $(".filter-tag-form .selectize-input input").focus();
      } else {
        $(".filter-tag").removeClass("show-filter-dropdown");
        performSearch(global_params);
      }
    });

    $(".filter-tag .filter-validate").on("click", function () {
      // Update global params
      console.log(global_params);
  
      // Trigger search with click
      $(".filter-tag").trigger("click");
    })
  });

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
  // TODO : Envoyer les bon paramètres
  // console.log(params.search_params);
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