
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

jQuery('[data-datepicker]').pwgDatepicker({
	showTimepicker: true,
	cancelButton: lang.Cancel
});

jQuery("a.preview-box").colorbox();
