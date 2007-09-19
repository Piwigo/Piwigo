<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | file          : $Id$
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
define('PHPWG_VERSION', 'Butterfly');
define('PHPWG_DOMAIN', 'phpwebgallery.net');
define('PHPWG_URL', 'http://www.'.PHPWG_DOMAIN);
define('PHPWG_DEFAULT_LANGUAGE', 'en_UK.iso-8859-1');
define('PHPWG_DEFAULT_TEMPLATE', 'yoga/clear');

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
define('CATEGORIES_TABLE', $conf['tables']['categories_table']);
define('COMMENTS_TABLE', $conf['tables']['comments_table']);
define('CONFIG_TABLE', $conf['tables']['config_table']);
define('FAVORITES_TABLE', $conf['tables']['favorites_table']);
define('GROUP_ACCESS_TABLE', $conf['tables']['group_access_table']);
define('GROUPS_TABLE', $conf['tables']['groups_table']);
define('HISTORY_TABLE', $conf['tables']['history_table']);
define('HISTORY_SUMMARY_TABLE', $conf['tables']['history_summary_table']);
define('IMAGE_CATEGORY_TABLE', $conf['tables']['image_category_table']);
define('IMAGES_TABLE', $conf['tables']['images_table']);
define('SESSIONS_TABLE', $conf['tables']['sessions_table']);
define('SITES_TABLE', $conf['tables']['sites_table']);
define('USER_ACCESS_TABLE', $conf['tables']['user_access_table']);
define('USER_GROUP_TABLE', $conf['tables']['user_group_table']);
define('USERS_TABLE', $conf['tables']['users_table']);
define('USER_INFOS_TABLE', $conf['tables']['user_infos_table']);
define('USER_FEED_TABLE', $conf['tables']['user_feed_table']);
define('WAITING_TABLE', $conf['tables']['waiting_table']);
define('IMAGE_METADATA_TABLE', $conf['tables']['image_metadata_table']);
define('RATE_TABLE', $conf['tables']['rate_table']);
define('USER_CACHE_TABLE', $conf['tables']['user_cache_table']);
define('USER_CACHE_CATEGORIES_TABLE', $conf['tables']['user_cache_categories_table']);
define('CADDIE_TABLE', $conf['tables']['caddie_table']);
define('UPGRADE_TABLE', $conf['tables']['upgrade_table']);
define('SEARCH_TABLE', $conf['tables']['search_table']);
define('USER_MAIL_NOTIFICATION_TABLE', $conf['tables']['user_mail_notification_table']);
define('TAGS_TABLE', $conf['tables']['tags_table']);
define('IMAGE_TAG_TABLE', $conf['tables']['image_tag_table']);
define('PLUGINS_TABLE', $conf['tables']['plugins_table']);
define('WEB_SERVICES_ACCESS_TABLE', $conf['tables']['web_services_access_table']);
define('OLD_PERMALINKS_TABLE', $conf['tables']['old_permalinks_table']);

?>
