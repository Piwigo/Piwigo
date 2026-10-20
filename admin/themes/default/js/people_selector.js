let activePeopleSelector = null;

/**
 * People selector instance, single selection: picking a person closes it.
 * @param {Function} selectPerson - Called with { person } when a person is picked or created.
 * @param {Number} perPage - Number of people loaded at once.
 */
class PeopleSelector {
  #select_person;
  #per_page;
  #people;
  #selected_people_ids;
  #search_timeout;
  #is_creation_mode;
  #loading_create;

  /**
   * UI Selectors for PeopleSelector
   */
  static selectors = {
    people_selector_modal: $('#people_selector_modal'),
    people_selector_close: $('#people_selector_close'),
    people_selector_icon: $('#people_selector_icon'),
    people_selector_face: $('#people_selector_face'),
    people_selector_search_container: $('#people_selector_search_container'),
    people_selector_search: $('#people_selector_search'),
    people_selector_search_status: $('#people_selector_search_status'),
    people_selector_search_pending: $('#people_selector_search_pending'),
    people_selector_empty: $('#people_selector_empty'),
    people_selector_list: $('#people_selector_list'),
    people_selector_create: $('#people_selector_create'),
    people_selector_create_name: $('#people_selector_create_name'),
    people_selector_create_save: $('#people_selector_create_save'),
    people_selector_create_check: $('#people_selector_create_check'),
    people_selector_error: $('#people_selector_error'),
  };

  constructor({
    selectPerson = () => {},
    perPage = 50,
  } = {}) {
    this.instanceId = `PeopleSelector-${Math.random().toString(36).substring(2, 9)}`;
    this.#select_person = selectPerson;
    this.#per_page = perPage;
    this.#people = [];
    this.#selected_people_ids = [];
    this.#is_creation_mode = false;
    this.#loading_create = false;
  }

  /*-----------
  Public method
  -----------*/
  /**
   * @param {Object|null} face - CSS from people_face_css(), shown instead of the icon.
   * @param {Array} selectedPeopleIds - People already associated, shown but not selectable.
   */
  open({ face = null, selectedPeopleIds = [] } = {}) {
    if (activePeopleSelector && activePeopleSelector !== this) {
      activePeopleSelector.close();
    }
    activePeopleSelector = this;

    this.#selected_people_ids = selectedPeopleIds.map(String);
    this.#load_events();
    this.#reset();
    this.#set_face(face);
    PeopleSelector.selectors.people_selector_modal.fadeIn();
    this.#load_people();
  }

  close() {
    if (activePeopleSelector === this) {
      activePeopleSelector = null;
    }

    clearTimeout(this.#search_timeout);
    this.#destroy_events();
    PeopleSelector.selectors.people_selector_modal.fadeOut();
  }

  /*----------
  Event method
  ----------*/
  #load_events() {
    const instance = `.${this.instanceId}`;
    const s = PeopleSelector.selectors;

    s.people_selector_close.off(`click${instance}`).on(`click${instance}`, () => {
      this.close();
    });

    $(document).off(`keyup${instance}`).on(`keyup${instance}`, (e) => {
      if (!s.people_selector_modal.is(':visible')) {
        return;
      }

      if ('Escape' === e.key) {
        this.close();
      }

      if ('Enter' === e.key && this.#is_creation_mode) {
        this.#create_person();
      }
    });

    s.people_selector_search.off(`input${instance}`).on(`input${instance}`, () => {
      clearTimeout(this.#search_timeout);
      this.#search_timeout = setTimeout(() => {
        this.#load_people(s.people_selector_search.val().trim());
      }, 300);
    });

    s.people_selector_list.off(`click${instance}`).on(`click${instance}`, '.people-card', (e) => {
      const card = $(e.currentTarget);
      if (card.hasClass('notClickable')) {
        return;
      }

      const person = this.#people[card.data('index')];
      if (person) {
        this.#pick(person);
      }
    });

    s.people_selector_create_check.off(`change${instance}`).on(`change${instance}`, (e) => {
      this.#switch_creation_mode($(e.currentTarget).is(':checked'));
    });

    s.people_selector_create_save.off(`click${instance}`).on(`click${instance}`, () => {
      this.#create_person();
    });

    s.people_selector_create_name.off(`input${instance}`).on(`input${instance}`, () => {
      s.people_selector_error.hide();
    });
  }

  #destroy_events() {
    const instance = `.${this.instanceId}`;
    const s = PeopleSelector.selectors;

    $(document).off(`keyup${instance}`);
    s.people_selector_close.off(`click${instance}`);
    s.people_selector_search.off(`input${instance}`);
    s.people_selector_list.off(`click${instance}`);
    s.people_selector_create_check.off(`change${instance}`);
    s.people_selector_create_save.off(`click${instance}`);
    s.people_selector_create_name.off(`input${instance}`);
  }

  /*--------------
  Dom modification
  --------------*/
  #reset() {
    const s = PeopleSelector.selectors;

    this.#people = [];
    this.#loading_create = false;
    s.people_selector_search.val('');
    s.people_selector_search_status.html(str_no_search_in_progress);
    s.people_selector_list.empty();
    s.people_selector_empty.hide();
    s.people_selector_error.hide();
    s.people_selector_create_name.val('');
    s.people_selector_create_check.prop('checked', false);
    this.#switch_creation_mode(false);
  }

  #set_face(face) {
    const s = PeopleSelector.selectors;

    if (face) {
      s.people_selector_face.css(face).show();
      s.people_selector_icon.hide();
    } else {
      s.people_selector_face.removeAttr('style').hide();
      s.people_selector_icon.show();
    }
  }

  #switch_creation_mode(is_creation_mode) {
    const s = PeopleSelector.selectors;
    this.#is_creation_mode = is_creation_mode;
    s.people_selector_error.hide();

    if (is_creation_mode) {
      s.people_selector_search_container.hide();
      s.people_selector_list.hide();
      s.people_selector_empty.hide();
      s.people_selector_create.show();
      s.people_selector_create_name.trigger('focus');
    } else {
      s.people_selector_create.hide();
      s.people_selector_search_container.show();
      s.people_selector_list.show();
      if (this.#people.length === 0 && '' === s.people_selector_search.val().trim()) {
        s.people_selector_empty.show();
      }
    }
  }

  #render_people() {
    const s = PeopleSelector.selectors;
    s.people_selector_list.empty();

    $.each(this.#people, (i, person) => {
      people_render_card(s.people_selector_list, person, i, (template, p) => {
        template.find('.people-card-menu, .people-card-dropdown').remove();

        if ('true' == p.is_visible) {
          template.find('.people-card-lock').hide();
        }

        if (this.#selected_people_ids.includes(String(p.id))) {
          template.addClass('notClickable').attr('title', str_people_already_associated);
        }
      });
    });
  }

  #pick(person) {
    this.#select_person({ person });
    this.close();
  }

  #show_error(message) {
    PeopleSelector.selectors.people_selector_error.text(message ?? str_people_selector_error).show();
  }

  /*-----------
  Ajax method
  -----------*/
  #load_people(search = '') {
    const s = PeopleSelector.selectors;
    s.people_selector_search_pending.show();

    const data = {
      per_page: this.#per_page,
      page: 0,
    };

    if ('' !== search) {
      data.search = search;
    }

    $.ajax({
      url: 'ws.php?format=json&method=pwg.people.getAdminList',
      type: 'GET',
      dataType: 'json',
      data: data,
      success: (res) => {
        s.people_selector_search_pending.hide();

        if ('ok' !== res.stat) {
          this.#show_error(res.message);
          return;
        }

        this.#people = res.result.people;
        this.#render_people();

        if ('' === search) {
          s.people_selector_search_status.html(str_no_search_in_progress);
          this.#people.length === 0 ? s.people_selector_empty.show() : s.people_selector_empty.hide();
        } else {
          s.people_selector_empty.hide();
          const total = res.result.paging.total;
          s.people_selector_search_status.html(sprintf(1 == total ? str_person_found : str_people_found, total));
        }
      },
      error: () => {
        s.people_selector_search_pending.hide();
        this.#show_error();
      },
    });
  }

  #create_person() {
    const s = PeopleSelector.selectors;
    const name = s.people_selector_create_name.val().trim();

    if (this.#loading_create || '' === name) {
      return;
    }
    this.#loading_create = true;

    $.ajax({
      url: 'ws.php?format=json&method=pwg.people.add',
      type: 'POST',
      dataType: 'json',
      data: {
        name: name,
        is_visible: true,
        pwg_token: PWG_TOKEN,
      },
      success: (res) => {
        this.#loading_create = false;

        if ('ok' !== res.stat) {
          this.#show_error(res.message);
          return;
        }

        this.#pick({
          id: res.result.id,
          name: name,
          is_visible: 'true',
          photos: 0,
        });
      },
      error: () => {
        this.#loading_create = false;
        this.#show_error();
      },
    });
  }
}
