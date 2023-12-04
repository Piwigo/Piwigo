<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/image.class.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

if (isset($_GET['action']))
{
  check_pwg_token();
}
// +-----------------------------------------------------------------------+
// | Commons parameters                                                    |
// +-----------------------------------------------------------------------+

$maint_actions = array(
  'derivatives' => array(
    'icon' => 'icon-trash-1',
    'label' => l10n('Delete multiple size images'),
  ),
  'lock_gallery' => array(
    'icon' => 'icon-lock',
    'label' => l10n('Lock gallery'),
  ),
  'unlock_gallery' => array(
    'icon' => 'icon-lock',
    'label' => l10n('Unlock gallery'),
  ),
  'categories' => array(
    'icon' => 'icon-folder-open',
    'label' => l10n('Update albums informations'),
  ),
  'images' => array(
    'icon' => 'icon-info-circled-1',
    'label' => l10n('Update photos information'),
  ),
  'delete_orphan_tags' => array(
    'icon' => 'icon-tags',
    'label' => l10n('Delete orphan tags'),
  ),
  'user_cache' => array(
    'icon' => 'icon-user-1',
    'label' => l10n('Purge user cache'),
  ),
  'history_detail' => array(
    'icon' => 'icon-back-in-time',
    'label' => l10n('Purge history detail'),
  ),
  'history_summary' => array(
    'icon' => 'icon-back-in-time',
    'label' => l10n('Purge history summary'),
  ),
  'sessions' => array(
    'icon' => 'icon-th-list',
    'label' => l10n('Purge sessions'),
  ),
  'feeds' => array(
    'icon' => 'icon-bell',
    'label' => l10n('Purge never used notification feeds'),
  ),
  'database' => array(
    'icon' => 'icon-database',
    'label' => l10n('Repair and optimize database'),
  ),
  'c13y' => array(
    'icon' => 'icon-ok',
    'label' => l10n('Reinitialize check integrity'),
  ),
  'search' => array(
    'icon' => 'icon-search',
    'label' => l10n('Purge search history'),
  ),
  'compiled-templates' => array(
    'icon' => 'icon-file-code',
    'label' => l10n('Purge compiled templates'),
  ),
); 

// +-----------------------------------------------------------------------+
// | tabs                                                                  |
// +-----------------------------------------------------------------------+

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
$my_base_url = get_root_url().'admin.php?page=';

if (isset($_GET['tab']))
{
  check_input_parameter('tab', $_GET, false, '/^(actions|env|sys)$/');
  $page['tab'] = $_GET['tab'];
}
else
{
  $page['tab'] = 'actions';
}


$tabsheet = new tabsheet();
$tabsheet->set_id('maintenance');
$tabsheet->select($page['tab']);
$tabsheet->assign();

include(PHPWG_ROOT_PATH.'admin/maintenance_'.$page['tab'].'.php');

$template->assign(
  array('ADMIN_PAGE_TITLE' => l10n('Maintenance'))
);
