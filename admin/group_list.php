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

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}
include_once( PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php' );

// +-----------------------------------------------------------------------+
// |                             delete a group                            |
// +-----------------------------------------------------------------------+

if (isset($_GET['delete']) and is_numeric($_GET['delete']))
{
  // destruction of the access linked to the group
  $query = '
DELETE
  FROM '.GROUP_ACCESS_TABLE.'
  WHERE group_id = '.$_GET['delete'].'
;';
  pwg_query($query);
  
  // destruction of the users links for this group
  $query = '
DELETE
  FROM '.USER_GROUP_TABLE.'
  WHERE group_id = '.$_GET['delete'].'
;';
  pwg_query($query);

  $query = '
SELECT name
  FROM '.GROUPS_TABLE.'
  WHERE id = '.$_GET['delete'].'
;';
  list($groupname) = mysql_fetch_row(pwg_query($query));
  
  // destruction of the group
  $query = '
DELETE
  FROM '.GROUPS_TABLE.'
  WHERE id = '.$_GET['delete'].'
;';
  pwg_query($query);

  array_push(
    $page['infos'],
    sprintf(l10n('group "%s" deleted'), $groupname)
    );
}

// +-----------------------------------------------------------------------+
// |                              add a group                              |
// +-----------------------------------------------------------------------+

if (isset($_POST['submit_add']))
{
  if (empty($_POST['groupname']))
  {
    array_push($page['errors'], $lang['group_add_error1']);
  }
  if (count($page['errors']) == 0)
  {
    // is the group not already existing ?
    $query = '
SELECT COUNT(*)
  FROM '.GROUPS_TABLE.'
  WHERE name = \''.$_POST['groupname'].'\'
;';
    list($count) = mysql_fetch_row(pwg_query($query));
    if ($count != 0)
    {
      array_push($page['errors'], $lang['group_add_error2']);
    }
  }
  if (count($page['errors']) == 0)
  {
    // creating the group
    $query = '
INSERT INTO '.GROUPS_TABLE.'
  (name)
  VALUES
  (\''.mysql_escape_string($_POST['groupname']).'\')
;';
    pwg_query($query);

    array_push(
      $page['infos'],
      sprintf(l10n('group "%s" added'), $_POST['groupname'])
      );
  }
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(array('group_list' => 'admin/group_list.tpl'));

$template->assign_vars(
  array(
    'F_ADD_ACTION' => PHPWG_ROOT_PATH.'admin.php?page=group_list'
    )
  );

// +-----------------------------------------------------------------------+
// |                              group list                               |
// +-----------------------------------------------------------------------+

$query = '
SELECT id, name
  FROM '.GROUPS_TABLE.'
  ORDER BY id ASC
;';
$result = pwg_query($query);

$admin_url = PHPWG_ROOT_PATH.'admin.php?page=';
$perm_url    = $admin_url.'group_perm&amp;group_id=';
$del_url     = $admin_url.'group_list&amp;delete=';
$members_url = $admin_url.'user_list&amp;group=';

$num = 0;
while ($row = mysql_fetch_array($result))
{
  $query = '
SELECT COUNT(*)
  FROM '.USER_GROUP_TABLE.'
  WHERE group_id = '.$row['id'].'
;';
  list($counter) = mysql_fetch_row(pwg_query($query));
  
  $template->assign_block_vars(
    'group',
    array(
      'CLASS' => ($num++ % 2 == 1) ? 'row2' : 'row1',
      'NAME' => $row['name'],
      'MEMBERS' => sprintf(l10n('%d members'), $counter),
      'U_MEMBERS' => $members_url.$row['id'],
      'U_DELETE' => $del_url.$row['id'],
      'U_PERM' => $perm_url.$row['id']
      )
    );
}

// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'group_list');

?>
