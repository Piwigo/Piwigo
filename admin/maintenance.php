<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
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

if (!defined('PHPWG_ROOT_PATH'))
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// |                                actions                                |
// +-----------------------------------------------------------------------+

$action = (isset($_GET['action']) and !is_adviser()) ? $_GET['action'] : '';

switch ($action)
{
  case 'categories' :
  {
    update_uppercats();
    update_category('all');
    update_global_rank();
    invalidate_user_cache(true);
    break;
  }
  case 'images' :
  {
    update_path();
    update_average_rate();
    break;
  }
  case 'history_detail' :
  {
    $query = '
DELETE
  FROM '.HISTORY_TABLE.'
;';
    pwg_query($query);
    break;
  }
  case 'history_summary' :
  {
    $query = '
DELETE
  FROM '.HISTORY_SUMMARY_TABLE.'
;';
    pwg_query($query);
    break;
  }
  case 'sessions' :
  {
    pwg_session_gc();
    break;
  }
  case 'feeds' :
  {
    $query = '
DELETE
  FROM '.USER_FEED_TABLE.'
  WHERE last_check IS NULL
;';
    pwg_query($query);
    break;
  }
  case 'database' :
  {
    do_maintenance_all_tables();
    break;
  }
  case 'c13y' :
  {
    include_once(PHPWG_ROOT_PATH.'admin/include/check_integrity.class.php');
    $c13y = new check_integrity();
    $c13y->maintenance();
    break;
  }
  case 'compiled-templates' :
  {
    $template->delete_compiled_templates();
    break;
  }
  default :
  {
    break;
  }
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(array('maintenance'=>'maintenance.tpl'));

$start_url = get_root_url().'admin.php?page=maintenance&amp;action=';

$template->assign(
  array(
    'U_MAINT_CATEGORIES' => $start_url.'categories',
    'U_MAINT_IMAGES' => $start_url.'images',
    'U_MAINT_HISTORY_DETAIL' => $start_url.'history_detail',
    'U_MAINT_HISTORY_SUMMARY' => $start_url.'history_summary',
    'U_MAINT_SESSIONS' => $start_url.'sessions',
    'U_MAINT_FEEDS' => $start_url.'feeds',
    'U_MAINT_DATABASE' => $start_url.'database',
    'U_MAINT_C13Y' => $start_url.'c13y',
    'U_MAINT_COMPILED_TEMPLATES' => $start_url.'compiled-templates',
    'U_HELP' => PHPWG_ROOT_PATH.'popuphelp.php?page=maintenance',
    )
  );

// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'maintenance');
?>