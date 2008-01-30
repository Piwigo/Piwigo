<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
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
// These questions are one of module name explanations (checker).

if((!defined("PHPWG_ROOT_PATH")) or (!$conf['allow_web_services']))
{
  die('Hacking attempt!');
}
include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'include/ws_functions.inc.php');

/**
 * official_req returns the managed requests list in array format
 * FIXME A New list need to be build for ws_checker.php
 * returns array of authrorized request/methods
 * */
function official_req()
{
  $official = array(                  /* Requests are limited to             */
      'categories.'                          /* all categories. methods */
    , 'categories.getImages'
    , 'categories.getList'
    , 'images.'                              /* all images. methods */
    , 'images.getInfo'
    , 'images.addComment'
    , 'images.search'
    , 'tags.'                                /* all tags. methods */
    , 'tags.getImages'
    , 'tags.getList'
  );
  if (function_exists('local_req')) {
     $local = local_req();
     return array_merge( $official, $local );
  }
  return $official;
}

/**
 * check_target($string) verifies and corrects syntax of target parameter
 * example : check_target(cat/23,24,24,24,25,27) returns cat/23-25,27
 * */
function check_target($list)
{
  if ( $list !== '' )
  {
    $type = explode('/',$list); // Find type list
    if ( !in_array($type[0],array('list','cat','tag') ) )
    {
      $type[0] = 'list'; // Assume an id list
    }
    $ids = explode( ',',$type[1] );
    $list = $type[0] . '/';

    // 1,2,21,3,22,4,5,9-12,6,11,12,13,2,4,6,

    $result = expand_id_list( $ids );

    // 1,2,3,4,5,6,9,10,11,12,13,21,22,
    // I would like
    // 1-6,9-13,21-22
    $serial[] = $result[0]; // To be shifted
    foreach ($result as $k => $id)
    {
      $next_less_1 = (isset($result[$k + 1]))? $result[$k + 1] - 1:-1;
      if ( $id == $next_less_1 and end($serial)=='-' )
      { // nothing to do
      }
      elseif ( $id == $next_less_1 )
      {
        $serial[]=$id;
        $serial[]='-';
      }
      else
      {
        $serial[]=$id;  // end serie or non serie
      }
    }
    $null = array_shift($serial); // remove first value
    $list .= array_shift($serial); // add the real first one
    $separ = ',';
    foreach ($serial as $id)
    {
      $list .= ($id=='-') ? '' : $separ . $id;
      $separ = ($id=='-') ? '-':','; // add comma except if hyphen
    }
  }
  return $list;
}

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

// accepted queries
$req_type_list = official_req();

//--------------------------------------------------------- update informations
$chk_partner = '';
// Is a new access required?

if (isset($_POST['wsa_submit']))
{
// Check $_post (Some values are commented - maybe a future use)
$add_partner = htmlspecialchars( $_POST['add_partner'], ENT_QUOTES);
$add_target = check_target( $_POST['add_target']) ;
$add_end = ( is_numeric($_POST['add_end']) ) ? $_POST['add_end']:0;
$add_request = htmlspecialchars( $_POST['add_request'], ENT_QUOTES);
$add_limit = ( is_numeric($_POST['add_limit']) ) ? $_POST['add_limit']:1; 
$add_comment = htmlspecialchars( $_POST['add_comment'], ENT_QUOTES);
if ( strlen($add_partner) < 8 )
{ // TODO What? Complete with some MD5...
}
  $query = '
INSERT INTO '.WEB_SERVICES_ACCESS_TABLE.' 
( `name` , `access` , `start` , `end` , `request` , `limit` , `comment` )
VALUES (' . "
  '$add_partner', '$add_target',
  NOW(), 
  ADDDATE( NOW(), INTERVAL $add_end DAY),
  '$add_request', '$add_limit', '$add_comment' );";

  pwg_query($query);
  $chk_partner = $add_partner;
  
  $template->assign_block_vars(
    'update_result',
    array(
      'UPD_ELEMENT'=> l10n('ws_adding_legend').l10n('ws_success_upd'),
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
        'UPD_ELEMENT'=> l10n('ws_update_legend').l10n('ws_success_upd'),
        )
    );
  } else {
    $template->assign_block_vars(
      'update_result',
      array(
        'UPD_ELEMENT'=> l10n('ws_update_legend').l10n('ws_failed_upd'),
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
        'UPD_ELEMENT'=> l10n('ws_delete_legend').l10n('ws_success_upd'),
        )
    );
  } else {
    $template->assign_block_vars(
      'update_result',
      array(
        'UPD_ELEMENT'=> l10n('Not selected / Not confirmed')
        .l10n('ws_failed_upd'),
        )
    );
  } 
}



$template->assign_vars(
  array(
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
  $chk_partner = ( $chk_partner == '' ) ? $row['name'] : $chk_partner;
  $template->assign_block_vars(
    'acc_list.access',
     array(
       'CLASS' => ($num % 2 == 1) ? 'row1' : 'row2',
       'ID'               => $row['id'],
       'NAME'             => 
         (is_adviser()) ? '*********' : $row['name'],       
       'TARGET'           => $row['access'],
       'END'              => $row['end'],
       'REQUEST'          => $row['request'],
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
       'CONTENT' => $value,
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
if ( $chk_partner !== '' )
{
  $request = get_absolute_root_url().'ws.php?method=pwg.getVersion&format=rest&'
           . "partner=$chk_partner" ;
  $session = curl_init($request);
  curl_setopt ($session, CURLOPT_POST, true);
  curl_setopt($session, CURLOPT_HEADER, true);
  curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($session);
  curl_close($session);
  $status_code = array();
  preg_match('/\d\d\d/', $response, $status_code);
  switch( $status_code[0] ) {
  	case 200:
      $ws_status = l10n('Web Services under control');
  		break;
  	case 503:
  		$ws_status = 'PhpWebGallery Web Services failed and returned an '
                 . 'HTTP status of 503. Service is unavailable. An internal '
                 . 'problem prevented us from returning data to you.';
  		break;
  	case 403:
  		$ws_status = 'PhpWebGallery Web Services failed and returned an '
                 . 'HTTP status of 403. Access is forbidden. You do not have '
                 . 'permission to access this resource, or are over '
                 . 'your rate limit.';
  		break;
  	case 400:
  		// You may want to fall through here and read the specific XML error
  		$ws_status = 'PhpWebGallery Web Services failed and returned an '
                 . 'HTTP status of 400. Bad request. The parameters passed '
                 . 'to the service did not match as expected. The exact '
                 . 'error is returned in the XML response.';
  		break;
  	default:
  		$ws_status = 'PhpWebGallery Web Services returned an unexpected HTTP '
                 . 'status of:' . $status_code[0];
  }
  $template->assign_block_vars(
    'acc_list.ws_status',
     array(
       'VALUE'=> $ws_status,
     )
  );
}

//----------------------------------------------------------- sending html code

$template->assign_var_from_handle('ADMIN_CONTENT', 'ws_checker');

include_once(PHPWG_ROOT_PATH.'include/ws_core.inc.php');
?>
