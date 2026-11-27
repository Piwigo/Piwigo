jQuery(document).ready(function() {
  const form = jQuery('#setup-form');

  form.find('input[name=next]').on('click', function(e) {
    const queue = [];

    const theme = form.find('input[name=setup_theme]:checked').closest('.setup-card');
    if (theme.length && 'default' != theme.attr('data-state')) {
      queue.push(theme);
    }

    form.find('input[name="setup_plugins[]"]:checked:not(:disabled)').each(function() {
      queue.push(jQuery(this).closest('.setup-card'));
    });

    if (0 == queue.length) {
      return;
    }

    e.preventDefault();

    const total = queue.length;
    const buttons = form.find('input[type=submit]').prop('disabled', true);
    const progress = jQuery('#setup-progress').attr('max', total).val(0);
    const label = jQuery('#setup-progress-label').text('0 / ' + total);
    jQuery('.setup-progress-bar').addClass('visible');

    let done = 0;
    let failed = 0;

    function install_next() {
      if (0 == queue.length) {
        if (0 == failed) {
          jQuery('<input type="hidden" name="next" value="1">').appendTo(form);
          form.trigger('submit');
        } else {
          buttons.prop('disabled', false);
        }
        return;
      }

      const card = queue.shift();
      card.addClass('setup-card-loading');
      card.find('.setup-card-error').text('');

      jQuery.ajax({
        url: form.attr('action'),
        type: 'POST',
        dataType: 'json',
        data: {
          install_extension: card.attr('data-id'),
          type: card.attr('data-type'),
          pwg_token: form.find('input[name=pwg_token]').val()
        }
      }).done(function(data) {
        if ('ok' == data.status) {
          card.attr('data-state', 'themes' == card.attr('data-type') ? 'default' : 'active');
          card.find('input[type=checkbox]').prop('disabled', true);
        } else {
          failed++;
          card.find('.setup-card-error').text(data.message || setup_str_error);
        }
      }).fail(function() {
        failed++;
        card.find('.setup-card-error').text(setup_str_error);
      }).always(function() {
        card.removeClass('setup-card-loading');
        done++;
        progress.val(done);
        label.text(done + ' / ' + total);
        install_next();
      });
    }

    install_next();
  });
});
