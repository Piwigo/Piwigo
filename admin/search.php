<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2004 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
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

define('PHPWG_ROOT_PATH','../');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
include_once( PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php' );

//----------------------------------------------------- template initialization
$title = $lang['Find_username'];
include(PHPWG_ROOT_PATH.'include/page_header.php');

$template->set_filenames( array('search'=>'admin/search_username.tpl') );
$template->assign_vars(array(
  'USERNAME'=>( !empty($search_match) ) ? strip_tags($search_match) : '',
  
  'L_SEARCH_USERNAME'=>$lang['Find_username'],
  'L_SEARCH'=>$lang['search'],
  'L_SEARCH_EXPLAIN'=>$lang['search_explain'],
  'L_SELECT'=>$lang['Select'],
  'L_UPDATE_USERNAME'=>$lang['Look_up_user'],
  'L_CLOSE_WINDOW'=>$lang['Close'],

  'F_SEARCH_ACTION' => add_session_id($_SERVER['PHP_SELF']),
  ));

//----------------------------------------------------------------- form action
//
// Define initial vars
//
if ( isset($_POST['mode']) || isset($_GET['mode']) )
{
  $mode = ( isset($_POST['mode']) ) ? $_POST['mode'] : $_GET['mode'];
}
else
{
  $mode = '';
}
$search_match = ''; 
if ( isset($_POST['search_username']) )
{
  $search_match = $_POST['search_username'];
}
  
$username_list = '';
if ( !empty($search_match) )
{
  $username_search = preg_replace('/\*/', '%', trim(strip_tags($search_match)));
  
  $sql = "SELECT username 
    FROM " . USERS_TABLE . " 
    WHERE username LIKE '" . str_replace("\'", "''", $username_search) . "' 
    AND id <> ".ANONYMOUS."
		ORDER BY username";
  if ( !($result = pwg_query($sql)) )
  {
    die('Could not obtain search results');
  }
  
  if ( $row = mysql_fetch_array($result) )
  {
    do
    {
    $username_list .= '<option value="' . $row['username'] . '">' . $row['username'] . '</option>';
    }
    while ( $row = mysql_fetch_array($result) );
  }
  else
  {
    $username_list .= '<option>' . $lang['No_match']. '</option>';
  }
  mysql_free_result($result);
}
  
//------------------------------------------------------------------ users list
if ( !empty($username_list))
{
  $template->assign_block_vars('switch_select_name', array(
    'F_USERNAME_OPTIONS'=>$username_list
    ));
}

$template->pparse('search');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>