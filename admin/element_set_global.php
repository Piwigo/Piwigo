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
// |                               functions                               |
// +-----------------------------------------------------------------------+

/**
 * returns the list of uniq keywords among given elements
 *
 * @param array element_ids
 */
function get_elements_keywords($element_ids)
{
  if (0 == count($element_ids))
  {
    return array();
  }
  
  $keywords = array();
  
  $query = '
SELECT keywords
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $element_ids).')
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    if (isset($row['keywords']) and !empty($row['keywords']))
    {
      $keywords = array_merge($keywords, explode(',', $row['keywords']));
    }
  }
  return array_unique($keywords);
}

// +-----------------------------------------------------------------------+
// |                       global mode form submission                     |
// +-----------------------------------------------------------------------+

if (isset($_POST['submit']))
{
  $collection = array();
  
//   echo '<pre>';
//   print_r($_POST);
//   echo '</pre>';
//   exit();

  switch ($_POST['target'])
  {
    case 'all' :
    {
      $collection = $page['cat_elements_id'];
      break;
    }
    case 'selection' :
    {
      $collection = $_POST['selection'];
      break;
    }
  }

  if ($_POST['associate'] != 0)
  {
    $datas = array();

    $query = '
SELECT image_id
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id = '.$_POST['associate'].'
;';
    $associated = array_from_query($query, 'image_id');

    // TODO : if $associable array is empty, no further actions
    $associable = array_diff($collection, $associated);
    
    foreach ($associable as $item)
    {
      array_push($datas,
                 array('category_id'=>$_POST['associate'],
                       'image_id'=>$item));
    }
  
    mass_inserts(IMAGE_CATEGORY_TABLE,
                 array('image_id', 'category_id'),
                 $datas);
    update_category(array($_POST['associate']));
  }

  if ($_POST['dissociate'] != 0)
  {
    // physical links must not be broken, so we must first retrieve image_id
    // which create virtual links with the category to "dissociate from".
    $query = '
SELECT id
  FROM '.IMAGE_CATEGORY_TABLE.' INNER JOIN '.IMAGES_TABLE.' ON image_id = id
  WHERE category_id = '.$_POST['dissociate'].'
    AND category_id != storage_category_id
    AND id IN ('.implode(',', $collection).')
;';
    $dissociables = array_from_query($query, 'id');

    $query = '
DELETE FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id = '.$_POST['dissociate'].'
  AND image_id IN ('.implode(',', $dissociables).')
';
    pwg_query($query);

    update_category(array($_POST['dissociate']));
  }

  $datas = array();
  $dbfields = array('primary' => array('id'), 'update' => array());

  if (!empty($_POST['add_keywords']) or $_POST['remove_keyword'] != '0')
  {
    array_push($dbfields['update'], 'keywords');
  }

  $formfields = array('author', 'name', 'date_creation');
  foreach ($formfields as $formfield)
  {
    if ($_POST[$formfield.'_action'] != 'leave')
    {
      array_push($dbfields['update'], $formfield);
    }
  }
  
  // updating elements is useful only if needed...
  if (count($dbfields['update']) > 0)
  {
    $query = '
SELECT id, keywords
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $collection).')
;';
    $result = pwg_query($query);

    while ($row = mysql_fetch_array($result))
    {
      $data = array();
      $data['id'] = $row['id'];
      
      if (!empty($_POST['add_keywords']))
      {
        $data['keywords'] =
          implode(
            ',',
            array_unique(
              array_merge(
                get_keywords(empty($row['keywords']) ? '' : $row['keywords']),
                get_keywords($_POST['add_keywords'])
                )
              )
            );
      }

      if ($_POST['remove_keyword'] != '0')
      {
        if (!isset($data['keywords']))
        {
          $data['keywords'] = empty($row['keywords']) ? '' : $row['keywords'];
        }
        
        $data['keywords'] =
          implode(
            ',',
            array_unique(
              array_diff(
                get_keywords($data['keywords']),
                array($_POST['remove_keyword'])
                )
              )
            );

        if ($data['keywords'] == '')
        {
          unset($data['keywords']);
        }
      }

      if ('set' == $_POST['author_action'])
      {
        $data['author'] = $_POST['author'];

        if ('' == $data['author'])
        {
          unset($data['author']);
        }
      }

      if ('set' == $_POST['name_action'])
      {
        $data['name'] = $_POST['name'];

        if ('' == $data['name'])
        {
          unset($data['name']);
        }
      }

      if ('set' == $_POST['date_creation_action'])
      {
        $data['date_creation'] =
          $_POST['date_creation_year']
          .'-'.$_POST['date_creation_month']
          .'-'.$_POST['date_creation_day']
          ;
      }
      
      array_push($datas, $data);
    }
    // echo '<pre>'; print_r($datas); echo '</pre>';
    mass_updates(IMAGES_TABLE, $dbfields, $datas);
  }
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+
$template->set_filenames(
  array('element_set_global' => 'admin/element_set_global.tpl'));

$base_url = PHPWG_ROOT_PATH.'admin.php';

// $form_action = $base_url.'?page=element_set_global';

$template->assign_vars(
  array(
    'CATEGORY_TITLE'=>$page['title'],
    
    'L_SUBMIT'=>$lang['submit'],

    'U_COLS'=>$base_url.get_query_string_diff(array('cols')),
    'U_DISPLAY'=>$base_url.get_query_string_diff(array('display')),
    
    'U_UNIT_MODE'
    =>
    $base_url
    .get_query_string_diff(array('mode','display'))
    .'&amp;mode=unit',
    
    'F_ACTION'=>$base_url.get_query_string_diff(array()),
   )
 );

// +-----------------------------------------------------------------------+
// |                            caddie options                             |
// +-----------------------------------------------------------------------+

if ('caddie' == $_GET['cat'])
{
  $template->assign_block_vars('in_caddie', array());
}
else
{
  $template->assign_block_vars('not_in_caddie', array());
}

// +-----------------------------------------------------------------------+
// |                           global mode form                            |
// +-----------------------------------------------------------------------+

// Virtualy associate a picture to a category
$blockname = 'associate_option';

$template->assign_block_vars(
  $blockname,
  array('SELECTED' => '',
        'VALUE'=> 0,
        'OPTION' => '------------'
    ));

$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
;';
display_select_cat_wrapper($query, array(), $blockname, true);

// Dissociate from a category : categories listed for dissociation can
// only represent virtual links. Links to physical categories can't be
// broken
$blockname = 'dissociate_option';

$template->assign_block_vars(
  $blockname,
  array('SELECTED' => '',
        'VALUE'=> 0,
        'OPTION' => '------------'
    ));

if (count($page['cat_elements_id']) > 0)
{
  $query = '
SELECT DISTINCT(category_id) AS id, c.name, uppercats, global_rank
  FROM '.IMAGE_CATEGORY_TABLE.' AS ic,
       '.CATEGORIES_TABLE.' AS c,
       '.IMAGES_TABLE.' AS i
  WHERE ic.image_id IN ('.implode(',', $page['cat_elements_id']).')
    AND ic.category_id = c.id
    AND ic.image_id = i.id
    AND ic.category_id != i.storage_category_id
;';
  display_select_cat_wrapper($query, array(), $blockname, true);
}

$blockname = 'remove_keyword_option';

$template->assign_block_vars(
  $blockname,
  array('VALUE'=> 0,
        'OPTION' => '------------'
    ));

$keywords = get_elements_keywords($page['cat_elements_id']);

foreach ($keywords as $keyword)
{
  $template->assign_block_vars(
  $blockname,
  array('VALUE'=> $keyword,
        'OPTION' => $keyword
    ));
}

// creation date
$day =
empty($_POST['date_creation_day']) ? date('j') : $_POST['date_creation_day'];
get_day_list('date_creation_day', $day);

if (!empty($_POST['date_creation_month']))
{
  $month = $_POST['date_creation_month'];
}
else
{
  $month = date('n');
}
get_month_list('date_creation_month', $month);

if (!empty($_POST['date_creation_year']))
{
  $year = $_POST['date_creation_year'];
}
else
{
  $year = date('Y');
}
$template->assign_vars(array('DATE_CREATION_YEAR_VALUE'=>$year));

// +-----------------------------------------------------------------------+
// |                        global mode thumbnails                         |
// +-----------------------------------------------------------------------+

$page['cols'] = !empty($_GET['cols']) ? intval($_GET['cols']) : 5;
$page['nb_images'] = !empty($_GET['display']) ? intval($_GET['display']) : 20;

if (count($page['cat_elements_id']) > 0)
{
  $nav_bar = create_navigation_bar(
    $base_url.get_query_string_diff(array('start')),
    count($page['cat_elements_id']),
    $page['start'],
    $page['nb_images'],
    '');
  $template->assign_vars(array('NAV_BAR' => $nav_bar));

  $query = '
SELECT id,path,tn_ext
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $page['cat_elements_id']).')
  '.$conf['order_by'].'
  LIMIT '.$page['start'].', '.$page['nb_images'].'
;';
  //echo '<pre>'.$query.'</pre>';
  $result = pwg_query($query);

  // template thumbnail initialization
  if (mysql_num_rows($result) > 0)
  {
    $template->assign_block_vars('thumbnails', array());
    // first line
    $template->assign_block_vars('thumbnails.line', array());
    // current row displayed
    $row_number = 0;
  }

  while ($row = mysql_fetch_array($result))
  {
    $src = get_thumbnail_src($row['path'], @$row['tn_ext']);
    
    $template->assign_block_vars(
      'thumbnails.line.thumbnail',
      array(
        'ID' => $row['id'],
        'SRC' => $src,
        'ALT' => 'TODO',
        'TITLE' => 'TODO'
        )
      );
    
    // create a new line ?
    if (++$row_number == $page['cols'])
    {
    $template->assign_block_vars('thumbnails.line', array());
    $row_number = 0;
    }
  }
}

//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'element_set_global');
?>
