<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
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

//----------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_GUEST);

//----------------------------------------------------- template initialization
//
// Start output of page
//
$title= l10n('About Piwigo');
$page['body_id'] = 'theAboutPage';

trigger_action('loc_begin_about');

$template->set_filename('about', 'about.tpl');

$template->assign('ABOUT_MESSAGE', load_language('about.html','', array('return'=>true)) );

$theme_about = load_language('about.html', PHPWG_THEMES_PATH.$user['theme'].'/', array('return' => true));
if ( $theme_about !== false )
{
  $template->assign('THEME_ABOUT', $theme_about);
}

// include menubar
$themeconf = $template->get_template_vars('themeconf');
if (!isset($themeconf['hide_menu_on']) OR !in_array('theAboutPage', $themeconf['hide_menu_on']))
{
  include( PHPWG_ROOT_PATH.'include/menubar.inc.php');
}

include(PHPWG_ROOT_PATH.'include/page_header.php');
$template->pparse('about');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
