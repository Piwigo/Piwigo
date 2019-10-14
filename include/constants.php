<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

// Default settings
define('PHPWG_VERSION', '2.10.1');
define('PHPWG_DEFAULT_LANGUAGE', 'en_UK');

// this constant is only used in the upgrade process, the true default theme
// is the theme of user "guest", which is initialized with column user_infos.theme
// default value (see file install/piwigo_structure-mysql.sql)
define('PHPWG_DEFAULT_TEMPLATE', 'modus');

define('PHPWG_THEMES_PATH', $conf['themes_dir'].'/');
defined('PWG_COMBINED_DIR') or define('PWG_COMBINED_DIR', $conf['data_location'].'combined/');
defined('PWG_DERIVATIVE_DIR') or define('PWG_DERIVATIVE_DIR', $conf['data_location'].'i/');

// Required versions
define('REQUIRED_PHP_VERSION', '5.3.0');

// Access codes
define('ACCESS_FREE', 0);
define('ACCESS_GUEST', 1);
define('ACCESS_CLASSIC', 2);
define('ACCESS_ADMINISTRATOR', 3);
define('ACCESS_WEBMASTER', 4);
define('ACCESS_CLOSED', 5);

// Sanity checks
define('PATTERN_ID', '/^\d+$/');

// Table names
if (!defined('CATEGORIES_TABLE'))
  define('CATEGORIES_TABLE', $prefixeTable.'categories');
if (!defined('COMMENTS_TABLE'))
  define('COMMENTS_TABLE', $prefixeTable.'comments');
if (!defined('CONFIG_TABLE'))
  define('CONFIG_TABLE', $prefixeTable.'config');
if (!defined('FAVORITES_TABLE'))
  define('FAVORITES_TABLE', $prefixeTable.'favorites');
if (!defined('GROUP_ACCESS_TABLE'))
  define('GROUP_ACCESS_TABLE', $prefixeTable.'group_access');
if (!defined('GROUPS_TABLE'))
  define('GROUPS_TABLE', $prefixeTable.'groups');
if (!defined('HISTORY_TABLE'))
  define('HISTORY_TABLE', $prefixeTable.'history');
if (!defined('HISTORY_SUMMARY_TABLE'))
  define('HISTORY_SUMMARY_TABLE', $prefixeTable.'history_summary');
if (!defined('IMAGE_CATEGORY_TABLE'))
  define('IMAGE_CATEGORY_TABLE', $prefixeTable.'image_category');
if (!defined('IMAGES_TABLE'))
  define('IMAGES_TABLE', $prefixeTable.'images');
if (!defined('SESSIONS_TABLE'))
  define('SESSIONS_TABLE', $prefixeTable.'sessions');
if (!defined('SITES_TABLE'))
  define('SITES_TABLE', $prefixeTable.'sites');
if (!defined('USER_ACCESS_TABLE'))
  define('USER_ACCESS_TABLE', $prefixeTable.'user_access');
if (!defined('USER_GROUP_TABLE'))
  define('USER_GROUP_TABLE', $prefixeTable.'user_group');
if (!defined('USERS_TABLE'))
  define('USERS_TABLE', isset($conf['users_table']) ? $conf['users_table'] : $prefixeTable.'users' );
if (!defined('USER_INFOS_TABLE'))
  define('USER_INFOS_TABLE', $prefixeTable.'user_infos');
if (!defined('USER_FEED_TABLE'))
  define('USER_FEED_TABLE', $prefixeTable.'user_feed');
if (!defined('RATE_TABLE'))
  define('RATE_TABLE', $prefixeTable.'rate');
if (!defined('USER_AUTH_KEYS_TABLE'))
  define('USER_AUTH_KEYS_TABLE', $prefixeTable.'user_auth_keys');
if (!defined('USER_CACHE_TABLE'))
  define('USER_CACHE_TABLE', $prefixeTable.'user_cache');
if (!defined('USER_CACHE_CATEGORIES_TABLE'))
  define('USER_CACHE_CATEGORIES_TABLE', $prefixeTable.'user_cache_categories');
if (!defined('CADDIE_TABLE'))
  define('CADDIE_TABLE', $prefixeTable.'caddie');
if (!defined('UPGRADE_TABLE'))
  define('UPGRADE_TABLE', $prefixeTable.'upgrade');
if (!defined('SEARCH_TABLE'))
  define('SEARCH_TABLE', $prefixeTable.'search');
if (!defined('USER_MAIL_NOTIFICATION_TABLE'))
  define('USER_MAIL_NOTIFICATION_TABLE', $prefixeTable.'user_mail_notification');
if (!defined('TAGS_TABLE'))
  define('TAGS_TABLE', $prefixeTable.'tags');
if (!defined('IMAGE_TAG_TABLE'))
  define('IMAGE_TAG_TABLE', $prefixeTable.'image_tag');
if (!defined('PLUGINS_TABLE'))
  define('PLUGINS_TABLE', $prefixeTable.'plugins');
if (!defined('OLD_PERMALINKS_TABLE'))
  define('OLD_PERMALINKS_TABLE', $prefixeTable.'old_permalinks');
if (!defined('THEMES_TABLE'))
  define('THEMES_TABLE', $prefixeTable.'themes');
if (!defined('LANGUAGES_TABLE'))
  define('LANGUAGES_TABLE', $prefixeTable.'languages');
if (!defined('IMAGE_FORMAT_TABLE'))
  define('IMAGE_FORMAT_TABLE', $prefixeTable.'image_format');
if (!defined('ACTIVITY_TABLE'))
  define('ACTIVITY_TABLE', $prefixeTable.'activity');

?>
