<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// | Copyright (C) 2006 Ruben ARNAUD - team@phpwebgallery.net              |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2005-09-21 00:04:57 +0200 (mer, 21 sep 2005) $
// | last modifier : $Author: plg $
// | revision      : $Revision: 870 $
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
// | include
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'include/common.inc.php');
include_once(PHPWG_ROOT_PATH.'include/functions_notification.inc.php');
include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// | functions
// +-----------------------------------------------------------------------+
/*
 * Search an available check_key
 *
 * It's a copy of function find_available_feed_id
 *
 * @return string feed identifier
 */
function find_available_check_key()
{
  while (true)
  {
    $key = generate_key(128);
    $query = '
select
  count(*)
from
  '.USER_MAIL_NOTIFICATION_TABLE.'
where
  check_key = \''.$key.'\';';

    list($count) = mysql_fetch_row(pwg_query($query));
    if ($count == 0)
    {
      return $key;
    }
  }
}

/*
 * Updating News users
 */
function update_data_user_mail_notification()
{
  global $conf, $page;

  // Set null mail_address empty
  $query = '
update 
  '.USERS_TABLE.'
set
  mail_address = null
where
  trim(mail_address) = \'\';';
  pwg_query($query);

  $query = '
select
  u.id user_id, u.username, u.mail_address
from
  '.USERS_TABLE.' as u left join '.USER_MAIL_NOTIFICATION_TABLE.' as m on u.id = m.user_id
where
  u.mail_address is not null and
  m.user_id is null
order by
  id;';

  $result = pwg_query($query);

  if (mysql_num_rows($result) > 0)
  {
    $inserts = array();

    while ($row = mysql_fetch_array($result))
    {
      array_push($inserts, array('user_id' => $row['user_id'],
                                 'check_key' => find_available_check_key(),
                                 'enabled' => ($conf['default_value_user_mail_notification_enabled'] == true ? 'true' : 'false')));
      array_push($page['infos'], sprintf(l10n('nbm_User %s [%s] added.'), $row['username'], $row['mail_address']));
    }

    mass_inserts(USER_MAIL_NOTIFICATION_TABLE, array('user_id', 'check_key', 'enabled'), $inserts);
  }
}

/*
 * Updating News users
 */
function send_all_user_mail_notification()
{
  global $conf, $conf_mail, $page, $user, $lang_info, $lang;
  list($dbnow) = mysql_fetch_row(pwg_query('SELECT NOW();'));

  $query = '
select
  N.user_id, U.username, U.mail_address, N.last_send
from
  '.USER_MAIL_NOTIFICATION_TABLE.' as N,
  '.USERS_TABLE.' as U
where
  N.user_id =  U.id and
  N.enabled = \'true\' and
  U.mail_address is not null
order by
  user_id;';

  $result = pwg_query($query);

  if (mysql_num_rows($result) > 0)
  {
    $error_on_mail_count = 0;
    $sended_mail_count = 0;
    $datas = array();
    // Save $user, $lang_info and $lang arrays (include/user.inc.php has been executed)
    $sav_mailtousers_user = $user;
    $sav_mailtousers_lang_info = $lang_info;
    $sav_mailtousers_lang = $lang;
    // Save message info and error in the original language
    $msg_info = l10n('nbm_Mail sended to %s [%s].');
    $msg_error = l10n('nbm_Error when sending email to %s [%s].');
    // Last Language
    $last_mailtousers_language = $user['language'];

    while ($row = mysql_fetch_array($result))
    {
      $user = array();
      $user['id'] = $row['user_id'];
      $user = array_merge($user, getuserdata($user['id'], true));

      if ($last_mailtousers_language != $user['language'])
      {
        $last_mailtousers_language = $user['language'];

        // Re-Init language arrays
        $lang_info = array();
        $lang  = array();

        // language files
        include(get_language_filepath('common.lang.php'));
        // No test admin because script is checked admin (user selected no)
        // Translations are in admin file too
        include(get_language_filepath('admin.lang.php'));
      }

      $message = '';
      $news = news($row['last_send'], $dbnow);
      if (count($news) > 0)
      {
        $subject = '['.$conf['gallery_title'].']: '.l10n('nbm_New elements added');
        $message .= sprintf(l10n('nbm_Hello %s'), $row['username']).",\n\n";

        if (!is_null($row['last_send']))
          $message .= sprintf(l10n('nbm_New elements were added between %s and %s:'), $row['last_send'], $dbnow);
        else
          $message .= sprintf(l10n('nbm_New elements were added on %s:'), $dbnow);
        $message .= "\n";

        foreach ($news as $line)
        {
          $message .= '  o '.$line."\n";
        }

        $message .= "\n".sprintf(l10n('nbm_Go to %s %s.'), $conf['gallery_title'], $conf['gallery_url'])."\n\n";
        $message .= "\n".sprintf(l10n('nbm_To unsubscribe send a message to %s.'), $conf_mail['email_webmaster'])."\n\n";

        if (pwg_mail(format_email($row['username'], $row['mail_address']), '', $subject, $message))
        {
          $sended_mail_count += 1;
          array_push($page['infos'], sprintf($msg_info, $row['username'], $row['mail_address']));
          $data = array('user_id' => $row['user_id'],
                        'last_send' => $dbnow);
          array_push($datas, $data);
        }
        else
        {
          $error_on_mail_count += 1;
          array_push($page['errors'], sprintf($msg_error, $row['username'], $row['mail_address']));
        }
      }
    }

    // Restore $user, $lang_info and $lang arrays (include/user.inc.php has been executed)
    $user = $sav_mailtousers_user;
    $lang_info = $sav_mailtousers_lang_info;
    $lang = $sav_mailtousers_lang;

    mass_updates(
      USER_MAIL_NOTIFICATION_TABLE,
      array(
        'primary' => array('user_id'),
        'update' => array('last_send')
       ),
       $datas
       );

    if ($error_on_mail_count != 0)
    {
      array_push($page['errors'], sprintf(l10n('nbm_%d mails were not sended.'), $error_on_mail_count));
    }
    else
    {
      if ($sended_mail_count == 0)
        array_push($page['infos'], l10n('nbm_No mail to send.'));
      else
        array_push($page['infos'], sprintf(l10n('nbm_%d mails were sended.'), $sended_mail_count));
    }
  }
  else
  {
    array_push($page['errors'], l10n('nbm_No user to send notifications by mail.'));
  }
}

// +-----------------------------------------------------------------------+
// | Main
// +-----------------------------------------------------------------------+
update_data_user_mail_notification();
send_all_user_mail_notification();


// +-----------------------------------------------------------------------+
// |                        template initialization                        |
// +-----------------------------------------------------------------------+

$title = l10n('nbm_Send mail to users');

// +-----------------------------------------------------------------------+
// |                        infos & errors display                         |
// +-----------------------------------------------------------------------+

/*echo '<pre>';

if (count($page['errors']) != 0)
{
  echo "\n\nErrors:\n";
  foreach ($page['errors'] as $error)
  {
    echo $error."\n";
  }
}

if (count($page['infos']) != 0)
{
  echo "\n\nInformations:\n";
  foreach ($page['infos'] as $info)
  {
    echo $info."\n";
  }
}

echo '</pre>';
*/
?>