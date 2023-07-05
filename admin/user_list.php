<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
 * Add users and manage users list
 */

check_input_parameter('group', $_GET, false, PATTERN_ID);

// +-----------------------------------------------------------------------+
// | tabs                                                                  |
// +-----------------------------------------------------------------------+

$page['tab'] = 'user_list';
include(PHPWG_ROOT_PATH.'admin/include/user_tabs.inc.php');

// +-----------------------------------------------------------------------+
// |                              groups list                              |
// +-----------------------------------------------------------------------+

$groups = array();

$query = '
SELECT id, name
  FROM `'.GROUPS_TABLE.'`
  ORDER BY name ASC
;';
$result = pwg_query($query);

while ($row = pwg_db_fetch_assoc($result))
{
  $groups[$row['id']] = $row['name'];
}

// +-----------------------------------------------------------------------+
// |                              Dates for filtering                      |
// +-----------------------------------------------------------------------+

$query = '
SELECT DISTINCT
      month(registration_date) as registration_month,
      year(registration_date) as registration_year
FROM '.USER_INFOS_TABLE.'
ORDER BY registration_date
;';
$result = pwg_query($query);

$register_dates = array();
while ($row = pwg_db_fetch_assoc($result))
{
  $register_dates[] = $row['registration_month'].' '.$row['registration_year'];
}

$template->assign('register_dates', implode(',' , $register_dates));


// +-----------------------------------------------------------------------+
// | template                                                              |
// +-----------------------------------------------------------------------+
$template->assign(
  array(
    'ADMIN_PAGE_TITLE' => l10n('Users'),
    'ACTIVATE_COMMENTS' => $conf['activate_comments'],
    'Double_Password' => $conf['double_password_type_in_admin']
  )
);

$template->set_filenames(array('user_list'=>'user_list.tpl'));

$default_user = get_default_user_info(true);

$protected_users = array(
  $user['id'],
  $conf['guest_id'],
  $conf['default_user_id'],
  $conf['webmaster_id'],
  );

$password_protected_users = array($conf['guest_id']);

// an admin can't delete other admin/webmaster
if ('admin' == $user['status'])
{
  $query = '
SELECT
    user_id
  FROM '.USER_INFOS_TABLE.'
  WHERE status IN (\'webmaster\', \'admin\')
;';
  $admin_ids = query2array($query, null, 'user_id');
  
  $protected_users = array_merge($protected_users, $admin_ids);

  // we add all admin+webmaster users BUT the user herself
  $password_protected_users = array_merge($password_protected_users, array_diff($admin_ids, array($user['id'])));
}

$template->assign(
  array(
    'U_HISTORY' => get_root_url().'admin.php?page=history&filter_user_id=',
    'PWG_TOKEN' => get_pwg_token(),
    'NB_IMAGE_PAGE' => $default_user['nb_image_page'],
    'RECENT_PERIOD' => $default_user['recent_period'],
    'theme_options' => get_pwg_themes(),
    'theme_selected' => get_default_theme(),
    'language_options' => get_languages(),
    'language_selected' => get_default_language(),
    'association_options' => $groups,
    'protected_users' => implode(',', array_unique($protected_users)),
    'password_protected_users' => implode(',', array_unique($password_protected_users)),
    'guest_user' => $conf['guest_id'],
    'filter_group' => (isset($_GET['group']) ? $_GET['group'] : null),
    'connected_user' => $user["id"],
    'connected_user_status' => $user['status'],
    'owner' => $conf['webmaster_id']
    )
  );

if (isset($_GET['show_add_user']))
{
  $template->assign('show_add_user', true);
}

// Status options
foreach (get_enums(USER_INFOS_TABLE, 'status') as $status)
{
  $label_of_status[$status] = l10n('user_status_'.$status);
}

$pref_status_options = $label_of_status;

// a simple "admin" can't set/remove statuses webmaster/admin
if ('admin' == $user['status'])
{
  unset($pref_status_options['webmaster']);
  unset($pref_status_options['admin']);
}

$template->assign('label_of_status', $label_of_status);
$template->assign('pref_status_options', $pref_status_options);
$template->assign('pref_status_selected', 'normal');

// user level options
foreach ($conf['available_permission_levels'] as $level)
{
  $level_options[$level] = l10n(sprintf('Level %d', $level));
}
$template->assign('level_options', $level_options);
$template->assign('level_selected', $default_user['level']);

$query = '
SELECT id, name, is_default
  FROM `'.GROUPS_TABLE.'`
  ORDER BY name ASC
;';
$result = pwg_query($query);

$groups_arr_id = [];
$groups_arr_name = [];
while ($row = pwg_db_fetch_assoc($result))
{
  $groups_arr_name[] = '"'.pwg_db_real_escape_string($row["name"]).'"';
  $groups_arr_id[] = $row["id"];
}

$template->assign('groups_arr_id', implode(',', $groups_arr_id));
$template->assign('groups_arr_name', implode(',', $groups_arr_name));
$template->assign('guest_id', $conf["guest_id"]);

$template->assign('view_selector', userprefs_get_param('user-manager-view', 'line'));

if (userprefs_get_param('user-manager-view', 'line') == 'line') 
{
  //Show 5 users by default
  $template->assign('pagination', userprefs_get_param('user-manager-pagination', 5));
}
else
{
  //Show 10 users by default
  $template->assign('pagination', userprefs_get_param('user-manager-pagination', 10));
}
// +-----------------------------------------------------------------------+
// | html code display                                                     |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'user_list');
?>