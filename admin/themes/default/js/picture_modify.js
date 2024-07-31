$(document).ready(function () {

  $(".linked-albums.add-item").on("click", function () {
    open_album_selector();
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

function remove_related_category(cat_id) {
  $(".invisible-related-categories-select option[value="+ cat_id +"]").remove();
  $(".invisible-related-categories-select").trigger('change');
  $("#" + cat_id).parent().remove();

  cat_to_remove_index = related_categories_ids.indexOf(cat_id);
  if (cat_to_remove_index > -1) {
    related_categories_ids.splice(cat_to_remove_index, 1);
  }

  check_related_categories();
}

function add_related_category(cat_id, cat_link_path) {
  if (!related_categories_ids.includes(cat_id)) {
    $(".related-categories-container").append(
      "<div class='breadcrumb-item'>" +
        "<span class='link-path'>" + cat_link_path + "</span><span id="+ cat_id + " class='icon-cancel-circled remove-item'></span>" +
      "</div>"
    );

    $(".search-result-item #" + cat_id).addClass("notClickable");
    related_categories_ids.push(cat_id);
    $(".invisible-related-categories-select").append("<option selected value="+ cat_id +"></option>").trigger('change');

    $("#"+ cat_id).on("click", function () {
      remove_related_category($(this).attr("id"))
    })

    close_album_selector();
  }

  check_related_categories();
}

function check_related_categories() {

  $(".linked-albums-badge").html(related_categories_ids.length);

  if (related_categories_ids.length == 0) {
    $(".linked-albums-badge").addClass("badge-red");
    $(".add-item").addClass("highlight");
    $(".orphan-photo").html(str_orphan).show();
  } else {
    $(".linked-albums-badge.badge-red").removeClass("badge-red");
    $(".add-item.highlight").removeClass("highlight");
    $(".orphan-photo").hide();
  }
}