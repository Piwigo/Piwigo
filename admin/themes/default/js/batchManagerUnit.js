$(document).ready(function () {

  $(".linked-albums.add-item").on("click", function () {
      var pictureId = $(this).parents("fieldset").data("image_id")
      linked_albums_open(pictureId);
      set_up_popin();
  });

  $(".limitReached").html(str_no_search_in_progress);
  $(".search-cancel-linked-album").hide();
  $(".linkedAlbumPopInContainer .searching").hide();
  $("#linkedAlbumSearch .search-input").on('input', function () {
    var pictureId = $("#linkedAlbumSearch .search-input").parents(".linkedAlbumPopInContainer").attr("id");

      if ($(this).val() != 0) {
      $("#linkedAlbumSearch .search-cancel-linked-album").show()
      } else {
      $("#linkedAlbumSearch .search-cancel-linked-album").hide();
      }

      // Search input value length required to start searching
      if ($(this).val().length > 0) {

        linked_albums_search($(this).val(), pictureId );
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
      var pictureId = $(this).parents("fieldset").data("image_id")
      remove_related_category($(this).attr("id"),pictureId);
  })

})

function fill_results(cats, pictureId) {
  
      $("#searchResult").empty();
      cats.forEach(cat => {
          $("#searchResult").append(
          "<div class='search-result-item' id="+ cat.id + ">" +
          "<span class='search-result-path'>" + cat.fullname +"</span><span id="+ cat.id + " class='icon-plus-circled item-add'></span>" +
          "</div>"
          );
          var this_related_category_ids = window["related_category_ids_" + pictureId];
          var catId = parseInt(cat.id);
          if (this_related_category_ids.includes(catId)) {
              $(".search-result-item#"+ catId +" .item-add").addClass("notClickable").attr("title", str_already_in_related_cats).on("click", function (event) {
              event.preventDefault();
              });
              $(".search-result-item").addClass("notClickable").attr("title", str_already_in_related_cats).on("click", function (event) {
              event.preventDefault();
              });
          } else {
              $(".search-result-item#"+ catId+ " .item-add").on("click", function () {
              add_related_category(catId, cat.full_name_with_admin_links, pictureId);
              });
          }
    });
  }

  function remove_related_category(cat_id,pictureId) {
    var catId = parseInt(cat_id);
    var this_related_category_ids = window["related_category_ids_" + pictureId];
    $("#"+pictureId+" .invisible-related-categories-select option[value="+ catId +"]").remove();
    $("#"+pictureId+" .invisible-related-categories-select").trigger('change');
    $("#"+pictureId+" #" + catId).parent().remove();
  
    cat_to_remove_index = this_related_category_ids.indexOf(catId);
    if (cat_to_remove_index > -1) {
      this_related_category_ids.splice(cat_to_remove_index, 1);
    }
    check_related_categories(pictureId);
  }
  
  function add_related_category(cat_id, cat_link_path, pictureId) {
    var catId = parseInt(cat_id);
    var this_related_category_ids = window["related_category_ids_" + pictureId];
    if (!this_related_category_ids.includes(catId)) {
      $("#"+pictureId+" .related-categories-container").append(
        "<div class='breadcrumb-item album-listed'>" +
          "<span class='link-path'>" + cat_link_path + "</span><span id="+ catId + " class='icon-cancel-circled remove-item'></span>" +
        "</div>"
      );
  
      $(".search-result-item#" + catId).addClass("notClickable");
      this_related_category_ids.push(catId);
      $(".invisible-related-categories-select").append("<option selected value="+ catId +"></option>").trigger('change');
  
      $("#"+ catId).on("click", function () {
        remove_related_category(catId, pictureId);
      })
  
      linked_albums_close();
    }
  
    check_related_categories(pictureId);
  }
  
  function check_related_categories(pictureId) {
    var this_related_category_ids = window["related_category_ids_" + pictureId];
  
    $("#picture-"+pictureId+" .linked-albums-badge").html(this_related_category_ids.length);
  
    if (this_related_category_ids.length == 0) {
      $("#"+pictureId+" .linked-albums-badge").addClass("badge-red");
      $("#"+pictureId+" .add-item").addClass("highlight");
      $("#"+pictureId+" .orphan-photo").html(str_orphan).show();
    } else {
      $("#"+pictureId+" .linked-albums-badge.badge-red").removeClass("badge-red");
      $("#"+pictureId+" .add-item.highlight").removeClass("highlight");
      $("#"+pictureId+" .orphan-photo").hide();
    }
  }
//   $(function () {
//     $('.privacy-filter-slider').each(function() {
//         var id = $(this).attr('id');

//         $(this).slider({
//             range: 'min',
//             value: $(this).attr('value'),
//             min: 0,
//             max: 4,
//             slide: function (event, ui) {
//                 updateCertificationFilterLabel(ui.value, id);
//             }
//         });
//     });
// });

// function updateCertificationFilterLabel(value, id) {
//     let label = strs_privacy[value];
//     $('#' + id + ' .privacy').html(label);
// }