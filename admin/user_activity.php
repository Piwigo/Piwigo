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

check_input_parameter('photo', $_GET, false, PATTERN_ID);
check_input_parameter('album', $_GET, false, PATTERN_ID);
check_input_parameter('group', $_GET, false, PATTERN_ID);

// +-----------------------------------------------------------------------+
// | tabs                                                                  |
// +-----------------------------------------------------------------------+

$page['tab'] = 'user_activity';
include(PHPWG_ROOT_PATH.'admin/include/user_tabs.inc.php');


if (isset($_GET['type']) && 'download_logs' == $_GET['type'])
{
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
    WHERE object = \'user\'
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
          fputcsv($f, $line, ";", '"', '\\'); 
      }
  fclose($f);
  
  exit();
}

// +-----------------------------------------------------------------------+
// |                       template initialization                         |
// +-----------------------------------------------------------------------+
$template->set_filename('user_activity', 'user_activity.tpl');
$template->assign('ADMIN_PAGE_TITLE', l10n('Users'));

// +-----------------------------------------------------------------------+
// |                          sending html code                            |
// +-----------------------------------------------------------------------+
$template->assign(array(
  'PWG_TOKEN' => get_pwg_token(),
  'INHERIT' => $conf['inheritance_by_default'],
  'CACHE_KEYS' => get_admin_client_cache_keys(array('users')),
  ));

$query = '
SELECT 
    performed_by, 
    COUNT(*) as counter 
  FROM '.ACTIVITY_TABLE.'
  WHERE object != \'system\'
  GROUP BY performed_by
;';

$nb_lines_for_user = query2array($query, 'performed_by', 'counter');

if (count($nb_lines_for_user) > 0)
{
  $query = '
  SELECT 
      '.$conf['user_fields']['id'].' AS id, 
      '.$conf['user_fields']['username'].' AS username 
    FROM '.USERS_TABLE.' 
    WHERE '.$conf['user_fields']['id'].' IN ('.implode(',', array_keys($nb_lines_for_user)).');';
}

$username_of = query2array($query, 'id', 'username');

$filterable_users = array();

foreach ($nb_lines_for_user as $id => $nb_line) {
  array_push(
    $filterable_users, 
    array(
      'id' => $id,
      'username' => isset($username_of[$id]) ? $username_of[$id] : 'user#'.$id,
      'nb_lines' => $nb_line,
    )
  );
}
$template->assign('ulist', $filterable_users);

$query = '
SELECT COUNT(*)
  FROM '.USERS_TABLE.'
;';

list($nb_users) = pwg_db_fetch_row(pwg_query($query));
$template->assign('nb_users', $nb_users);

$query = '
SELECT
    occured_on
  FROM '.ACTIVITY_TABLE.'
  ORDER BY activity_id ASC
  LIMIT 1
;';
list($min_date) = pwg_db_fetch_row(pwg_query($query));

$query = '
SELECT
    occured_on
  FROM '.ACTIVITY_TABLE.'
  ORDER BY activity_id DESC
  LIMIT 1
;';
list($max_date) = pwg_db_fetch_row(pwg_query($query));

$template->assign(
  'ACTIVITY_DATES',
  array(
    'min' => empty($min_date) ? '' : substr($min_date, 0, 10),
    'max' => empty($max_date) ? '' : substr($max_date, 0, 10),
  )
);

$additional_filt_type = false;
$additional_filt_name = null;
$additional_filt_value = null;

$additional_filters = array(
  'photo' => IMAGES_TABLE,
  'album' => CATEGORIES_TABLE,
  'group' => GROUPS_TABLE,
);

foreach ($additional_filters as $filter_key => $filter_table)
{
  if (isset($_GET[$filter_key]))
  {
    $query = '
SELECT
    name
  FROM '.$filter_table.'
  WHERE id = '.$_GET[$filter_key].'
;';
    $rows = query2array($query);

    if (count($rows) == 0)
    {
      fatal_error($filter_key.' #'.$_GET[$filter_key].' does not exist');
    }

    $additional_filt_type = $filter_key;
    $additional_filt_name = $rows[0]['name'];
    $additional_filt_value = $_GET[$filter_key];

    break;
  }
}

$template->assign('ADDITIONAL_FILT', array(
  'type' => $additional_filt_type,
  'name' => $additional_filt_name,
  'value' => $additional_filt_value
));

$query = '
SELECT
    object, 
    action, 
    count(*) AS counter
  FROM '.ACTIVITY_TABLE.'
  WHERE object != \'system\'';

  if ($additional_filt_type)
  {
    $query .= '
    AND object = "'.$additional_filt_type.'"';
  }

  $query .= '
  GROUP BY 
    action,
    object
  ORDER BY object ASC
  ;';


$actions = query2array($query);
foreach($actions as &$action){
  $action['value'] = $action['object'].'/'.$action['action'];
}

$template->assign('ACTIONS', $actions);

$template->assign_var_from_handle('ADMIN_CONTENT', 'user_activity');

?>