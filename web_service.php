<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2006-12-15 23:16:37 +0200 (ven., 15 dec. 2006) $
// | last modifier : $Author: vdigital $
// | revision      : $Revision: 1658 $
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

if((!defined("PHPWG_ROOT_PATH")) or (!$conf['allow_web_services']))
{
  die('Hacking attempt!');
}

// Full call syntax sample:
//-----------------------------------------------------------------------------
// web_service.php?key=123456789012
// &pos=5&acc=cat/23,25-32&req=landscape&lim=5&tpl=myxml

// &pos=is position defined by caller to substring, see key below, (default 0)
// key=substr(md5(partner_id),&pos,12)
// &acc=cat/23,25-35 or list/1-125,136,141-162 or tag/27,45,54-55 
//      (the specified access list will be respected 
//       ONLY if access is not specified in web_services access table)
// req=any request (except if limited to a specific one in ws access table)
// lim=number (returned picture count and limited it self by ws access table)

// the tpl file must exist in ./template/"default template"/xml/
// tpl=myxml (xml/myxml.tpl will be used, by default: xml/default.tpl)

// All are facultative EXCEPT key
// 

// Check call process (Keyed call)
//
if (!isset($_get['key'])) 
{
  die('Hacking attempt!');
}
if ( strlen($_get['key']) < 12 )
{
  die('Invalid key (Length issue)!');
}

// Is service active (Temporary it could be inactive / Online parameter)
//
$query = '
SELECT value FROM '.CONFIG_TABLE.'
WHERE param = \'ws_status\'
;';
$active = mysql_fetch_array(pwg_query($query));
if ($active='false')
{
  die('Web service is temporary inactive');
}

// Look for partner_key
//
$key = $_get['key'];
$key = ( strlen($key) > 20 ) ? substr($key,0,20) : $key;
$len = strlen($key);
&hash = 0;
if (isset($_get['pos']))
{
  $hash = (!is_numeric($hash)) ? 0 : $_get['pos'];
  $hash = (int) $hash;
  $hash = $hash % 12; 
}
$query = '
SELECT *
  FROM '.WEB_SERVICES_ACCESS_TABLE.'
;';
$result = pwg_query($query);

while ($row = mysql_fetch_array($result))
{
  if ( substr( md5($row['name']),$hash,$len) == $key )
  {
    $len = 0;
    continue;
  }
}
if ( $len > 0 )
{
  die('Invalid key!');
}
// $def = Web service already defined partner access
$def = $row;
//
// Now, the partner will get a reply in time
//
$stat_id = 'Web Service';
if (isset($_SERVER["HTTP_REFERER"]) and
   !eregi($_SERVER["HTTP_HOST"],$_SERVER["HTTP_REFERER"]))
{
  $stats_id = substr($_SERVER["HTTP_REFERER"],7);
  $pos = strpos($stats_id,'/');
  $stats_id = ( $pos>0 ) ? substr($stats_id,0,$pos) : $stats_id;
}



// FIXME


// Check keywords
//

// Check requested XML template
//

// Generate query
//

// Generate XML
//

// Log it
//





//------------ Main security strategy ---------------------
$partner_id = 'default';
// Security considerations: HTTP_REFERER and FOPEN 
// 1 - FOPEN doesn't update current HTTP_REFERER
// 2 - HTTP_REFERER may be hidden/altered for lot of reasons.
// 3 - By this process, you can log HTTP_REFERER of your partner (not yours).
// 4 - Logging HTTP_REFERER needs declarative procedures in some countries.
// 5 - Following those links can be considered as risky.
// 6 - You can turn off, referer logging by $conf['ws-refback'] = false;
// 7 - In the other hand, your partner may give his key to another web site.
// Above all, this information is just an indication.
// $conf['ws-refback'] : Default value is false.

if (isset($_SERVER["HTTP_REFERER"]) and
   !eregi($_SERVER["HTTP_HOST"],$_SERVER["HTTP_REFERER"]))
{
  $partner_id = substr($_SERVER["HTTP_REFERER"],7);
  $pos = strpos($partner_id,'/');
  $partner_id = ( $pos>0 ) ? substr($partner_id,0,$pos) : $partner_id;
} 
// $partner_id = Is used to check prohibited REFER site (but not only)
// example: www.prohibited-access.be

if ( isset($conf['ws-refback']) and $conf['ws-refback'])
{
  $log_id = $partner_id;
}
else
{
  $log_id = ''; // Would be set in time by process end
}
// $log_id = History log information
// examples: forum.phpwebgallery.net
//           phpwebgallery.net
//           demo.phpwebgallery.net

//
$partner_id = strtolower($partner_id);
// Prohibited REFER: $partner_id is compared (strtolower).
//
//----------------------------- Is a prohibited refer?
if ( $partner_id !== 'default' )
{
  // Is Referer a prohibited site?
  //  Compare requestor site to web service key table
  //  Found and limit = 0 => die
  foreach ( $conf['ws_keys'] as $key => $vkey )
  {
    if ( strtolower($vkey['id']) == $partner_id and $vkey['limit'] == 0 ) 
    {
      pwg_log( 'WS Prohibited', 'Req.:'.$type, 'From: ws_keys['.$key.']' );
      die($lang['access_forbiden']); 
    }
  }
}
//----------------------------- Which access he will use?
$access = check_ws_access( $conf['ws_keys'] ); 
// given key arg is compared asis (Take care of upper/lower case).

parse_str($access['force'], $force);
// $force contains all forced arguments
// get requested arguments and apply limits
$force['limit'] = ( isset($access['limit']) ) ? $access['limit'] :
        $conf['ws_limit'] ;
$arg = force_arg_ws_limit( $force, $conf['ws_limit'] ); 
// $arg contains all retain query arguments

// Warning about $arg !!! Warning !!! Warning !!! Warning !!! Warning !!!
// specially to MOD developpers :
// FOR SECURITY REASON NEVER USE extract() AGAINST $arg
// ( $arg is like $_GET )

if ( is_numeric(isset($arg['cat'])) )
{
  $arg['cat']=floor($arg['cat']);
}
else
{
  unset($arg['cat']);
}
// AND category_id is concatenated if requested or forced
$cat_criterion = '';
if ( isset($arg['cat']) and ($arg['cat']) > 0 )
{
  $cat_criterion = ' AND ic.`category_id` ='.$arg['cat'].' ';
}
//-------------------------------------------- SQL Query statement building
// Has to be tested against a LARGE configuration 
// for performance consideration
// and maybe rewrite in some cases.

// All below has to be check to respect code writing rule convention



$query='
  SELECT DISTINCT (i.`id`),
         i.`path` , i.`file` , i.`date_available` ,
         i.`date_creation`, i.`tn_ext` , i.`name` ,
         i.`filesize` , i.`storage_category_id` , i.`average_rate`,
         i.`comment` , i.`author` , i.`hit` ,i.`width` ,
         i.`height`
     FROM `'.IMAGES_TABLE.'` AS i
     INNER JOIN `'.IMAGE_CATEGORY_TABLE.'` 
           AS ic ON i.`id` = ic.`image_id`
     INNER JOIN `'.CATEGORIES_TABLE.'` 
           AS c ON c.`id` = ic.`category_id`
     WHERE c.`status` = \'public\'
       AND i.`width` > 0 
       AND i.`height` > 0
       AND i.`representative_ext` IS NULL 
       '.$cat_criterion.'
       AND c.`id` NOT IN ('.$user['forbidden_categories'].') ';

//     AND c.`agreed_ws` = \'true\' (Obsolete specification replaced by force)

$list = ( isset($arg['list']) ) ? $arg['list'] : '';
$type = $arg['type'];
switch($type) 
{
  case ($type === 'random' or $type === 'listcat'):     /* Random order */
    $query .= ' ORDER BY RAND() DESC ';
    break;
  case ($type === 'list'):             /* list on MBt & z0rglub request */
    $query .= ' AND i.`id` IN ('.$list.') ';
    break;
  case $type === 'maxviewed':             /* hit > 0 and hit desc order */
    $query .= ' AND  i.`hit` > 0
                ORDER BY i.`hit` DESC, RAND() DESC ';
    break;
  case $type === 'recent':        /* recent = Date_available desc order */
    $query .= ' ORDER BY i.`date_available` DESC, RAND() DESC ';
    break;
  case $type === 'highrated':            /* avg_rate > 0 and desc order */
// French Joke : Cette requete s'appelle officieusement l' "ail_gratte"
    $query .= ' AND  i.`average_rate` > 0
                ORDER BY i.`average_rate` DESC, RAND() DESC ';
    break;
  case $type === 'oldest':                  /* Date_available asc order */
    $query .= ' ORDER BY i.`date_available` ASC, RAND() DESC ';
    break;
  case $type === 'lessviewed':                         /* hit asc order */
// French Joke : Cette requete s'appelle officieusement la "lessive"
    $query .= ' ORDER BY i.`hit` ASC, RAND() DESC ';
    break;
  case $type === 'lowrated':                      /* avg_rate asc order */
    $query .= ' AND  i.`average_rate` IS NOT NULL
                ORDER BY i.`average_rate` ASC, RAND() DESC ';
    break;
  case $type === 'undescribed':                  /* description missing */
// US/UK Joke : This request is unofficially named 'indiscribable' horror
    $query .= ' AND  i.`comment` IS NULL
                ORDER BY RAND() DESC ';
    break;
  case $type === 'unnamed':                         /* new name missing */
    $query .= ' AND  i.`comment` IS NULL
                ORDER BY RAND() DESC ';
    break;
  case $type === 'portraits':     /* width < height (portrait oriented) */
    $query .= ' AND  `width` < (`height` * 0.95)
                ORDER BY RAND() DESC ';
    break;
  case $type === 'landscapes':   /* width > height (landscape oriented) */
    $query .= ' AND `width` > (`height` * 1.05)
                ORDER BY RAND() DESC ';
    break;
  case $type === 'squares':             /* width ~ height (square form) */
    $query .= ' AND  `width` BETWEEN (`height` * 0.95) 
                                 AND (`height` * 1.05)
                ORDER BY RAND() DESC ';
    break;
  default:                                     /* Just say: Goodbye !!! */
    die($lang['access_forbiden']); 
} /* End switch */
$query .= ' LIMIT 0 , '.$arg['limit'].';';
$result = pwg_query( $query );
$attributes = array( 'width', 'height', 'author', 'date_creation',
                    'date_available', 'hit', 'filesize');
$xml = '<items> ';
$hr_nbr = 0; $ns_nbr = 0; $tn_nbr = 0;
if ( $log_id == '')
{
  foreach ( $conf['ws_keys'] as $key => $vkey )
  {
    if ( $vkey['id'] == $access['id'] ) 
    {
      $log_id = 'R:#'.$key; 
      break;
    }
  }
}
while ( $row = mysql_fetch_array( $result ) )
{
  $tn_nbr++;
  $item = '<item ';
  $path = strtolower(strtok($_SERVER['SERVER_PROTOCOL'],
    '/')).'://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,-16).
    substr($row['path'],1);
  if ( isset($access['pwg_n']) and $access['pwg_n'] )
  {
    $ns_nbr++;
    $item .= ' src="'.$path.'"'; 
  }
  else
  {
  unset($attributes['width']);
  unset($attributes['height']);
  unset($attributes['filesize']);
  }
  foreach ( $attributes as $attribute )
  {
    if ( isset($row{$attribute}) )
    {
      $item.= ' '.$attribute.'="'.$row{$attribute}.'"';
    }
  }
  if ( isset($row['comment']) ) 
  {
    $item .= ' description="'.$row['comment'].'"';
  }
  $tnsrc = get_thumbnail_src( $path, $row['tn_ext'] ); 
  $item .= ' tnsrc="'.$tnsrc.'"';
  $tnsize = @getimagesize($tnsrc);
  $item .= ' tnwidth="'.$tnsize[0].'"';
  $item .= ' tnheight="'.$tnsize[1].'"';
  if ( isset($access['pwg_h']) and $access['pwg_h'] )
  {  
    $high = dirname( $path ).'/pwg_high/'.$row['file'];
    $hrsize = @getimagesize($high);
    if ( $hrsize[0] > 0 ) 
    {
      $hr_nbr++;
      $item .= ' hrsrc="'.$high.'"';
      $item .= ' hrwidth="'.$hrsize[0].'"';
      $item .= ' hrheight="'.$hrsize[1].'"';
    }
  }
  $xml .= $item.' />';
  //-------------------------------------- picture ----- log informations
//      request_type ( R:#id_requester ),  real_category_id [ request_number ],   image_file_name );
  pwg_log( $type.'('.$log_id.')',       $row['storage_category_id'].'['.$tn_nbr.']',      $row['file'] );
}
$xml .= ' </items>';
echo $xml; // Send XML
//---------------------------------------- service ----- log informations
$size = 'tn('.$tn_nbr.')'; // thumbnails
if ( $ns_nbr > 0 )
{
  $size = '('.$ns_nbr.')'; // pictures
}
if  ( $hr_nbr > 0 )
{
  $size = 'HR('.$hr_nbr.'/'.$tn_nbr.')'; // high res.
}

//pwg_log( 'Web service',          'Req.:'.$type,                 'From:'.$log_id );






  /*-- Web Service function
  Which access is correct for this resquest?
  Compare requestor key to  web service key table
  If 'defined' => use that one
  If not => use default access
  If no 'defined' default => exit
  'defined' : Obviously check period and not only defined access
  
  Return corresponding access (= an entry from web service key table) 
  --*/
function check_ws_access( $ws_keys )
{
  $partnr = ( isset($_GET['key']) ) ? $_GET['key'] : 'default'; 
   
  foreach ( $ws_keys as $key => $access )
  {
    if ( $access['id'] == $partnr ) 
    {
      break; 
    }
  }
  if ( $access['id'] !== $partnr )            // Not found? =default.
  {
    $access = $ws_keys[0];
    if ( $access['id'] !== 'default' )    // Check if it's really default
    {                                     // definition
      die($lang['access_forbiden']);      // No default access
    }
  }
  // Checking Dates...
  // Take care of that: my partner can be out of dates
  //   but via default... Answer is NO.
  // With out of date period, a partner can be seen as prohibited partner
  //   if you want to authorise him/her change $conf['ws_keys']
    
  // Tests are done with server local time...
  if (isset($access['end']) and date('Y-m-d H:i:s')>$access['end']) 
  {                                            //-- Access ended?
     die($lang['access_forbiden']);            //-- BTW prohibited     
  }
  if (isset($access['start']) and date('Y-m-d H:i:s')<$access['start'])
  {                                            //-- Access started?
    die($lang['access_forbiden']);
  }
  return $access;
}

  /*-- Web Sevice function
  Force global arguments to ensure access restriction
  ( access defined in web service key table ) 
  Considering the default limit as well and prohibited site case
  
  Return overided request ( overided $_GET )
  --*/
function force_arg_ws_limit( $use, $default )
{
  if ( $use['limit'] < 1 )
  {                                            //-- Access deny
    die($lang['access_forbiden']);
  }
  $arg = $_GET;             // what is required?
  if (!isset($arg['limit']))
  {
    $arg['limit']=$use['limit'];
  }
  if (!is_numeric($arg['limit']))
  {
    $arg['limit']=$use['limit'];
  }
  // ----------- use force arg if they are some
  foreach ( $use as $kuse => $vuse )
  {
    if ( $kuse !== 'limit' )
    {
      $arg[$kuse] = $vuse;
    }
  }
  $arg['limit'] = floor(min($arg['limit'], $use['limit']));
  return $arg;
}
?>
