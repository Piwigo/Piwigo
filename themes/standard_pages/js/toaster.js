function pwgToaster(info) {
  if (!info.text || !info.icon) {
    console.log('set info.text or info.icon');
    return;
  }

  if (typeof info.text !== 'string') {
    console.log('info.text is not a string');
    return;
  }

  if (info.icon !== 'success' && info.icon !== 'error') {
    console.log('info.icon must be success or error');
    return;
  }

  const template = $('#toast_template').clone();

  template.find('.toast_text').html(info.text);
  template.find('.toast_icon').addClass(info.icon === 'success' ? 'icon-ok' : 'icon-cancel');
  template.addClass(info.icon === 'success' ? info.icon : 'error');

  template.removeClass('template-pwg-toaster');
  template.appendTo('#pwg_toaster');

  const time = info.time ?? 3600;
  setTimeout(() => {
    template.fadeOut(() => {
      template.remove();
    })
  }, time);
}