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
else
{
  echo l10n('Unknown feed identifier');
  exit();
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

$rss->title = $conf['gallery_title'].', notifications';
$rss->title.= ' (as '.$user['username'].')';

$rss->link = $conf['gallery_url'];

// +-----------------------------------------------------------------------+
// |                            Feed creation                              |
// +-----------------------------------------------------------------------+

$news = news($user['last_check'], $dbnow);

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
  
  $rss->addItem($item);
}

$query = '
UPDATE '.USER_FEED_TABLE.'
  SET last_check = \''.$dbnow.'\'
  WHERE id = \''.$_GET['feed'].'\'
;';
pwg_query($query);

// send XML feed
echo $rss->saveFeed('RSS2.0', '', true);
?>