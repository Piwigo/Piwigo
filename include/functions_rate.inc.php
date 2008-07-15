<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
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

/**
 * rate a picture by a user
 *
 * @param int image identifier
 * @param int rate
 * @return void
 */
function rate_picture($image_id, $rate)
{
  global $conf, $user;

  if (!isset($rate)
      or !$conf['rate']
      or !in_array($rate, $conf['rate_items']))
  {
    return false;
  }

  $user_anonymous = is_autorize_status(ACCESS_CLASSIC) ? false : true;

  if ($user_anonymous and !$conf['rate_anonymous'])
  {
    return false;
  }

  $ip_components = explode('.', $_SERVER["REMOTE_ADDR"]);
  if (count($ip_components) > 3)
  {
    array_pop($ip_components);
  }
  $anonymous_id = implode ('.', $ip_components);

  if ($user_anonymous)
  {
    $save_anonymous_id = pwg_get_cookie_var('anonymous_rater', $anonymous_id);

    if ($anonymous_id != $save_anonymous_id)
    { // client has changed his IP adress or he's trying to fool us
      $query = '
SELECT element_id
  FROM '.RATE_TABLE.'
  WHERE user_id = '.$user['id'].'
    AND anonymous_id = \''.$anonymous_id.'\'
;';
      $already_there = array_from_query($query, 'element_id');

      if (count($already_there) > 0)
      {
        $query = '
DELETE
  FROM '.RATE_TABLE.'
  WHERE user_id = '.$user['id'].'
    AND anonymous_id = \''.$save_anonymous_id.'\'
    AND element_id IN ('.implode(',', $already_there).')
;';
         pwg_query($query);
       }

       $query = '
UPDATE '.RATE_TABLE.'
  SET anonymous_id = \'' .$anonymous_id.'\'
  WHERE user_id = '.$user['id'].'
    AND anonymous_id = \'' . $save_anonymous_id.'\'
;';
       pwg_query($query);
    } // end client changed ip

    pwg_set_cookie_var('anonymous_rater', $anonymous_id);
  } // end anonymous user

  $query = '
DELETE
  FROM '.RATE_TABLE.'
  WHERE element_id = '.$image_id.'
    AND user_id = '.$user['id'].'
';
  if (isset($user_anonymous))
  {
    $query.= ' AND anonymous_id = \''.$anonymous_id.'\'';
  }
  pwg_query($query);
  $query = '
INSERT
  INTO '.RATE_TABLE.'
  (user_id,anonymous_id,element_id,rate,date)
  VALUES
  ('
    .$user['id'].','
    .'\''.$anonymous_id.'\','
    .$image_id.','
    .$rate
    .',NOW())
;';
  pwg_query($query);

  // update of images.average_rate field
  $query = '
SELECT COUNT(rate) AS count
     , ROUND(AVG(rate),2) AS average
     , ROUND(STD(rate),2) AS stdev
  FROM '.RATE_TABLE.'
  WHERE element_id = '.$image_id.'
;';
  $row = mysql_fetch_assoc(pwg_query($query));
  $query = '
UPDATE '.IMAGES_TABLE.'
  SET average_rate = '.$row['average'].'
  WHERE id = '.$image_id.'
;';
  pwg_query($query);
  return $row;
}

?>