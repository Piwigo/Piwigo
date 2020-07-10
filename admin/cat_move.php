<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// |                               functions                               |
// +-----------------------------------------------------------------------+


// +-----------------------------------------------------------------------+
// |                          categories movement                          |
// +-----------------------------------------------------------------------+

if (isset($_POST['submit']))
{
  if (count($_POST['selection']) > 0)
  {
    check_input_parameter('selection', $_POST, true, PATTERN_ID);
    check_input_parameter('parent', $_POST, false, PATTERN_ID);

    move_categories($_POST['selection'], $_POST['parent']);
  }
  else
  {
    $page['errors'][] = l10n('Select at least one album');
  }
}

// +-----------------------------------------------------------------------+
// |                       template initialization                         |
// +-----------------------------------------------------------------------+
$template->set_filename('cat_move', 'cat_move.tpl');

$template->assign(
  array(
    'U_HELP' => get_root_url().'admin/popuphelp.php?page=cat_move',
    'F_ACTION' => get_root_url().'admin.php?page=cat_move',
    )
  );
  
// +-----------------------------------------------------------------------+
// | tabs                                                                  |
// +-----------------------------------------------------------------------+

$page['tab'] = 'move';
include(PHPWG_ROOT_PATH.'admin/include/albums_tab.inc.php');

// +-----------------------------------------------------------------------+
// |                          Album display                                |
// +-----------------------------------------------------------------------+

$query = '
SELECT id,name,global_rank,status
  FROM '.CATEGORIES_TABLE.'
;';

$allAlbum = query2array($query);
$sortedAlbums = array();

foreach ($allAlbum as $album) {
  $parents = explode('.',$album['global_rank']);
  $the_place = &$sortedAlbums[intval($parents[0])];
  for ($i=1; $i < count($parents); $i++) { 
    $the_place = &$the_place['children'][intval($parents[$i])];
  }
  $the_place['name'] = $album['name'];
  $the_place['status'] = $album['status'];
  $the_place['id'] = $album['id'];
}

$template->assign('album_data', $sortedAlbums);
$template->assign('PWG_TOKEN', get_pwg_token());
// +-----------------------------------------------------------------------+
// |                          sending html code                            |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'cat_move');
?>
