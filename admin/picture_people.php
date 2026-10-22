<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if(!defined("PHPWG_ROOT_PATH"))
{
  die('Hacking attempt!');
}

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
global $page;
check_status(ACCESS_ADMINISTRATOR);

check_input_parameter('image_id', $_GET, false, PATTERN_ID);

$curr_image = $page['image'] ?? get_image_infos($_GET['image_id'], true);
$src_image = new SrcImage($curr_image);
$image_persons = get_image_people($curr_image['id']);

// boxes are in pixels of the original as displayed, so after the EXIF
// rotation: width and height are swapped for a 90° rotation
list($displayed_width, $displayed_height) = $src_image->get_size();

$template->assign(array(
  'MEDIUM_SRC' => DerivativeImage::url(IMG_MEDIUM, $src_image),
  'FORMAT' => ($displayed_width >= $displayed_height)? 1:0,
  'IMAGE_ID' => $curr_image['id'],
  'IMG_WIDTH' => $displayed_width,
  'IMG_HEIGHT' => $displayed_height,
  "IMAGE_PERSONS" => $image_persons,
  'PWG_TOKEN' => get_pwg_token(),
));

$template->set_filename('picture_people', 'picture_people.tpl');

$template->assign_var_from_handle('ADMIN_CONTENT', 'picture_people');
?>
