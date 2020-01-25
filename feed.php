<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
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
function datetime_to_ts($datetime)
{
  return strtotime($datetime);
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

check_input_parameter('feed', $_GET, false, '/^[0-9a-z]{50}$/i');

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
    page_not_found(l10n('Unknown feed identifier'));
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

$rss->link = get_gallery_home_url();

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
    $item->title = l10n('New on %s', format_date($dbnow) );
    $item->link = get_gallery_home_url();

    // content creation
    $item->description = '<ul>';
    foreach ($news as $line)
    {
      $item->description.= '<li>'.$line.'</li>';
    }
    $item->description.= '</ul>';
    $item->descriptionHtmlSyndicated = true;

    $item->date = ts_to_iso8601(datetime_to_ts($dbnow));
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
    or time()-datetime_to_ts($feed_row['last_check']) > 30*24*3600 )
  {
    $query = '
UPDATE '.USER_FEED_TABLE.'
  SET last_check = '.pwg_db_get_recent_period_expression(-15, $dbnow).'
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

  $item->date = ts_to_iso8601(datetime_to_ts($date));
  $item->author = $conf['rss_feed_author'];
  $item->guid= sprintf('%s', 'pics-'.$date);;

  $rss->addItem($item);
}

$fileName= PHPWG_ROOT_PATH.$conf['data_location'].'tmp';
mkgetdir($fileName); // just in case
$fileName.='/feed.xml';
// send XML feed
echo $rss->saveFeed('RSS2.0', $fileName, true);
?>