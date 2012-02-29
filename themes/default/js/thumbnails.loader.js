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

jQuery('img').each(function() {
  var img = jQuery(this);
  if (typeof img.data('src') != 'undefined') {
    add_thumbnail_to_queue(img, 0);
  }
});