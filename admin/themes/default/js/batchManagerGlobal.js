
/* ********** Filters*/
function filter_enable(filter) {
	/* show the filter*/
	$("#"+filter).show();

	/* check the checkbox to declare we use this filter */
	$("input[type=checkbox][name="+filter+"_use]").prop("checked", true);

	/* forbid to select this filter in the addFilter list */
	$("#addFilter").children("option[value="+filter+"]").attr("disabled", "disabled");
}

function filter_disable(filter) {
	/* hide the filter line */
	$("#"+filter).hide();

	/* uncheck the checkbox to declare we do not use this filter */
	$("input[name="+filter+"_use]").prop("checked", false);

	/* give the possibility to show it again */
	$("#addFilter").children("option[value="+filter+"]").removeAttr("disabled");
}

$(".removeFilter").click(function () {
	var filter = $(this).parent('li').attr("id");
	filter_disable(filter);

	return false;
});

$("#addFilter").change(function () {
	var filter = $(this).prop("value");
	filter_enable(filter);
	$(this).prop("value", -1);
});

$("#removeFilters").click(function() {
	$("#filterList li").each(function() {
		var filter = $(this).attr("id");
		filter_disable(filter);
	});
	return false;
});

$('[data-slider=widths]').pwgDoubleSlider(sliders.widths);
$('[data-slider=heights]').pwgDoubleSlider(sliders.heights);
$('[data-slider=ratios]').pwgDoubleSlider(sliders.ratios);
$('[data-slider=filesizes]').pwgDoubleSlider(sliders.filesizes);


/* ********** Thumbs */

/* Shift-click: select all photos between the click and the shift+click */
jQuery(document).ready(function() {
	var last_clicked=0,
		last_clickedstatus=true;
	jQuery.fn.enableShiftClick = function() {
		var inputs = [],
			count=0;
		this.find('input[type=checkbox]').each(function() {
			var pos=count;
			inputs[count++]=this;
			$(this).bind("shclick", function (dummy,event) {
				if (event.shiftKey) {
					var first = last_clicked;
					var last = pos;
					if (first > last) {
						first=pos;
						last=last_clicked;
					}

					for (var i=first; i<=last;i++) {
						input = $(inputs[i]);
						$(input).prop('checked', last_clickedstatus);
						if (last_clickedstatus)
						{
							$(input).siblings("span.wrap2").addClass("thumbSelected");
						}
						else
						{
							$(input).siblings("span.wrap2").removeClass("thumbSelected");
						}
					}
				}
				else {
					last_clicked = pos;
					last_clickedstatus = this.checked;
				}
				return true;
			});
			$(this).click(function(event) { $(this).triggerHandler("shclick",event)});
		});
	}
	$('ul.thumbnails').enableShiftClick();
});

jQuery("a.preview-box").colorbox();

jQuery('.thumbnails img').tipTip({
	'delay' : 0,
	'fadeIn' : 200,
	'fadeOut' : 200
});


/* ********** Actions*/

jQuery('[data-datepicker]').pwgDatepicker({
	showTimepicker: true,
	cancelButton: lang.Cancel
});

jQuery('[data-add-album]').pwgAddAlbum();

$("input[name=remove_author]").click(function () {
	if ($(this).is(':checked')) {
		$("input[name=author]").hide();
	}
	else {
		$("input[name=author]").show();
	}
});

$("input[name=remove_title]").click(function () {
	if ($(this).is(':checked')) {
		$("input[name=title]").hide();
	}
	else {
		$("input[name=title]").show();
	}
});

$("input[name=remove_date_creation]").click(function () {
	if ($(this).is(':checked')) {
		$("#set_date_creation").hide();
	}
	else {
		$("#set_date_creation").show();
	}
});

var derivatives = {
	elements: null,
	done: 0,
	total: 0,

	finished: function() {
		return derivatives.done == derivatives.total && derivatives.elements && derivatives.elements.length==0;
	}
};

function progress(success) {
  jQuery('#progressBar').progressBar(derivatives.done, {
    max: derivatives.total,
    textFormat: 'fraction',
    boxImage: 'themes/default/images/progressbar.gif',
    barImage: 'themes/default/images/progressbg_orange.gif'
  });
	if (success !== undefined) {
		var type = success ? 'regenerateSuccess': 'regenerateError',
			s = jQuery('[name="'+type+'"]').val();
		jQuery('[name="'+type+'"]').val(++s);
	}

	if (derivatives.finished()) {
		jQuery('#applyAction').click();
	}
}

function getDerivativeUrls() {
	var ids = derivatives.elements.splice(0, 500);
	var params = {max_urls: 100000, ids: ids, types: []};
	jQuery("#action_generate_derivatives input").each( function(i, t) {
		if ($(t).is(":checked"))
			params.types.push( t.value );
	} );

	jQuery.ajax( {
		type: "POST",
		url: 'ws.php?format=json&method=pwg.getMissingDerivatives',
		data: params,
		dataType: "json",
		success: function(data) {
			if (!data.stat || data.stat != "ok") {
				return;
			}
			derivatives.total += data.result.urls.length;
			progress();
			for (var i=0; i < data.result.urls.length; i++) {
				jQuery.manageAjax.add("queued", {
					type: 'GET',
					url: data.result.urls[i] + "&ajaxload=true",
					dataType: 'json',
					success: ( function(data) { derivatives.done++; progress(true) }),
					error: ( function(data) { derivatives.done++; progress(false) })
				});
			}
			if (derivatives.elements.length)
				setTimeout( getDerivativeUrls, 25 * (derivatives.total-derivatives.done));
		}
	} );
}

function selectGenerateDerivAll() {
	$("#action_generate_derivatives input[type=checkbox]").prop("checked", true);
}
function selectGenerateDerivNone() {
	$("#action_generate_derivatives input[type=checkbox]").prop("checked", false);
}

function selectDelDerivAll() {
	$("#action_delete_derivatives input[type=checkbox]").prop("checked", true);
}
function selectDelDerivNone() {
	$("#action_delete_derivatives input[type=checkbox]").prop("checked", false);
}
