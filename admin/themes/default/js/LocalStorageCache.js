var LocalStorageCache = function(key, lifetime, loader) {
  this.key = key;
  this.lifetime = lifetime*1000;
  this.loader = loader;
  
  this.storage = window.localStorage;
  this.ready = !!this.storage;
};

LocalStorageCache.prototype.get = function(callback) {
  var now = new Date().getTime(),
      that = this;
  
  if (this.ready && this.storage[this.key] != undefined) {
    var cache = JSON.parse(this.storage[this.key]);
    
    if (now - cache.timestamp <= this.lifetime) {
      callback(cache.data);
      return;
    }
  }
  
  this.loader(function(data) {
    if (that.ready) {
      that.storage[that.key] = JSON.stringify({
        timestamp: now,
        data: data
      });
    }
    
    callback(data);
  });
};

LocalStorageCache.prototype.set = function(data) {
  if (this.ready) {
    that.storage[that.key] = JSON.stringify({
      timestamp: new Date().getTime(),
      data: data
    });
  }
};

LocalStorageCache.prototype.clear = function() {
  if (this.ready) {
    this.storage.removeItem(this.key);
  }
};