
GeoIp = {
	cache: {},
	pending: {},
	
	get: function(ip, callback){
		if (!GeoIp.storageInit && window.localStorage) {
			GeoIp.storageInit = true;
			var cache = localStorage.getItem("freegeoip");
			if (cache) {
				cache = JSON.parse(cache);
				for (var ip in cache) {
					var data = cache[ip];
					if ( (new Date()).getTime() - data.reqTime > 36 * 3600000)
						delete cache[ip];
				}
				GeoIp.cache = cache;
			}
			jQuery(window).on("unload", function() {
				localStorage.setItem("freegeoip", JSON.stringify(GeoIp.cache) );
			} );
		}

		if (GeoIp.cache.hasOwnProperty(ip))
			callback(GeoIp.cache[ip]);
		else if (GeoIp.pending[ip])
			GeoIp.pending[ip].push(callback);
		else {
			GeoIp.pending[ip] = [callback];
			jQuery.ajax( {
				url: "http://freegeoip.net/json/" + ip,
				dataType: "jsonp",
				cache: true,
				success: function(data) {
					data.reqTime = (new Date()).getTime();
					var res=[];
					if (data.city) res.push(data.city);
					if (data.region_name) res.push(data.region_name);
					if (data.country_name) res.push(data.country_name);
					data.fullName = res.join(", ");

					GeoIp.cache[ip] = data;
					var callbacks = GeoIp.pending[ip];
					delete GeoIp.pending[ip];
					for (var i=0; i<callbacks.length; i++)
						callbacks[i].call(null, data);
				},

				error: function() {
					var data = {ip:ip, fullName:"", reqTime: (new Date()).getTime()};

					GeoIp.cache[ip] = data;
					var callbacks = GeoIp.pending[ip];
					delete GeoIp.pending[ip];
					for (var i=0; i<callbacks.length; i++)
						callbacks[i].call(null, data);
				}
			});
		}
	}
}