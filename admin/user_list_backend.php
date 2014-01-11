<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2014 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
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

define('PHPWG_ROOT_PATH','../');
define('IN_ADMIN', true);

include_once(PHPWG_ROOT_PATH.'include/common.inc.php');

check_status(ACCESS_ADMINISTRATOR);
	
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */
	
/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array('id', 'username', 'status', 'mail_address', 'registration_date');
$aColumns = trigger_change('user_list_columns', $aColumns);
	
/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "id";
	
/* DB table to use */
$sTable = USERS_TABLE.' INNER JOIN '.USER_INFOS_TABLE.' AS ui ON id = ui.user_id';

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
  $sLimit = "LIMIT ".pwg_db_real_escape_string( $_REQUEST['iDisplayStart'] ).", ".
    pwg_db_real_escape_string( $_REQUEST['iDisplayLength'] );
}
	
	
/*
 * Ordering
 */
if ( isset( $_REQUEST['iSortCol_0'] ) )
{
  $sOrder = "ORDER BY  ";
  for ( $i=0 ; $i<intval( $_REQUEST['iSortingCols'] ) ; $i++ )
  {
    if ( $_REQUEST[ 'bSortable_'.intval($_REQUEST['iSortCol_'.$i]) ] == "true" )
    {
      $sOrder .= $aColumns[ intval( $_REQUEST['iSortCol_'.$i] ) ]."
				 	".pwg_db_real_escape_string( $_REQUEST['sSortDir_'.$i] ) .", ";
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
	
while ( $aRow = pwg_db_fetch_array( $rResult ) )
{
  $row = array();
  for ( $i=0 ; $i<count($aColumns) ; $i++ )
  {
    if ( $aColumns[$i] == "status" )
    {
      $row[] = l10n('user_status_'.$aRow[ $aColumns[$i] ]);
    }
    else if ( $aColumns[$i] != ' ' )
    {
      /* General output */
      $row[] = $aRow[ $aColumns[$i] ];
    }
  }
  $output['aaData'][] = $row;
}

$output = trigger_change('after_render_user_list', $output);
	
echo json_encode( $output );
?>