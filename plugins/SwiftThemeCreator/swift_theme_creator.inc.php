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

/* Ajouter le lien au menu de l'admin */
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');
Class ThemeCreator
{
  var $theme_config;
  function ThemeCreator()
  {
    $this->theme_config = array();
  }
  function get_config_file_dir()
  {
    global $conf;
    return $conf['local_data_dir'].'/plugins/';
  }
  function get_config_file_name()
  {
    return basename(dirname(__FILE__)).'.dat';
  }
  function reload()
  {
    $x = @file_get_contents( $this->get_config_file_dir().$this->get_config_file_name() );
    if ($x!==false)
    {
      $y = unserialize($x);
      $this->theme_config = $y;
    }
  }
  function save_theme_config()
  {
    $dir = $this->get_config_file_dir();
    @mkdir($dir);
    $file = fopen( $dir.$this->get_config_file_name(), 'w' );
    fwrite($file, serialize($this->theme_config) );
    fclose( $file );
  }
  function plugin_admin_menu($menu)
  {
    array_push($menu,
        array(
          'NAME' => 'Swift Theme Creator',
          'URL' => get_admin_plugin_menu_link(dirname(__FILE__).'/theme_creator.php')
        )
      );
    return $menu;
  }  
  /**
   * returns available template/theme
   */
  function get_pwg_templates()
  {
    $templates = array();
    $template_dir = PHPWG_ROOT_PATH.'template';
    foreach (get_dirs($template_dir) as $template)
    {
      array_push($templates, $template);
    }
    return $templates;
  }
}
$swift_theme_creator = new ThemeCreator();
$swift_theme_creator->reload();
add_event_handler('get_admin_plugin_menu_links', 
                   array(&$swift_theme_creator, 'plugin_admin_menu') );
set_plugin_data($plugin['id'], $swift_theme_creator);
?>