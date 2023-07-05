jQuery(document).ready(function() {
  
  activateCommentDropdown();
  checkAlbumLock();

  $(".unlock-album").on('click', function () {
    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.categories.setInfo",
      type:"POST",
      dataType: "json",
      data: {
        category_id: album_id,
        visible: 'true',
      },
      success:function(data) {
        if (data.stat == "ok") {

          is_visible = 'true';
          if ($("#cat-locked").is(":checked")) {
            $("input[id='cat-locked']").trigger('click');
          }
          checkAlbumLock();

          setTimeout(
            function() {
              $('.info-message').hide()
            }, 
            5000
          )
        } else {
          $('.info-error').show()
          setTimeout(
            function() {
              $('.info-error').hide()
            }, 
            5000
          )
        }
      },
      error:function(XMLHttpRequest, textStatus, errorThrows) {
        save_button_set_loading(false)

        $('.info-error').show()
        setTimeout(
          function() {
            $('.info-error').hide()
          }, 
          5000
        )
        console.log(errorThrows);
      }
    });
  })

  jQuery('.tiptip').tipTip({
    'delay' : 0,
    'fadeIn' : 200,
    'fadeOut' : 200
  });

  
  $('#cat-properties-save').click(() => {
    save_button_set_loading(true)
    $('.info-error,.info-message').hide()

    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.categories.setInfo",
      type:"POST",
      dataType: "json",
      data: {
        category_id: album_id,
        name: $("#cat-name").val(),
        comment: $("#cat-comment").val(),
        visible: $("#cat-locked").is(":checked") ? 'false' : 'true',
        commentable: $("#cat-commentable").is(":checked") ? "true":"false",
      },
      success:function(data) {
        if (data.stat == "ok") {
          save_button_set_loading(false)

          $('.info-message').show()
          $('.cat-modification .cat-modify-info-subcontent').html(str_just_now)
          $('.cat-modification .cat-modify-info-content').html(str_just_now)

          is_visible = $("#cat-locked").is(":checked") ? 'false' : 'true';
          checkAlbumLock();

          setTimeout(
            function() {
              $('.info-message').hide()
            }, 
            5000
          )
        } else {
          $('.info-error').show()
          setTimeout(
            function() {
              $('.info-error').hide()
            }, 
            5000
          )
        }
      },
      error:function(XMLHttpRequest, textStatus, errorThrows) {
        save_button_set_loading(false)

        $('.info-error').show()
        setTimeout(
          function() {
            $('.info-error').hide()
          }, 
          5000
        )
        console.log(errorThrows);
      }
    });

    if (parent_album != default_parent_album) {
      jQuery.ajax({
        url: "ws.php?format=json&method=pwg.categories.move",
        type:"POST",
        dataType: "json",
        data: {
          category_id: album_id,
          parent: parent_album,
          pwg_token: pwg_token,
        },
        success: function (data) {
          if (data.stat === "ok") {
            $(".cat-modify-ariane").html(
              data.result.new_ariane_string
            )
            default_parent_album = parent_album;
          } else {
            $('.info-error').show()
            setTimeout(
              function() {
                $('.info-error').hide()
              }, 
              5000
            )
          }
        },
        error: function(e) {
          console.log(e.message);
        }
      });
    }
  })

  function save_button_set_loading(state = true) {
    if (state) {
      $('#cat-properties-save i').removeClass("icon-floppy")
      $('#cat-properties-save i').addClass("icon-spin6")
      $('#cat-properties-save i').addClass("animate-spin")
    } else {
      $('#cat-properties-save i').addClass("icon-floppy")
      $('#cat-properties-save i').removeClass("icon-spin6")
      $('#cat-properties-save i').removeClass("animate-spin")
    }

    $('#cat-properties-save').attr("disabled", state)
  }

  $(".deleteAlbum").on("click", function() {
    
    $.confirm({
      title: str_delete_album,
      content : function () {
        const self = this
        return $.ajax({
          url: "ws.php?format=json&method=pwg.categories.calculateOrphans",
          type: "GET",
          data: {
            category_id: album_id,
          },
          success: function (raw_data) {
            let data = JSON.parse(raw_data).result[0]

            let message = "<p>" + str_delete_album_and_his_x_subalbums
              .replace("%s", "<strong>"+album_name+"</strong>")
              .replace("%d", "<strong>"+nb_sub_albums+"</strong>") + "</p>"
            
            message += `<div class="cat-delete-modes">`;
            message += 
              `<div  ${data.nb_images_recursive? "":"style='display:none'"}> 
                <input type="radio" name="deletion-mode" value="no_delete" id="no_delete" checked>
                <label for="no_delete">${str_dont_delete_photos}</label>
              </div>`;

            if (data.nb_images_recursive) {
              let t = 0
              message += `<div> 
                <input type="radio" name="deletion-mode" value="force_delete" id="force_delete">
                <label for="force_delete">${str_delete_all_photos.replaceAll("%d", _ => [data.nb_images_recursive, data.nb_images_associated_outside][t++])}</label>
              </div>`;
            }

            if (data.nb_images_becoming_orphan)
              message += 
              `<div> 
                <input type="radio" name="deletion-mode" value="delete_orphans" id="delete_orphans">
                <label for="delete_orphans">${str_delete_orphans.replace("%d", data.nb_images_becoming_orphan)}</label>
              </div>`;
            message += `</div>`;

            self.setContent(message)
          },
          error: function(message) {
            console.log(message);
            self.setContent("An error has occured while calculating orphans")
          }
        });
      },
      buttons: {
        deleteAlbum: {
          text: str_delete_album,
          btnClass: 'btn-red',
          action: function () {
            this.showLoading()
            let deletionMode = $('input[name="deletion-mode"]:checked').val();
            delete_album(deletionMode)
            .then(()=>window.location.href = u_delete)
            .catch((err)=> {
              this.close()
              console.log(err)
            })
            return false
          },
        },
        cancel: {
          text: str_cancel
        }
      },
      ...jConfirm_confirm_options
    })
  });

  function delete_album(photo_deletion_mode) {
    return new Promise((res, rej) => {
      $.ajax({
        url: "ws.php?format=json&method=pwg.categories.delete",
        type: "POST",
        data: {
          category_id: album_id,
          photo_deletion_mode: photo_deletion_mode,
          pwg_token : pwg_token,
        },
        success: function (raw_data) {
          res()
        },
        error: function(message) {
          rej(message)
        }
      });
    })
  }

  $('#refreshRepresentative').on('click', function(e) {
    var method = 'pwg.categories.refreshRepresentative';

    $('#refreshRepresentative i').removeClass("icon-ccw").addClass("icon-spin6").addClass("animate-spin")

    jQuery.ajax({
      url: "ws.php?format=json&method="+method,
      type:"POST",
      data: {
        category_id: album_id
      },
      success:function(data) {
        var data = jQuery.parseJSON(data);
        if (data.stat == 'ok') {
          jQuery("#deleteRepresentative").show();

          jQuery(".cat-modify-representative")
            .attr('style', `background-image:url('${data.result.src}')`)
            .removeClass('icon-dice-solid')
          
          }
          else {
            console.error(data);
          }
          $('#refreshRepresentative i').addClass("icon-ccw").removeClass("icon-spin6").removeClass("animate-spin")
      },
      error:function(XMLHttpRequest, textStatus, errorThrows) {
        console.error(errorThrows);
        $('#refreshRepresentative i').addClass("icon-ccw").removeClass("icon-spin6").removeClass("animate-spin")
      }
    });

    e.preventDefault();
  });

  $('#deleteRepresentative').on('click',  function(e) {
    var method = 'pwg.categories.deleteRepresentative';

    $('#deleteRepresentative i').removeClass("icon-cancel").addClass("icon-spin6").addClass("animate-spin")

    jQuery.ajax({
      url: "ws.php?format=json&method="+method,
      type:"POST",
      data: {
        category_id: album_id
      },
      success:function(data) {
        var data = jQuery.parseJSON(data);
        if (data.stat == 'ok') {
          jQuery("#deleteRepresentative").hide();
          jQuery(".cat-modify-representative")
            .attr('style', ``)
            .addClass('icon-dice-solid')
        }
        else {
          console.error(data);
        }
        $('#deleteRepresentative i').addClass("icon-cancel").removeClass("icon-spin6").removeClass("animate-spin")
      },
      error:function(XMLHttpRequest, textStatus, errorThrows) {
        console.error(errorThrows);
        $('#deleteRepresentative i').addClass("icon-cancel").removeClass("icon-spin6").removeClass("animate-spin")
      }
    });

    e.preventDefault();
  });

  // Parent album popin
  $("#cat-parent.icon-pencil").on("click", function (e) {
    // Don't open the popin if you click on the album link
    if (e.target.localName != 'a') {
      linked_albums_open();
      set_up_popin();

      if (parent_album != 0) {
        $(".put-to-root").removeClass("notClickable");
        $(".put-to-root").click(function () {
          add_related_category(0, str_root);
        });
      } else {
        $(".put-to-root").addClass("notClickable");
      }
    }
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

  $(".allow-comments").on("click", function () {
    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.categories.setInfo",
      type:"POST",
      dataType: "json",
      data: {
        category_id: album_id,
        commentable: true,
        apply_commentable_to_subalbums: true,
      },
      beforeSend: function () {
        save_button_set_loading(true);
      },
      success:function(data) {
        if (data.stat == "ok") {

          save_button_set_loading(false);
          if (!$("#cat-commentable").is(":checked")) {
            $("#cat-commentable").trigger("click");
          }

          temp_txt = $(".info-message").text();
          $(".info-message").text(str_album_comment_allow);
          $(".info-message").show();

          setTimeout(
            function() {
              $('.info-message').hide()
              $(".info-message").text(temp_txt);
            }, 
            5000
          )
        } else {
          $('.info-error').show()
          setTimeout(
            function() {
              $('.info-error').hide()
            }, 
            5000
          )
        }
      },
      error:function(e) {
        console.log(e);
        save_button_set_loading(false);
      }
    });
  });
  $(".disallow-comments").on("click", function () {
    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.categories.setInfo",
      type:"POST",
      dataType: "json",
      data: {
        category_id: album_id,
        commentable: false,
        apply_commentable_to_subalbums: true,
      },
      beforeSend: function () {
        save_button_set_loading(true);
      },
      success:function(data) {
        if (data.stat == "ok") {

          save_button_set_loading(false);
          if ($("#cat-commentable").is(":checked")) {
            $("#cat-commentable").trigger("click");
          }

          temp_txt = $(".info-message").text();
          $(".info-message").text(str_album_comment_disallow);
          $(".info-message").show();

          setTimeout(
            function() {
              $('.info-message').hide()
              $(".info-message").text(temp_txt);
            }, 
            5000
          )
        } else {
          $('.info-error').show()
          setTimeout(
            function() {
              $('.info-error').hide()
            }, 
            5000
          )
        }
      },
      error:function(e) {
        console.log(e);
        save_button_set_loading(false);
      }
    });
  });
});

function checkAlbumLock() {
  if (is_visible == 'true') {
    $(".warnings").hide();
  } else {
    $(".warnings").show();
  }
}

// Parent album popin functions

function fill_results(cats) {
  $("#searchResult").empty();
  cats.forEach(cat => {
    $("#searchResult").append(
    "<div class='search-result-item' id="+ cat.id + ">" +
      "<span class='search-result-path'>" + cat.fullname +"</span><span class='icon-plus-circled item-add'></span>" +
    "</div>"
    );

    // If the searched albums are in the children of the current album they become unclickable
    // Same if the album is already selected

    if (parent_album == cat.id || cat.uppercats.split(',').includes(album_id+"")) {
      $(" #"+ cat.id +".search-result-item").addClass("notClickable").attr("title", str_already_in_related_cats).on("click", function (event) {
        event.preventDefault();
      });
    } else {
      $("#"+ cat.id + ".search-result-item ").on("click", function () {
        add_related_category(cat.id, cat.full_name_with_admin_links);
      });
    }
  });
}

function add_related_category(cat_id, cat_link_path) {
  if (parent_album != cat_id) {

    $("#cat-parent").html(
      cat_link_path
    );

    $(".search-result-item #" + cat_id).addClass("notClickable");
    parent_album = cat_id;
    $(".invisible-related-categories-select").append("<option selected value="+ cat_id +"></option>");

    linked_albums_close();
  }
}

function activateCommentDropdown() {
  $(".toggle-comment-option").find(".comment-option").hide();

  /* Display the option on the click on "..." */
  $(".toggle-comment-option").on("click", function () {
    $(this).find(".comment-option").toggle();
  })

  /* Hide img options and rename field on click on the screen */

  $(document).mouseup(function (e) {
    e.stopPropagation();
    let option_is_clicked = false
    $(".comment-option span").each(function () {
      if (!($(this).has(e.target).length === 0)) {
        option_is_clicked = true;
      }
    })
    if (!option_is_clicked) {
      $(".toggle-comment-option").find(".comment-option").hide();
    }
  });
}