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


/**
 * Extract news fonctions of feed.php
 */

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
 * currently waiting pictures
 *
 * @return array waiting ids
 */
function waiting_elements()
{
  $query = '
SELECT id
  FROM '.WAITING_TABLE.'
  WHERE validated = \'false\'
;';

  return array_from_query($query, 'id');
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
  
  if (is_admin())
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

    $nb_waiting_elements = count(waiting_elements());
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