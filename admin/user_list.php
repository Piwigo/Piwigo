<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
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
    $filter['username'] = mysql_real_escape_string($username);
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
                ui.enabled_high,
                ui.level
  FROM '.USERS_TABLE.' AS u
    INNER JOIN '.USER_INFOS_TABLE.' AS ui
      ON u.'.$conf['user_fields']['id'].' = ui.user_id
    LEFT JOIN '.USER_GROUP_TABLE.' AS ug
      ON u.'.$conf['user_fields']['id'].' = ug.user_id
  WHERE u.'.$conf['user_fields']['id'].' > 0';
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
  'id' => l10n('registration_date'),
  'username' => l10n('Username'),
  'level' => l10n('Privacy level'),
  'language' => l10n('language'),
  );

$page['direction_items'] = array(
  'asc' => l10n('ascending'),
  'desc' => l10n('descending')
  );

// +-----------------------------------------------------------------------+
// |                              add a user                               |
// +-----------------------------------------------------------------------+

// Check for config_default var - If True : Using double password type else single password type
// This feature is discussed on Piwigo's english forum
if ($conf['double_password_type_in_admin'] == true)
{
	if (isset($_POST['submit_add']))
	{
		if(empty($_POST['password']))
		{
			array_push($page['errors'], l10n('Password is missing'));
		}
		else if(empty($_POST['password_conf']))
		{
			array_push($page['errors'], l10n('Password confirmation is missing'));
		}
		else if(empty($_POST['email']))
		{
			array_push($page['errors'], l10n('Email address is missing'));
		}
		else if ($_POST['password'] != $_POST['password_conf'])
		{
			array_push($page['errors'], l10n('Password confirmation error'));
		}
		else
		{
  		$page['errors'] = register_user(
    		$_POST['login'], $_POST['password'], $_POST['email'], false);

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
	}
}
else if ($conf['double_password_type_in_admin'] == false)
{
	if (isset($_POST['submit_add']))
	{
  	$page['errors'] = register_user(
    	$_POST['login'], $_POST['password'], $_POST['email'], false);

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
  if (in_array($conf['guest_id'], $collection))
  {
    array_push($page['errors'], l10n('Guest cannot be deleted'));
  }
  if (($conf['guest_id'] != $conf['default_user_id']) and
      in_array($conf['default_user_id'], $collection))
  {
    array_push($page['errors'], l10n('Default user cannot be deleted'));
  }
  if (in_array($conf['webmaster_id'], $collection))
  {
    array_push($page['errors'], l10n('Webmaster cannot be deleted'));
  }
  if (in_array($user['id'], $collection))
  {
    array_push($page['errors'], l10n('You cannot delete your account'));
  }

  if (count($page['errors']) == 0)
  {
    if (isset($_POST['confirm_deletion']) and 1 == $_POST['confirm_deletion'])
    {
      foreach ($collection as $user_id)
      {
        delete_user($user_id);
      }
      array_push(
        $page['infos'],
        l10n_dec(
          '%d user deleted', '%d users deleted',
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
          'show_nb_hits', 'maxheight', 'status', 'enabled_high',
          'level');

  $true_false_fields = array('expand', 'show_nb_comments',
                       'show_nb_hits', 'enabled_high');
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

      // special users checks
      if
        (
          ($conf['webmaster_id'] == $user_id) or
          ($conf['guest_id'] == $user_id) or
          ($conf['default_user_id'] == $user_id)
        )
      {
        // status must not be changed
        if (isset($data['status']))
        {
          if ($conf['webmaster_id'] == $user_id)
          {
            $data['status'] = 'webmaster';
          }
          else
          {
            $data['status'] = 'guest';
          }
        }

        // could not be adivser
        if (isset($data['adviser']))
        {
          $data['adviser'] = 'false';
        }
      }

      array_push($datas, $data);
    }

    mass_updates(USER_INFOS_TABLE, $dbfields, $datas);
  }

  redirect(
    get_root_url().
    'admin.php'.
    get_query_string_diff(array(), false)
    );
}

// +-----------------------------------------------------------------------+
// |                              groups list                              |
// +-----------------------------------------------------------------------+

$groups[-1] = '------------';

$query = '
SELECT id, name
  FROM '.GROUPS_TABLE.'
  ORDER BY name ASC
;';
$result = pwg_query($query);

while ($row = mysql_fetch_array($result))
{
  $groups[$row['id']] = $row['name'];
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(array('user_list'=>'user_list.tpl'));

$base_url = PHPWG_ROOT_PATH.'admin.php?page=user_list';

if (isset($_GET['start']) and is_numeric($_GET['start']))
{
  $start = $_GET['start'];
}
else
{
  $start = 0;
}

$template->assign(
  array(
    'U_HELP' => get_root_url().'popuphelp.php?page=user_list',

    'F_ADD_ACTION' => $base_url,
    'F_USERNAME' => @htmlentities($_GET['username']),
    'F_FILTER_ACTION' => get_root_url().'admin.php'
    ));

// Hide radio-button if not allow to assign adviser
if ($conf['allow_adviser'])
{
  $template->assign('adviser', true);
}

// Display or Hide double password type
if ($conf['double_password_type_in_admin'])
{
  $template->assign('Double_Password', true);
}

// Filter status options
$status_options[-1] = '------------';
foreach (get_enums(USER_INFOS_TABLE, 'status') as $status)
{
  $status_options[$status] = l10n('user_status_'.$status);
}
$template->assign('status_options', $status_options);
$template->assign('status_selected',
    isset($_GET['status']) ? $_GET['status'] : '');

// Filter group options
$template->assign('group_options', $groups);
$template->assign('group_selected',
    isset($_GET['group']) ? $_GET['group'] : '');

// Filter order options
$template->assign('order_options', $page['order_by_items']);
$template->assign('order_selected',
    isset($_GET['order_by']) ? $_GET['order_by'] : '');

// Filter direction options
$template->assign('direction_options', $page['direction_items']);
$template->assign('direction_selected',
    isset($_GET['direction']) ? $_GET['direction'] : '');


if (isset($_POST['pref_submit']))
{
  $template->assign(
    array(
      'NB_IMAGE_LINE' => $_POST['nb_image_line'],
      'NB_LINE_PAGE' => $_POST['nb_line_page'],
      'MAXWIDTH' => $_POST['maxwidth'],
      'MAXHEIGHT' => $_POST['maxheight'],
      'RECENT_PERIOD' => $_POST['recent_period'],
      ));
}
else
{
  $default_user = get_default_user_info(true);
  $template->assign(
    array(
      'NB_IMAGE_LINE' => $default_user['nb_image_line'],
      'NB_LINE_PAGE' => $default_user['nb_line_page'],
      'MAXWIDTH' => $default_user['maxwidth'],
      'MAXHEIGHT' => $default_user['maxheight'],
      'RECENT_PERIOD' => $default_user['recent_period'],
      ));
}

// Template Options
$template->assign('template_options', get_pwg_themes());
$template->assign('template_selected', 
    isset($_POST['pref_submit']) ? $_POST['template'] : get_default_template());

// Language options
$template->assign('language_options', get_languages());
$template->assign('language_selected', 
    isset($_POST['pref_submit']) ? $_POST['language'] : get_default_language());

// Status options
foreach (get_enums(USER_INFOS_TABLE, 'status') as $status)
{
  // Only status <= can be assign
  if (is_autorize_status(get_access_type_status($status)))
  {
    $pref_status_options[$status] = l10n('user_status_'.$status);
  }
}
$template->assign('pref_status_options', $pref_status_options);
$template->assign('pref_status_selected', 
    isset($_POST['pref_submit']) ? $_POST['status'] : 'normal');

// associate and dissociate options
$template->assign('association_options', $groups);
$template->assign('associate_selected',
    isset($_POST['pref_submit']) ? $_POST['associate'] : '');
$template->assign('dissociate_selected',
    isset($_POST['pref_submit']) ? $_POST['dissociate'] : '');


// user level options
foreach ($conf['available_permission_levels'] as $level)
{
  $level_options[$level] = l10n(sprintf('Level %d', $level));
}
$template->assign('level_options', $level_options);
$template->assign('level_selected', 
    isset($_POST['pref_submit']) ? $_POST['level'] : $default_user['level']);

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

$template->assign('NAVBAR', $navbar);

// +-----------------------------------------------------------------------+
// |                               user list                               |
// +-----------------------------------------------------------------------+

$profile_url = get_root_url().'admin.php?page=profile&amp;user_id=';
$perm_url = get_root_url().'admin.php?page=user_perm&amp;user_id=';

$visible_user_list = array();
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

  $visible_user_list[] = $local_user;
}

// allow plugins to fill template var plugin_user_list_column_titles and 
// plugin_columns/plugin_actions for each user in the list
$visible_user_list = trigger_event('loc_visible_user_list', $visible_user_list);

foreach ($visible_user_list as $local_user)
{
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

  $properties = array();
  if ( $local_user['level'] != 0 )
  {
    $properties[] = l10n( sprintf('Level %d', $local_user['level']) );
  }
  $properties[] =
    (isset($local_user['enabled_high']) and ($local_user['enabled_high'] == 'true'))
        ? l10n('is_high_enabled') : l10n('is_high_disabled');

  $template->append(
    'users',
    array(
      'ID' => $local_user['id'],
      'CHECKED' => $checked,
      'U_PROFILE' => $profile_url.$local_user['id'],
      'U_PERM' => $perm_url.$local_user['id'],
      'USERNAME' => $local_user['username']
        .($local_user['id'] == $conf['guest_id']
          ? '<br>['.l10n('is_the_guest').']' : '')
        .($local_user['id'] == $conf['default_user_id']
          ? '<br>['.l10n('is_the_default').']' : ''),
      'STATUS' => l10n('user_status_'.
        $local_user['status']).(($local_user['adviser'] == 'true')
        ? '<br>['.l10n('adviser').']' : ''),
      'EMAIL' => get_email_address_as_display_text($local_user['email']),
      'GROUPS' => $groups_string,
      'PROPERTIES' => implode( ', ', $properties),
      'plugin_columns' => isset($local_user['plugin_columns']) ? $local_user['plugin_columns'] : array(),
      'plugin_actions' => isset($local_user['plugin_actions']) ? $local_user['plugin_actions'] : array(),
      )
    );
}

// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'user_list');
?>
