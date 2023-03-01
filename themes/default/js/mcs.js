$(document).ready(function () {
  var url = window.location.href;
  var search_id = url.substring(url.lastIndexOf('/') + 1);

  $.ajax({
    url: "ws.php?format=json&method=pwg.gallery.getSearch",
    type:"POST",
    dataType: "JSON",
    data: {
      search_id: search_id,
    },
    success:function(data) {
      console.log("Global params after fetch");

      console.log(data.result);
      if (data.stat == "ok") {
        global_params = data.result;
        // global_params.search_id = search_id;
      }
      // console.log(global_params);

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
      } else {
        $(".filter-word").removeClass("show-filter-dropdown");
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
      } else {
        $(".filter-tag").removeClass("show-filter-dropdown");
        performSearch(global_params);
      }
    });
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
  console.log(params);
  // TODO : Envoyer les bon paramètres
  // console.log(params.search_params);
  $.ajax({
    url: "ws.php?format=json&method=pwg.gallery.updateSearch",
    type:"POST",
    dataType: "json",
    data: {
      search_id: 199,
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