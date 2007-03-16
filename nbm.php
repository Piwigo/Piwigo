<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// | Copyright (C) 2006-2007   Ruben ARNAUD - team@phpwebgallery.net       |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
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


//--------------------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once(PHPWG_ROOT_PATH.'include/common.inc.php');
check_status(ACCESS_NONE);
include_once(PHPWG_ROOT_PATH.'include/functions_notification.inc.php');
include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_notification_by_mail.inc.php');
// Translations are in admin file too
include(get_language_filepath('admin.lang.php'));
// Need to update a second time
trigger_action('loading_lang');
@include(get_language_filepath('local.lang.php'));


// +-----------------------------------------------------------------------+
// | Main                                                                  |
// +-----------------------------------------------------------------------+
$page['errors'] = array();
$page['infos'] = array();

if (isset($_GET['subscribe'])
    and preg_match('/^[A-Za-z0-9]{16}$/', $_GET['subscribe']))
{
  subscribe_notification_by_mail(false, array($_GET['subscribe']));
}
else
if (isset($_GET['unsubscribe'])
    and preg_match('/^[A-Za-z0-9]{16}$/', $_GET['unsubscribe']))
{
  unsubscribe_notification_by_mail(false, array($_GET['unsubscribe']));
}
else
{
/*  echo l10n('nbm_unknown_identifier');
  exit();*/
  array_push($page['errors'], l10n('nbm_unknown_identifier'));
}

// +-----------------------------------------------------------------------+
// | template initialization                                               |
// +-----------------------------------------------------------------------+
$title = $lang['nbm_item_notification'];
$page['body_id'] = 'theNBMPage';
include(PHPWG_ROOT_PATH.'include/page_header.php');

$template->set_filenames(array('nbm'=>'nbm.tpl'));

$template->assign_vars(array('U_HOME' => make_index_url()));

// +-----------------------------------------------------------------------+
// | errors & infos                                                        |
// +-----------------------------------------------------------------------+
if (count($page['errors']) != 0)
{
  $template->assign_block_vars('errors',array());
  foreach ($page['errors'] as $error)
  {
    $template->assign_block_vars('errors.error',array('ERROR'=>$error));
  }
}

if (count($page['infos']) != 0)
{
  $template->assign_block_vars('infos',array());
  foreach ($page['infos'] as $info)
  {
    $template->assign_block_vars('infos.info',array('INFO'=>$info));
  }
}

// +-----------------------------------------------------------------------+
// | html code display                                                     |
// +-----------------------------------------------------------------------+
$template->parse('nbm');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>