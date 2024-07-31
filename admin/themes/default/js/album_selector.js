/*-------
Variables
-------*/
const addLinkedAlbum = $('#addLinkedAlbum');
const closeAlbumPopIn = $('#closeAlbumPopIn');
const searchInput = $('.search-input');
const searchResult = $('#searchResult');
const limitReached = $('.limitReached');
const iconCancelInput = $(".search-cancel-linked-album");
const relatedCategoriesDom = $('.related-categories-container .breadcrumb-item .remove-item');
const iconSearchingSpin = $('.searching');
const albumSelector = $('#linkedAlbumSelector');
const albumCreate = $('#linkedAlbumCreate');
const albumCheckBox = $('#album-create-check');
const linkedAddAlbum = $('#linkedAddAlbum');
const linkedModalTitle = $('#linkedModalTitle');
const linkedAlbumSwitch = $('#linkedAlbumSwitch');
const linkedAlbumSubTitle = $('#linkedAlbumSubtitle');
const linkedAddNewAlbum = $('#linkedAddNewAlbum');
const linkedAlbumInput = $('#linkedAlbumInput');
const putToRoot = $('.put-to-root-container');
const linkedAlbumCancel = $('#linkedAlbumCancel');
const linkedAddAlbumErrors = $('#linkedAddAlbumErrors');
const AddAlbumErrors = $('.AddAlbumErrors');

let isAlbumCreationChecked = false;

/*--------------------
Document ready / event
--------------------*/
$(function() {
  iconCancelInput.hide();
  albumCreate.hide();

  // event close pop in
  closeAlbumPopIn.on('click', () => {
    close_album_selector();
  });

  // event empty search input
  if (iconCancelInput.length) {
    iconCancelInput.on("click", function () {
      searchInput.val("");
      searchInput.trigger("input");
    });
  }

  // event perform search
  searchInput.on('input', function () {
    if ($(this).val() != 0) {
      iconCancelInput.show()
    } else {
      iconCancelInput.hide();
    }
  
    // Search input value length required to start searching
    if ($(this).val().length > 0) {
      perform_albums_search($(this).val());
    } else {
      limitReached.html(str_no_search_in_progress);
      searchResult.empty();
      prefill_search();
    }
  });

  // event remove category
  relatedCategoriesDom.on("click", function () {
    const pictureId = $(this).parents("fieldset").data("image_id");
    if (pictureId) {
      remove_related_category($(this).attr("id"), pictureId);
    } else {
      remove_related_category($(this).attr("id"));
    }
  });

  // in admin mode
  if (in_admin_mode) {
    // toggle view
    albumCheckBox.on('change', function () {
      isAlbumCreationChecked = $(this).is(':checked');
      switch_album_creation();
    });
  }
});

/*--------------
General function
--------------*/
function open_album_selector() {
  if (in_admin_mode) {
    hard_reset_album_selector();
  } else {
    reset_album_selector();
  }
  addLinkedAlbum.fadeIn();
}

function close_album_selector() {
  addLinkedAlbum.fadeOut();
}

function reset_album_selector() {
  prefill_search();
  searchInput.val('');
  searchInput.trigger("input").trigger('focus');
  limitReached.html(str_no_search_in_progress);
  albumSelector.show();
}

function switch_album_creation() {
  reset_album_selector();
  
  if (isAlbumCreationChecked) {
    if (putToRoot.length) {
      putToRoot.hide();
    }
    linkedModalTitle.hide();
    linkedModalTitle.html(str_create_and_select);
    linkedAddAlbum.fadeIn();
    linkedModalTitle.fadeIn();

    linkedAddAlbum.off('click').on('click', function() {
      switch_album_view('root');
    });
  } else {
    if (putToRoot.length) {
      putToRoot.fadeIn();
    }
    linkedModalTitle.hide();
    linkedModalTitle.html(str_album_modal_title);
    linkedModalTitle.fadeIn();
    linkedAddAlbum.fadeOut();
    linkedAddAlbum.off('click');
  }
}

function switch_album_view(cat) {
  albumSelector.hide();
  searchResult.hide();
  linkedAlbumSwitch.hide();
  albumCreate.fadeIn();

  linkedAlbumSubTitle.html(sprintf(str_add_subcat_of, cat === 'root' ? str_root_album_select : cat.name));
  linkedAddNewAlbum.off('click').on('click', function () {
    add_new_album(cat === 'root' ? cat : cat.id);
  });

  linkedAlbumCancel.off('click').on('click', function () {
    close_album_selector();
  });

  linkedAlbumInput.off('input').on('input', function () {
    hide_new_album_error();
  });
}

function select_new_album_and_close(cat) {
  if (typeof b_current_picture_id !== 'undefined') {
    add_related_category(cat.id, cat.full_name_with_admin_links, b_current_picture_id);
  } else {
    add_related_category(cat.id, cat.full_name_with_admin_links);
  }
  
  close_album_selector();
  hard_reset_album_selector();
}

function hard_reset_album_selector() {
  albumCreate.hide();
  hide_new_album_error();

  reset_album_selector();
  linkedAlbumInput.val('');
  if (albumCheckBox.is(':checked')) {
    albumCheckBox.trigger('click');
  }
  searchResult.show();
  linkedAlbumSwitch.show();
}

function hide_new_album_error() {
  AddAlbumErrors.css('visibility', 'hidden');
}

function show_new_album_error(text) {
  linkedAddAlbumErrors.html(text);
  AddAlbumErrors.css('visibility', 'visible');
}

function prefill_results(rank, cats, limit) {
  let display_div = $('#subcat-'+rank);
  if ('root' == rank){
    $("#searchResult").empty();
    display_div = $('#searchResult');
  } else {
    display_div = $('#subcat-'+rank);
  }

  cats.forEach(cat => {
      let subcat = '';
      if (cat.nb_categories > 0) {
        subcat = "<span id=" + cat.id + " class='display-subcat gallery-icon-up-open'></span>"
      }

      const iconAlbum = isAlbumCreationChecked ? 'icon-add-album' : 'gallery-icon-plus-circled';

      if (!related_categories_ids.includes(cat.id) || isAlbumCreationChecked) {
        display_div.append(
          "<div class='search-result-item' id="+ cat.id + ">" +
            subcat +
            "<div class='prefill-results-item available' id=" + cat.id + ">" +
              "<span class='search-result-path'>" + cat.name +"</span>" + 
              "<span id="+ cat.id + " class='" + iconAlbum + " item-add'></span>" +
            "</div>" +
          "</div>"
        );
      } else {
        display_div.append(
          "<div class='search-result-item already-in' id="+ cat.id + " title='" + str_already_in_related_cats + "'>" +
            subcat +
            "<div class='prefill-results-item' id=" + cat.id + ">" +
              "<span class='search-result-path'>" + cat.name +"</span>" + 
              "<span id="+ cat.id + " class='gallery-icon-plus-circled item-add notClickable' title='" + str_already_in_related_cats + "'></span>" +
            "</div>" +
          "</div>"
        );
      }

      if (rank !== 'root') {
        const item = $("#"+rank+".search-result-item");
        const margin_left = parseInt(item.css('margin-left')) + 25;
        $("#"+cat.id+".search-result-item").css('margin-left', margin_left);
        $("#"+cat.id+".search-result-item .search-result-path").css('max-width', 400 - margin_left - 80);
      }

      if (isAlbumCreationChecked) {
        $('#'+ cat.id +'.prefill-results-item').off('click').on('click', function () {
          switch_album_view(cat);
        });
      } else {
        $("#"+ cat.id +".prefill-results-item.available").off('click').on('click', function () {
          if (typeof b_current_picture_id !== 'undefined') {
            add_related_category(cat.id, in_admin_mode ? cat.full_name_with_admin_links : cat.name, b_current_picture_id);
          } else {
            add_related_category(cat.id, in_admin_mode ? cat.full_name_with_admin_links : cat.name);
          }
        });
      }
      
      $("#"+cat.id+".display-subcat").off('click').on('click', function () {
        const cat_id = $(this).prop('id');

        if($(this).hasClass('open')){
          // CLOSING SUBCAT
          $(this).removeClass('open');
          $("#subcat-"+cat.id).fadeOut();

        } else {
          // OPENING SUBCAT
          // if subcat div exist
          if ($("#subcat-"+cat.id).length){
            $(this).addClass('open');
            $("#subcat-"+cat.id).fadeIn();
          } else { // if subcat div doesn't exist
            $("#"+cat_id+".display-subcat").removeClass('gallery-icon-up-open').addClass('gallery-icon-spin6 animate-spin');
            $("#"+cat_id+".search-result-item").after(`<div id="subcat-${cat_id}" class="search-result-subcat-item"></div>`);
            prefill_search_subcats(cat_id).then(() => {
              $("#"+cat_id+".display-subcat").removeClass('gallery-icon-spin6 animate-spin').addClass('gallery-icon-up-open');
              $(this).addClass('open');
              $("#subcat-"+cat.id).fadeIn();
            });
          }
        }

      });
    
  });
  // for debug
  // console.log(limit);
  if (limit.remaining_cats > 0) {
    const text = sprintf(str_plus_albums_found, limit.limited_to, limit.total_cats);
    display_div.append(
      "<p class='and-more'>" + text + "</p>"
    );
  }
}

function fill_results(cats) {
  $("#searchResult").empty();
  cats.forEach(cat => {
    const cat_name = in_admin_mode ? cat.fullname : cat.name;
    const iconAlbum = isAlbumCreationChecked ? 'icon-add-album' : 'gallery-icon-plus-circled';

    $("#searchResult").append(
    "<div class='search-result-item' id="+ cat.id + ">" +
      "<span class='search-result-path'>" +  cat_name + "</span><span id="+ cat.id + " class='" + iconAlbum + " item-add'></span>" +
    "</div>"
    );

    if (isAlbumCreationChecked) {
      $(".search-result-item#"+ cat.id).off('click').on("click", function () {
        switch_album_view(cat);
      });
      return
    }

    console.log(related_categories_ids)
    if (related_categories_ids.includes(cat.id)) {
      $(".search-result-item #"+ cat.id +".item-add").addClass("notClickable").attr("title", str_already_in_related_cats).off('click').on("click", function (event) {
        event.preventDefault();
      });
      $("#"+cat.id+".search-result-item").addClass("notClickable").attr("title", str_already_in_related_cats).off('click').on("click", function (event) {
        event.preventDefault();
      });
    } else {
      $(".search-result-item#"+ cat.id).off('click').on("click", function () {
        if (typeof b_current_picture_id !== 'undefined') {
          add_related_category(cat.id, in_admin_mode ? cat.full_name_with_admin_links : cat.name, b_current_picture_id);
        } else {
          add_related_category(cat.id, in_admin_mode ? cat.full_name_with_admin_links : cat.name);
        }
      });
    }
  });
}

/*-----------
Ajax function
-----------*/

function prefill_search() {
  $(".linkedAlbumPopInContainer .searching").show();
  let api_params = {
    cat_id: 0,
    recursive: false,
    fullname: true,
    limit: limit_params,
  };

  in_admin_mode && (api_params.additional_output = 'full_name_with_admin_links');

  $.ajax({
    url: "ws.php?format=json&method=" + methodPwg,
    type: "POST",
    dataType: "json",
    data: api_params,
    success: function (data) {
      // for debug
      // console.log(data);
      $(".linkedAlbumPopInContainer .searching").hide();
      const cats = data.result.categories;
      const limit = data.result.limit;
      prefill_results("root", cats, limit);
    },
    error: function (e) {
      $(".linkedAlbumPopInContainer .searching").hide();
      console.log("error : ", e.message);
    },
  });
}

async function prefill_search_subcats(cat_id) {
  let api_params = {
    cat_id: cat_id,
    recursive: false,
    limit: limit_params,
  };

  in_admin_mode && (api_params.additional_output = 'full_name_with_admin_links');

  try {
    const data = await $.ajax({
      url: "ws.php?format=json&method=" + methodPwg,
      type: "POST",
      dataType: "json",
      data: api_params,
    });

    // for debug
    // console.log(data);
    const cats = data.result.categories.filter((c) => c.id != cat_id);
    const limit = data.result.limit;
    prefill_results(cat_id, cats, limit);
  } catch (e) {
    console.log("error", e.message);
  }
}

function perform_albums_search(searchText) {
  let api_params = {
    cat_id: 0,
    recursive: true,
    fullname: true,
    search: searchText,
  }

  in_admin_mode && (api_params.additional_output = 'full_name_with_admin_links');

  iconSearchingSpin.show();
  $.ajax({
    url: "ws.php?format=json&method=" + methodPwg,
    type: "POST",
    dataType: "json",
    data : api_params,
    before: function () {
      
    },
    success: function (raw_data) {
      iconSearchingSpin.hide();
      categories = raw_data.result.categories;
      fill_results(categories);

      if (raw_data.result.limit_reached) {
        limitReached.html(str_result_limit.replace("%d", categories.length));
      } else {
        if (categories.length == 1) {
          limitReached.html(str_album_found);
        } else {
          limitReached.html(str_albums_found.replace("%d", categories.length));
        }
      }
    },
    error: function (e) {
      iconSearchingSpin.hide();
      console.log(e.message);
    }
  })
}

function add_new_album(cat_id) {
  const cat_name = linkedAlbumInput.val();
  const cat_position = $("input[name=position]:checked").val();
  const api_params = {
    name: cat_name,
    parent: cat_id === 'root' ? 0 : +cat_id,
    position: cat_position,
  }

  if(!cat_name || '' === cat_name) {
    show_new_album_error(str_complete_name_field);
    return
  }

  $.ajax({
    url: 'ws.php?format=json&method=pwg.categories.add',
    type: 'POST',
    dataType: 'json',
    data: api_params,
    success: function (data) {
      if (data.stat === 'ok') {
        get_album_by_id(data.result.id);
      } else {
        show_new_album_error(str_an_error_has_occured);
      }
    },
    error: function () {
      show_new_album_error(str_an_error_has_occured);
    }
  });
}

function get_album_by_id(cat_id) {
  $.ajax({
    url: 'ws.php?format=json&method=pwg.categories.getAdminList',
    dataType: 'json',
    data: {
      cat_id,
      additional_output: 'full_name_with_admin_links',
    },
    success: function(data) {
      if(data.stat === 'ok') {
        select_new_album_and_close(data.result.categories[0]);
      } else {
        show_new_album_error(str_an_error_has_occured);
      }
    },
    error: function () {
      show_new_album_error(str_an_error_has_occured);
    }
  });
}