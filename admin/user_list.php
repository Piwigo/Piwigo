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

// +-----------------------------------------------------------------------+
// | tabs                                                                  |
// +-----------------------------------------------------------------------+

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

$my_base_url = get_root_url().'admin.php?page=';

$tabsheet = new tabsheet();
$tabsheet->set_id('users');
$tabsheet->select('user_list');
$tabsheet->assign();

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
// | template                                                              |
// +-----------------------------------------------------------------------+

$template->set_filenames(array('user_list'=>'user_list.tpl'));

$query = '
SELECT
    DISTINCT u.'.$conf['user_fields']['id'].' AS id,
    u.'.$conf['user_fields']['username'].' AS username,
    u.'.$conf['user_fields']['email'].' AS email,
    ui.status,
    ui.enabled_high,
    ui.level
  FROM '.USERS_TABLE.' AS u
    INNER JOIN '.USER_INFOS_TABLE.' AS ui ON u.'.$conf['user_fields']['id'].' = ui.user_id
  WHERE u.'.$conf['user_fields']['id'].' > 0
;';

$result = pwg_query($query);
while ($row = pwg_db_fetch_assoc($result))
{
  $users[] = $row;
  $user_ids[] = $row['id'];
}

$template->assign(
  array(
    'users' => $users,
    'all_users' => join(',', $user_ids),
    'ACTIVATE_COMMENTS' => $conf['activate_comments'],
    'Double_Password' => $conf['double_password_type_in_admin']
    )
  );

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


// +-----------------------------------------------------------------------+
// | html code display                                                     |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'user_list');
?>