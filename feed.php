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

define('PHPWG_ROOT_PATH','./');
include_once(PHPWG_ROOT_PATH.'include/common.inc.php');

// +-----------------------------------------------------------------------+
// |                               functions                               |
// +-----------------------------------------------------------------------+

/**
 * new comments between two dates, according to authorized categories
 *
 * @param string start (mysql datetime format)
 * @param string end (mysql datetime format)
 * @param string forbidden categories (comma separated)
 * @return array comment ids
 */
function new_comments($start, $end)
{
  global $user;
  
  $query = '
SELECT DISTINCT c.id AS comment_id
  FROM '.COMMENTS_TABLE.' AS c
     , '.IMAGE_CATEGORY_TABLE.' AS ic
  WHERE c.image_id = ic.image_id
    AND c.validation_date > \''.$start.'\'
    AND c.validation_date <= \''.$end.'\'
    AND category_id NOT IN ('.$user['forbidden_categories'].')
;';
  return array_from_query($query, 'comment_id');
}

/**
 * unvalidated at a precise date
 *
 * Comments that are registered and not validated yet on a precise date
 *
 * @param string date (mysql datetime format)
 * @return array comment ids
 */
function unvalidated_comments($date)
{
  $query = '
SELECT DISTINCT id
  FROM '.COMMENTS_TABLE.'
  WHERE date <= \''.$date.'\'
    AND (validated = \'false\'
         OR validation_date > \''.$date.'\')
;';
  return array_from_query($query, 'id');
}

/**
 * new elements between two dates, according to authorized categories
 *
 * @param string start (mysql datetime format)
 * @param string end (mysql datetime format)
 * @param string forbidden categories (comma separated)
 * @return array element ids
 */
function new_elements($start, $end)
{
  global $user;
  
  $query = '
SELECT DISTINCT image_id
  FROM '.IMAGES_TABLE.' INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON image_id = id
  WHERE date_available > \''.$start.'\'
    AND date_available <= \''.$end.'\'
    AND category_id NOT IN ('.$user['forbidden_categories'].')
;';
  return array_from_query($query, 'image_id');
}

/**
 * updated categories between two dates, according to authorized categories
 *
 * @param string start (mysql datetime format)
 * @param string end (mysql datetime format)
 * @param string forbidden categories (comma separated)
 * @return array element ids
 */
function updated_categories($start, $end)
{
  global $user;
  
  $query = '
SELECT DISTINCT category_id
  FROM '.IMAGES_TABLE.' INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON image_id = id
  WHERE date_available > \''.$start.'\'
    AND date_available <= \''.$end.'\'
    AND category_id NOT IN ('.$user['forbidden_categories'].')
;';
  return array_from_query($query, 'category_id');
}

/**
 * new registered users between two dates
 *
 * @param string start (mysql datetime format)
 * @param string end (mysql datetime format)
 * @return array user ids
 */
function new_users($start, $end)
{
  $query = '
SELECT user_id
  FROM '.USER_INFOS_TABLE.'
  WHERE registration_date > \''.$start.'\'
    AND registration_date <= \''.$end.'\'
;';
  return array_from_query($query, 'user_id');
}

/**
 * What's new between two dates ?
 *
 * Informations : number of new comments, number of new elements, number of
 * updated categories. Administrators are also informed about : number of
 * unvalidated comments, number of new users (TODO : number of unvalidated
 * elements)
 *
 * @param string start date (mysql datetime format)
 * @param string end date (mysql datetime format)
 */
function news($start, $end)
{
  global $user;

  $news = array();
  
  $nb_new_comments = count(new_comments($start, $end));
  if ($nb_new_comments > 0)
  {
    array_push($news, sprintf(l10n('%d new comments'), $nb_new_comments));
  }

  $nb_new_elements = count(new_elements($start, $end));
  if ($nb_new_elements > 0)
  {
    array_push($news, sprintf(l10n('%d new elements'), $nb_new_elements));
  }

  $nb_updated_categories = count(updated_categories($start, $end));
  if ($nb_updated_categories > 0)
  {
    array_push($news, sprintf(l10n('%d categories updated'),
                              $nb_updated_categories));
  }
  
  if ('admin' == $user['status'])
  {
    $nb_unvalidated_comments = count(unvalidated_comments($end));
    if ($nb_unvalidated_comments > 0)
    {
      array_push($news, sprintf(l10n('%d comments to validate'),
                                $nb_unvalidated_comments));
    }

    $nb_new_users = count(new_users($start, $end));
    if ($nb_new_users > 0)
    {
      array_push($news, sprintf(l10n('%d new users'), $nb_new_users));
    }
  }

  return $news;
}

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
 * creates a MySQL datetime format (2005-07-14 23:01:37) from a Unix
 * timestamp (number of seconds since 1970-01-01 00:00:00 GMT)
 *
 * @param int unix timestamp
 * @return string mysql datetime format
 */
function ts_to_mysqldt($ts)
{
  return date('Y-m-d H:i:s', $ts);
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

// clean $user array (include/user.inc.php has been executed)
$user = array();

// echo '<pre>'.generate_key(50).'</pre>';
if (isset($_GET['feed'])
    and preg_match('/^[A-Za-z0-9]{50}$/', $_GET['feed']))
{
  $query = '
SELECT user_id AS id,
       status,
       last_feed_check
  FROM '.USER_INFOS_TABLE.'
  WHERE feed_id = \''.$_GET['feed'].'\'
;';
  $user = mysql_fetch_array(pwg_query($query));
}
else
{
  $user = array('id' => $conf['guest_id'],
                'status' => 'guest');
}

$user['forbidden_categories'] = calculate_permissions($user['id'],
                                                      $user['status']);
if ('' == $user['forbidden_categories'])
{
  $user['forbidden_categories'] = '-1';
}

list($dbnow) = mysql_fetch_row(pwg_query('SELECT NOW();'));

include_once(PHPWG_ROOT_PATH.'include/feedcreator.class.php');

$rss = new UniversalFeedCreator();
// $rss->useCached(); // use cached version if age<1 hour
$rss->title = 'PhpWebGallery notifications';
$rss->link = 'http://phpwebgallery.net';

// +-----------------------------------------------------------------------+
// |                            Feed creation                              |
// +-----------------------------------------------------------------------+

if ($conf['guest_id'] != $user['id'])
{
  $news = news($user['last_feed_check'], $dbnow);

  if (count($news) > 0)
  {
    // echo '<pre>';
    // print_r($news);
    // echo '</pre>';
    
    $item = new FeedItem(); 
    $item->title = sprintf(l10n('New on %s'), $dbnow);
    $item->link = 'http://phpwebgallery.net';
    
    // content creation
    $item->description = '<ul>';
    foreach ($news as $line)
    {
      $item->description.= '<li>'.$line.'</li>';
    }
    $item->description.= '</ul>';
    $item->descriptionHtmlSyndicated = true;
    
    $item->date = $dbnow; 
    $item->author = 'PhpWebGallery notifier'; 
  
    $rss->addItem($item);
  }

  $query = '
UPDATE '.USER_INFOS_TABLE.'
  SET last_feed_check = \''.$dbnow.'\'
  WHERE user_id = '.$user['id'].'
;';
  pwg_query($query);
}
else
{
  // The feed is filled with periodical blocks of informations. Date
  // "checkpoints" cut the blocks. The first step is to find those
  // checkpoints according to the configured feed period.
  //
  // checkpoints are first calculated in Unix timestamp (number of seconds
  // since 1970-01-01 00:00:00 GMT) and then converted to MySQL datetime
  // format.

  $now = explode_mysqldt($dbnow);

  $checkpoints = array();
  $checkpoints[0] = mysqldt_to_ts($dbnow);

  // if the feed period was not configured the right way (ie among the list
  // of possible values), the configuration is overloaded here.
  if (!in_array($conf['feed_period'],
                array('hour', 'half day', 'day', 'week', 'month')))
  {
    $conf['feed_period'] = 'week';
  }

  // foreach feed_period possible, we need to find the beginning of the
  // current period. The variable $timeshift contains the shift to apply to
  // each checkpoint to find the previous one with strtotime function
  switch ($conf['feed_period'])
  {
    // 2005-07-14 23:36:19 => 2005-07-14 23:00:00
    case 'hour' :
    {
      $checkpoints[1] = mktime($now['hour'],0,0,
                               $now['month'],$now['day'],$now['year']);
      $timeshift = '1 hour ago';
      break;
    }
    // 2005-07-14 23:36:19 => 2005-07-14 12:00:00
    case 'half day' :
    {
      $checkpoints[1] = mktime(($now['hour'] < 12) ? 0 : 12,0,0,
                               $now['month'],$now['day'],$now['year']);
      $timeshift = '12 hours ago';
      break;
    }
    // 2005-07-14 23:36:19 => 2005-07-14 00:00:00
    case 'day' :
    {
      $checkpoints[1] = mktime(0,0,0,$now['month'],$now['day'],$now['year']);
      $timeshift = '1 day ago';
      break;
    }
    // 2005-07-14 23:36:19 => 2005-07-11 00:00:00
    case 'week' :
    {
      $checkpoints[1] = strtotime('last monday', $checkpoints[0]);
      $timeshift = '1 week ago';
      break;
    }
    // 2005-07-14 23:36:19 => 2005-07-01 00:00:00
    case 'month' :
    {
      $checkpoints[1] = mktime(0,0,0,$now['month'],1,$now['year']);
      $timeshift = '1 month ago';
      break;
    }
  }

  for ($i = 2; $i <= 11; $i++)
  {
    $checkpoints[$i] = strtotime($timeshift, $checkpoints[$i-1]);
  }

  // converts all timestamp values to MySQL datetime format
  $checkpoints = array_map('ts_to_mysqldt', $checkpoints);

  for ($i = 1; $i <= max(array_keys($checkpoints)); $i++)
  {
    $news = news($checkpoints[$i], $checkpoints[$i-1]);

    if (count($news) > 0)
    {
      $item = new FeedItem(); 
      $item->title = sprintf(l10n('New from %s to %s'),
                             $checkpoints[$i],
                             $checkpoints[$i-1]);
      $item->link = 'http://phpwebgallery.net';
      
      // content creation
      $item->description = '<ul>';
      foreach ($news as $line)
      {
        $item->description.= '<li>'.$line.'</li>';
      }
      $item->description.= '</ul>';
      $item->descriptionHtmlSyndicated = true;
      
      $item->date = ts_to_iso8601(mysqldt_to_ts($checkpoints[$i-1]));
      $item->author = 'PhpWebGallery notifier'; 
      
      $rss->addItem($item);
    }
  }
}

// send XML feed
echo $rss->saveFeed('RSS2.0', '', true);
?>