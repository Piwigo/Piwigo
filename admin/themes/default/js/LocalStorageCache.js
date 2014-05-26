/**
 * Base LocalStorage cache
 *
 * @param options {object}
 *    - key (required) identifier of the collection
 *    - serverId (recommended) identifier of the Piwigo instance
 *    - serverKey (required) state of collection server-side
 *    - lifetime (optional) cache lifetime in seconds
 *    - loader (required) function called to fetch data, takes a callback as first argument
 *        which must be called with the loaded date
 */
var LocalStorageCache = function(options) {
  this._init(options);
};

/*
 * Constructor (deported for easy inheritance)
 */
LocalStorageCache.prototype._init = function(options) {
  this.key = options.key + '_' + options.serverId;
  this.serverKey = options.serverKey;
  this.lifetime = options.lifetime ? options.lifetime*1000 : 3600*1000;
  this.loader = options.loader;
  
  this.storage = window.localStorage;
  this.ready = !!this.storage;
};

/*
 * Get the cache content
 * @param callback {function} called with the data as first parameter
 */
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

/*
 * Manually set the cache content
 * @param data {mixed}
 */
LocalStorageCache.prototype.set = function(data) {
  if (this.ready) {
    this.storage[this.key] = JSON.stringify({
      timestamp: new Date().getTime(),
      key: this.serverKey,
      data: data
    });
  }
};

/*
 * Manually clear the cache
 */
LocalStorageCache.prototype.clear = function() {
  if (this.ready) {
    this.storage.removeItem(this.key);
  }
};


/**
 * Special LocalStorage for admin categories list
 *
 * @param options {object}
 *    - serverId (recommended) identifier of the Piwigo instance
 *    - serverKey (required) state of collection server-side
 *    - rootUrl (required) used for WS call
 */
var CategoriesCache = function(options) {
  options.key = 'categoriesAdminList';
  
  options.loader = function(callback) {
    jQuery.getJSON(options.rootUrl + 'ws.php?format=json&method=pwg.categories.getAdminList', function(data) {
      callback(data.result.categories);
    });
  };
  
  this._init(options);
};

CategoriesCache.prototype = new LocalStorageCache({});

/*
 * Init Selectize with cache content
 * @param $target {jQuery}
 * @param options {object}
 *    - default (optional) default value which will be forced if the select is emptyed
 *    - filter (optional) function called for each select before applying the data
 *      takes two parameters: cache data, options
 *      must return new data
 */
CategoriesCache.prototype.selectize = function($target, options) {
  options = options || {};

  $target.selectize({
    valueField: 'id',
    labelField: 'fullname',
    sortField: 'global_rank',
    searchField: ['fullname'],
    plugins: ['remove_button']
  });
  
  this.get(function(categories) {
    $target.each(function() {
      var data;
      if (options.filter != undefined) {
        data = options.filter.call(this, categories, options);
      }
      else {
        data = categories;
      }
      
      this.selectize.load(function(callback) {
        callback(data);
      });

      if (jQuery(this).data('value')) {
        jQuery.each(jQuery(this).data('value'), jQuery.proxy(function(i, id) {
          this.selectize.addItem(id);
        }, this));
      }
      
      if (options.default != undefined) {
        if (this.selectize.getValue() == '') {
          this.selectize.addItem(options.default);
        }

        // if multiple: prevent item deletion
        if (this.multiple) {
          this.selectize.getItem(options.default).find('.remove').hide();
          
          this.selectize.on('item_remove', function(id) {
            if (id == options.default) {
              this.addItem(id);
              this.getItem(id).find('.remove').hide();
            }
          });
        }
        // if single: restore default on blur
        else {
          this.selectize.on('dropdown_close', function() {
            if (this.getValue() == '') {
              this.addItem(options.default);
            }
          });
        }
      }
    });
  });
};