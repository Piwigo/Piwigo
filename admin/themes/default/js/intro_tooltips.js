$(function() {
  Object.entries(storage_details).forEach(([type, infos]) => {
    // Determine if we use MB or GB and show it correctly 
    let size = infos.total.filesize;
    let str_size_type_string = size > 1000000 ? str_gb : str_mb;
    let size_nb = size > 1000000 ? (size / 1000000).toFixed(2) : (size / 1000).toFixed(0);
    let str_size = str_size_type_string.replace("%s", size_nb);
  
    // Display head of Tooltip
    $('#storage-title-' + type).html('<b>'+translate_type[type]+'</b>');
    $('#storage-size-' + type).html('<b>'+ str_size +'</b>');
    $('#storage-files-' + type).html('<p>'+ (infos.total.nb_files ? translate_files.replace('%d', infos.total.nb_files) : "~") +'</p>');
  
    // Display body of Tooltip
    if (infos.details) {
      $.each(infos.details, function(ext, data) {
        // Determinate if we use MB or GB and show it correctly (duplicate code from total size for scaling code)
        let detail_size = data.filesize;
        let detail_str_size_type_string;
        let detail_size_nb = 0;
        if (detail_size > 1000000) {
          detail_str_size_type_string = str_gb;
          detail_size_nb = (detail_size / 1000000).toFixed(2);
        } else {
          detail_str_size_type_string = str_mb;
          detail_size_nb =  (detail_size / 1000).toFixed(0) < 1 ? (detail_size / 1000).toFixed(2) : (detail_size / 1000).toFixed(0);
        }
        let detail_str_size = detail_str_size_type_string.replace("%s", detail_size_nb);
        $('#storage-detail-' + type).append(''+
          '<span class="tooltip-details-cont">'+
            '<span class="tooltip-details-ext"><b>'+ ext +'</b></span>'+
            '<span class="tooltip-details-size"><b>'+ detail_str_size +'</b></span>'+
            '<span class="tooltip-details-files">'+ translate_files.replace('%d', data.nb_files) +'</span>'+
          '</span>'+
        '');
        let ext_bg_color = $('.storage-chart span[data-type="storage-'+type+'"]').css('background-color');
        $('#storage-'+type+' .tooltip-details-ext b').css('color', ext_bg_color);
      });
    } else {
      $('#storage-'+ type +' .separated').attr('style', 'display: none !important');
      $('#storage-' + type +' .tooltip-header').css('margin', '0');
    }
    
    // Fixing storage chart tooltip bug in little screen
    // Keep showing tooltip and his % when hovered
    $('#storage-' + type).on('mouseenter', function() {
      $(this).css('display', 'block');
      $('.storage-chart span[data-type="storage-'+ type +'"] p').css('opacity', '0.4');
    }).on('mouseleave', function() {
      $(this).css('display', 'none');
      $('.storage-chart span[data-type="storage-'+ type +'"] p').css('opacity', '0');
    });

    $('.storage-chart span[data-type="storage-'+ type +'"]').on('mouseover', function() {
      $(this).find('p').css('opacity', '0.4');
    }).on('mouseout', function() {
      $(this).find('p').css('opacity', '0');
    });
  });
  
  //Tooltip for the storage chart
  resizeStorageTooltips();
  //Tooltip for the activity chart
  resizeActivityTooltips();

  
  // Resize
  $(window).on('resize', function(){
    // resize storage tooltips
    resizeStorageTooltips(true);
    // resize activity tooltips
    resizeActivityTooltips();
  });
});

/*----------------
General function
----------------*/
function resizeStorageTooltips(resize=false) {
  $('.storage-chart span').each(function () {
    let tooltip = $('.storage-tooltips #'+$(this).data('type'));
    let arrow = $('.storage-tooltips #'+$(this).data("type")+' .tooltip-arrow');
    let left = $(this).position().left + $(this).width()/2 - tooltip.innerWidth()/2;
    // Move tooltip if he create horizontal scrollbar
    let storage_width = $('#chart-title-storage').innerWidth();
    if(left + tooltip.innerWidth() > storage_width){
        let diff = (left + tooltip.innerWidth()) - storage_width;
        left = left - diff;
        arrow.css('left', 'calc(50% + '+ diff +'px)');
    }
    tooltip.css('left', left+"px");
    // Move tooltip if he create vertical scrollbar
    let str_chart_pos = $('.storage-chart').offset().top;
    let str_chart_height = $('.storage-chart').innerHeight();
    let tooltip_height = $('.storage-tooltips #'+$(this).data("type")).innerHeight() + str_chart_height;
    let windows_height = $(window).height();

    if (resize) {
      if (str_chart_pos + tooltip_height > windows_height) {
        tooltip.css('bottom', 'calc(100% + '+ str_chart_height +'px)');
        arrow.addClass('bottom');
      } else {
        tooltip.css('bottom', '');
        arrow.removeClass('bottom');
      }
    } else {
      if (str_chart_pos + tooltip_height > windows_height) {
        tooltip.css('bottom', 'calc(100% + '+ str_chart_height +'px)');
        arrow.addClass('bottom');
      }
      $(this).off('mouseenter').on('mouseenter', function() {
        tooltip.show();
      });
      $(this).off('mouseleave').on('mouseleave', function() {
        tooltip.hide();
      });
    }
  });
}

function resizeActivityTooltips() {
  $('.activity_tooltips').has('.tooltip').each(function() {
    const max_width = $('#pwgMain').innerWidth() - 20;
    const tooltip = $(this).find('.tooltip');
    let left = $(this).position().left + ($(this).innerWidth() / 2) + (tooltip.innerWidth() / 2);
    if (left > max_width) {
      const arrow = $(this).find('.tooltip-arrow');
      const diff = max_width - left;

      tooltip.css('left', 'calc(50% + '+ diff +'px)');
      arrow.css('left', 'calc(50% - '+ diff +'px)');
    }
  });
}

