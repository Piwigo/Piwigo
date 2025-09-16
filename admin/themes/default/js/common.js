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
    else {
      jQuery(this).closest('label').addClass('selected');
    }
  });
  this.find('input[type=radio]').on('change', function() {
    jQuery('.font-checkbox input[type=radio][name="'+ jQuery(this).attr('name') +'"]').each(function() {
      jQuery(this).prev().removeClass();
      jQuery(this).closest('label').removeClass('selected');
      if (!jQuery(this).is(':checked')) {
        jQuery(this).prev().addClass('icon-circle-empty');
      }
      else {
        jQuery(this).prev().addClass('icon-dot-circled');
        jQuery(this).closest('label').addClass('selected');
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

$('.search-cancel').on('click', function () {
  $('.search-input').val('');
  $('.search-input').trigger ("input");
})

$('.search-input').on('input', function() {
  if ($('.search-input').val() == '') {
    $('.search-cancel').hide();
  } else {
    $('.search-cancel').show();
  }
})

// Class to implement a temporary state and reverse it
class TemporaryState {
  constructor() {
    //Arrays to reverse changes
    this.attrChanges = []; //Attribute changes : {object(s), attribute, value}
    this.classChanges = []; //Class changes : {object(s), state(add:true/remove:false), class}
    this.htmlChanges = []; //Html changes : {object(s), html}
  }

  /**
   * Change temporaly an attribute of an object
   * @param {Jquery Object(s)} obj HTML Object(s)
   * @param {String} attr Attribute
   * @param {String} tempVal Temporary value of the attribute 
   */
  changeAttribute(obj, attr, tempVal) {
    for (let i = 0; i < obj.length; i++) {
      this.attrChanges.push({
        object: $(obj[i]),
        attribute: attr,
        value: $(obj[i]).attr(attr)
      })
    }
    obj.attr(attr, tempVal)
  }

  /**
   * Add/remove a class temporarily
   * @param {Jquery Object(s)} obj HTML Object
   * @param {Boolean} st Add (true) or Remove (false) the class
   * @param {String} loadclass Class Name
   */
  changeClass(obj, st, tempclass) {
    for (let i = 0; i < obj.length; i++) {
      if (!($(obj[i]).hasClass(tempclass) && st)) {
        this.classChanges.push({
          object: $(obj[i]),
          state: !st,
          class: tempclass
        })
        if (st) 
          $(obj[i]).addClass(tempclass)
        else
          $(obj[i]).removeClass(tempclass)
      }
    }
  }

  /**
   * Add temporarily a class to the object
   * @param {Jquery Object(s)} obj 
   * @param {string} tempclass 
   */
  addClass(obj, tempclass) {
    this.changeClass(obj, true, tempclass);
  }

  /**
   * Remove temporarily a class to the object
   * @param {Jquery Object(s)} obj 
   * @param {string} tempclass 
   */
  removeClass(obj, tempclass) {
    this.changeClass(obj, false, tempclass);
  }

  /**
   * Change temporaly the html of objects (remove event handlers on the actual content)
   * @param {Jquery Object(s)} obj 
   * @param {string} temphtml 
   */
  changeHTML(obj, temphtml) {
    for (let i = 0; i < obj.length; i++) {
      this.htmlChanges.push({
        object:$(obj[i]),
        html:$(obj[i]).html()
      })
    }
    obj.html(temphtml);
  }

  /**
   * Reverse all the changes and clear the history
   */
  reverse() {
    this.attrChanges.forEach(function(change) {
      if (change.value == undefined) {
        change.object.removeAttr(change.attribute);
      } else {
        change.object.attr(change.attribute, change.value)
      }
    })
    this.classChanges.forEach(function(change) {
      if (change.state)
        change.object.addClass(change.class)
      else
        change.object.removeClass(change.class)
    })
    this.htmlChanges.forEach(function(change) {
      change.object.html(change.html);
    })
    this.attrChanges = [];
    this.classChanges = [];
    this.htmlChanges = [];
  }
}

const jConfirm_alert_options = {
  icon: 'icon-ok',
  titleClass: "jconfirmAlert",
  theme:"modern",
  closeIcon: true,
  draggable: false,
  animation: "zoom",
  boxWidth: '20%',
  useBootstrap: false,
  backgroundDismiss: true,
  animateFromElement: false,
  typeAnimated: false,
}

const jConfirm_confirm_options = {
  draggable: false,
  titleClass: "jconfirmDeleteConfirm",
  theme: "modern",
  animation: "zoom",
  boxWidth: '40%',
  useBootstrap: false,
  type: 'red',
  animateFromElement: false,
  backgroundDismiss: true,
  typeAnimated: false,
}

const jConfirm_warning_options = {
  icon: "icon-attention",
  draggable: false,
  titleClass: "jconfirmWarning jconfirmAlert",
  theme:"modern",
  type: 'orange',
  closeIcon: true,
  draggable: false,
  animation: "zoom",
  boxWidth: '20%',
  useBootstrap: false,
  backgroundDismiss: true,
  animateFromElement: false,
  typeAnimated: false,
}

const jConfirm_confirm_with_content_options = {
  draggable: false,
  theme: "modern",
  animation: "zoom",
  boxWidth: '40%',
  useBootstrap: false,
  type: 'red',
  animateFromElement: false,
  backgroundDismiss: true,
  typeAnimated: false,
}



jQuery.fn.pwg_jconfirm_follow_href = function({
  alert_title = "TITLE", 
  alert_confirm = "CONFIRM",
  alert_cancel = "CANCEL",
  alert_content = ""
} = {}) {
  let button_href = $(this).attr('href');
  const options = alert_content === "" ? jConfirm_confirm_options : jConfirm_confirm_with_content_options
  $(this).click(function() {
    $.confirm({
      content: alert_content,
      title: alert_title,
      buttons: {
        confirm: {
          text: alert_confirm,
          btnClass: 'btn-red',
          action: function () {
            window.location.href = button_href;
          }
        },
        cancel: {
          text: alert_cancel
        }
      },
      ...options
    });
    return (false);
  });
}

//Get username initials
//Used in menu and user list page
function get_initials(username) {
  let words = username.toUpperCase().split(" ");
  let res = words[0][0];

  if (words.length > 1 && words[1][0] !== undefined ) {
      res += words[1][0];
  }
  return res;
}

//Set any cookie
function setCookie(cname, cvalue, exdays) {
  const d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  let expires = "expires="+ d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

//Get the value of any cookie with its name
function getCookie(cname) {
  let name = cname + "=";
  let decodedCookie = decodeURIComponent(document.cookie);
  let ca = decodedCookie.split(';');
  for(let i = 0; i <ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

//Toggle light or dark mode in admin
function toggle_admin_mode(mode) {
  if ("roma" == mode)
  {
    //Dark mode
    $('#theAdminPage').addClass('roma');
  }
  else
  {
    //Light mode
    $('#theAdminPage').addClass('clear');
  }
}

//Function to handle admin mode change, used on click of inputs in menu
function checkModeSelection() {
  //Get the check radio
  const selectedMode = document.querySelector('input[name="appearance"]:checked');

  let autoMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
  let userPref = selectedMode.value;

  if('auto' == userPref){
    if(autoMode){
      userPref = 'roma';
    }
    else if( !autoMode){
      userPref = 'clear';
    }
    setCookie("autoAdminMode",true,30);
  }
  else{
    setCookie("autoAdminMode",false,30);
  }


  toggle_admin_mode(selectedMode.value);
  setCookie("adminMode",userPref,30);
  
  //update userpref 
  $.ajax({
    url: "ws.php?format=json&method=pwg.users.preferences.set",
    type: "POST",
    dataType: "JSON",
    data: {
      param: 'admin_theme',
      value: userPref,
    },
    success: function (res) {
      //We refresh at the moment due to how the swicth for themes used to be handled.
      //The css for roma and clear are in two different themes loaded in two seperate files
      //Until we change how the admin css is, aka all moved to one theme/One file we need to refresh
      window.location.replace(window.location.pathname + window.location.search);
    }
  })
}

// Hide the user options block when we click outisde of it
function hide_user_options(e){
  if (!$(e.target).closest('.user-actions').length) {
    $('.user-sub-link-container').hide();
    $('.icon-left-open').show();
    $('#menubar .user-actions').removeClass('active');
  }
}

// Check user system preference for light or dark mode, 
// set the preference in the cookie and change theme accordingly
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
  let newMode = event.matches ? "roma" : "clear";
  toggle_admin_mode(newMode);
});

$('document').ready(function()
{
  const isMobile = /Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent);

  // Get the first letter of user name and fill the green circle span 
  const initial_to_fill = get_initials(username);
  const initialSpan = $('#menubar').find(".user-container-initials-wrapper span");
  initialSpan.html(initial_to_fill);

  // on hover of menu display menu subitems for desktop
  $('.page-link').mouseenter(function (e) {
    const current_link = $(this);
    // Add a 2ms delay before displaying menu subitems 
    current_link.data('timeout', setTimeout(function () {
      // Hide user options 
      hide_user_options(e)
      current_link.children('.sub-link-container').css('display','block');
      current_link.children('span').children('.hover').children('.icon-down-open').css('rotate','-90deg');
    }, 250));
  }).mouseleave(function () {
    // when mouse leaves element reset the timeout so it doesn't display the sub elements because it's not the one the user wants to display 
    const current_link = $(this);
    current_link.children('.sub-link-container').css('display','none');
    current_link.children('span').children('.hover').children('.icon-down-open').css('rotate','0deg');
    clearTimeout(current_link.data('timeout'));
  });

  // On click of user name display sub menu 
  $('.user-actions').click(function () {
    const current_link = $(this);
    current_link.children('.user-sub-link-container').toggle();
    current_link.children('.icon-left-open').toggle();
    $('#menubar .user-actions').toggleClass('active');
  })

  // Outside of user options block if we click hide it 
  $(document).click(function (e) {
    hide_user_options(e)
  });

  //On document load check if adminMode cookie is set if not set auto preference
  let adminModeCookie = getCookie("adminMode"); 
  let menuSizePreferenceCookie = getCookie("menuSizePreference")

  //Adapt menu size to user cookie
  if ("reduced" == menuSizePreferenceCookie)
  {
    $('#menubar').toggleClass('enlarged').toggleClass('reduced');
    $('#content').toggleClass('reduced');
  }

  //Check if adminMode cookie has been set and check the option that corresponds
  if("" != adminModeCookie)
  {
    if (getCookie("autoAdminMode")){
      adminModeCookie = 'auto'
    }

    toggle_admin_mode()
    $('input[name="appearance"]').each(function(){
        if(adminModeCookie == $( this ).val()){
          $(this).parent('label').addClass('selected');
          $(this).siblings('span').toggleClass('icon-circle-empty icon-dot-circled');
        }
    });
  }

  //Reduce and enlarge menu
  //Save the user preferences in the cookies
  $('#reduce-enlarge').click(function () {
    //We make sure that we are using a desktop to enable enlarged and reduced
    if (!isMobile) {
      $('#menubar').toggleClass('enlarged').toggleClass('reduced');
      $('#content').toggleClass('reduced');
      //Set cookie to be used in user pref for enlarged or reduced menu
      if($('#menubar').hasClass('enlarged'))
      {
        setCookie("menuSizePreference",'enlarged',30);
      }
      else if($('#menubar').hasClass('reduced'))
      {
        setCookie("menuSizePreference",'reduced',30);
      }
    }
  });

  // //Actions are for mobile
  // if (isMobile){
  //   //Toggle slide up of menu
  //   $('#menu-toggle').click(function () {
  //     $('#menubar').toggleClass('open');
  //   });

  //   //Toggle menu sub items on mobile touch
  //   $('.page-link').on('pointerdown', function(e) {
  //     if (e.pointerType === 'touch') {  // only trigger for touch aka mobile
  //       let child = $(this).find('.sub-link-container');
  //       child.slideToggle();
  //     }
  //   });
  // } 

})
