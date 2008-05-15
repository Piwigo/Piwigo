<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
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

/*
Plugin Name: Hello World
Version: 1.8
Description: This example plugin changes the page banner for the administration page.
Plugin URI: http://piwigo.org
Author: Piwigo team
Author URI: http://piwigo.org
*/

add_event_handler('loc_begin_page_header', 'hello_world_begin_header' );

function hello_world_begin_header()
{
  global $page;
  if ( isset($page['body_id']) and $page['body_id']=='theAdminPage')
  {
    $hellos = array( 'Aloha', 'Ahoy', 'Guten tag', 'Hello', 'Hoi', 'Hola', 'Salut', 'Yo' );
    shuffle($hellos);
    $page['page_banner'] = $hellos[0];
    // just as an example we modify it a little bit later
    add_event_handler('loc_end_page_header', 'hello_world_end_header');
  }
}


function hello_world_end_header()
{
  global $template, $page;
  $template->assign( 'PAGE_BANNER',
    '<h1>"'.$page['page_banner'].'" from Piwigo plugin!</h1>');
}

?>