<?php
/*
Plugin Name: Add Index
Version: 1.1.0.0
Description: Add file index.php file on all sub-directories of local galleries pictures. / Ajoute le fichier index.php sur les sous-répertoires de galeries d'images locales.
Plugin URI: http://www.phpwebgallery.net
*/
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

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

class AddIndex
{
  var $path;
  
  function AddIndex()
  {
    $this->path = dirname(__FILE__).'/';
  }

  function get_popup_help_content($popup_help_content, $page)
  {
    $help_content =
      @file_get_contents(get_language_filepath('help/'.$page.'.html', $this->path));
      if ($help_content == false)
      {
        return $popup_help_content;
      }
      else
      {
        return $popup_help_content.$help_content;
      }
  }
}

if (defined('IN_ADMIN') and IN_ADMIN)
{
  include_once(dirname(__FILE__).'/'.'main.admin.inc.php');
}
else
{
  $add_index = new AddIndex();
  add_event_handler('get_popup_help_content', array(&$add_index, 'get_popup_help_content'), EVENT_HANDLER_PRIORITY_NEUTRAL, 2);
}

?>