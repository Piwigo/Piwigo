<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
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

function check_upgrade()
{
  // Is PhpWebGallery already installed ?
  if (!defined('PHPWG_IN_UPGRADE') or !PHPWG_IN_UPGRADE)
  {
    $message = 'PhpWebGallery is not in upgrade mode. In include/mysql.inc.php,
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
  global $conf, $prefixeTable;

  $conf['tables']['categories_table'] = $prefixeTable.'categories';
  $conf['tables']['comments_table'] = $prefixeTable.'comments';
  $conf['tables']['config_table'] = $prefixeTable.'config';
  $conf['tables']['favorites_table'] = $prefixeTable.'favorites';
  $conf['tables']['group_access_table'] = $prefixeTable.'group_access';
  $conf['tables']['groups_table'] = $prefixeTable.'groups';
  $conf['tables']['history_table'] = $prefixeTable.'history';
  $conf['tables']['history_summary_table'] = $prefixeTable.'history_summary';
  $conf['tables']['image_category_table'] = $prefixeTable.'image_category';
  $conf['tables']['images_table'] = $prefixeTable.'images';
  $conf['tables']['sessions_table'] = $prefixeTable.'sessions';
  $conf['tables']['sites_table'] = $prefixeTable.'sites';
  $conf['tables']['user_access_table'] = $prefixeTable.'user_access';
  $conf['tables']['user_group_table'] = $prefixeTable.'user_group';
  $conf['tables']['users_table'] = $prefixeTable.'users';
  $conf['tables']['user_infos_table'] = $prefixeTable.'user_infos';
  $conf['tables']['user_feed_table'] = $prefixeTable.'user_feed';
  $conf['tables']['waiting_table'] = $prefixeTable.'waiting';
  $conf['tables']['image_metadata_table'] = $prefixeTable.'image_metadata';
  $conf['tables']['rate_table'] = $prefixeTable.'rate';
  $conf['tables']['user_cache_table'] = $prefixeTable.'user_cache';
  $conf['tables']['user_cache_categories_table'] = $prefixeTable.'user_cache_categories';
  $conf['tables']['caddie_table'] = $prefixeTable.'caddie';
  $conf['tables']['upgrade_table'] = $prefixeTable.'upgrade';
  $conf['tables']['search_table'] = $prefixeTable.'search';
  $conf['tables']['user_mail_notification_table'] = $prefixeTable.'user_mail_notification';
  $conf['tables']['tags_table'] = $prefixeTable.'tags';
  $conf['tables']['image_tag_table'] = $prefixeTable.'image_tag';
  $conf['tables']['plugins_table'] = $prefixeTable.'plugins';
  $conf['tables']['web_services_access_table'] = $prefixeTable.'ws_access';
  $conf['tables']['old_permalinks_table'] = $prefixeTable.'old_permalinks';
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

?>
