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
// |                              functions                                |
// +-----------------------------------------------------------------------+

/**
 * returns a list of users depending on page filters (in $_GET)
 *
 * Each user comes with his related informations : id, username, mail
 * address, list of groups.
 *
 * @return array
 */
function get_filtered_user_list()
{
  global $conf, $page;

  $users = array();
  
  // filter
  $filter = array();
  
  if (isset($_GET['username']) and !empty($_GET['username']))
  {
    $username = str_replace('*', '%', $_GET['username']);
    if (function_exists('mysql_real_escape_string'))
    {
      $filter['username'] = mysql_real_escape_string($username);
    }
    else
    {
      $filter['username'] = mysql_escape_string($username);
    }
  }

  if (isset($_GET['group'])
      and -1 != $_GET['group']
      and is_numeric($_GET['group']))
  {
    $filter['group'] = $_GET['group'];
  }

  if (isset($_GET['status'])
      and in_array($_GET['status'], get_enums(USER_INFOS_TABLE, 'status')))
  {
    $filter['status'] = $_GET['status'];
  }

  // how to order the list?
  $order_by = 'id';
  if (isset($_GET['order_by'])
      and in_array($_GET['order_by'], array_keys($page['order_by_items'])))
  {
    $order_by = $_GET['order_by'];
  }
  
  $direction = 'ASC';
  if (isset($_GET['direction'])
      and in_array($_GET['direction'], array_keys($page['direction_items'])))
  {
    $direction = strtoupper($_GET['direction']);
  }

  // search users depending on filters and order
  $query = '
SELECT DISTINCT u.'.$conf['user_fields']['id'].' AS id,
                u.'.$conf['user_fields']['username'].' AS username,
                u.'.$conf['user_fields']['email'].' AS email,
                ui.status,
                ui.adviser,
                ui.enabled_high
  FROM '.USERS_TABLE.' AS u
    INNER JOIN '.USER_INFOS_TABLE.' AS ui
      ON u.'.$conf['user_fields']['id'].' = ui.user_id
    LEFT JOIN '.USER_GROUP_TABLE.' AS ug
      ON u.'.$conf['user_fields']['id'].' = ug.user_id
  WHERE u.'.$conf['user_fields']['id'].' != '.$conf['guest_id'];
  if (isset($filter['username']))
  {
    $query.= '
  AND u.'.$conf['user_fields']['username'].' LIKE \''.$filter['username'].'\'';
  }
  if (isset($filter['group']))
  {
    $query.= '
    AND ug.group_id = '.$filter['group'];
  }
  if (isset($filter['status']))
  {
    $query.= '
    AND ui.status = \''.$filter['status']."'";
  }
  $query.= '
  ORDER BY '.$order_by.' '.$direction.'
;';

  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    $user = $row;
    $user['groups'] = array();

    array_push($users, $user);
  }

  // add group lists
  $user_ids = array();
  foreach ($users as $i => $user)
  {
    $user_ids[$i] = $user['id'];
  }
  $user_nums = array_flip($user_ids);
  
  if (count($user_ids) > 0)
  {
    $query = '
SELECT user_id, group_id
  FROM '.USER_GROUP_TABLE.'
  WHERE user_id IN ('.implode(',', $user_ids).')
;';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      array_push(
        $users[$user_nums[$row['user_id']]]['groups'],
        $row['group_id']
        );
    }
  }
    
  return $users;
}

// +-----------------------------------------------------------------------+
// |                           initialization                              |
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

$page['order_by_items'] = array(
  'id' => $lang['registration_date'],
  'username' => $lang['Username']
  );

$page['direction_items'] = array(
  'asc' => $lang['ascending'],
  'desc' => $lang['descending']
  );

// +-----------------------------------------------------------------------+
// |                              add a user                               |
// +-----------------------------------------------------------------------+

if (isset($_POST['submit_add']))
{
  $page['errors'] = register_user($_POST['login'], $_POST['password'], '');

  if (count($page['errors']) == 0)
  {
    array_push(
      $page['infos'],
      sprintf(
        l10n('user "%s" added'),
        $_POST['login']
        )
      );
  }
}

// +-----------------------------------------------------------------------+
// |                               user list                               |
// +-----------------------------------------------------------------------+

$page['filtered_users'] = get_filtered_user_list();

// +-----------------------------------------------------------------------+
// |                            selected users                             |
// +-----------------------------------------------------------------------+

if (isset($_POST['delete']) or isset($_POST['pref_submit']))
{
  $collection = array();
  
  switch ($_POST['target'])
  {
    case 'all' :
    {
      foreach($page['filtered_users'] as $local_user)
      {
        array_push($collection, $local_user['id']);
      }
      break;
    }
    case 'selection' :
    {
      if (isset($_POST['selection']))
      {
        $collection = $_POST['selection'];
      }
      break;
    }
  }

  if (count($collection) == 0)
  {
    array_push($page['errors'], l10n('Select at least one user'));
  }
}

// +-----------------------------------------------------------------------+
// |                             delete users                              |
// +-----------------------------------------------------------------------+

if (isset($_POST['delete']) and count($collection) > 0)
{
  if (in_array($conf['webmaster_id'], $collection))
  {
    array_push($page['errors'], l10n('Webmaster cannot be deleted'));
  }
  else
  {
    if (isset($_POST['confirm_deletion']) and 1 == $_POST['confirm_deletion'])
    {
      foreach ($collection as $user_id)
      {
        delete_user($user_id);
      }
      array_push(
        $page['infos'],
        sprintf(
          l10n('%d users deleted'),
          count($collection)  
          )
        );
      foreach ($page['filtered_users'] as $filter_key => $filter_user)
      {
        if (in_array($filter_user['id'], $collection))
        {
          unset($page['filtered_users'][$filter_key]);
        }
      }
    }
    else
    {
      array_push($page['errors'], l10n('You need to confirm deletion'));
    }
  }
}

// +-----------------------------------------------------------------------+
// |                       preferences form submission                     |
// +-----------------------------------------------------------------------+

if (isset($_POST['pref_submit']) and count($collection) > 0)
{
  if (-1 != $_POST['associate'])
  {
    $datas = array();
    
    $query = '
SELECT user_id
  FROM '.USER_GROUP_TABLE.'
  WHERE group_id = '.$_POST['associate'].'
;';
    $associated = array_from_query($query, 'user_id');
    
    $associable = array_diff($collection, $associated);
    
    if (count($associable) > 0)
    {
      foreach ($associable as $item)
      {
        array_push($datas,
                   array('group_id'=>$_POST['associate'],
                         'user_id'=>$item));
      }
        
      mass_inserts(USER_GROUP_TABLE,
                   array('group_id', 'user_id'),
                   $datas);
    }
  }
  
  if (-1 != $_POST['dissociate'])
  {
    $query = '
DELETE FROM '.USER_GROUP_TABLE.'
  WHERE group_id = '.$_POST['dissociate'].'
  AND user_id IN ('.implode(',', $collection).')
';
    pwg_query($query);
  }
  
  // properties to set for the collection (a user list)
  $datas = array();
  $dbfields = array('primary' => array('user_id'), 'update' => array());
  
  $formfields =
    array('nb_image_line', 'nb_line_page', 'template', 'language',
          'recent_period', 'maxwidth', 'expand', 'show_nb_comments',
          'maxheight', 'status', 'enabled_high');
  
  $true_false_fields = array('expand', 'show_nb_comments', 'enabled_high');
  if ($conf['allow_adviser'])
  {
    array_push($formfields, 'adviser');
    array_push($true_false_fields, 'adviser');
  }
  
  foreach ($formfields as $formfield)
  {
    // special for true/false fields
    if (in_array($formfield, $true_false_fields))
    {
      $test = $formfield;
    }
    else
    {
      $test = $formfield.'_action';
    }
    
    if ($_POST[$test] != 'leave')
    {
      array_push($dbfields['update'], $formfield);
    }
  }
  
  // updating elements is useful only if needed...
  if (count($dbfields['update']) > 0)
  {
    $datas = array();
    
    foreach ($collection as $user_id)
    {
      $data = array();
      $data['user_id'] = $user_id;
      
      // TODO : verify if submited values are semanticaly correct
      foreach ($dbfields['update'] as $dbfield)
      {
        // if the action is 'unset', the key won't be in row and
        // mass_updates function will set this field to NULL
        if (in_array($dbfield, $true_false_fields)
            or 'set' == $_POST[$dbfield.'_action'])
        {
          $data[$dbfield] = $_POST[$dbfield];
        }
      }

      // Webmaster status must not be changed
      if ($conf['webmaster_id'] == $user_id and isset($data['status']))
      {
        $data['status'] = 'webmaster';
      }

      // Webmaster and guest adviser must not be changed
      if ((($conf['webmaster_id'] == $user_id) or ($conf['guest_id'] == $user_id)) and isset($data['adviser']))
      {
        $data['adviser'] = 'false';
      }

      array_push($datas, $data);
    }
    
    mass_updates(USER_INFOS_TABLE, $dbfields, $datas);
  }

  redirect(
    PHPWG_ROOT_PATH.
    'admin.php'.
    get_query_string_diff(
      array(
        'start'
        )
      )
    );
}

// +-----------------------------------------------------------------------+
// |                              groups list                              |
// +-----------------------------------------------------------------------+

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

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(array('user_list'=>'admin/user_list.tpl'));

$base_url = PHPWG_ROOT_PATH.'admin.php?page=user_list';

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
    'L_PASSWORD' => $lang['password'],
    'L_EMAIL' => $lang['mail_address'],
    'L_ORDER_BY' => $lang['order_by'],
    'L_ACTIONS' => $lang['actions'],
    'L_PROPERTIES' => $lang['properties'],
    'L_PERMISSIONS' => $lang['permissions'],
    'L_USERS_LIST' => $lang['title_liste_users'],
    'L_LANGUAGE' => $lang['language'],
    'L_NB_IMAGE_LINE' => $lang['nb_image_per_row'],
    'L_NB_LINE_PAGE' => $lang['nb_row_per_page'],
    'L_TEMPLATE' => $lang['theme'],
    'L_RECENT_PERIOD' => $lang['recent_period'],
    'L_EXPAND' => $lang['auto_expand'],
    'L_SHOW_NB_COMMENTS' => $lang['show_nb_comments'],
    'L_MAXWIDTH' => $lang['maxwidth'],
    'L_MAXHEIGHT' => $lang['maxheight'],
    'L_YES' => $lang['yes'],
    'L_NO' => $lang['no'],
    'L_SUBMIT' => $lang['submit'],
    'L_RESET' => $lang['reset'],
    'L_DELETE' => $lang['user_delete'],
    'L_DELETE_HINT' => $lang['user_delete_hint'],

    'U_HELP' => PHPWG_ROOT_PATH.'/popuphelp.php?page=user_list',
    
    'F_ADD_ACTION' => $base_url,
    'F_USERNAME' => @$_GET['username'],
    'F_FILTER_ACTION' => PHPWG_ROOT_PATH.'admin.php'
    ));

if (isset($_GET['id']))
{
  $template->assign_block_vars('session', array('ID' => $_GET['id']));
}

// Hide radio-button if not allow to assign adviser
if ($conf['allow_adviser'])
{
  $template->assign_block_vars('adviser', array());
}

foreach ($page['order_by_items'] as $item => $label)
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

foreach ($page['direction_items'] as $item => $label)
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

foreach ($groups as $group_id => $group_name)
{
  $selected = (isset($_GET['group']) and $_GET['group'] == $group_id) ?
    'selected="selected"' : '';
  $template->assign_block_vars(
    $blockname,
    array(
      'VALUE' => $group_id,
      'CONTENT' => $group_name,
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

foreach (get_enums(USER_INFOS_TABLE, 'status') as $status)
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

// ---
//   $user['template'] = $conf['default_template'];
//   $user['nb_image_line'] = $conf['nb_image_line'];
//   $user['nb_line_page'] = $conf['nb_line_page'];
//   $user['language'] = $conf['default_language'];
//   $user['maxwidth'] = $conf['default_maxwidth'];
//   $user['maxheight'] = $conf['default_maxheight'];
//   $user['recent_period'] = $conf['recent_period'];
//   $user['expand'] = $conf['auto_expand'];
//   $user['show_nb_comments'] = $conf['show_nb_comments'];
// ---

if (isset($_POST['pref_submit']))
{
//  echo '<pre>'; print_r($_POST); echo '</pre>';
  $template->assign_vars(
    array(
      'ADVISER_YES' => 'true' == (isset($_POST['adviser']) and $_POST['adviser']) ? 'checked="checked"' : '',
      'ADVISER_NO' => 'false' == (isset($_POST['adviser']) and $_POST['adviser']) ? 'checked="checked"' : '',
      'NB_IMAGE_LINE' => $_POST['nb_image_line'],
      'NB_LINE_PAGE' => $_POST['nb_line_page'],
      'MAXWIDTH' => $_POST['maxwidth'],
      'MAXHEIGHT' => $_POST['maxheight'],
      'RECENT_PERIOD' => $_POST['recent_period'],
      'EXPAND_YES' => 'true' == $_POST['expand'] ? 'checked="checked"' : '',
      'EXPAND_NO' => 'false' == $_POST['expand'] ? 'checked="checked"' : '',
      'SHOW_NB_COMMENTS_YES' =>
        'true' == $_POST['show_nb_comments'] ? 'checked="checked"' : '',
      'SHOW_NB_COMMENTS_NO' =>
        'false' == $_POST['show_nb_comments'] ? 'checked="checked"' : '',
      'ENABLED_HIGH_YES' => 'true' == $_POST['enabled_high'] ? 'checked="checked"' : '',
      'ENABLED_HIGH_NO' => 'false' == $_POST['enabled_high'] ? 'checked="checked"' : '',
      ));
}
else
{
  $template->assign_vars(
    array(
      'NB_IMAGE_LINE' => $conf['nb_image_line'],
      'NB_LINE_PAGE' => $conf['nb_line_page'],
      'MAXWIDTH' => @$conf['default_maxwidth'],
      'MAXHEIGHT' => @$conf['default_maxheight'],
      'RECENT_PERIOD' => $conf['recent_period'],
      ));
}

$blockname = 'template_option';

foreach (get_pwg_themes() as $pwg_template)
{
  if (isset($_POST['pref_submit']))
  {
    $selected = $_POST['template']==$pwg_template ? 'selected="selected"' : '';
  }
  else if ($conf['default_template'] == $pwg_template)
  {
    $selected = 'selected="selected"';
  }
  else
  {
    $selected = '';
  }
  
  $template->assign_block_vars(
    $blockname,
    array(
      'VALUE'=> $pwg_template,
      'CONTENT' => $pwg_template,
      'SELECTED' => $selected
      ));
}

$blockname = 'language_option';

foreach (get_languages() as $language_code => $language_name)
{
  if (isset($_POST['pref_submit']))
  {
    $selected = $_POST['language']==$language_code ? 'selected="selected"':'';
  }
  else if ($conf['default_language'] == $language_code)
  {
    $selected = 'selected="selected"';
  }
  else
  {
    $selected = '';
  }
  
  $template->assign_block_vars(
    $blockname,
    array(
      'VALUE'=> $language_code,
      'CONTENT' => $language_name,
      'SELECTED' => $selected
      ));
}

$blockname = 'pref_status_option';

foreach (get_enums(USER_INFOS_TABLE, 'status') as $status)
{
  if (isset($_POST['pref_submit']))
  {
    $selected = $_POST['status'] == $status ? 'selected="selected"' : '';
  }
  else if ('normal' == $status)
  {
    $selected = 'selected="selected"';
  }
  else
  {
    $selected = '';
  }

  // Only status <= can be assign
  if (is_autorize_status(get_access_type_status($status)))
  {
    $template->assign_block_vars(
      $blockname,
      array(
        'VALUE' => $status,
        'CONTENT' => $lang['user_status_'.$status],
        'SELECTED' => $selected
        ));
  }
}

// associate
$blockname = 'associate_option';

$template->assign_block_vars(
  $blockname,
  array(
    'VALUE'=> -1,
    'CONTENT' => '------------',
    'SELECTED' => ''
    ));

foreach ($groups as $group_id => $group_name)
{
  if (isset($_POST['pref_submit']))
  {
    $selected = $_POST['associate'] == $group_id ? 'selected="selected"' : '';
  }
  else
  {
    $selected = '';
  }
    
  $template->assign_block_vars(
    $blockname,
    array(
      'VALUE' => $group_id,
      'CONTENT' => $group_name,
      'SELECTED' => $selected
      ));
}

// dissociate
$blockname = 'dissociate_option';

$template->assign_block_vars(
  $blockname,
  array(
    'VALUE'=> -1,
    'CONTENT' => '------------',
    'SELECTED' => ''
    ));

foreach ($groups as $group_id => $group_name)
{
  if (isset($_POST['pref_submit']))
  {
    $selected = $_POST['dissociate'] == $group_id ? 'selected="selected"' : '';
  }
  else
  {
    $selected = '';
  }
    
  $template->assign_block_vars(
    $blockname,
    array(
      'VALUE' => $group_id,
      'CONTENT' => $group_name,
      'SELECTED' => $selected
      ));
}

// +-----------------------------------------------------------------------+
// |                            navigation bar                             |
// +-----------------------------------------------------------------------+

$url = PHPWG_ROOT_PATH.'admin.php'.get_query_string_diff(array('start'));

$navbar = create_navigation_bar(
  $url,
  count($page['filtered_users']),
  $start,
  $conf['users_page']
  );

$template->assign_vars(array('NAVBAR' => $navbar));

// +-----------------------------------------------------------------------+
// |                               user list                               |
// +-----------------------------------------------------------------------+

$profile_url = PHPWG_ROOT_PATH.'admin.php?page=profile&amp;user_id=';
$perm_url = PHPWG_ROOT_PATH.'admin.php?page=user_perm&amp;user_id=';

foreach ($page['filtered_users'] as $num => $local_user)
{
  // simulate LIMIT $start, $conf['users_page']
  if ($num < $start)
  {
    continue;
  }
  if ($num >= $start + $conf['users_page'])
  {
    break;
  }

  $groups_string = preg_replace(
    '/(\d+)/e',
    "\$groups['$1']",
    implode(
      ', ',
      $local_user['groups']
      )
    );

  if (isset($_POST['pref_submit'])
      and isset($_POST['selection'])
      and in_array($local_user['id'], $_POST['selection']))
  {
    $checked = 'checked="checked"';
  }
  else
  {
    $checked = '';
  }

  $template->assign_block_vars(
    'user',
    array(
      'CLASS' => ($num % 2 == 1) ? 'row2' : 'row1',
      'ID' => $local_user['id'],
      'CHECKED' => $checked,
      'U_MOD' => $profile_url.$local_user['id'],
      'U_PERM' => $perm_url.$local_user['id'],
      'USERNAME' => $local_user['username'],
      'STATUS' => $lang['user_status_'.$local_user['status']].(($local_user['adviser'] == 'true') ? ' ['.$lang['adviser'].']' : ''),
      'EMAIL' => isset($local_user['email']) ? $local_user['email'] : '',
      'GROUPS' => $groups_string,
      'PROPERTIES' => (isset($local_user['enabled_high']) and ($local_user['enabled_high'] == 'true')) ? $lang['is_high_enabled'] : $lang['is_high_disabled']
      )
    );
}

// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'user_list');
?>
