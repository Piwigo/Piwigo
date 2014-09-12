(function($){

/**
 * OPTIONS:
 * values {mixed[]}
 * selected {object} min and max
 * text {string}
 */
$.fn.pwgDoubleSlider = function(options) {
  var that = this;
  
  function onChange(e, ui) {
    that.find('[data-input=min]').val(options.values[ui.values[0]]);
    that.find('[data-input=max]').val(options.values[ui.values[1]]);

    that.find('.slider-info').html(sprintf(
      options.text,
      options.values[ui.values[0]],
      options.values[ui.values[1]]
    ));
  }

  function findClosest(array, value) {
    var closest = null, index = -1;
    $.each(array, function(i, v){
      if (closest == null || Math.abs(v - value) < Math.abs(closest - value)) {
        closest = v;
        index = i;
      }
    });
    return index;
  }

  var values = [
    options.values.indexOf(options.selected.min),
    options.values.indexOf(options.selected.max)
  ];
  if (values[0] == -1) {
    values[0] = findClosest(options.values, options.selected.min);
  }
  if (values[1] == -1) {
    values[1] = findClosest(options.values, options.selected.max);
  }

  var slider = this.find('.slider-slider').slider({
    range: true,
    min: 0,
    max: options.values.length - 1,
    values: values,
    slide: onChange,
    change: onChange
  });

  this.find('.slider-choice').on('click', function(){
    slider.slider('values', 0, options.values.indexOf($(this).data('min')));
    slider.slider('values', 1, options.values.indexOf($(this).data('max')));
  });

  return this;
};

}(jQuery));