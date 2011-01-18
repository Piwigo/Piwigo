<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
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