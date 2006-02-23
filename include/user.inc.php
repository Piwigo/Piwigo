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

// retrieving connected user informations
if (isset($_COOKIE[session_name()])) 
{
 session_start();
 if (isset($_SESSION['id'])) 
 {
   $user['id'] = $_SESSION['id'];    
   $user['is_the_guest'] = false;
 }
 else 
 {
   // session timeout
   $user['id'] = $conf['guest_id'];
   $user['is_the_guest'] = true;
 }
} 
else 
{
 $user['id'] = $conf['guest_id'];
 $user['is_the_guest'] = true;
}

// using Apache authentication override the above user search
if ($conf['apache_authentication'] and isset($_SERVER['REMOTE_USER']))
{
  if (!($user['id'] = get_userid($_SERVER['REMOTE_USER'])))
  {
    register_user($_SERVER['REMOTE_USER'], '', '');
    $user['id'] = get_userid($_SERVER['REMOTE_USER']);
  }
  
  $user['is_the_guest'] = false;
}
$user = array_merge(
  $user,
  getuserdata(
    $user['id'],
    ( defined('IN_ADMIN') and IN_ADMIN ) ? false : true // use cache ?
    )
  );

// properties of user guest are found in the configuration
if ($user['is_the_guest'])
{
  $user['template'] = $conf['default_template'];
  $user['nb_image_line'] = $conf['nb_image_line'];
  $user['nb_line_page'] = $conf['nb_line_page'];
  $user['language'] = $conf['default_language'];
  $user['maxwidth'] = $conf['default_maxwidth'];
  $user['maxheight'] = $conf['default_maxheight'];
  $user['recent_period'] = $conf['recent_period'];
  $user['expand'] = $conf['auto_expand'];
  $user['show_nb_comments'] = $conf['show_nb_comments'];
}

// calculation of the number of picture to display per page
$user['nb_image_page'] = $user['nb_image_line'] * $user['nb_line_page'];
?>
