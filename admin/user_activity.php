<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// | tabs                                                                  |
// +-----------------------------------------------------------------------+

$page['tab'] = 'user_activity';
include(PHPWG_ROOT_PATH.'admin/include/user_tabs.inc.php');


if (isset($_GET['type']) && 'download_logs' == $_GET['type']) {
  
  global $conf;

  $output_lines = array();

  $query = '
SELECT
    activity_id,
    performed_by,
    object,
    object_id,
    action,
    ip_address,
    occured_on,
    details,
    '.$conf['user_fields']['username'].' AS username
  FROM '.ACTIVITY_TABLE.'
    JOIN '.USERS_TABLE.' AS u ON performed_by = u.'.$conf['user_fields']['id'].'
  ORDER BY activity_id DESC
;';

  $result = pwg_query($query);
  array_push($output_lines, ['User', 'ID_User', 'Object', 'Object_ID', 'Action', 'Date', 'Hour', 'IP_Address', 'Details']);
  while ($row = pwg_db_fetch_assoc($result))
  {
    $row['details'] = str_replace('`groups`', 'groups', $row['details']);
    $row['details'] = str_replace('`rank`', 'rank', $row['details']);

    list($date, $hour) = explode(' ', $row['occured_on']);

    $output_lines[] = array(
      'username' => $row['username'],
      'user_id' => $row['performed_by'],
      'object' => $row['object'],
      'object_id' => $row['object_id'],
      'action' => $row['action'],
      'date' => $date,
      'hour' => $hour,
      'ip_address' => $row['ip_address'],
      'details' => $row['details'],
    );
  }

  header('Content-type: application/csv');
  header('Content-Disposition: attachment; filename='.date('YmdGis').'piwigo_activity_log.csv');
  header("Content-Transfer-Encoding: UTF-8");

  $f = fopen('php://output', 'w');  
      foreach ($output_lines as $line) { 
          fputcsv($f, $line, ";"); 
      }
  fclose($f);
  
  exit();
}

// +-----------------------------------------------------------------------+
// |                       template initialization                         |
// +-----------------------------------------------------------------------+
$template->set_filename('user_activity', 'user_activity.tpl');
$template->assign('ADMIN_PAGE_TITLE', l10n('User Activity logs'));

// +-----------------------------------------------------------------------+
// |                          sending html code                            |
// +-----------------------------------------------------------------------+
$template->assign(array(
  'PWG_TOKEN' => get_pwg_token(),
  'INHERIT' => $conf['inheritance_by_default'],
  'CACHE_KEYS' => get_admin_client_cache_keys(array('users')),
  ));

$template->assign_var_from_handle('ADMIN_CONTENT', 'user_activity');

?>