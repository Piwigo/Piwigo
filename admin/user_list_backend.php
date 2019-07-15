<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

define('PHPWG_ROOT_PATH','../');
define('IN_ADMIN', true);

include_once(PHPWG_ROOT_PATH.'include/common.inc.php');

check_status(ACCESS_ADMINISTRATOR);

check_input_parameter('iDisplayStart', $_REQUEST, false, PATTERN_ID);
check_input_parameter('iDisplayLength', $_REQUEST, false, PATTERN_ID);

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */
	
/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array(
  $conf['user_fields']['id'],
  $conf['user_fields']['username'],
  'status',
  $conf['user_fields']['email'],
  'recent_period',
  'level',
  'registration_date'
  );

$aColumns = trigger_change('user_list_columns', $aColumns);
	
/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = 'user_id';
	
/* DB table to use */
$sTable = USERS_TABLE.' INNER JOIN '.USER_INFOS_TABLE.' AS ui ON '.$conf['user_fields']['id'].' = ui.user_id';

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
 * no need to edit below this line
 */

/* 
 * Paging
 */
$sLimit = "";
if ( isset( $_REQUEST['iDisplayStart'] ) && $_REQUEST['iDisplayLength'] != '-1' )
{
  $sLimit = "LIMIT ".$_REQUEST['iDisplayStart'].", ".$_REQUEST['iDisplayLength'];
}
	
	
/*
 * Ordering
 */
if ( isset( $_REQUEST['iSortCol_0'] ) )
{
  $sOrder = "ORDER BY  ";
  for ( $i=0 ; $i<intval( $_REQUEST['iSortingCols'] ) ; $i++ )
  {
    check_input_parameter('iSortCol_'.$i, $_REQUEST, false, PATTERN_ID);

    if ( $_REQUEST[ 'bSortable_'.$_REQUEST['iSortCol_'.$i] ] == "true" )
    {
      check_input_parameter('sSortDir_'.$i, $_REQUEST, false, '/^(asc|desc)$/');

      $sOrder .= $aColumns[ $_REQUEST['iSortCol_'.$i] ].' '.$_REQUEST['sSortDir_'.$i].', ';
    }
  }
		
  $sOrder = substr_replace( $sOrder, "", -2 );
  if ( $sOrder == "ORDER BY" )
  {
    $sOrder = "";
  }
}
	
	
/* 
 * Filtering
 * NOTE this does not match the built-in DataTables filtering which does it
 * word by word on any field. It's possible to do here, but concerned about efficiency
 * on very large tables, and MySQL's regex functionality is very limited
 */
$sWhere = "";
if ( $_REQUEST['sSearch'] != "" )
{
  $sWhere = "WHERE (";
  for ( $i=0 ; $i<count($aColumns) ; $i++ )
  {
    $sWhere .= $aColumns[$i]." LIKE '%".pwg_db_real_escape_string( $_REQUEST['sSearch'] )."%' OR ";
  }
  $sWhere = substr_replace( $sWhere, "", -3 );
  $sWhere .= ')';
}
	
/* Individual column filtering */
for ( $i=0 ; $i<count($aColumns) ; $i++ )
{
  if (isset($_REQUEST['bSearchable_'.$i]) && isset($_REQUEST['sSearch_'.$i])
      &&$_REQUEST['bSearchable_'.$i] == "true" && $_REQUEST['sSearch_'.$i] != ''
    )
  {
    if ( $sWhere == "" )
    {
      $sWhere = "WHERE ";
    }
    else
    {
      $sWhere .= " AND ";
    }
    $sWhere .= $aColumns[$i]." LIKE '%".pwg_db_real_escape_string($_REQUEST['sSearch_'.$i])."%' ";
  }
}
	
	
/*
 * SQL queries
 * Get data to display
 */
$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
	";
$rResult = pwg_query($sQuery);
	
/* Data set length after filtering */
$rResultFilterTotal = pwg_query('SELECT FOUND_ROWS();');
list($iFilteredTotal) = pwg_db_fetch_row($rResultFilterTotal);
	
/* Total data set length */
$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable
	";
$rResultTotal = pwg_query($sQuery);
$aResultTotal = pwg_db_fetch_array($rResultTotal);
$iTotal = $aResultTotal[0];
	
	
/*
 * Output
 */
$output = array(
  "sEcho" => intval($_REQUEST['sEcho']),
  "iTotalRecords" => $iTotal,
  "iTotalDisplayRecords" => $iFilteredTotal,
  "aaData" => array()
	);

$user_ids = array();

while ( $aRow = pwg_db_fetch_array( $rResult ) )
{
  $user_ids[] = $aRow[ $conf['user_fields']['id'] ];
  
  $row = array();
  for ( $i=0 ; $i<count($aColumns) ; $i++ )
  {
    if ( $aColumns[$i] == "status" )
    {
      $row[] = l10n('user_status_'.$aRow[ $aColumns[$i] ]);
    }
    else if ( $aColumns[$i] == "level" )
    {
      $row[] = $aRow[ $aColumns[$i] ] == 0 ? '' : l10n(sprintf('Level %d', $aRow[ $aColumns[$i] ]));
    }
    else if ( $aColumns[$i] != ' ' )
    {
      /* General output */
      $colname = $aColumns[$i];
      foreach ($conf['user_fields'] as $real_name => $alias)
      {
        if ($aColumns[$i] == $real_name)
        {
          $colname = $alias;
        }
      }
      
      $row[] = $aRow[$colname];
    }

  }
  $output['aaData'][] = $row;
}

// replace "recent_period" by the list of groups
if (count($user_ids) > 0)
{
  $groups_of_user = array();
  
  $query = '
SELECT
    user_id,
    GROUP_CONCAT(name ORDER BY name SEPARATOR ", ") AS groups
  FROM '.USER_GROUP_TABLE.'
    JOIN `'.GROUPS_TABLE.'` ON id = group_id
  WHERE user_id IN ('.implode(',', $user_ids).')
  GROUP BY user_id
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    $groups_of_user[ $row['user_id'] ] = $row['groups'];
  }

  $key_replace = array_search('recent_period', $aColumns);
  
  // replacement
  foreach (array_keys($output['aaData']) as $idx)
  {
    $user_id = $output['aaData'][$idx][0];
    $output['aaData'][$idx][$key_replace] = isset($groups_of_user[$user_id]) ? $groups_of_user[$user_id] : '';
  }
}

$output = trigger_change('after_render_user_list', $output);
	
echo json_encode( $output );
?>