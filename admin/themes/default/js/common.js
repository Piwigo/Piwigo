jQuery.fn.fontCheckbox = function() {
  /* checkbox */
  this.find('input[type=checkbox]').each(function() {
    if (!jQuery(this).is(':checked')) {
      jQuery(this).prev().toggleClass('icon-check icon-check-empty');
    }
  });
  this.find('input[type=checkbox]').on('change', function() {
    jQuery(this).prev().removeClass();
    if (!jQuery(this).is(':checked')) {
      jQuery(this).prev().addClass('icon-check-empty');
    }
    else {
      jQuery(this).prev().addClass('icon-check');
    }
  });

  /* radio */
  this.find('input[type=radio]').each(function() {
    if (!jQuery(this).is(':checked')) {
      jQuery(this).prev().toggleClass('icon-dot-circled icon-circle-empty');
    }
  });
  this.find('input[type=radio]').on('change', function() {
    jQuery('.font-checkbox input[type=radio][name="'+ jQuery(this).attr('name') +'"]').each(function() {
      jQuery(this).prev().removeClass();
      if (!jQuery(this).is(':checked')) {
        jQuery(this).prev().addClass('icon-circle-empty');
      }
      else {
        jQuery(this).prev().addClass('icon-dot-circled');
      }
    })
  });
};

// init fontChecbox everywhere
jQuery('.font-checkbox').fontCheckbox();

function array_delete(arr, item) {
  var i = arr.indexOf(item);
  if (i != -1) arr.splice(i, 1);
}

function str_repeat(i, m) {
  for (var o = []; m > 0; o[--m] = i);
  return o.join('');
}

if (!Array.prototype.indexOf)
{
  Array.prototype.indexOf = function(elt /*, from*/)
  {
    var len = this.length;

    var from = Number(arguments[1]) || 0;
    from = (from < 0)
         ? Math.ceil(from)
         : Math.floor(from);
    if (from < 0)
      from += len;

    for (; from < len; from++)
    {
      if (from in this &&
          this[from] === elt)
        return from;
    }
    return -1;
  };
}

function getRandomInt(min, max) {
  min = Math.ceil(min);
  max = Math.floor(max);
  return Math.floor(Math.random() * (max - min)) + min;
}

function sprintf() {
  var i = 0, a, f = arguments[i++], o = [], m, p, c, x, s = '';
  while (f) {
    if (m = /^[^\x25]+/.exec(f)) {
      o.push(m[0]);
    }
    else if (m = /^\x25{2}/.exec(f)) {
      o.push('%');
    }
    else if (m = /^\x25(?:(\d+)\$)?(\+)?(0|'[^$])?(-)?(\d+)?(?:\.(\d+))?([b-fosuxX])/.exec(f)) {
      if (((a = arguments[m[1] || i++]) == null) || (a == undefined)) {
        throw('Too few arguments.');
      }
      if (/[^s]/.test(m[7]) && (typeof(a) != 'number')) {
        throw('Expecting number but found ' + typeof(a));
      }

      switch (m[7]) {
        case 'b': a = a.toString(2); break;
        case 'c': a = String.fromCharCode(a); break;
        case 'd': a = parseInt(a); break;
        case 'e': a = m[6] ? a.toExponential(m[6]) : a.toExponential(); break;
        case 'f': a = m[6] ? parseFloat(a).toFixed(m[6]) : parseFloat(a); break;
        case 'o': a = a.toString(8); break;
        case 's': a = ((a = String(a)) && m[6] ? a.substring(0, m[6]) : a); break;
        case 'u': a = Math.abs(a); break;
        case 'x': a = a.toString(16); break;
        case 'X': a = a.toString(16).toUpperCase(); break;
      }

      a = (/[def]/.test(m[7]) && m[2] && a >= 0 ? '+'+ a : a);
      c = m[3] ? m[3] == '0' ? '0' : m[3].charAt(1) : ' ';
      x = m[5] - String(a).length - s.length;
      p = m[5] ? str_repeat(c, x) : '';
      o.push(s + (m[4] ? a + p : p + a));
    }
    else {
      throw('Huh ?!');
    }

    f = f.substring(m[0].length);
  }

  return o.join('');
}