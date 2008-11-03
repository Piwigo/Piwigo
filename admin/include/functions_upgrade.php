<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
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

function check_upgrade()
{
  // Is Piwigo already installed ?
  if (!defined('PHPWG_IN_UPGRADE') or !PHPWG_IN_UPGRADE)
  {
    $message = 'Piwigo is not in upgrade mode. In include/mysql.inc.php,
insert line
<pre style="background-color:lightgray">
define(\'PHPWG_IN_UPGRADE\', true);
</pre>
if you want to upgrade';
    die($message);
  }
}

// concerning upgrade, we use the default tables
function prepare_conf_upgrade()
{
  global $prefixeTable;

  // $conf is not used for users tables
  // define cannot be re-defined
  define('CATEGORIES_TABLE', $prefixeTable.'categories');
  define('COMMENTS_TABLE', $prefixeTable.'comments');
  define('CONFIG_TABLE', $prefixeTable.'config');
  define('FAVORITES_TABLE', $prefixeTable.'favorites');
  define('GROUP_ACCESS_TABLE', $prefixeTable.'group_access');
  define('GROUPS_TABLE', $prefixeTable.'groups');
  define('HISTORY_TABLE', $prefixeTable.'history');
  define('HISTORY_SUMMARY_TABLE', $prefixeTable.'history_summary');
  define('IMAGE_CATEGORY_TABLE', $prefixeTable.'image_category');
  define('IMAGES_TABLE', $prefixeTable.'images');
  define('SESSIONS_TABLE', $prefixeTable.'sessions');
  define('SITES_TABLE', $prefixeTable.'sites');
  define('USER_ACCESS_TABLE', $prefixeTable.'user_access');
  define('USER_GROUP_TABLE', $prefixeTable.'user_group');
  define('USERS_TABLE', $prefixeTable.'users');
  define('USER_INFOS_TABLE', $prefixeTable.'user_infos');
  define('USER_FEED_TABLE', $prefixeTable.'user_feed');
  define('WAITING_TABLE', $prefixeTable.'waiting');
  define('RATE_TABLE', $prefixeTable.'rate');
  define('USER_CACHE_TABLE', $prefixeTable.'user_cache');
  define('USER_CACHE_CATEGORIES_TABLE', $prefixeTable.'user_cache_categories');
  define('CADDIE_TABLE', $prefixeTable.'caddie');
  define('UPGRADE_TABLE', $prefixeTable.'upgrade');
  define('SEARCH_TABLE', $prefixeTable.'search');
  define('USER_MAIL_NOTIFICATION_TABLE', $prefixeTable.'user_mail_notification');
  define('TAGS_TABLE', $prefixeTable.'tags');
  define('IMAGE_TAG_TABLE', $prefixeTable.'image_tag');
  define('PLUGINS_TABLE', $prefixeTable.'plugins');
  define('OLD_PERMALINKS_TABLE', $prefixeTable.'old_permalinks');
}

// Create empty local files to avoid log errors
function create_empty_local_files()
{
   $files =
      array (
         PHPWG_ROOT_PATH . 'template-common/local-layout.css',
         PHPWG_ROOT_PATH . 'template/yoga/local-layout.css'
         );

   foreach ($files as $path)
   {
      if (!file_exists ($path))
      {
         $file = @fopen($path, "w");
         @fwrite($file , '/* You can modify this file */');
         @fclose($file);
      }
   }
}

// Deactivate all non-standard plugins
function deactivate_non_standard_plugins()
{
  global $page;

  $standard_plugins = array(
    'add_index',
    'admin_advices',
    'admin_multi_view',
    'c13y_upgrade',
    'event_tracer',
    'language_switch',
    'LocalFilesEditor'
    );

  $query = '
SELECT id
FROM '.PREFIX_TABLE.'plugins
WHERE state = "active"
AND id NOT IN ("' . implode('","', $standard_plugins) . '")
;';

  $result = pwg_query($query);
  $plugins = array();
  while ($row = mysql_fetch_assoc($result))
  {
    array_push($plugins, $row['id']);
  }

  if (!empty($plugins))
  {
    $query = '
UPDATE '.PREFIX_TABLE.'plugins
SET state="inactive"
WHERE id IN ("' . implode('","', $plugins) . '")
;';
    mysql_query($query);

    array_push($page['infos'],
      l10n('deactivated plugins') . '<pre>' . implode(', ', $plugins) . '</pre>');
  }
}

?>
