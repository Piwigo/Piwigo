<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $Id$
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

// +-----------------------------------------------------------------------+
// |                           initialization                              |
// +-----------------------------------------------------------------------+
define('PHPWG_ROOT_PATH','./');
include_once(PHPWG_ROOT_PATH.'include/common.inc.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_GUEST);

$sort_order = array(
  'descending' => 'DESC',
  'ascending' => 'ASC'
  );

// sort_by : database fields proposed for sorting comments list
$sort_by = array(
  'date' => 'comment date',
  'image_id' => 'picture'
  );

// items_number : list of number of items to display per page
$items_number = array(5,10,20,50,'all');

// since when display comments ?
//
$since_options = array(
  1 => array('label' => l10n('today'),
             'clause' => 'date > SUBDATE(CURDATE(), INTERVAL 1 DAY)'),
  2 => array('label' => sprintf(l10n('last %d days'), 7),
             'clause' => 'date > SUBDATE(CURDATE(), INTERVAL 7 DAY)'),
  3 => array('label' => sprintf(l10n('last %d days'), 30),
             'clause' => 'date > SUBDATE(CURDATE(), INTERVAL 30 DAY)'),
  4 => array('label' => l10n('the beginning'),
             'clause' => '1=1') // stupid but generic
  );

$page['since'] = isset($_GET['since']) ? $_GET['since'] : 3;

// on which field sorting
//
$page['sort_by'] = 'date';
// if the form was submitted, it overloads default behaviour
if (isset($_GET['sort_by']))
{
  $page['sort_by'] = $_GET['sort_by'];
}

// order to sort
//
$page['sort_order'] = $sort_order['descending'];
// if the form was submitted, it overloads default behaviour
if (isset($_GET['sort_order']))
{
  $page['sort_order'] = $sort_order[$_GET['sort_order']];
}

// number of items to display
//
$page['items_number'] = 5;
if (isset($_GET['items_number']))
{
  $page['items_number'] = $_GET['items_number'];
}

// which category to filter on ?
$page['cat_clause'] = '1=1';
if (isset($_GET['cat']) and 0 != $_GET['cat'])
{
  $page['cat_clause'] =
    'category_id IN ('.implode(',', get_subcat_ids(array($_GET['cat']))).')';
}

// search a particular author
$page['author_clause'] = '1=1';
if (isset($_GET['author']) and !empty($_GET['author']))
{
  if (function_exists('mysql_real_escape_string'))
  {
    $author = mysql_real_escape_string($_GET['author']);
  }
  else
  {
    $author = mysql_escape_string($_GET['author']);
  }

  $page['author_clause'] = 'author = \''.$author.'\'';
}

// search a substring among comments content
$page['keyword_clause'] = '1=1';
if (isset($_GET['keyword']) and !empty($_GET['keyword']))
{
  if (function_exists('mysql_real_escape_string'))
  {
    $keyword = mysql_real_escape_string($_GET['keyword']);
  }
  else
  {
    $keyword = mysql_escape_string($_GET['keyword']);
  }
  $page['keyword_clause'] =
    '('.
    implode(' AND ',
            array_map(
              create_function(
                '$s',
                'return "content LIKE \'%$s%\'";'
                ),
              preg_split('/[\s,;]+/', $keyword)
              )
      ).
    ')';
}

// which status to filter on ?
if ( is_admin() )
{
  $page['status_clause'] = '1=1';
}
else
{
  $page['status_clause'] = 'validated="true"';
}


// +-----------------------------------------------------------------------+
// |                         comments management                           |
// +-----------------------------------------------------------------------+
if (is_admin() and !is_adviser() )
{
  if (isset($_GET['delete']) and is_numeric($_GET['delete']) )
  {// comments deletion
    $query = '
DELETE FROM '.COMMENTS_TABLE.'
  WHERE id='.$_GET['delete'].'
;';
    pwg_query($query);
  }

  if (isset($_GET['validate']) and is_numeric($_GET['validate']) )
  {  // comments validation
    $query = '
UPDATE '.COMMENTS_TABLE.'
  SET validated = \'true\'
    , validation_date = NOW()
  WHERE id='.$_GET['validate'].'
;';
    pwg_query($query);
  }
}
// +-----------------------------------------------------------------------+
// |                       page header and options                         |
// +-----------------------------------------------------------------------+

$title= l10n('title_comments');
$page['body_id'] = 'theCommentsPage';
include(PHPWG_ROOT_PATH.'include/page_header.php');

$template->set_filenames(array('comments'=>'comments.tpl'));
$template->assign_vars(
  array(
    'L_COMMENT_TITLE' => $title,

    'F_ACTION'=>PHPWG_ROOT_PATH.'comments.php',
    'F_KEYWORD'=>@$_GET['keyword'],
    'F_AUTHOR'=>@$_GET['author'],

    'U_HOME' => make_index_url(),
    )
  );

// +-----------------------------------------------------------------------+
// |                          form construction                            |
// +-----------------------------------------------------------------------+

// Search in a particular category
$blockname = 'category';

$template->assign_block_vars(
  $blockname,
  array('SELECTED' => '',
        'VALUE'=> 0,
        'OPTION' => '------------'
    ));

$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE;
if ($user['forbidden_categories'] != '')
{
  $query.= '
    WHERE id NOT IN ('.$user['forbidden_categories'].')';
}
$query.= '
;';
display_select_cat_wrapper($query, array(@$_GET['cat']), $blockname, true);

// Filter on recent comments...
$blockname = 'since_option';

foreach ($since_options as $id => $option)
{
  $selected = ($id == $page['since']) ? 'selected="selected"' : '';

  $template->assign_block_vars(
    $blockname,
    array('SELECTED' => $selected,
          'VALUE'=> $id,
          'CONTENT' => $option['label']
      ));
}

// Sort by
$blockname = 'sort_by_option';

foreach ($sort_by as $key => $value)
{
  $selected = ($key == $page['sort_by']) ? 'selected="selected"' : '';

  $template->assign_block_vars(
    $blockname,
    array('SELECTED' => $selected,
          'VALUE'=> $key,
          'CONTENT' => l10n($value)
      ));
}

// Sorting order
$blockname = 'sort_order_option';

foreach (array_keys($sort_order) as $option)
{
  $selected = ($option == $page['sort_order']) ? 'selected="selected"' : '';

  $template->assign_block_vars(
    $blockname,
    array('SELECTED' => $selected,
          'VALUE'=> $option,
          'CONTENT' => l10n($option)
      ));
}

// Number of items
$blockname = 'items_number_option';

foreach ($items_number as $option)
{
  $selected = ($option == $page['items_number']) ? 'selected="selected"' : '';

  $template->assign_block_vars(
    $blockname,
    array('SELECTED' => $selected,
          'VALUE'=> $option,
          'CONTENT' => is_numeric($option) ? $option : l10n($option)
      ));
}

// +-----------------------------------------------------------------------+
// |                            navigation bar                             |
// +-----------------------------------------------------------------------+

if (isset($_GET['start']) and is_numeric($_GET['start']))
{
  $start = $_GET['start'];
}
else
{
  $start = 0;
}

$query = '
SELECT COUNT(DISTINCT(id))
  FROM '.IMAGE_CATEGORY_TABLE.' AS ic
    INNER JOIN '.COMMENTS_TABLE.' AS com
    ON ic.image_id = com.image_id
  WHERE '.$since_options[$page['since']]['clause'].'
    AND '.$page['cat_clause'].'
    AND '.$page['author_clause'].'
    AND '.$page['keyword_clause'].'
    AND '.$page['status_clause'];
if ($user['forbidden_categories'] != '')
{
  $query.= '
    AND category_id NOT IN ('.$user['forbidden_categories'].')';
}
$query.= '
;';
list($counter) = mysql_fetch_row(pwg_query($query));

$url = PHPWG_ROOT_PATH
    .'comments.php'
    .get_query_string_diff(array('start','delete','validate'));

$navbar = create_navigation_bar($url,
                                $counter,
                                $start,
                                $page['items_number'],
                                '');

$template->assign_vars(array('NAVBAR' => $navbar));

// +-----------------------------------------------------------------------+
// |                        last comments display                          |
// +-----------------------------------------------------------------------+

$comments = array();
$element_ids = array();
$category_ids = array();

$query = '
SELECT com.id AS comment_id
     , com.image_id
     , ic.category_id
     , com.author
     , com.date
     , com.content
     , com.id AS comment_id
     , com.validated
  FROM '.IMAGE_CATEGORY_TABLE.' AS ic
    INNER JOIN '.COMMENTS_TABLE.' AS com
    ON ic.image_id = com.image_id
  WHERE '.$since_options[$page['since']]['clause'].'
    AND '.$page['cat_clause'].'
    AND '.$page['author_clause'].'
    AND '.$page['keyword_clause'].'
    AND '.$page['status_clause'];
if ($user['forbidden_categories'] != '')
{
  $query.= '
    AND category_id NOT IN ('.$user['forbidden_categories'].')';
}
$query.= '
  GROUP BY comment_id
  ORDER BY '.$page['sort_by'].' '.$page['sort_order'];
if ('all' != $page['items_number'])
{
  $query.= '
  LIMIT '.$start.','.$page['items_number'];
}
$query.= '
;';
$result = pwg_query($query);
while ($row = mysql_fetch_assoc($result))
{
  array_push($comments, $row);
  array_push($element_ids, $row['image_id']);
  array_push($category_ids, $row['category_id']);
}

if (count($comments) > 0)
{
  // retrieving element informations
  $elements = array();
  $query = '
SELECT id, name, file, path, tn_ext
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $element_ids).')
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_assoc($result))
  {
    $elements[$row['id']] = $row;
  }

  // retrieving category informations
  $categories = array();
  $query = '
SELECT id, name, uppercats
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',', $category_ids).')
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_assoc($result))
  {
    $categories[$row['id']] = $row;
  }

  foreach ($comments as $comment)
  {
    if (!empty($elements[$comment['image_id']]['name']))
    {
      $name=$elements[$comment['image_id']]['name'];
    }
    else
    {
      $name=get_name_from_file($elements[$comment['image_id']]['file']);
    }

    // source of the thumbnail picture
    $thumbnail_src = get_thumbnail_url( $elements[$comment['image_id']] );

    // link to the full size picture
    $url = make_picture_url(
            array(
              'category' => $comment['category_id'],
              'cat_name' => $categories[ $comment['category_id']] ['name'],
              'image_id' => $comment['image_id'],
              'image_file' => $elements[$comment['image_id']]['file'],
            )
          );

    $author = $comment['author'];
    if (empty($comment['author']))
    {
      $author = l10n('guest');
    }

    $template->assign_block_vars(
      'comment',
      array(
        'U_PICTURE' => $url,
        'TN_SRC' => $thumbnail_src,
        'ALT' => $name,
        'AUTHOR' => $author,
        'DATE'=>format_date($comment['date'],'mysql_datetime',true),
        'CONTENT'=>trigger_event('render_comment_content',$comment['content']),
        ));

    if ( is_admin() )
    {
      $url = get_root_url().'comments.php'.get_query_string_diff(array('delete','validate'));
      $template->assign_block_vars(
        'comment.action_delete',
        array(
          'U_DELETE' => add_url_params($url,
                          array('delete'=>$comment['comment_id'])
                         ),
          ));
      if ($comment['validated'] != 'true')
      {
        $template->assign_block_vars(
          'comment.action_validate',
          array(
            'U_VALIDATE' => add_url_params($url,
                            array('validate'=>$comment['comment_id'])
                           ),
            ));
      }
    }
  }
}
// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+
$template->assign_block_vars('title',array());
$template->parse('comments');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
