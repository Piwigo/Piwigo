<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2004 PhpWebGallery Team - http://phpwebgallery.net |
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
define('PHPWG_VERSION', 'BSF');
define('PHPWG_URL', 'http://www.phpwebgallery.net');
define('PHPWG_FORUM_URL', 'http://forum.phpwebgallery.net');

// User level
define('ANONYMOUS', 2);
 
// Error codes
define('GENERAL_MESSAGE', 200);
define('GENERAL_ERROR', 202);
define('CRITICAL_MESSAGE', 203);
define('CRITICAL_ERROR', 204); 

// Table names
define('CATEGORIES_TABLE', $table_prefix.'categories');
define('COMMENTS_TABLE', $table_prefix.'comments');
define('CONFIG_TABLE', $table_prefix.'config');
define('FAVORITES_TABLE', $table_prefix.'favorites');
define('GROUP_ACCESS_TABLE', $table_prefix.'group_access');
define('GROUPS_TABLE', $table_prefix.'groups');
define('HISTORY_TABLE', $table_prefix.'history');
define('IMAGE_CATEGORY_TABLE', $table_prefix.'image_category');
define('IMAGES_TABLE', $table_prefix.'images');
define('SESSIONS_TABLE', $table_prefix.'sessions');
define('SITES_TABLE', $table_prefix.'sites');
define('USER_ACCESS_TABLE', $table_prefix.'user_access');
define('USER_GROUP_TABLE', $table_prefix.'user_group');
define('USERS_TABLE', $table_prefix.'users');
define('WAITING_TABLE', $table_prefix.'waiting');
define('IMAGE_METADATA_TABLE', $table_prefix.'image_metadata');
define('RATE_TABLE', $table_prefix.'rate');
?>
