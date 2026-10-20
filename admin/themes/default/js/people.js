// VARIABLES
let people_per_page = 50;
let people_current_page = 0;
let people_search_value = '';
let people_search_timeout;
let people = [];

const people_container = $('#people_container');
const people_loading = $('#people_loading');
const people_error = $('#people_error');
const people_empty = $('#people_empty');
const people_empty_init = $('#people_empty_init');
const people_pagination = $('#people_pagination');
const people_pagination_container = $('#people_pagination_container');

const add_person_btn = $('#add_person');
const people_modal_person = $('#people_modal_person');
const people_modal_save = $('#people_modal_save');
const people_modal_title = $('#people_modal_title');
const people_modal_icon = $('#people_modal_icon');
const people_modal_name_input = $('#people_modal_name_input');
const people_modal_show_gallery = $('#people_modal_show_gallery');
const people_modal_error = $('#people_modal_error');
const people_search = $('#people_search');

// ON DOM START
$(function() {
  load_people(true);

  // open add/edit modal
  add_person_btn.on('click', function() {
    show_add_people_modal();
  });

  // close add/edit modal
  $('#people_modal_close, #people_modal_cancel').on('click', function() {
    close_people_modal();
  });

  // general keydown event (e.g espace to close modal)
  $(document).on('keydown', function (e) {
    if (e.key === 'Escape') {
      close_people_modal();
    }

    if (e.key === 'Enter' && people_modal_person.is(':visible')) {
      // trigger save click
      people_modal_save.trigger('click');
    }
  });

  // select per page
  $(document).on('click', '.people-per-page', function(e) {
    e.preventDefault();
    people_per_page = parseInt($(this).data('value'));
    people_current_page = 0;
    load_people();
  });

  // search
  people_search.on('input', function() {
    clearTimeout(people_search_timeout);
    const search = $(this).val().trim();
    people_search_timeout = setTimeout(function() {
      people_search_value = search;
      load_people();
    }, 300);
  });
});

// FUNCTIONS
function get_card_person(el) {
  const card = $(el).closest('.people-card');
  const id = card.attr('id')?.split('_')[1];
  const index = card.data('index');

  if (!id || !Number.isInteger(index)) {
    console.error('id or index is missing');
    return null;
  }

  const person = people[index];
  if (!person || person.id != id) {
    console.error('Person or person.id undefined');
    return null;
  }
  return person;
}

function load_people_events() {
  // open card dropdown
  $('.people-card .people-card-menu').off('click').on('click', function(e) {
    e.stopPropagation();
    const card = $(this).parent();
    const dropdown = card.find('.people-card-dropdown');
    $('.people-card-dropdown').hide();
    dropdown.show();
  });

  // edit person
  $('.dropdown-option.edit').off('click').on('click', function() {
    const person = get_card_person(this);
    if (!person) return;
    show_edit_people_modal(person);
  });

  // toggle show in gallery
  $('.dropdown-option.show, .dropdown-option.hide').off('click').on('click', function() {
    const person = get_card_person(this);
    if (!person) return;

    const data = {
      person_id: person.id,
      is_visible: $(this).hasClass('show') ? true : false,
    }
    edit_people(data);
  });

  // change the cover
  $('.dropdown-option.cover').off('click').on('click', function() {
    const person = get_card_person(this);
    if (!person) return;
    $('.people-card-dropdown').hide();
    show_people_cover_selector(person);
  });

  // delete person
  $('.dropdown-option.delete').off('click').on('click', function() {
    const person = get_card_person(this);
    if (!person) return;
    delete_people([person.id]);
  });

  // hide all dropdown on click everywhere
  $('html').off('click').on('click', function(e) {
    $('.people-card-dropdown').hide();
  });
  $('.people-card-dropdown').off('click').on('click', function(e) {
    e.stopPropagation();
  });
}

function render_people_footer(paging) {
  paging.total_pages <= 1 ? people_pagination_container.hide() : people_pagination_container.show();

  $('.people-per-page').removeClass('selected-pagination');
  $('.people-per-page[data-value="' + people_per_page + '"]').addClass('selected-pagination');

  let html = '';
  if (paging.page > 0) {
    html += '<a href="#" class="pagination-arrow left people-go-page" data-page="' + (paging.page - 1) + '"><span class="icon-left-open"></span></a>';
  } else {
    html += '<a class="pagination-arrow left unavailable"><span class="icon-left-open"></span></a>';
  }

  html += '<div class="pagination-item-container">' + people_pagination_pages(paging.page, paging.total_pages) + '</div>';

  if (paging.page < paging.total_pages - 1) {
    html += '<a href="#" class="pagination-arrow rigth people-go-page" data-page="' + (paging.page + 1) + '"><span class="icon-left-open"></span></a>';
  } else {
    html += '<a class="pagination-arrow rigth unavailable"><span class="icon-left-open"></span></a>';
  }

  people_pagination_container.html(html);
  people_pagination.css('display', 'flex');

  $(document).off('click', '.people-go-page').on('click', '.people-go-page', function(e) {
    e.preventDefault();
    people_current_page = parseInt($(this).data('page'));
    load_people();
  });
}

function people_pagination_pages(current, total) {
  if (total <= 1) return '';

  const pages = {};
  pages[0] = true;
  pages[total - 1] = true;
  if (current - 1 >= 0) pages[current - 1] = true;
  pages[current] = true;
  if (current + 1 < total) pages[current + 1] = true;

  const sorted = Object.keys(pages).map(Number).sort((a, b) => a - b);

  let html = '';
  let prev = -1;
  for (const p of sorted) {
    if (prev !== -1 && p > prev + 1) {
      html += '<span>...</span>';
    }
    if (p === current) {
      html += '<a class="actual">' + (p + 1) + '</a>';
    } else {
      html += '<a href="#" class="people-go-page" data-page="' + p + '">' + (p + 1) + '</a>';
    }
    prev = p;
  }
  return html;
}

function show_add_people_modal() {
  people_modal_title.text(str_add_new_people);
  people_modal_icon.removeClass('icon-pencil icon-yellow').addClass('icon-plus-circled icon-blue');
  
  people_modal_save.off('click').on('click', function() {
    const data = {
      name: people_modal_name_input.val().trim(),
      is_visible: people_modal_show_gallery.is(':checked'),
    };
    add_people(data);
  });

  show_people_modal();
}

function show_edit_people_modal(person) {
  people_modal_title.text(str_edit_people);
  people_modal_icon.removeClass('icon-plus-circled icon-blue').addClass('icon-pencil icon-yellow');

  people_modal_name_input.val(person.name);
  people_modal_show_gallery.prop('checked', 'true' == person.is_visible ? true : false);

  people_modal_save.off('click').on('click', function() {
    const data = {
      name: people_modal_name_input.val().trim(),
      is_visible: people_modal_show_gallery.is(':checked'),
      person_id: person.id,
    };
    edit_people(data);
  });
  show_people_modal();
}

function close_people_modal() {
  people_modal_person.fadeOut(function() {
    reset_people_modal();
  });
}

function show_people_modal() {
  people_modal_person.fadeIn();
  people_modal_name_input.trigger('focus');
}

function show_people_cover_selector(person) {
  const selector = new PictureSelector({
    title: str_people_choose_cover,
    selectedId: person.cover_image_person_id,
    emptyText: str_people_no_identified_photo,
    loadPictures: ({ page }) => new Promise((resolve, reject) => {
      $.ajax({
        url: 'ws.php?format=json&method=pwg.people.getImages',
        type: 'GET',
        dataType: 'json',
        data: {
          person_id: person.id,
          identified: true,
          per_page: 40,
          page: page,
        },
        success: (res) => {
          if ('ok' !== res.stat) return reject(res.message);

          resolve({
            total: res.result.paging.total,
            pictures: res.result.images.map((image) => ({
              id: image.image_person_id,
              src: image.tn_url,
              css: (size) => people_face_css(image.medium_url, image, size),
            })),
          });
        },
        error: () => reject(),
      });
    }),
    onSave: (picture) => new Promise((resolve, reject) => {
      $.ajax({
        url: 'ws.php?format=json&method=pwg.people.edit',
        type: 'POST',
        dataType: 'json',
        data: {
          person_id: person.id,
          cover_image_person_id: picture.id,
          pwg_token: PWG_TOKEN,
        },
        success: (res) => {
          if ('ok' !== res.stat) return reject(res.message);
          load_people();
          resolve();
        },
        error: () => reject(),
      });
    }),
  });

  selector.open();
}

function reset_people_modal() {
  people_modal_name_input.val('');
  people_modal_show_gallery.prop('checked', true);
  people_modal_save.off('click');
  people_modal_error.hide();
}

// FUNCTIONS AJAX
function load_people(is_init = false) {
  people_loading.show();
  people_container.empty();
  people_error.hide();
  people_empty.hide();
  people_empty_init.hide();
  people_pagination.hide();

  const data = {
    page: people_current_page,
    per_page: people_per_page,
  };

  if (people_search_value) {
    data.search = people_search_value;
  }

  people = [];
  $.ajax({
    url: 'ws.php?format=json&method=pwg.people.getAdminList',
    type: 'GET',
    dataType: 'json',
    data: data,
    success: function(res) {
      people_loading.hide();

      if (res.stat !== 'ok') return;

      people = res.result.people;
      const paging = res.result.paging;

      if (!people) {
        return people_error.show();
      } else if (people.length === 0 || !paging) {
        return is_init 
          ? people_empty_init.show() 
          : people_empty.show();
      }

      // update badge number
      if (res.result.total_people) {
        $('#people_badge_number').text(res.result.total_people);
      }

      // build and display person card
      $.each(people, function(i, person) {
        people_render_card(people_container, person, i, function(template, p) {
          const is_visible = 'true' == p.is_visible;
          if (is_visible) {
            template.find('.dropdown-option.show').hide();
            template.find('.people-card-lock').hide();
          } else {
            template.find('.dropdown-option.hide').hide();
          }
        });
      });

      // load events
      load_people_events();

      // build pagination
      render_people_footer(paging);
    },
    error: function() {
      people_loading.hide();
      people_error.show();
    }
  });
}

function add_people(data) {
  data.pwg_token = PWG_TOKEN;

  $.ajax({
    url: 'ws.php?format=json&method=pwg.people.add',
    type: 'POST',
    dataType: 'json',
    data: data,
    success: function(res) {
      if (res.stat === 'ok') {
        close_people_modal();
        people_current_page = 0;
        return load_people();
      }

      people_modal_error.text(res.message ?? str_people_error_occured).show();
    },
    error: function(e) {
      people_modal_error.text(str_people_error_occured).show();
    }
  });
}

function edit_people(data) {
  data.pwg_token = PWG_TOKEN;
  $.ajax({
    url: 'ws.php?format=json&method=pwg.people.edit',
    type: 'POST',
    dataType: 'json',
    data: data,
    success: function(res) {
      if (res.stat === 'ok') {
        close_people_modal();
        return load_people();
      }

      people_modal_error.text(res.message ?? str_people_error_occured).show();
    },
    error: function(e) {
      people_modal_error.text(str_people_error_occured).show();
    }
  });
}

function delete_people(ids) {
  const data = {
    pwg_token: PWG_TOKEN,
    people_ids: ids,
  }

  $.ajax({
    url: 'ws.php?format=json&method=pwg.people.delete',
    type: 'POST',
    dataType: 'json',
    data: data,
    success: function(res) {
      if (res.stat === 'ok') {
        return load_people();
      }

      // TODO: toaster
    },
    error: function(e) {
      // TODO: toaster
    }
  });
}