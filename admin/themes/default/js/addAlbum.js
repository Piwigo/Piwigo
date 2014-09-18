jQuery.fn.pwgAddAlbum = function(options) {
  if (!options.cache) {
    jQuery.error('pwgAddAlbum: missing categories cache');
  }
  
  var $popup = jQuery('#addAlbumForm');
  
  function init() {
    if ($popup.data('init')) {
      return;
    }
    $popup.data('init', true);
    
    options.cache.selectize($popup.find('[name="category_parent"]'), {
      'default': 0,
      'filter': function(categories) {
        categories.push({
          id: 0,
          fullname: '------------',
          global_rank: 0
        });
        
        return categories;
      }
    });
    
    $popup.find('form').on('submit', function(e) {
      e.preventDefault();
      
      jQuery('#categoryNameError').text('');
      
      var albumParent = $popup.find('[name="category_parent"]'),
          parent_id = albumParent.val(),
          name = $popup.find('[name=category_name]').val(),
          target = $popup.data('target');

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
          jQuery('#albumCreationLoading').show();
        },
        success: function(data) {
          jQuery('#albumCreationLoading').hide();
          jQuery('[data-add-album="'+ target +'"]').colorbox.close();

          var newAlbum = data.result.id,
              newAlbum_name = '',
              newAlbum_rank = '0';
              
          if (parent_id != 0) {
            newAlbum_name = albumParent[0].selectize.options[parent_id].fullname +' / ';
            newAlbum_rank = albumParent[0].selectize.options[parent_id].global_rank +'.1';
          }
          newAlbum_name+= name;
          
          var $albumSelect = jQuery('[name="'+ target +'"]');
          
          // target is a normal select
          if (!$albumSelect[0].selectize) {
            var new_option = jQuery('<option/>')
                .attr('value', newAlbum)
                .attr('selected', 'selected')
                .text(newAlbum_name);

            $albumSelect.find('option').removeAttr('selected');
            
            if (parent_id==0) {
              $albumSelect.prepend(new_option);
            }
            else {
              $albumSelect.find('option[value='+ parent_id +']').after(new_option);
            }
          }
          // target is selectize
          else {
            var selectize = $albumSelect[0].selectize;
            
            if (jQuery.isEmptyObject(selectize.options)) {
              options.cache.clear();
              options.cache.selectize($albumSelect, {
                'default': newAlbum,
                'value': newAlbum
              });
            }
            else {
              $albumSelect[0].selectize.addOption({
                id: newAlbum,
                fullname: newAlbum_name,
                global_rank: newAlbum_rank
              });
              
              $albumSelect[0].selectize.setValue(newAlbum);
            }
          }

          albumParent.val('');
          jQuery('#albumSelection, .selectFiles, .showFieldset').show();
        },
        error: function(XMLHttpRequest, textStatus, errorThrows) {
            jQuery('#albumCreationLoading').hide();
            jQuery('#categoryNameError').text(errorThrows).css('color', 'red');
        }
      });
    });
  }
  
  this.colorbox({
    inline: true,
    href: '#addAlbumForm',
    width: 650, height: 300,
    onComplete: function() {
      init();
      $popup.data('target', jQuery(this).data('addAlbum'));
      $popup.find('[name=category_name]').focus();
    }
  });
  
  return this;
};