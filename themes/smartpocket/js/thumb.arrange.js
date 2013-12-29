
function SPTLine(margin, rowHeight) {
	this.elements = new Array;
	this.margin = margin;
	this.rowHeight = rowHeight;
	this.maxHeight = 0;
}

SPTLine.prototype = {
	width: 0,
	elementsWidth: 0,
	firstThumbIndex: 0,

	add: function($elt, absIndex) {
		if (this.elements.length === 0)
			this.firstThumbIndex = absIndex;
		if (!$elt.data("w"))
		{
			var w=$elt.width(), h=$elt.height();
			if (h > this.rowHeight) {
				w = Math.round(w * this.rowHeight/h);
				h = this.rowHeight;
			}
			$elt.data("w", w)
				.data("h", h);
		}

		var eltObj = {
			$elt: $elt,
			w: $elt.data("w"),
			h: $elt.data("h")
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


function SPThumbs(options) {
	this.opts = options;

	this.$thumbs = $('.thumbnails');
	if (this.$thumbs.length==0) return;
	this.$thumbs.css('text-align', 'left');

	this.opts.extraRowHeight = 0;
	if (window.devicePixelRatio > 1) {
		var dpr = window.devicePixelRatio;
		this.opts.extraRowHeight = 6; /*loose sharpness but only for small screens when we could "almost" fit with full sharpness*/
		this.opts.rowHeight = Math.round(this.opts.rowHeight / dpr ) + this.opts.extraRowHeight;
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
}

SPThumbs.prototype = {
	prevContainerWidth:0,
	prevLastLineFirstThumbIndex: 0,

	process: function(startIndex) {
		startIndex = startIndex ? startIndex : 0;
		var containerWidth  = this.$thumbs.width()
			, maxExtraMarginPerThumb = 1;
		this.prevContainerWidth = containerWidth;

		var $elts = $('li.liVisible>a>img', this.$thumbs)
			, line  = new SPTLine(this.opts.hMargin, this.opts.rowHeight);

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

		if (line.width / containerWidth > 1.01) {
			var ratio = line.elementsWidth / (line.elementsWidth + containerWidth - line.width);
			var adjustedRowHeight = rowHeight / (1 + (ratio-1) * 0.95 );
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
		$img.attr("width", imgW)
			.attr("height", imgH);

		$img.closest("li").css( {
			width: liW+"px",
			height: liH+"px"
		});

		$img.parent("a").css( {
			left: Math.round((liW-imgW)/2)+"px",
			top: Math.round((liH-imgH)/2)+"px"
		});
	}

}

