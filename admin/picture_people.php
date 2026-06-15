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

$template->assign(array(
  'MEDIUM_SRC' => DerivativeImage::url(IMG_MEDIUM, $src_image),
  'FORMAT' => ($curr_image['width'] >= $curr_image['height'])? 1:0,
  'PWG_TOKEN' => get_pwg_token(),
));

$template->set_filename('picture_people', 'picture_people.tpl');

$template->assign_var_from_handle('ADMIN_CONTENT', 'picture_people');
?>
