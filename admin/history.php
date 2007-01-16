<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2006-11-29 05:18:11 +0100 (mer, 29 nov 2006) $
// | last modifier : $Author: rvelices $
// | revision      : $Revision: 1620 $
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
 * Display filtered history lines
 */

// echo '<pre>$_POST:
// '; print_r($_POST); echo '</pre>';
// echo '<pre>$_GET:
// '; print_r($_GET); echo '</pre>';

// +-----------------------------------------------------------------------+
// |                              functions                                |
// +-----------------------------------------------------------------------+

// +-----------------------------------------------------------------------+
// |                           initialization                              |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

if (isset($_GET['start']) and is_numeric($_GET['start']))
{
  $page['start'] = $_GET['start'];
}
else
{
  $page['start'] = 0;
}

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// | Build search criteria and redirect to results                         |
// +-----------------------------------------------------------------------+

$errors = array();
$search = array();

if (isset($_POST['submit']))
{
  // dates
  if (!empty($_POST['start_year']))
  {
    $search['fields']['date-after'] = sprintf(
      '%d-%02d-%02d',
      $_POST['start_year'],
      $_POST['start_month'],
      $_POST['start_day']
      );
  }

  if (!empty($_POST['end_year']))
  {
    $search['fields']['date-before'] = sprintf(
      '%d-%02d-%02d',
      $_POST['end_year'],
      $_POST['end_month'],
      $_POST['end_day']
      );
  }

  // echo '<pre>'; print_r($search); echo '</pre>';
  
  if (!empty($search))
  {
    // register search rules in database, then they will be available on
    // thumbnails page and picture page.
    $query ='
INSERT INTO '.SEARCH_TABLE.'
  (rules)
  VALUES
  (\''.serialize($search).'\')
;';
    pwg_query($query);

    $search_id = mysql_insert_id();
    
    redirect(
      PHPWG_ROOT_PATH.'admin.php?page=history&search_id='.$search_id
      );
  }
  else
  {
    array_push($errors, $lang['search_one_clause_at_least']);
  }
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(array('history'=>'admin/history.tpl'));

$base_url = PHPWG_ROOT_PATH.'admin.php?page=history';

$template->assign_vars(
  array(
    'U_HELP' => PHPWG_ROOT_PATH.'popuphelp.php?page=history',

    'F_ACTION' => PHPWG_ROOT_PATH.'admin.php?page=history'
    )
  );

$template->assign_vars(
  array(
    'TODAY_DAY'   => date('d', time()),
    'TODAY_MONTH' => date('m', time()),
    'TODAY_YEAR'  => date('Y', time()),
    )
  );

// +-----------------------------------------------------------------------+
// |                             history lines                             |
// +-----------------------------------------------------------------------+

if (isset($_GET['search_id'])
    and $page['search_id'] = (int)$_GET['search_id'])
{
  // what are the lines to display in reality ?
  $query = '
SELECT rules
  FROM '.SEARCH_TABLE.'
  WHERE id = '.$page['search_id'].'
;';
  list($serialized_rules) = mysql_fetch_row(pwg_query($query));

  $page['search'] = unserialize($serialized_rules);

  // echo '<pre>'; print_r($page['search']); echo '</pre>';
  
  $clauses = array();

  if (isset($page['search']['fields']['date-after']))
  {
    array_push(
      $clauses,
      "date >= '".$page['search']['fields']['date-after']."'"
      );
  }

  if (isset($page['search']['fields']['date-before']))
  {
    array_push(
      $clauses,
      "date <= '".$page['search']['fields']['date-before']."'"
      );
  }

  $clauses = prepend_append_array_items($clauses, '(', ')');

  $where_separator =
    implode(
      "\n    AND ",
      $clauses
      );
  
  $query = '
SELECT COUNT(*)
  FROM '.HISTORY_TABLE.'
  WHERE '.$where_separator.'
';

  list($page['nb_lines']) = mysql_fetch_row(pwg_query($query));

  $query = '
SELECT date, time, user_id, IP, section, category_id, tag_ids, image_id
  FROM '.HISTORY_TABLE.'
  WHERE '.$where_separator.'
  LIMIT '.$page['start'].', '.$conf['nb_logs_page'].'
;';

  $result = pwg_query($query);
  $history_lines = array();
  while ($row = mysql_fetch_array($result))
  {
    $user_ids[$row['user_id']] = 1;

    if (isset($row['category_id']))
    {
      $category_ids[$row['category_id']] = 1;
    }

    if (isset($row['image_id']))
    {
      $image_ids[$row['image_id']] = 1;
    }

    array_push(
      $history_lines,
      $row
      );
  }

  // prepare reference data (users, tags, categories...)
  if (count($user_ids) > 0)
  {
    $query = '
SELECT '.$conf['user_fields']['id'].' AS id
     , '.$conf['user_fields']['username'].' AS username
  FROM '.USERS_TABLE.'
  WHERE id IN ('.implode(',', array_keys($user_ids)).')
;';
    $result = pwg_query($query);

    $username_of = array();
    while ($row = mysql_fetch_array($result))
    {
      $username_of[$row['id']] = $row['username'];
    }
  }

  if (count($category_ids) > 0)
  {
    $query = '
SELECT id, uppercats
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',', array_keys($category_ids)).')
;';
    $uppercats_of = simple_hash_from_query($query, 'id', 'uppercats');

    $name_of_category = array();
    
    foreach ($uppercats_of as $category_id => $uppercats)
    {
      $name_of_category[$category_id] = get_cat_display_name_cache(
        $uppercats
        );
    }
  }

  if (count($image_ids) > 0)
  {
    $query = '
SELECT id, IF(name IS NULL, file, name) AS label
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', array_keys($image_ids)).')
;';
    $label_of_image = simple_hash_from_query($query, 'id', 'label');
  }
  
  $i = 0;

  foreach ($history_lines as $line)
  {
    $template->assign_block_vars(
      'detail',
      array(
        'DATE'      => $line['date'],
        'TIME'      => $line['time'],
        'USER'      => isset($username_of[$line['user_id']])
          ? $username_of[$line['user_id']]
          : $line['user_id']
        ,
        'IP'        => $line['IP'],
        'IMAGE'     => isset($line['image_id'])
          ? $label_of_image[$line['image_id']]
          : $line['image_id'],
        'SECTION'   => $line['section'],
        'CATEGORY'  => isset($line['category_id'])
          ? $name_of_category[$line['category_id']]
          : '',
        'TAG'       => $line['tag_ids'],
        'T_CLASS'   => ($i++ % 2) ? 'row1' : 'row2',
        )
      );
  }
}

// $groups_string = preg_replace(
//     '/(\d+)/e',
//     "\$groups['$1']",
//     implode(
//       ', ',
//       $local_user['groups']
//       )
//     );

// +-----------------------------------------------------------------------+
// |                            navigation bar                             |
// +-----------------------------------------------------------------------+

if (isset($page['search_id']))
{
  $navbar = create_navigation_bar(
    PHPWG_ROOT_PATH.'admin.php'.get_query_string_diff(array('start')),
    $page['nb_lines'],
    $page['start'],
    $conf['nb_logs_page']
    );

  $template->assign_block_vars(
    'navigation',
    array(
      'NAVBAR' => $navbar
      )
    );
}

// +-----------------------------------------------------------------------+
// |                             filter form                               |
// +-----------------------------------------------------------------------+

$form = array();

if (isset($page['search']))
{
  if (isset($page['search']['fields']['date-after']))
  {
    $tokens = explode('-', $page['search']['fields']['date-after']);
    
    $form['start_year']  = (int)$tokens[0];
    $form['start_month'] = (int)$tokens[1];
    $form['start_day']   = (int)$tokens[2];
  }

  if (isset($page['search']['fields']['date-before']))
  {
    $tokens = explode('-', $page['search']['fields']['date-before']);
    
     (int)$tokens[0];
     (int)$tokens[1];
     (int)$tokens[2];
  }
}
else
{
  // by default, at page load, we want the selected date to be the current
  // date
  $form['start_year']  = $form['end_year']  = date('Y');
  $form['start_month'] = $form['end_month'] = date('n');
  $form['start_day']   = $form['end_day']   = date('j');
}

// start date
get_day_list('start_day', @$form['start_day']);
get_month_list('start_month', @$form['start_month']);
// end date
get_day_list('end_day', @$form['end_day']);
get_month_list('end_month', @$form['end_month']);

$template->assign_vars(
  array(
    'START_YEAR' => @$form['start_year'],
    'END_YEAR'   => @$form['end_year'],
    )
  );
  
// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'history');
?>
