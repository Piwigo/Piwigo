// VARIABLES
let people_per_page = 50;
let people_current_page = 0;
let people_search_value = '';
let people_search_timeout;
let people = [];
// selection mode: selected people by id, kept across pages and searches
let people_selection_mode = false;
let people_selected = {};

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

  // delete modal
  $('#people_modal_delete_close, #people_modal_delete_cancel').on('click', function() {
    close_delete_people_modal();
  });

  $('#people_modal_delete_confirm').on('click', function() {
    if (people_to_delete) {
      delete_people([people_to_delete.id]);
    }
    close_delete_people_modal();
  });

  // general keydown event (e.g espace to close modal)
  $(document).on('keydown', function (e) {
    if (e.key === 'Escape') {
      close_people_modal();
      close_delete_people_modal();
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

  // selection mode
  $('#people_selection_toggle').on('change', function() {
    people_selection_switch($(this).is(':checked'));
  });

  people_container.on('click', '.people-card', function(e) {
    if (!people_selection_mode || $(e.target).closest('.people-card-menu, .people-card-dropdown').length) {
      return;
    }

    const person = people[$(this).data('index')];
    if (!person) return;

    if (undefined === people_selected[person.id]) {
      people_selected[person.id] = person.name;
    } else {
      delete people_selected[person.id];
    }
    people_selection_refresh();
  });

  $('#people_selection_controller').on('click', 'a', function() {
    const action = $(this).data('select');
    $.each(people, function(i, person) {
      const is_selected = undefined !== people_selected[person.id];
      if ('all' === action || ('invert' === action && !is_selected)) {
        people_selected[person.id] = person.name;
      } else {
        delete people_selected[person.id];
      }
    });
    people_selection_refresh();
  });

  $('#people_selection_list').on('click', '.people-selection-remove', function() {
    delete people_selected[$(this).data('id')];
    people_selection_refresh();
  });

  // action on the selection: the options shown depend on the chosen action
  $('#people_selection_action_choice').on('change', function() {
    people_selection_action_show($(this).val());
  });

  // yes/no and confirmation checkboxes, same as the selection panel of the users
  $('#people_selection').on('click', '.user-list-checkbox', function() {
    const box = $(this);
    if (box.closest('.yes_no_radio').length) {
      box.siblings('.user-list-checkbox').removeAttr('data-selected');
      box.attr('data-selected', '1');
    } else {
      box.attr('data-selected', '1' === box.attr('data-selected') ? null : '1');
    }
  });

  $('#people_selection_apply').on('click', function() {
    const ids = Object.keys(people_selected);
    if (ids.length === 0) return;
    $('#people_selection_error').hide();

    switch ($('#people_selection_action_choice').val()) {
      case 'merge':
        merge_people();
        break;

      case 'delete':
        if ('1' !== $('#people_selection_action_delete .user-list-checkbox').attr('data-selected')) return;
        delete_people(ids, function() {
          people_selected = {};
          people_selection_action_reset();
          people_selection_refresh();
        });
        break;

      case 'visibility':
        edit_selected_people({
          person_id: ids,
          is_visible: '1' === $('#people_selection_action_visibility [data-name=visible_yes]').attr('data-selected'),
        });
        break;
    }
  });
});

// SELECTION MODE
function people_selection_switch(is_on) {
  people_selection_mode = is_on;
  people_selected = {};

  $('.people-page').toggleClass('people-selection-on', is_on);
  $('#people_selection, #people_selection_controller').toggle(is_on);
  add_person_btn.toggle(!is_on);
  $('.people-card-dropdown').hide();

  people_selection_action_reset();
  people_selection_refresh();
}

function people_selection_refresh() {
  const ids = Object.keys(people_selected);

  people_container.find('.people-card').each(function() {
    const person = people[$(this).data('index')];
    const is_selected = person && undefined !== people_selected[person.id];
    $(this).toggleClass('GroupBackgroudSelected people-card-selected', is_selected);
  });

  const list = $('#people_selection_list').empty();
  $.each(ids, function(i, id) {
    list.append(
      $('<div class="people-selection-item"></div>')
        .append($('<a class="people-selection-remove icon-cancel"></a>').attr('data-id', id))
        .append($('<p></p>').text(people_selected[id]))
    );
  });

  // merge needs at least 2 people, its destination list follows the selection
  const choice = $('#people_selection_action_choice');
  choice.find('option[value=merge]').prop('disabled', ids.length < 2);
  if ('merge' === choice.val()) {
    ids.length < 2 ? people_selection_action_reset() : people_selection_merge_fill();
  }

  $('#people_selection_nothing').toggle(ids.length === 0);
  $('#people_selection_actions').toggle(ids.length > 0);
}

function people_selection_action_show(action) {
  $('.people-selection-action .bulkAction, #people_selection_error').hide();
  if ('' !== action) {
    $('#people_selection_action_' + action).show();
  }
  $('#people_selection_apply').toggle('' !== action);

  if ('merge' === action) {
    people_selection_merge_fill();
  }
}

function people_selection_action_reset() {
  $('#people_selection_action_choice').val('');
  $('#people_selection_action_delete .user-list-checkbox').removeAttr('data-selected');
  people_selection_action_show('');
}

function people_selection_merge_fill() {
  const choice = $('#people_selection_merge_choice').empty();
  $.each(Object.keys(people_selected), function(i, id) {
    choice.append($('<option></option>').val(id).text(people_selected[id]));
  });
}

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

  // delete person, after a confirmation
  $('.dropdown-option.delete').off('click').on('click', function() {
    const person = get_card_person(this);
    if (!person) return;
    $('.people-card-dropdown').hide();
    show_delete_people_modal(person);
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

let people_to_delete = null;

function show_delete_people_modal(person) {
  people_to_delete = person;
  $('#people_modal_delete_title').text(sprintf(str_delete_person, person.name));
  $('#people_modal_delete').fadeIn();
}

function close_delete_people_modal() {
  people_to_delete = null;
  $('#people_modal_delete').fadeOut();
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
          people_toast(str_cover_updated);
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

          // only shown in selection mode
          template.append('<span class="people-card-check icon-ok"></span>');
        });
      });

      // load events
      load_people_events();
      people_selection_refresh();

      // build pagination
      render_people_footer(paging);
    },
    error: function() {
      people_loading.hide();
      people_error.show();
    }
  });
}

// toast of the standard pages toaster, it inserts html so the text is escaped
function people_toast(text, is_error = false) {
  if (typeof pwgToaster !== 'function') return;
  pwgToaster({
    text: $('<div></div>').text(text).html(),
    icon: is_error ? 'error' : 'success',
  });
}

// error in the add/edit modal when it is open, in a toast otherwise
function people_show_error(message) {
  const text = message ?? str_people_error_occured;
  if (people_modal_person.is(':visible')) {
    people_modal_error.text(text).show();
  } else {
    people_toast(text, true);
  }
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
        people_toast(str_person_added);
        people_current_page = 0;
        return load_people();
      }

      people_show_error(res.message);
    },
    error: function(e) {
      people_show_error();
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
        people_toast(str_person_updated);
        return load_people();
      }

      people_show_error(res.message);
    },
    error: function(e) {
      people_show_error();
    }
  });
}

// same values for all the selected people, the selection is kept
function edit_selected_people(data) {
  data.pwg_token = PWG_TOKEN;

  $.ajax({
    url: 'ws.php?format=json&method=pwg.people.edit',
    type: 'POST',
    dataType: 'json',
    data: data,
    success: function(res) {
      if (res.stat !== 'ok') {
        return people_toast(res.message ?? str_people_error_occured, true);
      }

      people_toast(1 === data.person_id.length ? str_person_updated : str_people_updated);
      load_people();
    },
    error: function() {
      people_toast(str_people_error_occured, true);
    }
  });
}

function merge_people() {
  const destination_id = $('#people_selection_merge_choice').val();
  const destination_name = people_selected[destination_id];
  const merge_ids = Object.keys(people_selected).filter((id) => id !== destination_id);

  $.ajax({
    url: 'ws.php?format=json&method=pwg.people.merge',
    type: 'POST',
    dataType: 'json',
    data: {
      destination_person_id: destination_id,
      merge_person_id: merge_ids,
      pwg_token: PWG_TOKEN,
    },
    success: function(res) {
      if (res.stat !== 'ok') {
        return $('#people_selection_error').text(res.message ?? str_people_error_occured).show();
      }

      people_selected = {};
      people_selection_action_reset();
      people_selection_refresh();
      people_toast(sprintf(str_people_merged, destination_name));
      load_people();
    },
    error: function() {
      $('#people_selection_error').text(str_people_error_occured).show();
    }
  });
}

function delete_people(ids, on_success = null) {
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
        if (typeof on_success === 'function') on_success();
        people_toast(1 === ids.length ? str_person_deleted : sprintf(str_people_deleted, ids.length));
        return load_people();
      }

      people_toast(res.message ?? str_people_error_occured, true);
    },
    error: function(e) {
      people_toast(str_people_error_occured, true);
    }
  });
}