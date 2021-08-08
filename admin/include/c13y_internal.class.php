<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

class c13y_internal
{
  function __construct()
  {
    add_event_handler('list_check_integrity', array(&$this, 'c13y_version'));
    add_event_handler('list_check_integrity', array(&$this, 'c13y_exif'));
    add_event_handler('list_check_integrity', array(&$this, 'c13y_user'));
  }

  /**
   * Check version
   *
   * @param c13y object
   * @return void
   */
  function c13y_version($c13y)
  {
    global $conf;

    $check_list = array();

    $check_list[] = array(
        'type' => 'PHP',
        'current' => phpversion(),
        'required' => REQUIRED_PHP_VERSION,
        );

    $check_list[] = array(
        'type' => 'MySQL',
        'current' => pwg_get_db_version(), 
        'required' => REQUIRED_MYSQL_VERSION,
        );

    foreach ($check_list as $elem)
    {
      if (version_compare($elem['current'], $elem['required'], '<'))
      {
        $c13y->add_anomaly(
          sprintf(l10n('The version of %s [%s] installed is not compatible with the version required [%s]'), $elem['type'], $elem['current'], $elem['required']),
          null,
          null,
          l10n('You need to upgrade your system to take full advantage of the application else the application will not work correctly, or not at all')
          .'<br>'.
          $c13y->get_htlm_links_more_info());
      }
    }
  }

  /**
   * Check exif
   *
   * @param c13y object
   * @return void
   */
  function c13y_exif($c13y)
  {
    global $conf;

    foreach (array('show_exif', 'use_exif') as $value)
    {
      if (($conf[$value]) and (!function_exists('exif_read_data')))
      {
        $c13y->add_anomaly(
          sprintf(l10n('%s value is not correct file because exif are not supported'), '$conf[\''.$value.'\']'),
          null,
          null,
          sprintf(l10n('%s must be to set to false in your local/config/config.inc.php file'), '$conf[\''.$value.'\']')
          .'<br>'.
          $c13y->get_htlm_links_more_info());
      }
    }
  }

  /**
   * Check user
   *
   * @param c13y object
   * @return void
   */
  function c13y_user($c13y)
  {
    global $conf;

    $c13y_users = array();
    $c13y_users[$conf['guest_id']] = array(
      'status' => 'guest',
      'l10n_non_existent' => 'Main "guest" user does not exist',
      'l10n_bad_status' => 'Main "guest" user status is incorrect');

    if ($conf['guest_id'] != $conf['default_user_id'])
    {
      $c13y_users[$conf['default_user_id']] = array(
        'password' => null,
        'l10n_non_existent' => 'Default user does not exist');
    }

    $c13y_users[$conf['webmaster_id']] = array(
      'status' => 'webmaster',
      'l10n_non_existent' => 'Main "webmaster" user does not exist',
      'l10n_bad_status' => 'Main "webmaster" user status is incorrect');

      $query = '
  select u.'.$conf['user_fields']['id'].' as id, ui.status
  from '.USERS_TABLE.' as u
    left join '.USER_INFOS_TABLE.' as ui
        on u.'.$conf['user_fields']['id'].' = ui.user_id
  where
    u.'.$conf['user_fields']['id'].' in ('.implode(',', array_keys($c13y_users)).')
  ;';


    $status = array();

    $result = pwg_query($query);
    while ($row = pwg_db_fetch_assoc($result))
    {
      $status[$row['id']] = $row['status'];
    }

    foreach ($c13y_users as $id => $data)
    {
      if (!array_key_exists($id, $status))
      {
        $c13y->add_anomaly(l10n($data['l10n_non_existent']), 'c13y_correction_user',
          array('id' => $id, 'action' => 'creation'));
      }
      else
      if (!empty($data['status']) and $status[$id] != $data['status'])
      {
        $c13y->add_anomaly(l10n($data['l10n_bad_status']), 'c13y_correction_user',
          array('id' => $id, 'action' => 'status'));
      }
    }
  }

  /**
   * Do correction user
   *
   * @param user_id, action
   * @return boolean true if ok else false
   */
  function c13y_correction_user($id, $action)
  {
    global $conf, $page;

    $result = false;

    if (!empty($id))
    {
      switch ($action)
      {
        case 'creation':
          if ($id == $conf['guest_id'])
          {
            $name = 'guest';
            $password = null;
          }
          else if  ($id == $conf['default_user_id'])
          {
            $name = 'guest';
            $password = null;
          }
          else if  ($id == $conf['webmaster_id'])
          {
            $name = 'webmaster';
            $password = generate_key(6);
          }

          if (isset($name))
          {
            $name_ok = false;
            while (!$name_ok)
            {
              $name_ok = (get_userid($name) === false);
              if (!$name_ok)
              {
                $name .= generate_key(1);
              }
            }

            $inserts = array(
              array(
                'id'       => $id,
                'username' => addslashes($name),
                'password' => $password
                ),
              );
            mass_inserts(USERS_TABLE, array_keys($inserts[0]), $inserts);

            create_user_infos($id);

            $page['infos'][] = sprintf(l10n('User "%s" created with "%s" like password'), $name, $password);

            $result = true;
          }
          break;
        case 'status':
          if ($id == $conf['guest_id'])
          {
            $status = 'guest';
          }
          else if  ($id == $conf['default_user_id'])
          {
            $status = 'guest';
          }
          else if  ($id == $conf['webmaster_id'])
          {
            $status = 'webmaster';
          }

          if (isset($status))
          {
            $updates = array(
              array(
                'user_id' => $id,
                'status'  => $status
                ),
              );
            mass_updates(USER_INFOS_TABLE,
              array('primary' => array('user_id'),'update' => array('status')),
              $updates);

            $page['infos'][] = sprintf(l10n('Status of user "%s" updated'), get_username($id));

            $result = true;
          }
          break;
      }
    }

    return $result;
  }
}

?>
