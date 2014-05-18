jQuery.timepicker.log = jQuery.noop; // that's ugly, but the timepicker is acting weird and throws parsing errors

jQuery.fn.pwgDatepicker = function(options) {
  options = options || {};
  
  return this.each(function() {
    var $this = jQuery(this),
        $target = jQuery('[name="'+ jQuery(this).data('datepicker') +'"]'),
        linked = !!$target.length;
    
    if (linked) { // get value before init
      var value = $target.val().split(' ');
    }

    // custom setter
    function set(date, init) {
      $this.datetimepicker('setDate', date);
      
      if ($this.data('datepicker-start')) {
        $start.datetimepicker('option', 'maxDate', date);
      }
      else if ($this.data('datepicker-end')) {
        if (!init) { // on init, "end" is not initialized yet (assuming "start" is before "end" in the DOM)
          $end.datetimepicker('option', 'minDate', date);
        }
      }
      
      if (!date && linked) {
        $target.val('');
      }
    }

    // init picker
    $this.datetimepicker(jQuery.extend({
      dateFormat: linked ? 'DD d MM yy' : 'yy-mm-dd',
      timeFormat: 'HH:mm',
      
      altField: linked ? $target : null,
      altFormat: 'yy-mm-dd',
      altTimeFormat: options.showTimepicker ? 'HH:mm:ss' : '',
      
      autoSize: true,
      changeMonth : true,
      changeYear: true,
      showTimepicker: false,
      altFieldTimeOnly: false,
      showSecond: false,
      alwaysSetTime: false,
      stepMinute: 5
    }, options));
    
    // attach range pickers
    if ($this.data('datepicker-start')) {
      var $start = jQuery('[data-datepicker="'+ jQuery(this).data('datepicker-start') +'"]');
      
      $this.datetimepicker('option', 'onClose', function(date) {
        $start.datetimepicker('option', 'maxDate', date);
      });
      
      $this.datetimepicker('option', 'minDate', $start.datetimepicker('getDate'));
    }
    else if ($this.data('datepicker-end')) {
      var $end = jQuery('[data-datepicker="'+ jQuery(this).data('datepicker-end') +'"]');
      
      $this.datetimepicker('option', 'onClose', function(date) {
        $end.datetimepicker('option', 'minDate', date);
      });
    }
    
    // attach unset button
    if ($this.data('datepicker-unset')) {
      jQuery('#'+ $this.data('datepicker-unset')).on('click', function(e) {
        e.preventDefault();
        set(null, false);
      });
    }
    
    // set value from linked input
    if (linked) {
      if (value[0].length == 10 && !options.showTimepicker) {
        set(jQuery.datepicker.parseDate('yy-mm-dd', value[0]), true);
      }
      else if (value.length == 2 && options.showTimepicker) {
        set(jQuery.datepicker.parseDateTime('yy-mm-dd', 'HH:mm:ss', value.join(' ')), true);
      }
      else {
        set(null, true);
      }
    }
    
    // autoSize not handled by timepicker
    if (options.showTimepicker) {
      $this.attr('size', parseInt($this.attr('size'))+6);
    }
  });
};