let PWG_TOKEN;
$(function() {
  PWG_TOKEN = $('#pwg_token').val();
  $('.profile-section .display-btn').on('click', function () {
    const display = $(this).data('display');
    const selector = $(`#${display}`);
    const element = selector.get(0);
  
    if (selector.hasClass('open')) {
      // close
      element.style.maxHeight = element.scrollHeight + 'px';
      void element.offsetHeight;
      element.style.maxHeight = '0px';
      selector.removeClass('open');
      $(this).addClass('close');
    } else {
      // open
      selector.addClass('open');
      element.style.maxHeight = element.scrollHeight + 'px';
      $(this).removeClass('close');
      if ('account-display' !== display) {
        setTimeout(() => {
          const el = $(`#${display.split('-')[0]}-section`).get(0);
          el.scrollIntoView({
              behavior: 'smooth',
              block: 'start'
            });
          }, 200);
      }
    }
  });

  $('#account-section .display-btn').trigger('click');

  $('#save_account').on('click', function() {
    const mail = $('#email').val();
    if (!mail || mail == '') {
      $('#email_error').show();
      return;
    }
    setInfos({ email: mail });
  });

  $('#save_preferences').on('click', function() {
    const values = {
      nb_image_page: $('#nb_image_page').val(),
      theme: $('select[name="theme"]').val(),
      language: $('select[name="language"]').val(),
      recent_period: $('#recent_period').val(),
      expand: $('#opt_album').is(':checked'),
      show_nb_comments: $('#opt_comment').is(':checked'),
      show_nb_hits: $('#opt_hits').is(':checked')
    }

    if (values.nb_image_page == '') {
      $('#error_nb_image').show();
      return;
    }

    if (values.recent_period == '') {
      $('#error_period').show();
      return;
    }

    setInfos({...values});
  });

  $('#save_password').on('click', function() {
    const passwords = {
      password: $('#password').val(),
      new_password: $('#password_new').val(),
      conf_new_password: $('#password_conf').val(),
    }
    if (passwords.password  == '' || passwords.new_password == '' || passwords.conf_new_password == '') {
      $('#password-section input').each((i, element) => {
        const el = $(element);
        if (el.val() == '') {
          el.parent().siblings().show();
        }
      });
      return;
    }
    setInfos({...passwords});
    $('#password-section input').val('');
  });

  standardSaveSelector.forEach((selector, i) => {
    // console.log(i, selector);
    $(selector).on('click', function() {
      const values = {};
      $(`#${i}-section`).find('input, textarea, select').each((i, element) => {
        const el = $(element);
        const inputName = el.attr('name');
        const inputValue = el.val();
        values[inputName] = inputValue;
      });
      setInfos({...values});
    });
  });


  const userDefaultValues = {
    nb_image_page: $('input[name="nb_image_page"]').val(),
    theme: $('select[name="theme"]').val(),
    language: $('select[name="language"]').val(),
    recent_period: $('input[name="recent_period"]').val(),
    opt_album: $('#opt_album').is(':checked'),
    opt_comment: $('#opt_comment').is(':checked'),
    opt_hits: $('#opt_hits').is(':checked'),
  }

  $('#reset_preferences').on('click', function() {
    $('input[name="nb_image_page"]').val(userDefaultValues.nb_image_page);
    $('select[name="theme"]').val(userDefaultValues.theme);
    $('select[name="language"]').val(userDefaultValues.language);
    $('input[name="recent_period"]').val(userDefaultValues.recent_period);
    $('#opt_album').prop('checked', userDefaultValues.opt_album);
    $('#opt_comment').prop('checked', userDefaultValues.opt_comment);
    $('#opt_hits').prop('checked', userDefaultValues.opt_hits);
  });

  $('#default_preferences').on('click', function() {
    $('input[name="nb_image_page"]').val(preferencesDefaultValues.nb_image_page);
    $('input[name="recent_period"]').val(preferencesDefaultValues.recent_period);
    $('#opt_album').prop('checked', preferencesDefaultValues.opt_album);
    $('#opt_comment').prop('checked', preferencesDefaultValues.opt_comment);
    $('#opt_hits').prop('checked', preferencesDefaultValues.opt_hits);
  });
});

function setInfos(params, method='pwg.users.setMyInfo') {
  // for debug
  // console.log('setInfos', params);
  const all_params = {
    ...params,
    pwg_token: PWG_TOKEN
  }
  $.ajax({
    url: `ws.php?format=json&method=${method}`,
    type: "POST",
    dataType: "json",
    data: all_params,
    success: (data) => {
      if (data.stat == 'ok') {
        pwgToaster({ text: data.result, icon: 'success' });
      } else if (data.stat == 'fail') {
        pwgToaster({ text: data.message, icon: 'error' });
      } else {
        pwgToaster({ text: 'Error try later...', icon: 'error' });
      }
    },
    error: function (e) {
      pwgToaster({ text: e.responseJSON?.message ?? 'Server Internal Error try later...', icon: 'error' });
    },
  });
}