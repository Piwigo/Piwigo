<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
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

/**
 * Display filtered history lines
 */

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
include_once(PHPWG_ROOT_PATH.'admin/include/functions_history.inc.php');

if (isset($_GET['start']) and is_numeric($_GET['start']))
{
  $page['start'] = $_GET['start'];
}
else
{
  $page['start'] = 0;
}

$types = array('none', 'picture', 'high', 'other');
$display_thumbnails = array('no_display_thumbnail', 'display_thumbnail_classic', 'display_thumbnail_hoverbox');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// | Build search criteria and redirect to results                         |
// +-----------------------------------------------------------------------+

$page['errors'] = array();
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

  if (empty($_POST['types']))
  {
    $search['fields']['types'] = $types;
  }
  else
  {
    $search['fields']['types'] = $_POST['types'];
  }

  $search['fields']['user'] = $_POST['user'];

  if (!empty($_POST['image_id']))
  {
    $search['fields']['image_id'] = intval($_POST['image_id']);
  }
  
  if (!empty($_POST['filename']))
  {
    $search['fields']['filename'] = str_replace(
      '*',
      '%',
      mysql_escape_string($_POST['filename'])
      );
  }

  $search['fields']['display_thumbnail'] = $_POST['display_thumbnail'];
  // Display choise are also save to one cookie
  pwg_set_cookie_var('history_display_thumbnail', $_POST['display_thumbnail']);

  // TODO manage inconsistency of having $_POST['image_id'] and
  // $_POST['filename'] simultaneously
  
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
    array_push($page['errors'], $lang['search_one_clause_at_least']);
  }
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filename('history', 'admin/history.tpl');

// TabSheet initialization
history_tabsheet();

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

  if (isset($_GET['user_id']))
  {
    if (!is_numeric($_GET['user_id']))
    {
      die('user_id GET parameter must be an integer value');
    }

    $page['search']['fields']['user'] = $_GET['user_id'];

    $query ='
INSERT INTO '.SEARCH_TABLE.'
  (rules)
  VALUES
  (\''.serialize($page['search']).'\')
;';
    pwg_query($query);

    $search_id = mysql_insert_id();
    
    redirect(
      PHPWG_ROOT_PATH.'admin.php?page=history&search_id='.$search_id
      );
  }

  $data = trigger_event('get_history', array(), $page['search'], $types);
  usort($data, 'history_compare');

  $page['nb_lines'] = count($data);

  $history_lines = array();
  $user_ids = array();
  $username_of = array();
  $category_ids = array();
  $image_ids = array();
  $tag_ids = array();

  foreach ($data as $row)
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

    if (isset($row['tag_ids']))
    {
      foreach (explode(',', $row['tag_ids']) as $tag_id)
      {
        array_push($tag_ids, $tag_id);
      }
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
SELECT
    id,
    IF(name IS NULL, file, name) AS label,
    filesize,
    high_filesize,
    file,
    path,
    tn_ext
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', array_keys($image_ids)).')
;';
    // $label_of_image = simple_hash_from_query($query, 'id', 'label');
    $label_of_image = array();
    $filesize_of_image = array();
    $high_filesize_of_image = array();
    $file_of_image = array();
    $path_of_image = array();
    $tn_ext_of_image = array();

    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      $label_of_image[ $row['id'] ] = $row['label'];

      if (isset($row['filesize']))
      {
        $filesize_of_image[ $row['id'] ] = $row['filesize'];
      }

      if (isset($row['high_filesize']))
      {
        $high_filesize_of_image[ $row['id'] ] = $row['high_filesize'];
      }

      $file_of_image[ $row['id'] ] = $row['file'];
      $path_of_image[ $row['id'] ] = $row['path'];
      $tn_ext_of_image[ $row['id'] ] = $row['tn_ext'];
    }

    // echo '<pre>'; print_r($high_filesize_of_image); echo '</pre>';
  }

  if (count($tag_ids) > 0)
  {
    $tag_ids = array_unique($tag_ids);

    $query = '
SELECT
    id,
    name
  FROM '.TAGS_TABLE.'
  WHERE id IN ('.implode(', ', $tag_ids).')
;';
    $name_of_tag = array();

    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      $name_of_tag[ $row['id'] ] = $row['name'];
    }
  }
  
  $i = 0;
  $first_line = $page['start'] + 1;
  $last_line = $page['start'] + $conf['nb_logs_page'];

  $summary['total_filesize'] = 0;
  $summary['guests_IP'] = array();

  foreach ($history_lines as $line)
  {
    // FIXME when we watch the representative of a non image element, it is
    // the not the representative filesize that is counted (as it is
    // unknown) but the non image element filesize. Proposed solution: add
    // #images.representative_filesize and add 'representative' in the
    // choices of #history.image_type.
    
    if (isset($line['image_type']))
    {
      if ($line['image_type'] == 'high')
      {
        if (isset($high_filesize_of_image[$line['image_id']]))
        {
          $summary['total_filesize']+=
            $high_filesize_of_image[$line['image_id']];
        }
      }
      else
      {
        if (isset($filesize_of_image[$line['image_id']]))
        {
          $summary['total_filesize']+=
            $filesize_of_image[$line['image_id']];
        }
      }
    }

    if ($line['user_id'] == $conf['guest_id'])
    {
      if (!isset($summary['guests_IP'][ $line['IP'] ]))
      {
        $summary['guests_IP'][ $line['IP'] ] = 0;
      }
      
      $summary['guests_IP'][ $line['IP'] ]++;
    }
    
    $i++;
    
    if ($i < $first_line or $i > $last_line)
    {
      continue;
    }

    $user_string = '';
    if (isset($username_of[$line['user_id']]))
    {
      $user_string.= $username_of[$line['user_id']];
    }
    else
    {
      $user_string.= $line['user_id'];
    }
    $user_string.= '&nbsp;<a href="';
    $user_string.= PHPWG_ROOT_PATH.'admin.php?page=history';
    $user_string.= '&amp;search_id='.$page['search_id'];
    $user_string.= '&amp;user_id='.$line['user_id'];
    $user_string.= '">+</a>';

    $tags_string = '';
    if (isset($line['tag_ids']))
    {
      $tags_string = preg_replace(
        '/(\d+)/e',
        '$name_of_tag["$1"]',
        str_replace(
          ',',
          ', ',
          $line['tag_ids']
          )
        );
    }

    $image_string = '';
    if (isset($line['image_id']))
    {
      $picture_url = make_picture_url(
        array(
          'image_id' => $line['image_id'],
          )
        );

      $element = array(
           'id' => $line['image_id'],
           'file' => $file_of_image[$line['image_id']],
           'path' => $path_of_image[$line['image_id']],
           'tn_ext' => $tn_ext_of_image[$line['image_id']],
           );

      $image_title = '('.$line['image_id'].')';

      if (isset($label_of_image[$line['image_id']]))
      {
        $image_title.= ' '.$label_of_image[$line['image_id']];
      }
      else
      {
        $image_title.= ' unknown filename';
      }

      $image_string = '';

      switch ($page['search']['fields']['display_thumbnail'])
      {
        case 'no_display_thumbnail':
        {
          $image_string= '<a href="'.$picture_url.'">'.$image_title.'</a>';
          break;
        }
        case 'display_thumbnail_classic':
        {
          $image_string =
            '<a class="thumbnail" href="'.$picture_url.'">'
            .'<span><img src="'.get_thumbnail_url($element)
            .'" alt="'.$image_title.'" title="'.$image_title.'">'
            .'</span></a>';
          break;
        }
        case 'display_thumbnail_hoverbox':
        {
          $image_string =
            '<a class="over" href="'.$picture_url.'">'
            .'<span><img src="'.get_thumbnail_url($element)
            .'" alt="'.$image_title.'" title="'.$image_title.'">'
            .'</span>'.$image_title.'</a>';
          break;
        }
      }
    }
    
    $template->assign_block_vars(
      'detail',
      array(
        'DATE'      => $line['date'],
        'TIME'      => $line['time'],
        'USER'      => $user_string,
        'IP'        => $line['IP'],
        'IMAGE'     => $image_string,
        'TYPE'      => $line['image_type'],
        'SECTION'   => $line['section'],
        'CATEGORY'  => isset($line['category_id'])
          ? ( isset($name_of_category[$line['category_id']])
                ? $name_of_category[$line['category_id']]
                : 'deleted '.$line['category_id'] )
          : '',
        'TAGS'       => $tags_string,
        'T_CLASS'   => ($i % 2) ? 'row1' : 'row2',
        )
      );
  }

  $summary['nb_guests'] = 0;
  if (count(array_keys($summary['guests_IP'])) > 0)
  {
    $summary['nb_guests'] = count(array_keys($summary['guests_IP']));

    // we delete the "guest" from the $username_of hash so that it is
    // avoided in next steps
    unset($username_of[ $conf['guest_id'] ]);
  }
  
  $summary['nb_members'] = count($username_of);

  $member_strings = array();
  foreach ($username_of as $user_id => $user_name)
  {
    $member_string = $user_name.'&nbsp;<a href="';
    $member_string.= PHPWG_ROOT_PATH.'admin.php?page=history';
    $member_string.= '&amp;search_id='.$page['search_id'];
    $member_string.= '&amp;user_id='.$user_id;
    $member_string.= '">+</a>';

    $member_strings[] = $member_string;
  }
  
  $template->assign_block_vars(
    'summary',
    array(
      'NB_LINES' => l10n_dec(
        '%d line filtered', '%d lines filtered',
        $page['nb_lines']
        ),
      'FILESIZE' => $summary['total_filesize'].' KB',
      'USERS' => l10n_dec(
        '%d user', '%d users',
        $summary['nb_members'] + $summary['nb_guests']
        ),
      'MEMBERS' => sprintf(
        l10n_dec('%d member', '%d members', $summary['nb_members']).': %s',
        implode(
          ', ',
          $member_strings
          )
        ),
      'GUESTS' => l10n_dec(
        '%d guest', '%d guests',
        $summary['nb_guests']
        ),
      )
    );
}

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

    $form['end_year']  = (int)$tokens[0];
    $form['end_month'] = (int)$tokens[1];
    $form['end_day']   = (int)$tokens[2];
  }

  $form['types'] = $page['search']['fields']['types'];

  if (isset($page['search']['fields']['user']))
  {
    $form['user'] = $page['search']['fields']['user'];
  }
  else
  {
    $form['user'] = null;
  }

  $form['image_id'] = @$page['search']['fields']['image_id'];
  $form['filename'] = @$page['search']['fields']['filename'];

  $form['display_thumbnail'] = @$page['search']['fields']['display_thumbnail'];
}
else
{
  // by default, at page load, we want the selected date to be the current
  // date
  $form['start_year']  = $form['end_year']  = date('Y');
  $form['start_month'] = $form['end_month'] = date('n');
  $form['start_day']   = $form['end_day']   = date('j');
  $form['types'] = $types;
  // Hoverbox by default
  $form['display_thumbnail'] =
    pwg_get_cookie_var('history_display_thumbnail', $display_thumbnails[2]);
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
    'IMAGE_ID' => @$form['image_id'],
    'FILENAME' => @$form['filename'],
    )
  );

foreach ($types as $option)
{
  $selected = '';
  
  if (in_array($option, $form['types']))
  {
    $selected = 'selected="selected"';
  }
  
  $template->assign_block_vars(
    'types_option',
    array(
      'VALUE' => $option,
      'CONTENT' => l10n($option),
      'SELECTED' => $selected,
      )
    );
}

$template->assign_block_vars(
  'user_option',
  array(
    'VALUE'=> -1,
    'CONTENT' => '------------',
    'SELECTED' => ''
    )
  );

$query = '
SELECT
    '.$conf['user_fields']['id'].' AS id,
    '.$conf['user_fields']['username'].' AS username
  FROM '.USERS_TABLE.'
  ORDER BY username ASC
;';
$result = pwg_query($query);

while ($row = mysql_fetch_array($result))
{
  $selected = '';

  if (isset($form['user'])
      and $row['id'] == $form['user'])
  {
    $selected = 'selected="selected"';
  }
  
  $template->assign_block_vars(
    'user_option',
    array(
      'VALUE' => $row['id'],
      'CONTENT' => $row['username'],
      'SELECTED' => $selected,
      )
    );
}

foreach ($display_thumbnails as $display_thumbnail)
{
  $selected = '';
  
  if ($display_thumbnail === $form['display_thumbnail'])
  {
    $selected = 'selected="selected"';
  }
  
  $template->assign_block_vars(
    'display_thumbnail',
    array(
      'VALUE' => $display_thumbnail,
      'CONTENT' => l10n($display_thumbnail),
      'SELECTED' => $selected,
      )
    );
}

// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'history');
?>
