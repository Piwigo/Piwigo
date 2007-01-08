<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// | Copyright (C) 2006-2007 Ruben ARNAUD - team@phpwebgallery.net              |
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
  function loading_lang()
  {
    global $lang;

    include(get_language_filepath('plugin.lang.php', $this->path));
  }

  function array_advanced_features($advanced_features)
  {
    array_push($advanced_features,
      array
      (
        'CAPTION' => l10n('Advanced_Add_Index'),
        'URL' => get_root_url().'admin.php?page=main_page&page_type=plugin&plugin_id=add_index&overwrite'
      ));

    return $advanced_features;
  }

  function array_site_manager_plugin_links($site_manager_plugin_links, $site_id, $is_remote)
  {
    if (!$is_remote)
    {
      array_push($site_manager_plugin_links,
        array
        (
          'U_HREF' => get_root_url().'admin.php?page=main_page&page_type=plugin&plugin_id=add_index&site_id='.$site_id,
          'U_CAPTION' => l10n('Manager_Add_Index'),
          'U_HINT' => l10n('Add_Index')
        ));
    }

    return $site_manager_plugin_links;
  }
}

$add_index = new AdminAddIndex();

add_event_handler('loading_lang', array(&$add_index, 'loading_lang'));
add_event_handler('array_advanced_features', array(&$add_index, 'array_advanced_features'));
add_event_handler('array_site_manager_plugin_links', array(&$add_index, 'array_site_manager_plugin_links'), EVENT_HANDLER_PRIORITY_NEUTRAL, 3);

?>