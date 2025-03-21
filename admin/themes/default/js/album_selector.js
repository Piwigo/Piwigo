let activeAlbumSelector = null;

$(window).on('keypress', function(e) {
  const haveAlbumSelector = $('#addLinkedAlbum').is(':visible');
  if (haveAlbumSelector && e.key === "Enter") {
    e.preventDefault();
  }
});

/**
 * Album selector instance
 * @param {Array} selectedCategoriesIds - Array of IDs for elements already selected.
 * @param {Function} selectAlbum - Function to handle the selection of an album.
 * @param {Function} removeSelectedAlbum - Function to handle the removal of a selected album.
 * @param {Boolean} showRootButton - Flag to indicate whether to show the "root" button.
 * @param {Boolean} adminMode - Flag to indicate if the selector is in admin mode.
 * @param {Number} limitParam - Maximum number of results to retrieve.
 * @param {Number} currentAlbumId - ID of the currently selected album. (Only if you use ShowRootButton to keep one album always selected)
 * @param {String} modalTitle - Custom title for the album selector modal.
 * @param {String} modalSearchPlaceholder - Custom placeholder text for the search input in the modal.
 */
class AlbumSelector {
  #in_admin_mode;
  #methodPwg;
  #limitParam;
  #isAlbumCreationChecked;
  #selectAlbum;
  #removeSelectedAlbum;
  #currentSelectedId;
  #searchCat;
  #cats;
  #selected_categories;
  #show_root_btn;
  #put_to_root;
  #current_cat;
  #title;
  #searchPlaceholder;
  #loading_add;

  /**
   * Selector for AlbumSelector
   */
  static selectors = {
    addLinkedAlbum: $('#addLinkedAlbum'),
    closeAlbumPopIn: $('#closeAlbumPopIn'),
    searchInput: $('#search-input-ab'),
    searchResult: $('#searchResult'),
    limitReached: $('.limitReached'),
    iconCancelInput: $('.search-cancel-linked-album'),
    relatedCategoriesDom: $('.related-categories-container .breadcrumb-item .remove-item'),
    iconSearchingSpin: $('.searching'),
    albumSelector: $('#linkedAlbumSelector'),
    albumCreate: $('#linkedAlbumCreate'),
    albumCheckBox: $('#album-create-check'),
    linkedAddAlbum: $('#linkedAddAlbum'),
    linkedModalTitle: $('#linkedModalTitle'),
    linkedAlbumSwitch: $('#linkedAlbumSwitch'),
    linkedAlbumSubTitle: $('#linkedAlbumSubtitle'),
    linkedAddNewAlbum: $('#linkedAddNewAlbum'),
    linkedAlbumInput: $('#linkedAlbumInput'),
    putToRoot: $('.put-to-root-container'),
    linkedAlbumCancel: $('#linkedAlbumCancel'),
    linkedAddAlbumErrors: $('#linkedAddAlbumErrors'),
    addAlbumErrors: $('.AddAlbumErrors'),
    putToRootBtn: $('#put-to-root'),
    linkedAlbumPopInContainer: $('.linkedAlbumPopInContainer'),
  };

  constructor({ 
    selectedCategoriesIds=[],
    selectAlbum=() => {},
    removeSelectedAlbum=() => {},
    showRootButton=false,
    adminMode=false,
    limitParam=50,
    currentAlbumId=0,
    modalTitle='',
    modalSearchPlaceholder='',
  }) {
    this.instanceId = `AlbumSelector-${Math.random().toString(36).substring(2, 9)}`;
    this.#in_admin_mode = adminMode;
    this.#methodPwg = adminMode ? 'pwg.categories.getAdminList' : 'pwg.categories.getList';
    this.#limitParam = limitParam;
    this.#selected_categories = adminMode ? [...selectedCategoriesIds] : selectedCategoriesIds.map(String);
    this.#isAlbumCreationChecked = false;
    this.#cats = {};
    this.#searchCat = {};
    this.#selectAlbum = (args) => selectAlbum.call(
      null,
      {
        ...args, 
        newSelectedAlbum: this.#newSelectedAlbum.bind(this),
        addSelectedAlbum: this.#addSelectedAlbum.bind(this),
        getSelectedAlbum: this.get_selected_albums.bind(this),
      }
    );
    this.#removeSelectedAlbum = (args) => removeSelectedAlbum.call(
      null,
      {
        ...args,
        getSelectedAlbum: this.get_selected_albums.bind(this),
      }
    );
    this.#currentSelectedId = '';
    this.#show_root_btn = showRootButton;
    this.#put_to_root = false;
    this.#current_cat = currentAlbumId;
    this.#title = modalTitle === '' ? str_album_modal_title : modalTitle;
    this.#searchPlaceholder = modalSearchPlaceholder === '' ? 
    str_album_modal_placeholder : modalSearchPlaceholder;
    this.#loading_add = false;

    this.#init();
  }

  #init() {
    // console.log('init id:', activeAlbumSelector.instanceId);
    if (this.#in_admin_mode && this.#show_root_btn) {
      AlbumSelector.selectors.linkedAlbumPopInContainer.addClass('big');
    }

    if (!this.#show_root_btn) {
      AlbumSelector.selectors.putToRoot.remove();
    }

    if (!this.#in_admin_mode) {
      AlbumSelector.selectors.albumCreate.remove();
      AlbumSelector.selectors.linkedAlbumSwitch.remove();
    }
  }

  /*-----------
  Public method
  -----------*/
  open() {
    if (activeAlbumSelector && activeAlbumSelector !== this) {
        activeAlbumSelector.close();
    }
    activeAlbumSelector = this;
    this.#open_album_selector();
  }

  close() {
    if (activeAlbumSelector === this) {
        activeAlbumSelector = null;
    }
    this.#close_album_selector();
  }

  remove_selected_album(id) {
    if (this.#selected_categories.includes(id)) {
      const cat_to_remove_index = this.#selected_categories.indexOf(id);
      if (cat_to_remove_index > -1) {
        this.#selected_categories.splice(cat_to_remove_index, 1);
      }
    }

    this.#removeSelectedAlbum({ id_album: id });
  }

  get_selected_albums() {
    return [...this.#selected_categories];
  }

  select_album(id) {
    this.#selected_categories.push(id.toString());
  }

  resetAll() {
    this.#selected_categories = [];
    if (this.#in_admin_mode) {
      this.#hard_reset_album_selector();
    } else {
      this.#reset_album_selector();
    }
  }

  hardUpdate(cats) {
    this.#selected_categories = cats;
  }

  /*---------------------
  in selectAlbum() method
  ---------------------*/
  #newSelectedAlbum() {
    this.#selected_categories = this.#show_root_btn && this.#put_to_root 
    ? ['0']
    : [this.#currentSelectedId];
  }

  #addSelectedAlbum() {
    this.#selected_categories.push(this.#currentSelectedId);
  }

  /*----------
  Event method
  ----------*/
  #loadGeneralEvent() {
    const instanceAb = `.${this.instanceId}`;
    // event close album selector
    AlbumSelector.selectors.closeAlbumPopIn.off(`click${instanceAb}`).on(`click${instanceAb}`, () => {
      this.#close_album_selector();
    });

    // event escape album selector
    $(document).off(`keyup${instanceAb}`).on(`keyup${instanceAb}`, (e) => {
      if (e.key === "Escape" && AlbumSelector.selectors.addLinkedAlbum.is(":visible")) {
        this.#close_album_selector();
      }

      if (e.key === 'Enter' && AlbumSelector.selectors.addLinkedAlbum.is(":visible")) {
        if ($('#linkedAddNewAlbum').is(':visible')) {
          AlbumSelector.selectors.linkedAddNewAlbum.trigger(`click${instanceAb}`);
        }
      }
    });

    // event empty search input
    if (AlbumSelector.selectors.iconCancelInput.length) {
      AlbumSelector.selectors.iconCancelInput.off(`click${instanceAb}`).on(`click${instanceAb}`, () => {
        this.#reset_search_input(true);
      });
    }

    // event perform search
    AlbumSelector.selectors.searchInput.off(`keyup${instanceAb}`).on(`keyup${instanceAb}`, (e) => {
      const searchValue = AlbumSelector.selectors.searchInput.val();
      if (searchValue.length > 0) {
        AlbumSelector.selectors.iconCancelInput.show();
      } else {
        AlbumSelector.selectors.iconCancelInput.hide();
      }
      this.#perform_albums_search(searchValue);
    });

    // event in admin mode
    if (this.#in_admin_mode) {
      AlbumSelector.selectors.albumCheckBox.off(`change${instanceAb}`).on(`change${instanceAb}`, (e) => {
        this.#isAlbumCreationChecked = $(e.currentTarget).is(':checked');
        this.#switch_album_creation();
      });
    }

    // event put root btn
    if (this.#show_root_btn) {
      AlbumSelector.selectors.putToRootBtn.off(`click${instanceAb}`).on(`click${instanceAb}`, (e) => {
        if (!this.#selected_categories.includes('0')) {
          const curr = $(e.currentTarget);
          curr.addClass('notClickable');
          this.#put_to_root = true;
          this.#selectAlbum({ album: {id: 0, root: str_root} });
          this.#close_album_selector();
        }
      });
    }
  }

  #loadPickAlbumEvent() {
    const instanceAb = `.${this.instanceId}`;
    if (this.#isAlbumCreationChecked) {
      $('.prefill-results-item').off(`click${instanceAb}`).on(`click${instanceAb}`, (e) => {
        const curr = $(e.currentTarget);
        const cat_id = curr.attr('id');
        const cat = this.#cats[cat_id];
        this.#switch_album_view(cat);
      });
    } else {
      $('.prefill-results-item.available').off(`click${instanceAb}`).on(`click${instanceAb}`, (e) => {
        const curr = $(e.currentTarget);
        const cat_id = curr.attr('id');
        const cat = this.#cats[cat_id];

        this.#currentSelectedId = cat.id;
        this.#selectAlbum({ album: cat });
        this.#close_album_selector();
      });
    }
  }

  #loadSubCatEvent() {
    const instanceAb = `.${this.instanceId}`;
    $('.display-subcat').off(`click${instanceAb}`).on(`click${instanceAb}`, (e) => {
      const curr = e.currentTarget;
      const cat_id = $(curr).prop('id');
      const cat = this.#cats[cat_id];

      if ($(curr).hasClass('open')) {
        $(curr).removeClass('open');
        $("#subcat-" + cat.id).fadeOut();
      } else if ($("#subcat-" + cat.id).length) {
        $(curr).addClass('open');
        $("#subcat-" + cat.id).fadeIn();
      } else {
        $("#" + cat_id + ".display-subcat").removeClass('gallery-icon-up-open').addClass('gallery-icon-spin6 animate-spin');
        $("#" + cat_id + ".search-result-item").after(`<div id="subcat-${cat_id}" class="search-result-subcat-item"></div>`);
        this.#prefill_search_subcats(cat_id).then(() => {
          $("#" + cat_id + ".display-subcat").removeClass('gallery-icon-spin6 animate-spin').addClass('gallery-icon-up-open');
          $(curr).addClass('open');
          $("#subcat-" + cat.id).fadeIn();
        });
      }
    });
  }

  #loadFillResultEvent(tempSelect) {
    const instanceAb = `.${this.instanceId}`;

    AlbumSelector.selectors.searchResult.find('.search-result-item').off(`click${instanceAb}`).on(`click${instanceAb}`, (e) => {
      const curr = $(e.currentTarget);
      const cat_id = curr.attr('id');
      const cat = this.#searchCat[cat_id];

      const formated_cat_id = this.#in_admin_mode ? cat.id : String(cat.id);
      if (!tempSelect.includes(formated_cat_id)) {
        this.#currentSelectedId = cat.id;
        this.#selectAlbum({ album: cat });
        this.#close_album_selector();
      }
    });
  }

  /*--------------
  General method
  --------------*/
  #setActive() {
    if (activeAlbumSelector && activeAlbumSelector !== this) {
      activeAlbumSelector.close();
    }
    activeAlbumSelector = this;
  }

  #open_album_selector() {
    this.#setActive();
    this.#loadGeneralEvent();

    if (this.#in_admin_mode) {
        this.#hard_reset_album_selector();
    } else {
        this.#reset_album_selector();
    }

    if (this.#show_root_btn && !this.#selected_categories.includes('0')) {
        AlbumSelector.selectors.putToRootBtn.removeClass('notClickable');
    } else {
        AlbumSelector.selectors.putToRootBtn.addClass('notClickable');
    }

    AlbumSelector.selectors.linkedModalTitle.html(this.#title);
    AlbumSelector.selectors.searchInput.attr('placeholder', this.#searchPlaceholder);
    AlbumSelector.selectors.addLinkedAlbum.fadeIn();
  }

  #close_album_selector() {
    this.#cats = {};
    this.#searchCat = {};
    this.#currentSelectedId = '';
    this.#put_to_root = false;
    this.#loading_add = false;

    this.#destroyEvent();

    AlbumSelector.selectors.addLinkedAlbum.fadeOut();
  }

  #reset_album_selector() {
    this.#prefill_search();
    this.#reset_search_input(false);
    // AlbumSelector.selectors.searchInput.val('');
    // // AlbumSelector.selectors.searchInput.trigger("input");
    AlbumSelector.selectors.limitReached.html(str_no_search_in_progress);
    AlbumSelector.selectors.albumSelector.show();
  }

  #hard_reset_album_selector() {
    AlbumSelector.selectors.albumCreate.hide();
    this.#hide_new_album_error();

    this.#reset_album_selector();
    AlbumSelector.selectors.linkedAlbumInput.val('');
    if (AlbumSelector.selectors.albumCheckBox.is(':checked')) {
      AlbumSelector.selectors.albumCheckBox.trigger('click');
    }
    AlbumSelector.selectors.searchResult.show();
    AlbumSelector.selectors.linkedAlbumSwitch.show();

  }

  #reset_search_input(prefill) {
    AlbumSelector.selectors.searchInput.val('');
    AlbumSelector.selectors.limitReached.show().html(str_no_search_in_progress);
    AlbumSelector.selectors.searchResult.empty();
    if(prefill) {
      this.#prefill_search();
    }
  }

  #switch_album_creation() {
    this.#reset_album_selector();
    const instanceAb = `.${this.instanceId}`;

    if (this.#isAlbumCreationChecked) {
      if (AlbumSelector.selectors.putToRoot.length) {
        AlbumSelector.selectors.putToRoot.hide();
      }
      AlbumSelector.selectors.linkedModalTitle.hide();
      AlbumSelector.selectors.linkedModalTitle.html(str_create_and_select);
      AlbumSelector.selectors.linkedAddAlbum.show();
      AlbumSelector.selectors.linkedModalTitle.fadeIn();

      AlbumSelector.selectors.linkedAddAlbum.off(`click${instanceAb}`).on(`click${instanceAb}`, () => {
        this.#switch_album_view('root');
      });
    } else {
      if (AlbumSelector.selectors.putToRoot.length) {
        AlbumSelector.selectors.putToRoot.fadeIn();
      }
      AlbumSelector.selectors.linkedModalTitle.hide();
      AlbumSelector.selectors.linkedModalTitle.html(this.#title);
      AlbumSelector.selectors.linkedModalTitle.fadeIn();
      AlbumSelector.selectors.linkedAddAlbum.hide();
      AlbumSelector.selectors.linkedAddAlbum.off('click');
    }
  }

  #switch_album_view(cat) {
    const instanceAb = `.${this.instanceId}`;

    AlbumSelector.selectors.albumSelector.hide();
    AlbumSelector.selectors.searchResult.hide();
    AlbumSelector.selectors.linkedAlbumSwitch.hide();
    AlbumSelector.selectors.albumCreate.fadeIn();

    AlbumSelector.selectors.linkedAlbumSubTitle.html(sprintf(str_add_subcat_of, cat === 'root' ? str_root_album_select : cat.name));
    AlbumSelector.selectors.linkedAddNewAlbum.off(`click${instanceAb}`).on(`click${instanceAb}`, () => {
      this.#add_new_album(cat === 'root' ? cat : cat.id);
    });

    AlbumSelector.selectors.linkedAlbumCancel.off(`click${instanceAb}`).on(`click${instanceAb}`, () => {
      this.#close_album_selector();
    });

    AlbumSelector.selectors.linkedAlbumInput.off(`input${instanceAb}`).on(`input${instanceAb}`, () => {
      this.#hide_new_album_error();
    });
  }

  #hide_new_album_error() {
    AlbumSelector.selectors.addAlbumErrors.css('visibility', 'hidden');
  }

  #show_new_album_error(text) {
    AlbumSelector.selectors.linkedAddAlbumErrors.html(text);
    AlbumSelector.selectors.addAlbumErrors.css('visibility', 'visible');
  }

  #select_new_album_and_close(cat) {
    const tempThis = this;
    this.#currentSelectedId = cat.id;
    this.#selectAlbum({ album: cat });
    this.#close_album_selector();
  }

  #destroyEvent() {
    const instanceAb = `.${this.instanceId}`;

    $(document).off(`keyup${instanceAb}`);
    $(document).off(`click${instanceAb}`);
    $(document).off(`change${instanceAb}`);
    $(document).off(`input${instanceAb}`);
    AlbumSelector.selectors.searchInput.off(`keyup${instanceAb}`);
    AlbumSelector.selectors.searchResult.find('.search-result-item').off(`click${instanceAb}`);
    $('.prefill-results-item').off(`click${instanceAb}`);
    $('.prefill-results-item.available').off(`click${instanceAb}`);
  }

  /*--------------
  Dom modification
  --------------*/
  #prefill_results(rank, cats, limit) {
    const isCreationMode = this.#isAlbumCreationChecked;
    const iconAlbum = this.#isAlbumCreationChecked ? 'icon-add-album' : 'gallery-icon-plus-circled';
    const tempSelectedCat = this.#current_cat ? [...this.#selected_categories, this.#current_cat.toString()] : [...this.#selected_categories];

    this.#cats = { ...this.#cats, ...Object.fromEntries(cats.map(c => [c.id, c])) };
    let display_div = $('#subcat-' + rank);
    if ('root' == rank) {
      AlbumSelector.selectors.searchResult.empty();
      display_div = AlbumSelector.selectors.searchResult;
    } else {
      display_div = $('#subcat-' + rank);
    }

    cats.forEach(cat => {
      let subcat = '';
      if (cat.nb_categories > 0) {
        subcat = `<span id="${cat.id}" class="display-subcat gallery-icon-up-open"></span>`
      }

      const isNotInSelectedCat = !tempSelectedCat.includes(cat.id);
      if (isCreationMode || isNotInSelectedCat ) {
        display_div.append(
          `<div class="search-result-item" id="${cat.id}">
              ${subcat}
              <div class="prefill-results-item available" id="${cat.id}">
                <span class="search-result-path"><span class="search-result-path-name">${cat.name}</span></span>
                <span id=${cat.id}" class="${iconAlbum} item-add"></span>
              </div>
            </div>`
        );
      } else {
        display_div.append(
          `<div class="search-result-item already-in" id="${cat.id}" title="${str_album_selected}">
              ${subcat}
              <div class="prefill-results-item" id="${cat.id}">
                <span class="search-result-path"><span class="search-result-path-name">${cat.name}</span></span> 
                <span id="${cat.id}" class="gallery-icon-plus-circled item-add notClickable" title="${str_album_selected}"></span>
              </div>
            </div>`
        );
      }

      if (rank !== 'root') {
        const item = $("#" + rank + ".search-result-item");
        const margin_left = parseInt(item.css('margin-left')) + 25;
        $("#" + cat.id + ".search-result-item").css('margin-left', margin_left);
        $("#" + cat.id + ".search-result-item .search-result-path").css('max-width', 400 - margin_left - 80);
      }
    });

    this.#loadPickAlbumEvent();
    this.#loadSubCatEvent();
    // for debug
    // console.log(limit);
    if (limit.remaining_cats > 0) {
      const text = sprintf(str_plus_albums_found, limit.limited_to, limit.total_cats);
      display_div.append(
        `<p class="and-more">${text}</p>`
      );
    }
  }

  #fill_results(cats) {
    const iconAlbum = this.#isAlbumCreationChecked ? 'icon-add-album' : 'gallery-icon-plus-circled';
    const tempSelectedCat = this.#current_cat ? [...this.#selected_categories, this.#current_cat.toString()] : [...this.#selected_categories];

    this.#searchCat = Object.fromEntries(cats.map(c => [c.id, c]));
    AlbumSelector.selectors.searchResult.empty();

    cats.forEach(cat => {
      const cat_name = this.#in_admin_mode ? cat.fullname : cat.name;

      AlbumSelector.selectors.searchResult.append(
        `<div class='search-result-item' id="${cat.id}">
        <span class="search-result-path">${cat_name}</span><span id="${cat.id}" class="${iconAlbum} item-add"></span>
      </div>`
      );

      if (this.#isAlbumCreationChecked) {
        const instanceAb = `.${this.instanceId}`;
        $(".search-result-item#" + cat.id).off(`click${instanceAb}`).on(`click${instanceAb}`, () => {
          this.#switch_album_view(cat);
        });
        return
      }

      if (tempSelectedCat.includes(cat.id)) {
        $(".search-result-item #" + cat.id + ".item-add").addClass("notClickable").attr("title", str_album_selected);
        $("#" + cat.id + ".search-result-item").addClass("notClickable").attr("title", str_album_selected);
      } 
    });

    !this.#isAlbumCreationChecked && this.#loadFillResultEvent(tempSelectedCat);
  }

  /*-----------
  Ajax method
  -----------*/
  #prefill_search() {
    $(".linkedAlbumPopInContainer .searching").show();
    let api_params = {
      cat_id: 0,
      recursive: false,
      fullname: true,
      limit: this.#limitParam,
    };

    this.#in_admin_mode && (api_params.additional_output = 'full_name_with_admin_links');

    $.ajax({
      url: "ws.php?format=json&method=" + this.#methodPwg,
      type: "POST",
      dataType: "json",
      data: api_params,
      success: (data) => {
        // for debug
        // console.log(data);
        $(".linkedAlbumPopInContainer .searching").hide();
        const cats = data.result.categories;
        const limit = data.result.limit;
        this.#prefill_results("root", cats, limit);
      },
      error: function (e) {
        $(".linkedAlbumPopInContainer .searching").hide();
        console.log("error : ", e.message);
      },
    });
  }

  async #prefill_search_subcats(cat_id) {
    let api_params = {
      cat_id: cat_id,
      recursive: false,
      limit: this.#limitParam,
    };

    this.#in_admin_mode && (api_params.additional_output = 'full_name_with_admin_links');

    $.ajax({
      url: "ws.php?format=json&method=" + this.#methodPwg,
      type: "POST",
      dataType: "json",
      data: api_params,
      success: (data) => {
        const cats = data.result.categories.filter((c) => c.id != cat_id);
        const limit = data.result.limit;
        this.#prefill_results(cat_id, cats, limit);
      },
      error: (e) => {
        console.log('prefill search error :', e);
      }
    });
  }

  #perform_albums_search(searchText) {
    if (searchText == '') {
      this.#reset_search_input(true);
      return;
    }
    let api_params = {
      cat_id: 0,
      recursive: true,
      fullname: true,
      search: searchText,
    }

    this.#in_admin_mode && (api_params.additional_output = 'full_name_with_admin_links');

    AlbumSelector.selectors.iconSearchingSpin.show();
    $.ajax({
      url: "ws.php?format=json&method=" + this.#methodPwg,
      type: "POST",
      dataType: "json",
      data: api_params,
      success: (raw_data) => {
        if ('ok' !== raw_data.stat) { return }
        AlbumSelector.selectors.iconSearchingSpin.hide();
        let categories = raw_data.result.categories;
        this.#fill_results(categories);

        if (raw_data.result.limit_reached) {
          AlbumSelector.selectors.limitReached.html(str_result_limit.replace("%d", categories.length));
        } else {
          if (categories.length == 1) {
            AlbumSelector.selectors.limitReached.html(str_album_found);
          } else {
            AlbumSelector.selectors.limitReached.html(str_albums_found.replace("%d", categories.length));
          }
        }
      },
      error: (e) => {
        AlbumSelector.selectors.iconSearchingSpin.hide();
        console.log(e.message);
      }
    });
  }

  #add_new_album(cat_id) {
    if (this.#loading_add) return;
    this.#loading_add = true;
    const cat_name = AlbumSelector.selectors.linkedAlbumInput.val();
    const cat_position = $("input[name=position]:checked").val();
    const api_params = {
      name: cat_name,
      parent: cat_id === 'root' ? 0 : +cat_id,
      position: cat_position,
    }
  
    if(!cat_name || '' === cat_name) {
      this.#show_new_album_error(str_complete_name_field);
      return
    }
  
    $.ajax({
      url: 'ws.php?format=json&method=pwg.categories.add',
      type: 'POST',
      dataType: 'json',
      data: api_params,
      success: (data) => {
        if (data.stat === 'ok') {
          this.#get_album_by_id(data.result.id);
        } else {
          this.#show_new_album_error(str_an_error_has_occured);
        }
      },
      error: () => {
        this.#show_new_album_error(str_an_error_has_occured);
      }
    });
  }

  #get_album_by_id(cat_id) {
    $.ajax({
      url: 'ws.php?format=json&method=pwg.categories.getAdminList',
      dataType: 'json',
      data: {
        cat_id,
        additional_output: 'full_name_with_admin_links',
      },
      success: (data) => {
        if(data.stat === 'ok') {
          this.#select_new_album_and_close(data.result.categories[0]);
        } else {
          this.#show_new_album_error(str_an_error_has_occured);
        }
      },
      error: () => {
        this.#show_new_album_error(str_an_error_has_occured);
      }
    });
  }

}
