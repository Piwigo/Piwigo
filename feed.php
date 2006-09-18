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

// clean $user array (include/user.inc.php has been executed)
$user = array();

// echo '<pre>'.generate_key(50).'</pre>';
if (isset($_GET['feed'])
    and preg_match('/^[A-Za-z0-9]{50}$/', $_GET['feed']))
{
  $query = '
SELECT uf.user_id AS id,
       ui.status,
       uf.last_check,
       u.'.$conf['user_fields']['username'].' AS username
  FROM '.USER_FEED_TABLE.' AS uf
    INNER JOIN '.USER_INFOS_TABLE.' AS ui
      ON ui.user_id = uf.user_id
    INNER JOIN '.USERS_TABLE.' AS u
      ON u.'.$conf['user_fields']['id'].' = uf.user_id
  WHERE uf.id = \''.$_GET['feed'].'\'
;';
  $user = mysql_fetch_array(pwg_query($query));
}

if ( empty($user) )
{
  page_not_found('Unknown/missing feed identifier');
}

$user['forbidden_categories'] = calculate_permissions($user['id'],
                                                      $user['status']);
if ('' == $user['forbidden_categories'])
{
  $user['forbidden_categories'] = '0';
}

list($dbnow) = mysql_fetch_row(pwg_query('SELECT NOW();'));

include_once(PHPWG_ROOT_PATH.'include/feedcreator.class.php');

$base_url = 'http://'.$_SERVER["HTTP_HOST"].cookie_path();
if ( strrpos($base_url, '/') !== strlen($base_url)-1 )
{
  $base_url .= '/';
}
$page['root_path']=$base_url;

$rss = new UniversalFeedCreator();

$rss->title = $conf['gallery_title'];
$rss->title.= ' (as '.$user['username'].')';

$rss->link = $conf['gallery_url'];

// +-----------------------------------------------------------------------+
// |                            Feed creation                              |
// +-----------------------------------------------------------------------+

$news = news($user['last_check'], $dbnow, true);

if (count($news) > 0)
{
  $item = new FeedItem();
  $item->title = sprintf(l10n('New on %s'), $dbnow);
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
  WHERE id = \''.$_GET['feed'].'\'
;';
  pwg_query($query);
}


// build items for new images/albums
$query = '
SELECT date_available,
      COUNT(DISTINCT id) nb_images,
      COUNT(DISTINCT category_id) nb_cats
  FROM '.IMAGES_TABLE.' INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON id=image_id
  WHERE category_id NOT IN ('.$user['forbidden_categories'].')
  GROUP BY date_available
  ORDER BY date_available DESC
  LIMIT 0,5
;';
$result = pwg_query($query);
$dates = array();
while ($row = mysql_fetch_array($result))
{
  array_push($dates, $row);
}

foreach($dates as  $date_detail)
{ // for each recent post date we create a feed item
  $date = $date_detail['date_available'];
  $exploded_date = explode_mysqldt($date);
  $item = new FeedItem();
  $item->title = sprintf(l10n('%d new elements'), $date_detail['nb_images']);
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
        .sprintf(l10n('%d new elements'), $date_detail['nb_images'])
        .' ('
        .'<a href="'.make_index_url(array('section'=>'recent_pics')).'">'
          .l10n('recent_pics_cat').'</a>'
        .')'
        .'</li>';

  // get some thumbnails ...
  $query = '
SELECT DISTINCT id, path, name, tn_ext
  FROM '.IMAGES_TABLE.' INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON id=image_id
  WHERE category_id NOT IN ('.$user['forbidden_categories'].')
    AND date_available="'.$date.'"
    AND tn_ext IS NOT NULL
  LIMIT 0,6
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    $tn_src = get_thumbnail_src($row['path'], @$row['tn_ext']);
    $item->description .= '<img src="'.$tn_src.'"/>';
  }
  $item->description .= '...<br/>';


  $item->description .=
        '<li>'
        .sprintf(l10n('%d categories updated'), $date_detail['nb_cats'])
        .'</li>';
  // get some categories ...
  $query = '
SELECT DISTINCT c.uppercats, COUNT(DISTINCT i.id) img_count
  FROM '.IMAGES_TABLE.' i INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON i.id=image_id
    INNER JOIN '.CATEGORIES_TABLE.' c ON c.id=category_id
  WHERE category_id NOT IN ('.$user['forbidden_categories'].')
    AND date_available="'.$date.'"
  GROUP BY category_id
  ORDER BY img_count DESC
  LIMIT 0,6
;';
  $result = pwg_query($query);
  $item->description .= '<ul>';
  while ($row = mysql_fetch_array($result))
  {
    $item->description .=
          '<li>'
          .get_cat_display_name_cache($row['uppercats'])
          .' ('.sprintf(l10n('%d new elements'), $row['img_count']).')'
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