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

check_input_parameter('to', $_GET, false, '/^\d+\.\d+\.\d+$/');
$upgrade_to = isset($_GET['to']) ? $_GET['to'] : '';

$updates = new updates();

// +-----------------------------------------------------------------------+
// |                                Step 0                                 |
// +-----------------------------------------------------------------------+
if ($step == 0)
{
  $new_versions = $updates->get_piwigo_new_versions();

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

  $updates->get_merged_extensions($upgrade_to);
  $updates->get_server_extensions($upgrade_to);
  $template->assign('missing', $updates->missing);
}

// +-----------------------------------------------------------------------+
// |                        Process template                               |
// +-----------------------------------------------------------------------+

if (!is_webmaster())
{
  $page['errors'][] = l10n('Webmaster status is required.');
}

$template->assign(array(
  'STEP'          => $step,
  'PHPWG_VERSION' => PHPWG_VERSION,
  'UPGRADE_TO'    => $upgrade_to,
  'RELEASE_URL'   => PHPWG_URL.'/releases/'.$upgrade_to,
  )
);

$template->set_filename('plugin_admin_content', 'updates_pwg.tpl');
$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');

?>