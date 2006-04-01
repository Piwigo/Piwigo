<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// | Copyright (C) 2006 Ruben ARNAUD - team@phpwebgallery.net              |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2006-03-23 02:49:04 +0100 (jeu., 23 mars 2006) $
// | last modifier : $Author: rvelices $
// | revision      : $Revision: 1094 $
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


define('PHPWG_ROOT_PATH','./');
include_once(PHPWG_ROOT_PATH.'include/common.inc.php');
include_once(PHPWG_ROOT_PATH.'include/functions_notification.inc.php');
include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_notification_by_mail.inc.php');
// Translations are in admin file too
include(get_language_filepath('admin.lang.php'));

// +-----------------------------------------------------------------------+
// | Main                                                                  |
// +-----------------------------------------------------------------------+
$page['errors'] = array();
$page['infos'] = array();

if (isset($_GET['subscribe'])
    and preg_match('/^[A-Za-z0-9]{16}$/', $_GET['subscribe']))
{
  subcribe_notification_by_mail(false, array($_GET['subscribe']));
}
else
if (isset($_GET['unsubscribe'])
    and preg_match('/^[A-Za-z0-9]{16}$/', $_GET['unsubscribe']))
{
  unsubcribe_notification_by_mail(false, array($_GET['unsubscribe']));
}
else
{
  echo l10n('nbm_unknown_identifier');
  exit();
}

// +-----------------------------------------------------------------------+
// |                        infos & errors display                         |
// +-----------------------------------------------------------------------+
echo '<pre>';

if (count($page['errors']) != 0)
{
  echo "\n\nErrors:\n";
  foreach ($page['errors'] as $error)
  {
    echo $error."\n";
  }
}

if (count($page['infos']) != 0)
{
  echo "\n\nInformations:\n";
  foreach ($page['infos'] as $info)
  {
    echo $info."\n";
  }
}

echo '</pre>';


?>