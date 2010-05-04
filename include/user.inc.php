<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
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

// by default we start with guest
$user['id'] = $conf['guest_id'];

if (isset($_COOKIE[session_name()]))
{
  session_start();
  if (isset($_GET['act']) and $_GET['act'] == 'logout')
  { // logout
    logout_user();
    redirect(make_index_url());
  }
  elseif (!empty($_SESSION['pwg_uid']))
  {
    $user['id'] = $_SESSION['pwg_uid'];
  }
}

// Now check the auto-login
if ( $user['id']==$conf['guest_id'] )
{
  auto_login();
}

if (session_id()=="")
{
  session_start();
}

// using Apache authentication override the above user search
if ($conf['apache_authentication'])
{
  $remote_user = null;
  foreach (array('REMOTE_USER', 'REDIRECT_REMOTE_USER') as $server_key)
  {
    if (isset($_SERVER[$server_key]))
    {
      $remote_user = $_SERVER[$server_key];
      echo $server_key;
      break;
    }
  }

  if (isset($remote_user))
  {
    if (!($user['id'] = get_userid($remote_user)))
    {
      register_user($remote_user, '', '', false);
      $user['id'] = get_userid($remote_user);
    }
  }
}

$user = build_user( $user['id'],
          ( defined('IN_ADMIN') and IN_ADMIN ) ? false : true // use cache ?
         );
if ($conf['browser_language'] and (is_a_guest() or is_generic()) )
{
  get_browser_language($user['language']);
}
trigger_action('user_init', $user);
?>
