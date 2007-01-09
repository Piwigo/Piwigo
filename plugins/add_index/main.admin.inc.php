<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// | Copyright (C) 2007 Ruben ARNAUD - team@phpwebgallery.net              |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2006-07-18 23:38:54 +0200 (mar., 18 juil. 2006) $
// | last modifier : $Author: rub $
// | revision      : $Revision: 1481 $
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

if ((!defined('PHPWG_ROOT_PATH')) or (!(defined('IN_ADMIN') and IN_ADMIN)))
{
  die('Hacking attempt!');
}

class AdminAddIndex extends AddIndex
{
  function load_params()
  {
    global $conf;
    // Name of index file (index.php or index.htm or index.html)
    if (!isset($conf['add_index_filename']))
    {
      $conf['add_index_filename'] = 'index.php';
    }
    // Name of index file (index.php or index.htm or index.html)
    if (!isset($conf['add_index_source_directory_path']))
    {
      // Name of the directoty use in order to copy index file
      $conf['add_index_source_directory_path'] = PHPWG_ROOT_PATH.'include/';
    }
  }

  function loading_lang()
  {
    global $lang;

    include(get_language_filepath('plugin.lang.php', $this->path));
  }

  function get_admin_advanced_features_links($advanced_features)
  {
    array_push($advanced_features,
      array
      (
        'CAPTION' => l10n('Advanced_Add_Index'),
        'URL' => get_root_url().'admin.php?page=main_page&amp;page_type=plugin&amp;plugin_id=add_index&amp;overwrite'
      ));

    return $advanced_features;
  }

  function get_admins_site_links($site_manager_plugin_links, $site_id, $is_remote)
  {
    if (!$is_remote)
    {
      array_push($site_manager_plugin_links,
        array
        (
          'U_HREF' => get_root_url().'admin.php?page=main_page&amp;page_type=plugin&amp;plugin_id=add_index&amp;site_id='.$site_id,
          'U_CAPTION' => l10n('Manager_Add_Index'),
          'U_HINT' => l10n('Add_Index')
        ));
    }

    return $site_manager_plugin_links;
  }

  function plugin_admin_menu()
  {
    add_plugin_admin_menu(l10n('Menu_Add_Index'), array(&$this, 'do_plugin_admin_menu'));
  }

  function do_plugin_admin_menu($my_url)
  {
    include_once(dirname(__FILE__).'/admin/'.'admin_menu.php');
  }

}

// Create object
$add_index = new AdminAddIndex();

// Load Add Index parameters
$add_index->load_params();

// Add events
add_event_handler('loading_lang', array(&$add_index, 'loading_lang'));
add_event_handler('get_admin_advanced_features_links', array(&$add_index, 'get_admin_advanced_features_links'));
add_event_handler('get_admins_site_links', array(&$add_index, 'get_admins_site_links'), EVENT_HANDLER_PRIORITY_NEUTRAL, 3);
add_event_handler('plugin_admin_menu', array(&$add_index, 'plugin_admin_menu') );

?>