$(document).ready(function () {

  // Detect unsaved changes on any inputs
  var user_interacted = false;

  $('input, textarea, select').on('focus', function() {
      user_interacted = true;
  });

  $('input, textarea, select').on('change', function() {
      var pictureId = $(this).parents("fieldset").data("image_id");
      if (user_interacted == true) {
          showUnsavedLocalBadge(pictureId);
          
      }
  });

  $('.related-categories-container .remove-item, .datepickerDelete').on('click', function() {
    user_interacted = true;
    var pictureId = $(this).parents("fieldset").data("image_id");
    showUnsavedLocalBadge(pictureId);
    

});

  // DELETE
  $('.action-delete-picture').on('click', function(event) {
      var $fieldset = $(this).parents("fieldset");
      var pictureId = $fieldset.data("image_id");


      $.confirm({
          title: str_are_you_sure,
          draggable: false,
          titleClass: "groupDeleteConfirm",
          theme: "modern",
          content: "",
          animation: "zoom",
          boxWidth: '30%',
          useBootstrap: false,
          type: 'red',
          animateFromElement: false,
          backgroundDismiss: true,
          typeAnimated: false,
          buttons: {
              confirm: {
                  text: str_yes,
                  btnClass: 'btn-red',
                  action: function () {
                      var image_ids = [pictureId];
                      (function(ids) {
                          $.ajax({
                              type: 'POST',
                              url: 'ws.php?format=json',
                              data: {
                                  method: "pwg.images.delete",
                                  pwg_token: jQuery("input[name=pwg_token]").val(),
                                  image_id: ids.join(',')
                              },
                              dataType: 'json',
                              success: function(data) {
                                  var isOk = data.stat && data.stat === "ok";
                                  if (isOk) {
                                      console.log("Success");
                                      $fieldset.remove();
                                      $('.pagination-container').css({
                                          'pointer-events': 'none',
                                          'opacity': '0.5'
                                      });
                                      $('.button-reload').css('display', 'block');
                                      $('div[data-image_id="' + pictureId + '"]').css('display', 'flex');
                                  } else {
                                      console.log("Image was not deleted successfully");
                                  }
                              },
                              error: function(data) {
                                  console.error("Error occurred");
                              }
                          });
                      })(image_ids);

                      image_ids = [];
                  }
              },
              cancel: {
                  text: str_no
              }
          }
      });
  });

  // VALIDATION

  //Unit Save
  $('.action-save-picture').on('click', function(event) {
      var $fieldset = $(this).parents("fieldset");
      var pictureId = $fieldset.data("image_id");
      saveChanges(pictureId);
  });

  //Global Save
  $('.action-save-global').on('click', function(event) {
    saveAllChanges();
  });




//Categories 

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

  $('.action-sync-metadata').on('click', function() {
    var pictureId = $(this).parents("fieldset").data("image_id");
    syncMetadata(pictureId);
  });
})

function fill_results(cats, pictureId) {
  
      $("#searchResult").empty();
      cats.forEach(cat => {
          $("#searchResult").append(
          "<div class='search-result-item' id="+ cat.id + ">" +
          "<span class='search-result-path'>" + cat.fullname +"</span><span id="+ cat.id + " class='icon-plus-circled item-add' onclick='showUnsavedLocalBadge("+ pictureId + ")'></span>" +
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

  function updateUnsavedGlobalBadge() {
    var visibleLocalUnsavedCount = $(".local-unsaved-badge").filter(function() {
        return $(this).css('display') === 'block';
    }).length;

    if (visibleLocalUnsavedCount > 0) {
        $(".global-unsaved-badge").css('display', 'block');
        $("#unsaved-count").text(visibleLocalUnsavedCount);
    } else {
        $(".global-unsaved-badge").css('display', 'none');
        $("#unsaved-count").text('');
    }
}

function showUnsavedLocalBadge(pictureId) {
    hideSuccesLocalBadge(pictureId);
    hideErrorLocalBadge(pictureId);
    $("#picture-" + pictureId + " .local-unsaved-badge").css('display', 'block');
    updateUnsavedGlobalBadge();
}

function hideUnsavedLocalBadge(pictureId) {
    $("#picture-" + pictureId + " .local-unsaved-badge").css('display', 'none');
    updateUnsavedGlobalBadge();
}

$(window).on('beforeunload', function() {
    if (user_interacted) {
        return "You have unsaved changes, are you sure you want to leave this page?";
    }
});
//Error badge
function showErrorLocalBadge(pictureId) {
  $("#picture-" + pictureId + " .local-error-badge").css('display', 'block');
}
function hideErrorLocalBadge(pictureId) {
  $("#picture-" + pictureId + " .local-error-badge").css('display', 'none');
}

//Succes badge
function updateSuccessGlobalBadge() {
  var visibleLocalSuccesCount = $(".local-succes-badge").filter(function() {
      return $(this).css('display') === 'block';
  }).length;

  if (visibleLocalSuccesCount > 0) {
      showSuccesGlobalBadge()
  } else {
      hideSuccesGlobalBadge()
  }
}

function showSuccessLocalBadge(pictureId) {
  var badge = $("#picture-" + pictureId + " .local-succes-badge");
  badge.css({
      'display': 'block',
      'opacity': 1
  });

  setTimeout(() => {
      badge.fadeOut(1000, function() {
          badge.css('display', 'none');
      });
  }, 3000);
}

function hideSuccesLocalBadge(pictureId) {
  $("#picture-" + pictureId + " .local-succes-badge").css('display', 'none');
}

function showSuccesGlobalBadge() {
  var badge = $(".global-succes-badge");
  badge.css({
      'display': 'block',
      'opacity': 1
  });

  setTimeout(() => {
      badge.fadeOut(1000, function() {
          badge.css('display', 'none');
      });
  }, 3000);
}

function hideSuccesGlobalBadge() {
  $("global-succes-badge").css('display', 'none');
}

function disableLocalButton(pictureId) {
  $("#picture-" + pictureId + " .action-save-picture").addClass("disabled");

  $("#picture-" + pictureId + " .action-save-picture i").removeClass("icon-floppy").addClass("icon-spin6 animate-spin");
  disableGlobalButton();
}

function enableLocalButton(pictureId) {
  $("#picture-" + pictureId + " .action-save-picture").removeClass("disabled");

  $("#picture-" + pictureId + " .action-save-picture i").removeClass("icon-spin6 animate-spin").addClass("icon-floppy");
}

function disableGlobalButton() {
  $(".action-save-global").addClass("disabled");

  $(".action-save-global i").removeClass("icon-floppy").addClass("icon-spin6 animate-spin");
}

function enableGlobalButton() {
  $(".action-save-global").removeClass("disabled");

  $(".action-save-global i").removeClass("icon-spin6 animate-spin").addClass("icon-floppy");
}

function saveChanges(pictureId) {
    if ($("#picture-" + pictureId + " .local-unsaved-badge").css('display') === 'block') {
          disableLocalButton(pictureId)
          console.log("Saving changes for " + pictureId);

          // Retrieve Infos
          var name = $("#name-" + pictureId).val();
          var author = $("#author-" + pictureId).val();
          var date_creation = $("#date_creation-" + pictureId).val();
          var comment = $("#description-" + pictureId).val();
          var level = $("#level-" + pictureId + " option:selected").val();
          
          // Get Categories
          var categories = [];
          $("#picture-" + pictureId + " .remove-item").each(function() {
              categories.push($(this).attr("id"));
          });
          var categoriesStr = categories.join(';');

          // Get Tags
          var tags = [];
          $("#tags-" + pictureId + " option").each(function() {
              var tagId = $(this).val().replace(/~~/g, '');
              tags.push(tagId);
          });
          var tagsStr = tags.join(',');

          $.ajax({
              url: 'ws.php?format=json',
              method: 'POST',
              data: {
                  method: 'pwg.images.setInfo',
                  image_id: pictureId,
                  name: name,
                  author: author,
                  date_creation: date_creation,
                  comment: comment,
                  categories: categoriesStr,
                  tag_ids: tagsStr,
                  level: level,
                  single_value_mode: "replace",
                  multiple_value_mode: "replace",
                  pwg_token: jQuery("input[name=pwg_token]").val()
              },
              success: function(response) {
                  enableLocalButton(pictureId);
                  enableGlobalButton();
                  hideUnsavedLocalBadge(pictureId);
                  showSuccessLocalBadge(pictureId);
                  updateSuccessGlobalBadge();
                  plugginSaveOption();
              },
              error: function(xhr, status, error) {
                  enableLocalButton(pictureId);
                  enableGlobalButton();
                  hideUnsavedLocalBadge(pictureId);
                  showErrorLocalBadge(pictureId);
                  updateSuccessGlobalBadge();
                  console.error('Error:', error);
              }
          });
      } else {
          console.log("No changes to save for " + pictureId);
      }
  }
  function saveAllChanges() {
    $("fieldset").each(function() {
        var pictureId = $(this).data("image_id");
        saveChanges(pictureId);
    });
}

  function syncMetadata(pictureId){
    $.confirm({
      title: str_meta_warning,
      draggable: false,
      titleClass: "groupDeleteConfirm",
      theme: "modern",
      content: "",
      animation: "zoom",
      boxWidth: '30%',
      useBootstrap: false,
      type: 'red',
      animateFromElement: false,
      backgroundDismiss: true,
      typeAnimated: false,
      buttons: {
          confirm: {
              text: str_meta_yes,
              btnClass: 'btn-red',
              action: function () {
                  (function(ids) {
                    console.log("metadata sync");
                      $.ajax({
                          type: 'POST',
                          url: 'ws.php?format=json',
                          data: {
                              method: "pwg.images.syncMetadata",
                              pwg_token: jQuery("input[name=pwg_token]").val(),
                              image_id: pictureId
                          },
                          dataType: 'json',
                          success: function(data) {
                            console.log("metadata sync done, starting update");
                              var isOk = data.stat && data.stat === "ok";
                              if (isOk) {
                                  console.log("Success, now updating current block");
                                  $.ajax({
                                    url: 'ws.php?format=json',
                                    type: 'GET',
                                    dataType: 'json',
                                    data: {
                                        method: 'pwg.images.getInfo',
                                        image_id: image_Id,
                                        format: 'json'
                                    },
                                    success: function(response) {
                                        var isOk = data.stat && data.stat === "ok";
                                        if (response.stat === 'ok') {
                                            console.log("success");
                                            $("#picture-" + pictureId + " #name-" + pictureId).val(response.result.name);
                                            $("#picture-" + pictureId + " #author-" + pictureId).val(response.result.author);
                                            $("#picture-" + pictureId + " #date_creation-" + pictureId).val(response.result.date_creation);
                                            $("#picture-" + pictureId + " #description-" + pictureId).val(response.result.comment);
                                            $("#picture-" + pictureId + " #level-" + pictureId).val(response.result.level);
                                            $("#picture-" + pictureId + " #filename-" + pictureId).text(response.result.file);
                                            $("#picture-" + pictureId + " #filesize-" + pictureId).text(response.result.filesize);
                                            $("#picture-" + pictureId + " #dimensions-" + pictureId).text(response.result.width + "x" + response.result.height);                                          

                                        } else {
                                            console.error("Error:", response.message);
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error("Error:", status, error);
                                    }
                                  });
                              } else {
                                  console.log("Error");
                              }
                          },
                          error: function(data) {
                              console.error("Error occurred");
                          }
                      });
                  })
              }
          },
          cancel: {
              text: str_meta_no
          }
      }
  });
  }

function plugginSaveOption() {
  //call your pluggin save functions here
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


