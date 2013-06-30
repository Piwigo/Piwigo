<?php

// Need upgrade?
global $conf;
include(PHPWG_THEMES_PATH.'smartpocket/admin/upgrade.inc.php');

load_language('theme.lang', PHPWG_THEMES_PATH.'smartpocket/');

$config_send= array();

if(isset($_POST['submit_smartpocket']))
{
  $config_send['loop']=(isset($_POST['loop']) and $_POST['loop']=="false") ? false : true;
  $config_send['autohide']=(isset($_POST['autohide']) and $_POST['autohide']=="0") ? 0 : 5000;
  
  $conf['smartpocket'] = serialize($config_send);
  conf_update_param('smartpocket', pwg_db_real_escape_string($conf['smartpocket']));

  array_push($page['infos'], l10n('Information data registered in database'));
}

$template->set_filenames(array(
    'theme_admin_content' => dirname(__FILE__) . '/admin.tpl'));

$template->assign('options', unserialize($conf['smartpocket']));

$template->assign_var_from_handle('ADMIN_CONTENT', 'theme_admin_content');
  
?>