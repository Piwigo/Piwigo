<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
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

// +-----------------------------------------------------------------------+
// |                        unit mode form submission                      |
// +-----------------------------------------------------------------------+

if (isset($_POST['submit']))
{
  $collection = explode(',', $_POST['element_ids']);

  $datas = array();

  $query = '
SELECT id, date_creation
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $collection).')
;';
  $result = pwg_query($query);

  while ($row = mysql_fetch_assoc($result))
  {
    $data = array();

    $data{'id'} = $row['id'];
    $data{'name'} = $_POST['name-'.$row['id']];
    $data{'author'} = $_POST['author-'.$row['id']];

    foreach (array('name', 'author') as $field)
    {
      if (!empty($_POST[$field.'-'.$row['id']]))
      {
        $data{$field} = strip_tags($_POST[$field.'-'.$row['id']]);
      }
    }

    if ($conf['allow_html_descriptions'])
    {
      $data{'comment'} = @$_POST['description-'.$row['id']];
    }
    else
    {
      $data{'comment'} = strip_tags(@$_POST['description-'.$row['id']]);
    }

    if (isset($_POST['date_creation_action-'.$row['id']]))
    {
      if ('set' == $_POST['date_creation_action-'.$row['id']])
      {
        $data{'date_creation'} =
          $_POST['date_creation_year-'.$row['id']]
            .'-'.$_POST['date_creation_month-'.$row['id']]
            .'-'.$_POST['date_creation_day-'.$row['id']];
      }
      else if ('unset' == $_POST['date_creation_action-'.$row['id']])
      {
        $data{'date_creation'} = '';
      }
    }
    else
    {
      $data{'date_creation'} = $row['date_creation'];
    }

    array_push($datas, $data);

    // tags management
    if (isset($_POST[ 'tags-'.$row['id'] ]))
    {
      set_tags($_POST[ 'tags-'.$row['id'] ], $row['id']);
    }
  }

  mass_updates(
    IMAGES_TABLE,
    array(
      'primary' => array('id'),
      'update' => array('name','author','comment','date_creation')
      ),
    $datas
    );

  array_push($page['infos'], l10n('Picture informations updated'));
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(
  array('element_set_unit' => 'element_set_unit.tpl'));

$base_url = PHPWG_ROOT_PATH.'admin.php';

$month_list = $lang['month'];
$month_list[0]='------------';
ksort($month_list);

$template->assign(
  array(
    'CATEGORIES_NAV'=>$page['title'],

    'U_ELEMENTS_PAGE'
    =>$base_url.get_query_string_diff(array('display','start')),

    'U_GLOBAL_MODE'
    =>
    $base_url
    .get_query_string_diff(array('mode','display'))
    .'&amp;mode=global',

    'F_ACTION'=>$base_url.get_query_string_diff(array()),
    
    'month_list' => $month_list
    )
  );

// +-----------------------------------------------------------------------+
// |                        global mode thumbnails                         |
// +-----------------------------------------------------------------------+

// how many items to display on this page
if (!empty($_GET['display']))
{
  if ('all' == $_GET['display'])
  {
    $page['nb_images'] = count($page['cat_elements_id']);
  }
  else
  {
    $page['nb_images'] = intval($_GET['display']);
  }
}
else
{
  $page['nb_images'] = 5;
}



if (count($page['cat_elements_id']) > 0)
{
  $nav_bar = create_navigation_bar(
    $base_url.get_query_string_diff(array('start')),
    count($page['cat_elements_id']),
    $page['start'],
    $page['nb_images']
    );
  $template->assign(array('navbar' => $nav_bar));

  // tags
  $all_tags = get_all_tags();

  $element_ids = array();

  $query = '
SELECT id,path,tn_ext,name,date_creation,comment,author,file
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $page['cat_elements_id']).')
  '.$conf['order_by'].'
  LIMIT '.$page['start'].', '.$page['nb_images'].'
;';
  $result = pwg_query($query);

  while ($row = mysql_fetch_assoc($result))
  {
    // echo '<pre>'; print_r($row); echo '</pre>';
    array_push($element_ids, $row['id']);

    $src = get_thumbnail_url($row);

    $query = '
SELECT tag_id
  FROM '.IMAGE_TAG_TABLE.'
  WHERE image_id = '.$row['id'].'
;';
    $selected_tags = array_from_query($query, 'tag_id');

    // creation date
    if (!empty($row['date_creation']))
    {
      list($year,$month,$day) = explode('-', $row['date_creation']);
    }
    else
    {
      list($year,$month,$day) = array('',0,0);
    }

    if (count($all_tags) > 0)
    {
      $tag_selection = get_html_tag_selection(
        $all_tags,
        'tags-'.$row['id'],
        $selected_tags
        );
    }
    else
    {
      $tag_selection =
        '<p>'.
        l10n('No tag defined. Use Administration>Pictures>Tags').
        '</p>';
    }

    $template->append(
      'elements',
      array(
        'ID' => $row['id'],
        'TN_SRC' => $src,
        'LEGEND' =>
          !empty($row['name']) ?
            $row['name'] : get_name_from_file($row['file']),
        'U_EDIT' =>
            PHPWG_ROOT_PATH.'admin.php?page=picture_modify'.
            '&amp;image_id='.$row['id'],
        'NAME' => @$row['name'],
        'AUTHOR' => @$row['author'],
        'DESCRIPTION' => @$row['comment'],
        'DATE_CREATION_YEAR' => $year,
        'DATE_CREATION_MONTH' => (int)$month,
        'DATE_CREATION_DAY' => (int)$day,

        'TAG_SELECTION' => $tag_selection,
        )
      );
  }

  $template->assign('ELEMENT_IDS', implode(',', $element_ids));
}

// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'element_set_unit');
?>