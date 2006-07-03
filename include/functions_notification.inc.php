<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2005-11-26 21:15:50 +0100 (sam., 26 nov. 2005) $
// | last modifier : $Author: plg $
// | revision      : $Revision: 958 $
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

// +-----------------------------------------------------------------------+
// |                               functions                               |
// +-----------------------------------------------------------------------+

/*
 * Execute custom notification query
 *
 * @param string action ('count' or 'info')
 * @param string type of query ('new_comments', 'unvalidated_comments', 'new_elements', 'updated_categories', 'new_users', 'waiting_elements')
 * @param string start (mysql datetime format)
 * @param string end (mysql datetime format)
 *
 * @return integer for action count
 *         array for info
 */
function custom_notification_query($action, $type, $start, $end)
{
  global $user;

  switch($type)
  {
    case 'new_comments':
      $query = '
  FROM '.COMMENTS_TABLE.' AS c
     , '.IMAGE_CATEGORY_TABLE.' AS ic
  WHERE c.image_id = ic.image_id
    AND c.validation_date > \''.$start.'\'
    AND c.validation_date <= \''.$end.'\'
    AND category_id NOT IN ('.$user['forbidden_categories'].')
;';
      break;
    case 'unvalidated_comments':
      $query = '
  FROM '.COMMENTS_TABLE.'
  WHERE date <= \''.$end.'\'
    AND (validated = \'false\'
         OR validation_date > \''.$end.'\')
;';
      break;
    case 'new_elements':
      $query = '
  FROM '.IMAGES_TABLE.' INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON image_id = id
  WHERE date_available > \''.$start.'\'
    AND date_available <= \''.$end.'\'
    AND category_id NOT IN ('.$user['forbidden_categories'].')
;';
      break;
    case 'updated_categories':
      $query = '
  FROM '.IMAGES_TABLE.' INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON image_id = id
  WHERE date_available > \''.$start.'\'
    AND date_available <= \''.$end.'\'
    AND category_id NOT IN ('.$user['forbidden_categories'].')
;';
      break;
    case 'new_users':
      $query = '
  FROM '.USER_INFOS_TABLE.'
  WHERE registration_date > \''.$start.'\'
    AND registration_date <= \''.$end.'\'
;';
      break;
    case 'waiting_elements':
      $query = '
  FROM '.WAITING_TABLE.'
  WHERE validated = \'false\'
;';
      break;
    default:
      // stop this function and return nothing
      return;
      break;
  }

  switch($action)
  {
    case 'count':
      switch($type)
      {
        case 'new_comments':
          $field_id = 'c.id';
          break;
        case 'unvalidated_comments':
          $field_id = 'id';
          break;
        case 'new_elements':
          $field_id = 'image_id';
          break;
        case 'updated_categories':
          $field_id = 'category_id';
          break;
        case 'new_users':
          $field_id = 'user_id';
          break;
        case 'waiting_elements':
          $field_id = 'id';
          break;
    }
    $query = 'SELECT count(distinct '.$field_id.') as CountId
'.$query;
    list($count) = mysql_fetch_array(pwg_query($query));
    return $count;
    
    break;
    case 'info':
      switch($type)
      {
        case 'new_comments':
          $fields = array('c.id');
          break;
        case 'unvalidated_comments':
          $fields = array('id');
          break;
        case 'new_elements':
          $fields = array('image_id');
          break;
        case 'updated_categories':
          $fields = array('category_id');
          break;
        case 'new_users':
          $fields = array('user_id');
          break;
        case 'waiting_elements':
          $fields = array('id');
          break;
      }

    $query = 'SELECT distinct '.implode(', ', $fields).' 
'.$query;
    $result = pwg_query($query);

    $infos = array();
  
    while ($row = mysql_fetch_array($result))
    {
      array_push($infos, $row);
    }

    return $infos;

    break;
  }

  //return is done on previous switch($action)
}

/**
 * new comments between two dates, according to authorized categories
 *
 * @param string start (mysql datetime format)
 * @param string end (mysql datetime format)
 * @param string forbidden categories (comma separated)
 * @return count comment ids
 */
function nb_new_comments($start, $end)
{
  return custom_notification_query('count', 'new_comments', $start, $end);
}

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
  return custom_notification_query('info', 'new_comments', $start, $end);
}

/**
 * unvalidated at a precise date
 *
 * Comments that are registered and not validated yet on a precise date
 *
 * @param string date (mysql datetime format)
 * @return count comment ids
 */
function nb_unvalidated_comments($date)
{
  return custom_notification_query('count', 'unvalidated_comments', $date, $date);
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
  return custom_notification_query('info', 'unvalidated_comments', $start, $end);
}

/**
 * new elements between two dates, according to authorized categories
 *
 * @param string start (mysql datetime format)
 * @param string end (mysql datetime format)
 * @param string forbidden categories (comma separated)
 * @return count element ids
 */
function nb_new_elements($start, $end)
{
  return custom_notification_query('count', 'new_elements', $start, $end);
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
  return custom_notification_query('info', 'new_elements', $start, $end);
}

/**
 * updated categories between two dates, according to authorized categories
 *
 * @param string start (mysql datetime format)
 * @param string end (mysql datetime format)
 * @param string forbidden categories (comma separated)
 * @return count element ids
 */
function nb_updated_categories($start, $end)
{
  return custom_notification_query('count', 'updated_categories', $start, $end);
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
  return custom_notification_query('info', 'updated_categories', $start, $end);
}

/**
 * new registered users between two dates
 *
 * @param string start (mysql datetime format)
 * @param string end (mysql datetime format)
 * @return count user ids
 */
function nb_new_users($start, $end)
{
  return custom_notification_query('count', 'new_users', $start, $end);
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
  return custom_notification_query('info', 'new_users', $start, $end);
}

/**
 * currently waiting pictures
 *
 * @return count waiting ids
 */
function nb_waiting_elements()
{
  return custom_notification_query('count', 'waiting_elements', '', '');
}

/**
 * currently waiting pictures
 *
 * @return array waiting ids
 */
function waiting_elements()
{
  return custom_notification_query('info', 'waiting_elements', $start, $end);
}

/**
 * There are new between two dates ?
 *
 * Informations : number of new comments, number of new elements, number of
 * updated categories. Administrators are also informed about : number of
 * unvalidated comments, number of new users (TODO : number of unvalidated
 * elements)
 *
 * @param string start date (mysql datetime format)
 * @param string end date (mysql datetime format)
 *
 * @return boolean : true if exist news else false
 */
function news_exists($start, $end)
{
  return (
          (nb_new_comments($start, $end) > 0) or
          (nb_new_elements($start, $end) > 0) or
          (nb_updated_categories($start, $end) > 0) or
          ((is_admin()) and (nb_unvalidated_comments($end) > 0)) or
          ((is_admin()) and (nb_new_users($start, $end) > 0)) or
          ((is_admin()) and (nb_waiting_elements() > 0))
        );
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
 *
 * @return array of news
 */
function news($start, $end)
{
  $news = array();

  $nb_new_comments = nb_new_comments($start, $end);
  if ($nb_new_comments > 0)
  {
    array_push($news, sprintf(l10n('%d new comments'), $nb_new_comments));
  }

  $nb_new_elements = nb_new_elements($start, $end);
  if ($nb_new_elements > 0)
  {
    array_push($news, sprintf(l10n('%d new elements'), $nb_new_elements));
  }

  $nb_updated_categories = nb_updated_categories($start, $end);
  if ($nb_updated_categories > 0)
  {
    array_push($news, sprintf(l10n('%d categories updated'),
                              $nb_updated_categories));
  }
  
  if (is_admin())
  {
    $nb_unvalidated_comments = nb_unvalidated_comments($end);
    if ($nb_unvalidated_comments > 0)
    {
      array_push($news, sprintf(l10n('%d comments to validate'),
                                $nb_unvalidated_comments));
    }

    $nb_new_users = nb_new_users($start, $end);
    if ($nb_new_users > 0)
    {
      array_push($news, sprintf(l10n('%d new users'), $nb_new_users));
    }

    $nb_waiting_elements = nb_waiting_elements();
    if ($nb_waiting_elements > 0)
    {
      array_push(
        $news,
        sprintf(
          l10n('%d waiting elements'),
          $nb_waiting_elements
          )
        );
    }
  }

  return $news;
}

?>