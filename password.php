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

// +-----------------------------------------------------------------------+
// |                           initialization                              |
// +-----------------------------------------------------------------------+

define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_FREE);

// +-----------------------------------------------------------------------+
// |                          send a new password                          |
// +-----------------------------------------------------------------------+

$page['errors'] = array();
$page['infos'] = array();

if (isset($_POST['submit']))
{
  $mailto =
    '<a href="mailto:'.get_webmaster_mail_address().'">'
    .l10n('Contact webmaster')
    .'</a>'
    ;

  if (isset($_POST['no_mail_address']) and $_POST['no_mail_address'] == 1)
  {
    array_push($page['infos'], l10n('Email address is missing. Please specify an email address.'));
    array_push($page['infos'], $mailto);
  }
  else if (isset($_POST['mail_address']) and !empty($_POST['mail_address']))
  {
    $mail_address = pwg_db_real_escape_string($_POST['mail_address']);
    
    $query = '
SELECT '.$conf['user_fields']['id'].' AS id
     , '.$conf['user_fields']['username'].' AS username
     , '.$conf['user_fields']['email'].' AS email
FROM '.USERS_TABLE.' as u
  INNER JOIN '.USER_INFOS_TABLE.' AS ui
      ON u.'.$conf['user_fields']['id'].' = ui.user_id
WHERE '.$conf['user_fields']['email'].' = \''.$mail_address.'\'
  AND ui.status = \'normal\'
;';
    $result = pwg_query($query);

    if (pwg_db_num_rows($result) > 0)
    {
      $error_on_mail = false;
      $datas = array();
      
      while ($row = pwg_db_fetch_assoc($result))
      {
        $new_password = generate_key(6);

        $infos =
          l10n('Username').': '.stripslashes($row['username'])
          ."\n".l10n('Password').': '.$new_password
          ;

        $infos = trigger_event('render_lost_password_mail_content', $infos);

        if (pwg_mail($row['email'],
              array('subject' => l10n('password updated'), 'content' => $infos)))
        {
          $data =
            array(
              $conf['user_fields']['id']
              => $row['id'],
              
              $conf['user_fields']['password']
              => $conf['pass_convert']($new_password)
              );

          array_push($datas, $data);
        }
        else
        {
          $error_on_mail = true;
        }
      }
      
      if ($error_on_mail)
      {
        array_push($page['errors'], l10n('Error sending email'));
        array_push($page['errors'], $mailto);
      }
      else
      {
        include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
        mass_updates(
          USERS_TABLE,
          array(
            'primary' => array($conf['user_fields']['id']),
            'update' => array($conf['user_fields']['password'])
          ),
          $datas
          );

        array_push($page['infos'], l10n('New password sent by email'));
      }
    }
    else
    {
      array_push($page['errors'], l10n('No classic user matches this email address'));
      array_push($page['errors'], l10n('Administrator, webmaster and special user cannot use this method'));
      array_push($page['errors'], $mailto);
    }
  }
}

// +-----------------------------------------------------------------------+
// |                        template initialization                        |
// +-----------------------------------------------------------------------+

$title = l10n('Forgot your password?');
$page['body_id'] = 'thePasswordPage';

$template->set_filenames(array('password'=>'password.tpl'));
$template->assign( array(
    'F_ACTION'=> get_root_url().'password.php'
    )
  );
// +-----------------------------------------------------------------------+
// |                        infos & errors display                         |
// +-----------------------------------------------------------------------+
$template->assign('errors', $page['errors']);
$template->assign('infos', $page['infos']);

// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+
include(PHPWG_ROOT_PATH.'include/page_header.php');
$template->pparse('password');
include(PHPWG_ROOT_PATH.'include/page_tail.php');

?>
