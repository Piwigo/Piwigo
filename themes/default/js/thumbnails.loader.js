var thumbnails_queue = jQuery.manageAjax.create('queued', {
  queue: true,  
  cacheResponse: false,
  maxRequests: 3,
  preventDoubleRequests: false
});

function add_thumbnail_to_queue(img, loop) {
  thumbnails_queue.add({
    type: 'GET', 
    url: img.data('src'), 
    data: { ajaxload: 'true' },
    dataType: 'json',
    success: function(result) {
      img.attr('src', result.url);
    },
    error: function() {
      if (loop < 3)
        add_thumbnail_to_queue(img, ++loop); // Retry 3 times
    }
  }); 
}

function pwg_ajax_thumbnails_loader() {
  jQuery('img[data-src]').each(function() {
    add_thumbnail_to_queue(jQuery(this), 0);
  });
}

jQuery(document).ready(pwg_ajax_thumbnails_loader);