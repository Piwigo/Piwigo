/*--------------
Variables
--------------*/
const btnFirstAlbum = $('#btnFirstAlbum');
const modalFirstAlbum = $('#addFirstAlbum');
const closeModalFirstAlbum = $('#closeFirstAlbum');
const inputFirstAlbum = $('#inputFirstAlbum');
const btnAddFirstAlbum = $('#btnAddFirstAlbum');
const firstAlbum = $('.addAlbumEmptyCenter');
const uploadForm = $('#uploadForm');
const addPhotosAS = $('#addPhotosAS');
const btnPhotosAS = $('#btnPhotosAS');
const selectedAlbum = $('#selectedAlbum');
const selectedAlbumName = $('#selectedAlbumName');
const selectedAlbumEdit = $('#selectedAlbumEdit');
const btnAddFiles = $('#addFiles');
const chooseAlbumFirst = $('#chooseAlbumFirst');
const uploaderPhotos = $('#uploader');
const formatsUpdated = [];
const formats = [];

/*--------------
On DOM load
--------------*/
$(function () {
  // First album event
  if (!nb_albums) {
    btnFirstAlbum.on('click', function () {
      open_new_album_modal();
    });

    closeModalFirstAlbum.on('click', function () {
      close_new_album_modal();
    });

    btnAddFirstAlbum.on('click', function () {
      add_first_album(ab.select_album.bind(ab));
    });

    inputFirstAlbum.on('keyup', function(e) {
      if (e.key === 'Enter') {
        btnAddFirstAlbum.trigger('click');
      }
    });
  }

  const ab = new AlbumSelector({
    selectedCategoriesIds: related_categories_ids,
    selectAlbum: add_related_category,
    adminMode: true,
    modalTitle: str_drop_album_ab,
  });

  // Open album selector event
  btnPhotosAS.on('click', function () {
    ab.open();
  });
  selectedAlbumEdit.on('click', function () {
    ab.open();
  });

  // Upload logics
  $(".dont-show-again").on("click", function () {
    $.ajax({
      url: "ws.php?format=json&method=pwg.users.preferences.set",
      type: "POST",
      dataType: "JSON",
      data: {
        param: 'promote-mobile-apps',
        value: false,
      },
      success: function (res) {
        jQuery(".promote-apps").hide();
      }
    })
  });

  $("#uploadWarningsSummary a.showInfo").on('click', function () {
    $("#uploadWarningsSummary").hide();
    $("#uploadWarnings").show();
    return false;
  });

  $("#showPermissions").on('click', function () {
    $(this).parent(".showFieldset").hide();
    $("#permissions").show();
    return false;
  });

  $("#uploadOptionsContent").hide();
  $("#uploadOptions").on("click", function(){
    $("#uploadOptionsContent").slideToggle();
    $("#uploadOptions").toggleClass('options-open');
    $(".moxie-shim-html5").css("display", "none");
  })

  $("#uploader").pluploadQueue({
    // General settings
    browse_button: 'addFiles',
    container: 'uploadForm',

    // runtimes : 'html5,flash,silverlight,html4',
    runtimes: 'html5',

    // url : '../upload.php',
    url: 'ws.php?method=pwg.images.upload&format=json',

    chunk_size,

    filters: {
      // Maximum file size
      max_file_size,
      // Specify what files to browse for
      mime_types: [
        { title: "Image files", extensions: formatMode ? format_ext : file_ext }
      ]
    },

    // Rename files by clicking on their titles
    rename: formatMode,

    // Enable ability to drag'n'drop files onto the widget (currently only HTML5 supports that)
    dragdrop: true,

    preinit: {
      Init: function (up, info) {
        $('#uploader_container').removeAttr("title"); //remove the "using runtime" text

        $('#startUpload').on('click', function (e) {
          e.preventDefault();
          up.start();
        });

        $('#cancelUpload').on('click', function (e) {
          e.preventDefault();
          up.stop();
          up.trigger('UploadComplete', up.files);
        });
      }
    },

    init: {
      // update custom button state on queue change
      QueueChanged: function (up) {
        $('#addFiles').addClass("addFilesButtonChanged");
        $('#startUpload').prop('disabled', up.files.length == 0);
        $("#addFiles").removeClass('buttonLike').addClass('buttonLike');

        if (up.files.length > 0) {
          $('.plupload_filelist_footer').show();
          $('.plupload_filelist').css("overflow-y", "scroll");
        }

        if (up.files.length == 0) {
          $('#addFiles').removeClass("addFilesButtonChanged");
          $("#addFiles").removeClass('buttonLike').addClass('buttonLike');
          $('.plupload_filelist_footer').hide();
          $('.plupload_filelist').css("overflow-y", "hidden");
        }
      },

      FilesAdded: async function (up, files) {
        // Création de la liste avec plupload_id : image_name
        fileNames = {};
        exts = {};
        files.forEach((file) => {
          fileNames[file.id] = file.name;
          exts[file.id] = file.name.substr(file.name.lastIndexOf('.') + 1);
        });

        if (formatMode) {
          formats.forEach((forms) => {
            $("#"+forms[0]+" > .plupload_file_name").append(`
            <a target=\"_blank\" href=\"admin.php?page=photo-${forms[1].trim()}-properties\">
              <span class=\"icon-eye\">
              </span>
            </a>`);
            if(formatsUpdated.includes(forms[0])){
              $("#"+forms[0]+" > .plupload_file_name").after(`
              <a target=\"_blank\" href=\"admin.php?page=photo-${forms[1].trim()}-formats\">
                <span class=\"icon-attention update-warning\">
                  ${format_update_warning}
                </span>
              </a>
              <a class="remove-format" id=\"remove_${forms[0]}\">
                <span class = \"icon-cancel-circled\">
                </span>
                ${format_remove}
              </a>`);
              $("#remove_"+forms[0]).on("click", function(){
                up.removeFile(forms[0]);
              });
            }
          });
          
          // If no original image is specified
          if (!haveFormatsOriginal) {
            const images_search = await new Promise((res, rej) => {
              //ajax qui renvois les id des images dans la gallerie.
              $.ajax({
                url: "ws.php?format=json&method=pwg.images.formats.searchImage",
                type: "POST",
                data: {
                  filename_list: JSON.stringify(fileNames),
                },
                success: function (result) {
                  let data = JSON.parse(result);
                  res(data.result)
                }
              })
            })

            const notFound = [];
            const multiple = [];

            files.forEach((f) => {
              const search = images_search[f.id];
              if (search.status == "found"){
                f.format_of = search.image_id;
                formats.push([f.id,f.format_of]);
                $("#"+f.id+" > .plupload_file_name").append(`
                <a target=\"_blank\" href=\"admin.php?page=photo-${f.format_of.trim()}-properties\">
                  <span class=\"icon-eye\">
                  </span>
                </a>`);
                if (search.format_exist)
                {
                  $("#"+f.id+" > .plupload_file_name").after(`
                  <a target=\"_blank\" href=\"admin.php?page=photo-${f.format_of.trim()}-formats\">
                    <span class=\"icon-attention update-warning\">
                      ${format_update_warning}
                    </span>
                  </a>
                  <a class="remove-format" id=\"remove_${f.id}\">
                    <span class = \"icon-cancel-circled\">
                    </span>
                    ${format_remove}
                  </a>`);
                  formatsUpdated.push(f.id);
                  $("#remove_"+f.id).on("click", function(){
                    up.removeFile(f.id);
                  });
                }
              }
              else {
                if (search.status == "multiple")
                  multiple.push(f.name);
                else
                  notFound.push(f.name);
                up.removeFile(f.id);
              }
            })

            files.filter(f => images_search[f.id].status === "found");

            // If a file is not found or found more than one time
            if (notFound.length || multiple.length) {
              const [multStr, notFoundStr] = [multiple, notFound].map((tab) => {
                //Get names
                tab = tab.map(f => f.slice(0, f.indexOf('.')))
                // Remove duplicates
                tab = tab.filter((f, i) => i === tab.indexOf(f))

                // Add "and X more" if necessary
                if (tab.length > 5) {
                  tab[5] = str_and_X_others.replace('%d', tab.length - 5);
                  tab = tab.splice(0, 6);
                }
                return tab;
              })

              $.alert({
                title: str_format_warning,
                content: (notFound.length ? `<p>${str_format_warning_notFound.replace('%s', notFoundStr.join(', '))}</p>` : "")
                  + (multiple.length ? `<p>${str_format_warning_multiple.replace('%s', multStr.join(', '))}</p>` : ""),
                ...jConfirm_warning_options
              })
            }
          } else {
            if (imageFormatsExtensions)
            {
              $forms_exts = JSON.parse(imageFormatsExtensions);
            }
            else
            {
              $forms_exts = [];
            }
            files.forEach((f) => {
              f.format_of = originalImageId;
              formats.push([f.id,f.format_of]);
              $("#"+f.id+" > .plupload_file_name").append(`
              <a target=\"_blank\" href=\"admin.php?page=photo-${f.format_of.trim()}-properties\">
                <span class=\"icon-eye\">
                </span>
              </a>`);
              if ($forms_exts.indexOf(exts[f.id]) != -1)
              {
                $("#"+f.id+" > .plupload_file_name").after(`
                <a target=\"_blank\" href=\"admin.php?page=photo-${originalImageId.trim()}-formats\">
                  <span class=\"icon-attention update-warning\">
                    ${format_update_warning}
                  </span>
                </a>
                <a class="remove-format" id=\"remove_${f.id}\">
                  <span class = \"icon-cancel-circled\">
                  </span>
                  ${format_remove}
                </a>`);
                formatsUpdated.push(f.id);
                $("#remove_"+f.id).on("click", function(){
                  up.removeFile(f.id);
                });
              }
            })
          }
        }
      },

      FilesRemoved: function(up, file){ 
        formats.forEach((forms) => {
          $("#"+forms[0]+" > .plupload_file_name").append(`
          <a target=\"_blank\" href=\"admin.php?page=photo-${forms[1].trim()}-properties\">
            <span class=\"icon-eye\">
            </span>
          </a>`);
          if(formatsUpdated.includes(forms[0])){
            $("#"+forms[0]+" > .plupload_file_name").after(`
            <a target=\"_blank\" href=\"admin.php?page=photo-${forms[1].trim()}-formats\">
              <span class=\"icon-attention update-warning\">
                ${format_update_warning}
              </span>
            </a>
            <a class="remove-format" id=\"remove_${forms[0]}\">
              <span class = \"icon-cancel-circled\">
              </span>
              ${format_remove}
            </a>`);
            $("#remove_"+forms[0]).on("click", function(){
              up.removeFile(forms[0]);
            });
          }
        });
      },

      UploadProgress: function (up, file) {
        $('#uploadingActions .progressbar').width(up.total.percent + '%');
        Piecon.setProgress(up.total.percent);
      },

      BeforeUpload: function (up, file) {
        // hide buttons
        $('#startUpload, .selectFilesButtonBlock').hide();
        $('#uploadingActions').show();
        $('.format-mode-group-manager').hide();
        $('#selectedAlbumEdit').hide();
        // if (!formatMode) {
        //   var categorySelectedId = $("select[name=category] option:selected").val();
        //   var categorySelectedPath = $("select[name=category]")[0].selectize.getItem(categorySelectedId).text();
        //   $('.selectedAlbum').show().find('span').html(categorySelectedPath);
        // }

        // warn user if she wants to leave page while upload is running
        $(window).bind('beforeunload', function () {
          return str_upload_in_progress;
        });

        // no more change on category/level
        $("select[name=level]").attr("disabled", "disabled");

        // You can override settings before the file is uploaded
        var options = {
          pwg_token: pwg_token
        };

        if (formatMode) {
          options.format_of = file.format_of;
        } else {
          // options.category = $("select[name=category] option:selected").val();
          options.category = ab.get_selected_albums()[0];
          // options.level = $("select[name=level] option:selected").val();
          options.name = file.name;
        }

        options.update_mode = $('#toggleUpdateMode').is(':checked');

        up.setOption('multipart_params', options);
      },

      FileUploaded: function (up, file, info) {
        // Called when file has finished uploading
        //console.log('[FileUploaded] File:', file, "Info:", info);

        // hide item line
        $('#' + file.id).hide();

        let data = JSON.parse(info.response);

        $("#uploadedPhotos").parent("fieldset").show();

        html = '<a href="admin.php?page=photo-' + data.result.image_id + '" style="position : relative" target="_blank">';
        html += '<img src="' + data.result.square_src + '" class="thumbnail" title="' + data.result.name + '">';
        if (formatMode) html += '<div class="format-ext-name" title="' + file.name + '"><span>' + file.name.slice(file.name.indexOf('.')) + '</span></div>';
        html += '</a> ';

        $("#uploadedPhotos").prepend(html);

        // do not remove file, or it will reset the progress bar :-/
        // up.removeFile(file);
        uploadedPhotos.push(parseInt(data.result.image_id));
        if(data.result.add_status=="add"){
          addedPhotos.push(parseInt(data.result.image_id));
        }
        else{
          updatedPhotos.push(parseInt(data.result.image_id));
        }
        if (!formatMode)
          uploadCategory = data.result.category;
      },

      Error: function (up, error) {
        // Called when file has finished uploading
        //console.log('[Error] error: ', error);
        var piwigoApiResponse = JSON.parse(error.response);

        $(".errors ul").append('<li>' + piwigoApiResponse.message + '</li>');
        $(".errors").show();
      },

      UploadComplete: function (up, files) {
        // Called when all files are either uploaded or failed
        //console.log('[UploadComplete]');

        Piecon.reset();

        if (!formatMode) {
          $.ajax({
            url: "ws.php?format=json&method=pwg.images.uploadCompleted",
            type: "POST",
            data: {
              pwg_token: pwg_token,
              image_id: uploadedPhotos.join(","),
              category_id: uploadCategory.id,
            }
          });
        }

        $("#uploadForm, #permissions, .showFieldset").hide();

        const infoTextAdd = formatMode ?
          sprintf(formatsAdded_label, addedPhotos.length, [...new Set(addedPhotos)].length)
          : sprintf(photosAdded_label, addedPhotos.length);

        const infoTextUpdate = formatMode ?
          sprintf(formatsUpdated_label, updatedPhotos.length, [...new Set(updatedPhotos)].length)
          : sprintf(photosUpdated_label, updatedPhotos.length);

        if (addedPhotos.length && updatedPhotos.length)
        {
          $(".infos").append( '<ul><li>' + infoTextAdd + ', ' + infoTextUpdate + '</li></ul>');
        }
        else
        {
          const infoText = addedPhotos.length ? infoTextAdd : infoTextUpdate;
          $(".infos").append('<ul><li>' + infoText + '</li></ul>');
        }

        if (!formatMode) {
          html = sprintf(
            albumSummary_label,
            '<a href="admin.php?page=album-' + uploadCategory.id + '">' + uploadCategory.label + '</a>',
            parseInt(uploadCategory.nb_photos)
          );

          $(".infos ul").append('<li>' + html + '</li>');
        }

        $(".infos").show();

        // TODO: use a new method pwg.caddie.empty +
        // pwg.caddie.add(uploadedPhotos) instead of relying on huge GET parameter
        // (and remove useless code from admin/photos_add_direct.php)

        $(".batchLink").attr("href", "admin.php?page=photos_add&section=direct&batch=" + [...new Set(uploadedPhotos)].join(","));
        $(".batchLink").html(sprintf(batch_Label, uploadedPhotos.length));

        $(".afterUploadActions").show();
        $('#uploadingActions').hide();
        $('#selectedAlbumEdit').show();

        // user can safely leave page without warning
        $(window).unbind('beforeunload');
      }
    }
  });
});

/*--------------
General functions
--------------*/

function add_related_category({ album, newSelectedAlbum }) {
  let text = '';
  $(album.full_name_with_admin_links).each(function (i, s) {
    if ($(s).html()) { text += $(s).html() }
  });
  newSelectedAlbum();

  selectedAlbumName.hide();
  selectedAlbumName.html(text);
  selectedAlbumName.fadeIn();

  addPhotosAS.hide();
  selectedAlbum.fadeIn();

  enable_uploader();
}

function enable_uploader() {
  btnAddFiles.removeAttr('disabled');
  chooseAlbumFirst.hide();
  uploaderPhotos.show();
}

/*-------------------
First album functions
-------------------*/

function open_new_album_modal() {
  inputFirstAlbum.val('');
  modalFirstAlbum.fadeIn();
  inputFirstAlbum.trigger('focus');
}

function close_new_album_modal() {
  modalFirstAlbum.fadeOut();
}

function hide_first_album(cat_name) {
  modalFirstAlbum.hide();
  firstAlbum.hide();

  addPhotosAS.hide();
  selectedAlbumName.html(cat_name);
  selectedAlbum.show();

  enable_uploader();
  uploadForm.fadeIn();
}

function add_first_album(add_cat) {
  const params = {
    name: inputFirstAlbum.val().toString(),
    pwg_token
  }

  $.ajax({
    url: 'ws.php?format=json&method=pwg.categories.add',
    method: 'POST',
    dataType: 'json',
    data: params,
    success: function (res) {
      if (res.stat === 'ok') {
        add_cat(res.result.id);
        hide_first_album(params.name);
      } else {
        console.error('An error has occurred');  
      }
    },
    error: function() {
      console.error('An error has occurred');
    }
  });
}