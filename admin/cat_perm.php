<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
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

if (!defined('PHPWG_ROOT_PATH'))
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// |                       variable initialization                         |
// +-----------------------------------------------------------------------+

// if the category is not correct (not numeric, not private)
if (isset($_GET['cat']) and is_numeric($_GET['cat']))
{
  $query = '
SELECT status
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$_GET['cat'].'
;';
  list($status) = pwg_db_fetch_row(pwg_query($query));
  
  if ('private' == $status)
  {
    $page['cat'] = $_GET['cat'];
  }
}

if (!isset($page['cat']))
{
  $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE status = \'private\'
  LIMIT 1
;';

  list($page['cat']) = pwg_db_fetch_row(pwg_query($query));
}

// +-----------------------------------------------------------------------+
// |                           form submission                             |
// +-----------------------------------------------------------------------+
if (isset($_POST['deny_groups_submit']) or isset($_POST['grant_groups_submit']) or isset($_POST['deny_users_submit']) or isset($_POST['grant_users_submit']) )
{
  check_pwg_token();
}

if (isset($_POST['deny_groups_submit'])
         and isset($_POST['deny_groups'])
         and count($_POST['deny_groups']) > 0)
{
  // if you forbid access to a category, all sub-categories become
  // automatically forbidden
  $query = '
DELETE
  FROM '.GROUP_ACCESS_TABLE.'
  WHERE group_id IN ('.implode(',', $_POST['deny_groups']).')
    AND cat_id IN ('.implode(',', get_subcat_ids(array($page['cat']))).')
;';
  pwg_query($query);
}
else if (isset($_POST['grant_groups_submit'])
         and isset($_POST['grant_groups'])
         and count($_POST['grant_groups']) > 0)
{
  $cat_ids = (isset($_POST['apply_on_sub'])) ? implode(',', get_subcat_ids(array($page['cat']))).",".implode(',', get_uppercat_ids(array($page['cat']))) : implode(',', get_uppercat_ids(array($page['cat'])));

  $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.$cat_ids.')
  AND status = \'private\'
;';
  $private_cats = array_from_query($query, 'id');

  // We must not reinsert already existing lines in group_access table
  $granteds = array();
  foreach ($private_cats as $cat_id)
  {
    $granteds[$cat_id] = array();
  }
  
  $query = '
SELECT group_id, cat_id
  FROM '.GROUP_ACCESS_TABLE.'
  WHERE cat_id IN ('.implode(',', $private_cats).')
    AND group_id IN ('.implode(',', $_POST['grant_groups']).')
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    array_push($granteds[$row['cat_id']], $row['group_id']);
  }

  $inserts = array();
  
  foreach ($private_cats as $cat_id)
  {
    $group_ids = array_diff($_POST['grant_groups'], $granteds[$cat_id]);
    foreach ($group_ids as $group_id)
    {
      array_push($inserts, array('group_id' => $group_id,
                                 'cat_id' => $cat_id));
    }
  }

  mass_inserts(GROUP_ACCESS_TABLE, array('group_id','cat_id'), $inserts);
}
else if (isset($_POST['deny_users_submit'])
         and isset($_POST['deny_users'])
         and count($_POST['deny_users']) > 0)
{
  // if you forbid access to a category, all sub-categories become
  // automatically forbidden
  $query = '
DELETE
  FROM '.USER_ACCESS_TABLE.'
  WHERE user_id IN ('.implode(',', $_POST['deny_users']).')
    AND cat_id IN ('.implode(',', get_subcat_ids(array($page['cat']))).')
;';
  pwg_query($query);
}
else if (isset($_POST['grant_users_submit'])
         and isset($_POST['grant_users'])
         and count($_POST['grant_users']) > 0)
{
  add_permission_on_category($page['cat'], $_POST['grant_users']);
}

// +-----------------------------------------------------------------------+
// |                       template initialization                         |
// +-----------------------------------------------------------------------+

$template->set_filename('cat_perm', 'cat_perm.tpl');

$template->assign(
  array(
    'CATEGORIES_NAV' =>
      get_cat_display_name_from_id(
        $page['cat'],
        'admin.php?page=cat_modify&amp;cat_id='
        ),
    'U_HELP' => get_root_url().'admin/popuphelp.php?page=cat_perm',
    'F_ACTION' => get_root_url().'admin.php?page=cat_perm&amp;cat='.$page['cat']
    )
  );

// +-----------------------------------------------------------------------+
// |                          form construction                            |
// +-----------------------------------------------------------------------+

// groups denied are the groups not granted. So we need to find all groups
// minus groups granted to find groups denied.

$groups = array();

$query = '
SELECT id, name
  FROM '.GROUPS_TABLE.'
  ORDER BY name ASC
;';
$groups = simple_hash_from_query($query, 'id', 'name');
$template->assign('all_groups', $groups);

// groups granted to access the category
$query = '
SELECT group_id
  FROM '.GROUP_ACCESS_TABLE.'
  WHERE cat_id = '.$page['cat'].'
;';
$group_granted_ids = array_from_query($query, 'group_id');
$group_granted_ids = order_by_name($group_granted_ids, $groups);
$template->assign('group_granted_ids', $group_granted_ids);


// groups denied
$template->assign('group_denied_ids',
    order_by_name(array_diff(array_keys($groups), $group_granted_ids), $groups)
  );

// users...
$users = array();

$query = '
SELECT '.$conf['user_fields']['id'].' AS id,
       '.$conf['user_fields']['username'].' AS username
  FROM '.USERS_TABLE.'
;';
$users = simple_hash_from_query($query, 'id', 'username');
$template->assign('all_users', $users);


$query = '
SELECT user_id
  FROM '.USER_ACCESS_TABLE.'
  WHERE cat_id = '.$page['cat'].'
;';
$user_granted_direct_ids = array_from_query($query, 'user_id');
$user_granted_direct_ids = order_by_name($user_granted_direct_ids, $users);
$template->assign('user_granted_direct_ids', $user_granted_direct_ids);



$user_granted_indirect_ids = array();
if (count($group_granted_ids) > 0)
{
  $granted_groups = array();

  $query = '
SELECT user_id, group_id
  FROM '.USER_GROUP_TABLE.'
  WHERE group_id IN ('.implode(',', $group_granted_ids).') 
';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    if (!isset($granted_groups[$row['group_id']]))
    {
      $granted_groups[$row['group_id']] = array();
    }
    array_push($granted_groups[$row['group_id']], $row['user_id']);
  }

  $user_granted_by_group_ids = array();

  foreach ($granted_groups as $group_users)
  {
    $user_granted_by_group_ids = array_merge($user_granted_by_group_ids,
                                             $group_users);
  }
  $user_granted_by_group_ids = array_unique($user_granted_by_group_ids);
  
  
  $user_granted_indirect_ids = array_diff($user_granted_by_group_ids,
                                          $user_granted_direct_ids);
  $user_granted_indirect_ids = 
    order_by_name($user_granted_indirect_ids, $users);  
  foreach ($user_granted_indirect_ids as $user_id)
  {
    foreach ($granted_groups as $group_id => $group_users)
    {
      if (in_array($user_id, $group_users))
      {
        $template->append(
          'user_granted_indirects',
          array(
            'USER'=>$users[$user_id],
            'GROUP'=>$groups[$group_id]
            )
          );
        break;
      }
    }
  }
}

$user_denied_ids = array_diff(array_keys($users),
                              $user_granted_indirect_ids,
                              $user_granted_direct_ids);
$user_denied_ids = order_by_name($user_denied_ids, $users);
$template->assign('user_denied_ids', $user_denied_ids);


// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+
$template->assign(array('PWG_TOKEN' => get_pwg_token()));

$template->assign_var_from_handle('ADMIN_CONTENT', 'cat_perm');
?>
