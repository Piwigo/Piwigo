(function(window, $, PhotoSwipe){
  $(document).ready(function(){
    var more_link
    var options = {
      jQueryMobile: true,
      loop: var_loop,
      captionAndToolbarAutoHideDelay: var_autohide,
      imageScaleMethod: "fitNoUpscale",
      getToolbar: function(){
return '<div class="ps-toolbar-close"><div class="ps-toolbar-content"></div></div><div class="ps-toolbar-play"><div class="ps-toolbar-content"></div></div><div id="more_link">'+var_trad+'</div><div class="ps-toolbar-previous"><div class="ps-toolbar-content"></div></div><div class="ps-toolbar-next"><div class="ps-toolbar-content"></div></div>';},
      getImageMetaData:function(el){
        return {
            picture_url: $(el).attr('data-picture-url'),
            image_id: $(el).attr('data-image-id'),
        };}
    };
    var myPhotoSwipe = $(".thumbnails a").photoSwipe(options);
    // onShow - store a reference to our "more_link" button
    myPhotoSwipe.addEventHandler(PhotoSwipe.EventTypes.onShow, function(e){
      more_link = window.document.querySelectorAll('#more_link')[0];
    });
    // onToolbarTap - listen out for when the toolbar is tapped
    myPhotoSwipe.addEventHandler(PhotoSwipe.EventTypes.onToolbarTap, function(e){
			if (e.toolbarAction === PhotoSwipe.Toolbar.ToolbarAction.none){
				if (e.tapTarget === more_link || Util.DOM.isChildOf(e.tapTarget, more_link)){
					var currentImage = myPhotoSwipe.getCurrentImage();
					window.location=currentImage.metaData.picture_url;
				}
			}
		});

    myPhotoSwipe.addEventHandler(PhotoSwipe.EventTypes.onDisplayImage, function(e) {
      var currentImage = myPhotoSwipe.getCurrentImage();

      jQuery.ajax({
        type: "POST",
        url: 'ws.php?format=json&method=smartpocket.images.logHistory',
        data: {
          image_id:currentImage.metaData.image_id,
          cat_id:jQuery("ul.thumbnails").data("cat_id"),
          section:jQuery("ul.thumbnails").data("section"),
          tags_string:jQuery("ul.thumbnails").data("tags_string"),
        }
      });
    });

		var spThumbs = new SPThumbs(SPThumbsOpts);
  });
}(window, window.jQuery, window.Code.PhotoSwipe));
