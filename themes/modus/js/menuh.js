$("#menuSwitcher").click( function() {
	var mb = $("#menubar");
	if (mb.is(":visible")) {
		mb.css("display", ""); // remove inline css in case browser resizes larger
	}
	else {
		$(".categoryActions,.actionButtons,.switchBox").css("display", "");
		mb.css("top", $(this).position().top + $(this).outerHeight(true))
			.css("display", "block");
	}
});

$("#menubar DT").click( function() {
	var $this = $(this);
	if ($this.css("display") != "block")
		return; // menu is horizontal
	var dd = $this.siblings("DD");
	if (dd.length) {
		if (dd.is(":visible"))
			dd.css("display", ""); // remove inline css in case browser resizes larger
		else
			dd.css("display", "block");
	}
});