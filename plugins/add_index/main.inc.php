<?php /*
Plugin Name: Add Index
Version: 1.0
Description: Add file index.php file on all sub-directories of local galleries pictures. / Ajoute le fichier index.php sur les sous-répertoires de galeries d'images locales.
Plugin URI: http://www.phpwebgallery.net
Author: PhpWebGallery team
Author URI: http://www.phpwebgallery.net
*/
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// | Copyright (C) 2007 Ruben ARNAUD - team@phpwebgallery.net              |
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
  die('Hacking attempt!');
}

if (in_array(script_basename(), array('popuphelp', 'admin')))
{
  if (defined('IN_ADMIN') and IN_ADMIN)
  {
    include_once(dirname(__FILE__).'/'.'main.base.inc.php');
    include_once(dirname(__FILE__).'/'.'main.admin.inc.php');
  }
  else
  {
    include_once(dirname(__FILE__).'/'.'main.base.inc.php');
    include_once(dirname(__FILE__).'/'.'main.normal.inc.php');
  }
  set_plugin_data($plugin['id'], $add_index);
}

?>