<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// | Copyright (C) 2006 Ruben ARNAUD - team@phpwebgallery.net              |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2006-03-23 02:49:04 +0100 (jeu., 23 mars 2006) $
// | last modifier : $Author: rvelices $
// | revision      : $Revision: 1094 $
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

/* nbm_global_var */
$env_nbm = array();

/*
 * Search an available check_key
 *
 * It's a copy of function find_available_feed_id
 *
 * @return string nbm identifier
 */
function find_available_check_key()
{
  while (true)
  {
    $key = generate_key(16);
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
 * Add quote to all elements of check_key_list
 *
 * @return quoted check key list
 */
function quote_check_key_list($check_key_list = array())
{
  return array_map(create_function('$s', 'return \'\\\'\'.$s.\'\\\'\';'), $check_key_list);
}

/*
 * Execute all main queries to get list of user 
 *
 * Type are the type of list 'subscribe', 'send'
 *
 * return array of users
 */
function get_user_notifications($action, $check_key_list = array(), $enabled_filter_value = '')
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
      // No mail empty and all users enabled
      $query .= ' and
  N.enabled = \'true\' and
  U.'.$conf['user_fields']['email'].' is not null';
    }

    $query .= $query_and_check_key;

    if (isset($enabled_filter_value) and ($enabled_filter_value != ''))
    {
      $query .= ' and
        N.enabled = \''.boolean_to_string($enabled_filter_value).'\'';
    }

    $query .= '
order by';

    if ($action == 'send')
    {
      $query .= '
  last_send, username;';
    }
    else
    {
      $query .= '
  username;';
    }

    $query .= ';';

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
 * Begin of use nbm environment
 * Prepare and save current environment and initialize data in order to send mail
 *
 * Return none
 */
function begin_users_env_nbm($is_to_send_mail = false)
{
  global $user, $lang, $lang_info, $conf, $env_nbm;

  // Save $user, $lang_info and $lang arrays (include/user.inc.php has been executed)
  $env_nbm['save_user'] = $user;
  $env_nbm['save_lang_info'] = $lang_info;
  $env_nbm['save_lang'] = $lang;
  // Last Language
  $env_nbm['last_language'] = $user['language'];

  $env_nbm['is_to_send_mail'] = $is_to_send_mail;

  if ($is_to_send_mail)
  {
    // Init mail configuration
    $env_nbm['send_as_name'] = ((isset($conf['nbm_send_mail_as']) and !empty($conf['nbm_send_mail_as'])) ? $conf['nbm_send_mail_as'] : $conf['gallery_title']);
    $env_nbm['send_as_mail_address'] = get_webmaster_mail_address();
    $env_nbm['send_as_mail_formated'] = format_email($env_nbm['send_as_name'], $env_nbm['send_as_mail_address']);
    // Init mail counter
    $env_nbm['error_on_mail_count'] = 0;
    $env_nbm['sent_mail_count'] = 0;
    // Save sendmail message info and error in the original language
    $env_nbm['msg_info'] = l10n('nbm_msg_mail_sent_to');
    $env_nbm['msg_error'] = l10n('nbm_msg_error_sending_email_to');
  }
}

/*
 * End of use nbm environment
 * Restore environment
 *
 * Return none
 */
function end_users_env_nbm()
{
  global $user, $lang, $lang_info, $env_nbm;

  // Restore $user, $lang_info and $lang arrays (include/user.inc.php has been executed)
  $user = $env_nbm['save_user'];
  $lang_info = $env_nbm['save_lang_info'];
  $lang = $env_nbm['save_lang'];
}

/*
 * Set user_id on nbm enviromnent
 *
 * Return none
 */
function set_user_id_on_env_nbm($user_id)
{
  global $user, $lang, $lang_info, $env_nbm;

  $user = array();
  $user['id'] = $user_id;
  $user = array_merge($user, getuserdata($user['id'], true));

  if ($env_nbm['last_language'] != $user['language'])
  {
    $env_nbm['last_language'] = $user['language'];

    // Re-Init language arrays
    $lang_info = array();
    $lang  = array();

    // language files
    include(get_language_filepath('common.lang.php'));
    // No test admin because script is checked admin (user selected no)
    // Translations are in admin file too
    include(get_language_filepath('admin.lang.php'));
  }
}

/*
 * Inc Counter success
 *
 * Return none
 */
function inc_mail_sent_success($nbm_user)
{
  global $page, $env_nbm;

  $env_nbm['sent_mail_count'] += 1;
  array_push($page['infos'], sprintf($env_nbm['msg_info'], $nbm_user['username'], $nbm_user['mail_address']));
}

/*
 * Inc Counter failed
 *
 * Return none
 */
function inc_mail_sent_failed($nbm_user)
{
  global $page, $env_nbm;

  $env_nbm['error_on_mail_count'] += 1;
  array_push($page['errors'], sprintf($env_nbm['msg_error'], $nbm_user['username'], $nbm_user['mail_address']));
}

/*
 * Display Counter Info
 *
 * Return none
 */
function display_counter_info()
{
  global $page, $env_nbm;

  if ($env_nbm['error_on_mail_count'] != 0)
  {
    array_push($page['errors'], sprintf(l10n('nbm_msg_no_mail_to_send'), $env_nbm['error_on_mail_count']));
    if ($env_nbm['sent_mail_count'] != 0)
      array_push($page['infos'], sprintf(l10n('nbm_msg_n_mails_sent'), $env_nbm['sent_mail_count']));
  }
  else
  {
    if ($env_nbm['sent_mail_count'] == 0)
      array_push($page['infos'], l10n('nbm_no_mail_to_send'));
    else
      array_push($page['infos'], sprintf(l10n('nbm_msg_n_mails_sent'), $env_nbm['sent_mail_count']));
  }
}

function get_mail_content_subscribe_unsubcribe($nbm_user)
{
  global $page, $env_nbm;
  
  $content = "\n\n\n";
  
  if ( isset($page['root_path']) )
  {
    $save_root_path = $page['root_path'];
  }

  $page['root_path'] = 'http://'.$_SERVER['HTTP_HOST'].cookie_path();
  
  $content .= "___________________________________________________\n\n";
  $content .= sprintf(l10n('nbm_content_unsubscribe_link'), add_url_params(get_root_url().'/nbm.php', array('unsubscribe' => $nbm_user['check_key'])))."\n";
  $content .= sprintf(l10n('nbm_content_subscribe_link'), add_url_params(get_root_url().'/nbm.php', array('subscribe' => $nbm_user['check_key'])))."\n";
  $content .= sprintf(l10n('nbm_content_subscribe_unsubscribe_contact'), $env_nbm['send_as_mail_address'])."\n";
  $content .= "___________________________________________________\n\n\n\n";

  if (isset($save_root_path))
  {
    $page['root_path'] = $save_root_path;
  }
  else
  {
    unset($page['root_path']);
  }

  return $content;
}

/*
 * Subscribe or unsubscribe notification by mail
 *
 * is_subscribe define if action=subscribe or unsubscribe
 * check_key list where action will be done
 *
 * @return updated data count
 */
function do_subscribe_unsubcribe_notification_by_mail($is_admin_request, $is_subscribe = false, $check_key_list = array())
{
  global $conf, $page, $env_nbm, $conf;

  $updated_data_count = 0;
  $error_on_updated_data_count = 0;

  if ($is_subscribe)
  {
    $msg_info = l10n('nbm_user_change_enabled_true');
    $msg_error = l10n('nbm_user_not_change_enabled_true');
  }
  else
  {
    $msg_info = l10n('nbm_user_change_enabled_false');
    $msg_error = l10n('nbm_user_not_change_enabled_false');
  }

  if (count($check_key_list) != 0)
  {
    $updates = array();
    $enabled_value = boolean_to_string($is_subscribe);
    $data_users = get_user_notifications('subscribe', $check_key_list, !$is_subscribe);

    // Begin nbm users environment
    begin_users_env_nbm(true);

    foreach ($data_users as $nbm_user)
    {
      if (($env_nbm['error_on_mail_count'] + $env_nbm['sent_mail_count']) >= $conf['nbm_max_mails_send'])
      {
        // Stop fill list on 'send', if the quota is override
        array_push($page['errors'], sprintf(l10n('nbm_nbm_break_send_mail'), $conf['nbm_max_mails_send']));
        break;
      }

      $do_update = true;
      if ($nbm_user['mail_address'] != '')
      {
        // set env nbm user
        set_user_id_on_env_nbm($nbm_user['user_id']);

        $message = '';

        $subject = '['.$conf['gallery_title'].']: '.($is_subscribe ? l10n('nbm_object_subcribe'): l10n('nbm_object_unsubcribe'));
        $message .= sprintf(l10n('nbm_content_hello'), $nbm_user['username']).",\n\n";

        if ($is_subscribe)
        {
          $message .= l10n($is_admin_request ? 'nbm_content_subscribe_by_admin' : 'nbm_content_subscribe_by_himself');
        }
        else
        {
          $message .= l10n($is_admin_request ? 'nbm_content_unsubscribe_by_admin' : 'nbm_content_unsubscribe_by_himself');
        }

        $message .= "\n\n";
        $message .= l10n('nbm_content_byebye')."\n   ".$env_nbm['send_as_name']."\n\n";

        $message .= get_mail_content_subscribe_unsubcribe($nbm_user);

        if (pwg_mail(format_email($nbm_user['username'], $nbm_user['mail_address']), $env_nbm['send_as_mail_formated'], $subject, $message))
        {
          inc_mail_sent_success($nbm_user);
        }
        else
        {
          inc_mail_sent_failed($nbm_user);
          $do_update = false;
        }
      }

      if ($do_update)
      {
        array_push
        (
          $updates,
          array
          (
            'check_key' => $nbm_user['check_key'],
            'enabled' => $enabled_value
          )
        );
        $updated_data_count += 1;
        array_push($page['infos'], sprintf($msg_info, $nbm_user['username'], $nbm_user['mail_address']));
      }
      else
      {
        $error_on_updated_data_count += 1;
        array_push($page['errors'], sprintf($msg_error, $nbm_user['username'], $nbm_user['mail_address']));
      }

    }

    // Restore nbm environment
    end_users_env_nbm();

    display_counter_info();

    mass_updates(
      USER_MAIL_NOTIFICATION_TABLE,
      array(
        'primary' => array('check_key'),
        'update' => array('enabled')
      ),
      $updates
    );

  }

  array_push($page['infos'], sprintf(l10n('nbm_user_change_enabled_updated_data_count'), $updated_data_count));
  if ($error_on_updated_data_count != 0)
  {
    array_push($page['errors'], sprintf(l10n('nbm_user_change_enabled_error_on_updated_data_count'), $error_on_updated_data_count));
  }

  return $updated_data_count;
}

/*
 * Unsubscribe notification by mail
 *
 * check_key list where action will be done
 *
 * @return updated data count
 */
function unsubcribe_notification_by_mail($is_admin_request, $check_key_list = array())
{
  return do_subscribe_unsubcribe_notification_by_mail($is_admin_request, false, $check_key_list);
}

/*
 * Subscribe notification by mail
 *
 * check_key list where action will be done
 *
 * @return updated data count
 */
function subcribe_notification_by_mail($is_admin_request, $check_key_list = array())
{
  return do_subscribe_unsubcribe_notification_by_mail($is_admin_request, true, $check_key_list);
}

?>