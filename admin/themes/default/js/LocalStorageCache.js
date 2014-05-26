var LocalStorageCache = function(options) {
  this.key = options.key + '_' + options.serverId;
  this.serverKey = options.serverKey;
  this.lifetime = options.lifetime ? options.lifetime*1000 : 3600*1000;
  this.loader = options.loader;
  
  this.storage = window.localStorage;
  this.ready = !!this.storage;
};

LocalStorageCache.prototype.get = function(callback) {
  var now = new Date().getTime(),
      that = this;
  
  if (this.ready && this.storage[this.key] != undefined) {
    var cache = JSON.parse(this.storage[this.key]);
    
    if (now - cache.timestamp <= this.lifetime && cache.key == this.serverKey) {
      callback(cache.data);
      return;
    }
  }
  
  this.loader(function(data) {
    that.set.call(that, data);
    callback(data);
  });
};

LocalStorageCache.prototype.set = function(data) {
  if (this.ready) {
    this.storage[this.key] = JSON.stringify({
      timestamp: new Date().getTime(),
      key: this.serverKey,
      data: data
    });
  }
};

LocalStorageCache.prototype.clear = function() {
  if (this.ready) {
    this.storage.removeItem(this.key);
  }
};