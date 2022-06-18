jQuery.fn.pwgAddAlbum = function(options) {
  options = options || {};

  var $popup = jQuery('#addAlbumForm'),
      $albumParent = $popup.find('[name="category_parent"]')
      $button = jQuery(this),
      $target = jQuery('[name="'+ $button.data('addAlbum') +'"]'),
      cache = $target.data('cache');

  if (!$target[0].selectize) {
    jQuery.error('pwgAddAlbum: target must use selectize');
  }
  if (!cache) {
    jQuery.error('pwgAddAlbum: missing categories cache');
  }

  function init() {
    $popup.data('init', true);

    cache.selectize($albumParent, {
      'default': 0,
      'filter': function(categories) {
        categories.push({
          id: 0,
          fullname: '------------',
          global_rank: 0
        });

        if (options.filter) {
          categories = options.filter.call(this, categories);
        }

        return categories;
      }
    });

    $popup.find('form').on('submit', function(e) {
      e.preventDefault();

      var parent_id = $albumParent.val(),
      name = $popup.find('[name=category_name]').val();

      if (!name) {
        jQuery('#categoryNameError').css('visibility', 'visible');
        return;
      }
      jQuery('#categoryNameError').css('visibility', 'hidden');

      jQuery.ajax({
        url: 'ws.php?format=json',
        type: 'POST',
        dataType: 'json',
        data: {
          method: 'pwg.categories.add',
          parent: parent_id,
          name: name
        },
        beforeSend: function() {
          jQuery('#albumCreationLoading').css('display', 'inline-block');
          jQuery('.albumCreationButton').hide();
        },
        success: function(data) {
          jQuery('#albumCreationLoading').hide();
          jQuery('.albumCreationButton').show();
          $button.colorbox.close();

          var newAlbum = {
            id: data.result.id,
            name: name,
            fullname: name,
            global_rank: '0',
            dir: null,
            nb_images: 0,
            pos: 0
          };

          var parentSelectize = $albumParent[0].selectize;

          if (parent_id != 0) {
            var parent = parentSelectize.options[parent_id];
            newAlbum.fullname = parent.fullname + ' / ' + newAlbum.fullname;
            newAlbum.global_rank = parent.global_rank + '.1';
            newAlbum.pos = parent.pos + 1;
          }

          var targetSelectize = $target[0].selectize;
          targetSelectize.addOption(newAlbum);
          targetSelectize.setValue(newAlbum.id);

          parentSelectize.addOption(newAlbum);

          if (options.afterSelect) {
            options.afterSelect();
          }
        },
        error: function(XMLHttpRequest, textStatus, errorThrows) {
            jQuery('#albumCreationLoading').hide();
            alert(errorThrows);
        }
      });
    });
  }

  this.colorbox({
    inline: true,
    href: '#addAlbumForm',
    width: 650, height: 'auto',
    onComplete: function() {
      if (!$popup.data('init')) {
        init();
      }

      jQuery('#categoryNameError').css('visibility','hidden');
      $popup.find('[name=category_name]').val('').focus();
      $albumParent[0].selectize.setValue($target.val() || 0);
    }
  });

  return this;
};