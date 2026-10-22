<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
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

// +-----------------------------------------------------------------------+
// | tabs                                                                  |
// +-----------------------------------------------------------------------+

$page['tab'] = 'people';
include(PHPWG_ROOT_PATH.'admin/include/people_tab.inc.php');

// +-----------------------------------------------------------------------+
// |                       template initialization                         |
// +-----------------------------------------------------------------------+

$template->set_filename('people', 'people.tpl');
$template->assign('ADMIN_PAGE_TITLE', l10n('People'));

// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+

$template->assign(array(
  'PWG_TOKEN' => get_pwg_token(),
  // toaster of the standard pages, reused here
  'PWG_TOASTER_TPL' => realpath(PHPWG_ROOT_PATH.'themes/standard_pages/template/toaster.tpl'),
  ));

$template->assign_var_from_handle('ADMIN_CONTENT', 'people');
