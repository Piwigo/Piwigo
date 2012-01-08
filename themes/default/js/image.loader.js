
function ImageLoader(opts) {
	this.opts = jQuery.extend( {
			maxRequests: 6,
			onChanged: jQuery.noop
		}, opts||{} );
}

ImageLoader.prototype = {
	loaded: 0,
	errors: 0,
	errorEma: 0,

	pause: false,

	current: [],
	queue: [],
	pool: [],

	remaining: function() {
		return this.current.length + this.queue.length;
	},

	add: function(urls) {
		this.queue = this.queue.concat( urls );
		this._fireChanged("add");
		this._checkQueue();
	},

	clear: function() {
		this.queue.length = 0;
		while (this.current.length)
			jQuery( this.current.pop() ).unbind();
		this.loaded = this.errors = this.errorEma = 0;
	},

	pause: function(val) {
		if (val !== undefined)
		{
			this.paused = val;
			this._checkQueue();
		}
		return this.paused;
	},

	_checkQueue: function() {
		while (!this.paused
			&& this.queue.length
			&& this.current.length < this.opts.maxRequests)
		{
			this._processOne( this.queue.shift() );
		}
	},

	_processOne: function(url) {
		var img = this.pool.shift() || new Image;
		this.current.push(img);
		var that = this;
		jQuery(img).bind( "load error abort", function(e) {
		//img.onload = function(e) {
			jQuery(img).unbind();
			img.onload=null;
			that.current.splice(jQuery.inArray(img, that.current), 1);
			if (e.type==="load") {
				that.loaded++;
				that.errorEma *= 0.9;
			}
			else {
				that.errors++;
				that.errorEma++;
				if (that.errorEma>=20 && that.errorEma<21)
					that.paused = true;
			}
			that._fireChanged(e.type, img);
			that._checkQueue();
			that.pool.push(img);
		} );
		img.src = url;
	},

	_fireChanged: function(type, img) {
		this.opts.onChanged(type, img);
	}
}