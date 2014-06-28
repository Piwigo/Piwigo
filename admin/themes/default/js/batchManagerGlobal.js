
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



/* ********** Actions*/

jQuery('[data-datepicker]').pwgDatepicker({
	showTimepicker: true,
	cancelButton: lang.Cancel
});

jQuery('[data-add-album]').pwgAddAlbum({ cache: categoriesCache });

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
