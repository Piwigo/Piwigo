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
 * Add quote to all elements of check_key_list
 *
 * @return quoted check key list
 */
function quote_check_key_list($check_key_list = array())
{
  return array_map(create_function('$s', 'return \'\\\'\'.$s.\'\\\'\';'), $check_key_list);
}

/*
 * Subscribe or unsubscribe notification by mail
 *
 * is_subscribe define if action=subscribe or unsubscribe
 * check_key list where action will be done
 *
 * @return updated data count
 */
function do_subscribe_unsubcribe_notification_by_mail($is_subscribe = false, $check_key_list = array())
{
  global $page;

  $updated_data_count = 0;
  if ($is_subscribe)
  {
    $msg_info = l10n('nbm_user_change_enabled_true');
  }
  else
  {
    $msg_info = l10n('nbm_user_change_enabled_false');
  }

  if (count($check_key_list) != 0)
  {
    $quoted_check_key_list = quote_check_key_list($check_key_list);

    $query = '
select
  N.check_key, U.username, U.mail_address
from
  '.USER_MAIL_NOTIFICATION_TABLE.' as N,
  '.USERS_TABLE.' as U
where
  N.user_id =  U.id and
  N.enabled = \''.boolean_to_string(!$is_subscribe).'\' and
  check_key in ('.implode(",", $quoted_check_key_list).')
order by
  username;';

    $result = pwg_query($query);
    if (!empty($result))
    {
      $updates = array();
      $enabled_value = boolean_to_string($is_subscribe);

      while ($row = mysql_fetch_array($result))
      {
        array_push
        (
          $updates,
          array
          (
            'check_key' => $row['check_key'],
            'enabled' => $enabled_value
          )
        );
        $updated_data_count += 1;
        array_push($page['infos'], sprintf($msg_info, $row['username'], $row['mail_address']));
      }

      mass_updates(
        USER_MAIL_NOTIFICATION_TABLE,
        array(
          'primary' => array('check_key'),
          'update' => array('enabled')
        ),
        $updates
      );
    }
  }

  array_push($page['infos'], sprintf(l10n('nbm_user_change_enabled_updated_data_count'), $updated_data_count));

  return $updated_data_count;
}

/*
 * Unsubscribe notification by mail
 *
 * check_key list where action will be done
 *
 * @return updated data count
 */
function unsubcribe_notification_by_mail($check_key_list = array())
{
  return do_subscribe_unsubcribe_notification_by_mail(false, $check_key_list);
}

/*
 * Subscribe notification by mail
 *
 * check_key list where action will be done
 *
 * @return updated data count
 */
function subcribe_notification_by_mail($check_key_list = array())
{
  return do_subscribe_unsubcribe_notification_by_mail(true, $check_key_list);
}

?>