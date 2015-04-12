(function () {
	var sbFunc = function(link, box) {
		jQuery(link).click(function() {
			var elt = jQuery(box);
			elt.css("left", Math.min( jQuery(this).position().left, jQuery(window).width() - elt.outerWidth(true) - 5))
				.css("top", jQuery(this).position().top + jQuery(this).outerHeight(true))
				.toggle();
			return false;
		});
		jQuery(box).on("mouseleave click", function() {
			jQuery(this).hide();
		});
	};

	if (window.SwitchBox) {
		for (var i=0; i<SwitchBox.length; i+=2)
			sbFunc(SwitchBox[i], SwitchBox[i+1]);
	}

	window.SwitchBox = {
		push: sbFunc
	}
})();