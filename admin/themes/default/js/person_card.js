const people_tiptip_config = {
  delay: 0,
  fadeIn: 200,
  fadeOut: 200,
  edgeOffset: 3
}

/**
 * CSS that shows the face of a box, cropped from an image, in a square of
 * `size` pixels. The box is in pixels of an image of image_width x image_height,
 * src can be any derivative of the same image.
 * @returns {Object|null} null if the person has no box
 */
function people_face_css(src, person, size) {
  if (null === person.box_x1 || undefined === person.box_x1) {
    return null;
  }

  const x1 = Number(person.box_x1);
  const y1 = Number(person.box_y1);
  const x2 = Number(person.box_x2);
  const y2 = Number(person.box_y2);

  // fill the square with the box, then keep the image covering the square
  // when the box is close to the edge of the image
  const scale = size / Math.min(x2 - x1, y2 - y1);
  const width = Number(person.image_width) * scale;
  const height = Number(person.image_height) * scale;
  const left = Math.min(0, Math.max(size - width, size / 2 - (x1 + x2) / 2 * scale));
  const top = Math.min(0, Math.max(size - height, size / 2 - (y1 + y2) / 2 * scale));

  return {
    'background-image': `url("${src}")`,
    'background-repeat': 'no-repeat',
    'background-size': `${width}px ${height}px`,
    'background-position': `${left}px ${top}px`,
  };
}

function people_render_card(selector, person, index = null, dropdown_func = null) {
  const photos = Number(person.photos);

  const template = $('.template.people-card').clone();
  template.removeClass('people-hidden template');

  template.attr('id', 'person_' + person.id);
  template.find('.people-dropdown-name').text(person.name);
  template.find('.people-card-name')
    .attr('title', person.name)
    .tipTip(people_tiptip_config)
    .text(person.name);
  template.find('.people-card-lock').tipTip(people_tiptip_config);

  template.find('.people-dropdown-photos').text(
    photos === 0 ? '~' : sprintf(str_x_photos, Number(person.photos))
  );

  if (typeof index === 'number') {
    template.attr('data-index', index);
  }

  // face of the cover, the grey icon stays when the cover has no box
  const face = person.cover ? people_face_css(person.cover.src, person.cover, 95) : null;
  if (face) {
    template.find('.people-card-picture').css(face).find('i').hide();
  }

  if (typeof dropdown_func === 'function') {
    dropdown_func(template, person);
  }

  if (typeof selector === 'string') {
    selector = $(selector);
  }
  selector.append(template);
}