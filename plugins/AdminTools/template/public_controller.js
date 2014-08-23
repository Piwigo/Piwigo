var AdminTools = function($) {
  var __this = this;

  this.urlWS;
  this.urlSelf;
  this.multiView;

  var $ato = $('#ato_header'),
      $ato_closed = $('#ato_header_closed'),
      ato_height = 28; // normal height, real height conputed on init()

  // move to whole page down or up
  function moveBody(dir, anim) {
    var operator = dir=='show' ? '+=' : '-=';

    if (anim) {
      $('body').animate({'margin-top': operator+ato_height});

      if ($('#the_page, [data-role="page"]').css('position')=='absolute') {
        $('#the_page, [data-role="page"]').animate({'top': operator+ato_height});
      }
    }
    else {
      $('body').css({'margin-top': operator+ato_height});

      if ($('#the_page, [data-role="page"]').css('position')=='absolute') {
        $('#the_page, [data-role="page"]').css({'top': operator+ato_height});
      }
    }
  }

  // fill multiview selects
  // data came from AJAX request or sessionStorage
  function populateMultiView() {
    var $multiview = $ato.find('.multiview');

    if ($multiview.data('init')) return;

    var render = function(data) {
      var html = '';
      $.each(data.users, function(i, user) {
        html+= '<option value="'+ user.id +'">'+ user.username +'</option>';
      });
      $multiview.find('select[data-type="view_as"]').html(html)
        .val(__this.multiView.view_as);

      html = '';
      $.each(data.themes, function(i, theme) {
        html+= '<option value="'+ theme +'">'+ theme +'</option>';
      });
      $multiview.find('select[data-type="theme"]').html(html)
        .val(__this.multiView.theme);

      html = '';
      $.each(data.languages, function(i, language) {
        html+= '<option value="'+ language.id +'">'+ language.name +'</option>';
      });
      $multiview.find('select[data-type="lang"]').html(html)
        .val(__this.multiView.lang);

      $multiview.data('init', true);

      $multiview.find('.switcher').show();
    };

    if ('sessionStorage' in window && window.sessionStorage.multiView != undefined) {
      render(JSON.parse(window.sessionStorage.multiView));
    }
    else {
      $.ajax({
        method: 'POST',
        url: __this.urlWS + 'multiView.getData',
        dataType: 'json',
        success: function(data) {
          render(data.result);
          if ('sessionStorage' in window) {
            window.sessionStorage.multiView = JSON.stringify(data.result);
          }
        },
        error: function(xhr, text, error) {
          alert(text + ' ' + error);
        }
      });
    }
  }

  // delete session cache
  this.deleteCache = function() {
    if ('sessionStorage' in window) {
      window.sessionStorage.removeItem('multiView');
    }
  };

  // move close button to smartpocket toolbar
  this.initMobile = function() {
    var $headerbar = $('div[data-role="header"] .title');
    if ($headerbar.length == 1) {
      $ato_closed.addClass('smartpocket');
      $ato_closed.find('a').attr({
        'data-iconpos':'notext',
        'data-role':'button'
      });
      $headerbar.prepend($ato_closed);
    }
  };

  // attach jquery handlers
  this.init = function(open) {
    $('body').prepend($ato); // ensure the bar is at the begining
    
    $ato.show();
    ato_height = $ato.height();

    if ('localStorage' in window) {
      if (window.localStorage.ato_panel_open == null) {
        window.localStorage.ato_panel_open = open;
      }

      if (window.localStorage.ato_panel_open == 1) {
        moveBody('show', false);
      }
      else {
        $ato.hide();
        $ato_closed.show();
      }
    }
    else {
      $ato.show();
      moveBody('show', false);
    }

    /* <!-- sub menus --> */
    $ato.find('.parent').on({
      'click': function() {
        if ($(this).hasClass('multiview')) {
          populateMultiView();
        }
        $(this).find('ul').toggle();
      },
      'mouseleave': function(e) {
        if (e.target.tagName.toLowerCase() != "select") {
          $(this).find('ul').hide();
        }
      }
    });
    $ato.find('.parent>a').on('click', function(e) {
      e.preventDefault();
    });
    $ato.find('.parent ul').on('mouseleave', function(e) {
      if (e.target.tagName.toLowerCase() != "select") {
        $(this).hide();
      }
    });

    /* <!-- select boxes --> */
    $ato.find('.switcher').on({
      'change': function() {
        window.location.href = __this.urlSelf + 'ato_'+ $(this).data('type') +'='+ $(this).val();
      },
      'click': function(e) {
        e.stopPropagation();
      }
    });

    /* <!-- toggle toolbar --> */
    $ato.find('.close-panel').on('click', function(e) {
      $ato.slideUp();
      $ato_closed.slideDown();
      moveBody('hide', true);

      if ('localStorage' in window) window.localStorage.ato_panel_open = 0;
      e.preventDefault();
    });

    $ato_closed.on('click', function(e) {
      $ato.slideDown();
      $ato_closed.slideUp();
      moveBody('show', true);

      if ('localStorage' in window) window.localStorage.ato_panel_open = 1;
      e.preventDefault();
    });
  };

  // init "set as representative" button
  this.initRepresentative = function(image_id, category_id) {
    $ato.find('.set-representative').on('click', function(e) {
      if (!$(this).parent().hasClass('disabled')) {
        $(this).parent().addClass('disabled')

        $.ajax({
          method: 'POST',
          url: __this.urlWS + 'pwg.categories.setRepresentative',
          dataType: 'json',
          data: {
            image_id: image_id,
            category_id: category_id
          },
          success: function() {
            $ato.find('.saved').fadeIn(200).delay(1600).fadeOut(200);
          },
          error: function(xhr, text, error) {
            alert(text + ' ' + error);
          }
        });
      }

      e.preventDefault();
    });
  };

  // init "add to caddie" button
  this.initCaddie = function(image_id) {
    $ato.find('.add-caddie').on('click', function(e) {
      if (!$(this).parent().hasClass('disabled')) {
        $(this).parent().addClass('disabled')

        $.ajax({
          method: 'POST',
          url: __this.urlWS + 'pwg.caddie.add',
          dataType: 'json',
          data: {
            image_id: image_id
          },
          success: function() {
            $ato.find('.saved').fadeIn(200).delay(1600).fadeOut(200);
          },
          error: function(xhr, text, error) {
            alert(text + ' ' + error);
          }
        });
      }

      e.preventDefault();
    });
  };

  // init "quick edit" popup
  this.initQuickEdit = function(is_picture, tokeninput_lang) {
    var $ato_edit = $('#ato_quick_edit');

    // try to find background color matching text color
    // there is a 1s delay to wait for jQuery Mobile initialization
    function bgColor() {
      var bg_color = 'white';
      var selectors = ['#the_page #content', '[data-role="page"]', 'body'];

      for (var i=0; i<selectors.length; i++) {
        var color = $(selectors[i]).css('background-color');
        if (color && color!='transparent') {
          bg_color = color;
          break;
        }
      }

      $ato_edit.css('background-color', bg_color);
    }

    $ato_edit.find('.close-edit').on('click', function(e) {
      $.colorbox.close()
      e.preventDefault();
    });

    $(".edit-quick").colorbox({
      inline: true,
      transition: 'none',
      width: 500,
      maxWidth: '100%',
      top: 50,
      title: $ato_edit.attr('title'),

      onComplete: function() {
        setTimeout(function() {
          $('#quick_edit_name').focus();
        }, 0);
      },

      onOpen: function() {
        bgColor();

        if (is_picture) {
          $ato_edit.find('.datepicker').datepicker({
            dateFormat: 'yy-mm-dd'
          });

          // fetch tags list on first open
          if ($(this).data('tags-init')) return;

          $.ajax({
            method: 'POST',
            url: __this.urlWS + 'pwg.tags.getList',
            dataType: 'json',
            success: function(data) {
              var tags = [];
              // convert to custom format
              for (var i=0, l=data.result.tags.length; i<l; i++) {
                tags.push({
                  id: '~~'+ data.result.tags[i].id +'~~',
                  name: data.result.tags[i].name
                });
              }

              $ato_edit.find('.tags').tokenInput(
                tags,
                $.extend({
                  animateDropdown: false,
                  preventDuplicates: true,
                  allowFreeTagging: true
                }, tokeninput_lang)
              );

              $.colorbox.resize();
              $(this).data('tags-init', true);
            },
            error: function(xhr, text, error) {
              alert(text + ' ' + error);
            }
          });
        }
      }
    });

    // Ctrl+E opens the quick edit
    Mousetrap.bind('mod+e', function(e) {
      e.preventDefault();
      $(".edit-quick").click();
    });
  };

  return this;
}(jQuery);