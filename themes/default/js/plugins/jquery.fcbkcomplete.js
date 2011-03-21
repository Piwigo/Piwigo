/**
 FCBKcomplete 2.7.5
 - Jquery version required: 1.2.x, 1.3.x, 1.4.x
 
 Based on TextboxList by Guillermo Rauch http://devthought.com/
 
 Changelog:
 - 2.00 new version of fcbkcomplete
 
 - 2.01 fixed bugs & added features
    fixed filter bug for preadded items
    focus on the input after selecting tag
    the element removed pressing backspace when the element is selected
    input tag in the control has a border in IE7
    added iterate over each match and apply the plugin separately
    set focus on the input after selecting tag
 
 - 2.02 fixed fist element selected bug
    fixed defaultfilter error bug
 
 - 2.5  removed selected="selected" attribute due ie bug
    element search algorithm changed
    better performance fix added
    fixed many small bugs
    onselect event added
    onremove event added
 
 - 2.6  ie6/7 support fix added
    added new public method addItem due request
    added new options "firstselected" that you can set true/false to select first element on dropdown list
    autoexpand input element added
    removeItem bug fixed
    and many more bug fixed
    fixed public method to use it $("elem").trigger("addItem",[{"title": "test", "value": "test"}]);
    
- 2.7   jquery 1.4 compability
    item lock possability added by adding locked class to preadded option <option value="value" class="selected locked">text</option>
    maximum item that can be added

- 2.7.1 bug fixed
    ajax delay added thanks to http://github.com/dolorian

- 2.7.2 some minor bug fixed
    minified version recompacted due some problems
    
- 2.7.3 event call fixed thanks to William Parry <williamparry!at!gmail.com>

- 2.7.4 standart event change call added on addItem, removeItem
    preSet also check if item have "selected" attribute
    addItem minor fix
    
- 2.7.5  event call removeItem fixed
         new public method destroy added needed to remove fcbkcomplete element from dome

 */
/* Coded by: emposha <admin@emposha.com> */
/* Copyright: Emposha.com <http://www.emposha.com> - Distributed under MIT - Keep this message! */

/**
 * json_url         - url to fetch json object
 * cache            - use cache
 * height           - maximum number of element shown before scroll will apear
 * newel            - show typed text like a element
 * firstselected    - automaticly select first element from dropdown
 * filter_case      - case sensitive filter
 * filter_selected  - filter selected items from list
 * complete_text    - text for complete page
 * maxshownitems    - maximum numbers that will be shown at dropdown list (less better performance)
 * onselect         - fire event on item select
 * onremove         - fire event on item remove
 * maxitimes        - maximum items that can be added
 * delay            - delay between ajax request (bigger delay, lower server time request)
 * addontab         - add first visible element on tab or enter hit
 * attachto         - after this element fcbkcomplete insert own elements
 */
jQuery(function($) {
    $.fn.fcbkcomplete = function(opt) {
        return this.each(function() {
            function init() {
                createFCBK();
                preSet();
                addInput(0);
            }

            function createFCBK() {
                element.hide();
                element.attr("multiple", "multiple");
                if (element.attr("name").indexOf("[]") == -1) {
                    element.attr("name", element.attr("name") + "[]");
                }

                holder = $(document.createElement("ul"));
                holder.attr("class", "holder");
                
                if (options.attachto) {
                  if (typeof(options.attachto) == "object") {
                    options.attachto.append(holder);
                  }
                  else {
                    $(options.attachto).append(holder);
                  }
                  
                }
                else {
                  element.after(holder);
                }

                complete = $(document.createElement("div"));
                complete.addClass("facebook-auto");
                complete.append('<div class="default">' + options.complete_text + "</div>");
                complete.hover(function() {options.complete_hover = 0;}, function() {options.complete_hover = 1;});
                
                feed = $(document.createElement("ul"));
                feed.attr("id", elemid + "_feed");

                complete.prepend(feed);
                holder.after(complete);
                feed.css("width", complete.width());
            }

            function preSet() {
                element.children("option").each(function(i, option) {
                    option = $(option);                    
                    if (option.hasClass("selected")) {
                        addItem(option.text(), option.val(), true, option.hasClass("locked"));
                        option.attr("selected", "selected");
                    }
                    cache.push({
                        key: option.text(),
                        value: option.val()
                    });
                    search_string += "" + (cache.length - 1) + ":" + option.text() + ";";
                });
            }

            //public method to add new item
            $(this).bind("addItem",
            function(event, data) {
                addItem(data.title, data.value, 0, 0, 0);
            });

            //public method to remove item
            $(this).bind("removeItem",
            function(event, data) {
                var item = holder.children('li[rel=' + data.value + ']');
                if (item.length) {
                    removeItem(item);
                }
            });
            
            //public method to remove item
            $(this).bind("destroy",
            function(event, data) {
                holder.remove();
                complete.remove();
                element.show();
            });

            function addItem(title, value, preadded, locked, focusme) {
                if (!maxItems()) {
                    return false;
                }
                var li = document.createElement("li");
                var txt = document.createTextNode(title);
                var aclose = document.createElement("a");
                var liclass = "bit-box" + (locked ? " locked": "");
                $(li).attr({
                    "class": liclass,
                    "rel": value
                });
                $(li).prepend(txt);
                $(aclose).attr({
                    "class": "closebutton",
                    "href": "#"
                });

                li.appendChild(aclose);
                holder.append(li);

                $(aclose).click(function() {
                    removeItem($(this).parent("li"));
                    return false;
                });

                if (!preadded) {
                    $("#" + elemid + "_annoninput").remove();
                    var _item;
                    addInput(focusme);
                    if (element.children("option[value=" + value + "]").length) {
                        _item = element.children("option[value=" + value + "]");
                        _item.get(0).setAttribute("selected", "selected");
                        _item.attr("selected", "selected");
                        if (!_item.hasClass("selected")) {
                            _item.addClass("selected");
                        }
                    }
                    else{
                        var _item = $(document.createElement("option"));
                        _item.attr("value", value).get(0).setAttribute("selected", "selected");
                        _item.attr("value", value).attr("selected", "selected");
                        _item.attr("value", value).addClass("selected");
                        _item.text(title);
                        element.append(_item);
                    }
                    if (options.onselect) {
                        funCall(options.onselect, _item)
                    }
                    element.change();
                }
                holder.children("li.bit-box.deleted").removeClass("deleted");
                feed.hide();
            }

            function removeItem(item) {

                if (!item.hasClass('locked')) {                    
                    item.fadeOut("fast");
                    if (options.onremove) {
                        var _item = element.children("option[value=" + item.attr("rel") + "]");
                        funCall(options.onremove, _item)
                    }
                    element.children('option[value="' + item.attr("rel") + '"]').removeAttr("selected").removeClass("selected");
                    item.remove();
                    element.change();
                    deleting = 0;
                }
            }

            function addInput(focusme) {
                var li = $(document.createElement("li"));
                var input = $(document.createElement("input"));
                var getBoxTimeout = 0;

                li.attr({
                    "class": "bit-input",
                    "id": elemid + "_annoninput"
                });
                input.attr({
                    "type": "text",
                    "class": "maininput",
                    "size": "1"
                });
                holder.append(li.append(input));

                input.focus(function() {
                    complete.fadeIn("fast");
                });

                input.blur(function() {
                    if (options.complete_hover) {
                      complete.fadeOut("fast");
                    }
                    else {
                      input.focus();
                    }
                });              

                holder.click(function() {
                    input.focus();
                    if (feed.length && input.val().length) {
                        feed.show();
                    }
                    else{
                        feed.hide();
                        complete.children(".default").show();
                    }
                });

                input.keypress(function(event) {
                    if (event.keyCode == 13) {
                        return false;
                    }
                    //auto expand input             
                    input.attr("size", input.val().length + 1);
                });

                input.keydown(function(event) {
                    //prevent to enter some bad chars when input is empty
                    if (event.keyCode == 191) {
                        event.preventDefault();
                        return false;
                    }
                });

                input.keyup(function(event) {
                    var etext = xssPrevent(input.val());

                    if (event.keyCode == 8 && etext.length == 0) {
                        feed.hide();
                        if (!holder.children("li.bit-box:last").hasClass('locked')) {
                            if (holder.children("li.bit-box.deleted").length == 0) {
                                holder.children("li.bit-box:last").addClass("deleted");
                                return false;
                            }
                            else{
                                if (deleting) {
                                    return;
                                }
                                deleting = 1;
                                holder.children("li.bit-box.deleted").fadeOut("fast",
                                function() {
                                    removeItem($(this));
                                    return false;
                                });
                            }
                        }
                    }

                    if (event.keyCode != 40 && event.keyCode != 38 && event.keyCode!=37 && event.keyCode!=39 && etext.length != 0) {
                        counter = 0;

                        if (options.json_url) {
                            if (options.cache && json_cache) {
                                addMembers(etext);
                                bindEvents();
                            }
                            else{
                                getBoxTimeout++;
                                var getBoxTimeoutValue = getBoxTimeout;
                                setTimeout(function() {
                                    if (getBoxTimeoutValue != getBoxTimeout) return;
                                    $.getJSON(options.json_url, {tag: etext},
                                    function(data) {
                                        addMembers(etext, data);
                                        json_cache = true;
                                        bindEvents();
                                    });
                                },
                                options.delay);
                            }
                        }
                        else{
                            addMembers(etext);
                            bindEvents();
                        }
                        complete.children(".default").hide();
                        feed.show();
                    }
                });
                if (focusme) {
                    setTimeout(function() {
                        input.focus();
                        complete.children(".default").show();
                    },
                    1);
                }
            }

            function addMembers(etext, data) {
                feed.html('');

                if (!options.cache && data != null) {
                    cache = new Array();
                    search_string = "";
                }

                addTextItem(etext);

                if (data != null && data.length) {
                    $.each(data,
                    function(i, val) {
                        cache.push({
                            key: val.key,
                            value: val.value
                        });
                        search_string += "" + (cache.length - 1) + ":" + val.key + ";";
                    });
                }

                var maximum = options.maxshownitems < cache.length ? options.maxshownitems: cache.length;
                var filter = "i";
                if (options.filter_case) {
                    filter = "";
                }

                var myregexp,
                match;
                try{
                    myregexp = eval('/(?:^|;)\\s*(\\d+)\\s*:[^;]*?' + etext + '[^;]*/g' + filter);
                    match = myregexp.exec(search_string);
                }
                catch(ex) {
                    };

                var content = '';
                while (match != null && maximum > 0) {
                    var id = match[1];
                    var object = cache[id];
                    if (options.filter_selected && element.children("option[value=" + object.value + "]").hasClass("selected")) {
                        //nothing here...
                        }
                    else{
                        content += '<li rel="' + object.value + '">' + itemIllumination(object.key, etext) + '</li>';
                        counter++;
                        maximum--;
                    }
                    match = myregexp.exec(search_string);
                }
                feed.append(content);

                if (options.firstselected) {
                    focuson = feed.children("li:visible:first");
                    focuson.addClass("auto-focus");
                }

                if (counter > options.height) {
                    feed.css({
                        "height": (options.height * 24) + "px",
                        "overflow": "auto"
                    });
                }
                else{
                    feed.css("height", "auto");
                }
            }

            function itemIllumination(text, etext) {
                if (options.filter_case) {
                    try{
                        eval("var text = text.replace(/(.*)(" + etext + ")(.*)/gi,'$1<em>$2</em>$3');");
                    }
                    catch(ex) {
                        };
                }
                else{
                    try{
                        eval("var text = text.replace(/(.*)(" + etext.toLowerCase() + ")(.*)/gi,'$1<em>$2</em>$3');");
                    }
                    catch(ex) {
                        };
                }
                return text;
            }

            function bindFeedEvent() {
                feed.children("li").mouseover(function() {
                    feed.children("li").removeClass("auto-focus");
                    $(this).addClass("auto-focus");
                    focuson = $(this);
                });

                feed.children("li").mouseout(function() {
                    $(this).removeClass("auto-focus");
                    focuson = null;
                });
            }

            function removeFeedEvent() {
                feed.children("li").unbind("mouseover");
                feed.children("li").unbind("mouseout");
                feed.mousemove(function() {
                    bindFeedEvent();
                    feed.unbind("mousemove");
                });
            }

            function bindEvents() {
                var maininput = $("#" + elemid + "_annoninput").children(".maininput");
                bindFeedEvent();
                feed.children("li").unbind("mousedown");
                feed.children("li").mousedown(function() {
                    var option = $(this);
                    addItem(option.text(), option.attr("rel"), 0, 0, 1);
                    feed.hide();
                    complete.hide();
                });

                maininput.unbind("keydown");
                maininput.keydown(function(event) {
                    if (event.keyCode == 191) {
                        event.preventDefault();
                        return false;
                    }

                    if (event.keyCode != 8) {
                        holder.children("li.bit-box.deleted").removeClass("deleted");
                    }

                    if ((event.keyCode == 13 || event.keyCode == 9) && checkFocusOn()) {
                        var option = focuson;
                        addItem(option.text(), option.attr("rel"), 0, 0, 1);
                        complete.hide();
                        event.preventDefault();
                        focuson = null;
                        return false;
                    }

                    if ((event.keyCode == 13 || event.keyCode == 9) && !checkFocusOn()) {
                        if (options.newel) {
                            var value = xssPrevent($(this).val());
                            addItem(value, value, 0, 0, 1);
                            complete.hide();
                            event.preventDefault();
                            focuson = null;
                            return false;
                        }
                        
                        if (options.addontab) {
                          focuson = feed.children("li:visible:first");
                          var option = focuson;
                          addItem(option.text(), option.attr("rel"), 0, 0, 1);
                          complete.hide();
                          event.preventDefault();
                          focuson = null;
                          return false;
                        }                        
                    }

                    if (event.keyCode == 40) {
                        removeFeedEvent();
                        if (focuson == null || focuson.length == 0) {
                            focuson = feed.children("li:visible:first");
                            feed.get(0).scrollTop = 0;
                        }
                        else{
                            focuson.removeClass("auto-focus");
                            focuson = focuson.nextAll("li:visible:first");
                            var prev = parseInt(focuson.prevAll("li:visible").length, 10);
                            var next = parseInt(focuson.nextAll("li:visible").length, 10);
                            if ((prev > Math.round(options.height / 2) || next <= Math.round(options.height / 2)) && typeof(focuson.get(0)) != "undefined") {
                                feed.get(0).scrollTop = parseInt(focuson.get(0).scrollHeight, 10) * (prev - Math.round(options.height / 2));
                            }
                        }
                        feed.children("li").removeClass("auto-focus");
                        focuson.addClass("auto-focus");
                    }
                    if (event.keyCode == 38) {
                        removeFeedEvent();
                        if (focuson == null || focuson.length == 0) {
                            focuson = feed.children("li:visible:last");
                            feed.get(0).scrollTop = parseInt(focuson.get(0).scrollHeight, 10) * (parseInt(feed.children("li:visible").length, 10) - Math.round(options.height / 2));
                        }
                        else{
                            focuson.removeClass("auto-focus");
                            focuson = focuson.prevAll("li:visible:first");
                            var prev = parseInt(focuson.prevAll("li:visible").length, 10);
                            var next = parseInt(focuson.nextAll("li:visible").length, 10);
                            if ((next > Math.round(options.height / 2) || prev <= Math.round(options.height / 2)) && typeof(focuson.get(0)) != "undefined") {
                                feed.get(0).scrollTop = parseInt(focuson.get(0).scrollHeight, 10) * (prev - Math.round(options.height / 2));
                            }
                        }
                        feed.children("li").removeClass("auto-focus");
                        focuson.addClass("auto-focus");
                    }
                });
            }

            function maxItems() {
                if (options.maxitems != 0) {
                    if (holder.children("li.bit-box").length < options.maxitems) {
                        return true;
                    }
                    else{
                        return false;
                    }
                }
            }

            function addTextItem(value) {
                if (options.newel && maxItems()) {
                    feed.children("li[fckb=1]").remove();
                    if (value.length == 0) {
                        return;
                    }
                    var li = $(document.createElement("li"));
                    li.attr({
                        "rel": value,
                        "fckb": "1"
                    }).html(value);
                    feed.prepend(li);
                    counter++;
                }
                else{
                    return;
                }
            }

            function funCall(func, item) {
                var _object = "";
                for (i = 0; i < item.get(0).attributes.length; i++) {
                    if (item.get(0).attributes[i].nodeValue != null) {
                        _object += "\"_" + item.get(0).attributes[i].nodeName + "\": \"" + item.get(0).attributes[i].nodeValue + "\",";
                    }
                }
                _object = "{" + _object + " notinuse: 0}";
                func.call(func, _object);
            }

            function checkFocusOn() {
                if (focuson == null) {
                    return false;
                }
                if (focuson.length == 0) {
                    return false;
                }
                return true;
            }

            function xssPrevent(string) {
                string = string.replace(/[\"\'][\s]*javascript:(.*)[\"\']/g, "\"\"");
                string = string.replace(/script(.*)/g, "");
                string = string.replace(/eval\((.*)\)/g, "");
                string = string.replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '');
                return string;
            }

            var options = $.extend({
                json_url: null,
                cache: false,
                height: "10",
                newel: false,
                addontab: false,
                firstselected: false,
                filter_case: false,
                filter_selected: false,
                complete_text: "Start to type...",
                maxshownitems: 30,
                maxitems: 10,
                onselect: null,
                onremove: null,
                attachto: null,
                delay: 350
            },
            opt);

            //system variables
            var holder = null;
            var feed = null;
            var complete = null;
            var counter = 0;
            var cache = new Array();
            var json_cache = false;
            var search_string = "";
            var focuson = null;
            var deleting = 0;
            var complete_hover = 1;

            var element = $(this);
            var elemid = element.attr("id");
            init();

            return this;
        });
    };
});
