$("#albumActionsSwitcher").click( function() {
	var box = $(this).siblings(".categoryActions");
	if (box.is(":visible")) {
		box.css("display", ""); // remove inline css in case browser resizes larger
	}
	else {
		$("#menubar,.switchBox").css("display", "");
		box.css("left", Math.min( $(this).position().left, $(window).width() - box.outerWidth(true) - 5))
			.css("top", $(this).position().top + $(this).outerHeight(true))
			.css("display", "block");
	}
});

if ( !("ontouchstart" in document) )
	$(".categoryActions").on("mouseleave", function() {
		if ($("#albumActionsSwitcher").is(":visible"))
			$(this).css("display", ""); // remove inline css in case browser resizes larger
	});


$("#imageActionsSwitch").click( function() {
	var box = $(".actionButtons");
	if (box.is(":visible")) {
		box.css("display", ""); // remove inline css in case browser resizes larger
	}
	else {
		$("#menubar,.switchBox").css("display", "");
		box.css("left", Math.min( $(this).position().left, $(window).width() - box.outerWidth(true) - 5))
			.css("top", $(this).position().top + $(this).outerHeight(true))
			.css("display", "block");
	}
});

if ( !("ontouchstart" in document) )
	$(".actionButtons").on("mouseleave", function() {
		if ($("#imageActionsSwitch").is(":visible"))
			$(this).css("display", ""); // remove inline css in case browser resizes larger
	});
