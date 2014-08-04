var AdminTools = function($) {
  var __this = this;

  this.urlWS;
  this.urlSelf;
  this.multiView;

  var $ato = $('#ato_container');

  // fill multiview selects
  // data came from AJAX request or sessionStorage
  function populateMultiView() {
    var $multiview = $ato.find('.multiview');

    if ($multiview.data('init')) return;

    var render = function(data) {
      var html = '';
      $.each(data.users, function(i, user) {
        if (user.status == 'webmaster' || user.status == 'admin') {
          html+= '<option value="'+ user.id +'">'+ user.username +'</option>';
        }
      });
      $multiview.find('select[data-type="view_as"]').html(html)
        .val(__this.multiView.view_as);

      html = '';
      $.each(['clear','roma'], function(i, theme) {
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

  // attach jquery handlers
  this.init = function(open) {
    $('.multiview').appendTo($ato);

    /* <!-- sub menus --> */
    $ato.on({
      'click': function(e) {
        populateMultiView();
        $(this).find('ul').toggle();
      },
      'mouseleave': function(e) {
        if (e.target.tagName.toLowerCase() != "select") {
          $(this).find('ul').hide();
        }
      }
    });
    $ato.find('>a').on('click', function(e) {
      e.preventDefault();
    });
    $ato.find('ul').on('mouseleave', function(e) {
      if (e.target.tagName.toLowerCase() != "select") {
        $(this).hide();
      }
    });

    /* <!-- select boxes --> */
    $ato.find('.switcher').on({
      'change': function() {
        if ($(this).data('type') == 'theme') {
          if ($(this).val() != __this.multiView.theme) {
            window.location.href = __this.urlSelf + 'change_theme=1';
          }
        }
        else {
          window.location.href = __this.urlSelf + 'ato_'+ $(this).data('type') +'='+ $(this).val();
        }
      },
      'click': function(e) {
        e.stopPropagation();
      }
    });
  };

  return this;
}(jQuery);