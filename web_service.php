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

if ( !$conf['allow_web_services'] )
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

if (!isset($_GET['key'])) 
{
  die('Hacking attempt!');
}
if ( strlen($_GET['key']) < 12 )
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
if ( $active['value']=='false' )
{
  die('Web service is temporary inactive');
}

// Look for partner_key
//
$key = $_GET['key'];
$key = ( strlen($key) > 20 ) ? substr($key,0,20) : $key;
$len = strlen($key);
$hash = 0;
if (isset($_GET['pos']))
{
  $hash = (!is_numeric($_GET['pos'])) ? 0 : $_GET['pos'];
  $hash = (int) $hash;
  $hash = $hash % 12; 
}
$query = '
SELECT `id`, `name`, `access`, `start`, `end`, `request`, 
  `high`, `normal`, `limit`, `comment` 
  FROM '.WEB_SERVICES_ACCESS_TABLE.'
;';

$result = pwg_query($query);

while ($row = mysql_fetch_array($result))
{
  if ( substr( md5($row['name']),$hash,$len) == $key )
  {
    $len = 0;
    $def = $row;
    continue;
  }
}
if ( $len > 0 )
{
  die('Invalid key!');
}

// $def = Web service already defined partner access

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

// Check keywords
// Key and pos are correct
// &acc=cat/23,25-32&req=landscape&lim=5&tpl=myxml

// Requested id list and authorized id list
// Both may empty
// Both can be build on differents basis cat/tag/list
// Both have to be convert in id list format
$req_access ='';
if (isset($_GET['pos']))
{
  $req_access = check_target($_GET['acc']);
}
// on one hand $req_access, requested ids 
$req_type = explode('/',$req_access); 
$req_ids = explode( ',',$req_type[1] );
$req_list = expand_id_list( $req_ids ); 
if ($req_type[0]=='cat')
{
  $req_list = convert_catlist($req_list);
}
if ($req_type[0]=='tag')
{
  $req_list = get_image_ids_for_tags($req_list);
}
// echo $def['name'].'<br />';
// on the other hand $def['access'], authorized default ids
$def_type = explode('/',$def['access']); 
$def_ids = explode( ',',$def_type[1] );
$def_list = expand_id_list( $def_ids );
if ($def_type[0]=='cat')
{
  $def_list = convert_catlist($def_list);
}
if ($def_type[0]=='tag')
{
  $def_list = get_image_ids_for_tags($def_list);
}

// could be no necessary, a surplus but we are obliged to
// Filter on forbidden_categories (default can have change from creation time)
$list = implode(',',$def_list);

$ret_ids = array();
$query = '
SELECT DISTINCT image_id 
  FROM '.IMAGE_CATEGORY_TABLE.'
WHERE
'.get_sql_condition_FandF
  (
    array
      (
        'forbidden_categories' => 'category_id',
        'visible_categories' => 'category_id',
        'visible_images' => 'image_id'
      ),
    '', true
  ).'
  AND  image_id IN ('.$list.')
;';
$result = pwg_query($query);
while ($row = mysql_fetch_array($result))
{
  $ret_ids[] = $row['image_id'];
}
$def_ids = $ret_ids;

// Notice: Filtering on forbidden_categories (from requested id list)
// is completely superfluous (see few lines below).
$req_ids = $req_list;

// if no requested ids then is the complete default
if (count($req_ids)==0)
{
  $req_ids = $def_ids;
} 

// Removing requested ids not in authorized access list
// if requested ids they must be in the complete default and only those
// will be assumed. (Including forbidden... )
$final = array();
foreach ( $req_ids as $req_id )
{
  if ( in_array($req_id, $def_ids) ) 
  {
    $final[] = $req_id; 
  }
}

$final = array_unique ($final);  
sort ($final);
 
// 77f1180bd215a0edf66939
// web_service.php?key=77f1180bd215&pos=3&acc=list/41,73,142,178,190,204,235-238&req=recent&lim=1&tpl=myxml

$request = (isset($_GET['req']))? $_GET['req']:$def['request'];
// if type of request is different from the authorized type then force it 
if ( $def['request'] !== '' and $request !== $def['request'] )

{
  $request = $def['request'];
}
// if it is not an official request then force it
// (remark that default request can no longer exist 
// (later an Upgrade, or a remove) so...
$official = official_req();
if ( !in_array($request, $official ) )
{
  $request = $official[0]; // default request is the first one
}
// limit belong default (remember $def['limit'] is always set)
$limit = (isset($_GET['limit']))? $_GET['limit']:$def['limit'];
$limit = (is_numeric($limit))? $limit:$def['limit']; 
$limit = ( $limit < $def['limit'] ) ? $limit:$def['limit'];

// XML template
$tplfile = (isset($_GET['tpl']))? $_GET['tpl']:'default';
// FIXME additional controls are maybe needed on $tplfile


trigger_action('loc_begin_'.$request);
$template->set_filenames(array( $tplfile => 'XML/'. $tplfile .'.tpl'));

// Generate the request
include(PHPWG_ROOT_PATH. 'services/' .$request. '.php');


// +-----------------------------------------------------------------------+
// |                       XML/xhtml code display                          |
// +-----------------------------------------------------------------------+
header('Content-Type: text/xml; charset=UTF-8');
//header('Content-Type: text/html; charset='.$lang_info['charset']);
$template->parse($tplfile);

// echo '<strong>Trace temporaire<strong><br />';
// echo '$final:<br />' . var_dump($final);
// 
die('');
// FIXME// FIXME// FIXME// FIXME// FIXME// FIXME// FIXME// FIXME

//------------------------------------------------------------ log informations
pwg_log($request, $stats_id, $tplfile); // or something like that






// Check requested XML template
//

// Generate query
//

// Generate XML
//

// Log it
//



// Old code below

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
       '.get_sql_condition_FandF
         (
            array
             (
               'forbidden_categories' => 'c.id',
               'visible_categories' => 'c.id',
               'visible_images' => 'i.id'
              ),
           'AND'
         );

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
