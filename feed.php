<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
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
include_once(PHPWG_ROOT_PATH.'include/common.inc.php');
include_once(PHPWG_ROOT_PATH.'include/functions_notification.inc.php');

// +-----------------------------------------------------------------------+
// |                               functions                               |
// +-----------------------------------------------------------------------+

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
  $feed_row = pwg_db_fetch_assoc(pwg_query($query));
  if ( empty($feed_row) )
  {
    page_not_found('Unknown/missing feed identifier');
  }
  if ($feed_row['user_id']!=$user['id'])
  { // new user
    $user = build_user( $feed_row['user_id'], true );
  }
}
else
{
  $image_only = true;
  if (!is_a_guest())
  {// auto session was created - so switch to guest
    $user = build_user( $conf['guest_id'], true );
  }
}

// Check the status now after the user has been loaded
check_status(ACCESS_GUEST);

list($dbnow) = pwg_db_fetch_row(pwg_query('SELECT NOW();'));

include_once(PHPWG_ROOT_PATH.'include/feedcreator.class.php');

set_make_full_url();

$rss = new UniversalFeedCreator();
$rss->encoding=get_pwg_charset();
$rss->title = $conf['gallery_title'];
$rss->title.= ' (as '.stripslashes($user['username']).')';

$rss->link = $conf['gallery_url'];

// +-----------------------------------------------------------------------+
// |                            Feed creation                              |
// +-----------------------------------------------------------------------+

$news = array();
if (!$image_only)
{
  $news = news($feed_row['last_check'], $dbnow, true, true);

  if (count($news) > 0)
  {
    $item = new FeedItem();
    $item->title = sprintf(l10n('New on %s'), format_date($dbnow) );
    $item->link = $conf['gallery_url'];

    // content creation
    $item->description = '<ul>';
    foreach ($news as $line)
    {
      $item->description.= '<li>'.$line.'</li>';
    }
    $item->description.= '</ul>';
    $item->descriptionHtmlSyndicated = true;

    $item->date = mysqldt_to_ts($dbnow);
    $item->author = $conf['rss_feed_author'];
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

if ( !empty($feed_id) and empty($news) )
{// update the last check from time to time to avoid deletion by maintenance tasks
  if ( !isset($feed_row['last_check'])
    or time()-mysqldt_to_ts($feed_row['last_check']) > 30*24*3600 )
  {
    $query = '
UPDATE '.USER_FEED_TABLE.'
  SET last_check = DATE_ADD(\''.$dbnow.'\', INTERVAL -15 DAY )
  WHERE id = \''.$feed_id.'\'
;';
    pwg_query($query);
  }
}

$dates = get_recent_post_dates_array($conf['recent_post_dates']['RSS']);

foreach($dates as $date_detail)
{ // for each recent post date we create a feed item
  $item = new FeedItem();
  $date = $date_detail['date_available'];
  $item->title = get_title_recent_post_date($date_detail);
  $item->link = make_index_url(
        array(
          'chronology_field' => 'posted',
          'chronology_style'=> 'monthly',
          'chronology_view' => 'calendar',
          'chronology_date' => explode('-', substr($date,0,10) )
        )
      );

  $item->description .=
    '<a href="'.make_index_url().'">'.$conf['gallery_title'].'</a><br> ';

  $item->description .= get_html_description_recent_post_date($date_detail);

  $item->descriptionHtmlSyndicated = true;

  $item->date = mysqldt_to_ts($date);
  $item->author = $conf['rss_feed_author'];
  $item->guid= sprintf('%s', 'pics-'.$date);;

  $rss->addItem($item);
}

$fileName= $conf['local_data_dir'].'/tmp';
mkgetdir($fileName); // just in case
$fileName.='/feed.xml';
// send XML feed
echo $rss->saveFeed('RSS2.0', $fileName, true);
?>