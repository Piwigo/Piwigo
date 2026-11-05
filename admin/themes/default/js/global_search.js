class GlobalSearch {
  #search_timeout;
  #request;
  #query;
  #type;
  #groups;
  #page;
  #total;
  #items;
  #active_index;

  static per_page = 20;
  static overview = 5;

  static selectors = {
    modal: $('#global_search_modal'),
    close: $('#global_search_close'),
    input: $('#global_search_input'),
    clear: $('#global_search_clear'),
    tabs: $('#global_search_tabs'),
    results: $('#global_search_results'),
    status: $('#global_search_status'),
    list: $('#global_search_list'),
    pending: $('#global_search_pending'),
    trigger: $('#global_search_trigger'),
  };

  static icons = {
    albums: 'icon-folder-open',
    photos: 'icon-picture',
    users: 'icon-user',
    people: 'icon-user-1',
    plugins: 'icon-puzzle',
    pages: 'icon-link',
  };

  constructor() {
    this.#query = '';
    this.#type = 'all';
    this.#groups = null;
    this.#page = 0;
    this.#total = 0;
    this.#items = [];
    this.#active_index = -1;
    this.#load_events();
  }

  /*-----------
  Public method
  -----------*/
  open() {
    const s = GlobalSearch.selectors;

    if (s.modal.is(':visible')) {
      s.input.trigger('focus').trigger('select');
      return;
    }

    s.modal.fadeIn(100, () => {
      s.input.trigger('focus').trigger('select');
    });
  }

  close() {
    clearTimeout(this.#search_timeout);
    this.#abort();
    GlobalSearch.selectors.modal.fadeOut(100);
  }

  /*----------
  Event method
  ----------*/
  #load_events() {
    const s = GlobalSearch.selectors;

    s.trigger.on('click', (e) => {
      e.preventDefault();
      this.open();
    });

    s.close.on('click', () => {
      this.close();
    });

    s.modal.on('click', (e) => {
      if (e.target === s.modal[0]) {
        this.close();
      }
    });

    $(document).on('keydown', (e) => {
      if ('k' === e.key.toLowerCase() && (e.metaKey || e.ctrlKey) && !e.altKey && !e.shiftKey) {
        e.preventDefault();
        this.open();
        return;
      }

      if (!s.modal.is(':visible')) {
        return;
      }

      switch (e.key) {
        case 'Escape':
          e.preventDefault();
          this.close();
          break;
        case 'ArrowDown':
          e.preventDefault();
          this.#move_active(1);
          break;
        case 'ArrowUp':
          e.preventDefault();
          this.#move_active(-1);
          break;
        case 'Tab':
          e.preventDefault();
          this.#move_tab(e.shiftKey ? -1 : 1);
          break;
        case 'Enter':
          e.preventDefault();
          this.#go_to_active();
          break;
      }
    });

    s.input.on('input', () => {
      s.clear.toggle('' !== s.input.val());
      clearTimeout(this.#search_timeout);
      this.#search_timeout = setTimeout(() => {
        this.#search(s.input.val().trim());
      }, 250);
    });

    s.clear.on('click', () => {
      clearTimeout(this.#search_timeout);
      s.input.val('').trigger('focus');
      s.clear.hide();
      this.#search('');
    });

    s.tabs.on('click', '.gsearch-tab', (e) => {
      this.#select_tab($(e.currentTarget).data('type'));
    });

    s.list.on('click', '.gsearch-group-label', (e) => {
      this.#select_tab($(e.currentTarget).data('type'));
    });

    s.list.on('mouseenter', '.gsearch-item', (e) => {
      this.#set_active($(e.currentTarget).data('index'));
    });

    s.results.on('scroll', () => {
      if ('all' === this.#type || this.#request || this.#items.length >= this.#total) {
        return;
      }

      const el = s.results[0];
      if (el.scrollTop + el.clientHeight >= el.scrollHeight - 80) {
        this.#page++;
        this.#load_more();
      }
    });
  }

  /*--------------
  Dom modification
  --------------*/
  #select_tab(type) {
    const s = GlobalSearch.selectors;

    if (type === this.#type) {
      return;
    }

    this.#type = type;
    s.tabs.find('.gsearch-tab').removeClass('active').filter('[data-type="' + type + '"]').addClass('active');
    s.input.trigger('focus');

    if (this.#groups) {
      this.#abort();
      this.#render_current();
    }
  }

  #move_tab(delta) {
    const tabs = GlobalSearch.selectors.tabs.find('.gsearch-tab');
    const current = tabs.index(tabs.filter('.active'));
    const next = (current + delta + tabs.length) % tabs.length;
    this.#select_tab(tabs.eq(next).data('type'));
  }

  #set_tab_counts(groups) {
    const s = GlobalSearch.selectors;

    $.each(groups, (i, group) => {
      let tab = s.tabs.find('.gsearch-tab[data-type="' + group.id + '"]');

      if (0 === tab.length) {
        tab = $('<a>', { class: 'gsearch-tab', 'data-type': group.id, text: group.label });
        $('<span>', { class: 'gsearch-tab-count' }).appendTo(tab);
        tab.appendTo(s.tabs);
      }

      tab.find('.gsearch-tab-count').text(Number(group.total) > 0 ? Number(group.total) : '');
    });
  }

  #reset_list() {
    GlobalSearch.selectors.list.empty();
    this.#items = [];
    this.#active_index = -1;
  }

  #render_item(group_id, item) {
    const index = this.#items.length;
    this.#items.push(item);

    const item_el = $('<a>', { class: 'gsearch-item', href: item.url, 'data-index': index });

    if (item.thumb && item.face) {
      $('<span>', { class: 'gsearch-item-thumb gsearch-item-face' }).css({
        'background-image': 'url("' + item.thumb + '")',
        'background-size': item.face.size + '% auto',
        'background-position': item.face.x + '% ' + item.face.y + '%',
      }).appendTo(item_el);
    } else if (item.thumb) {
      $('<img>', { class: 'gsearch-item-thumb', src: item.thumb, alt: '' }).appendTo(item_el);
    } else {
      $('<span>', { class: 'gsearch-item-icon ' + (GlobalSearch.icons[group_id] ?? 'icon-search') }).appendTo(item_el);
    }

    const text_el = $('<div>', { class: 'gsearch-item-text' }).appendTo(item_el);
    $('<span>', { class: 'gsearch-item-label', text: item.label }).appendTo(text_el);
    if (item.sub && item.sub !== item.label) {
      $('<span>', { class: 'gsearch-item-sub', text: item.sub }).appendTo(text_el);
    }

    if (item.status) {
      $('<span>', { class: 'gsearch-item-badge', text: item.status }).appendTo(item_el);
    }

    return item_el;
  }

  #render_current() {
    if ('all' === this.#type) {
      this.#render_groups();
      return;
    }

    const group = this.#groups.find((g) => g.id === this.#type);
    this.#page = 0;
    this.#total = group ? Number(group.total) : 0;
    this.#reset_list();

    if (group) {
      this.#render_items(group.id, group.items);
    }

    this.#after_render();
  }

  #render_groups() {
    const s = GlobalSearch.selectors;
    this.#reset_list();

    $.each(this.#groups, (i, group) => {
      if (0 === group.items.length) {
        return;
      }

      const group_el = $('<div>', { class: 'gsearch-group' });
      const label_el = $('<a>', { class: 'gsearch-group-label', 'data-type': group.id, text: group.label }).appendTo(group_el);
      $('<span>', { class: 'gsearch-tab-count', text: Number(group.total) }).appendTo(label_el);
      $('<i>', { class: 'icon-left-open gsearch-group-more' }).appendTo(label_el);

      $.each(group.items.slice(0, GlobalSearch.overview), (j, item) => {
        this.#render_item(group.id, item).appendTo(group_el);
      });

      group_el.appendTo(s.list);
    });

    this.#after_render();
  }

  #render_items(group_id, items) {
    const s = GlobalSearch.selectors;

    $.each(items, (j, item) => {
      this.#render_item(group_id, item).appendTo(s.list);
    });
  }

  #after_render(append = false) {
    const s = GlobalSearch.selectors;

    if (0 === this.#items.length) {
      s.status.text(str_global_search_no_result).show();
    } else {
      s.status.hide();
      if (!append) {
        s.results.scrollTop(0);
        this.#set_active(0);
      }
    }
  }

  #set_active(index) {
    const s = GlobalSearch.selectors;
    this.#active_index = index;
    s.list.find('.gsearch-item').removeClass('active');

    const active = s.list.find('.gsearch-item[data-index="' + index + '"]');
    if (!active.length) {
      return;
    }

    active.addClass('active');
    active[0].scrollIntoView({ block: 'nearest' });
  }

  #move_active(delta) {
    if (0 === this.#items.length) {
      return;
    }

    const count = this.#items.length;
    this.#set_active((this.#active_index + delta + count) % count);
  }

  #go_to_active() {
    const item = this.#items[this.#active_index];
    if (item) {
      window.location.href = item.url;
    }
  }

  /*-----------
  Ajax method
  -----------*/
  #abort() {
    if (this.#request) {
      this.#request.abort();
      this.#request = null;
    }
    GlobalSearch.selectors.pending.hide();
  }

  #search(query) {
    const s = GlobalSearch.selectors;

    if (query === this.#query) {
      return;
    }
    this.#query = query;
    this.#groups = null;
    this.#abort();

    if (query.length < 2) {
      this.#reset_list();
      this.#total = 0;
      s.tabs.find('.gsearch-tab-count').text('');
      s.status.text(str_global_search_no_search).show();
      return;
    }

    this.#call({ query: query, limit: GlobalSearch.per_page }, (result) => {
      this.#groups = result.groups;
      this.#set_tab_counts(result.groups);
      this.#render_current();
    });
  }

  #load_more() {
    const data = {
      query: this.#query,
      type: this.#type,
      page: this.#page,
      per_page: GlobalSearch.per_page,
    };

    this.#call(data, (result) => {
      const items = result.groups[0].items;
      this.#render_items(result.groups[0].id, items);
      this.#total = items.length < GlobalSearch.per_page ? this.#items.length : Number(result.paging.total);
      this.#after_render(true);
    });
  }

  #call(data, on_success) {
    const s = GlobalSearch.selectors;
    this.#abort();
    s.pending.show();

    this.#request = $.ajax({
      url: 'ws.php?format=json&method=pwg.admin.globalSearch',
      type: 'GET',
      dataType: 'json',
      data: data,
      success: (res) => {
        s.pending.hide();
        this.#request = null;

        if ('ok' !== res.stat) {
          s.status.text(res.message ?? str_global_search_error).show();
          return;
        }

        on_success(res.result);
      },
      error: (xhr, status) => {
        s.pending.hide();
        this.#request = null;

        if ('abort' !== status) {
          s.status.text(str_global_search_error).show();
        }
      },
    });
  }
}

$(function () {
  if (0 === $('#global_search_modal').length) {
    return;
  }

  const is_mac = /Mac|iPhone|iPad/.test(navigator.platform);
  $('#global_search_trigger .gsearch-head-kbd').text(is_mac ? '⌘K' : 'Ctrl K');

  new GlobalSearch();
});
