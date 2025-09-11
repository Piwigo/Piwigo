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
  $('#errorFilter').html(`<p>${message}</p>`).fadeIn();
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


  // Filter JS Validation
  $('.filterBlock select[data-selectize="tags"]').on('change', function() {
    if ($(this).val()) {
      hide_filters_error(str_select_tag);
    }
  })

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
  });

  $('.help-popin-search').on('click', function() {
    $('#modalQuickSearch').fadeIn();
  });

  $('#closeModalQuickSearch').on('click', function() {
    $('#modalQuickSearch').fadeOut();
  });
})

