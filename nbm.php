<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+


//--------------------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once(PHPWG_ROOT_PATH.'include/common.inc.php');
check_status(ACCESS_FREE);
include_once(PHPWG_ROOT_PATH.'include/functions_notification.inc.php');
include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_notification_by_mail.inc.php');
// Translations are in admin file too
load_language('admin.lang');
// Need to update a second time
trigger_notify('loading_lang');
load_language('lang', PHPWG_ROOT_PATH.PWG_LOCAL_DIR, array('no_fallback'=>true, 'local'=>true) );


// +-----------------------------------------------------------------------+
// | Main                                                                  |
// +-----------------------------------------------------------------------+
if (isset($_GET['subscribe'])
    and preg_match('/^[A-Za-z0-9]{16}$/', $_GET['subscribe']))
{
  subscribe_notification_by_mail(false, array($_GET['subscribe']));
}
else if (isset($_GET['unsubscribe'])
    and preg_match('/^[A-Za-z0-9]{16}$/', $_GET['unsubscribe']))
{
  unsubscribe_notification_by_mail(false, array($_GET['unsubscribe']));
}
else
{
  $page['errors'][] = l10n('Unknown identifier');
}

// +-----------------------------------------------------------------------+
// | template initialization                                               |
// +-----------------------------------------------------------------------+
$title = l10n('Notification');
$page['body_id'] = 'theNBMPage';

$template->set_filenames(array('nbm'=>'nbm.tpl'));


// include menubar
$themeconf = $template->get_template_vars('themeconf');
if (!isset($themeconf['hide_menu_on']) OR !in_array('theNBMPage', $themeconf['hide_menu_on']))
{
  include( PHPWG_ROOT_PATH.'include/menubar.inc.php');
}

// +-----------------------------------------------------------------------+
// | html code display                                                     |
// +-----------------------------------------------------------------------+
include(PHPWG_ROOT_PATH.'include/page_header.php');
flush_page_messages();
$template->parse('nbm');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
