<?php
// +-----------------------------------------------------------------------+
// |                               stats.php                               |
// +-----------------------------------------------------------------------+
// | application   : PhpWebGallery <http://phpwebgallery.net>              |
// | branch        : BSF (Best So Far)                                     |
// +-----------------------------------------------------------------------+
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
if( !defined("PHPWG_ROOT_PATH") )
{
	die ("Hacking attempt!");
}
include_once( PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php' );

$url_img_global_report = PHPWG_ROOT_PATH.'admin/images/global_stats.img.php';
//----------------------------------------------------- template initialization
$template->set_filenames( array('stats'=>'admin/stats.tpl') );

$template->assign_vars(array(
  'L_MONTH'=>$lang['w_month'],
  'L_PAGES_SEEN'=>$lang['stats_pages_seen'],
  'L_VISITORS'=>$lang['visitors'],
  'L_PICTURES'=>$lang['pictures'],
  'L_STAT_TITLE'=>$lang['stats_title'],
  'L_STAT_MONTH_TITLE'=>$lang['stats_month_title'],
  'L_STAT_MONTHLY_ALT'=>$lang['stats_global_graph_title'],
  
  'IMG_MONTHLY_REPORT'=>add_session_id($url_img_global_report)
  ));

//---------------------------------------------------------------- log  history
$query = '
SELECT DISTINCT COUNT(*) as p,
       MONTH(date) as m,
       YEAR(date) as y
  FROM '.HISTORY_TABLE.' 
  GROUP BY DATE_FORMAT(date,\'%Y-%m\') DESC
;';
$result = pwg_query( $query );
$i=0;
while ( $row = mysql_fetch_array( $result ) )
{
  $current_month = $row['y']."-";
  if ($row['m'] <10) {$current_month.='0';}
  $current_month .= $row['m'];
  // Number of pictures seen
  $query = '
SELECT COUNT(*) as p,
       FILE as f
  FROM '.HISTORY_TABLE.' 
  WHERE DATE_FORMAT(date,\'%Y-%m\') = \''.$current_month.'\'
    AND FILE = \'picture\'
  GROUP BY FILE
;';
  $pictures = mysql_fetch_array(pwg_query( $query ));
  
  // Number of different visitors
  $query = '
SELECT COUNT(*) as p, login
  FROM '.HISTORY_TABLE.' 
  WHERE DATE_FORMAT(date,\'%Y-%m\') = \''.$current_month.'\'
  GROUP BY login, IP
;';
  $user_results = pwg_query( $query );
  $nb_visitors = 0;
  $auth_users = array();
  while ( $user_array = mysql_fetch_array( $user_results ) )
  {
    if ($user_array['login'] == 'guest') 
	  $nb_visitors += 1;
	else
	  array_push($auth_users, $user_array['login']);
  }
  $nb_visitors +=count(array_unique($auth_users));
  $class = ($i % 2)? 'row1':'row2'; $i++;
  
  $template->assign_block_vars('month',array(
    'MONTH'=>$lang['month'][$row['m']].' '.$row['y'],
	'PAGES'=>$row['p'],
	'VISITORS'=>$nb_visitors,
	'IMAGES'=>$pictures['p'],
	
	'T_CLASS'=>$class
    ));
}
$nb_visitors = mysql_num_rows( $result );
$days = array();
$max_nb_visitors = 0;
$max_pages_seen = 0;

$starttime = mktime(  0, 0, 0,date('n'),date('j'),date('Y') );
$endtime   = mktime( 23,59,59,date('n'),date('j'),date('Y') );
//for ( $i = 0; $i <= MAX_DAYS; $i++ )
{
  /*
  // log lines for this day
  $query = 'SELECT date,login,IP,category,file,picture';
  $query.= ' FROM '.PREFIX_TABLE.'history';
  $query.= ' WHERE date > '.$starttime;
  $query.= ' AND date < '.$endtime;
  $query.= ' ORDER BY date DESC';
  $query.= ';';
  $result = pwg_query( $query );
  $nb_pages_seen = mysql_num_rows( $result );
  $day['nb_pages_seen'] = $nb_pages_seen;
  if ( $nb_pages_seen > $max_pages_seen ) $max_pages_seen = $nb_pages_seen;
  $vtp->setVar( $sub, 'day.nb_pages', $nb_pages_seen );
  if ( in_array( $i, $page['expand_days'] ) )
  {
    while ( $row = mysql_fetch_array( $result ) )
    {
      $vtp->addSession( $sub, 'line' );
      $vtp->setVar( $sub, 'line.date', date( 'G:i:s', $row['date'] ) );
      $vtp->setVar( $sub, 'line.login', $row['login'] );
      $vtp->setVar( $sub, 'line.IP', $row['IP'] );
      $vtp->setVar( $sub, 'line.category', $row['category'] );
      $vtp->setVar( $sub, 'line.file', $row['file'] );
      $vtp->setVar( $sub, 'line.picture', $row['picture'] );
      $vtp->closeSession( $sub, 'line' );
    }
  }
  $starttime-= 24*60*60;
  $endtime  -= 24*60*60;
  $vtp->closeSession( $sub, 'day' );
  array_push( $days, $day );*/
}
//------------------------------------------------------------ pages seen graph
foreach ( $days as $day ) {
  /*$vtp->addSession( $sub, 'pages_day' );
  if ( $max_pages_seen > 0 )
    $width = floor( ( $day['nb_pages_seen']*$max_pixels ) / $max_pages_seen );
  else $width = 0;
  $vtp->setVar( $sub, 'pages_day.date', $day['date'] );
  $vtp->setVar( $sub, 'pages_day.width', $width );
  $vtp->setVar( $sub, 'pages_day.nb_pages', $day['nb_pages_seen'] );
  $vtp->closeSession( $sub, 'pages_day' );*/
}
//-------------------------------------------------------------- visitors grpah
foreach ( $days as $day ) {
  /*$vtp->addSession( $sub, 'visitors_day' );
  if ( $max_nb_visitors > 0 )
    $width = floor( ( $day['nb_visitors'] * $max_pixels ) / $max_nb_visitors );
  else $width = 0;
  $vtp->setVar( $sub, 'visitors_day.date', $day['date'] );
  $vtp->setVar( $sub, 'visitors_day.width', $width );
  $vtp->setVar( $sub, 'visitors_day.nb_visitors', $day['nb_visitors'] );
  $vtp->closeSession( $sub, 'visitors_day' );*/
}
//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'stats');
?>
