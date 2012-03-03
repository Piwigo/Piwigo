(function(window, $, PhotoSwipe){
  $(document).ready(function(){
    var options = {
      jQueryMobile: true,
      imageScaleMethod: "fitNoUpscale"
    };
    $(".thumbnails a").photoSwipe(options);
    $(document).bind('orientationchange', set_thumbnails_width);
    set_thumbnails_width();
  });
}(window, window.jQuery, window.Code.PhotoSwipe));

function set_thumbnails_width() {
  nb_thumbs = Math.max(3, Math.ceil($('.thumbnails').width() / 130));
  width = Math.floor(1000000 / nb_thumbs) / 10000;
  $('.thumbnails li').css('width', width+'%');
}