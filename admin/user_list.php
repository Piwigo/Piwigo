<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

/**
 * Add users and manage users list
 */

// +-----------------------------------------------------------------------+
// |                           initialization                              |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}
include_once(PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php');

// +-----------------------------------------------------------------------+
// |                              add a user                               |
// +-----------------------------------------------------------------------+

if (isset($_POST['submit_add']))
{
  $errors = register_user($_POST['login'],
                          $_POST['password'],
                          $_POST['password'],
                          '');
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(array('user_list'=>'admin/user_list.tpl'));

$base_url = add_session_id(PHPWG_ROOT_PATH.'admin.php?page=user_list');

$conf['users_page'] = 20;

if (isset($_GET['start']) and is_numeric($_GET['start']))
{
  $start = $_GET['start'];
}
else
{
  $start = 0;
}

$template->assign_vars(
  array(
    'L_AUTH_USER'=>$lang['permuser_only_private'],
    'L_GROUP_ADD_USER' => $lang['group_add_user'],
    'L_SUBMIT'=>$lang['submit'],
    'L_STATUS'=>$lang['user_status'],
    'L_USERNAME' => $lang['login'],
    'L_PASSWORD' => $lang['password'],
    'L_EMAIL' => $lang['mail_address'],
    'L_ORDER_BY' => $lang['order_by'],
    'L_ACTIONS' => $lang['actions'],
    'L_PERMISSIONS' => $lang['permissions'],
    'L_USERS_LIST' => $lang['title_liste_users'],
    
    'F_ADD_ACTION' => $base_url,
    'F_USERNAME' => @$_GET['username'],
    'F_FILTER_ACTION' => PHPWG_ROOT_PATH.'admin.php'
    ));

if (isset($_GET['id']))
{
  $template->assign_block_vars('session', array('ID' => $_GET['id']));
}

$order_by_items = array('id' => $lang['registration_date'],
                        'username' => $lang['login']);

foreach ($order_by_items as $item => $label)
{
  $selected = (isset($_GET['order_by']) and $_GET['order_by'] == $item) ?
    'selected="selected"' : '';
  $template->assign_block_vars(
    'order_by',
    array(
      'VALUE' => $item,
      'CONTENT' => $label,
      'SELECTED' => $selected
      ));
}

$direction_items = array('asc' => $lang['ascending'],
                         'desc' => $lang['descending']);

foreach ($direction_items as $item => $label)
{
  $selected = (isset($_GET['direction']) and $_GET['direction'] == $item) ?
    'selected="selected"' : '';
  $template->assign_block_vars(
    'direction',
    array(
      'VALUE' => $item,
      'CONTENT' => $label,
      'SELECTED' => $selected
      ));
}

$blockname = 'group_option';

$template->assign_block_vars(
  $blockname,
  array(
    'VALUE'=> -1,
    'CONTENT' => '------------',
    'SELECTED' => ''
    ));

$query = '
SELECT id, name
  FROM '.GROUPS_TABLE.'
;';
$result = pwg_query($query);

while ($row = mysql_fetch_array($result))
{
  $selected = (isset($_GET['group']) and $_GET['group'] == $row['id']) ?
    'selected="selected"' : '';
  $template->assign_block_vars(
    $blockname,
    array(
      'VALUE' => $row['id'],
      'CONTENT' => $row['name'],
      'SELECTED' => $selected
      ));
}

$blockname = 'status_option';

$template->assign_block_vars(
  $blockname,
  array(
    'VALUE'=> -1,
    'CONTENT' => '------------',
    'SELECTED' => ''
    ));

foreach (get_enums(USERS_TABLE, 'status') as $status)
{
  $selected = (isset($_GET['status']) and $_GET['status'] == $status) ?
    'selected="selected"' : '';
  $template->assign_block_vars(
    $blockname,
    array(
      'VALUE' => $status,
      'CONTENT' => $lang['user_status_'.$status],
      'SELECTED' => $selected
      ));
}

// +-----------------------------------------------------------------------+
// |                                 filter                                |
// +-----------------------------------------------------------------------+

$filter = array();

if (isset($_GET['username']) and !empty($_GET['username']))
{
  $username = str_replace('*', '%', $_GET['username']);
  if (function_exists('mysql_real_escape_string'))
  {
    $username = mysql_real_escape_string($username);
  }
  else
  {
    $username = mysql_escape_string($username);
  }

  if (!empty($username))
  {
    $filter['username'] = $username;
  }
}

if (isset($_GET['group'])
    and -1 != $_GET['group']
    and is_numeric($_GET['group']))
{
  $filter['group'] = $_GET['group'];
}

if (isset($_GET['status'])
    and in_array($_GET['status'], get_enums(USERS_TABLE, 'status')))
{
  $filter['status'] = $_GET['status'];
}

// +-----------------------------------------------------------------------+
// |                            navigation bar                             |
// +-----------------------------------------------------------------------+

$query = '
SELECT COUNT(DISTINCT(id))
  FROM '.USERS_TABLE.' LEFT JOIN '.USER_GROUP_TABLE.' ON id = user_id
  WHERE id != 2';
if (isset($filter['username']))
{
  $query.= '
    AND username LIKE \''.$filter['username'].'\'';
}
if (isset($filter['group']))
{
  $query.= '
    AND group_id = '.$filter['group'];
}
if (isset($filter['status']))
{
  $query.= '
    AND status = \''.$filter['status']."'";
}
$query.= '
;';
list($counter) = mysql_fetch_row(pwg_query($query));

$url = PHPWG_ROOT_PATH.'admin.php'.get_query_string_diff(array('start'));

$navbar = create_navigation_bar($url,
                                $counter,
                                $start,
                                $conf['users_page'],
                                '');

$template->assign_vars(array('NAVBAR' => $navbar));

// +-----------------------------------------------------------------------+
// |                               user list                               |
// +-----------------------------------------------------------------------+

$profile_url = PHPWG_ROOT_PATH.'admin.php?page=profile&amp;user_id=';
$perm_url = PHPWG_ROOT_PATH.'admin.php?page=user_perm&amp;user_id=';

$users = array();
$user_ids = array();
$groups_content = array();

$order_by = 'id';
if (isset($_GET['order_by'])
    and in_array($_GET['order_by'], array_keys($order_by_items)))
{
  $order_by = $_GET['order_by'];
}

$direction = 'ASC';
if (isset($_GET['direction'])
    and in_array($_GET['direction'], array_keys($direction_items)))
{
  $direction = strtoupper($_GET['direction']);
}

$query = '
SELECT id, username, mail_address, status
  FROM '.USERS_TABLE.' LEFT JOIN '.USER_GROUP_TABLE.' ON id = user_id
  WHERE id != 2';
if (isset($filter['username']))
{
  $query.= '
    AND username LIKE \''.$filter['username'].'\'';
}
if (isset($filter['group']))
{
  $query.= '
    AND group_id = '.$filter['group'];
}
if (isset($filter['status']))
{
  $query.= '
    AND status = \''.$filter['status']."'";
}
$query.= '
  ORDER BY '.$order_by.' '.$direction.'
  LIMIT '.$start.', '.$conf['users_page'].'
;';
$result = pwg_query($query);
while ($row = mysql_fetch_array($result))
{
  array_push($users, $row);
  array_push($user_ids, $row['id']);
  $user_groups[$row['id']] = array();
}

if (count($user_ids) > 0)
{
  $query = '
SELECT user_id, group_id, name
  FROM '.USER_GROUP_TABLE.' INNER JOIN '.GROUPS_TABLE.' ON group_id = id
  WHERE user_id IN ('.implode(',', $user_ids).')
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    $groups_content[$row['group_id']] = $row['name'];
    array_push($user_groups[$row['user_id']], $row['group_id']);
  }

  foreach ($users as $item)
  {
    $groups = preg_replace('/(\d+)/e',
                           "\$groups_content['$1']",
                           implode(', ', $user_groups[$item['id']]));
    
    $template->assign_block_vars(
      'user',
      array(
        'U_MOD'=>add_session_id($profile_url.$item['id']),
        'U_PERM'=>add_session_id($perm_url.$item['id']),
        'USERNAME'=>$item['username'],
        'STATUS'=>$lang['user_status_'.$item['status']],
        'EMAIL'=>isset($item['mail_address']) ? $item['mail_address'] : '',
        'GROUPS'=>$groups
        ));
  }
}

// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'user_list');
?>
