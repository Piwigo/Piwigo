<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2012 Piwigo Team                  http://piwigo.org |
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

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

if (!empty($_POST) or isset($_GET['delete']) or isset($_GET['toggle_is_default']))
{
  check_pwg_token();
}
// +-----------------------------------------------------------------------+
// |                              add a group                              |
// +-----------------------------------------------------------------------+

if (isset($_POST['submit_add']))
{
  if (empty($_POST['groupname']))
  {
    array_push($page['errors'], l10n('The name of a group must not contain " or \' or be empty.'));
  }
  if (count($page['errors']) == 0)
  {
    // is the group not already existing ?
    $query = '
SELECT COUNT(*)
  FROM '.GROUPS_TABLE.'
  WHERE name = \''.$_POST['groupname'].'\'
;';
    list($count) = pwg_db_fetch_row(pwg_query($query));
    if ($count != 0)
    {
      array_push($page['errors'], l10n('This name is already used by another group.'));
    }
  }
  if (count($page['errors']) == 0)
  {
    // creating the group
    $query = '
INSERT INTO '.GROUPS_TABLE.'
  (name)
  VALUES
  (\''.pwg_db_real_escape_string($_POST['groupname']).'\')
;';
    pwg_query($query);

    array_push(
      $page['infos'],
      sprintf(l10n('group "%s" added'), $_POST['groupname'])
      );
  }
}

// +-----------------------------------------------------------------------+
// |                             action send                               |
// +-----------------------------------------------------------------------+
if (isset($_POST['submit']) and isset($_POST['selectAction']) and isset($_POST['group_selection']))
{
  // if the user tries to apply an action, it means that there is at least 1
  // photo in the selection
  $groups = $_POST['group_selection'];
  if (count($groups) == 0)
  {
    array_push($page['errors'], l10n('Select at least one group'));
  }

  $action = $_POST['selectAction'];

  // +
  // |rename a group
  // +

  if ($action=="rename")
  {
    foreach($groups as $group)
    {
      if ( !empty($_POST['rename_'.$group.'']) )
      {
        $query = '
        UPDATE '.GROUPS_TABLE.'
        SET name = \''.$_POST['rename_'.$group.''].'\'
        WHERE id = '.$group.'
      ;';
        pwg_query($query);
      }
    }
  }

  // +
  // |delete a group
  // +

  if ($action=="delete" and isset($_POST['confirm_deletion']) and $_POST['confirm_deletion'])
  {
    foreach($groups as $group)
    {
        // destruction of the access linked to the group
      $query = '
    DELETE
      FROM '.GROUP_ACCESS_TABLE.'
      WHERE group_id = '.$group.'
    ;';
      pwg_query($query);
      
      // destruction of the users links for this group
      $query = '
    DELETE
      FROM '.USER_GROUP_TABLE.'
      WHERE group_id = '.$group.'
    ;';
      pwg_query($query);
    
      $query = '
    SELECT name
      FROM '.GROUPS_TABLE.'
      WHERE id = '.$group.'
    ;';
      list($groupname) = pwg_db_fetch_row(pwg_query($query));
      
      // destruction of the group
      $query = '
    DELETE
      FROM '.GROUPS_TABLE.'
      WHERE id = '.$group.'
    ;';
      pwg_query($query);
    
      array_push(
        $page['infos'],
        sprintf(l10n('group "%s" deleted'), $groupname)
        );
    }
  }

  // +
  // |merge groups into a new one
  // +

  if ($action=="merge" )
  {
    // is the group not already existing ?
    $query = '
SELECT COUNT(*)
  FROM '.GROUPS_TABLE.'
  WHERE name = \''.pwg_db_real_escape_string($_POST['merge']).'\'
;';
    list($count) = pwg_db_fetch_row(pwg_query($query));
    if ($count != 0)
    {
      array_push($page['errors'], l10n('This name is already used by another group.'));
    }
    else
    {
      // creating the group
      $query = '
  INSERT INTO '.GROUPS_TABLE.'
    (name)
    VALUES
    (\''.pwg_db_real_escape_string($_POST['merge']).'\')
  ;';
      pwg_query($query);
      $query = '
      SELECT id
        FROM '.GROUPS_TABLE.'
        WHERE name = \''.pwg_db_real_escape_string($_POST['merge']).'\'
      ;';
      list($groupid) = pwg_db_fetch_row(pwg_query($query));
    }
    $grp_access = array();
    $usr_grp = array();
    foreach($groups as $group)
    {
      $query = '
    SELECT *
      FROM '.GROUP_ACCESS_TABLE.'
      WHERE group_id = '.$group.'
    ;';
      $res=pwg_query($query);
      while ($row = pwg_db_fetch_assoc($res))
      {
        $new_grp_access= array(
          'cat_id' => $row['cat_id'],
          'group_id' => $groupid
        );
        if (!in_array($new_grp_access,$grp_access))
        {
          $grp_access[]=$new_grp_access;
        }
      }

      $query = '
    SELECT *
      FROM '.USER_GROUP_TABLE.'
      WHERE group_id = '.$group.'
    ;';
      $res=pwg_query($query);
      while ($row = pwg_db_fetch_assoc($res))
      {
        $new_usr_grp= array(
          'user_id' => $row['user_id'],
          'group_id' => $groupid
        );
        if (!in_array($new_usr_grp,$usr_grp))
        {
          $usr_grp[]=$new_usr_grp;
        }
      }
    }
    mass_inserts(USER_GROUP_TABLE, array('user_id','group_id'), $usr_grp);
    mass_inserts(GROUP_ACCESS_TABLE, array('group_id','cat_id'), $grp_access);
    array_push(
      $page['infos'],
      sprintf(l10n('group "%s" added'), $_POST['merge'])
      );
  }
  
  // +
  // |duplicate a group
  // +

  if ($action=="duplicate" )
  {
    foreach($groups as $group)
    {
      if ( empty($_POST['duplicate_'.$group.'']) )
      {
        break;
      }
      // is the group not already existing ?
      $query = '
  SELECT COUNT(*)
    FROM '.GROUPS_TABLE.'
    WHERE name = \''.pwg_db_real_escape_string($_POST['duplicate_'.$group.'']).'\'
  ;';
      list($count) = pwg_db_fetch_row(pwg_query($query));
      if ($count != 0)
      {
        array_push($page['errors'], l10n('This name is already used by another group.'));
        break;
      }
      // creating the group
      $query = '
  INSERT INTO '.GROUPS_TABLE.'
    (name)
    VALUES
    (\''.pwg_db_real_escape_string($_POST['duplicate_'.$group.'']).'\')
  ;';
      pwg_query($query);
      $query = '
      SELECT id
        FROM '.GROUPS_TABLE.'
        WHERE name = \''.pwg_db_real_escape_string($_POST['duplicate_'.$group.'']).'\'
      ;';
      
      list($groupid) = pwg_db_fetch_row(pwg_query($query));
      $query = '
    SELECT *
      FROM '.GROUP_ACCESS_TABLE.'
      WHERE group_id = '.$group.'
    ;';
      $grp_access = array();
      $res=pwg_query($query);
      while ($row = pwg_db_fetch_assoc($res))
      {
          $grp_access[] = array(
            'cat_id' => $row['cat_id'],
            'group_id' => $groupid
          );
      }
      mass_inserts(GROUP_ACCESS_TABLE, array('group_id','cat_id'), $grp_access);

      $query = '
    SELECT *
      FROM '.USER_GROUP_TABLE.'
      WHERE group_id = '.$group.'
    ;';
      $usr_grp = array();
      $res=pwg_query($query);
      while ($row = pwg_db_fetch_assoc($res))
      {
          $usr_grp[] = array(
            'user_id' => $row['user_id'],
            'group_id' => $groupid
          );
      }
      mass_inserts(USER_GROUP_TABLE, array('user_id','group_id'), $usr_grp);
  
      array_push(
        $page['infos'],
        sprintf(l10n('group "%s" added'), $_POST['duplicate_'.$group.''])
        );
    }
  }


  // +
  // | toggle_default
  // +
  
  if ($action=="toggle_default")
  {
    foreach($groups as $group)
    {
      $query = '
    SELECT name, is_default
      FROM '.GROUPS_TABLE.'
      WHERE id = '.$group.'
    ;';
      list($groupname, $is_default) = pwg_db_fetch_row(pwg_query($query));
      
      // update of the group
      $query = '
    UPDATE '.GROUPS_TABLE.'
      SET is_default = \''.boolean_to_string(!get_boolean($is_default)).'\'
      WHERE id = '.$group.'
    ;';
      pwg_query($query);
    
      array_push(
        $page['infos'],
        sprintf(l10n('group "%s" updated'), $groupname)
        );
    }
  }
}
// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(array('group_list' => 'group_list.tpl'));

$template->assign(
  array(
    'F_ADD_ACTION' => get_root_url().'admin.php?page=group_list',
    'U_HELP' => get_root_url().'admin/popuphelp.php?page=group_list',
    'PWG_TOKEN' => get_pwg_token(),
    )
  );

// +-----------------------------------------------------------------------+
// |                              group list                               |
// +-----------------------------------------------------------------------+

$query = '
SELECT id, name, is_default
  FROM '.GROUPS_TABLE.'
  ORDER BY name ASC
;';
$result = pwg_query($query);

$admin_url = get_root_url().'admin.php?page=';
$perm_url    = $admin_url.'group_perm&amp;group_id=';
$del_url     = $admin_url.'group_list&amp;delete=';
$members_url = $admin_url.'user_list&amp;group=';
$toggle_is_default_url     = $admin_url.'group_list&amp;toggle_is_default=';

while ($row = pwg_db_fetch_assoc($result))
{
  $query = '
SELECT username
  FROM '.USERS_TABLE.' AS u
  INNER JOIN '.USER_GROUP_TABLE.' AS ug
    ON u.'.$conf['user_fields']['id'].' = ug.user_id
  WHERE ug.group_id = '.$row['id'].'
;';
  $members=array();
  $res=pwg_query($query);
  while ($us= pwg_db_fetch_assoc($res))
  {
    $members[]=$us['username'];
  }
  $template->append(
    'groups',
    array(
      'NAME' => $row['name'],
      'ID' => $row['id'],
      'IS_DEFAULT' => (get_boolean($row['is_default']) ? ' ['.l10n('default').']' : ''),
      'NB_MEMBERS' => count($members),
      'L_MEMBERS' => implode(' - ', $members),
      'MEMBERS' => l10n_dec('%d member', '%d members', count($members)),
      'U_MEMBERS' => $members_url.$row['id'],
      'U_DELETE' => $del_url.$row['id'].'&amp;pwg_token='.get_pwg_token(),
      'U_PERM' => $perm_url.$row['id'],
      'U_ISDEFAULT' => $toggle_is_default_url.$row['id'].'&amp;pwg_token='.get_pwg_token(),
      )
    );
}

// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'group_list');

?>