<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
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
 * Management of elements set. Elements can belong to a category or to the
 * user caddie.
 *
 */

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

check_input_parameter('selection', $_POST, true, PATTERN_ID);

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
$page['cat_elements_id'] = array();
if (is_numeric($_GET['cat']))
{
  $page['title'] =
    get_cat_display_name_from_id(
      $_GET['cat'],
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
  $page['title'] = l10n('caddie');

  $query = '
SELECT element_id
  FROM '.CADDIE_TABLE.'
  WHERE user_id = '.$user['id'].'
;';
  $page['cat_elements_id'] = array_from_query($query, 'element_id');
}
else if ('not_linked' == $_GET['cat'])
{
  $page['title'] = l10n('Not linked elements');
  $template->assign(array('U_ACTIVE_MENU' => 5 ));

  // we are searching elements not linked to any virtual category
  $query = '
SELECT id
  FROM '.IMAGES_TABLE.'
;';
  $all_elements = array_from_query($query, 'id');

  $linked_to_virtual = array();

  $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE dir IS NULL
;';
  $virtual_categories = array_from_query($query, 'id');
  if (!empty($virtual_categories))
  {
    $query = '
SELECT DISTINCT(image_id)
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id IN ('.implode(',', $virtual_categories).')
;';
    $linked_to_virtual = array_from_query($query, 'image_id');
  }

  $page['cat_elements_id'] = array_diff($all_elements, $linked_to_virtual);
}
else if ('duplicates' == $_GET['cat'])
{
  $page['title'] = l10n('Files with same name in more than one physical category');
  $template->assign(array('U_ACTIVE_MENU' => 5 ));

  // we are searching related elements twice or more to physical categories
  // 1 - Retrieve Files
  $query = '
SELECT DISTINCT(file)
  FROM '.IMAGES_TABLE.'
 GROUP BY file
HAVING COUNT(DISTINCT storage_category_id) > 1
;';

  $duplicate_files = array_from_query($query, 'file');
  $duplicate_files[]='Nofiles';
  // 2 - Retrives related picture ids
  $query = '
SELECT id, file
  FROM '.IMAGES_TABLE.'
WHERE file IN (\''.implode("','", $duplicate_files).'\')
ORDER BY file, id
;';

  $page['cat_elements_id'] = array_from_query($query, 'id');
}
elseif ('recent'== $_GET['cat'])
{
  $page['title'] = l10n('Recent pictures');
  $query = 'SELECT MAX(date_available) AS date
  FROM '.IMAGES_TABLE;
  $row = pwg_db_fetch_assoc(pwg_query($query));
  if (!empty($row['date']))
  {
    $query = 'SELECT id
  FROM '.IMAGES_TABLE.'
  WHERE date_available BETWEEN '.pwg_db_get_recent_period_expression(1, $row['date']).' AND \''.$row['date'].'\'';
    $page['cat_elements_id'] = array_from_query($query, 'id');
  }
}

// +-----------------------------------------------------------------------+
// |                       first element to display                        |
// +-----------------------------------------------------------------------+

// $page['start'] contains the number of the first element in its
// category. For exampe, $page['start'] = 12 means we must show elements #12
// and $page['nb_images'] next elements

if (!isset($_GET['start'])
    or !is_numeric($_GET['start'])
    or $_GET['start'] < 0
    or (isset($_GET['display']) and 'all' == $_GET['display']))
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
