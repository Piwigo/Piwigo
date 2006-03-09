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
  list($status) = mysql_fetch_array(pwg_query($query));
  
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
  LIMIT 0,1
;';

  list($page['cat']) = mysql_fetch_array(pwg_query($query));
}

// +-----------------------------------------------------------------------+
// |                           form submission                             |
// +-----------------------------------------------------------------------+

if (isset($_POST) and false)
{
  echo '<pre>';
  print_r($_POST);
  echo '</pre>';
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
  $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',', get_uppercat_ids(array($page['cat']))).')
  AND status = \'private\'
;';
  $private_uppercats = array_from_query($query, 'id');

  // We must not reinsert already existing lines in group_access table
  $granteds = array();
  foreach ($private_uppercats as $cat_id)
  {
    $granteds[$cat_id] = array();
  }
  
  $query = '
SELECT group_id, cat_id
  FROM '.GROUP_ACCESS_TABLE.'
  WHERE cat_id IN ('.implode(',', $private_uppercats).')
    AND group_id IN ('.implode(',', $_POST['grant_groups']).')
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    array_push($granteds[$row['cat_id']], $row['group_id']);
  }

  $inserts = array();
  
  foreach ($private_uppercats as $cat_id)
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
  $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',', get_uppercat_ids(array($page['cat']))).')
  AND status = \'private\'
;';
  $private_uppercats = array_from_query($query, 'id');

  // We must not reinsert already existing lines in user_access table
  $granteds = array();
  foreach ($private_uppercats as $cat_id)
  {
    $granteds[$cat_id] = array();
  }
  
  $query = '
SELECT user_id, cat_id
  FROM '.USER_ACCESS_TABLE.'
  WHERE cat_id IN ('.implode(',', $private_uppercats).')
    AND user_id IN ('.implode(',', $_POST['grant_users']).')
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    array_push($granteds[$row['cat_id']], $row['user_id']);
  }

  $inserts = array();
  
  foreach ($private_uppercats as $cat_id)
  {
    $user_ids = array_diff($_POST['grant_users'], $granteds[$cat_id]);
    foreach ($user_ids as $user_id)
    {
      array_push($inserts, array('user_id' => $user_id,
                                 'cat_id' => $cat_id));
    }
  }

  mass_inserts(USER_ACCESS_TABLE, array('user_id','cat_id'), $inserts);
}

// +-----------------------------------------------------------------------+
// |                       template initialization                         |
// +-----------------------------------------------------------------------+

$template->set_filenames(array('cat_perm'=>'admin/cat_perm.tpl'));

$template->assign_vars(
  array(
    'CATEGORIES_NAV' =>
      get_cat_display_name_from_id(
        $page['cat'],
        'admin.php?page=cat_modify&amp;cat_id='
        ),
    'U_HELP' => PHPWG_ROOT_PATH.'/popuphelp.php?page=cat_perm',
    'F_ACTION' => PHPWG_ROOT_PATH.'admin.php?page=cat_perm&amp;cat='.$page['cat']
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
;';
$result = pwg_query($query);

while ($row = mysql_fetch_array($result))
{
  $groups[$row['id']] = $row['name'];
}

$query = '
SELECT group_id
  FROM '.GROUP_ACCESS_TABLE.'
  WHERE cat_id = '.$page['cat'].'
;';
$group_granted_ids = array_from_query($query, 'group_id');

// groups granted to access the category
foreach ($group_granted_ids as $group_id)
{
  $template->assign_block_vars(
    'group_granted',
    array(
      'NAME'=>$groups[$group_id],
      'ID'=>$group_id
      )
    );
}

// groups denied
foreach (array_diff(array_keys($groups), $group_granted_ids) as $group_id)
{
  $template->assign_block_vars(
    'group_denied',
    array(
      'NAME'=>$groups[$group_id],
      'ID'=>$group_id
      )
    );
}

// users...
$users = array();

$query = '
SELECT '.$conf['user_fields']['id'].' AS id,
       '.$conf['user_fields']['username'].' AS username
  FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['id'].' != '.$conf['guest_id'].'
;';
$result = pwg_query($query);
while($row = mysql_fetch_array($result))
{
  $users[$row['id']] = $row['username'];
}

$query = '
SELECT user_id
  FROM '.USER_ACCESS_TABLE.'
  WHERE cat_id = '.$page['cat'].'
;';
$user_granted_direct_ids = array_from_query($query, 'user_id');

foreach ($user_granted_direct_ids as $user_id)
{
  $template->assign_block_vars(
    'user_granted',
    array(
      'NAME'=>$users[$user_id],
      'ID'=>$user_id
      )
    );
}

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
  while ($row = mysql_fetch_array($result))
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
  
  foreach ($user_granted_indirect_ids as $user_id)
  {
    $group = '';
    
    foreach ($granted_groups as $group_id => $group_users)
    {
      if (in_array($user_id, $group_users))
      {
        $group = $groups[$group_id];
        break;
      }
    }
    
    $template->assign_block_vars(
      'user_granted_indirect',
      array(
        'NAME'=>$users[$user_id],
        'GROUP'=>$group
        )
      );
  }
}

$user_denied_ids = array_diff(array_keys($users),
                              $user_granted_indirect_ids,
                              $user_granted_direct_ids);

foreach ($user_denied_ids as $user_id)
{
  $template->assign_block_vars(
    'user_denied',
    array(
      'NAME'=>$users[$user_id],
      'ID'=>$user_id
      )
    );
}


// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+
$template->assign_var_from_handle('ADMIN_CONTENT', 'cat_perm');
?>
