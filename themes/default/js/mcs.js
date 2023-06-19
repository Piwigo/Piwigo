$(document).ready(function () {
  related_categories_ids = [];

  $(".linkedAlbumPopInContainer .ClosePopIn").addClass("pwg-icon-cancel");
  $(".filter-validate").on("click", function () {
    $(this).find(".loading").css("display", "block");
    $(this).find(".validate-text").hide();
  });

  $(".filter-form").hover(
  function () {
    $(this).parent().find(".mcs-icon.remove-filter").css({
      display: 'none',
    });
    $(this).parent().find(".mcs-icon.filter-icon").css({
      display: 'block',
    });
  }, 
  function () {
    $(this).parent().find(".mcs-icon.remove-filter").css({
      display: 'block',
    });
    $(this).parent().find(".mcs-icon.filter-icon").css({
      display: 'none',
    });
  });

  global_params.search_id = search_id;
  console.log("Global params after fetch");
  console.log(global_params);

  if (!global_params.fields) {
    global_params.fields = {};
  }

  // Declare params sent to pwg.images.filteredSearch.update
  PS_params = {};
  PS_params.search_id = search_id;

  // Setup word filter
  if (global_params.fields.allwords) {
    $(".filter-word").css("display", "flex");
    $(".filter-manager-controller.word").prop("checked", true);

    word_search_str = "";
    word_search_words = global_params.fields.allwords.words != null ? global_params.fields.allwords.words : [];
    word_search_words.forEach(word => {
      word_search_str += word + " ";
    });
    $("#word-search").val(word_search_str.slice(0, -1));

    if (global_params.fields.allwords.words && global_params.fields.allwords.words.length > 0) {
      $(".filter-word").addClass("filter-filled");
      $(".filter-word .search-words").html(word_search_str.slice(0, -1));
    } else {
      $(".filter-word .search-words").html("Mot");
    }
    

    word_search_fields = global_params.fields.allwords.fields;
    Object.keys(word_search_fields).forEach(field_key => {
      $("#"+word_search_fields[field_key]).prop("checked", true);
    });

    word_search_mode = global_params.fields.allwords.mode;
    $(".word-search-options input[value=" + word_search_mode + "]").prop("checked", true);

    if (global_params.fields.search_in_tags) {
      $("#tags").prop("checked", true);
    }

    PS_params.allwords = word_search_str.slice(0, -1);
    PS_params.allwords_fields = word_search_fields;
    PS_params.allwords_mode = word_search_mode;
  }
  //Hide filter spinner
  $(".filter-spinner").hide();

  // Setup tag filter
  $("#tag-search").each(function() {
    $(this).selectize({
      plugins: ['remove_button'],
      maxOptions:$(this).find("option").length,
      items: global_params.fields.tags ? global_params.fields.tags.words : null,
    });
  });
  if (global_params.fields.tags) {
    $(".filter-tag").css("display", "flex");
    $(".filter-manager-controller.tags").prop("checked", true);
    $(".filter-tag-form .search-params input[value=" + global_params.fields.tags.mode + "]").prop("checked", true);

    tag_search_str = "";
    $("#tag-search")[0].selectize.getValue().forEach(id => {
      tag_search_str += $("#tag-search")[0].selectize.getItem(id).text().replace(/\(\d+ \w+\)×/, '').trim() + ", ";
    });
    if (global_params.fields.tags.words && global_params.fields.tags.words.length > 0) {
      $(".filter-tag").addClass("filter-filled");
      $(".filter.filter-tag .search-words").text(tag_search_str.slice(0, -2));
    } else {
      $(".filter.filter-tag .search-words").text("Tags");
    }
    

    PS_params.tags = global_params.fields.tags.words.length > 0 ? global_params.fields.tags.words : '';
    PS_params.tags_mode = global_params.fields.tags.mode;
  }

  // Setup album filter
  if (global_params.fields.cat) {
    console.log("there is an album in the search");
    $(".filter-album").css("display", "flex");
    $(".filter-manager-controller.album").prop("checked", true);
  
    album_widget_value = "";
    global_params.fields.cat.words.forEach(cat_id => {
      add_related_category(cat_id, fullname_of_cat[cat_id]);
      album_widget_value += fullname_of_cat[cat_id] + ", ";
    });
    if (global_params.fields.cat.words && global_params.fields.cat.words.length > 0) {
      $(".filter-album").addClass("filter-filled");
      $(".filter-album .search-words").html(album_widget_value.slice(0, -2));
    } else {
      $(".filter-album .search-words").html("Album");
    }
    

    if (global_params.fields.cat.sub_inc) {
      $("#search-sub-cats").prop("checked", true);
    }

    PS_params.categories = global_params.fields.cat.words.length > 0 ? global_params.fields.cat.words : '';
    PS_params.categories_withsubs = global_params.fields.cat.sub_inc;
  }
  
  // Setup author filter
  $("#authors").each(function() {
    $(this).selectize({
      plugins: ['remove_button'],
      maxOptions:$(this).find("option").length,
      items: global_params.fields.author ? global_params.fields.author.words : null,
    });
    if (global_params.fields.author) {
      $(".filter-author").css("display", "flex");
      $(".filter-manager-controller.author").prop("checked", true);

      author_search_str = "";
      $("#authors")[0].selectize.getValue().forEach(id => {
        author_search_str += $("#authors")[0].selectize.getItem(id).text().replace(/\(\d+ \w+\)×/, '').trim() + ", ";
      });

      if (global_params.fields.author.words && global_params.fields.author.words.length > 0) {
        $(".filter-author").addClass("filter-filled");
        $(".filter.filter-author .search-words").text(author_search_str.slice(0, -2));
      } else {
        $(".filter.filter-author .search-words").text("Auteur");
      }
      

      PS_params.authors = global_params.fields.author.words.length > 0 ? global_params.fields.author.words : '';
    }
  });

  // Setup added_by filter
  $("#added_by").each(function() {
    $(this).selectize({
      plugins: ['remove_button'],
      maxOptions:$(this).find("option").length,
      items: global_params.fields.added_by ? global_params.fields.added_by : null,
    });
    if (global_params.fields.added_by) {
      $(".filter-added_by").css("display", "flex");
      $(".filter-manager-controller.added_by").prop("checked", true);

      added_search_str = "";
      $("#added_by")[0].selectize.getValue().forEach(id => {
        added_search_str += $("#added_by")[0].selectize.getItem(id).text().replace(/\(\d+ \w+\)×/, '').trim() + ", ";
      });
      if (global_params.fields.added_by && global_params.fields.added_by.length > 0) {
        $(".filter-added_by").addClass("filter-filled");
        $(".filter.filter-added_by .search-words").text(added_search_str.slice(0, -2));
      } else {
        $(".filter.filter-added_by .search-words").text("Ajouté par");
      }
      

      PS_params.added_by = global_params.fields.added_by.length > 0 ? global_params.fields.added_by : '';
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
  $(".filter-manager-popin .filter-cancel, .filter-manager-popin .filter-manager-close").on('click', function () {
    $(".filter-manager-popin").hide();
  });

  $(".filter-manager-popin .filter-validate").on('click', function () {

    $(".filter-manager-controller-container input").each(function (e) {
      if ($(this).is(':checked')) {
        if (!$(".filter.filter-" + $(this).data("wid")).is(':visible')) {
          updateFilters($(this).data("wid"), 'add');
        }
      } else {
        if ($(".filter.filter-" + $(this).data("wid")).is(':visible')) {
          updateFilters($(this).data("wid"), 'del');
        }
      }
    });
    // Set second param to true to trigger reload
    performSearch(PS_params ,false);
  })

  /**
   * Filter Word
   */
  $(".filter-word").on("click", function (e) {
    if ($(".filter-form").has(e.target).length != 0 ||
        $(e.target).hasClass("filter-form") ||
        $(e.target).hasClass("remove-filter")) {
      return;
    }
    $(".filter-word-form").toggle(0, function () {
      
      if ($(this).is(':visible')) {
        $(".filter-word").addClass("show-filter-dropdown");
        $("#word-search").focus();
      } else {
        $(".filter-word").removeClass("show-filter-dropdown");

        global_params.fields.allwords = {};
        global_params.fields.allwords.words = $("#word-search").val();
        global_params.fields.allwords.mode = $(".word-search-options input:checked").attr('value');
        
        PS_params.allwords = $("#word-search").val();
        PS_params.allwords_mode = $(".word-search-options input:checked").attr('value');

        new_fields = [];
        $(".filter-word-form .search-params input:checked").each(function () {
          if ($(this).attr("name") == "tags") {
            global_params.fields.search_in_tags = true;
          }
          new_fields.push($(this).attr("name"));
        });
        if ($(".filter-word-form .search-params input[name='tags']:checked").length == 0) {
          delete global_params.fields.search_in_tags;
        }
        global_params.fields.allwords.fields = new_fields;
        PS_params.allwords_fields = new_fields.length > 0 ? new_fields : '';
      }
    });
  });
  $(".filter-word .filter-validate").on("click", function () {
    $(".filter-word").trigger("click");
    performSearch(PS_params, true);
  });
  $(".filter-word .remove-filter").on("click", function () {
    $(this).addClass('pwg-icon-spin6 animate-spin').removeClass('pwg-icon-cancel');
    updateFilters('word', 'del');
    performSearch(PS_params, $(".filter-word").hasClass("filter-filled"));
    if (!$(".filter-word").hasClass("filter-filled")) {
      $(".filter-word").hide();
      $(".filter-manager-controller.word").prop("checked", false);
    }
  });

  /**
   * Filter Tag
   */
  $(".filter-tag").on("click", function (e) {
    if ($(".filter-form").has(e.target).length != 0 ||
        $(e.target).hasClass("filter-form") ||
        $(e.target).hasClass("remove") ||
        $(e.target).hasClass("remove-filter")) {
      return;
    }
    $(".filter-tag-form").toggle(0, function () {
      if ($(this).is(':visible')) {
        $(".filter-tag").addClass("show-filter-dropdown");
      } else {
        $(".filter-tag").removeClass("show-filter-dropdown");
        global_params.fields.tags = {};
        global_params.fields.tags.mode = $(".filter-tag-form .search-params input:checked").val();
        global_params.fields.tags.words = $("#tag-search")[0].selectize.getValue();

        PS_params.tags = $("#tag-search")[0].selectize.getValue().length > 0 ? $("#tag-search")[0].selectize.getValue() : '';
        PS_params.tags_mode = $(".filter-tag-form .search-params input:checked").val();
      }
    });
  });
  $(".filter-tag .filter-validate").on("click", function () {
    $(".filter-tag").trigger("click");
    performSearch(PS_params, true);
  });
  $(".filter-tag .remove-filter").on("click", function () {
    $(this).addClass('pwg-icon-spin6 animate-spin').removeClass('pwg-icon-cancel');
    updateFilters('tag', 'del');
    performSearch(PS_params, $(".filter-tag").hasClass("filter-filled"));
    if (!$(".filter-tag").hasClass("filter-filled")) {
      $(".filter-tag").hide();
      $(".filter-manager-controller.tags").prop("checked", false);
    }
  });

  /**
   * Filter Date
   */
  $(".filter-date").on("click", function (e) {
    if ($(".filter-form").has(e.target).length != 0 ||
        $(e.target).hasClass("filter-form") ||
        $(e.target).hasClass("remove-filter")) {
      return;
    }
    $(".filter-date-form").toggle(0, function () {
      
      if ($(this).is(':visible')) {
        $(".filter-date").addClass("show-filter-dropdown");
      } else {
        $(".filter-date").removeClass("show-filter-dropdown");
        performSearch(PS_params, true);
      }
    });
  });
  $(".filter-date .remove-filter").on("click", function () {
    $(this).addClass('pwg-icon-spin6 animate-spin').removeClass('pwg-icon-cancel');
    updateFilters('date', 'del');
    performSearch(PS_params, $(".filter-date").hasClass("filter-filled"));
    if (!$(".filter-date").hasClass("filter-filled")) {
      $(".filter-date").hide();
      $(".filter-manager-controller.date").prop("checked", false);
    }
  });

  /**
   * Filter Album
   */
  $(".filter-album").on("click", function (e) {
    if ($(".filter-form").has(e.target).length != 0 ||
        $(e.target).hasClass("filter-form") ||
        $(e.target).hasClass("remove-item") ||
        $(e.target).hasClass("remove-filter")) {
      return;
    }
    $(".filter-album-form").toggle(0, function () {
      if ($(this).is(':visible')) {
        $(".filter-album").addClass("show-filter-dropdown");
      } else {
        $(".filter-album").removeClass("show-filter-dropdown");
        global_params.fields.cat = {};
        global_params.fields.cat.words = related_categories_ids;
        // global_params.fields.cat.search_params = $(".filter-form.filter-album-form .search-params input:checked").val().toLowerCase();
        global_params.fields.cat.sub_inc = $("input[name='search-sub-cats']:checked").length != 0;

        PS_params.categories = related_categories_ids.length > 0 ? related_categories_ids : '';
        PS_params.categories_withsubs = $("input[name='search-sub-cats']:checked").length != 0;
      }
    });
  });
  $(".filter-album .filter-validate").on("click", function () {
    $(".filter-album").trigger("click");
    performSearch(PS_params, false);
  });
  $(".filter-album .remove-filter").on("click", function () {
    $(this).addClass('pwg-icon-spin6 animate-spin').removeClass('pwg-icon-cancel');
    updateFilters('album', 'del');
    performSearch(PS_params, $(".filter-album").hasClass("filter-filled"));
    if (!$(".filter-album").hasClass("filter-filled")) {
      $(".filter-album").hide();
      $(".filter-manager-controller.album").prop("checked", false);
    }
  });

  $(".add-album-button").on("click", function () {
    linked_albums_open();
    set_up_popin();
  });

  $("#linkedAlbumSearch .search-input").on('input', function () {
    if ($(this).val() != 0) {
      $("#linkedAlbumSearch .search-cancel-linked-album").show();
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
    if ($(".filter-form").has(e.target).length != 0 ||
        $(e.target).hasClass("filter-form") ||
        $(e.target).hasClass("remove") ||
        $(e.target).hasClass("remove-filter")) {
      return;
    }
    $(".filter-author-form").toggle(0, function () {
      if ($(this).is(':visible')) {
        $(".filter-author").addClass("show-filter-dropdown");
      } else {
        $(".filter-author").removeClass("show-filter-dropdown");
        global_params.fields.author = {};
        global_params.fields.author.mode = "OR";
        global_params.fields.author.words = $("#authors")[0].selectize.getValue();

        PS_params.authors = $("#authors")[0].selectize.getValue().length > 0 ? $("#authors")[0].selectize.getValue() : '';
      }
    });
  });
  $(".filter-author .filter-validate").on("click", function () {
    $(".filter-author").trigger("click");
    performSearch(PS_params, true);
  });
  $(".filter-author .remove-filter").on("click", function () {
    $(this).addClass('pwg-icon-spin6 animate-spin').removeClass('pwg-icon-cancel');
    updateFilters('authors', 'del');
    performSearch(PS_params, $(".filter-author").hasClass("filter-filled"));
    if (!$(".filter-author").hasClass("filter-filled")) {
      $(".filter-author").hide();
      $(".filter-manager-controller.author").prop("checked", false);
    }
  });

  /**
   * Added by Widget
   */
  $(".filter-added_by").on("click", function (e) {
    if ($(".filter-form").has(e.target).length != 0 ||
        $(e.target).hasClass("filter-form") ||
        $(e.target).hasClass("remove") ||
        $(e.target).hasClass("remove-filter")) {
      return;
    }
    $(".filter-added_by-form").toggle(0, function () {
      if ($(this).is(':visible')) {
        $(".filter-added_by").addClass("show-filter-dropdown");
      } else {
        $(".filter-added_by").removeClass("show-filter-dropdown");
        global_params.fields.added_by = {};
        global_params.fields.added_by.mode = "OR";
        global_params.fields.added_by.words = $("#added_by")[0].selectize.getValue();

        PS_params.added_by = $("#added_by")[0].selectize.getValue().length > 0 ? $("#added_by")[0].selectize.getValue() : '';
      }
    });
  });
  $(".filter-added_by .filter-validate").on("click", function () {
    $(".filter-added_by").trigger("click");
    performSearch(PS_params, true);
  });
  $(".filter-added_by .remove-filter").on("click", function () {
    $(this).addClass('pwg-icon-spin6 animate-spin').removeClass('pwg-icon-cancel');
    updateFilters('added_by', 'del');
    performSearch(PS_params, $(".filter-added_by").hasClass("filter-filled"));
    if (!$(".filter-added_by").hasClass("filter-filled")) {
      $(".filter-added_by").hide();
      $(".filter-manager-controller.added_by").prop("checked", false);
    }
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

function performSearch(params, reload = false) {
  console.log("params sent to updatesearch");
  console.log(params);
  $.ajax({
    url: "ws.php?format=json&method=pwg.images.filteredSearch.update",
    type:"POST",
    dataType: "json",
    data: PS_params,
    success:function(data) {
      console.log("perform search");
      console.log(data);
      if (reload) {
        reloadPage();
      }
    },
    error:function(e) {
      console.log(e);
    },
  }).done(function () {
    console.log('ajax ended');
    $(".filter-validate").find(".validate-text").css("display", "block");
    $(".filter-validate").find(".loading").hide();
    $(".remove-filter").removeClass('pwg-icon-spin6 animate-spin').addClass('pwg-icon-cancel');
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
      remove_related_category($(this).attr("id"));
    });

    linked_albums_close();
}

function remove_related_category(cat_id) {
  $("#" + cat_id).parent().remove();

  cat_to_remove_index = related_categories_ids.indexOf(parseInt(cat_id));
  if (cat_to_remove_index > -1) {
    related_categories_ids.splice(cat_to_remove_index, 1);
  }
  if (related_categories_ids.length === 0) {
    related_categories_ids = '';
  }
}

function updateFilters(filterName, mode) {
  switch (filterName) {
    case 'word':
      if (mode == 'add') {
        global_params.fields.allwords = {};

        PS_params.allwords = '';
        PS_params.allwords_mode = 'AND';
        PS_params.allwords_fields = [];
      } else if (mode == 'del') {
        delete global_params.fields.allwords;

        delete PS_params.allwords;
        delete PS_params.allwords_mode;
        delete PS_params.allwords_fields;
      }
      break;

    case 'tag':
      if (mode == 'add') {
        global_params.fields.tags = {};

        PS_params.tags = '';
        PS_params.tags_mode = 'AND';
      } else if (mode == 'del') {
        delete global_params.fields.tags;

        delete PS_params.tags;
        delete PS_params.tags_mode;
      }
      break;

    case 'album':
      if (mode == 'add') {
        global_params.fields.cat = {};

        PS_params.categories = '';
        PS_params.categories_withsubs = false;
      } else if (mode == 'del') {
        delete global_params.fields.cat;

        delete PS_params.categories;
        delete PS_params.categories_withsubs;
      }
      break;

    default:
      if (mode == 'add') {
        global_params.fields[filterName] = {};

        PS_params[filterName] = '';
      } else if (mode == 'del') {
        delete global_params.fields[filterName];

        delete PS_params[filterName];
      }
      break;
  }
}

function reloadPage(){
  location.reload(true);
}