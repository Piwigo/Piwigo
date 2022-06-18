<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if( !defined("PHPWG_ROOT_PATH") ) die ("Hacking attempt!");

$edit_user = build_user( $_GET['user_id'], false );

if (!empty($_POST))
{
  check_pwg_token();
}

include_once(PHPWG_ROOT_PATH.'profile.php');

$errors = array();
save_profile_from_post($edit_user, $errors);

load_profile_in_template(
  get_root_url().'admin.php?page=profile&amp;user_id='.$edit_user['id'],
  get_root_url().'admin.php?page=user_list',
  $edit_user
  );
$page['errors'] = array_merge($page['errors'], $errors);

$template->set_filename('profile', 'profile.tpl');
$template->assign_var_from_handle('ADMIN_CONTENT', 'profile');
?>
