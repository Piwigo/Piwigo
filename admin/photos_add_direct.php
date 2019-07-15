<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if (!defined('PHOTOS_ADD_BASE_URL'))
{
  die ("Hacking attempt!");
}

// +-----------------------------------------------------------------------+
// |                        batch management request                       |
// +-----------------------------------------------------------------------+

if (isset($_GET['batch']))
{
  check_input_parameter('batch', $_GET, false, '/^\d+(,\d+)*$/');

  $query = '
DELETE FROM '.CADDIE_TABLE.'
  WHERE user_id = '.$user['id'].'
;';
  pwg_query($query);

  $inserts = array();
  foreach (explode(',', $_GET['batch']) as $image_id)
  {
    $inserts[] = array(
      'user_id' => $user['id'],
      'element_id' => $image_id,
      );
  }
  mass_inserts(
    CADDIE_TABLE,
    array_keys($inserts[0]),
    $inserts
    );

  redirect(get_root_url().'admin.php?page=batch_manager&filter=prefilter-caddie');
}

// +-----------------------------------------------------------------------+
// |                             prepare form                              |
// +-----------------------------------------------------------------------+

include_once(PHPWG_ROOT_PATH.'admin/include/photos_add_direct_prepare.inc.php');

// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+
trigger_notify('loc_end_photo_add_direct');

$template->assign_var_from_handle('ADMIN_CONTENT', 'photos_add');
?>
