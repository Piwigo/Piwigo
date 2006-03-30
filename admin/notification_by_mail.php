<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// | Copyright (C) 2006 Ruben ARNAUD - team@phpwebgallery.net              |
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

// +-----------------------------------------------------------------------+
// | include                                                               |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_notification_by_mail.inc.php');
include_once(PHPWG_ROOT_PATH.'include/common.inc.php');
include_once(PHPWG_ROOT_PATH.'include/functions_notification.inc.php');
include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// | functions                                                             |
// +-----------------------------------------------------------------------+

/*
 * Get the authorized_status for each tab
 * return corresponding status
 */
function get_tab_status($mode)
{
  $result = ACCESS_WEBMASTER;
  switch ($mode)
  {
    case 'param':
    case 'subscribe':
      $result = ACCESS_WEBMASTER;
      break;
    case 'send':
      $result = ACCESS_ADMINISTRATOR;
      break;
    default:
      $result = ACCESS_WEBMASTER;
      break;
  }
  return $result;
}

/*
 * Execute all main queries to get list of user 
 *
 * Type are the type of list 'subscribe', 'send'
 *
 * return array of users
 */
function get_user_notifications($action, $check_key_list = array())
{
  global $conf;

  $data_users = array();

  if (in_array($action, array('subscribe', 'send')))
  {
    $quoted_check_key_list = quote_check_key_list($check_key_list);
    if (count($quoted_check_key_list) != 0 )
    {
      $query_and_check_key = ' and 
    check_key in ('.implode(",", $quoted_check_key_list).') ';
    }
    else
    {
      $query_and_check_key = '';
    }

    $query = '
select
  N.user_id,
  N.check_key,
  U.'.$conf['user_fields']['username'].' as username,
  U.'.$conf['user_fields']['email'].' as mail_address,
  N.enabled,
  N.last_send
from
  '.USER_MAIL_NOTIFICATION_TABLE.' as N,
  '.USERS_TABLE.' as U
where
  N.user_id =  U.'.$conf['user_fields']['id'];
  
    if ($action == 'send')
    {
      $query .= ' and
  N.enabled = \'true\' and
  U.'.$conf['user_fields']['email'].' is not null'.$query_and_check_key;
    }

    $query .= '
order by
  username;';

    $result = pwg_query($query);
    if (!empty($result))
    {
      while ($nbm_user = mysql_fetch_array($result))
      {
        array_push($data_users, $nbm_user);
      }
    }
  }
  return $data_users;
}

/*
 * Inserting News users
 */
function insert_new_data_user_mail_notification()
{
  global $conf, $page;

  // Set null mail_address empty
  $query = '
update
  '.USERS_TABLE.'
set
  '.$conf['user_fields']['email'].' = null
where
  trim('.$conf['user_fields']['email'].') = \'\';';
  pwg_query($query);

  // null mail_address are not selected in the list
  $query = '
select
  u.'.$conf['user_fields']['id'].' as user_id,
  u.'.$conf['user_fields']['username'].' as username,
  u.'.$conf['user_fields']['email'].' as mail_address
from
  '.USERS_TABLE.' as u left join '.USER_MAIL_NOTIFICATION_TABLE.' as m on u.'.$conf['user_fields']['id'].' = m.user_id
where
  u.'.$conf['user_fields']['email'].' is not null and
  m.user_id is null
order by
  user_id;';

  $result = pwg_query($query);

  if (mysql_num_rows($result) > 0)
  {
    $inserts = array();
    $check_key_list = array();

    while ($nbm_user = mysql_fetch_array($result))
    {
      // Calculate key
      $nbm_user['check_key'] = find_available_check_key();

      // Save key
      array_push($check_key_list, $nbm_user['check_key']);

      // Insert new nbm_users
      array_push
      (
        $inserts, 
        array
        (
          'user_id' => $nbm_user['user_id'],
          'check_key' => $nbm_user['check_key'],
          'enabled' => 'false' // By default if false, set to true with specific functions
        )
      );

      array_push($page['infos'], sprintf(l10n('nbm_User %s [%s] added.'), $nbm_user['username'], $nbm_user['mail_address']));
    }

    // Insert new nbm_users
    mass_inserts(USER_MAIL_NOTIFICATION_TABLE, array('user_id', 'check_key', 'enabled'), $inserts);
    // Update field enabled with specific function
    do_subscribe_unsubcribe_notification_by_mail
    (
      ($conf['default_value_user_mail_notification_enabled'] == true ? true : false),
      $check_key_list
    );
  }
}

/*
 * Send mail for notification to all users
 * Return list of "treated/selected" users
 */
function do_action_send_mail_notification($action = 'list', $check_key_list = array(), $customize_mail_content = '')
{
  global $conf, $page, $user, $lang_info, $lang;
  $return_list = array();
  
  if (in_array($action, array('list', 'send')))
  {
    list($dbnow) = mysql_fetch_row(pwg_query('SELECT NOW();'));

    $is_action_send = ($action == 'send');

    if (isset($customize_mail_content))
    {
      $customize_mail_content = $conf['nbm_complementary_mail_content'];
    }

    // disabled and null mail_address are not selected in the list
    $data_users = get_user_notifications('send', $check_key_list);

    if (count($data_users) > 0)
    {
      $error_on_mail_count = 0;
      $sent_mail_count = 0;
      // Save $user, $lang_info and $lang arrays (include/user.inc.php has been executed)
      $sav_mailtousers_user = $user;
      $sav_mailtousers_lang_info = $lang_info;
      $sav_mailtousers_lang = $lang;
      // Save message info and error in the original language
      $msg_info = l10n('nbm_Mail sent to %s [%s].');
      $msg_error = l10n('nbm_Error when sending email to %s [%s].');
      // Last Language
      $last_mailtousers_language = $user['language'];

      if ($is_action_send)
      {
        // Init mail configuration
        $send_as_name = ((isset($conf['nbm_send_mail_as']) and !empty($conf['nbm_send_mail_as'])) ? $conf['nbm_send_mail_as'] : $conf['gallery_title']);
        $send_as_mail_address = get_webmaster_mail_address();
        $send_as_mail_formated = format_email($send_as_name, $send_as_mail_address);
      }

      foreach ($data_users as $nbm_user)
      {
        $user = array();
        $user['id'] = $nbm_user['user_id'];
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

        if ($is_action_send)
        {
          $message = '';

          if ($conf['nbm_send_detailed_content'])
          {
             $news = news($nbm_user['last_send'], $dbnow);
             $exist_data = count($news) > 0;
          }
          else
          {
            $exist_data = news_exists($nbm_user['last_send'], $dbnow);
          }

          if ($exist_data)
          {
            array_push($return_list, $nbm_user);

            $subject = '['.$conf['gallery_title'].']: '.l10n('nbm_ContentObject');
            $message .= sprintf(l10n('nbm_ContentHello'), $nbm_user['username']).",\n\n";

            if (!is_null($nbm_user['last_send']))
              $message .= sprintf(l10n('nbm_ContentNewElementsBetween'), $nbm_user['last_send'], $dbnow);
            else
              $message .= sprintf(l10n('nbm_ContentNewElements'), $dbnow);

            if ($conf['nbm_send_detailed_content'])
            {
              $message .= ":\n";

              foreach ($news as $line)
              {
                $message .= '  o '.$line."\n";
              }
              $message .= "\n";
            }
            else
            {
              $message .= ".\n";
            }

            $message .= sprintf(l10n('nbm_ContentGoTo'), $conf['gallery_title'], $conf['gallery_url'])."\n\n";
            $message .= $customize_mail_content."\n\n";
            $message .= l10n('nbm_ContentByeBye')."\n   ".$send_as_name."\n\n";
            $message .= "\n".sprintf(l10n('nbm_ContentUnsubscribe'), $send_as_mail_address)."\n\n";

            if (pwg_mail(format_email($nbm_user['username'], $nbm_user['mail_address']), $send_as_mail_formated, $subject, $message))
            {
              $sent_mail_count += 1;
              array_push($page['infos'], sprintf($msg_info, $nbm_user['username'], $nbm_user['mail_address']));

              $data = array('user_id' => $user_notification['user_id'],
                            'last_send' => $dbnow);
              array_push($datas, $data);
            }
            else
            {
              $error_on_mail_count += 1;
              array_push($page['errors'], sprintf($msg_error, $nbm_user['username'], $nbm_user['mail_address']));
            }
          }
        }
        else
        {
          if (news_exists($nbm_user['last_send'], $dbnow))
          {
            array_push($return_list, $nbm_user);
          }
        }
      }

      // Restore $user, $lang_info and $lang arrays (include/user.inc.php has been executed)
      $user = $sav_mailtousers_user;
      $lang_info = $sav_mailtousers_lang_info;
      $lang = $sav_mailtousers_lang;

      if ($is_action_send)
      {
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
          array_push($page['errors'], sprintf(l10n('nbm_%d mails were not sent.'), $error_on_mail_count));
        }
        else
        {
          if ($sent_mail_count == 0)
            array_push($page['infos'], l10n('nbm_No mail to send.'));
          else
            array_push($page['infos'], sprintf(l10n('nbm_%d mails were sent.'), $sent_mail_count));
        }
      }
    }
    else
    {
      if ($is_action_send)
      {
        array_push($page['errors'], l10n('nbm_No user to send notifications by mail.'));
      }
    }
  }
  return $return_list;
}

// +-----------------------------------------------------------------------+
// | Main                                                                  |
// +-----------------------------------------------------------------------+
if (!isset($_GET['mode']))
{
  $page['mode'] = 'send';
}
else
{
  $page['mode'] = $_GET['mode'];
}

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(get_tab_status($page['mode']));

// +-----------------------------------------------------------------------+
// | Insert new users with mails                                           |
// +-----------------------------------------------------------------------+
if (!isset($_POST) or (count($_POST) ==0))
{
  // No insert data in post mode
  insert_new_data_user_mail_notification();
}

// +-----------------------------------------------------------------------+
// | Treatment of tab post                                                 |
// +-----------------------------------------------------------------------+
switch ($page['mode'])
{
  case 'param' :
  {
    $updated_param_count = 0;
    // Update param
    $result = pwg_query('select param, value from '.CONFIG_TABLE.' where param like \'nbm\\_%\'');
    while ($nbm_user = mysql_fetch_array($result))
    {
      if (isset($_POST['param_submit']))
      {
        if (isset($_POST[$nbm_user['param']]))
        {
          $value = $_POST[$nbm_user['param']];

          $query = '
update
  '.CONFIG_TABLE.'
set 
  value = \''. str_replace("\'", "''", $value).'\'
where
  param = \''.$nbm_user['param'].'\';';
          pwg_query($query);
          $updated_param_count += 1;
        }
      }

      $conf[$nbm_user['param']] = $nbm_user['value'];

      // if the parameter is present in $_POST array (if a form is submited), we
      // override it with the submited value
      if (isset($_POST[$nbm_user['param']]))
      {
        $conf[$nbm_user['param']] = stripslashes($_POST[$nbm_user['param']]);
      }

      // If the field is true or false, the variable is transformed into a
      // boolean value.
      if ($conf[$nbm_user['param']] == 'true' or $conf[$nbm_user['param']] == 'false')
      {
        $conf[$nbm_user['param']] = get_boolean($conf[$nbm_user['param']]);
      }
    }
    
    if ($updated_param_count != 0)
    {
      array_push($page['infos'], sprintf(l10n('nbm_updated_param_count'), $updated_param_count));
    }
  }
  case 'subscribe' :
  {
    if (isset($_POST['falsify']) and isset($_POST['cat_true']))
    {
      unsubcribe_notification_by_mail($_POST['cat_true']);
    }
    else
    if (isset($_POST['trueify']) and isset($_POST['cat_false']))
    {
      subcribe_notification_by_mail($_POST['cat_false']);
    }
    break;
  }

  case 'send' :
  {
    if (isset($_POST['send_submit']) and isset($_POST['send_selection']) and isset($_POST['send_customize_mail_content']))
    {
      do_action_send_mail_notification('send', $_POST['send_selection'], $_POST['send_customize_mail_content']);
    }
  }
}

// +-----------------------------------------------------------------------+
// | template initialization                                               |
// +-----------------------------------------------------------------------+
$template->set_filenames
(
  array
  (
    'double_select' => 'admin/double_select.tpl',
    'notification_by_mail'=>'admin/notification_by_mail.tpl'
  )
);

$base_url = get_root_url().'admin.php';

$template->assign_vars
(
  array
  (
    'U_TABSHEET_TITLE' => l10n('nbm_'.$page['mode'].'_mode'),
    'U_HELP' => add_url_params(get_root_url().'/popuphelp.php', array('page' => 'notification_by_mail')),
    'F_ACTION'=> $base_url.get_query_string_diff(array())
  )
);

if (is_autorize_status(ACCESS_WEBMASTER))
{
  $template->assign_block_vars
  (
    'header_link',
    array
    (
      'PARAM_MODE' => add_url_params($base_url.get_query_string_diff(array('mode', 'select')), array('mode' => 'param')),
      'SUBSCRIBE_MODE' => add_url_params($base_url.get_query_string_diff(array('mode', 'select')), array('mode' => 'subscribe')),
      'SEND_MODE' => add_url_params($base_url.get_query_string_diff(array('mode', 'select')), array('mode' => 'send'))
    )
  );
}

switch ($page['mode'])
{
  case 'param' :
  {
    $template->assign_block_vars(
      $page['mode'],
      array(
        'SEND_MAIL_AS' => $conf['nbm_send_mail_as'],
        'SEND_DETAILED_CONTENT_YES' => ($conf['nbm_send_detailed_content'] ? 'checked="checked"' : ''),
        'SEND_DETAILED_CONTENT_NO' => (!$conf['nbm_send_detailed_content'] ? 'checked="checked"' : ''),
        'COMPLEMENTARY_MAIL_CONTENT' => $conf['nbm_complementary_mail_content']
        ));
    break;
  }

  case 'subscribe' :
  {
    $template->assign_block_vars(
      $page['mode'],
      array(
        ));

    $template->assign_vars(
      array(
        'L_CAT_OPTIONS_TRUE' => l10n('nbm_subscribe_col'),
        'L_CAT_OPTIONS_FALSE' => l10n('nbm_unsubscribe_col')
        )
      );

    $data_users = get_user_notifications('subscribe');
    foreach ($data_users as $nbm_user)
    {
      $template->assign_block_vars(
        (get_boolean($nbm_user['enabled']) ? 'category_option_true' : 'category_option_false'),
        array('SELECTED' => '',
              'VALUE' => $nbm_user['check_key'],
              'OPTION' => $nbm_user['username'].'['.$nbm_user['mail_address'].']'
          ));
    }

    break;
  }

  case 'send' :
  {
    $template->assign_block_vars($page['mode'], array());

    $data_users = do_action_send_mail_notification('list');

    if  (count($data_users) == 0)
    {
      $template->assign_block_vars($page['mode'].'.send_empty', array());
    }
    else
    {
      $template->assign_block_vars(
        $page['mode'].'.send_data',
        array(
          'CUSTOMIZE_MAIL_CONTENT' => isset($_POST['send_customize_mail_content']) ? $_POST['send_customize_mail_content'] : $conf['nbm_complementary_mail_content']
          ));

      foreach ($data_users as $num => $nbm_user)
          $template->assign_block_vars(
            $page['mode'].'.send_data.user_send_mail',
            array(
              'CLASS' => ($num % 2 == 1) ? 'nbm_user2' : 'nbm_user1',
              'ID' => $nbm_user['check_key'],
              'CHECKED' =>  ( // not check if not selected,  on init select<all
                              isset($_POST['send_selection']) and // not init
                              !in_array($nbm_user['check_key'],  $_POST['send_selection']) // not selected
                            )   ? '' : 'checked="checked"',
              'USERNAME'=> $nbm_user['username'],
              'EMAIL' => $nbm_user['mail_address'],
              'LAST_SEND'=> $nbm_user['last_send']
              ));
    }

    break;
  }
}

// +-----------------------------------------------------------------------+
// | Sending html code                                                     |
// +-----------------------------------------------------------------------+
$template->assign_var_from_handle('DOUBLE_SELECT', 'double_select');
$template->assign_var_from_handle('ADMIN_CONTENT', 'notification_by_mail');

?>