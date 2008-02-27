<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2008 PhpWebGallery Team - http://phpwebgallery.net |
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

/**
 * Check integrity
 *
 * @param void
 * @return void
 */
function check_integrity()
{
  global $page, $header_notes, $conf;

  // Ignore list
  $conf_c13y_ignore = unserialize($conf['c13y_ignore']);
  if (
        is_array($conf_c13y_ignore) and
        isset($conf_c13y_ignore['version']) and
        ($conf_c13y_ignore['version'] == PHPWG_VERSION) and
        is_array($conf_c13y_ignore['list'])
      )
  {
    $ignore_list_changed = false;
    $page['check_integrity']['ignore_list'] = $conf_c13y_ignore['list'];
  }
  else
  {
    $ignore_list_changed = true;
    $page['check_integrity']['ignore_list'] = array();
  }

  // Retrieve list
  $page['check_integrity']['list'] = array();
  $page['check_integrity']['build_ignore_list'] = array();

  add_event_handler('list_check_integrity', 'c13y_exif');
  add_event_handler('list_check_integrity', 'c13y_user');
  trigger_action('list_check_integrity');

  // Information
  if (count($page['check_integrity']['list']) > 0)
  {
    $header_notes[] =
      l10n_dec('c13y_anomaly_count', 'c13y_anomalies_count',
        count($page['check_integrity']['list']));
  }

  // Treatments
  if (!is_adviser())
  {
    if (isset($_POST['c13y_submit_correction']) and isset($_POST['c13y_selection']))
    {
      $corrected_count = 0;
      $not_corrected_count = 0;

      foreach ($page['check_integrity']['list'] as $i => $c13y)
      {
        if (!empty($c13y['correction_fct']) and
            $c13y['is_callable'] and
            in_array($c13y['id'], $_POST['c13y_selection']))
        {
          if (is_array($c13y['correction_fct_args']))
          {
            $args = $c13y['correction_fct_args'];
          }
          else
          if (!is_null($c13y['correction_fct_args']))
          {
            $args = array($c13y['correction_fct_args']);
          }
          else
          {
            $args = array();
          }
          $page['check_integrity']['list'][$i]['corrected'] = call_user_func_array($c13y['correction_fct'], $args);

          if ($page['check_integrity']['list'][$i]['corrected'])
          {
            $corrected_count += 1;
          }
          else
          {
            $not_corrected_count += 1;
          }
        }
      }

      if ($corrected_count > 0)
      {
        $page['infos'][] =
          l10n_dec('c13y_anomaly_corrected_count', 'c13y_anomalies_corrected_count',
            $corrected_count);
      }
      if ($not_corrected_count > 0)
      {
        $page['errors'][] =
          l10n_dec('c13y_anomaly_not_corrected_count', 'c13y_anomalies_not_corrected_count',
            $not_corrected_count);
      }
    }
    else
    {
      if (isset($_POST['c13y_submit_ignore']) and isset($_POST['c13y_selection']))
      {
        $ignored_count = 0;

        foreach ($page['check_integrity']['list'] as $i => $c13y)
        {
          if (in_array($c13y['id'], $_POST['c13y_selection']))
          {
            $page['check_integrity']['build_ignore_list'][] = $c13y['id'];
            $page['check_integrity']['list'][$i]['ignored'] = true;
            $ignored_count += 1;
          }
        }

        if ($ignored_count > 0)
        {
          $page['infos'][] =
            l10n_dec('c13y_anomaly_ignored_count', 'c13y_anomalies_ignored_count',
              $ignored_count);
        }
      }
    }
  }

  $ignore_list_changed =
    (
      ($ignore_list_changed) or
      (count(array_diff($page['check_integrity']['ignore_list'], $page['check_integrity']['build_ignore_list'])) > 0) or
      (count(array_diff($page['check_integrity']['build_ignore_list'], $page['check_integrity']['ignore_list'])) > 0)
      );

  if ($ignore_list_changed)
  {
    c13y_update_conf($page['check_integrity']['build_ignore_list']);
  }
}

/**
 * Display anomalies list
 *
 * @param void
 * @return void
 */
function display_check_integrity()
{
  global $template, $page;

  $check_automatic_correction = false;
  $submit_automatic_correction = false;
  $submit_ignore = false;

  if (isset($page['check_integrity']['list']) and count($page['check_integrity']['list']) > 0)
  {
    $template->set_filenames(array('check_integrity' => 'admin/check_integrity.tpl'));

    foreach ($page['check_integrity']['list'] as $i => $c13y)
    {
      $can_select = false;

      $template->assign_block_vars('c13y',
        array(
         'CLASS' => ($i % 2 == 1) ? 'row2' : 'row1',
         'ID' => $c13y['id'],
         'ANOMALY' => $c13y['anomaly']
        ));


      if (isset($c13y['ignored']))
      {
        if ($c13y['ignored'])
        {
          $template->assign_block_vars('c13y.ignore_msg', array());
        }
        else
        {
          die('$c13y[\'ignored\'] cannot be false');
        }
      }
      else
      {
        if (!empty($c13y['correction_fct']))
        {
          if (isset($c13y['corrected']))
          {
            if ($c13y['corrected'])
            {
              $template->assign_block_vars('c13y.correction_success_fct', array());
            }
            else
            {
              $template->assign_block_vars('c13y.correction_error_fct',
                array('WIKI_FOROM_LINKS' => get_htlm_links_more_info()));
            }
          }
          else if ($c13y['is_callable'])
          {
            $template->assign_block_vars('c13y.correction_fct', array());
            $template->assign_block_vars('c13y_link_check_automatic_correction.c13y_do_check', array('ID' => $c13y['id']));
            $submit_automatic_correction = true;
            $can_select = true;
          }
          else
          {
            $template->assign_block_vars('c13y.correction_bad_fct', array());
            $can_select = true;
          }
        }
        else
        {
          $can_select = true;
        }

        if (!empty($c13y['correction_fct']) and !empty($c13y['correction_msg']))
        {
          $template->assign_block_vars('c13y.br', array());
        }

        if (!empty($c13y['correction_msg']) and !isset($c13y['corrected']))
        {
          $template->assign_block_vars('c13y.correction_msg',
            array(
             'DATA' => nl2br($c13y['correction_msg'])
            ));
        }
      }

      if ($can_select)
      {
        $template->assign_block_vars('c13y.can_select', array());
        $submit_ignore = true;
      }
    }

    if ($submit_automatic_correction)
    {
      $template->assign_block_vars('c13y_submit_automatic_correction', array());
    }

    if ($submit_ignore)
    {
      $template->assign_block_vars('c13y_link_check_uncheck', array());
      $template->assign_block_vars('c13y_submit_ignore', array());
    }

    $template->concat_var('ADMIN_CONTENT', $template->parse('check_integrity', true) );
  }
}

/**
 * Returns structured anomaly data
 *
 * @param anomaly arguments
 * @return c13y anomaly array
 */
function add_c13y($anomaly, $correction_fct = null, $correction_fct_args = null, $correction_msg = null)
{
  global $page;

  $id = md5($anomaly.$correction_fct.serialize($correction_fct_args).$correction_msg);

  if (in_array($id, $page['check_integrity']['ignore_list']))
  {
    $page['check_integrity']['build_ignore_list'][] = $id;
  }
  else
  {
    $page['check_integrity']['list'][] =
      array(
        'id' => $id,
        'anomaly' => $anomaly,
        'correction_fct' => $correction_fct,
        'correction_fct_args' => $correction_fct_args,
        'correction_msg' => $correction_msg,
        'is_callable' => is_callable($correction_fct));
  }
}

/**
 * Update table config
 *
 * @param ignore list array
 * @return void
 */
function c13y_update_conf($ignore_list = array())
{
  $conf_c13y_ignore =  array();
  $conf_c13y_ignore['version'] = PHPWG_VERSION;
  $conf_c13y_ignore['list'] = $ignore_list;
  $query = 'update '.CONFIG_TABLE.' set value =\''.serialize($conf_c13y_ignore).'\'where param = \'c13y_ignore\';';
  pwg_query($query);
}

/**
 * Apply maintenance
 *
 * @param void
 * @return void
 */
function c13y_maintenance()
{
  c13y_update_conf();
}

/**
 * Returns links more informations
 *
 * @param void
 * @return html links
 */
function get_htlm_links_more_info()
{
  $pwg_links = pwg_URL();
  $link_fmt = '<a href="%s" onclick="window.open(this.href, \'\'); return false;">%s</a>';
  return
    sprintf
    (
      l10n('c13y_more_info'),
      sprintf($link_fmt, $pwg_links['FORUM'], l10n('c13y_more_info_forum')),
      sprintf($link_fmt, $pwg_links['WIKI'], l10n('c13y_more_info_wiki'))
    );
}

/**
 * Check exif
 *
 * @param void
 * @return void
 */
function c13y_exif()
{
  global $conf;

  foreach (array('show_exif', 'use_exif') as $value)
  {
    if (($conf[$value]) and (!function_exists('read_exif_data')))
    {
      add_c13y(
        sprintf(l10n('c13y_exif_anomaly'), '$conf[\''.$value.'\']'),
        null,
        null,
        sprintf(l10n('c13y_exif_correction'), '$conf[\''.$value.'\']')
        .'<BR />'.
        get_htlm_links_more_info());
    }
  }
}

/**
 * Check user
 *
 * @param void
 * @return void
 */
function c13y_user()
{
  global $conf;

  $c13y_users = array();
  $c13y_users[$conf['guest_id']] = array(
    'status' => 'guest',
    'l10n_non_existent' => 'c13y_guest_non_existent',
    'l10n_bad_status' => 'c13y_bad_guest_status');

  if ($conf['guest_id'] != $conf['default_user_id'])
  {
    $c13y_users[$conf['default_user_id']] = array(
      'password' => null,
      'l10n_non_existent' => 'c13y_default_non_existent');
  }

  $c13y_users[$conf['webmaster_id']] = array(
    'status' => 'webmaster',
    'l10n_non_existent' => 'c13y_webmaster_non_existent',
    'l10n_bad_status' => 'c13y_bad_webmaster_status');

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
  while ($row = mysql_fetch_array($result))
  {
    $status[$row['id']] = $row['status'];
  }

  foreach ($c13y_users as $id => $data)
  {
    if (!array_key_exists($id, $status))
    {
      add_c13y(l10n($data['l10n_non_existent']), 'c13y_correction_user',
        array('id' => $id, 'action' => 'creation'));
    }
    else
    if (!empty($data['status']) and $status[$id] != $data['status'])
    {
      add_c13y(l10n($data['l10n_bad_status']), 'c13y_correction_user',
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
              'username' => $name,
              'password' => $password
              ),
            );
          mass_inserts(USERS_TABLE, array_keys($inserts[0]), $inserts);

          create_user_infos($id);

          $page['infos'][] = sprintf(l10n('c13y_user_created'), $name, $password);

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

          $page['infos'][] = sprintf(l10n('c13y_user_status_updated'), get_username($id));

          $result = true;
        }
        break;
    }
  }

  return $result;
}

?>
