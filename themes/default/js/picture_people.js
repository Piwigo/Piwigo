jQuery(function($) {
  const $avatars = $('.people-avatar[data-box]');
  if (!$avatars.length) {
    return;
  }

  const $frame = $('<div class="people-frame"><span class="people-frame-name"></span></div>').hide().appendTo('body');
  let $selected = null;

  function show_frame($avatar) {
    const img = document.getElementById('theMainImage');
    if (!img || img.hasAttribute('data-src')) {
      $frame.hide();
      return;
    }

    const rect = img.getBoundingClientRect();
    if (!rect.width) {
      $frame.hide();
      return;
    }

    const box = String($avatar.data('box')).split(',').map(Number);
    const scale = rect.width / Number($avatar.data('width'));

    $frame.css({
      left: rect.left + window.scrollX + box[0] * scale,
      top: rect.top + window.scrollY + box[1] * scale,
      width: (box[2] - box[0]) * scale,
      height: (box[3] - box[1]) * scale,
    });
    $frame.find('.people-frame-name').text($avatar.data('name'));
    $frame.show();
  }

  function refresh_frame() {
    if ($selected) {
      show_frame($selected);
    }
    else {
      $frame.hide();
    }
  }

  $avatars
    .on('mouseenter focus', function() {
      show_frame($(this));
    })
    .on('mouseleave blur', refresh_frame)
    .on('click keydown', function(e) {
      if ('keydown' == e.type && 'Enter' != e.key && ' ' != e.key) {
        return;
      }
      e.preventDefault();

      const $avatar = $(this);
      $avatars.removeClass('people-avatar-selected');

      if ($selected && $selected.is($avatar)) {
        $selected = null;
      }
      else {
        $selected = $avatar;
        $avatar.addClass('people-avatar-selected');
      }

      show_frame($avatar);
    });

  $(window).on('resize', refresh_frame);
  $('#theMainImage').on('load', refresh_frame);
});
