<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2016 Piwigo Team                  http://piwigo.org |
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

include_once(PHPWG_ROOT_PATH.'admin/include/updates.class.php');
include_once(PHPWG_ROOT_PATH.'admin/include/pclzip.lib.php');

/*
STEP:
0 = check is needed. If version is latest or check fail, we stay on step 0
1 = new version on same branch AND new branch are available => user may choose upgrade.
2 = upgrade on same branch
3 = upgrade on different branch
*/
$step = isset($_GET['step']) ? $_GET['step'] : 0;
$upgrade_to = isset($_GET['to']) ? $_GET['to'] : '';
$updates = new updates();

$new_versions = $updates->get_piwigo_new_versions();
$update_php_version_minor = '5.2.0';
$update_mysql_version_minor = '5.0.0';
$update_php_version_major = '5.2.0';
$update_mysql_version_major = '5.0.0';

// +-----------------------------------------------------------------------+
// |                                Step 0                                 |
// +-----------------------------------------------------------------------+
if ($step == 0)
{

  if (isset($new_versions['minor']) and isset($new_versions['major']))
  {
    $step = 1;
    $upgrade_to = $new_versions['major'];

    $template->assign(
      array(
        'MINOR_VERSION' => $new_versions['minor'],
        'MAJOR_VERSION' => $new_versions['major'],
        )
      );
  }
  elseif (isset($new_versions['minor']))
  {
    $step = 2;
    $upgrade_to = $new_versions['minor'];
  }
  elseif (isset($new_versions['major']))
  {
    $step = 3;
    $upgrade_to = $new_versions['major'];
  }

  $template->assign('CHECK_VERSION', $new_versions['piwigo.org-checked']);
  $template->assign('DEV_VERSION', $new_versions['is_dev']);
}

// +-----------------------------------------------------------------------+
// |                                Step 1                                 |
// +-----------------------------------------------------------------------+
if ($step == 1)
{
  // nothing to do here
}

// +-----------------------------------------------------------------------+
// |                                Step 2                                 |
// +-----------------------------------------------------------------------+
if ($step == 2 and is_webmaster())
{
  if (isset($_POST['submit']) and isset($_POST['upgrade_to']))
  {
    updates::upgrade_to($_POST['upgrade_to'], $step);
  }
  $update_php_version_minor = $new_versions['minor_php'];
  $update_mysql_version_minor = $new_versions['minor_mysql'];
}

// +-----------------------------------------------------------------------+
// |                                Step 3                                 |
// +-----------------------------------------------------------------------+
if ($step == 3 and is_webmaster())
{
  if (isset($_POST['dumpDatabase']))
  {
    updates::dump_database(isset($_POST['includeHistory']));
  }

  if (isset($_POST['submit']) and isset($_POST['upgrade_to']))
  {
    updates::upgrade_to($_POST['upgrade_to'], $step);
  }

  $update_php_version_major = $new_versions['major_php'];
  $update_mysql_version_major = $new_versions['major_mysql'];
  $updates->get_merged_extensions($upgrade_to);
  $updates->get_server_extensions($upgrade_to);
  $template->assign('missing', $updates->missing);
}

//+-------------------------------------------------------------+
//|                        Pre_Update check                     |
//+-------------------------------------------------------------+

$user_mysql_version = pwg_get_db_version();
$user_php_version = phpversion();
$update_php_minor = false;
$update_sql_minor = false;
$update_php_major = false;
$update_sql_major = false;

if (version_compare($user_php_version, $update_php_version_minor, '<'))
{
  $update_php_minor = true;
}

if (version_compare($user_mysql_version, $update_mysql_version_minor, '<'))
{
  $update_sql_minor = true;
}

if (version_compare($user_php_version, $update_php_version_major, '<'))
{
  $update_php_major = true;
}

if (version_compare($user_mysql_version, $update_mysql_version_major, '<'))
{
  $update_sql_major = true;
}


// +-----------------------------------------------------------------------+
// |                        Process template                               |
// +-----------------------------------------------------------------------+

if (!is_webmaster())
{
  $page['errors'][] = l10n('Webmaster status is required.');
}

$template->assign(array(
  'STEP'                => $step,
  'CHECK_PHP_MAJOR'     => $update_php_major,
  'UPDATE_PHP_MAJOR'    => $update_php_version_major,
  'CHECK_SQL_MAJOR'     => $update_sql_major,
  'UPDATE_SQL_MAJOR'    => $update_mysql_version_major,
  'CHECK_PHP_MINOR'     => $update_php_minor,
  'UPDATE_PHP_MINOR'    => $update_php_version_minor,
  'CHECK_SQL_MINOR'     => $update_sql_minor,
  'UPDATE_SQL_MINOR'    => $update_mysql_version_minor,
  'PHPWG_VERSION'       => PHPWG_VERSION,
  'UPGRADE_TO'          => $upgrade_to,
  'RELEASE_URL'         => PHPWG_URL.'/releases/'.$upgrade_to,
  )
);

$template->set_filename('plugin_admin_content', 'updates_pwg.tpl');
$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');

?>
