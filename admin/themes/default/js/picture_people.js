// VARIABLES
// people of the photo as displayed, saved or not, sent on save
let people_picture = [];
let people_picture_unsaved = false;
let people_picture_selector = null;
// identify modal: null index means a new association, else the edited person
let people_identify_cropper = null;
let people_identify_index = null;
let people_pending_box = null;

// ON DOM START
$(function() {
  people_picture = people_picture_people.map((p) => ({ ...p }));

  people_picture_selector = new PeopleSelector({
    selectPerson: ({ person }) => {
      people_picture.push({
        image_person_id: null,
        person_id: String(person.id),
        name: person.name,
        is_visible: person.is_visible,
        photos: person.photos,
        source: 'manual',
        is_cover: 'false',
        ...(people_pending_box ?? {}),
      });
      people_pending_box = null;
      people_picture_set_unsaved();
      people_picture_render();
    },
  });

  // associate: identify first
  $('#people_picture_associate').on('click', function() {
    people_identify_open();
  });

  // identify modal
  $('#people_identify_close, #people_identify_cancel').on('click', function() {
    people_identify_close();
  });

  $('#people_identify_skip').on('click', function() {
    people_pending_box = null;
    people_identify_close();
    people_picture_selector.open({
      selectedPeopleIds: people_picture_ids(),
    });
  });

  $('#people_identify_save').on('click', function() {
    const box = people_identify_get_box();
    if (!box) return;

    if (null === people_identify_index) {
      people_pending_box = box;
      people_identify_close();
      people_picture_selector.open({
        face: people_face_css(people_picture_medium_src, box, 70),
        selectedPeopleIds: people_picture_ids(),
      });
      return;
    }

    Object.assign(people_picture[people_identify_index], box);
    people_identify_close();
    people_picture_set_unsaved();
    people_picture_render();
  });

  $(document).on('keyup', function(e) {
    if ('Escape' === e.key && $('#people_identify_modal').is(':visible')) {
      people_identify_close();
    }
  });

  // card menu
  $('#people_picture_list').on('click', '.people-card-menu', function(e) {
    e.stopPropagation();
    $('.people-card-dropdown').hide();
    $(this).closest('.people-card').find('.people-card-dropdown').show();
  });

  $('#people_picture_list').on('click', '.dropdown-option.identify', function() {
    $('.people-card-dropdown').hide();
    people_identify_open(people_picture_card_index(this));
  });

  $('#people_picture_list').on('click', '.dropdown-option.cover', function() {
    const index = people_picture_card_index(this);
    $.each(people_picture, (i, p) => {
      p.is_cover = i === index ? 'true' : 'false';
    });
    people_picture_set_unsaved();
    people_picture_render();
  });

  $('#people_picture_list').on('click', '.dropdown-option.dissociate', function() {
    people_picture.splice(people_picture_card_index(this), 1);
    people_picture_set_unsaved();
    people_picture_render();
  });

  $('html').on('click', function() {
    $('#people_picture_list .people-card-dropdown').hide();
  });

  // save
  $('#people_picture_save').on('click', function() {
    people_picture_save();
  });

  $(window).on('beforeunload', function() {
    if (people_picture_unsaved) {
      return 'Some changes are not registered';
    }
  });

  people_picture_render();
});

// FUNCTIONS
function people_picture_ids() {
  return people_picture.map((p) => p.person_id);
}

function people_picture_card_index(el) {
  return $(el).closest('.people-card').data('index');
}

function people_picture_set_unsaved() {
  people_picture_unsaved = true;
  $('#people_picture_saved, #people_picture_error').hide();
  $('#people_picture_unsaved').show();
}

// FUNCTIONS AJAX
let people_picture_saving = false;
function people_picture_save() {
  if (people_picture_saving) return;
  people_picture_saving = true;

  const people = people_picture.map((p) => ({
    person_id: p.person_id,
    box_x1: p.box_x1 ?? null,
    box_y1: p.box_y1 ?? null,
    box_x2: p.box_x2 ?? null,
    box_y2: p.box_y2 ?? null,
    image_width: p.image_width ?? null,
    image_height: p.image_height ?? null,
    is_cover: p.is_cover,
  }));

  const show_error = (message) => {
    $('#people_picture_error_text').text(message ?? str_people_picture_error);
    $('#people_picture_error').show();
  };

  $.ajax({
    url: 'ws.php?format=json&method=pwg.people.setImagePeople',
    type: 'POST',
    dataType: 'json',
    data: {
      image_id: people_picture_image_id,
      people: JSON.stringify(people),
      pwg_token: PWG_TOKEN,
    },
    success: function(res) {
      people_picture_saving = false;

      if ('ok' !== res.stat) {
        return show_error(res.message);
      }

      people_picture = res.result.people;
      people_picture_unsaved = false;
      $('#people_picture_unsaved, #people_picture_error').hide();
      $('#people_picture_saved').show();
      people_picture_render();
    },
    error: function() {
      people_picture_saving = false;
      show_error();
    },
  });
}

function people_picture_render() {
  const list = $('#people_picture_list');
  list.empty();

  if (people_picture.length === 0) {
    list.hide();
    $('#people_picture_empty').show();
    return;
  }

  $('#people_picture_empty').hide();
  list.show();

  $.each(people_picture, (i, p) => {
    people_render_card(list, { ...p, id: p.person_id, photos: p.photos ?? 0 }, i, (template, person) => {
      if ('true' == person.is_visible) {
        template.find('.people-card-lock').hide();
      }

      const face = people_face_css(people_picture_medium_src, person, 95);
      if (face) {
        template.find('.people-card-picture').css(face).find('i').hide();
      }

      if ('true' == person.is_cover) {
        template.find('.dropdown-option.cover').hide();
        $('<i class="people-card-header people-card-cover icon-star"></i>')
          .toggleClass('with-lock', 'true' != person.is_visible)
          .attr('title', str_people_cover)
          .tipTip(people_tiptip_config)
          .prependTo(template);
      }
    });
  });
}

// IDENTIFY MODAL
function people_identify_open(index = null) {
  people_identify_index = index;

  const title = $('#people_identify_title').empty();
  if (null === index) {
    title.text(str_people_identify_person);
    $('#people_identify_skip').show();
  } else {
    title.text(str_people_identify + ' ').append($('<i></i>').text(people_picture[index].name));
    $('#people_identify_skip').hide();
  }

  // Cropper needs the image laid out to compute its size: the modal is
  // displayed transparent, Cropper is built, then the modal fades in
  const modal = $('#people_identify_modal');
  modal.stop(true, true);
  people_identify_destroy_cropper();
  modal.css('opacity', 0).show();
  people_identify_init_cropper(() => {
    modal.animate({ opacity: 1 }, 200);
  });
}

function people_identify_close() {
  $('#people_identify_modal').stop(true, true).fadeOut(200, people_identify_destroy_cropper);
}

function people_identify_destroy_cropper() {
  if (people_identify_cropper) {
    people_identify_cropper.destroy();
    people_identify_cropper = null;
  }
}

function people_identify_init_cropper(on_ready) {
  const img = document.getElementById('people_identify_img');
  const person = null === people_identify_index ? null : people_picture[people_identify_index];

  const start = () => {
    people_identify_cropper = new Cropper(img, {
      viewMode: 1,
      dragMode: 'crop',
      autoCropArea: 0.3,
      aspectRatio: 1,
      zoomable: false,
      movable: false,
      rotatable: false,
      scalable: false,
      background: false,
      guides: false,
      toggleDragModeOnDblclick: false,
      ready() {
        if (person && null !== person.box_x1 && undefined !== person.box_x1) {
          // the box is stored in pixels of the original image
          const ratio = img.naturalWidth / Number(person.image_width);
          people_identify_cropper.setData({
            x: Number(person.box_x1) * ratio,
            y: Number(person.box_y1) * ratio,
            width: (Number(person.box_x2) - Number(person.box_x1)) * ratio,
            height: (Number(person.box_y2) - Number(person.box_y1)) * ratio,
          });
        }

        on_ready();
      },
    });
  };

  if (img.complete) {
    start();
  } else {
    img.onload = start;
  }
}

/**
 * The frame converted from the displayed image (medium) to the original image.
 * @returns {Object|null}
 */
function people_identify_get_box() {
  if (!people_identify_cropper) {
    return null;
  }

  const data = people_identify_cropper.getData(true);
  const natural_width = people_identify_cropper.getImageData().naturalWidth;
  const ratio = people_picture_image_width / natural_width;

  const box = {
    box_x1: Math.max(0, Math.round(data.x * ratio)),
    box_y1: Math.max(0, Math.round(data.y * ratio)),
    box_x2: Math.min(people_picture_image_width, Math.round((data.x + data.width) * ratio)),
    box_y2: Math.min(people_picture_image_height, Math.round((data.y + data.height) * ratio)),
    image_width: people_picture_image_width,
    image_height: people_picture_image_height,
  };

  if (box.box_x1 >= box.box_x2 || box.box_y1 >= box.box_y2) {
    return null;
  }

  return box;
}
