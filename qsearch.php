<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_GUEST);

if (empty($_GET['q']))
{
  redirect( make_index_url() );
}

$search = array();
$search['q']=$_GET['q'];

$query = '
SElECT id FROM '.SEARCH_TABLE.'
  WHERE rules = \''.addslashes(serialize($search)).'\'
;';
$search_id = array_from_query( $query, 'id');
if ( !empty($search_id) )
{
  $search_id = $search_id[0];
  $query = '
UPDATE '.SEARCH_TABLE.'
  SET last_seen=NOW()
  WHERE id='.$search_id;
  pwg_query($query);
}
else
{
  $query ='
INSERT INTO '.SEARCH_TABLE.'
  (rules, last_seen)
  VALUES
  (\''.addslashes(serialize($search)).'\', NOW() )
;';
  pwg_query($query);
  $search_id = pwg_db_insert_id(SEARCH_TABLE);
}

redirect(
  make_index_url(
    array(
      'section' => 'search',
      'search'  => $search_id,
      )
    )
  );
?>