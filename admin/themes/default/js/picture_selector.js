let activePictureSelector = null;

/**
 * Generic picture selector: a grid of pictures, one to pick, then save.
 * It knows nothing about what the pictures are, the caller loads them and
 * saves the choice (cover of a person, thumbnail of an album...).
 *
 * @param {String} title
 * @param {String|Number|null} selectedId - Picture selected when it opens.
 * @param {Function} loadPictures - ({ page }) => Promise of { pictures, total },
 *   pictures being [{ id, src, css }] with css an optional function (size) => css object.
 * @param {Function} onSave - (picture) => Promise, the selector closes when it resolves.
 * @param {Function|null} onRandom - () => Promise, no random button when null.
 * @param {String} emptyText - Shown when there is no picture.
 */
class PictureSelector {
  static picture_size = 150;

  #title;
  #selected_id;
  #initial_id;
  #load_pictures;
  #on_save;
  #on_random;
  #empty_text;
  #pictures;
  #page;
  #total;
  #loading;
  #saving;

  constructor({
    title = '',
    selectedId = null,
    loadPictures,
    onSave,
    onRandom = null,
    emptyText = '',
  }) {
    this.instanceId = `PictureSelector-${Math.random().toString(36).substring(2, 9)}`;
    this.#title = title;
    this.#initial_id = null === selectedId ? null : String(selectedId);
    this.#load_pictures = loadPictures;
    this.#on_save = onSave;
    this.#on_random = onRandom;
    this.#empty_text = emptyText;
  }

  /*-----------
  Public method
  -----------*/
  open() {
    if (activePictureSelector && activePictureSelector !== this) {
      activePictureSelector.close();
    }
    activePictureSelector = this;

    this.#reset();
    this.#load_events();
    $('#picture_selector_modal').fadeIn();
    this.#load_next_page();
  }

  close() {
    if (activePictureSelector === this) {
      activePictureSelector = null;
    }

    this.#destroy_events();
    $('#picture_selector_modal').fadeOut();
  }

  /*----------
  Event method
  ----------*/
  #load_events() {
    const instance = `.${this.instanceId}`;

    $('#picture_selector_close, #picture_selector_cancel').off(`click${instance}`).on(`click${instance}`, () => {
      this.close();
    });

    $(document).off(`keyup${instance}`).on(`keyup${instance}`, (e) => {
      if ('Escape' === e.key && $('#picture_selector_modal').is(':visible')) {
        this.close();
      }
    });

    $('#picture_selector_grid').off(`click${instance}`).on(`click${instance}`, '.picture-selector-item', (e) => {
      this.#selected_id = String($(e.currentTarget).data('id'));
      $('#picture_selector_grid .picture-selector-item').removeClass('selected');
      $(e.currentTarget).addClass('selected');
      $('#picture_selector_error').hide();
    });

    // next page when the grid is scrolled near its bottom
    $('#picture_selector_grid').off(`scroll${instance}`).on(`scroll${instance}`, (e) => {
      const grid = e.currentTarget;
      if (grid.scrollTop + grid.clientHeight >= grid.scrollHeight - 100) {
        this.#load_next_page();
      }
    });

    $('#picture_selector_save').off(`click${instance}`).on(`click${instance}`, () => {
      this.#save();
    });

    $('#picture_selector_random').off(`click${instance}`).on(`click${instance}`, () => {
      this.#random();
    });
  }

  #destroy_events() {
    const instance = `.${this.instanceId}`;

    $('#picture_selector_close, #picture_selector_cancel, #picture_selector_save, #picture_selector_random').off(`click${instance}`);
    $('#picture_selector_grid').off(`click${instance}`).off(`scroll${instance}`);
    $(document).off(`keyup${instance}`);
  }

  /*--------------
  Dom modification
  --------------*/
  #reset() {
    this.#selected_id = this.#initial_id;
    this.#pictures = [];
    this.#page = 0;
    this.#total = null;
    this.#loading = false;
    this.#saving = false;

    $('#picture_selector_title').text(this.#title);
    $('#picture_selector_grid').empty().scrollTop(0);
    $('#picture_selector_empty').text(this.#empty_text).hide();
    $('#picture_selector_error').hide();
    $('#picture_selector_random').toggle(typeof this.#on_random === 'function');
  }

  #render_pictures(pictures) {
    const grid = $('#picture_selector_grid');
    const size = PictureSelector.picture_size;

    $.each(pictures, (i, picture) => {
      const item = $('<div class="picture-selector-item"></div>').attr('data-id', picture.id);

      const css = typeof picture.css === 'function' ? picture.css(size) : null;
      item.css(css ?? { 'background-image': `url("${picture.src}")` });

      if (String(picture.id) === this.#selected_id) {
        item.addClass('selected');
      }

      grid.append(item);
    });
  }

  #show_error(message) {
    $('#picture_selector_error').text(message ?? str_picture_selector_error).show();
  }

  /*-----------
  Load and save
  -----------*/
  #load_next_page() {
    if (this.#loading || (null !== this.#total && this.#pictures.length >= this.#total)) {
      return;
    }
    this.#loading = true;
    $('#picture_selector_loading').show();

    this.#load_pictures({ page: this.#page })
      .then(({ pictures, total }) => {
        this.#loading = false;
        $('#picture_selector_loading').hide();

        this.#total = Number(total);
        this.#page += 1;
        this.#pictures = this.#pictures.concat(pictures);
        this.#render_pictures(pictures);

        $('#picture_selector_empty').toggle(this.#pictures.length === 0);
      })
      .catch((message) => {
        this.#loading = false;
        $('#picture_selector_loading').hide();
        this.#show_error(message);
      });
  }

  #save() {
    if (this.#saving) return;

    // nothing picked or nothing changed
    if (null === this.#selected_id || this.#selected_id === this.#initial_id) {
      this.close();
      return;
    }

    const picture = this.#pictures.find((p) => String(p.id) === this.#selected_id);
    if (!picture) return;

    this.#saving = true;
    this.#on_save(picture)
      .then(() => {
        this.#saving = false;
        this.close();
      })
      .catch((message) => {
        this.#saving = false;
        this.#show_error(message);
      });
  }

  #random() {
    if (this.#saving || typeof this.#on_random !== 'function') return;

    this.#saving = true;
    this.#on_random()
      .then(() => {
        this.#saving = false;
        this.close();
      })
      .catch((message) => {
        this.#saving = false;
        this.#show_error(message);
      });
  }
}
