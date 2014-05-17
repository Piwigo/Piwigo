jQuery.fn.pwgDatepicker = function(options) {
  options = options || {};
  
  return this.each(function() {
    var $this = jQuery(this),
        $target = jQuery('[name="'+ jQuery(this).data('datepicker') +'"]'),
        value = $target.val().split('-');
    
    function set(date) {
      $this.datepicker('setDate', date);
      
      if ($this.data('datepicker-start')) {
        $start.datepicker('option', 'maxDate', date);
      }
      else if ($this.data('datepicker-end')) {
        $end.datepicker('option', 'minDate', date);
      }
    }

    // init picker
    $this.datepicker(jQuery.extend({
      dateFormat: 'DD d MM yy',
      altField: $target,
      altFormat: 'yy-mm-dd',
      autoSize: true,
      changeMonth : true,
      changeYear: true
    }, options));
    
    // attach linked picker (for ranges)
    if ($this.data('datepicker-start')) {
      var $start = jQuery('[data-datepicker="'+ jQuery(this).data('datepicker-start') +'"]');
      
      $this.datepicker('option', 'onClose', function(date) {
        $start.datepicker('option', 'maxDate', date);
      });
    }
    else if ($this.data('datepicker-end')) {
      var $end = jQuery('[data-datepicker="'+ jQuery(this).data('datepicker-end') +'"]');
      
      $this.datepicker('option', 'onClose', function(date) {
        $end.datepicker('option', 'minDate', date);
      });
    }
    
    // attach unset button
    if ($this.data('datepicker-unset')) {
      jQuery('#'+ $this.data('datepicker-unset')).on('click', function(e) {
        e.preventDefault();
        
        $target.val('');
        set(null);
      });
    }
    
    // set value from linked input
    if (value.length == 3) {
      set(new Date(value[0], value[1]-1, value[2]));
    }
  });
};