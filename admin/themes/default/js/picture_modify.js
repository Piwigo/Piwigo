$(document).ready(function () {
  const ab = new AlbumSelector({
    selectedCategoriesIds: related_categories_ids,
    selectAlbum: add_related_category,
    removeSelectedAlbum: remove_related_category,
    adminMode: true,
    modalTitle: str_assoc_album_ab,
  });

  $(".linked-albums.add-item").on("click", function () {
    ab.open();
  });

  $('.related-categories-container').on('click', (e) => {
    if (e.target.classList.contains("remove-item")) {
      ab.remove_selected_album($(e.target).attr('id'));
    }
  });

  // Unsaved settings message before leave this page
  let form_unsaved = false;
  let user_interacted = false;
  $('#pictureModify').find(':input').on('focus', function () {
    user_interacted = true;
  });
  $('#pictureModify').find(':input').on('change', function () {
    if (user_interacted) {
      form_unsaved = true;
      console.log($(this)[0].name, $(this));
    }
  });
  $(window).on('beforeunload', function () {
    if (form_unsaved) {
      return 'Somes changes are not registered';
    }
  });
  $('#pictureModify').on('submit', function () {
    form_unsaved = false;
  });
})

function remove_related_category({ id_album, getSelectedAlbum }) {
  $(".invisible-related-categories-select option[value="+ id_album +"]").remove();
  $(".invisible-related-categories-select").trigger('change');
  $("#" + id_album).parent().remove();
  check_related_categories(getSelectedAlbum());
}

function add_related_category({ album, addSelectedAlbum, getSelectedAlbum }) {
  if (!getSelectedAlbum().includes(album.id)) {
    $(".related-categories-container").append(
      `<div class="breadcrumb-item">
        <span class="link-path">${album.full_name_with_admin_links}</span><span id="${album.id}" class="icon-cancel-circled remove-item"></span>
      </div>`
    );

    $(".search-result-item #" + album.id).addClass("notClickable");
    $(".invisible-related-categories-select").append("<option selected value="+ album.id +"></option>").trigger('change');
    addSelectedAlbum();
  }

  check_related_categories(getSelectedAlbum());
}

function check_related_categories(selected_cat) {
  $(".linked-albums-badge").html(selected_cat.length);

  if (selected_cat.length == 0) {
    $(".linked-albums-badge").addClass("badge-red");
    $(".add-item").addClass("highlight");
    $(".orphan-photo").html(str_orphan).show();
  } else {
    $(".linked-albums-badge.badge-red").removeClass("badge-red");
    $(".add-item.highlight").removeClass("highlight");
    $(".orphan-photo").hide();
  }
}