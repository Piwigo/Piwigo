(function($) {
jQuery.timepicker.log = jQuery.noop; // that's ugly, but the timepicker is acting weird and throws parsing errors


// modify DatePicker internal methods to replace year select by a numeric input
var origGenerateMonthYearHeader = $.datepicker._generateMonthYearHeader,
    origSelectMonthYear = $.datepicker._selectMonthYear;

$.datepicker._generateMonthYearHeader = function(inst, drawMonth, drawYear, minDate, maxDate,
      secondary, monthNames, monthNamesShort) {

  var html = origGenerateMonthYearHeader.call(this, inst, drawMonth, drawYear, minDate, maxDate,
      secondary, monthNames, monthNamesShort);

  var yearshtml = "<input type='number' class='ui-datepicker-year' data-handler='selectYear' data-event='change keyup' value='"+drawYear+"' style='width:4em;margin-left:2px;'>";

  return html.replace(new RegExp('<select class=\'ui-datepicker-year\'.*</select>', 'gm'), yearshtml);
};

$.datepicker._selectMonthYear = debounce(function(id, select, period) {
  if (period === 'M') {
    origSelectMonthYear.call(this, id, select, period);
  }
  else {
    var target = $(id),
      inst = this._getInst(target[0]),
      val = parseInt(select.value, 10);

    if (isNaN(val)) {
      inst['drawYear'] = '';
    }
    else {
      inst['selectedYear'] = inst['drawYear'] = val;

      this._notifyChange(inst);
      this._adjustDate(target);

      $('.ui-datepicker-year').focus();
    }
  }
}, 500);


// plugin definition
jQuery.fn.pwgDatepicker = function(settings) {
  var options = jQuery.extend(true, {
    showTimepicker: false,
    cancelButton: false,
  }, settings || {});

  return this.each(function() {
    var $this = jQuery(this),
        originalValue = $this.val(),
        originalDate,
        $target = jQuery('[name="'+ $this.data('datepicker') +'"]'),
        linked = !!$target.length,
        $start, $end;

    if (linked) {
      originalValue = $target.val();
    }

    // custom setter
    function set(date, init) {
      if (date === '') date = null;
      $this.datetimepicker('setDate', date);

      if ($this.data('datepicker-start') && $start) {
        $start.datetimepicker('option', 'maxDate', date);
      }
      else if ($this.data('datepicker-end') && $end) {
        if (!init) { // on init, "end" is not initialized yet (assuming "start" is before "end" in the DOM)
          $end.datetimepicker('option', 'minDate', date);
        }
      }

      if (!date && linked) {
        $target.val('');
      }
    }

    // and custom cancel button
    if (options.cancelButton) {
      options.beforeShow = options.onChangeMonthYear = function() {
        setTimeout(function() {
          var buttonPane = $this.datepicker('widget')
              .find('.ui-datepicker-buttonpane');

          if (buttonPane.find('.pwg-datepicker-cancel').length == 0) {
            $('<button type="button">'+ options.cancelButton +'</button>')
              .on('click', function() {
                set(originalDate, false);
                $this.datepicker('hide').blur();
              })
              .addClass('pwg-datepicker-cancel ui-state-error ui-corner-all')
              .appendTo(buttonPane);
          }
        }, 1);
      };
    }

    // init picker
    $this.datetimepicker(jQuery.extend({
      dateFormat: linked ? 'DD d MM yy' : 'yy-mm-dd',
      timeFormat: 'HH:mm',
      separator: options.showTimepicker ? ' ' : '',

      altField: linked ? $target : null,
      altFormat: 'yy-mm-dd',
      altTimeFormat: options.showTimepicker ? 'HH:mm:ss' : '',

      autoSize: true,
      changeMonth : true,
      changeYear: true,
      altFieldTimeOnly: false,
      showSecond: false,
      alwaysSetTime: false
    }, options));

    // attach range pickers
    if ($this.data('datepicker-start')) {
      $start = jQuery('[data-datepicker="'+ $this.data('datepicker-start') +'"]');

      $this.datetimepicker('option', 'onClose', function(date) {
        $start.datetimepicker('option', 'maxDate', date);
      });

      $this.datetimepicker('option', 'minDate', $start.datetimepicker('getDate'));
    }
    else if ($this.data('datepicker-end')) {
      $end = jQuery('[data-datepicker="'+ $this.data('datepicker-end') +'"]');

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
      var splitted = originalValue.split(' ');
      if (splitted.length == 2 && options.showTimepicker) {
        set(jQuery.datepicker.parseDateTime('yy-mm-dd', 'HH:mm:ss', originalValue), true);
      }
      else if (splitted[0].length == 10) {
        set(jQuery.datepicker.parseDate('yy-mm-dd', splitted[0]), true);
      }
      else {
        set(null, true);
      }
    }

    originalDate = $this.datetimepicker('getDate');

    // autoSize not handled by timepicker
    if (options.showTimepicker) {
      $this.attr('size', parseInt($this.attr('size'))+6);
    }
  });
};

function debounce(func, wait, immediate) {
  var timeout;
  return function() {
    var context = this, args = arguments;
    var later = function() {
      timeout = null;
      if (!immediate) func.apply(context, args);
    };
    var callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) func.apply(context, args);
  };
}

}(jQuery));