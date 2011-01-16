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

// +-----------------------------------------------------------------------+
// |                           initialization                              |
// +-----------------------------------------------------------------------+
define('PHPWG_ROOT_PATH','./');
include_once(PHPWG_ROOT_PATH.'include/common.inc.php');
include_once(PHPWG_ROOT_PATH.'include/functions_comment.inc.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_GUEST);

$sort_order = array(
  'DESC' => l10n('descending'),
  'ASC'  => l10n('ascending')
  );

// sort_by : database fields proposed for sorting comments list
$sort_by = array(
  'date' => l10n('comment date'),
  'image_id' => l10n('photo')
  );

// items_number : list of number of items to display per page
$items_number = array(5,10,20,50,'all');

// since when display comments ?
//
$since_options = array(
  1 => array('label' => l10n('today'),
             'clause' => 'date > '.pwg_db_get_recent_period_expression(1)),
  2 => array('label' => sprintf(l10n('last %d days'), 7),
             'clause' => 'date > '.pwg_db_get_recent_period_expression(7)),
  3 => array('label' => sprintf(l10n('last %d days'), 30),
             'clause' => 'date > '.pwg_db_get_recent_period_expression(30)),
  4 => array('label' => l10n('the beginning'),
             'clause' => '1=1') // stupid but generic
  );

if (!empty($_GET['since']) && is_numeric($_GET['since']))
{
  $page['since'] = $_GET['since'];
}
else
{
  $page['since'] = 4;
}

// on which field sorting
//
$page['sort_by'] = 'date';
// if the form was submitted, it overloads default behaviour
if (isset($_GET['sort_by']) and isset($sort_by[$_GET['sort_by']]) )
{
  $page['sort_by'] = $_GET['sort_by'];
}

// order to sort
//
$page['sort_order'] = 'DESC';
// if the form was submitted, it overloads default behaviour
if (isset($_GET['sort_order']) and isset($sort_order[$_GET['sort_order']]))
{
  $page['sort_order'] = $_GET['sort_order'];
}

// number of items to display
//
$page['items_number'] = 10;
if (isset($_GET['items_number']))
{
  $page['items_number'] = $_GET['items_number'];
}
if ( !is_numeric($page['items_number']) and $page['items_number']!='all' )
{
  $page['items_number'] = 10;
}

$page['where_clauses'] = array();

// which category to filter on ?
if (isset($_GET['cat']) and 0 != $_GET['cat'])
{
  check_input_parameter('cat', $_GET, false, PATTERN_ID);

  $category_ids = get_subcat_ids(array($_GET['cat']));
  if (empty($category_id))
  {
    $category_ids = array(-1);
  }
  
  $page['where_clauses'][] =
    'category_id IN ('.implode(',', $category_ids).')';
}

// search a particular author
if (!empty($_GET['author']))
{
  $page['where_clauses'][] =
    'u.'.$conf['user_fields']['username'].' = \''.$_GET['author'].'\'
     OR author = \''.$_GET['author'].'\'';
}

// search a specific comment (if you're coming directly from an admin
// notification email)
if (!empty($_GET['comment_id']))
{
  check_input_parameter('comment_id', $_GET, false, PATTERN_ID);

  // currently, the $_GET['comment_id'] is only used by admins from email
  // for management purpose (validate/delete)
  if (!is_admin())
  {
    $login_url =
      get_root_url().'identification.php?redirect='
      .urlencode(urlencode($_SERVER['REQUEST_URI']))
      ;
    redirect($login_url);
  }

  $page['where_clauses'][] = 'com.id = '.$_GET['comment_id'];
}

// search a substring among comments content
if (!empty($_GET['keyword']))
{
  $page['where_clauses'][] =
    '('.
    implode(' AND ',
            array_map(
              create_function(
                '$s',
                'return "content LIKE \'%$s%\'";'
                ),
              preg_split('/[\s,;]+/', $_GET['keyword'] )
              )
      ).
    ')';
}

$page['where_clauses'][] = $since_options[$page['since']]['clause'];

// which status to filter on ?
if ( !is_admin() )
{
  $page['where_clauses'][] = 'validated=\'true\'';
}

$page['where_clauses'][] = get_sql_condition_FandF
  (
    array
      (
        'forbidden_categories' => 'category_id',
        'visible_categories' => 'category_id',
        'visible_images' => 'ic.image_id'
      ),
    '', true
  );

// +-----------------------------------------------------------------------+
// |                         comments management                           |
// +-----------------------------------------------------------------------+

$comment_id = null;
$action = null;

$actions = array('delete', 'validate', 'edit');
foreach ($actions as $loop_action)
{
  if (isset($_GET[$loop_action]))
  {
    $action = $loop_action;
    check_input_parameter($action, $_GET, false, PATTERN_ID);
    $comment_id = $_GET[$action];
    break;
  }
}

if (isset($action))
{
  check_pwg_token();

  $comment_author_id = get_comment_author_id($comment_id);

  if (can_manage_comment($action, $comment_author_id))
  {
    $perform_redirect = false;

    if ('delete' == $action)
    {
      delete_user_comment($comment_id);
      $perform_redirect = true;
    }

    if ('validate' == $action)
    {
      validate_user_comment($comment_id);
      $perform_redirect = true;
    }

    if ('edit' == $action)
    {
      if (!empty($_POST['content']))
      {
        update_user_comment(
          array(
            'comment_id' => $_GET['edit'],
            'image_id' => $_POST['image_id'],
            'content' => $_POST['content']
            ),
          $_POST['key']
          );

        $edit_comment = null;
      }
      else
      {
        $edit_comment = $_GET['edit'];
      }
    }

    if ($perform_redirect)
    {
      $redirect_url =
        PHPWG_ROOT_PATH
        .'comments.php'
        .get_query_string_diff(array('delete','validate','pwg_token'));

      redirect($redirect_url);
    }
  }
}

// +-----------------------------------------------------------------------+
// |                       page header and options                         |
// +-----------------------------------------------------------------------+

$title= l10n('User comments');
$page['body_id'] = 'theCommentsPage';

$template->set_filenames(array('comments'=>'comments.tpl'));
$template->assign(
  array(
    'F_ACTION'=>PHPWG_ROOT_PATH.'comments.php',
    'F_KEYWORD'=> @htmlspecialchars(stripslashes($_GET['keyword'], ENT_QUOTES, 'utf-8')),
    'F_AUTHOR'=> @htmlspecialchars(stripslashes($_GET['author'], ENT_QUOTES, 'utf-8')),
    )
  );

// +-----------------------------------------------------------------------+
// |                          form construction                            |
// +-----------------------------------------------------------------------+

// Search in a particular category
$blockname = 'categories';

$query = '
SELECT id, name, uppercats, global_rank
  FROM '.CATEGORIES_TABLE.'
'.get_sql_condition_FandF
  (
    array
      (
        'forbidden_categories' => 'id',
        'visible_categories' => 'id'
      ),
    'WHERE'
  ).'
;';
display_select_cat_wrapper($query, array(@$_GET['cat']), $blockname, true);

// Filter on recent comments...
$tpl_var=array();
foreach ($since_options as $id => $option)
{
  $tpl_var[ $id ] = $option['label'];
}
$template->assign( 'since_options', $tpl_var);
$template->assign( 'since_options_selected', $page['since']);

// Sort by
$template->assign( 'sort_by_options', $sort_by);
$template->assign( 'sort_by_options_selected', $page['sort_by']);

// Sorting order
$template->assign( 'sort_order_options', $sort_order);
$template->assign( 'sort_order_options_selected', $page['sort_order']);


// Number of items
$blockname = 'items_number_option';
$tpl_var=array();
foreach ($items_number as $option)
{
  $tpl_var[ $option ] = is_numeric($option) ? $option : l10n($option);
}
$template->assign( 'item_number_options', $tpl_var);
$template->assign( 'item_number_options_selected', $page['items_number']);


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
SELECT COUNT(DISTINCT(com.id))
  FROM '.IMAGE_CATEGORY_TABLE.' AS ic
    INNER JOIN '.COMMENTS_TABLE.' AS com
    ON ic.image_id = com.image_id
    LEFT JOIN '.USERS_TABLE.' As u
    ON u.'.$conf['user_fields']['id'].' = com.author_id
  WHERE '.implode('
    AND ', $page['where_clauses']).'
;';
list($counter) = pwg_db_fetch_row(pwg_query($query));

$url = PHPWG_ROOT_PATH
    .'comments.php'
  .get_query_string_diff(array('start','delete','validate','pwg_token'));

$navbar = create_navigation_bar($url,
                                $counter,
                                $start,
                                $page['items_number'],
                                '');

$template->assign('navbar', $navbar);

// +-----------------------------------------------------------------------+
// |                        last comments display                          |
// +-----------------------------------------------------------------------+

$comments = array();
$element_ids = array();
$category_ids = array();

$query = '
SELECT com.id AS comment_id,
       com.image_id,
       com.author,
       com.author_id,
       com.date,
       com.content,
       com.validated
  FROM '.IMAGE_CATEGORY_TABLE.' AS ic
    INNER JOIN '.COMMENTS_TABLE.' AS com
    ON ic.image_id = com.image_id
    LEFT JOIN '.USERS_TABLE.' As u
    ON u.'.$conf['user_fields']['id'].' = com.author_id
  WHERE '.implode('
    AND ', $page['where_clauses']).'
  GROUP BY comment_id,
       com.image_id,
       com.author,
       com.author_id,
       com.date,
       com.content,
       com.validated
  ORDER BY '.$page['sort_by'].' '.$page['sort_order'];
if ('all' != $page['items_number'])
{
  $query.= '
  LIMIT '.$page['items_number'].' OFFSET '.$start;
}
$query.= '
;';
$result = pwg_query($query);
while ($row = pwg_db_fetch_assoc($result))
{
  array_push($comments, $row);
  array_push($element_ids, $row['image_id']);
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
  while ($row = pwg_db_fetch_assoc($result))
  {
    $elements[$row['id']] = $row;
  }

  // retrieving category informations
  $query = '
SELECT c.id, name, permalink, uppercats, com.id as comment_id
  FROM '.CATEGORIES_TABLE.' AS c
  LEFT JOIN '.IMAGE_CATEGORY_TABLE.' AS ic
  ON c.id=ic.category_id
  LEFT JOIN '.COMMENTS_TABLE.' AS com
  ON ic.image_id=com.image_id
  '.get_sql_condition_FandF
    (
      array
      (
	'forbidden_categories' => 'c.id',
	'visible_categories' => 'c.id'
       ),
      'WHERE'
     ).'
;';
  $categories = hash_from_query($query, 'comment_id');

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
        'category' => $categories[ $comment['comment_id'] ],
        'image_id' => $comment['image_id'],
        'image_file' => $elements[$comment['image_id']]['file'],
        )
      );

    $tpl_comment = array(
      'U_PICTURE' => $url,
      'TN_SRC' => $thumbnail_src,
      'ALT' => $name,
      'AUTHOR' => trigger_event('render_comment_author', $comment['author']),
      'DATE'=>format_date($comment['date'], true),
      'CONTENT'=>trigger_event('render_comment_content',$comment['content']),
      );

    if (can_manage_comment('delete', $comment['author_id']))
    {
      $url =
        get_root_url()
        .'comments.php'
        .get_query_string_diff(array('delete','validate','edit', 'pwg_token'));

      $tpl_comment['U_DELETE'] = add_url_params(
        $url,
        array(
          'delete' => $comment['comment_id'],
          'pwg_token' => get_pwg_token(),
          )
        );
    }

    if (can_manage_comment('edit', $comment['author_id']))
    {
      $url =
        get_root_url()
        .'comments.php'
	.get_query_string_diff(array('edit', 'delete','validate', 'pwg_token'));

      $tpl_comment['U_EDIT'] = add_url_params(
        $url,
        array(
          'edit' => $comment['comment_id'],
          'pwg_token' => get_pwg_token(),
          )
        );

      if (isset($edit_comment) and ($comment['comment_id'] == $edit_comment))
      {
	$tpl_comment['IN_EDIT'] = true;
	$key = get_ephemeral_key(2, $comment['image_id']);
	$tpl_comment['KEY'] = $key;
	$tpl_comment['IMAGE_ID'] = $comment['image_id'];
	$tpl_comment['CONTENT'] = $comment['content'];
      }
    }

    if (can_manage_comment('validate', $comment['author_id']))
    {
      if ('true' != $comment['validated'])
      {
        $tpl_comment['U_VALIDATE'] = add_url_params(
          $url,
          array(
            'validate'=> $comment['comment_id'],
            'pwg_token' => get_pwg_token(),
            )
          );
      }
    }
    $template->append('comments', $tpl_comment);
  }
}
// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+
include(PHPWG_ROOT_PATH.'include/page_header.php');
$template->pparse('comments');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>