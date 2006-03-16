<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2006-03-15 03:26:25 +0100 (mer, 15 mar 2006) $
// | last modifier : $Author: rvelices $
// | revision      : $Revision: 1081 $
// | revision      : $Revision: 1081 $
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
 * @param int user identifier
 * @param int image identifier
 * @param int rate
 * @return void
 */
function rate_picture($user_id, $image_id, $rate)
{
  global $conf;

  $query = '
SELECT status
  FROM '.USER_INFOS_TABLE.'
  WHERE user_id = '.$user_id.'
;';
  list($user_status) = mysql_fetch_array(pwg_query($query));

  if ('guest' == $user_status
      or 'generic' == $user_status)
  {
    $user_anonymous = true;
  }
  else
  {
    $user_anonymous = false;
  }
  
  if (isset($rate)
      and $conf['rate']
      and (!$user_anonymous or $conf['rate_anonymous'])
      and in_array($rate, $conf['rate_items']))
  {
    if ($user_anonymous)
    {
      $ip_components = explode('.', $_SERVER["REMOTE_ADDR"]);
      if (count($ip_components) > 3)
      {
        array_pop($ip_components);
      }
      $anonymous_id = implode ('.', $ip_components);
          
      if (isset($_COOKIE['pwg_anonymous_rater']))
      {
        if ($anonymous_id != $_COOKIE['pwg_anonymous_rater'])
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
    AND anonymous_id = \''.$_COOKIE['pwg_anonymous_rater'].'\'
    AND element_id NOT IN ('.implode(',', $already_there).')
;';
            pwg_query($query);
          }

          $query = '
UPDATE
  '.RATE_TABLE.'
  SET anonymous_id = \'' .$anonymous_id.'\'
  WHERE user_id = '.$user['id'].'
    AND anonymous_id = \'' . $_COOKIE['pwg_anonymous_rater'].'\'
;';
          pwg_query($query);

          setcookie(
            'pwg_anonymous_rater',
            $anonymous_id,
            strtotime('+10 years'),
            cookie_path()
            );
        }
      }
      else
      {
        setcookie(
          'pwg_anonymous_rater',
          $anonymous_id,
          strtotime('+10 years'),
          cookie_path()
          );
      }
    }
    
    $query = '
DELETE
  FROM '.RATE_TABLE.'
  WHERE element_id = '.$image_id.'
  AND user_id = '.$user_id.'
';
    if (isset($anonymous_id))
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
      .$user_id.','
      .(isset($anonymous_id) ? '\''.$anonymous_id.'\'' : "''").','
      .$image_id.','
      .$rate
      .',NOW())
;';
    pwg_query($query);
        
    // update of images.average_rate field
    $query = '
SELECT ROUND(AVG(rate),2) AS average_rate
  FROM '.RATE_TABLE.'
  WHERE element_id = '.$image_id.'
;';
    $row = mysql_fetch_array(pwg_query($query));
    $query = '
UPDATE '.IMAGES_TABLE.'
  SET average_rate = '.$row['average_rate'].'
  WHERE id = '.$image_id.'
;';
    pwg_query($query);
  }
}

?>