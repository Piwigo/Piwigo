<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
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
//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'stats');
?>
