(function(window, $, PhotoSwipe){
  $(document).ready(function(){
    var options = {
      jQueryMobile: true,
      captionAndToolbarAutoHideDelay: 0,
      imageScaleMethod: "fitNoUpscale",
      getToolbar: function(){
return '<div class="ps-toolbar-close"><div class="ps-toolbar-content"></div></div><div class="ps-toolbar-play"><div class="ps-toolbar-content"></div></div><a href="#" id="more_link">More Information</a><div class="ps-toolbar-previous"><div class="ps-toolbar-content"></div></div><div class="ps-toolbar-next"><div class="ps-toolbar-content"></div></div>';},
      getImageMetaData:function(el){
        return {
            picture_url: $(el).attr('data-picture-url')
        };}
    };
    var myPhotoSwipe = $(".thumbnails a").photoSwipe(options);
    myPhotoSwipe.addEventHandler(PhotoSwipe.EventTypes.onDisplayImage, function(e){
        var currentImage = myPhotoSwipe.getCurrentImage();
        $("#more_link").attr("href", currentImage.metaData.picture_url);
      });
    $(document).bind('orientationchange', set_thumbnails_width);
    $("#more_link").click(function(){
      console.log($(this).attr('href'));
      });
    set_thumbnails_width();
  });
}(window, window.jQuery, window.Code.PhotoSwipe));

function set_thumbnails_width() {
  nb_thumbs = Math.max(3, Math.ceil($('.thumbnails').width() / 130));
  width = Math.floor(1000000 / nb_thumbs) / 10000;
  $('.thumbnails li').css('width', width+'%');
}

