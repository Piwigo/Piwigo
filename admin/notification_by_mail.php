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
// | Initialization                                                        |
// +-----------------------------------------------------------------------+
$base_url = get_root_url().'admin.php';
$must_repost = false;

// +-----------------------------------------------------------------------+
// | functions                                                             |
// +-----------------------------------------------------------------------+

/*
 * Do timeout treatment in order to finish to send mails
 *
 * @param $post_keyname: key of check_key post array
 * @param check_key_treated: array of check_key treated
 * @return none
 */
function do_timeout_treatment($post_keyname, $check_key_treated = array())
{
  global $env_nbm, $base_url, $page, $must_repost;

  if ($env_nbm['is_sendmail_timeout'])
  {
    if (isset($_POST[$post_keyname]))
    {
      $post_count = count($_POST[$post_keyname]);
      $treated_count = count($check_key_treated);
      if ($treated_count != 0)
      {
        $time_refresh = ceil((get_moment() - $env_nbm['start_time']) * $post_count / $treated_count);
      }
      else
      {
        $time_refresh = 0;
      }
      $_POST[$post_keyname] = array_diff($_POST[$post_keyname], $check_key_treated);

      $must_repost = true;
      array_push($page['errors'], sprintf(l10n('nbm_background_treatment_redirect'), $time_refresh));
    }
  }

}

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
 * Inserting News users
 */
function insert_new_data_user_mail_notification()
{
  global $conf, $page, $env_nbm;

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

      array_push
      (
        $page['infos'], 
        sprintf(
          l10n('nbm_user_x_added'), 
          $nbm_user['username'], 
          get_email_address_as_display_text($nbm_user['mail_address'])
        )
      );
    }

    // Insert new nbm_users
    mass_inserts(USER_MAIL_NOTIFICATION_TABLE, array('user_id', 'check_key', 'enabled'), $inserts);
    // Update field enabled with specific function
    $check_key_treated = do_subscribe_unsubcribe_notification_by_mail
    (
      true,
      $conf['nbm_default_value_user_enabled'],
      $check_key_list
    );

     // On timeout simulate like tabsheet send
    if ($env_nbm['is_sendmail_timeout'])
    {
      $quoted_check_key_list = quote_check_key_list(array_diff($check_key_list, $check_key_treated));
      if (count($quoted_check_key_list) != 0 )
      {
        $query = 'delete from '.USER_MAIL_NOTIFICATION_TABLE.' where check_key in ('.implode(",", $quoted_check_key_list).');';
        $result = pwg_query($query);

        redirect($base_url.get_query_string_diff(array()), l10n('nbm_redirect_msg'));
      }
    }
  }
}

/*
 * Send mail for notification to all users
 * Return list of "selected" users for 'list_to_send'
 * Return list of "treated" check_key for 'send'
 */
function do_action_send_mail_notification($action = 'list_to_send', $check_key_list = array(), $customize_mail_content = '')
{
  global $conf, $page, $user, $lang_info, $lang, $env_nbm;
  $return_list = array();
  
  if (in_array($action, array('list_to_send', 'send')))
  {
    list($dbnow) = mysql_fetch_row(pwg_query('SELECT NOW();'));

    $is_action_send = ($action == 'send');

    if (!isset($customize_mail_content))
    {
      $customize_mail_content = $conf['nbm_complementary_mail_content'];
    }

    // disabled and null mail_address are not selected in the list
    $data_users = get_user_notifications('send', $check_key_list);

    // List all if it's define on options or on timeout
    $is_list_all_without_test = ($env_nbm['is_sendmail_timeout'] or $conf['nbm_list_all_enabled_users_to_send']);

    // Check if exist news to list user or send mails
    if ((!$is_list_all_without_test) or ($is_action_send))
    {
      if (count($data_users) > 0)
      {
        $datas = array();

        // Prepare message after change language
        if ($is_action_send)
        {
          $msg_break_timeout = l10n('nbm_break_timeout_send_mail');
        }
        else
        {
          $msg_break_timeout = l10n('nbm_break_timeout_list_user');
        }

        // Begin nbm users environment
        begin_users_env_nbm($is_action_send);

        foreach ($data_users as $nbm_user)
        {
          if ((!$is_action_send) and check_sendmail_timeout())
          {
            // Stop fill list on 'list_to_send', if the quota is override
            array_push($page['infos'], $msg_break_timeout);
            break;
          }
          if (($is_action_send) and check_sendmail_timeout())
          {
            // Stop fill list on 'send', if the quota is override
            array_push($page['errors'], $msg_break_timeout);
            break;
          }

          // set env nbm user
          set_user_id_on_env_nbm($nbm_user['user_id']);

          if ($is_action_send)
          {
            // Fill return list of "treated" check_key for 'send'
            array_push($return_list, $nbm_user['check_key']);
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
              $subject = '['.$conf['gallery_title'].']: '.l10n('nbm_object_news');
              $message .= sprintf(l10n('nbm_content_hello'), $nbm_user['username']).",\n\n";

              if (!is_null($nbm_user['last_send']))
                $message .= sprintf(l10n('nbm_content_new_elements_between'), $nbm_user['last_send'], $dbnow);
              else
                $message .= sprintf(l10n('nbm_content_new_elements'), $dbnow);

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

              $message .= sprintf(l10n('nbm_content_goto'), $conf['gallery_title'], $conf['gallery_url'])."\n\n";
              $message .= $customize_mail_content."\n\n";
              $message .= l10n('nbm_content_byebye')."\n   ".$env_nbm['send_as_name']."\n\n";

              $message .= get_mail_content_subscribe_unsubcribe($nbm_user);

              if (pwg_mail(format_email($nbm_user['username'], $nbm_user['mail_address']), $env_nbm['send_as_mail_formated'], $subject, $message))
              {
                inc_mail_sent_success($nbm_user);

                $data = array('user_id' => $nbm_user['user_id'],
                              'last_send' => $dbnow);
                array_push($datas, $data);
              }
              else
              {
                inc_mail_sent_failed($nbm_user);
              }
            }
          }
          else
          {
            if (news_exists($nbm_user['last_send'], $dbnow))
            {
              // Fill return list of "selected" users for 'list_to_send'
              array_push($return_list, $nbm_user);
            }
          }
        }

        // Restore nbm environment
        end_users_env_nbm();

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

          display_counter_info();
        }
      }
      else
      {
        if ($is_action_send)
        {
          array_push($page['errors'], l10n('nbm_no_user_to send_notifications_by_mail'));
        }
      }
    }
    else
    {
      // Quick List, don't check news
      // Fill return list of "selected" users for 'list_to_send'
      $return_list = $data_users;
    }
  }

  // Return list of "selected" users for 'list_to_send'
  // Return list of "treated" check_key for 'send'
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
      $check_key_treated = unsubcribe_notification_by_mail(true, $_POST['cat_true']);
      do_timeout_treatment('cat_true', $check_key_treated);
    }
    else
    if (isset($_POST['trueify']) and isset($_POST['cat_false']))
    {
      $check_key_treated = subcribe_notification_by_mail(true, $_POST['cat_false']);
      do_timeout_treatment('cat_false', $check_key_treated);
    }
    break;
  }

  case 'send' :
  {
    if (isset($_POST['send_submit']) and isset($_POST['send_selection']) and isset($_POST['send_customize_mail_content']))
    {
      $check_key_treated = do_action_send_mail_notification('send', $_POST['send_selection'], stripslashes($_POST['send_customize_mail_content']));
      do_timeout_treatment('send_selection', $check_key_treated);
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

$template->assign_vars
(
  array
  (
    'U_TABSHEET_TITLE' => l10n('nbm_'.$page['mode'].'_mode'),
    'U_HELP' => add_url_params(get_root_url().'popuphelp.php', array('page' => 'notification_by_mail')),
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

if ($must_repost)
{
  // Get name of submit button
  $repost_submit_name = '';
  if (isset($_POST['falsify']))
  {
    $repost_submit_name = 'falsify';
  }
  elseif (isset($_POST['trueify']))
  {
    $repost_submit_name = 'trueify';
  }
  elseif (isset($_POST['send_submit']))
  {
    $repost_submit_name = 'send_submit';
  }

  $template->assign_block_vars
  (
    'repost', 
      array
      (
        'REPOST_SUBMIT_NAME' => $repost_submit_name
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
        array('SELECTED' => ( // Keep selected user where enabled are not changed when change has been notify
                              get_boolean($nbm_user['enabled']) ? (isset($_POST['falsify']) and isset($_POST['cat_true']) and in_array($nbm_user['check_key'], $_POST['cat_true']))
                                                                : (isset($_POST['trueify']) and isset($_POST['cat_false']) and in_array($nbm_user['check_key'], $_POST['cat_false']))
                            ) ? 'selected="selected"' : '',
              'VALUE' => $nbm_user['check_key'],
              'OPTION' => $nbm_user['username'].'['.get_email_address_as_display_text($nbm_user['mail_address']).']'
          ));
    }

    break;
  }

  case 'send' :
  {
    $template->assign_block_vars($page['mode'], array());

    $data_users = do_action_send_mail_notification('list_to_send');

    if  (count($data_users) == 0)
    {
      $template->assign_block_vars($page['mode'].'.send_empty', array());
    }
    else
    {
      $template->assign_block_vars(
        $page['mode'].'.send_data',
        array(
          'CUSTOMIZE_MAIL_CONTENT' => isset($_POST['send_customize_mail_content']) ? stripslashes($_POST['send_customize_mail_content']) : $conf['nbm_complementary_mail_content']
          ));

      foreach ($data_users as $num => $nbm_user)
      {
        if (
            (!$must_repost) or // Not timeout, normal treatment
            (($must_repost) and in_array($nbm_user['check_key'], $_POST['send_selection']))  // Must be repost, show only user to send
            )
        {
          $template->assign_block_vars(
            $page['mode'].'.send_data.user_send_mail',
            array(
              'CLASS' => ($num % 2 == 1) ? 'nbm_user2' : 'nbm_user1',
              'ID' => $nbm_user['check_key'],
              'CHECKED' =>  ( // not check if not selected,  on init select<all
                              isset($_POST['send_selection']) and // not init
                              !in_array($nbm_user['check_key'], $_POST['send_selection']) // not selected
                            )   ? '' : 'checked="checked"',
              'USERNAME'=> $nbm_user['username'],
              'EMAIL' => get_email_address_as_display_text($nbm_user['mail_address']),
              'LAST_SEND'=> $nbm_user['last_send']
              ));
        }
      }
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