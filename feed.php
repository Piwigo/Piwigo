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

define('PHPWG_ROOT_PATH','./');
include_once(PHPWG_ROOT_PATH.'include/common.inc.php');
include_once(PHPWG_ROOT_PATH.'include/functions_notification.inc.php');

// +-----------------------------------------------------------------------+
// |                               functions                               |
// +-----------------------------------------------------------------------+

/**
 * explodes a MySQL datetime format (2005-07-14 23:01:37) in fields "year",
 * "month", "day", "hour", "minute", "second".
 *
 * @param string mysql datetime format
 * @return array
 */
function explode_mysqldt($mysqldt)
{
  $date = array();
  list($date['year'],
       $date['month'],
       $date['day'],
       $date['hour'],
       $date['minute'],
       $date['second'])
    = preg_split('/[-: ]/', $mysqldt);

  return $date;
}

/**
 * creates a Unix timestamp (number of seconds since 1970-01-01 00:00:00
 * GMT) from a MySQL datetime format (2005-07-14 23:01:37)
 *
 * @param string mysql datetime format
 * @return int timestamp
 */
function mysqldt_to_ts($mysqldt)
{
  $date = explode_mysqldt($mysqldt);
  return mktime($date['hour'], $date['minute'], $date['second'],
                $date['month'], $date['day'], $date['year']);
}

/**
 * creates an ISO 8601 format date (2003-01-20T18:05:41+04:00) from Unix
 * timestamp (number of seconds since 1970-01-01 00:00:00 GMT)
 *
 * function copied from Dotclear project http://dotclear.net
 *
 * @param int timestamp
 * @return string ISO 8601 date format
 */
function ts_to_iso8601($ts)
{
  $tz = date('O',$ts);
  $tz = substr($tz, 0, -2).':'.substr($tz, -2);
  return date('Y-m-d\\TH:i:s',$ts).$tz;
}

// +-----------------------------------------------------------------------+
// |                            initialization                             |
// +-----------------------------------------------------------------------+

$feed_id= isset($_GET['feed']) ? $_GET['feed'] : '';
$image_only=isset($_GET['image_only']);

// echo '<pre>'.generate_key(50).'</pre>';
if ( !empty($feed_id) )
{
  $query = '
SELECT user_id,
       last_check
  FROM '.USER_FEED_TABLE.'
  WHERE id = \''.$feed_id.'\'
;';
  $feed_row = mysql_fetch_assoc(pwg_query($query));
  if ( empty($feed_row) )
  {
    page_not_found('Unknown/missing feed identifier');
  }
  if ($feed_row['user_id']!=$user['id'])
  { // new user
    $user = array();
    $user = build_user( $feed_row['user_id'], true );
  }
}
else
{
  $image_only = true;
  if (!$user['is_the_guest'])
  {// auto session was created - so switch to guest
    $user = array();
    $user = build_user( $conf['guest_id'], true );
  }
}

list($dbnow) = mysql_fetch_row(pwg_query('SELECT NOW();'));

include_once(PHPWG_ROOT_PATH.'include/feedcreator.class.php');

set_make_full_url();

$rss = new UniversalFeedCreator();

$rss->title = $conf['gallery_title'];
$rss->title.= ' (as '.$user['username'].')';

$rss->link = $conf['gallery_url'];

// +-----------------------------------------------------------------------+
// |                            Feed creation                              |
// +-----------------------------------------------------------------------+

if (!$image_only)
{
  $news = news($feed_row['last_check'], $dbnow, true, true);

  if (count($news) > 0)
  {
    $item = new FeedItem();
    $item->title = sprintf(l10n('New on %s'),
        format_date($dbnow, 'mysql_datetime') );
    $item->link = $conf['gallery_url'];

    // content creation
    $item->description = '<ul>';
    foreach ($news as $line)
    {
      $item->description.= '<li>'.$line.'</li>';
    }
    $item->description.= '</ul>';
    $item->descriptionHtmlSyndicated = true;

    $item->date = ts_to_iso8601(mysqldt_to_ts($dbnow));
    $item->author = 'PhpWebGallery notifier';
    $item->guid= sprintf('%s', $dbnow);;

    $rss->addItem($item);

    $query = '
UPDATE '.USER_FEED_TABLE.'
  SET last_check = \''.$dbnow.'\'
  WHERE id = \''.$feed_id.'\'
;';
    pwg_query($query);
  }
}
else
{
  if ( !empty($feed_id) )
  {// update the last check to avoid deletion by maintenance task
    $query = '
  UPDATE '.USER_FEED_TABLE.'
    SET last_check = \''.$dbnow.'\'
    WHERE id = \''.$feed_id.'\'
  ;';
    pwg_query($query);
  }
}

$dates = get_recent_post_dates( 5, 6, 6);

foreach($dates as  $date_detail)
{ // for each recent post date we create a feed item
  $date = $date_detail['date_available'];
  $exploded_date = explode_mysqldt($date);
  $item = new FeedItem();
  $item->title = l10n_dec('%d new element', '%d new elements', $date_detail['nb_elements']);
  $item->title .= ' ('.$lang['month'][(int)$exploded_date['month']].' '.$exploded_date['day'].')';
  $item->link = make_index_url(
        array(
          'chronology_field' => 'posted',
          'chronology_style'=> 'monthly',
          'chronology_view' => 'calendar',
          'chronology_date' => explode('-', substr($date,0,10) )
        )
      );

  $item->description .=
    '<a href="'.make_index_url().'">'.$conf['gallery_title'].'</a><br/> ';

  $item->description .=
        '<li>'
        .l10n_dec('%d new element', '%d new elements', $date_detail['nb_elements'])
        .' ('
        .'<a href="'.make_index_url(array('section'=>'recent_pics')).'">'
          .l10n('recent_pics_cat').'</a>'
        .')'
        .'</li>';

  foreach( $date_detail['elements'] as $element )
  {
    $tn_src = get_thumbnail_url($element);
    $item->description .= '<img src="'.$tn_src.'"/>';
  }
  $item->description .= '...<br/>';

  $item->description .=
        '<li>'
        .l10n_dec('%d category updated', '%d categories updated',
                  $date_detail['nb_cats'])
        .'</li>';

  $item->description .= '<ul>';
  foreach( $date_detail['categories'] as $cat )
  {
    $item->description .=
          '<li>'
          .get_cat_display_name_cache($cat['uppercats'])
          .' ('.
          l10n_dec('%d new element',
                   '%d new elements', $cat['img_count']).')'
          .'</li>';
  }
  $item->description .= '</ul>';

  $item->descriptionHtmlSyndicated = true;

  $item->date = ts_to_iso8601(mysqldt_to_ts($date));
  $item->author = 'PhpWebGallery notifier';
  $item->guid= sprintf('%s', 'pics-'.$date);;

  $rss->addItem($item);
}

// send XML feed
echo $rss->saveFeed('RSS2.0', '', true);
?>