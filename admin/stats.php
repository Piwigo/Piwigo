<?php
/***************************************************************************
 *                                 stats.php                               *
 *                            -------------------                          *
 *   application   : PhpWebGallery 1.3 <http://phpwebgallery.net>          *
 *   author        : Pierrick LE GALL <pierrick@z0rglub.com>               *
 *                                                                         *
 *   $Id$
 *                                                                         *
 ***************************************************************************/

/***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/
include_once( './include/isadmin.inc.php' );
$max_pixels = 500;
//------------------------------------------------------------ comment deletion
if ( isset( $_GET['del'] ) and is_numeric( $_GET['del'] ) )
{
  $query = 'DELETE FROM '.PREFIX_TABLE.'comments';
  $query.= ' WHERE id = '.$_GET['del'];
  $query.= ';';
  mysql_query( $query );
}
//--------------------------------------------------------- history table empty
if ( isset( $_GET['act'] ) and $_GET['act'] == 'empty' )
{
  $query = 'DELETE FROM '.PREFIX_TABLE.'history';
  $query.= ';';
  mysql_query( $query );
}
//----------------------------------------------------- template initialization
$sub = $vtp->Open( './template/'.$user['template'].'/admin/stats.vtp' );
$tpl = array( 'stats_last_days','date','login',
              'IP','file','picture','category','stats_pages_seen',
              'stats_visitors','stats_empty', 'stats_pages_seen_graph_title',
              'stats_visitors_graph_title');
templatize_array( $tpl, 'lang', $sub );
$vtp->setGlobalVar( $sub, 'user_template', $user['template'] );
//--------------------------------------------------- number of days to display
if ( isset( $_GET['last_days'] ) ) define( MAX_DAYS, $_GET['last_days'] );
else                               define( MAX_DAYS, 0 );

foreach ( $conf['last_days'] as $option ) {
  $vtp->addSession( $sub, 'last_day_option' );
  $vtp->setVar( $sub, 'last_day_option.option', $option );
  $url = './admin.php?page=stats&amp;expand='.$_GET['expand'];
  $url.= '&amp;last_days='.($option - 1);
  $vtp->setVar( $sub, 'last_day_option.link', add_session_id( $url ) );
  if ( $option == MAX_DAYS + 1 )
  {
    $vtp->setVar( $sub, 'last_day_option.style', 'font-weight:bold;');
  }
  $vtp->closeSession( $sub, 'last_day_option' );
}
//---------------------------------------------------------------- log  history
// empty link
$url = './admin.php?page=stats&amp;last_days='.$_GET['last_days'];
$url.= '&amp;expand='.$_GET['expand'];
$url.= '&amp;act=empty';
$vtp->setVar( $sub, 'emply_url', add_session_id( $url ) );
// expand array management
$expand_days = explode( ',', $_GET['expand'] );
$page['expand_days'] = array();
foreach ( $expand_days as $expand_day ) {
  if ( is_numeric( $expand_day ) )
  {
    array_push( $page['expand_days'], $expand_day );
  }
}

$days = array();
$max_nb_visitors = 0;
$max_pages_seen = 0;

$starttime = mktime(  0, 0, 0,date('n'),date('j'),date('Y') );
$endtime   = mktime( 23,59,59,date('n'),date('j'),date('Y') );
for ( $i = 0; $i <= MAX_DAYS; $i++ )
{
  $day = array();
  $vtp->addSession( $sub, 'day' );
  // link to open the day to see details
  $local_expand = $page['expand_days'];
  if ( in_array( $i, $page['expand_days'] ) )
  {
    $vtp->addSession( $sub, 'expanded' );
    $vtp->closeSession( $sub, 'expanded' );
    $vtp->setVar( $sub, 'day.open_or_close', $lang['close'] );
    $local_expand = array_remove( $local_expand, $i );
  }
  else
  {
    $vtp->addSession( $sub, 'collapsed' );
    $vtp->closeSession( $sub, 'collapsed' );
    $vtp->setVar( $sub, 'day.open_or_close', $lang['open'] );
    array_push( $local_expand, $i );
  }
  $url = './admin.php?page=stats&amp;last_days='.$_GET['last_days'];
  $url.= '&amp;expand='.implode( ',', $local_expand );
  $vtp->setVar( $sub, 'day.url', add_session_id( $url ) );
  // date displayed like this (in English ) :
  //                     Sunday 15 June 2003
  $date = $lang['day'][date( 'w', $starttime )];   // Sunday
  $date.= date( ' j ', $starttime );               // 15
  $date.= $lang['month'][date( 'n', $starttime )]; // June
  $date.= date( ' Y', $starttime );                // 2003
  $day['date'] = $date;
  $vtp->setVar( $sub, 'day.name', $date );
  // number of visitors for this day
  $query = 'SELECT DISTINCT(IP) as nb_visitors';
  $query.= ' FROM '.PREFIX_TABLE.'history';
  $query.= ' WHERE date > '.$starttime;
  $query.= ' AND date < '.$endtime;
  $query.= ';';
  $result = mysql_query( $query );
  $nb_visitors = mysql_num_rows( $result );
  $day['nb_visitors'] = $nb_visitors;
  if ( $nb_visitors > $max_nb_visitors ) $max_nb_visitors = $nb_visitors;
  $vtp->setVar( $sub, 'day.nb_visitors', $nb_visitors );
  // log lines for this day
  $query = 'SELECT date,login,IP,category,file,picture';
  $query.= ' FROM '.PREFIX_TABLE.'history';
  $query.= ' WHERE date > '.$starttime;
  $query.= ' AND date < '.$endtime;
  $query.= ' ORDER BY date DESC';
  $query.= ';';
  $result = mysql_query( $query );
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
  array_push( $days, $day );
}
//------------------------------------------------------------ pages seen graph
foreach ( $days as $day ) {
  $vtp->addSession( $sub, 'pages_day' );
  if ( $max_pages_seen > 0 )
    $width = floor( ( $day['nb_pages_seen']*$max_pixels ) / $max_pages_seen );
  else $width = 0;
  $vtp->setVar( $sub, 'pages_day.date', $day['date'] );
  $vtp->setVar( $sub, 'pages_day.width', $width );
  $vtp->setVar( $sub, 'pages_day.nb_pages', $day['nb_pages_seen'] );
  $vtp->closeSession( $sub, 'pages_day' );
}
//-------------------------------------------------------------- visitors grpah
foreach ( $days as $day ) {
  $vtp->addSession( $sub, 'visitors_day' );
  if ( $max_nb_visitors > 0 )
    $width = floor( ( $day['nb_visitors'] * $max_pixels ) / $max_nb_visitors );
  else $width = 0;
  $vtp->setVar( $sub, 'visitors_day.date', $day['date'] );
  $vtp->setVar( $sub, 'visitors_day.width', $width );
  $vtp->setVar( $sub, 'visitors_day.nb_visitors', $day['nb_visitors'] );
  $vtp->closeSession( $sub, 'visitors_day' );
}
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle , 'sub', $sub );
?>