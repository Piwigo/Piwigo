if (window.jQuery) {
/**
* hoverIntent is similar to jQuery's built-in "hover" function except that
* instead of firing the onMouseOver event immediately, hoverIntent checks
* to see if the user's mouse has slowed down (beneath the sensitivity
* threshold) before firing the onMouseOver event.
* 
* hoverIntent r6 // 2011.02.26 // jQuery 1.5.1+
* <http://cherne.net/brian/resources/jquery.hoverIntent.html>
* 
* hoverIntent is currently available for use in all personal or commercial 
* projects under both MIT and GPL licenses. This means that you can choose 
* the license that best suits your project, and use it accordingly.
* 
* // basic usage (just like .hover) receives onMouseOver and onMouseOut functions
* $("ul li").hoverIntent( showNav , hideNav );
* 
* // advanced usage receives configuration object only
* $("ul li").hoverIntent({
*	sensitivity: 7, // number = sensitivity threshold (must be 1 or higher)
*	interval: 100,   // number = milliseconds of polling interval
*	over: showNav,  // function = onMouseOver callback (required)
*	timeout: 0,   // number = milliseconds delay before onMouseOut function call
*	out: hideNav    // function = onMouseOut callback (required)
* });
* 
* @param  f  onMouseOver function || An object with configuration options
* @param  g  onMouseOut function  || Nothing (use configuration options object)
* @author    Brian Cherne brian(at)cherne(dot)net
*/
(function($) {
	$.fn.hoverIntent = function(f,g) {
		// default configuration options
		var cfg = {
			sensitivity: 7,
			interval: 100,
			timeout: 0
		};
		// override configuration options with user supplied object
		cfg = $.extend(cfg, g ? { over: f, out: g } : f );

		// instantiate variables
		// cX, cY = current X and Y position of mouse, updated by mousemove event
		// pX, pY = previous X and Y position of mouse, set by mouseover and polling interval
		var cX, cY, pX, pY;

		// A private function for getting mouse position
		var track = function(ev) {
			cX = ev.pageX;
			cY = ev.pageY;
		};

		// A private function for comparing current and previous mouse position
		var compare = function(ev,ob) {
			ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
			// compare mouse positions to see if they've crossed the threshold
			if ( ( Math.abs(pX-cX) + Math.abs(pY-cY) ) < cfg.sensitivity ) {
				$(ob).unbind("mousemove",track);
				// set hoverIntent state to true (so mouseOut can be called)
				ob.hoverIntent_s = 1;
				return cfg.over.apply(ob,[ev]);
			} else {
				// set previous coordinates for next time
				pX = cX; pY = cY;
				// use self-calling timeout, guarantees intervals are spaced out properly (avoids JavaScript timer bugs)
				ob.hoverIntent_t = setTimeout( function(){compare(ev, ob);} , cfg.interval );
			}
		};

		// A private function for delaying the mouseOut function
		var delay = function(ev,ob) {
			ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
			ob.hoverIntent_s = 0;
			return cfg.out.apply(ob,[ev]);
		};

		// A private function for handling mouse 'hovering'
		var handleHover = function(e) {
			// copy objects to be passed into t (required for event object to be passed in IE)
			var ev = jQuery.extend({},e);
			var ob = this;

			// cancel hoverIntent timer if it exists
			if (ob.hoverIntent_t) { ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t); }

			// if e.type == "mouseenter"
			if (e.type == "mouseenter") {
				// set "previous" X and Y position based on initial entry point
				pX = ev.pageX; pY = ev.pageY;
				// update "current" X and Y position based on mousemove
				$(ob).bind("mousemove",track);
				// start polling interval (self-calling timeout) to compare mouse coordinates over time
				if (ob.hoverIntent_s != 1) { 
					ob.hoverIntent_t = setTimeout( function(){compare(ev,ob);} , cfg.interval );}

			// else e.type == "mouseleave"
			} else {
				// unbind expensive mousemove event
				$(ob).unbind("mousemove",track);
				// if hoverIntent state is true, then call the mouseOut function after the specified delay
				if (ob.hoverIntent_s == 1) { ob.hoverIntent_t = setTimeout( function(){delay(ev,ob);} , cfg.timeout );}
			}
		};


		// bind the function to the two event listeners
		//radu return this.bind('mouseenter',handleHover).bind('mouseleave',handleHover);
		return jQuery( this.context ).on( 'mouseenter mouseleave', this.selector, handleHover );
	};
})(jQuery);















jQuery("li>a>img", jQuery("#thumbnails")).hoverIntent({
interval: 150, sensitivity: 3,
over: function() {
	var $src_img = $(this),
		$pop = $("#pop")
		,data = $src_img.data("pop");
	if (!data) return;

	data = $.extend( {
		iw: $src_img.width(),
		ih: $src_img.height(),
		image: new Image
		}, data);
	data.image.src = data.url;
	if (window.devicePixelRatio) {
		data.w = Math.floor(data.w/window.devicePixelRatio);
		data.h = Math.floor(data.h/window.devicePixelRatio);
	}
	
	var finalLeft = $src_img.offset().left - (data.w-data.iw)/2,
		remaining =  $(window).scrollLeft() + $(window).width() - (finalLeft+data.w) - 1 /*rounding*/;
	if (remaining<0)
		finalLeft+=remaining;
	finalLeft = Math.max(finalLeft, $(window).scrollLeft() );
		
	var finalTop = $src_img.offset().top - (data.h-data.ih)/2,
		remaining = $(window).scrollTop() + $(window).height() - (finalTop+data.h) - 60 /*bottom description*/;
	if (remaining<0)
		finalTop+=remaining;
	finalTop = Math.max(finalTop, $(window).scrollTop() );

	$pop.css( {
		left:  $src_img.offset().left,
		top:  $src_img.offset().top,
		width: data.iw
		})
	.html('<div><a href="'+$src_img.parent('a').attr('href')+'"><img src="'+ $src_img.attr('src') + '" style="width:100%;height:'+data.ih+'px"></a></div>'
		+'<div style="overflow:hidden;height:5em;background-color:#000">'+$('.popDesc', $src_img.closest('li')).html()+'</div>')
	.show()
	.animate( {
		width: data.w+"px",
		left: finalLeft,
		top: finalTop
		},{
		duration: 100,
		complete: function() {
				$pop.one("mouseleave", function() {$pop.hide()} );
			},
		step: function(tw,fx) {
			if (fx.prop !== "width") return;
			var th = tw * data.h / data.w;
			$("img", $pop).first().css("height", th);
			if (tw==data.iw)
				$("img", $pop).first().attr("src", data.url);
		}
	})
},
out: $.noop
});

} //if (window.jQuery)