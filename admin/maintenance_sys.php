<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die ("Hacking attempt!");
}

// +-----------------------------------------------------------------------+
// |                    Only Webmaster can see this tab                    |
// +-----------------------------------------------------------------------+

if (is_webmaster())
{
  // Get system activities data
  if (isset($_GET['method']) && 'pwg.activity_sys.getList' == $_GET['method'])
  {
    $response = array();
    $data = array();

    $query = '
  SELECT
      activity_id,
      object,
      object_id,
      action,
      performed_by,
      occured_on,
      details,
  IF(performed_by = 0, \'System\', '.$conf['user_fields']['username'].') AS username
  FROM '.ACTIVITY_TABLE.'
  LEFT JOIN '.USERS_TABLE.' ON performed_by = '.$conf['user_fields']['id'].'
  WHERE object = \'system\'
  ORDER BY occured_on DESC';
    
    // Format our data for frontend
    $result = pwg_query($query);
    while ($rows = pwg_db_fetch_assoc($result))
    {
      $major_infos = false;
      $object = '';
      $object_icon = '';
      $action_icon = '';
      $action_color = '';
      $action = $rows['action'];
      $date = '';
      $hour = '';
      $details = unserialize($rows['details']);
      $detail = array(
        'type' => 'empty',
      );

      // For each categories (Core, Plugin and Theme) we need to format theirs actions
      switch ($rows['object_id'])
      {
        case ACTIVITY_SYSTEM_CORE:
          $object_icon = 'icon-piwigo';
          $object = l10n('Core');

          switch ($rows['action'])
          {
            case 'install':
              $action_icon = 'icon-download';
              $action_color = 'icon-green';
              $action = l10n('Install');
              break;
            
            case 'config':
              $action_icon = 'icon-cog-alt';
              $action_color = 'icon-yellow';
              $action = l10n('Configuration');
              // for config we need to specific format details
              if (isset($details['config_section']))
              {
                $c_icon = '';
                $c_text = '';
                switch ($details['config_section'])
                {
                  case 'main':
                    $c_icon = 'icon-cog';
                    $c_text = l10n('General');
                    break;

                  case 'watermark':
                    $c_icon = 'icon-file-image';
                    $c_text = l10n('Watermark');
                    break;

                  case 'sizes':
                    $c_icon = 'icon-zoom-square';
                    $c_text = l10n('Photo sizes');
                    // sizes have 2 params always Photo sizes and sometimes config_action
                    if (isset($details['config_action']) && 'restore_settings' == $details['config_action'])
                    {
                      $detail[] = array(
                        'icon' => 'icon-back-in-time',
                        'text' => l10n('Set as default')
                      );
                    }
                    break;

                  case 'comments':
                    $c_icon = 'icon-chat';
                    $c_text = l10n('Comments');
                    break;

                  case 'display':
                    $c_icon = 'icon-television';
                    $c_text = l10n('Display');
                    break;

                  default:
                    $c_icon = 'icon-cog-alt';
                    $c_text = $details['config_section'];
                    break;
                }

                $detail['type'] = 'config_section';
                $detail[] = array(
                  'icon' => $c_icon,
                  'text' => $c_text
                );
              }
              break;

            case 'maintenance':
              $action_icon = 'icon-cone';
              $action_color = 'icon-yellow';
              $action = l10n('Maintenance');
              // for maintenance we need to specific format details
              if (isset($details['maintenance_action']))
              {
                $action_detail = $details['maintenance_action'];
                $detail = array(
                  'type' => 'maintenance_action',
                  'icon' => $maint_actions[$action_detail]['icon'] ?? 'icon-cone',
                  'text' => $maint_actions[$action_detail]['label'] ?? $action_detail,
                );
              }
              break;

            case 'update':
              $action_icon = 'icon-arrows-cw';
              $action_color = 'icon-blue';
              $action = l10n('Update');
              $major_infos = true;
              break;

            case 'autoupdate':
              $action_icon = 'icon-arrows-cw';
              $action_color = 'icon-blue';
              $action = l10n('Auto-update');
              $major_infos = true;
              break;

            default:
              $action_icon = 'icon-download';
              $action_color = 'icon-yellow';
              break;
          }
          break;

        case ACTIVITY_SYSTEM_PLUGIN:
          $object_icon = 'icon-puzzle';
          $object = str_replace(['_', '-'], ' ', $details['plugin_id']);
          switch ($rows['action'])
          {
            case 'install':
              $action_icon = 'icon-download';
              $action_color = 'icon-green';
              $action = l10n('Install');
              break;

            case 'update':
              $action_icon = 'icon-arrows-cw';
              $action_color = 'icon-blue';
              $action = l10n('Update');
              break;

            case 'activate':
              $action_icon = 'icon-check';
              $action_color = 'icon-green';
              $action = l10n('Activate');
              break;

            case 'deactivate':
              $action_icon = 'icon-block';
              $action_color = 'icon-purple';
              $action = l10n('Deactivate');
              break;

            case 'uninstall':
              $action_icon = 'icon-trash-1';
              $action_color = 'icon-red';
              $action = l10n('Uninstall');
              break;

            case 'restore':
              $action_icon = 'icon-back-in-time';
              $action_color = 'icon-blue';
              $action = l10n('Restore');
              break;

            case 'delete':
              $action_icon = 'icon-trash-1';
              $action_color = 'icon-red';
              $action = l10n('Delete');
              // for delete we need to specific format details
              if (isset($details['db_version']))
              {
                $detail['type'] = 'db_fs_version';
                $detail[] = array(
                  'icon' => 'icon-flow-branch',
                  'text' => 'database : ' . $details['db_version']
                );
              }
              if (isset($details['fs_version']))
              {
                $detail['type'] = 'db_fs_version';
                $detail[] = array(
                  'icon' => 'icon-flow-branch',
                  'text' => 'filesystem : ' . $details['fs_version']
                );
              }
              break;

            case 'autoupdate':
              $action_icon = 'icon-arrows-cw';
              $action_color = 'icon-blue';
              $action = l10n('Auto-update');
              break;

            default:
              $action_icon = 'icon-puzzle';
              $action_color = 'icon-yellow';
              break;
          }
          break;

        case ACTIVITY_SYSTEM_THEME:
          $object_icon = 'icon-brush';
          $object = str_replace(['_', '-'], ' ', $details['theme_id']);

          switch ($rows['action'])
          {
            case 'install':
              $action_icon = 'icon-download';
              $action_color = 'icon-green';
              $action = l10n('Install');
              break;

            case 'activate':
              $action_icon = 'icon-check';
              $action_color = 'icon-green';
              $action = l10n('Activate');
              break;

            case 'deactivate':
              $action_icon = 'icon-block';
              $action_color = 'icon-purple';
              $action = l10n('Deactivate');
              break;

            case 'delete':
              $action_icon = 'icon-trash-1';
              $action_color = 'icon-red';
              $action = l10n('Delete');
              break;

            case 'set_default':
              $action_icon = 'icon-star';
              $action_color = 'icon-yellow';
              $action = l10n('Set as default');
              break;

            case 'update':
              $action_icon = 'icon-arrows-cw';
              $action_color = 'icon-blue';
              $action = l10n('Update');
              break;

            default:
              $action_icon = 'icon-brush';
              $action_color = 'icon-yellow';
              break;
          }
          break;

        default:
          break;
      }

      // For each lines we need to format theirs details (general details)
      if (isset($details['from_version']))
      {
        $detail = array(
          'type' => 'from_to',
          array(
            'icon' => 'icon-flow-branch',
            'text' => $details['from_version'],
          ),
          array(
            'icon' => isset($details['to_version']) ? 'icon-flow-branch' : 'icon-block',
            'text' => isset($details['to_version']) ? $details['to_version'] : ($details['result'] ?? ''),
          ),
        );
      }
      else if (isset($details['version']))
      {
        $detail = array(
          'type' => 'version',
          'icon' => 'icon-flow-branch',
          'text' => $details['version']
        );
      }
      else if (isset($details['result']))
      {
        $detail = array(
          'type' => 'error',
          'icon' => 'icon-block',
          'text' => $details['result']
        );
      }

      // Format our data before send
      // This data will be manipulate by maintenance_sys.js
      list($date, $hour) = explode(' ', $rows['occured_on']);
      $data[] = array(
        'major_infos' => $major_infos,
        'id' => $rows['activity_id'],
        'object_icon' => $object_icon,
        'object' => ucwords($object),
        'action_icon' => $action_icon,
        'action_color' => $action_color,
        'action' => $action,
        'user_id' => $rows['performed_by'],
        'username' => $rows['username'],
        'date' => format_date($date),
        'hour' => $hour,
        'detail' => $detail
      );
    }

    // Now we good to send our response data
    $response = array(
      'data' => $data,
    );
    echo json_encode($response);
    exit;
  }
}
else
{
  $page['warnings'][] = str_replace('%s', l10n('user_status_webmaster'), l10n('%s status is required to edit parameters.'));
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->assign('isWebmaster', (is_webmaster()) ? 1 : 0);
$template->set_filenames(array('maintenance'=>'maintenance_sys.tpl'));

// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'maintenance');
?>