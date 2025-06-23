function rvas_get_scaled_size(d, available) {
	var ratio_w = d.w / available.w
		, ratio_h = d.h / available.h;
	if (ratio_w>1 || ratio_h>1) {
		if (ratio_w>ratio_h)
			return {w: available.w / available.dpr, h: Math.floor(d.h / ratio_w / available.dpr)};
		else
			return {w: Math.floor(d.w / ratio_h / available.dpr), h: available.h / available.dpr};
	}
	return {w: Math.round(d.w / available.dpr), h: Math.round(d.h / available.dpr)};
}

function rvas_get_available_size(){
		var width = $("#theImage").width(),
			zoom = 1,
			docHeight;

		if ("innerHeight" in window) {
			docHeight = window.innerHeight;
			if (document.documentElement.clientWidth > window.innerWidth && window.innerWidth)
				zoom = document.documentElement.clientWidth / window.innerWidth;
			docHeight = Math.floor(docHeight*zoom);
		}
		else
			docHeight = document.documentElement.offsetHeight;
		var height = docHeight - Math.ceil($("#theImage").offset().top);

		var dpr = window.devicePixelRatio && window.devicePixelRatio>1 ? window.devicePixelRatio : 1;
		width = Math.floor(width*dpr); height = Math.floor(height*dpr);

		document.cookie= 'phavsz='+width+'x'+height+'x'+dpr+';path='+RVAS.cp;
		return {w:width, h:height, dpr:dpr, zoom:zoom};
}

function rvas_choose(relaxed){
	var best,
		available = rvas_get_available_size(),
		$img = $("#theMainImage"),
		changed = true;
	for (var i=0; i<RVAS.derivatives.length; i++){
		var d = RVAS.derivatives[i];
		if (d.w > available.w*available.zoom || d.h > available.h*available.zoom){
			if (available.dpr>1 || !best)
				best = d;
			break;
		}
		else
			best = d;
	}
	if (best) {
		if (available.dpr > 1) {
			var rescaled = rvas_get_scaled_size(best, available);
			if ($img.attr("width") && available.zoom==1) {
				var changeRatio = rescaled.h / $img.height()
					, limit = relaxed ? 1.25 : 1.15;
				if (changeRatio>=1 && changeRatio<limit
					|| (changeRatio<1 && changeRatio>1/limit && $img.width()<available.w/available.dpr) )
						return;
			}
			if (!$img.data("natural-w") || $img.data("natural-w") < best.w) {
				$img.attr("width", rescaled.w).attr("height", rescaled.h)
					.attr("src", best.url)
					.removeAttr("usemap")
					.data("natural-w", best.w);
			}
			else {
				$img.attr("width", rescaled.w).attr("height", rescaled.h);
				changed = false;
			}
		}
		else {
			if ($img.attr("width")) {
				var changeRatio = best.h / $img.height()
					, limit = relaxed ? 2 : 1.15;
				if (changeRatio>=1 && changeRatio<limit
					|| (changeRatio<1 && changeRatio>1/limit && $img.width()<available.w) )
						return;
			}
			$img
				.attr("width", best.w).attr("height", best.h)
				.attr("src", best.url)
				.attr("usemap", "#map"+best.type);
		}
		if (changed) {
			$('#derivativeSwitchBox .switchCheck').css('visibility','hidden');
			$('#derivativeChecked'+best.type).css('visibility','visible');
		}
	}
	$img.off('load').on('load', function() {
		const attrW = $(this).attr('width');
		const attrH = $(this).attr('height');
		$(this).css({
			'width': attrW ? attrW : 'auto',
			'height': attrH ? attrH : 'auto',
		});
	});
}

$(document).ready( function() {

	if (window.changeImgSrc) {
		RVAS.changeImgSrcOrig = changeImgSrc;
		changeImgSrc = function() {
			RVAS.disable = 1;
			RVAS.changeImgSrcOrig.apply(undefined, arguments);
		}
	}

	$(window).resize(function() {
		var w = $("body").width(),
			de = $(document.documentElement);
		if (document.location.search.indexOf("slideshow")==-1) {
			if (w<1262)
				de.removeClass("wide");
			else
				de.addClass("wide");
		}

		if (RVAS.disable)
			rvas_get_available_size();
		else
			rvas_choose();
	});

	$("#theMainImage").click( function(e) {
		if (!$(this).attr("usemap") && e.clientY) {
			var pct = (e.pageX - $(this).offset().left) / $(this).width()
				, clientY = e.pageY - $(this).offset().top;
			if (pct < 0.3) {
				if ($("#linkPrev").length && clientY>15)
					window.location = $("#linkPrev").attr("href");
			}
			else if (pct > 0.7 ) {
				if ($("#linkNext").length && clientY>15)
					window.location = $("#linkNext").attr("href");
			}
			else if (clientY/$(this).height() < 0.5 && clientY>15) {
				var href = $(".pwg-icon-arrow-n").parent("a").attr("href");
				if (href)
					window.location = href;
			}
		}
	});
});
