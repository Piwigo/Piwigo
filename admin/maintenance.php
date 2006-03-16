<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
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

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action)
{
  case 'categories' :
  {
    check_links();
    update_uppercats();
    update_category('all');
    ordering();
    update_global_rank();
    break;
  }
  case 'images' :
  {
    update_path();
    update_average_rate();
    break;
  }
  case 'history' :
  {
    $query = '
DELETE
  FROM '.HISTORY_TABLE.'
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
  default :
  {
    break;
  }
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(array('maintenance'=>'admin/maintenance.tpl'));

$start_url = PHPWG_ROOT_PATH.'admin.php?page=maintenance&amp;action=';

if (!is_adviser())
{
  $template->assign_vars(
    array(
      'U_MAINT_CATEGORIES' => $start_url.'categories',
      'U_MAINT_IMAGES' => $start_url.'images',
      'U_MAINT_HISTORY' => $start_url.'history',
      'U_MAINT_SESSIONS' => $start_url.'sessions',
      'U_MAINT_FEEDS' => $start_url.'feeds',
      'U_HELP' => PHPWG_ROOT_PATH.'/popuphelp.php?page=maintenance',
      )
    );
}
else
{
  $template->assign_vars(
    array(
      'U_MAINT_CATEGORIES' => $start_url,
      'U_MAINT_IMAGES' => $start_url,
      'U_MAINT_HISTORY' => $start_url,
      'U_MAINT_SESSIONS' => $start_url,
      'U_MAINT_FEEDS' => $start_url,
      'U_HELP' => PHPWG_ROOT_PATH.'/popuphelp.php?page=maintenance',
      )
    );
}

// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'maintenance');
?>