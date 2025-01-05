jQuery(document).ready(function() {
  
  activateCommentDropdown();
  checkAlbumLock();
  const ab = new AlbumSelector({ 
    selectedCategoriesIds: related_categories_ids,
    selectAlbum: add_related_category,
    showRootButton: true,
    adminMode: true,
    currentAlbumId: album_id,
    modalTitle: str_modal_ab,
  });

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
        pwg_token: pwg_token,
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
      ab.open();
    }
  });

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

  // Modal description
  let form_unsaved = false;
  const cat_modify = $('#cat-modify');
  const desc_modal = $('#desc-modal');
  const textareas = $('.sync-textarea');
  $('#desc-zoom-square, #desc-modal-close').on('click', function() {
    desc_modal.fadeToggle();
  });
  textareas.keyup(function() {
    textareas.val($(this).val());
  });
  $(window).on('click', function(e) {
    if(e.target == desc_modal[0]){
      desc_modal.fadeToggle();
    }
  });
  $(document).on('keyup', function (e) {
    // 27 is 'Escape'
    if(e.keyCode === 27 && desc_modal.is(':visible')) {
      desc_modal.fadeToggle();
    }
  });
});

function checkAlbumLock() {
  if (is_visible == 'true') {
    $(".warnings").hide();
  } else {
    $(".warnings").css('display', 'flex');
  }
}

// Parent album popin functions

function add_related_category({ album, newSelectedAlbum, getSelectedAlbum }) {
  if (parent_album != album.id) {
    $("#cat-parent").html(
      album.full_name_with_admin_links ?? album.root
    );

    $(".search-result-item #" + album.id).addClass("notClickable");
    $(".invisible-related-categories-select").append("<option selected value="+ album.id +"></option>");

    newSelectedAlbum();
    parent_album = getSelectedAlbum()[0];
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