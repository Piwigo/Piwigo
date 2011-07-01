<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery team    http://phpwebgallery.net |
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

/*
Plugin Name: LocalFiles Editor
Version: 2.3.0
Description: Edit local files from administration panel
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=144
Author: Piwigo team
Author URI: http://piwigo.org
*/

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');
define('LOCALEDIT_PATH' , PHPWG_PLUGINS_PATH . basename(dirname(__FILE__)) . '/');

function localfiles_admin_menu($menu)
{
  array_push(
    $menu,
    array(
      'NAME' => 'LocalFiles Editor',
      'URL' => get_root_url().'admin.php?page=plugin-'.basename(dirname(__FILE__))
      )
    );
  
  return $menu;
}

function localfiles_css_link()
{
  global $template;
  
  $template->set_prefilter('themes', 'localfiles_css_link_prefilter');
}

function localfiles_css_link_prefilter($content, &$smarty)
{
  $search = '#{if isset\(\$theme.admin_uri\)}.*?{/if}#s';
  $replacement = '
{if isset($theme.admin_uri)}
      <br><a href="{$theme.admin_uri}" title="{\'Configuration\'|@translate}">{\'Configuration\'|@translate}</a>
      | <a href="admin.php?page=plugin-LocalFilesEditor-css&amp;theme={$theme.id}">CSS</a>
{else}
      <br><a href="admin.php?page=plugin-LocalFilesEditor-css&amp;theme={$theme.id}">CSS</a>
{/if}
';

  return preg_replace($search, $replacement, $content);
}

add_event_handler('get_admin_plugin_menu_links', 'localfiles_admin_menu');
add_event_handler('loc_begin_admin', 'localfiles_css_link');
?>