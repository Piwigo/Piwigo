<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
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

// Default settings
define('PHPWG_VERSION', '%PWGVERSION%');
define('PHPWG_URL', 'http://www.phpwebgallery.net');
define('PHPWG_FORUM_URL', 'http://forum.phpwebgallery.net');
define('PHPWG_DEFAULT_LANGUAGE', 'en_UK.iso-8859-1');

// Error codes
define('GENERAL_MESSAGE', 200);
define('GENERAL_ERROR', 202);
define('CRITICAL_MESSAGE', 203);
define('CRITICAL_ERROR', 204); 

// Access codes
define('ACCESS_NONE', 0);
define('ACCESS_GUEST', 1);
define('ACCESS_CLASSIC', 2);
define('ACCESS_ADMINISTRATOR', 3);
define('ACCESS_WEBMASTER', 4);

// Table names
define('CATEGORIES_TABLE', $prefixeTable.'categories');
define('COMMENTS_TABLE', $prefixeTable.'comments');
define('CONFIG_TABLE', $prefixeTable.'config');
define('FAVORITES_TABLE', $prefixeTable.'favorites');
define('GROUP_ACCESS_TABLE', $prefixeTable.'group_access');
define('GROUPS_TABLE', $prefixeTable.'groups');
define('HISTORY_TABLE', $prefixeTable.'history');
define('IMAGE_CATEGORY_TABLE', $prefixeTable.'image_category');
define('IMAGES_TABLE', $prefixeTable.'images');
define('SESSIONS_TABLE', $prefixeTable.'sessions');
define('SITES_TABLE', $prefixeTable.'sites');
define('USER_ACCESS_TABLE', $prefixeTable.'user_access');
define('USER_GROUP_TABLE', $prefixeTable.'user_group');
define('USERS_TABLE', $conf['users_table']);
define('USER_INFOS_TABLE', $prefixeTable.'user_infos');
define('USER_FEED_TABLE', $prefixeTable.'user_feed');
define('WAITING_TABLE', $prefixeTable.'waiting');
define('IMAGE_METADATA_TABLE', $prefixeTable.'image_metadata');
define('RATE_TABLE', $prefixeTable.'rate');
define('USER_CACHE_TABLE', $prefixeTable.'user_cache');
define('CADDIE_TABLE', $prefixeTable.'caddie');
define('UPGRADE_TABLE', $prefixeTable.'upgrade');
define('SEARCH_TABLE', $prefixeTable.'search');
define('USER_MAIL_NOTIFICATION_TABLE', $prefixeTable.'user_mail_notification');
define('TAGS_TABLE', $prefixeTable.'tags');
define('IMAGE_TAG_TABLE', $prefixeTable.'image_tag');
?>
