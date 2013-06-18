function switchBox(link, box) {
  jQuery(link).click(function() {
    var elt = jQuery(box);
    elt.css("left", Math.min( jQuery(this).offset().left, jQuery(window).width() - elt.outerWidth(true) - 5))
      .css("top", jQuery(this).offset().top + jQuery(this).outerHeight(true))
      .toggle();
  });
  jQuery(box).on("mouseleave click", function() {
    jQuery(this).hide();
  });
}