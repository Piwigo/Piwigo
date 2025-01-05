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
  $('#filterCategoryValue').val(+getSelectedAlbum()[0]);
  $('#selectAlbumFilter').hide();
  $('#selectedAlbumFilterArea').fadeIn();
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
})

