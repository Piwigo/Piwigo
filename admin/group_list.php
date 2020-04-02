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
// | tabs                                                                  |
// +-----------------------------------------------------------------------+

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

$my_base_url = get_root_url().'admin.php?page=';

$tabsheet = new tabsheet();
$tabsheet->set_id('groups');
$tabsheet->select('group_list');
$tabsheet->assign();

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
    $page['errors'][] = l10n('The name of a group must not contain " or \' or be empty.');
  }
  else
  {
    $_POST['groupname'] = strip_tags($_POST['groupname']);
  }

  if (count($page['errors']) == 0)
  {
    // is the group not already existing ?
    $query = '
SELECT COUNT(*)
  FROM `'.GROUPS_TABLE.'`
  WHERE name = \''.pwg_db_real_escape_string($_POST['groupname']).'\'
;';
    list($count) = pwg_db_fetch_row(pwg_query($query));
    if ($count != 0)
    {
      $page['errors'][] = l10n('This name is already used by another group.');
    }
  }
  if (count($page['errors']) == 0)
  {
    // creating the group
    $query = '
INSERT INTO `'.GROUPS_TABLE.'`
  (name)
  VALUES
  (\''.pwg_db_real_escape_string($_POST['groupname']).'\')
;';
    pwg_query($query);

    $page['infos'][] = l10n('group "%s" added', $_POST['groupname']);

    $inserted_id = pwg_db_insert_id(GROUPS_TABLE);
    pwg_activity('group', $inserted_id, 'add');
  }
}

// +-----------------------------------------------------------------------+
// |                             action send                               |
// +-----------------------------------------------------------------------+
if (isset($_POST['submit']) and isset($_POST['selectAction']) and isset($_POST['group_selection']))
{
  check_input_parameter('group_selection', $_POST, true, PATTERN_ID);
  // if the user tries to apply an action, it means that there is at least 1
  // photo in the selection
  $groups = $_POST['group_selection'];
  if (count($groups) == 0)
  {
    $page['errors'][] = l10n('Select at least one group');
  }

  $action = $_POST['selectAction'];

  // +
  // |rename a group
  // +

  if ($action=="rename")
  {
    // is the group not already existing ?
    $query = '
SELECT name
  FROM `'.GROUPS_TABLE.'`
;';
    $group_names = array_from_query($query, 'name');
    foreach($groups as $group)
    {
      $_POST['rename_'.$group] = strip_tags(stripslashes($_POST['rename_'.$group]));

      if (in_array($_POST['rename_'.$group], $group_names))
      {
        $page['errors'][] = $_POST['rename_'.$group].' | '.l10n('This name is already used by another group.');
      }
      elseif ( !empty($_POST['rename_'.$group.'']))
      {
        $query = '
        UPDATE `'.GROUPS_TABLE.'`
        SET name = \''.pwg_db_real_escape_string($_POST['rename_'.$group]).'\'
        WHERE id = '.$group.'
      ;';
        pwg_query($query);
        pwg_activity('group', $group, 'edit', array('action'=>$action));
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
    SELECT id, name
      FROM `'.GROUPS_TABLE.'`
      WHERE id = '.$group.'
    ;';

      $group_list = query2array($query, 'id', 'name');
      $groupids = array_keys($group_list);
      list($groupname) = array_values($group_list);

      // destruction of the group
      $query = '
    DELETE
      FROM `'.GROUPS_TABLE.'`
      WHERE id = '.$group.'
    ;';
      pwg_query($query);

      trigger_notify('delete_group', $groupids);
      pwg_activity('group', $groupids, 'delete');

      $page['infos'][] = l10n('group "%s" deleted', $groupname);
    }
  }

  // +
  // |merge groups into a new one
  // +

  if ($action=="merge" and count($groups) > 1)
  {
    $_POST['merge'] = strip_tags($_POST['merge']);

    // is the group not already existing ?
    $query = '
SELECT COUNT(*)
  FROM `'.GROUPS_TABLE.'`
  WHERE name = \''.pwg_db_real_escape_string($_POST['merge']).'\'
;';
    list($count) = pwg_db_fetch_row(pwg_query($query));
    if ($count != 0)
    {
      $page['errors'][] = l10n('This name is already used by another group.');
    }
    else
    {
      // creating the group
      $query = '
  INSERT INTO `'.GROUPS_TABLE.'`
    (name)
    VALUES
    (\''.pwg_db_real_escape_string($_POST['merge']).'\')
  ;';
      pwg_query($query);
      $query = '
      SELECT id
        FROM `'.GROUPS_TABLE.'`
        WHERE name = \''.pwg_db_real_escape_string($_POST['merge']).'\'
      ;';
      list($groupid) = pwg_db_fetch_row(pwg_query($query));
      pwg_activity('group', $groupid, 'add', array('action'=>$action, 'groups'=>implode(',', $groups)));
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
    
    $page['infos'][] = l10n('group "%s" added', $_POST['merge']);
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
      else
      {
        $_POST['duplicate_'.$group.''] = strip_tags(stripslashes($_POST['duplicate_'.$group.'']));
      }

      // is the group not already existing ?
      $query = '
  SELECT COUNT(*)
    FROM `'.GROUPS_TABLE.'`
    WHERE name = \''.pwg_db_real_escape_string($_POST['duplicate_'.$group.'']).'\'
  ;';
      list($count) = pwg_db_fetch_row(pwg_query($query));
      if ($count != 0)
      {
        $page['errors'][] = l10n('This name is already used by another group.');
        break;
      }
      // creating the group
      $query = '
  INSERT INTO `'.GROUPS_TABLE.'`
    (name)
    VALUES
    (\''.pwg_db_real_escape_string($_POST['duplicate_'.$group.'']).'\')
  ;';
      pwg_query($query);
      $query = '
      SELECT id
        FROM `'.GROUPS_TABLE.'`
        WHERE name = \''.pwg_db_real_escape_string($_POST['duplicate_'.$group.'']).'\'
      ;';
      
      list($groupid) = pwg_db_fetch_row(pwg_query($query));
      pwg_activity('group', $groupid, 'add', array('action'=>$action, 'group'=>$group));
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
  
      $page['infos'][] = l10n('group "%s" added', $_POST['duplicate_'.$group.'']);
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
      FROM `'.GROUPS_TABLE.'`
      WHERE id = '.$group.'
    ;';
      list($groupname, $is_default) = pwg_db_fetch_row(pwg_query($query));
      
      // update of the group
      $query = '
    UPDATE `'.GROUPS_TABLE.'`
      SET is_default = \''.boolean_to_string(!get_boolean($is_default)).'\'
      WHERE id = '.$group.'
    ;';
      pwg_query($query);

      pwg_activity('group', $group, 'edit', array('action'=>$action));

      $page['infos'][] = l10n('group "%s" updated', $groupname);
    }
  }
  invalidate_user_cache();
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
  FROM `'.GROUPS_TABLE.'`
  ORDER BY name ASC
;';
$result = pwg_query($query);

$admin_url = get_root_url().'admin.php?page=';
$perm_url    = $admin_url.'group_perm&amp;group_id=';
$users_url = $admin_url.'user_list&amp;group=';
$del_url     = $admin_url.'group_list&amp;delete=';
$toggle_is_default_url     = $admin_url.'group_list&amp;toggle_is_default=';

while ($row = pwg_db_fetch_assoc($result))
{
  $query = '
SELECT u.'. $conf['user_fields']['username'].' AS username
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
      'L_MEMBERS' => implode(' <span class="userSeparator">&middot;</span> ', $members),
      'MEMBERS' => l10n_dec('%d member', '%d members', count($members)),
      'U_DELETE' => $del_url.$row['id'].'&amp;pwg_token='.get_pwg_token(),
      'U_PERM' => $perm_url.$row['id'],
      'U_USERS' => $users_url.$row['id'],
      'U_ISDEFAULT' => $toggle_is_default_url.$row['id'].'&amp;pwg_token='.get_pwg_token(),
      )
    );
}

// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'group_list');

?>