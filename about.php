<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2008 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | file          : $Id$
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

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_GUEST);

//----------------------------------------------------- template initialization
//
// Start output of page
//
$title= l10n('about_page_title');
$page['body_id'] = 'theAboutPage';
include(PHPWG_ROOT_PATH.'include/page_header.php');

/**
 * set in ./language/en_UK.iso-8859-1/local.lang.php (maybe to create)
 * for example for clear theme:
  $lang['Theme: clear'] = 'This is the clear theme based on yoga template. '.
  ' A standard template/theme of PhpWebgallery.';
 *
 * Don't forget php tags !!!
 *
 * Another way is to code it thru the theme itself in ./themeconf.inc.php
 */
@include(PHPWG_ROOT_PATH.'template/'.$user['template'].
  '/theme/'.$user['theme'].'/themeconf.inc.php');

$template->set_filenames(
  array(
    'about'=>'about.tpl',
    )
  );
if ( isset($lang['Theme: '.$user['theme']]) )
{
  $template->assign(
    'THEME_ABOUT',l10n('Theme: '.$user['theme'])
    );
}

$template->assign('ABOUT_MESSAGE', load_language('about.html','','',true) );

$template->pparse('about');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
