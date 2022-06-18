<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

if (!empty($_POST))
{
  check_pwg_token();
  check_input_parameter('cat_true', $_POST, true, PATTERN_ID);
  check_input_parameter('cat_false', $_POST, true, PATTERN_ID);
}

// +-----------------------------------------------------------------------+
// |                            variables init                             |
// +-----------------------------------------------------------------------+

if (!isset($_GET['group_id']))
{
  fatal_error('group_id URL parameter is missing');
}

check_input_parameter('group_id', $_GET, false, PATTERN_ID);

$page['group'] = $_GET['group_id'];

// +-----------------------------------------------------------------------+
// |                                updates                                |
// +-----------------------------------------------------------------------+

if (isset($_POST['falsify'])
    and isset($_POST['cat_true'])
    and count($_POST['cat_true']) > 0)
{
  // if you forbid access to a category, all sub-categories become
  // automatically forbidden
  $subcats = get_subcat_ids($_POST['cat_true']);
  $query = '
DELETE
  FROM '.GROUP_ACCESS_TABLE.'
  WHERE group_id = '.$page['group'].'
  AND cat_id IN ('.implode(',', $subcats).')
;';
  pwg_query($query);
}
else if (isset($_POST['trueify'])
         and isset($_POST['cat_false'])
         and count($_POST['cat_false']) > 0)
{
  $uppercats = get_uppercat_ids($_POST['cat_false']);
  $private_uppercats = array();

  $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',', $uppercats).')
  AND status = \'private\'
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    $private_uppercats[] = $row['id'];
  }

  // retrying to authorize a category which is already authorized may cause
  // an error (in SQL statement), so we need to know which categories are
  // accesible
  $authorized_ids = array();

  $query = '
SELECT cat_id
  FROM '.GROUP_ACCESS_TABLE.'
  WHERE group_id = '.$page['group'].'
;';
  $result = pwg_query($query);

  while ($row = pwg_db_fetch_assoc($result))
  {
    $authorized_ids[] = $row['cat_id'];
  }

  $inserts = array();
  $to_autorize_ids = array_diff($private_uppercats, $authorized_ids);
  foreach ($to_autorize_ids as $to_autorize_id)
  {
    $inserts[] = array(
      'group_id' => $page['group'],
      'cat_id' => $to_autorize_id
      );
  }

  mass_inserts(GROUP_ACCESS_TABLE, array('group_id','cat_id'), $inserts);
  invalidate_user_cache();
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(
  array(
    'group_perm' => 'group_perm.tpl',
    'double_select' => 'double_select.tpl'
    )
  );

$template->assign(
  array(
    'TITLE' =>
      l10n(
        'Manage permissions for group "%s"',
        get_groupname($page['group'])
        ),
    'L_CAT_OPTIONS_TRUE'=>l10n('Authorized'),
    'L_CAT_OPTIONS_FALSE'=>l10n('Forbidden'),

    'F_ACTION' =>
        get_root_url().
        'admin.php?page=group_perm&amp;group_id='.
        $page['group']
    )
  );

// only private categories are listed
$query_true = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.' INNER JOIN '.GROUP_ACCESS_TABLE.' ON cat_id = id
  WHERE status = \'private\'
    AND group_id = '.$page['group'].'
;';
display_select_cat_wrapper($query_true,array(),'category_option_true');

$result = pwg_query($query_true);
$authorized_ids = array();
while ($row = pwg_db_fetch_assoc($result))
{
  $authorized_ids[] = $row['id'];
}

$query_false = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE status = \'private\'';
if (count($authorized_ids) > 0)
{
  $query_false.= '
    AND id NOT IN ('.implode(',', $authorized_ids).')';
}
$query_false.= '
;';
display_select_cat_wrapper($query_false,array(),'category_option_false');

$template->assign('PWG_TOKEN', get_pwg_token());

// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('DOUBLE_SELECT', 'double_select');
$template->assign_var_from_handle('ADMIN_CONTENT', 'group_perm');

?>
