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
check_input_parameter('user_id', $_GET, false, PATTERN_ID);

// +-----------------------------------------------------------------------+
// | tabs                                                                  |
// +-----------------------------------------------------------------------+

$page['tab'] = 'user_list';
include(PHPWG_ROOT_PATH.'admin/include/user_tabs.inc.php');

// +-----------------------------------------------------------------------+
// |                              groups list                              |
// +-----------------------------------------------------------------------+

$groups = array();
$groups_for_filter = array();

$query = '
SELECT id, name, COUNT(ug.user_id) as nb_users_of
  FROM `'.GROUPS_TABLE.'`
    LEFT JOIN `'. USER_GROUP_TABLE .'` ug ON id = ug.group_id
  GROUP BY name
  ORDER BY name ASC
;';
$result = pwg_query($query);

while ($row = pwg_db_fetch_assoc($result))
{
  $groups[$row['id']] = $row['name'];
  $groups_for_filter[] = array(
    'id' => $row['id'],
    'name' => $row['name'],
    'counter' => $row['nb_users_of']
  );
}

$template->assign('groups_for_filter', $groups_for_filter);

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
  $register_dates[] = $row['registration_year'].'-'.sprintf('%02u', $row['registration_month']);
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

$query = '
SELECT
    username
    FROM '.USERS_TABLE.'
    WHERE id = '.$conf['webmaster_id'].'
;';

$owner_username = query2array($query, null, 'username');

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
    'search_input' => (isset($_GET['user_id']) ? 'id:'.$_GET['user_id'] : null),
    'connected_user' => $user["id"],
    'connected_user_status' => $user['status'],
    'owner' => $conf['webmaster_id'],
    'owner_username' => $owner_username[0]
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

$query = '
SELECT
    status,
    COUNT(*) AS nb_users_of
  FROM '. USER_INFOS_TABLE .'
  WHERE user_id != '. $conf['guest_id'] .'
  GROUP BY status
';

$result = pwg_query($query);
while($row = pwg_db_fetch_assoc($result))
{
  $nb_users_by_status[$row['status']] = array(
    'name' => l10n('user_status_'.$row['status']),
    'counter' => $row['nb_users_of'],
  );
}

$nb_users_by_status = array_merge($label_of_status, $nb_users_by_status);

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
$template->assign('nb_users_by_status', $nb_users_by_status);

// user level options
foreach ($conf['available_permission_levels'] as $level)
{
  $level_options[$level] = l10n(sprintf('Level %d', $level));
}

$query = '
SELECT
    level,
    COUNT(*) AS nb_users_of
  FROM '. USER_INFOS_TABLE .'
  WHERE user_id != '. $conf['guest_id'] .'
  GROUP BY level
';

$result = pwg_query($query);
$nb_users_by_level = $level_options;
while($row = pwg_db_fetch_assoc($result))
{
  $nb_users_by_level[$row['level']] = array(
    'name' => l10n(sprintf('Level %d', $row['level'])),
    'counter' => $row['nb_users_of']
  );
}

$template->assign('level_options', $level_options);
$template->assign('level_selected', $default_user['level']);
$template->assign('nb_users_by_level', $nb_users_by_level);

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

function webmaster_id_is_local()
{
  $conf = array();
  include(PHPWG_ROOT_PATH . 'include/config_default.inc.php');
  @include(PHPWG_ROOT_PATH. 'local/config/config.inc.php');
  if (isset($conf['local_dir_site']))
  {
    @include(PHPWG_ROOT_PATH.PWG_LOCAL_DIR. 'config/config.inc.php');
  }
  return $conf['webmaster_id'] ?? false;
}

if (webmaster_id_is_local())
{
  $page['warnings'][] = l10n('You have specified <i>$conf[\'webmaster_id\']</i> in your local configuration file, this parameter in deprecated, please remove it!');
}
// +-----------------------------------------------------------------------+
// | html code display                                                     |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'user_list');
?>