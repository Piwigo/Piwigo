jQuery(function($) {
  const $people = $('.people-person');
  if (!$people.length) {
    return;
  }

  const $frame = $('<div class="people-frame"><span class="people-frame-name"></span></div>').hide().appendTo('body');
  let $selected = null;

  function show_frame($person) {
    const img = document.getElementById('theMainImage');
    if (!$person || !$person.data('box') || !img || img.hasAttribute('data-src')) {
      $frame.hide();
      return;
    }

    const rect = img.getBoundingClientRect();
    if (!rect.width) {
      $frame.hide();
      return;
    }

    const box = String($person.data('box')).split(',').map(Number);
    const scale = rect.width / Number($person.data('width'));

    $frame.css({
      left: rect.left + window.scrollX + box[0] * scale,
      top: rect.top + window.scrollY + box[1] * scale,
      width: (box[2] - box[0]) * scale,
      height: (box[3] - box[1]) * scale,
    });
    $frame.find('.people-frame-name').text($person.data('name'));
    $frame.show();
  }

  function refresh_frame() {
    show_frame($selected);
  }

  function select_person($person) {
    $people.removeClass('people-person-selected');
    $selected = $person;
    if ($selected) {
      $selected.addClass('people-person-selected');
    }
    refresh_frame();
  }

  $people
    .on('mouseenter', function() {
      show_frame($(this));
    })
    .on('mouseleave', refresh_frame);

  $people.find('.people-avatar').on('click', function(e) {
    if (e.ctrlKey || e.metaKey || e.shiftKey) {
      return;
    }
    e.preventDefault();

    const $person = $(this).closest('.people-person');
    select_person($selected && $selected.is($person) ? null : $person);
    if (e.detail) {
      $(this).trigger('blur');
    }
  });

  $(document).on('click', function(e) {
    if ($selected && !$(e.target).closest('.people-person').length) {
      select_person(null);
    }
  });

  $(window).on('resize', refresh_frame);
  $('#theMainImage').on('load', refresh_frame);
});
