/* ********** Filters*/
function filter_enable(filter) {
	/* show the filter*/
	$("#"+filter).show();

	/* check the checkbox to declare we use this filter */
	$("input[type=checkbox][name="+filter+"_use]").prop("checked", true);

	/* forbid to select this filter in the addFilter list */
  $("#addFilter").find("a[data-value="+filter+"]").addClass("disabled", "disabled");
  
  /* hide the no filter message */
  $('.noFilter').hide();
  $('.addFilter-button').removeClass('highlight');
}

function filter_disable(filter) {
	/* hide the filter line */
	$("#"+filter).hide();

	/* uncheck the checkbox to declare we do not use this filter */
	$("input[name="+filter+"_use]").prop("checked", false);

	/* give the possibility to show it again */
  $("#addFilter").find("a[data-value="+filter+"]").removeClass("disabled");
  
  /* show the no filter message if no filter selected */
  if ($('#filterList li:visible').length == 0) {
    $('.noFilter').show();
    $('.addFilter-button').addClass('highlight');
  }
  
}
// Album Selector
function select_album_filter({ album, newSelectedAlbum, getSelectedAlbum }) {
  $('#selectedAlbumNameFilter').html(album.name);
  newSelectedAlbum();
  hide_filters_error(str_select_album);
  $('#filterCategoryValue').val(+getSelectedAlbum()[0]);
  $('#selectAlbumFilter').hide();
  $('#selectedAlbumFilterArea').fadeIn();
}

// Tags and Albums validation
function show_filters_error(message) {
  errorFilters = message;
  $('#errorFilter').html(`<p style="margin: 0 10px;">${message}</p>`).fadeIn();
}

function hide_filters_error(message) {
  if (message === errorFilters) {
    $('#errorFilter').hide();
  }
}

$(document).ready(function () {
  const ab_filter = new AlbumSelector({
    selectedCategoriesIds: selected_filter_cat_ids,
    selectAlbum: select_album_filter,
    adminMode: true,
  });

  $('#selectAlbumFilter, #selectedAlbumEditFilter').on('click', function() {
    ab_filter.open();
  });

  $(".removeFilter").addClass("icon-cancel-circled");
  
  $(".removeFilter").click(function () {
    const filter = $(this).parent('li').attr("id");
    filter_disable(filter);
  
    return false;
  });
  
  $("#addFilter a").on('click', function () {
    const filter = $(this).attr("data-value");
    filter_enable(filter);
  });
  
  $("#removeFilters").click(function() {
    $("#filterList li").each(function() {
      const filter = $(this).attr("id");
      filter_disable(filter);
    });
    return false;
  });
  
  $('[data-slider=widths]').pwgDoubleSlider(sliders.widths);
  $('[data-slider=heights]').pwgDoubleSlider(sliders.heights);
  $('[data-slider=ratios]').pwgDoubleSlider(sliders.ratios);
  $('[data-slider=filesizes]').pwgDoubleSlider(sliders.filesizes);
  
  $(document).mouseup(function (e) {
    e.stopPropagation();
    if (!$(event.target).hasClass('addFilter-button')) {
      $('.addFilter-dropdown').slideUp();
    }
  });

  // Filetype filter
  $(document).on('click', function(e) {
    if (!$(e.target).closest('#filter_filetype .custom-select-dropdown').length) {
      $('#filter_filetype .custom-select-dropdown').removeAttr('open');
    }
  });

  function updateFiletypeSelectedText() {
    const checked = $('#filter_filetype input[name="filter_filetype[]"]:checked');
    const label = $('#filter_filetype .selected-text');
    const defaultPlaceholder = label.data('placeholder');

    if (checked.length === 0) {
      label.text(defaultPlaceholder);
    } else {
      const selectedTypes = checked.map(function() {
        return $(this).closest('label').text().trim().split(' ')[0];
      }).get();

      label.text(selectedTypes.join(', '));
    }
  }

  $('#filter_filetype').on('change', 'input[name="filter_filetype[]"]', function() {
    updateFiletypeSelectedText();
  });
  
  updateFiletypeSelectedText();

  // Date posted filter
  $('#date_posted_min_date, #date_posted_max_date').on('click', function() {
    if (typeof this.showPicker === 'function') {
      this.showPicker();
    }
  });

  $('#filter_date_posted').on('click', '.slider-choice', function(e) {
    e.preventDefault();

    const $choice = $(this);
    const $parent = $choice.closest('li');
    const isSingle = $choice.data('value') == 1;

    $parent.find('.slider-choice').removeClass('active');
    $choice.addClass('active');

    $parent.find('#filter_date_posted_single').val(isSingle ? '1' : '0');

    if (isSingle) {
      $parent.find('.date-label-between-posted, .date-label-and-posted, #date_posted_max_date').hide();
      $parent.find('.date-label-single-posted').show();
      $parent.find('#date_posted_max_date').val('');
    } else {
      $parent.find('.date-label-single-posted').hide();
      $parent.find('.date-label-between-posted, .date-label-and-posted, #date_posted_max_date').show();
    }
  });

  // Date created filter
  $('#date_created_min_date, #date_created_max_date').on('click', function() {
    if (typeof this.showPicker === 'function') {
      this.showPicker();
    }
  });

  $('#filter_date_created').on('click', '.slider-choice', function(e) {
    e.preventDefault();

    const $choice = $(this);
    const $parent = $choice.closest('li');
    const isSingle = $choice.data('value') == 1;

    $parent.find('.slider-choice').removeClass('active');
    $choice.addClass('active');

    $parent.find('#filter_date_created_single').val(isSingle ? '1' : '0');

    if (isSingle) {
      $parent.find('.date-label-between-created, .date-label-and-created, #date_created_max_date').hide();
      $parent.find('.date-label-single-created').show();
      $parent.find('#date_created_max_date').val('');
    } else {
      $parent.find('.date-label-single-created').hide();
      $parent.find('.date-label-between-created, .date-label-and-created, #date_created_max_date').show();
    }
  });
  
  // Filter JS Validation
  $('.filterBlock select[data-selectize="tags"]').on('change', function() {
    if ($(this).val()) {
      hide_filters_error(str_select_tag);
    }
  })

  $('#date_posted_min_date, #date_posted_max_date').on('change input', function() {
    if ($('#date_posted_min_date').val() || $('#date_posted_max_date').val()) {
      hide_filters_error(str_select_date);
    }
  });

  $('#date_created_min_date, #date_created_max_date').on('change input', function() {
    if ($('#date_created_min_date').val() || $('#date_created_max_date').val()) {
      hide_filters_error(str_select_date);
    }
  });
  
  $('#filter_filetype').on('change', 'input[name="filter_filetype[]"]', function() {
    if ($('#filter_filetype input[name="filter_filetype[]"]:checked').length > 0) {
      hide_filters_error(str_select_filetype);
    }
  });

  $('#applyFilter').on('click', function(e) {
    if ($('#filter_tags').is(':visible')) {
      const tags = $('.filterBlock select[data-selectize="tags"]');
      if (!tags.val()) {
        e.preventDefault();
        show_filters_error(str_select_tag);
        $('#filter_tags .removeFilter').off('click.apply').on('click.apply', function() {
          hide_filters_error(str_select_tag);
        });
      }
    }

    if ($('#filter_category').is(':visible')) {
      const albums = ab_filter.get_selected_albums();
      if (albums.length === 0) {
        e.preventDefault();
        show_filters_error(str_select_album);
        $('#filter_category .removeFilter').off('click.apply').on('click.apply', function() {
          hide_filters_error(str_select_album);
        });
      }
    }
    
    if ($('#filter_filetype').is(':visible')) {
      const checkedFiletypes = $('#filter_filetype input[name="filter_filetype[]"]:checked');
      const isValid = checkedFiletypes.length > 0;

      if (!isValid) {
        e.preventDefault();
        show_filters_error(str_select_filetype);
        $('#filter_filetype .removeFilter').off('click.apply').on('click.apply', function() {
          hide_filters_error(str_select_filetype);
        });
      }
    }
    
    if ($('#filter_date_posted').is(':visible')) {
      const minDate = $('#date_posted_min_date').val();
      const maxDate = $('#date_posted_max_date').val();
      const isSingle = $('#filter_date_posted_single').val() === '1';

      const isValid = isSingle ? !!minDate : (!!minDate || !!maxDate);

      if (!isValid) {
        e.preventDefault();
        show_filters_error(str_select_date);
        $('#filter_date_posted .removeFilter').off('click.apply').on('click.apply', function() {
          hide_filters_error(str_select_date);
        });
      }
    }

    if ($('#filter_date_created').is(':visible')) {
      const minDate = $('#date_created_min_date').val();
      const maxDate = $('#date_created_max_date').val();
      const isSingle = $('#filter_date_created_single').val() === '1';

      const isValid = isSingle ? !!minDate : (!!minDate || !!maxDate);

      if (!isValid) {
        e.preventDefault();
        show_filters_error(str_select_date);
        $('#filter_date_created .removeFilter').off('click.apply').on('click.apply', function() {
          hide_filters_error(str_select_date);
        });
      }
    }
  });

  $('.help-popin-search').on('click', function() {
    $('#modalQuickSearch').fadeIn();
  });

  $('#closeModalQuickSearch').on('click', function() {
    $('#modalQuickSearch').fadeOut();
  });
})

