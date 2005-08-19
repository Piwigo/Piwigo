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
// |                        unit mode form submission                      |
// +-----------------------------------------------------------------------+

if (isset($_POST['submit']))
{
  $collection = explode(',', $_POST['list']);
  
//   echo '<pre>';
//   print_r($_POST);
//   echo '</pre>';
//   exit();

  $datas = array();
  $dbfields =
    array(
      'primary' => array('id'),
      'update' => array('name','author','comment','date_creation','keywords')
      );
  
  $query = '
SELECT id, date_creation
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $collection).')
;';
  $result = pwg_query($query);

  while ($row = mysql_fetch_array($result))
  {
    $data = array();
    $data['id'] = $row['id'];
    
    foreach (array_diff($dbfields['update'], array('date_creation')) as $field)
    {
      if (!empty($_POST[$field.'-'.$row['id']]))
      {
        $data[$field] = $_POST[$field.'-'.$row['id']];
      }
    }

    if ('set' == $_POST['date_creation_action-'.$row['id']])
    {
      $data['date_creation'] =
        $_POST['date_creation_year-'.$row['id']]
        .'-'.$_POST['date_creation_month-'.$row['id']]
        .'-'.$_POST['date_creation_day-'.$row['id']]
        ;
    }
    else if ('leave' == $_POST['date_creation_action-'.$row['id']]
             and !empty($row['date_creation']))
    {
      $data['date_creation'] = $row['date_creation'];
    }

    array_push($datas, $data);
  }
  // echo '<pre>'; print_r($datas); echo '</pre>';
  mass_updates(IMAGES_TABLE, $dbfields, $datas);
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(
  array('element_set_unit' => 'admin/element_set_unit.tpl'));

$base_url = PHPWG_ROOT_PATH.'admin.php';

// $form_action = $base_url.'?page=element_set_global';

$template->assign_vars(
  array(
    'CATEGORIES_NAV'=>$page['title'],

    'L_SUBMIT'=>$lang['submit'],
    
    'U_ELEMENTS_PAGE'
    =>$base_url.get_query_string_diff(array('display','start')),
    
    'U_GLOBAL_MODE'
    =>
    $base_url
    .get_query_string_diff(array('mode','display'))
    .'&amp;mode=global',
    
    'F_ACTION'=>$base_url.get_query_string_diff(array()),
    )
  );

// +-----------------------------------------------------------------------+
// |                        global mode thumbnails                         |
// +-----------------------------------------------------------------------+

$page['nb_images'] = !empty($_GET['display']) ? intval($_GET['display']) : 5;


if (count($page['cat_elements_id']) > 0)
{
  $nav_bar = create_navigation_bar(
    $base_url.get_query_string_diff(array('start')),
    count($page['cat_elements_id']),
    $page['start'],
    $page['nb_images'],
    '');
  $template->assign_vars(array('NAV_BAR' => $nav_bar));

 
  $element_ids = array();

  $query = '
SELECT id,path,tn_ext,name,date_creation,comment,keywords,author
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $page['cat_elements_id']).')
  '.$conf['order_by'].'
  LIMIT '.$page['start'].', '.$page['nb_images'].'
;';
  $result = pwg_query($query);

  while ($row = mysql_fetch_array($result))
  {
    // echo '<pre>'; print_r($row); echo '</pre>';
    array_push($element_ids, $row['id']);
    
    $src = get_thumbnail_src($row['path'], @$row['tn_ext']);
    
    // creation date
    if (!empty($row['date_creation']))
    {
      list($year,$month,$day) = explode('-', $row['date_creation']);
    }
    else
    {
      list($year,$month,$day) = array('','','');
    }
    
    $template->assign_block_vars(
      'element',
      array(
        'ID' => $row['id'],
        'FILENAME' => $row['path'],
        'TN_SRC' => $src,
        'NAME' => @$row['name'],
        'AUTHOR' => @$row['author'],
        'COMMENT' => @$row['comment'],
        'DATE_CREATION_YEAR' => $year,
        'KEYWORDS' => @$row['keywords']
        )
      );
    
    get_day_list('element.date_creation_day', $day);
    get_month_list('element.date_creation_month', $month);
  }

  $template->assign_vars(array('IDS_LIST' => implode(',', $element_ids)));
}

// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'element_set_unit');
?>