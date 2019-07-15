<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

//--------------------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_FREE);

// but if the user is already identified, we redirect to gallery home
// instead of displaying the log in form
if (!is_a_guest())
{
  redirect(get_gallery_home_url());
}

trigger_notify('loc_begin_identification');

//-------------------------------------------------------------- identification

// security (level 1): the redirect must occur within Piwigo, so the
// redirect param must start with the relative home url
if (isset($_POST['redirect']))
{
  $_POST['redirect_decoded'] = urldecode($_POST['redirect']);
}
check_input_parameter('redirect_decoded', $_POST, false, '{^'.preg_quote(cookie_path()).'}');

$redirect_to = '';
if ( !empty($_GET['redirect']) )
{
  $redirect_to = urldecode($_GET['redirect']);
  if ( $conf['guest_access'] and !isset($_GET['hide_redirect_error']))
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
      // security (level 2): force redirect within Piwigo. We redirect to
      // absolute root url, including http(s)://, without the cookie path,
      // concatenated with $_POST['redirect'] param.
      //
      // example:
      // {redirect (raw) = /piwigo/git/admin.php}
      // {get_absolute_root_url = http://localhost/piwigo/git/}
      // {cookie_path = /piwigo/git/}
      // {host = http://localhost}
      // {redirect (final) = http://localhost/piwigo/git/admin.php}
      $root_url = get_absolute_root_url();

      redirect(
        empty($redirect_to)
          ? get_gallery_home_url()
          : substr($root_url, 0, strlen($root_url) - strlen(cookie_path())).$redirect_to
        );
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
