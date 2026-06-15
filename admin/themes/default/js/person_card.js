const people_tiptip_config = {
  delay: 0,
  fadeIn: 200,
  fadeOut: 200,
  edgeOffset: 3
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

  if (typeof dropdown_func === 'function') {
    dropdown_func(template, person);
  }

  if (typeof selector === 'string') {
    selector = $(selector);
  }
  selector.append(template);
}