<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2016 Piwigo Team                  http://piwigo.org |
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

//--------------------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_FREE);

trigger_notify('loc_begin_identification');

//-------------------------------------------------------------- identification
$redirect_to = '';
if ( !empty($_GET['redirect']) )
{
  $redirect_to = urldecode($_GET['redirect']);
  if ( is_a_guest() )
  {
    $page['errors'][] = l10n('You are not authorized to access the requested page');
  }
}

if (isset($_POST['login']))
{
  if (!isset($_COOKIE[session_name()]))
  {
    $page['errors'][] = l10n('Cookies are blocked or not supported by your browser. You must enable cookies to connect.');
  }
  else
  {
    if ($conf['insensitive_case_logon'] == true)
    {
      $_POST['username'] = search_case_username($_POST['username']);
    }
    
    $redirect_to = isset($_POST['redirect']) ? urldecode($_POST['redirect']) : '';
    $remember_me = isset($_POST['remember_me']) and $_POST['remember_me']==1;

    if ( try_log_user($_POST['username'], $_POST['password'], $remember_me) )
    {
      redirect(empty($redirect_to) ? get_gallery_home_url() : $redirect_to);
    }
    else
    {
      $page['errors'][] = l10n('Invalid username or password!');
    }
  }
}

//----------------------------------------------------- template initialization
//
// Start output of page
//
$title = l10n('Identification');
$page['body_id'] = 'theIdentificationPage';

$template->set_filenames( array('identification'=>'identification.tpl') );

$template->assign(
  array(
    'U_REDIRECT' => $redirect_to,

    'F_LOGIN_ACTION' => get_root_url().'identification.php',
    'authorize_remembering' => $conf['authorize_remembering'],
    ));

if (!$conf['gallery_locked'] && $conf['allow_user_registration'])
{
  $template->assign('U_REGISTER', get_root_url().'register.php' );
}

if (!$conf['gallery_locked'])
{
  $template->assign('U_LOST_PASSWORD', get_root_url().'password.php' );
}

// include menubar
$themeconf = $template->get_template_vars('themeconf');
if (!$conf['gallery_locked'] && (!isset($themeconf['hide_menu_on']) OR !in_array('theIdentificationPage', $themeconf['hide_menu_on'])))
{
  include( PHPWG_ROOT_PATH.'include/menubar.inc.php');
}

//----------------------------------------------------------- html code display
include(PHPWG_ROOT_PATH.'include/page_header.php');
trigger_notify('loc_end_identification');
flush_page_messages();
$template->pparse('identification');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
