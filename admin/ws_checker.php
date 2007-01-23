<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
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

// Next evolution... 
// Out of parameter WS management
// The remainer objective is to check 
//  -  Does Web Service working properly?
//  -  Does any access return something really?
//     Give a way to check to the webmaster...
// These questions are one of module name explainations (checker).

if((!defined("PHPWG_ROOT_PATH")) or (!$conf['allow_web_services']))
{
  die('Hacking attempt!');
}
include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);


// accepted queries
$req_type_list = official_req();

//--------------------------------------------------------- update informations

// Is a new access required?

if (isset($_POST['wsa_submit']))
{
// Check $_post (Some values are commented - maybe a future use)
$add_partner = htmlspecialchars( $_POST['add_partner'], ENT_QUOTES);
$add_access = check_target( $_POST['add_access']) ;
$add_start = 0; // ( is_numeric($_POST['add_start']) ) ? $_POST['add_start']:0; 
$add_end = ( is_numeric($_POST['add_end']) ) ? $_POST['add_end']:0;
$add_request = ( ctype_alpha($_POST['add_request']) ) ?
  $_POST['add_request']:'';
$add_high = 'true'; // ( $_POST['add_high'] == 'true' ) ? 'true':'false';
$add_normal = 'true'; // ( $_POST['add_normal'] == 'true' ) ? 'true':'false';
$add_limit = ( is_numeric($_POST['add_limit']) ) ? $_POST['add_limit']:1; 
$add_comment = htmlspecialchars( $_POST['add_comment'], ENT_QUOTES);
if ( strlen($add_partner) < 8 )
{
}
  $query = '
INSERT INTO '.WEB_SERVICES_ACCESS_TABLE.' 
( `name` , `access` , `start` , `end` , `request` , 
  `high` , `normal` , `limit` , `comment` ) 
VALUES (' . "
  '$add_partner', '$add_access',
  ADDDATE( NOW(), INTERVAL $add_start DAY),
  ADDDATE( NOW(), INTERVAL $add_end DAY),
  '$add_request', '$add_high', '$add_normal', '$add_limit', '$add_comment' );";

  pwg_query($query);
  
  $template->assign_block_vars(
    'update_result',
    array(
      'UPD_ELEMENT'=> $lang['ws_adding_legend'].$lang['ws_success_upd'],
      )
  );
}

// Next, Update selected access
if (isset($_POST['wsu_submit']))
{
  $upd_end = ( is_numeric($_POST['upd_end']) ) ? $_POST['upd_end']:0;
  $settxt = ' end = ADDDATE(NOW(), INTERVAL '. $upd_end .' DAY)';

  if ((isset($_POST['selection'])) and (trim($settxt) != ''))
  {
    $uid = (int) $_POST['selection'];
    $query = '
    UPDATE '.WEB_SERVICES_ACCESS_TABLE.' 
    SET '.$settxt.'
    WHERE id = '.$uid.'; ';
    pwg_query($query);
    $template->assign_block_vars(
      'update_result',
      array(
        'UPD_ELEMENT'=> $lang['ws_update_legend'].$lang['ws_success_upd'],
        )
    );
  } else {
    $template->assign_block_vars(
      'update_result',
      array(
        'UPD_ELEMENT'=> $lang['ws_update_legend'].$lang['ws_failed_upd'],
        )
    );
  }
}
// Next, Delete selected access

if (isset($_POST['wsX_submit']))
{
  if ((isset($_POST['delete_confirmation']))
   and (isset($_POST['selection'])))
  {
    $uid = (int) $_POST['selection'];
    $query = 'DELETE FROM '.WEB_SERVICES_ACCESS_TABLE.'
               WHERE id = '.$uid.'; ';
    pwg_query($query);
    $template->assign_block_vars(
      'update_result',
      array(
        'UPD_ELEMENT'=> $lang['ws_delete_legend'].$lang['ws_success_upd'],
        )
    );
  } else {
    $template->assign_block_vars(
      'update_result',
      array(
        'UPD_ELEMENT'=> $lang['Not selected / Not confirmed']
        .$lang['ws_failed_upd'],
        )
    );
  } 
}



$template->assign_vars(
  array(
    'DEFLT_HIGH_YES' => '',
    'DEFLT_HIGH_NO' => 'checked',
    'DEFLT_NORMAL_YES' => '',
    'DEFLT_NORMAL_NO' => 'checked',
    'U_HELP' => PHPWG_ROOT_PATH.'popuphelp.php?page=web_service',    
    )
  );

// Build where
$where = '';
$order = ' ORDER BY `id` DESC' ;

$query = '
SELECT *
  FROM '.WEB_SERVICES_ACCESS_TABLE.'
WHERE 1=1  '
.$where.
' '
.$order.
';';
$result = pwg_query($query);
$acc_list = mysql_num_rows($result);
$result = pwg_query($query);
// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(
  array(
    'ws_checker' => 'admin/ws_checker.tpl'
    )
  );

$selected = 'selected="selected"';
$num=0;
if ( $acc_list > 0 )
{
  $template->assign_block_vars(
    'acc_list', array() );
}

// Access List
while ($row = mysql_fetch_array($result))
{
  $num++;
  $template->assign_block_vars(
    'acc_list.access',
     array(
       'CLASS' => ($num % 2 == 1) ? 'row1' : 'row2',
       'ID'               => $row['id'],
       'NAME'             => 
         (is_adviser()) ? '*********' : $row['name'],       
       'ACCESS'           => $row['access'],
       'START'            => $row['start'],
       'END'              => $row['end'],
       'FORCE'            => $row['request'],
       'HIGH'             => $row['high'],
       'NORMAL'           => $row['normal'],
       'LIMIT'            => $row['limit'],
       'COMMENT'          => $row['comment'],
       'SELECTED'         => '',
     )
  );
}

$template->assign_block_vars(
  'add_request',
   array(
     'VALUE'=> '',
     'CONTENT' => '',
     'SELECTED' => $selected,
   )
);
foreach ($req_type_list as $value) {

  $template->assign_block_vars(
    'add_request',
     array(
       'VALUE'=> $value,
       'CONTENT' => $lang['ws_'.$value],
       'SELECTED' => '',
     )
  );
}

foreach ($conf['ws_allowed_limit'] as $value) {
  $template->assign_block_vars(
    'add_limit',
     array(
       'VALUE'=> $value,
       'CONTENT' => $value,
       'SELECTED' => ($conf['ws_allowed_limit'][0] == $value) ? $selected:'',
     )
  );
}

// Postponed Start Date 
// By default 0, 1, 2, 3, 5, 7, 14 or 30 days
foreach ($conf['ws_postponed_start'] as $value) {
  $template->assign_block_vars(
    'add_start',
     array(
       'VALUE'=> $value,
       'CONTENT' => $value,
       'SELECTED' => ($conf['ws_postponed_start'][0] == $value) ? $selected:'',
     )
  );
}

// Durations (Allowed Web Services Period)
// By default 10, 5, 2, 1 year(s) or 6, 3, 1 month(s) or 15, 10, 7, 5, 1, 0 day(s)
foreach ($conf['ws_durations'] as $value) {
  $template->assign_block_vars(
    'add_end',
     array(
       'VALUE'=> $value,
       'CONTENT' => $value,
       'SELECTED' => ($conf['ws_durations'][3] == $value) ? $selected:'',
     )
  );
  if ( $acc_list > 0 )
  {
    $template->assign_block_vars(
      'acc_list.upd_end',
       array(
         'VALUE'=> $value,
         'CONTENT' => $value,
         'SELECTED' => ($conf['ws_durations'][3] == $value) ? $selected:'',
       )
    );
  }
}

//----------------------------------------------------------- sending html code

$template->assign_var_from_handle('ADMIN_CONTENT', 'ws_checker');
?>
