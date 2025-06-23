
function RVGTLine(margin, rowHeight) {
	this.elements = new Array;
	this.margin = margin;
	this.rowHeight = rowHeight;
	this.maxHeight = 0;
}

RVGTLine.prototype = {
	width: 0,
	elementsWidth: 0,
	firstThumbIndex: 0,

	add: function($elt, absIndex) {
		if (this.elements.length === 0)
			this.firstThumbIndex = absIndex;
		var w,h;

		if (! (w=$elt.data("w")) ) {
			if ( (w=$elt[0].getAttribute("width")) && (w=parseInt(w)) ) {
				h=parseInt($elt[0].getAttribute("height"));
			}
			else {
				w=$elt.width();
				h=$elt.height();
			}
			if (h > this.rowHeight) {
				w = Math.round(w * this.rowHeight/h);
				h = this.rowHeight;
			}
			$elt.data("w", w)
				.data("h", h);
		}
		else
			h=$elt.data("h");
		

		var eltObj = {
			$elt: $elt,
			w: w,
			h: h
		};
		this.elements.push(eltObj);

		if (eltObj.h > this.maxHeight)
			this.maxHeight = eltObj.h;

		this.width += this.margin + eltObj.w;
		this.elementsWidth += eltObj.w;
	},

	clear: function() {
		if (!this.elements.length) return;
		this.width = this.elementsWidth = 0;
		this.maxHeight = 0;
		this.elements.length = 0;
	}
}


function RVGThumbs(options) {
	this.opts = options;

	this.$thumbs = $('#thumbnails');
	if (this.$thumbs.length==0) return;
	this.$thumbs.css('text-align', 'left');

	this.opts.extraRowHeight = 0;
	if (window.devicePixelRatio > 1) {
		var dpr = window.devicePixelRatio;
		this.opts.resizeThreshold = 1.01;
		this.opts.resizeFactor = 0.95;
		this.opts.extraRowHeight = 6; /*loose sharpness but only for small screens when we could "almost" fit with full sharpness*/
		this.opts.rowHeight = Math.round(this.opts.rowHeight / dpr ) + this.opts.extraRowHeight;
	}
	else {
		this.opts.resizeThreshold = 1.12; /*if row is less than 12% larger than available width, distribute extra width through cropping*/
		this.opts.resizeFactor = 0.8;/* when row is more than 12% larger than available width, distribute extra width 80% through resizing and 20% through cropping*/
	}
	this.process();

	var that = this;
	$(window).on('resize', function() {
		if (Math.abs(that.$thumbs.width() - that.prevContainerWidth)>1)
			that.process();
	})
		.on('RVTS_loaded', function(evt, down) {
			that.process( down && that.$thumbs.width() == that.prevContainerWidth ? that.prevLastLineFirstThumbIndex : 0);
		} );

	if (!$.isReady) {
		$(document).ready( function() {
			if ( that.$thumbs.width() < that.prevContainerWidth )
				that.process();
			} );
	}
}

RVGThumbs.prototype = {
	prevContainerWidth:0,
	prevLastLineFirstThumbIndex: 0,

	process: function(startIndex) {
		startIndex = startIndex ? startIndex : 0;
		var containerWidth  = this.$thumbs.width()
			, maxExtraMarginPerThumb = 1;
		this.prevContainerWidth = containerWidth;

		var $elts = $('li>a>img', this.$thumbs)
			, line  = new RVGTLine(this.opts.hMargin, this.opts.rowHeight);

		for (var i=startIndex; i<$elts.length; i++) {
			var $elt = $( $elts[i] );

			line.add($elt, i);
			if (line.width >= containerWidth - maxExtraMarginPerThumb * line.elements.length) {
				this.processLine(line, containerWidth);
				line.clear();
			}
		};

		if(line.elements.length)
			this.processLine(line, containerWidth, true);
		this.prevLastLineFirstThumbIndex = line.firstThumbIndex;
	},

	processLine: function(line, containerWidth, lastLine) {
		var toRecover, eltW, eltH
			, rowHeight = line.maxHeight ? line.maxHeight : line.elements[0].h;

		if (line.width / containerWidth > this.opts.resizeThreshold) {
			var ratio = line.elementsWidth / (line.elementsWidth + containerWidth - line.width);
			var adjustedRowHeight = rowHeight / (1 + (ratio-1) * this.opts.resizeFactor );
			adjustedRowHeight = 6 * Math.round( adjustedRowHeight/6 );
			if (adjustedRowHeight < rowHeight / ratio ) {
				adjustedRowHeight = Math.ceil( rowHeight / ratio );
				var missing = this.opts.rowHeight - this.opts.extraRowHeight - adjustedRowHeight;
				if (missing>0 && missing<6)
					adjustedRowHeight += missing;
			}
			if (adjustedRowHeight < rowHeight)
				rowHeight = adjustedRowHeight;
		}
		else if (lastLine)
			rowHeight = Math.min( rowHeight, this.opts.rowHeight - this.opts.extraRowHeight);

		toRecover = line.width - containerWidth;
		if (lastLine)
			toRecover = 0;

		for(var i=0; i<line.elements.length; i++) {
			var eltObj = line.elements[i]
				, eltW=eltObj.w
				, eltH=eltObj.h
				, eltToRecover;

			if (i==line.elements.length-1)
				eltToRecover = toRecover;
			else
				eltToRecover = Math.round(toRecover * eltW / line.elementsWidth);

			toRecover -= eltToRecover;
			line.elementsWidth -= eltW;

			if (eltH > rowHeight ) {
				eltW = Math.round( eltW * rowHeight/eltObj.h );
				eltH = rowHeight;
				eltToRecover -= eltObj.w - eltW;
				if (lastLine)
					eltToRecover = 0;
			}

			this.reposition(eltObj.$elt, eltW, eltH, eltW-eltToRecover, rowHeight);
		}
	},

	reposition: function($img, imgW, imgH, liW, liH) {
		/* JQuery .attr and .css functions add too much overhead ...*/
		var elt = $img[0];
		elt.setAttribute("width", imgW+"");
		elt.setAttribute("height", imgH+"");

		elt = elt.parentNode;//a
		elt.style.left = Math.round((liW-imgW)/2)+"px";
		elt.style.top = Math.round((liH-imgH)/2)+"px";

		elt = elt.parentNode;//li
		elt.style.width = liW+"px";
		elt.style.height = liH+"px";

	}

}

