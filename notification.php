<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2012 Piwigo Team                  http://piwigo.org |
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

// +-----------------------------------------------------------------------+
// |                           initialization                              |
// +-----------------------------------------------------------------------+

define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_GUEST);

// +-----------------------------------------------------------------------+
// |                          new feed creation                            |
// +-----------------------------------------------------------------------+

$page['feed'] = find_available_feed_id();

$query = '
INSERT INTO '.USER_FEED_TABLE.'
  (id, user_id, last_check)
  VALUES
  (\''.$page['feed'].'\', '.$user['id'].', NULL)
;';
pwg_query($query);


$feed_url=PHPWG_ROOT_PATH.'feed.php';
if (is_a_guest())
{
  $feed_image_only_url=$feed_url;
  $feed_url .= '?feed='.$page['feed'];
}
else
{
  $feed_url .= '?feed='.$page['feed'];
  $feed_image_only_url=$feed_url.'&amp;image_only';
}

// +-----------------------------------------------------------------------+
// |                        template initialization                        |
// +-----------------------------------------------------------------------+

$title = l10n('Notification');
$page['body_id'] = 'theNotificationPage';
$page['meta_robots']=array('noindex'=>1, 'nofollow'=>1);


$template->set_filenames(array('notification'=>'notification.tpl'));

$template->assign(
  array(
    'U_FEED' => $feed_url,
    'U_FEED_IMAGE_ONLY' => $feed_image_only_url,
    )
  );
  
// include menubar
$themeconf = $template->get_template_vars('themeconf');
if (!isset($themeconf['hide_menu_on']) OR !in_array('theNotificationPage', $themeconf['hide_menu_on']))
{
  include( PHPWG_ROOT_PATH.'include/menubar.inc.php');
}

// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+
include(PHPWG_ROOT_PATH.'include/page_header.php');
$template->pparse('notification');
include(PHPWG_ROOT_PATH.'include/page_tail.php');

?>