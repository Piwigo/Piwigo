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

//----------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
//----------------------------------------------------- template initialization
//
// Start output of page
//
$title= $lang['about_page_title'];
$page['body_id'] = 'theAboutPage';
include(PHPWG_ROOT_PATH.'include/page_header.php');

$template->set_filenames(array('about'=>'about.tpl'));
$template->assign_vars(
  array(
    'U_HOME' => add_session_id(PHPWG_ROOT_PATH.'category.php')
    )
  );

// language files
$user_langdir = PHPWG_ROOT_PATH.'language/'.$user['language'];
$conf_langdir = PHPWG_ROOT_PATH.'language/'.$conf['default_language'];

if (file_exists($user_langdir.'/about.html'))
{
  $html_file = $user_langdir.'/about.html';
}
else
{
  $html_file = $conf_langdir.'/about.html';
}

$template->set_filenames(array('about_content' => $html_file));
$template->assign_var_from_handle('ABOUT_MESSAGE', 'about_content');
  
$template->parse('about');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
