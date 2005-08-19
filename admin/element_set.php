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

/**
 * Management of elements set. Elements can belong to a category or to the
 * user caddie.
 * 
 */
 
if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}
include_once(PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php');

// +-----------------------------------------------------------------------+
// |                          caddie management                            |
// +-----------------------------------------------------------------------+

if (isset($_POST['submit_caddie']))
{
  if (isset($_POST['caddie_action']))
  {
    switch ($_POST['caddie_action'])
    {
      case 'empty_all' :
      {
          $query = '
DELETE FROM '.CADDIE_TABLE.'
  WHERE user_id = '.$user['id'].'
;';
          pwg_query($query);
          break;
      }
      case 'empty_selected' :
      {
        if (isset($_POST['selection']) and count($_POST['selection']) > 0)
        {
          $query = '
DELETE
  FROM '.CADDIE_TABLE.'
  WHERE element_id IN ('.implode(',', $_POST['selection']).')
    AND user_id = '.$user['id'].'
;';
          pwg_query($query);
        }
        else
        {
          // TODO : add error
        }
        break;
      }
      case 'add_selected' :
      {
        if (isset($_POST['selection']) and count($_POST['selection']) > 0)
        {
          fill_caddie($_POST['selection']);
        }
        else
        {
          // TODO : add error
        }
        break;
      }
    }
  }
  else
  {
    // TODO : add error
  }
}

// +-----------------------------------------------------------------------+
// |                    initialize info about category                     |
// +-----------------------------------------------------------------------+

// To element_set_(global|unit).php, we must provide the elements id of the
// managed category in $page['cat_elements_id'] array.

if (is_numeric($_GET['cat']))
{
  $cat_infos = get_cat_info($_GET['cat']);
  $page['title'] =
    get_cat_display_name(
      $cat_infos['name'],
      PHPWG_ROOT_PATH.'admin.php?page=cat_modify&amp;cat_id=',
      false
      );
  
  $query = '
SELECT image_id
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id = '.$_GET['cat'].'
;';
  $page['cat_elements_id'] = array_from_query($query, 'image_id');
}
else if ('caddie' == $_GET['cat'])
{
  $page['title'] = 'caddie';
  
  $query = '
SELECT element_id
  FROM '.CADDIE_TABLE.'
  WHERE user_id = '.$user['id'].'
;';
  $page['cat_elements_id'] = array_from_query($query, 'element_id');
}
else if ('not_linked' == $_GET['cat'])
{
  $page['title'] = 'elements not linked to any virtual categories';
  
  // we are searching elements not linked to any virtual category
  $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE dir IS NULL
;';
  $virtual_categories = array_from_query($query, 'id');

  $query = '
SELECT DISTINCT(image_id)
  FROM '.IMAGE_CATEGORY_TABLE.'
;';
  $all_elements = array_from_query($query, 'image_id');
  
  $query = '
SELECT DISTINCT(image_id)
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id IN ('.implode(',', $virtual_categories).')
;';
  $linked_to_virtual = array_from_query($query, 'image_id');

  $page['cat_elements_id'] = array_diff($all_elements, $linked_to_virtual);
}

// +-----------------------------------------------------------------------+
// |                       first element to display                        |
// +-----------------------------------------------------------------------+

// $page['start'] contains the number of the first element in its
// category. For exampe, $page['start'] = 12 means we must show elements #12
// and $page['nb_images'] next elements

if (!isset($_GET['start'])
    or !is_numeric($_GET['start'])
    or $_GET['start'] < 0)
{
  $page['start'] = 0;
}
else
{
  $page['start'] = $_GET['start'];
}

// +-----------------------------------------------------------------------+
// |                         open specific mode                            |
// +-----------------------------------------------------------------------+

$_GET['mode'] = !empty($_GET['mode']) ? $_GET['mode'] : 'global';

switch ($_GET['mode'])
{
  case 'global' :
  {
    include(PHPWG_ROOT_PATH.'admin/element_set_global.php');
    break;
  }
  case 'unit' :
  {
    include(PHPWG_ROOT_PATH.'admin/element_set_unit.php');
    break;
  }
}
?>