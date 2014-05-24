jQuery.fn.pwgAddAlbum = function(options) {
  if (!options.cache) {
    jQuery.error('pwgAddAlbum: missing categories cache');
  }
  
  var $popup = jQuery('#addAlbumForm');
  if (!$popup.data('init')) {
    $popup.find('[name="category_parent"]').selectize({
      valueField: 'id',
      labelField: 'fullname',
      sortField: 'fullname',
      searchField: ['fullname'],
      plugins: ['remove_button'],
      onInitialize: function() {
        this.on('dropdown_close', function() {
          if (this.getValue() == '') {
            this.setValue(0);
          }
        });
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
              newAlbum_name = '';
              
          if (parent_id != 0) {
            newAlbum_name = albumParent[0].selectize.options[parent_id].fullname +' / ';
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
            $albumSelect[0].selectize.addOption({
              id: newAlbum,
              fullname: newAlbum_name
            });
            
            $albumSelect[0].selectize.setValue(newAlbum);
          }

          albumParent.val('');
          jQuery('#albumSelection').show();
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
    width: 350, height: 300,
    onComplete: function() {
      var albumParent = $popup.find('[name="category_parent"]')[0];
      
      $popup.data('target', jQuery(this).data('addAlbum'));

      albumParent.selectize.clearOptions();
      
      options.cache.get(function(categories) {
        categories.push({
          id: 0,
          fullname: '------------'
        });
        
        albumParent.selectize.load(function(callback) {
          callback(categories);
        });
        
        albumParent.selectize.setValue(0);
      });
    }
  });
  
  return this;
};