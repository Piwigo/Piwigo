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

if (!$conf['enable_core_update'])
{
  die('Piwigo core update system is disabled');
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
$new_versions = $updates->get_piwigo_new_versions();

// +-----------------------------------------------------------------------+
// |                                Step 0                                 |
// +-----------------------------------------------------------------------+
if ($step == 0)
{
  if (isset($new_versions['minor']) and isset($new_versions['major']))
  {
    $step = 1;
    $upgrade_to = $new_versions['major'];
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
  if (isset($_POST['submit']) and isset($_POST['upgrade_to']))
  {
    updates::upgrade_to($_POST['upgrade_to'], $step);
  }

  $updates->get_merged_extensions($upgrade_to);
  $updates->get_server_extensions($upgrade_to);
  $template->assign('missing', $updates->missing);
}

// +-----------------------------------------------------------------------+
// | Check for requirements                                                |
// +-----------------------------------------------------------------------+

if (isset($new_versions['minor_php']) and version_compare(phpversion(), $new_versions['minor_php'], '<'))
{
  $template->assign('MINOR_RELEASE_PHP_REQUIRED', $new_versions['minor_php']);
}

if (isset($new_versions['major_php']) and version_compare(phpversion(), $new_versions['major_php'], '<'))
{
  $template->assign('MAJOR_RELEASE_PHP_REQUIRED', $new_versions['major_php']);
}

// +-----------------------------------------------------------------------+
// |                        Process template                               |
// +-----------------------------------------------------------------------+

if (!is_webmaster())
{
  $page['warnings'][] = str_replace('%s', l10n('user_status_webmaster'), l10n('%s status is required to edit parameters.'));
}

$template->assign(array(
  'STEP'          => $step,
  'PIWIGO_CURRENT_VERSION' => isset($page['updated_version']) ? $page['updated_version'] : PHPWG_VERSION,
  'UPGRADE_TO'    => $upgrade_to,
  )
);

if (isset($new_versions['minor']))
{
  $template->assign(
    array(
      'MINOR_VERSION' => $new_versions['minor'],
      'MINOR_RELEASE_URL' => PHPWG_URL.'/releases/'.$new_versions['minor'],
    )
  );
}

if (isset($new_versions['major']))
{
  $template->assign(
    array(
      'MAJOR_VERSION' => $new_versions['major'],
      'MAJOR_RELEASE_URL' => PHPWG_URL.'/releases/'.$new_versions['major'],
    )
  );
}

$template->assign('ADMIN_PAGE_TITLE', l10n('Updates'));
$template->set_filename('plugin_admin_content', 'updates_pwg.tpl');
$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');

?>