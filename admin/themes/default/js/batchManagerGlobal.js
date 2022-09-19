
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

$(".removeFilter").addClass("icon-cancel-circled");

$(".removeFilter").click(function () {
	var filter = $(this).parent('li').attr("id");
	filter_disable(filter);

	return false;
});

$("#addFilter a").on('click', function () {
	var filter = $(this).attr("data-value");
	filter_enable(filter);
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


$(document).mouseup(function (e) {
  e.stopPropagation();
  if (!$(event.target).hasClass('addFilter-button')) {
    $('.addFilter-dropdown').slideUp();
  }
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
						$(input).prop('checked', last_clickedstatus).trigger("change");
						if (last_clickedstatus)
						{
							$(input).closest("li").addClass("thumbSelected");
						}
						else
						{
							$(input).closest("li").removeClass("thumbSelected");
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

jQuery("a.preview-box").colorbox( {photo: true} );

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

function progress_start() {
  jQuery('#uploadingActions').show();
  jQuery('#uploadingActions .progress-bar').width("0%");
}

function progress_end() {
  jQuery('#uploadingActions').hide();
}

function progress(success) {

  percent = parseInt(derivatives.done / derivatives.total * 100);
  jQuery('#uploadingActions .progressbar').width(percent.toString()+'%');
	if (success !== undefined) {
		var type = success ? 'regenerateSuccess': 'regenerateError',
			s = jQuery('[name="'+type+'"]').val();
		jQuery('[name="'+type+'"]').val(++s);
	}

	if (derivatives.finished()) {
    progress_end();
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
  jQuery('#applyActionBlock').hide();
  jQuery('.permitActionListButton').hide();
  jQuery('#confirmDel').hide();
  jQuery('#regenerationMsg').show();
  jQuery('#regenerationText').html(lang.generateMsg);
  progress_start();
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
      jQuery('#regenerationStatus .badge-number').html(derivatives.done.toString() + "/" + derivatives.total.toString());
			progress();
			for (var i=0; i < data.result.urls.length; i++) {
				jQuery.manageAjax.add("queued", {
					type: 'GET',
					url: data.result.urls[i] + "&ajaxload=true",
					dataType: 'json',
					success: ( function(data) {
            derivatives.done++;
            jQuery('#regenerationStatus .badge-number').html(derivatives.done.toString() + "/" + derivatives.total.toString());
            progress(true)
          }),
					error: ( function(data) {
            derivatives.done++;
            jQuery('#regenerationStatus .badge-number').html(derivatives.done.toString() + "/" + derivatives.total.toString());
            progress(false)
          })
				});
			}
			if (derivatives.elements.length)
				setTimeout( getDerivativeUrls, 25 * (derivatives.total-derivatives.done));
		}
	} );
}

function selectGenerateDerivAll() {
	$("#action_generate_derivatives input[type=checkbox]").prop("checked", true).trigger("change");
}
function selectGenerateDerivNone() {
	$("#action_generate_derivatives input[type=checkbox]").prop("checked", false).trigger("change");
}

function selectDelDerivAll() {
	$('#action_delete_derivatives input[name="del_derivatives_type[]"]').prop("checked", true).trigger("change");
}
function selectDelDerivNone() {
	$('#action_delete_derivatives input[name="del_derivatives_type[]"]').prop("checked", false).trigger("change");
}

/* sync metadatas or delete photos by blocks, with progress bar */
jQuery('#applyAction').click(function(e) {
  if (typeof(elements) != "undefined") {
    return true;
  }

  if (jQuery('[name="selectAction"]').val() == 'metadata') {
    e.stopPropagation();
    jQuery('.bulkAction').hide();
    jQuery('#regenerationText').html(lang.syncProgressMessage);
    elements = Array();

    if (jQuery('input[name=setSelected]').is(':checked')) {
      elements = all_elements;
    }
    else {
      jQuery('input[name="selection[]"]').filter(':checked').each(function() {
        elements.push(jQuery(this).val());
      });
    }

    progressBar_max = elements.length;
    var todo = 0;
    var syncBlockSize = Math.min(
      Number((elements.length/2).toFixed()),
      1000
    );
    var image_ids = Array();

    jQuery('#applyActionBlock').hide();
    jQuery('.permitActionListButton').hide();
    jQuery('#confirmDel').hide();
    jQuery('#regenerationMsg').show();
    progress_bar_start();
    for (i=0;i<elements.length;i++) {
      image_ids.push(elements[i]);
      if (i % syncBlockSize != syncBlockSize - 1 && i != elements.length - 1) {
        continue;
      }

      (function(ids) {
        var thisBatchSize = ids.length;
        jQuery.ajax({
          url: "ws.php?format=json&method=pwg.images.syncMetadata",
          type:"POST",
          dataType: "json",
          data: {
            pwg_token: jQuery("input[name=pwg_token]").val(),
            image_id: ids
          },
          success: function(data) {
            todo += thisBatchSize;
            var isOk = data.stat && "ok" == data.stat;
            if (isOk && data.result.nb_synchronized != thisBatchSize)
            /*TODO: user feedback only data.nb_synchronized images out of thisBatchSize were sync*/;
            /*TODO: user feedback if isError*/
            jQuery('#regenerationStatus .badge-number').html(todo.toString() + "/" + progressBar_max.toString());
            progress_bar(todo, progressBar_max, false);
          },
          error: function(data) {
            todo += thisBatchSize;
            /*TODO: user feedback*/
            jQuery('#regenerationStatus .badge-number').html(todo.toString() + "/" + progressBar_max.toString());
            progress_bar(todo, progressBar_max, false);
          }
        });
      } )(image_ids);
      image_ids = Array();
    }
  }

  if (jQuery('[name="selectAction"]').val() == 'delete') {
    if (!jQuery("#confirmDel input[name=confirm_deletion]").is(':checked')) {
      jQuery("#confirmDel span.errors").css("visibility", "visible");
      return false;
    }
    e.stopPropagation();
  }
  else {
    return true;
  }

  jQuery('.bulkAction').hide();
  var maxRequests=1;

  var queuedManager = jQuery.manageAjax.create('queued', {
    queue: true,
    cacheResponse: false,
    maxRequests: maxRequests
  });

  elements = Array();

  if (jQuery('input[name=setSelected]').is(':checked')) {
    elements = all_elements;
  }
  else {
    jQuery('input[name="selection[]"]').filter(':checked').each(function() {
      elements.push(jQuery(this).val());
    });
  }

  progressBar_max = elements.length;
  var todo = 0;
  var deleteBlockSize = Math.min(
    Number((elements.length/2).toFixed()),
    1000
  );
  var image_ids = Array();

  jQuery('#applyActionBlock').hide();
  jQuery('.permitActionListButton').hide();
  jQuery('#confirmDel').hide();
  jQuery('#regenerationText').html(lang.deleteProgressMessage);
  jQuery('#regenerationMsg').show();
  progress_bar_start();
  for (i=0;i<elements.length;i++) {
    image_ids.push(elements[i]);
    if (i % deleteBlockSize != deleteBlockSize - 1 && i != elements.length - 1) {
      continue;
    }

    (function(ids) {
      var thisBatchSize = ids.length;
      queuedManager.add({
        type: 'POST',
        url: 'ws.php?format=json',
        data: {
          method: "pwg.images.delete",
          pwg_token: jQuery("input[name=pwg_token]").val(),
          image_id: ids.join(',')
        },
        dataType: 'json',
        success: function(data) {
          todo += thisBatchSize;
          var isOk = data.stat && "ok" == data.stat;
          if (isOk && data.result != thisBatchSize);
            /*TODO: user feedback only data.result images out of thisBatchSize were deleted*/;
          /*TODO: user feedback if isError*/
          jQuery('#regenerationStatus .badge-number').html(todo.toString() + "/" + progressBar_max.toString());
          progress_bar(todo, progressBar_max, false);
        },
        error: function(data) {
          todo += thisBatchSize;
          /*TODO: user feedback*/
          jQuery('#regenerationStatus .badge-number').html(todo.toString() + "/" + progressBar_max.toString());
          progress_bar(todo, progressBar_max, false);
        }
      });
    } )(image_ids);

    image_ids = Array();
  }

  /* tell PHP how many photos were deleted */
  jQuery('form').append('<input type="hidden" name="nb_photos_deleted" value="'+elements.length+'">');

  return false;
});

function progress_bar_start() {
  jQuery('#uploadingActions').show();
  jQuery('#uploadingActions .progress-bar').width("0%");
}

function progress_bar_end() {
  jQuery('#uploadingActions').hide();
}

function progress_bar(val, max, success) {
  percent = parseInt(val / max * 100);
  jQuery('#uploadingActions .progressbar').width(percent.toString()+'%');
  if (val == max)
    jQuery('#applyAction').click();
}

jQuery("#confirmDel input[name=confirm_deletion]").change(function() {
  jQuery("#confirmDel span.errors").css("visibility", "hidden");
});

jQuery('#sync_md5sum').click(function(e) {
  jQuery(this).hide();
  jQuery('#add_md5sum').show();

  var addBlockSize = Math.min(
    Number((jQuery('#md5sum_to_add').data('origin') / 2).toFixed()),
    1000
  );
  add_md5sum_block(addBlockSize);

  return false;
});

function add_md5sum_block(blockSize){
  jQuery.ajax({
    url: "ws.php?format=json&method=pwg.images.setMd5sum",
    type:"POST",
    dataType: "json",
    data: {
      pwg_token: jQuery("input[name=pwg_token]").val(),
      block_size: blockSize
    },
    success:function(data) {
      jQuery('#md5sum_to_add').html(data.result.nb_no_md5sum);

      var percent_remaining = Number(
        (data.result.nb_no_md5sum * 100 / jQuery('#md5sum_to_add').data('origin')).toFixed()
      );
      var percent_done = 100 - percent_remaining;
      jQuery('#md5sum_added').html(percent_done);
      if (data.result.nb_no_md5sum > 0) {
        add_md5sum_block();
      }
      else {
        // time to refresh the whole page
        var redirect_to = 'admin.php?page=batch_manager';
        redirect_to += '&action=sync_md5sum';
        redirect_to += '&nb_md5sum_added='+jQuery('#md5sum_to_add').data('origin');

        document.location = redirect_to;
      }
    },
    error:function(XMLHttpRequest) {
      jQuery('#add_md5sum').hide();
      jQuery('#add_md5sum_error').show().html('error '+XMLHttpRequest.status+' : '+XMLHttpRequest.statusText);
    }
  });
}

jQuery('#delete_orphans').click(function(e) {
  jQuery(this).hide();
  jQuery('#orphans_deletion').show();

  var deleteBlockSize = Math.min(
    Number((jQuery('#orphans_to_delete').data('origin') / 2).toFixed()),
    1000
  );

  delete_orphans_block(deleteBlockSize);

  return false;
});

function delete_orphans_block(blockSize) {
  jQuery.ajax({
    url: "ws.php?format=json&method=pwg.images.deleteOrphans",
    type:"POST",
    dataType: "json",
    data: {
      pwg_token: jQuery("input[name=pwg_token]").val(),
      block_size: blockSize
    },
    success:function(data) {
      jQuery('#orphans_to_delete').html(data.result.nb_orphans);

      var percent_remaining = Number(
        (data.result.nb_orphans * 100 / jQuery('#orphans_to_delete').data('origin')).toFixed()
      );
      var percent_done = 100 - percent_remaining;
      jQuery('#orphans_deleted').html(percent_done);

      if (data.result.nb_orphans > 0) {
        delete_orphans_block();
      }
      else {
        // time to refresh the whole page
        var redirect_to = 'admin.php?page=batch_manager';
        redirect_to += '&action=delete_orphans';
        redirect_to += '&nb_orphans_deleted='+jQuery('#orphans_to_delete').data('origin');

        document.location = redirect_to;
      }
    },
    error:function(XMLHttpRequest) {
      jQuery('#orphans_deletion').hide();
      jQuery('#orphans_deletion_error').show().html('error '+XMLHttpRequest.status+' : '+XMLHttpRequest.statusText);
    }
  });
}
