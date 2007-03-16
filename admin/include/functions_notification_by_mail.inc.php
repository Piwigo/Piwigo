<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// | Copyright (C) 2006-2007 Ruben ARNAUD - team@phpwebgallery.net         |
// +-----------------------------------------------------------------------+
// | file          : $Id$
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

/* nbm_global_var */
$env_nbm = array
          (
            'start_time' => get_moment(),
            'sendmail_timeout' => (intval(ini_get('max_execution_time')) * $conf['nbm_max_treatment_timeout_percent']),
            'is_sendmail_timeout' => false
          );

if
  (
    (!isset($env_nbm['sendmail_timeout'])) or
    (!is_numeric($env_nbm['sendmail_timeout'])) or
    ($env_nbm['sendmail_timeout'] <= 0)
  )
{
  $env_nbm['sendmail_timeout'] = $conf['nbm_treatment_timeout_default'];
}

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
 * Check sendmail timeout state
 *
 * @return true, if it's timeout
 */
function check_sendmail_timeout()
{
  global $env_nbm;

  $env_nbm['is_sendmail_timeout'] = ((get_moment() - $env_nbm['start_time']) > $env_nbm['sendmail_timeout']);

  return $env_nbm['is_sendmail_timeout'];
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
  // Save current language to stack, necessary because $user change during NBM
  switch_lang_to($user['language']);

  $env_nbm['is_to_send_mail'] = $is_to_send_mail;

  if ($is_to_send_mail)
  {
    // Init mail configuration
    $env_nbm['email_format'] = get_str_email_format($conf['nbm_send_html_mail']);
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
  // Restore current language to stack, necessary because $user change during NBM
  switch_lang_back();

  if ($env_nbm['is_to_send_mail'])
  {
    unset($env_nbm['email_format']);
    unset($env_nbm['send_as_name']);
    unset($env_nbm['send_as_mail_address']);
    unset($env_nbm['send_as_mail_formated']);
    // Don t unset counter
    //unset($env_nbm['error_on_mail_count']);
    //unset($env_nbm['sent_mail_count']);
    unset($env_nbm['msg_info']);
    unset($env_nbm['msg_error']);
  }

  unset($env_nbm['save_user']);
  unset($env_nbm['is_to_send_mail']);
}

/*
 * Set user on nbm enviromnent
 *
 * Return none
 */
function set_user_on_env_nbm(&$nbm_user, $is_action_send)
{
  global $user, $lang, $lang_info, $env_nbm;

  $user = build_user( $nbm_user['user_id'], true );

  switch_lang_to($user['language']);

  if ($is_action_send)
  {
    $nbm_user['template'] = $user['template'];
    $nbm_user['theme'] = $user['theme'];
    $env_nbm['mail_template'] =
      get_mail_template($env_nbm['email_format'], 
        array('template' => $nbm_user['template'], 'theme' => $nbm_user['theme']));
    $env_nbm['mail_template']->set_filename('notification_by_mail', 'admin/notification_by_mail.tpl');
  }
}

/*
 * Unset user on nbm enviromnent
 *
 * Return none
 */
function unset_user_on_env_nbm()
{
  global $env_nbm;

  switch_lang_back();
  unset($env_nbm['mail_template']);
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
    array_push($page['errors'], l10n_dec('nbm_msg_n_mail_not_send', 'nbm_msg_n_mails_not_send', $env_nbm['error_on_mail_count']));
    if ($env_nbm['sent_mail_count'] != 0)
      array_push($page['infos'], l10n_dec('nbm_msg_n_mail_sent', 'nbm_msg_n_mails_sent', $env_nbm['sent_mail_count']));
  }
  else
  {
    if ($env_nbm['sent_mail_count'] == 0)
      array_push($page['infos'], l10n('nbm_no_mail_to_send'));
    else
      array_push($page['infos'], l10n_dec('nbm_msg_n_mail_sent', 'nbm_msg_n_mails_sent', $env_nbm['sent_mail_count']));
  }
}

function assign_vars_nbm_mail_content($nbm_user)
{
  global $env_nbm;

  set_make_full_url();

  $env_nbm['mail_template']->assign_vars
  (
    array
    (
      'USERNAME' => $nbm_user['username'],

      'SEND_AS_NAME' => $env_nbm['send_as_name'],

      'UNSUBSCRIBE_LINK' => add_url_params(get_root_url().'nbm.php', array('unsubscribe' => $nbm_user['check_key'])),
      'SUBSCRIBE_LINK' => add_url_params(get_root_url().'nbm.php', array('subscribe' => $nbm_user['check_key'])),
      'CONTACT_EMAIL' => $env_nbm['send_as_mail_address']
    )
  );

  unset_make_full_url();
}

/*
 * Subscribe or unsubscribe notification by mail
 *
 * is_subscribe define if action=subscribe or unsubscribe
 * check_key list where action will be done
 *
 * @return check_key list treated
 */
function do_subscribe_unsubscribe_notification_by_mail($is_admin_request, $is_subscribe = false, $check_key_list = array())
{
  global $conf, $page, $env_nbm, $conf;

  $check_key_treated = array();
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

    // Prepare message after change language
    $msg_break_timeout = l10n('nbm_break_timeout_send_mail');

    // Begin nbm users environment
    begin_users_env_nbm(true);

    foreach ($data_users as $nbm_user)
    {
      if (check_sendmail_timeout())
      {
        // Stop fill list on 'send', if the quota is override
        array_push($page['errors'], $msg_break_timeout);
        break;
      }

      // Fill return list
      array_push($check_key_treated, $nbm_user['check_key']);

      $do_update = true;
      if ($nbm_user['mail_address'] != '')
      {
        // set env nbm user
        set_user_on_env_nbm($nbm_user, true);

        $subject = '['.$conf['gallery_title'].']: '.($is_subscribe ? l10n('nbm_object_subscribe'): l10n('nbm_object_unsubscribe'));

        // Assign current var for nbm mail
        assign_vars_nbm_mail_content($nbm_user);

        $section_action_by = ($is_subscribe ? 'subscribe_by_' : 'unsubscribe_by_');
        $section_action_by .= ($is_admin_request ? 'admin' : 'himself');
        $env_nbm['mail_template']->assign_block_vars
        (
          $section_action_by, array('DUMMY' => 'dummy')
        );

        if (pwg_mail
            (
              format_email($nbm_user['username'], $nbm_user['mail_address']),
              array
              (
                'from' => $env_nbm['send_as_mail_formated'],
                'subject' => $subject,
                'email_format' => $env_nbm['email_format'],
                'content' => $env_nbm['mail_template']->parse('notification_by_mail', true),
                'content_format' => $env_nbm['email_format'],
                'template' => $nbm_user['template'],
                'theme' => $nbm_user['theme']
              )
            ))
        {
          inc_mail_sent_success($nbm_user);
        }
        else
        {
          inc_mail_sent_failed($nbm_user);
          $do_update = false;
        }

        // unset env nbm user
        unset_user_on_env_nbm();

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

  array_push($page['infos'], l10n_dec('nbm_user_change_enabled_updated_data_count', 'nbm_users_change_enabled_updated_data_count', $updated_data_count));
  if ($error_on_updated_data_count != 0)
  {
    array_push($page['errors'],
      l10n_dec('nbm_user_change_enabled_error_on_updated_data_count',
               'nbm_users_change_enabled_error_on_updated_data_count',
               $error_on_updated_data_count));
  }

  return $check_key_treated;
}

/*
 * Unsubscribe notification by mail
 *
 * check_key list where action will be done
 *
 * @return check_key list treated
 */
function unsubscribe_notification_by_mail($is_admin_request, $check_key_list = array())
{
  return do_subscribe_unsubscribe_notification_by_mail($is_admin_request, false, $check_key_list);
}

/*
 * Subscribe notification by mail
 *
 * check_key list where action will be done
 *
 * @return check_key list treated
 */
function subscribe_notification_by_mail($is_admin_request, $check_key_list = array())
{
  return do_subscribe_unsubscribe_notification_by_mail($is_admin_request, true, $check_key_list);
}

?>
