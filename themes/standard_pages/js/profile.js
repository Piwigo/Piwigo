let PWG_TOKEN;
$(function() {
  PWG_TOKEN = $('#pwg_token').val();
  $('.profile-section .display-section').on('click', function() {
    const display = $(this).data('display');
    const selector = $(`#${display}`);
    const element = selector.get(0);
    const arrow = $(this).find('.display-btn');
  
    if (selector.hasClass('open')) {
      // close
      element.style.maxHeight = element.scrollHeight + 'px';
      void element.offsetHeight;
      element.style.maxHeight = '1px';
      selector.removeClass('open');
      arrow.addClass('close');
    } else {
      // open
      selector.addClass('open');
      resetSection(display);
      arrow.removeClass('close');
    }
  });

  setTimeout(() => {
    $('#account-section .display-section').trigger('click');
  }, 100);

  $('#save_account').on('click', function() {
    const mail = $('#email').val();
    if (!mail || mail == '') {
      $('#email_error').show();
      return;
    }
    setInfos({ email: mail });
  });

  if (canUpdatePreferences) {
    $('#save_preferences').on('click', function () {
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

      setInfos({ ...values });
    });

    $('#reset_preferences').on('click', function () {
      $('input[name="nb_image_page"]').val(user.nb_image_page);
      $('select[name="theme"]').val(user.theme);
      $('select[name="language"]').val(user.language);
      $('input[name="recent_period"]').val(user.recent_period);
      $('#opt_album').prop('checked', user.opt_album);
      $('#opt_comment').prop('checked', user.opt_comment);
      $('#opt_hits').prop('checked', user.opt_hits);
    });

    $('#default_preferences').on('click', function () {
      $('input[name="nb_image_page"]').val(preferencesDefaultValues.nb_image_page);
      $('input[name="recent_period"]').val(preferencesDefaultValues.recent_period);
      $('#opt_album').prop('checked', preferencesDefaultValues.opt_album);
      $('#opt_comment').prop('checked', preferencesDefaultValues.opt_comment);
      $('#opt_hits').prop('checked', preferencesDefaultValues.opt_hits);
    });
  }

  if (canUpdatePassword) {
    $('#save_password').on('click', function () {
      const passwords = {
        password: $('#password').val(),
        new_password: $('#password_new').val(),
        conf_new_password: $('#password_conf').val(),
      }
      if (passwords.password == '' || passwords.new_password == '' || passwords.conf_new_password == '') {
        $('#password-section input').each((i, element) => {
          const el = $(element);
          if (el.val() == '') {
            el.parent().siblings().show();
          }
        });
        return;
      }
      setInfos({ ...passwords });
      $('#password-section input').val('');
    });
  }
  

  standardSaveSelector.forEach((selector, i) => {
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

  // API KEY BELOW
  if (!can_manage_api) {
    $('.can-manage').hide();
    $('#cant_manage_api').show();
    return;
  };  
  $('#new_apikey').on('click', function() {
    openApiModal();
  });
  
  $('#close_api_modal, #cancel_apikey').on('click', function() {
    closeApiModal();
  });

  $('#close_api_modal_edit').on('click', function() {
    closeApiEditModal();
  });

  $('#close_api_modal_revoke, #cancel_api_revoke').on('click', function() {
    closeApiRevokeModal();
  });

  $('#show_expired_list').on('click', function() {
    const api_list_expired = $('#api_key_list_expired');
    const isOpen = $(this).data('show');
    if(!isOpen) {
      api_list_expired.get(0).style.maxHeight = 'max-content';
      $(this).text(str_hide_expired);
    } else {
      api_list_expired.get(0).style.maxHeight = '0';
      $(this).text(str_show_expired);
    }

    $(this).data('show', !isOpen);

    resetSection('apikey-display', false, true);
  });
  
  $(window).on('keydown', function(e) {
    const haveApiModal = $('#api_modal').is(':visible');
    const haveApiEditModal = $('#api_modal_edit').is(':visible');
    const haveApiRevokeModal = $('#api_modal_revoke').is(':visible');
    if (haveApiModal && e.key === 'Escape') {
      closeApiModal();
    }
    if (haveApiEditModal && e.key === 'Escape') {
      closeApiEditModal();
    }
    if (haveApiRevokeModal && e.key === 'Escape') {
      closeApiRevokeModal();
    }
  });
  
  $('select[name="api_expiration"]').on('change', function() {
    const custom_date = $('#api_custom_date');
    const value = $(this).val();
    if ('custom' === value) {
      custom_date.css('display', 'flex');
    } else {
      custom_date.css('display', 'none');
    }
    $('#error_api_key_date').hide();
  });

  $('#api_expiration_date').on('change', function() {
    $('#error_api_key_date').hide();
  });
  
  getAllApiKeys();
});

function setInfos(params, method='pwg.users.setMyInfo', callback=null, errCallback=null) {
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
        user = {...user, ...params};
        if (typeof callback === 'function') {
          callback(data.result);
          return;
        };
        pwgToaster({ text: data.result, icon: 'success' });
        return;
      } else if (data.stat == 'fail') {
        pwgToaster({ text: data.message, icon: 'error' });
      } else {
        pwgToaster({ text: str_handle_error, icon: 'error' });
      }
      if (typeof errCallback === 'function') {
        errCallback(data);
        return;
      }
    },
    error: function (e) {
      pwgToaster({ text: e.responseJSON?.message ?? str_handle_error, icon: 'error' });
      if (typeof errCallback === 'function') {
        errCallback(e);
        return;
      }
    },
  });
}

function getAllApiKeys(reset = false) {
  $.ajax({
    url: 'ws.php?format=json&method=pwg.users.api_key.get',
    type: "POST",
    dataType: 'json',
    data: {
      pwg_token: PWG_TOKEN
    },
    success: function(res) {
      if (res.stat == 'ok') {
        if (typeof res.result === 'string' || res.result === false) {
          // No keys
        } else {
          AddApiLine(res.result, reset);
        }
      }
    },
    error: function(e) {
      pwgToaster({ text: e.responseJSON?.message ?? str_handle_error + 'getAllApiKeys', icon: 'error' });
    }
  });
}

function AddApiLine(lines, reset) {
  const api_list = $('#api_key_list');
  const api_list_expired = $('#api_key_list_expired');

  $('#api_key_list .api-tab-line:not(.template-api), #api_key_list .api-tab-collapse:not(.template-api)').remove();
  $('#api_key_list_expired .api-tab-line:not(.template-api), #api_key_list_expired .api-tab-collapse:not(.template-api)').remove();

  lines.forEach((line, i) => {
    const api_line = $('#api_line').clone();
    const api_collapse = $('#api_collapse').clone();
    const tmp_id = line.auth_key.slice(24, 34);

    api_line.removeClass('template-api').addClass('api-tab');
    api_line.attr('id', `api_${tmp_id}`);
    api_line.find('.icon-collapse').data('api', tmp_id);
    api_line.find('.api_name').text(line.apikey_name).attr('title', line.apikey_name);
    api_line.find('.api_creation').text(line.created_on_format);
    api_line.find('.api_last_use').text(line.last_used_on_since).attr('title', line.last_used_on_since);
    api_line.find('.api_expiration').text(line.expiration);
    api_line.find('.api-icon-action').attr('data-api', `api_${tmp_id}`);
    api_line.find('.api-icon-action').attr('data-pkid', line.auth_key);

    api_collapse.attr('id', `api_collapse_${tmp_id}`);
    api_collapse.removeClass('template-api');
    api_collapse.find('.api_key').text(line.auth_key);
    api_collapse.find('.icon-clone').attr({
      'data-copy': line.auth_key,
      'data-success': `api_copy_success_${tmp_id}`
    });
    api_collapse.find('.api-copy').attr('id', `api_copy_success_${tmp_id}`);

    if (!line.revoked_on && !line.is_expired) {
      api_list.append(api_line);
      api_line.after(api_collapse);
    } else {
      $('#show_expired_list').show();
      api_list_expired.append(api_line);
      api_line.after(api_collapse);
      api_line.find('.api-icon-action').remove();
      if (line.is_expired) {
        api_line.find('.api_expiration').html(`<i class="gallery-icon-skull api-skull"></i> <span data-tooltip="${line.expired_on_format}">${line.expired_on_since}</span>`);
      } else {
        api_line.find('.api_expiration').html(`<i class="gallery-icon-skull api-skull"></i> <span>${/\d/.test(line.revoked_on_since) ? line.revoked_on_since : no_time_elapsed}</span> <i data-tooltip="${line.revoked_on_message}" class="icon-info-circled-1 api-info"></i>`);
      }
    }
    
  });

  apiLineEvent();
  if (reset) {
    resetSection('apikey-display');
  }
}

function apiLineEvent() {
  $('.icon-collapse').off('click').on('click', function() {
    const api_collapse = $(`#api_collapse_${$(this).data('api')}`);
    const api_line = $(`#api_${$(this).data('api')}`);

    if (api_collapse.is(':visible')) {
      api_collapse.removeClass('open');
      api_line.removeClass('open');
      api_line.find('.icon-collapse').addClass('close');
      api_collapse.css('display', 'none');
      api_collapse.find('.api-copy').addClass('api-hide');
    } else {
      api_collapse.addClass('open');
      api_line.addClass('open');
      api_line.find('.icon-collapse').removeClass('close');
      api_collapse.css('display', 'grid');
    }

    resetSection('apikey-display', false, true);
  });

  $('.api-tab-collapse .icon-clone').off('click').on('click', function() {
    const data_to_copy = $(this).data('copy');
    const selector = $(this).data('success');
    copyToClipboard(data_to_copy, str_copy_key_id, `#${selector}`);
  });

  $('.api-tab-line .edit-mode').off('click').on('click', function() {
    const selector = $(this).parent().data('api');
    openApiEditModal(`#${selector}`);
  });

  $('.api-tab-line .delete-mode').off('click').on('click', function() {
    const selector = $(this).parent().data('api');
    openApiRevokeModal(`#${selector}`);
  });

}

function resetSection(selector, scroll = true, maxContent = false) {
  const el = $(`#${selector}`);
  const element = el.get(0);
  const scrollH = maxContent ? 'max-content' : element.scrollHeight + 'px';
  element.style.maxHeight = scrollH;

  if ('account-display' !== selector && scroll) {
    setTimeout(() => {
      const el = $(`#${selector.split('-')[0]}-section`).get(0);
      el.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    }, 200);
  }
}

function openApiModal() {
  $('#api_modal').fadeIn();
  $('#api_key_name').trigger('focus');
  saveApiKeyEvent();
}

function closeApiModal() {
  $('#api_modal').fadeOut(() => {
    $('#api_key_name').val('');
    $('select[name="api_expiration"]').val(selected_date).trigger('change');
    $('#api_expiration_date').val('');

    $('#api_secret_key').val('');
    $('#retrieves_keyapi').hide();
    $('#generate_keyapi').show();
    $('#done_apikey').attr('disabled', true);
    $('#api_key_copy_success, #api_id_copy_success').addClass('api-hide');
  });
  unbindApiKeyEvents();
}

function successApiModal(secret, id) {
  $('#api_secret_key').val(secret);
  $('#api_id_key').val(id);

  $('#generate_keyapi').hide();
  $('#retrieves_keyapi').fadeIn();

  $('#api_secret_copy').off('click').on('click', function() {
    const copy = copyToClipboard(secret, str_copy_key_secret, '#api_key_copy_success');
    
    $('#done_apikey').removeAttr('disabled');
    $('#done_apikey').on('click', closeApiModal);
  });

  $('#api_id_copy').off('click').on('click', function() {
    const copy = copyToClipboard(id, str_copy_key_id, '#api_id_copy_success');
  });
}

//api edit modal
function openApiEditModal(selector) {
  const value = $(selector).find('.api_name').text();
  const pkid = $(selector).find('.api-icon-action').data('pkid');
  $('#api_key_edit').val(value);
  $('#api_modal_edit').fadeIn();
  $('#api_key_edit').trigger('focus');
  saveApiEditEvents(pkid);
}

function closeApiEditModal() {
  $('#api_modal_edit').fadeOut(() => {
    $('#api_key_edit').val('');
    unbindApiEditEvents();
  });
}

function saveApiEditEvents(pkid) {
  $('#save_api_edit').on('click', function() {
    const value = $('#api_key_edit').val();

    if ('' == value) {
      $('#error_api_key_edit').show();
      return;
    }
    setInfos(
      {
        pkid,
        key_name: value,
      },
      'pwg.users.api_key.edit', 
      (res) => {
        pwgToaster({ text: str_api_edited, icon: 'success' });
        getAllApiKeys(true);
        closeApiEditModal();
      }
    );
  });
}

function unbindApiEditEvents() {
  $('#save_api_edit').off('click');
}

// api revoke modal
function openApiRevokeModal(selector) {
  const apiName = $(selector).find('.api_name').text();
  const pkid = $(selector).find('.api-icon-action').data('pkid');
  const text = sprintf(str_revoke_key, apiName);
  $('#api_modal_revoke_title').text(text);

  $('#api_modal_revoke').fadeIn();
  saveApiRevokeEvents(pkid);
}

function closeApiRevokeModal() {
  $('#api_modal_revoke').fadeOut(() => {
    $('#api_modal_revoke_title').text('');
    unbindApiRevokeEvents();
  });
}

function saveApiRevokeEvents(pkid) {
  $('#revoke_api_key').on('click', function() {
    setInfos(
      {
        pkid,
      },
      'pwg.users.api_key.revoke', 
      (res) => {
        pwgToaster({ text: str_api_revoked, icon: 'success' });
        getAllApiKeys(true);
        closeApiRevokeModal();
      }
    );
  });
}

function unbindApiRevokeEvents() {
  $('#revoke_api_key').off('click');
}

function copyToClipboard(copy, message, selector = null) {
  if (window.isSecureContext && navigator.clipboard) {
    navigator.clipboard.writeText(copy);
    if (selector) {
      $(selector).removeClass('api-hide');
      // auto hide
      // setTimeout(() => {
      //   $(selector).addClass('api-hide');
      // }, 1000);
    } else {
      pwgToaster({ text: message, icon: 'success' });
    }
    return true;
  } else {
    pwgToaster({ text: str_cant_copy, icon: 'error' });
    return false;
  }
}

function saveApiKeyEvent() {
  const handler = () => {
    const api_name = $('#api_key_name').val();
    let api_duration = $('select[name="api_expiration"]').val();
    
    if (api_name == '') {
      $('#error_api_key_name').show();
      return;
    }

    if ('custom' === api_duration && !$('#api_expiration_date').val()) {
      $('#error_api_key_date').show();
      return;
    }


    unbindApiKeyEvents();

    if ('custom' === api_duration) {
      const today = new Date();
      const custom_date = new Date($('#api_expiration_date').val());
      const one_day = 1000 * 60 * 60 * 24;
      const days = Math.ceil((custom_date.getTime() - today.getTime() ) / (one_day));
      api_duration = days;
    } else {
      api_duration = Number(api_duration) ?? 1;
    }

    setInfos(
      {
        key_name: api_name,
        duration: api_duration
      },
      'pwg.users.api_key.create', 
      (res) => {
        pwgToaster({ text: str_api_added, icon: 'success' });
        getAllApiKeys(true);
        successApiModal(res.apikey_secret, res.auth_key);
      },
      (err) => {
        saveApiKeyEvent();
      }
    );
  }

  $('#save_apikey').on('click.apikey', handler);
  $(window).on('keydown.apikey', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      handler();
    }
  })
}

function unbindApiKeyEvents() {
  $('#api_modal').find('*').addBack().off('.apikey');
  $(window).off('.apikey');
}
