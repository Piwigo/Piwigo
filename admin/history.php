<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2016 Piwigo Team                  http://piwigo.org |
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

$types = array_merge(array('none'), get_enums(HISTORY_TABLE, 'image_type'));

$display_thumbnails = array('no_display_thumbnail' => l10n('No display'),
                            'display_thumbnail_classic' => l10n('Classic display'),
                            'display_thumbnail_hoverbox' => l10n('Hoverbox display')
  );

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
  if (!empty($_POST['start']))
  {
    check_input_parameter('start', $_POST, false, '/^\d{4}-\d{2}-\d{2}$/');
    $search['fields']['date-after'] = $_POST['start'];
  }

  if (!empty($_POST['end']))
  {
    check_input_parameter('end', $_POST, false, '/^\d{4}-\d{2}-\d{2}$/');
    $search['fields']['date-before'] = $_POST['end'];
  }

  if (empty($_POST['types']))
  {
    $search['fields']['types'] = $types;
  }
  else
  {
    check_input_parameter('types', $_POST, true, '/^('.implode('|', $types).')$/');
    $search['fields']['types'] = $_POST['types'];
  }

  $search['fields']['user'] = intval($_POST['user']);

  if (!empty($_POST['image_id']))
  {
    $search['fields']['image_id'] = intval($_POST['image_id']);
  }

  if (!empty($_POST['filename']))
  {
    $search['fields']['filename'] = str_replace(
      '*',
      '%',
      pwg_db_real_escape_string($_POST['filename'])
      );
  }

  if (!empty($_POST['ip']))
  {
    $search['fields']['ip'] = str_replace(
      '*',
      '%',
      pwg_db_real_escape_string($_POST['ip'])
      );
  }

  check_input_parameter('display_thumbnail', $_POST, false, '/^('.implode('|', array_keys($display_thumbnails)).')$/');
  
  $search['fields']['display_thumbnail'] = $_POST['display_thumbnail'];
  // Display choise are also save to one cookie
  if (!empty($_POST['display_thumbnail'])
      and isset($display_thumbnails[$_POST['display_thumbnail']]))
  {
    $cookie_val = $_POST['display_thumbnail'];
  }
  else
  {
    $cookie_val = null;
  }

  pwg_set_cookie_var('display_thumbnail', $cookie_val, strtotime('+1 month') );

  // TODO manage inconsistency of having $_POST['image_id'] and
  // $_POST['filename'] simultaneously

  if (!empty($search))
  {
    // register search rules in database, then they will be available on
    // thumbnails page and picture page.
    $query ='
INSERT INTO '.SEARCH_TABLE.'
  (rules)
  VALUES
  (\''.pwg_db_real_escape_string(serialize($search)).'\')
;';

    pwg_query($query);

    $search_id = pwg_db_insert_id(SEARCH_TABLE);

    redirect(
      PHPWG_ROOT_PATH.'admin.php?page=history&search_id='.$search_id
      );
  }
  else
  {
    $page['errors'][] = l10n('Empty query. No criteria has been entered.');
  }
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filename('history', 'history.tpl');

// TabSheet initialization
history_tabsheet();

$template->assign(
  array(
    'U_HELP' => get_root_url().'admin/popuphelp.php?page=history',
    'F_ACTION' => get_root_url().'admin.php?page=history'
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
  list($serialized_rules) = pwg_db_fetch_row(pwg_query($query));

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

    $search_id = pwg_db_insert_id(SEARCH_TABLE);

    redirect(
      PHPWG_ROOT_PATH.'admin.php?page=history&search_id='.$search_id
      );
  }

  /*TODO - no need to get a huge number of rows from db (should take only what needed for display + SQL_CALC_FOUND_ROWS*/
  $data = trigger_change('get_history', array(), $page['search'], $types);
  usort($data, 'history_compare');

  $page['nb_lines'] = count($data);

  $history_lines = array();
  $user_ids = array();
  $username_of = array();
  $category_ids = array();
  $image_ids = array();
  $has_tags = false;

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
      $has_tags = true;
    }

    $history_lines[] = $row;
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
    while ($row = pwg_db_fetch_assoc($result))
    {
      $username_of[$row['id']] = stripslashes($row['username']);
    }
  }

  if (count($category_ids) > 0)
  {
    $query = '
SELECT id, uppercats
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',', array_keys($category_ids)).')
;';
    $uppercats_of = query2array($query, 'id', 'uppercats');

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
    file,
    path,
    representative_ext
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', array_keys($image_ids)).')
;';
    $image_infos = query2array($query, 'id');
  }

  if ($has_tags > 0)
  {
    $query = '
SELECT
    id,
    name, url_name
  FROM '.TAGS_TABLE;

    global $name_of_tag; // used for preg_replace
    $name_of_tag = array();
    $result = pwg_query($query);
    while ($row=pwg_db_fetch_assoc($result))
    {
      $name_of_tag[ $row['id'] ] = '<a href="'.make_index_url( array('tags'=>array($row))).'">'.trigger_change("render_tag_name", $row['name'], $row).'</a>';
    }
  }

  $i = 0;
  $first_line = $page['start'] + 1;
  $last_line = $page['start'] + $conf['nb_logs_page'];

  $summary['total_filesize'] = 0;
  $summary['guests_IP'] = array();

  foreach ($history_lines as $line)
  {
    if (isset($line['image_type']) and $line['image_type'] == 'high')
    {
      $summary['total_filesize'] += @intval($image_infos[$line['image_id']]['filesize']);
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
      $tags_string = preg_replace_callback(
        '/(\d+)/',
        create_function('$m', 'global $name_of_tag; return isset($name_of_tag[$m[1]]) ? $name_of_tag[$m[1]] : $m[1];'),
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

      if (isset($image_infos[$line['image_id']]))
      {
        $element = array(
          'id' => $line['image_id'],
          'file' => $image_infos[$line['image_id']]['file'],
          'path' => $image_infos[$line['image_id']]['path'],
          'representative_ext' => $image_infos[$line['image_id']]['representative_ext'],
          );
        $thumbnail_display = $page['search']['fields']['display_thumbnail'];
      }
      else
      {
        $thumbnail_display = 'no_display_thumbnail';
      }

      $image_title = '('.$line['image_id'].')';

      if (isset($image_infos[$line['image_id']]['label']))
      {
        $image_title.= ' '.trigger_change('render_element_description', $image_infos[$line['image_id']]['label']);
      }
      else
      {
        $image_title.= ' unknown filename';
      }

      $image_string = '';

      switch ($thumbnail_display)
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
            .'<span><img src="'.DerivativeImage::thumb_url($element)
            .'" alt="'.$image_title.'" title="'.$image_title.'">'
            .'</span></a>';
          break;
        }
        case 'display_thumbnail_hoverbox':
        {
          $image_string =
            '<a class="over" href="'.$picture_url.'">'
            .'<span><img src="'.DerivativeImage::thumb_url($element)
            .'" alt="'.$image_title.'" title="'.$image_title.'">'
            .'</span>'.$image_title.'</a>';
          break;
        }
      }
    }

    $template->append(
      'search_results',
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
    $member_string.= get_root_url().'admin.php?page=history';
    $member_string.= '&amp;search_id='.$page['search_id'];
    $member_string.= '&amp;user_id='.$user_id;
    $member_string.= '">+</a>';

    $member_strings[] = $member_string;
  }

  $template->assign(
    'search_summary',
    array(
      'NB_LINES' => l10n_dec(
        '%d line filtered', '%d lines filtered',
        $page['nb_lines']
        ),
      'FILESIZE' => $summary['total_filesize'] != 0 ? ceil($summary['total_filesize']/1024).' MB' : '',
      'USERS' => l10n_dec(
        '%d user', '%d users',
        $summary['nb_members'] + $summary['nb_guests']
        ),
      'MEMBERS' => sprintf(
        l10n_dec('%d member', '%d members', $summary['nb_members']).': %s',
        implode(', ', $member_strings)
        ),
      'GUESTS' => l10n_dec(
        '%d guest', '%d guests',
        $summary['nb_guests']
        ),
      )
    );

  unset($name_of_tag);
}

// +-----------------------------------------------------------------------+
// |                            navigation bar                             |
// +-----------------------------------------------------------------------+

if (isset($page['search_id']))
{
  $navbar = create_navigation_bar(
    get_root_url().'admin.php'.get_query_string_diff(array('start')),
    $page['nb_lines'],
    $page['start'],
    $conf['nb_logs_page']
    );

  $template->assign('navbar', $navbar);
}

// +-----------------------------------------------------------------------+
// |                             filter form                               |
// +-----------------------------------------------------------------------+

$form = array();

if (isset($page['search']))
{
  if (isset($page['search']['fields']['date-after']))
  {
    $form['start'] = $page['search']['fields']['date-after'];
  }

  if (isset($page['search']['fields']['date-before']))
  {
    $form['end'] = $page['search']['fields']['date-before'];
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
  $form['ip'] = @$page['search']['fields']['ip'];

  $form['display_thumbnail'] = @$page['search']['fields']['display_thumbnail'];
}
else
{
  // by default, at page load, we want the selected date to be the current
  // date
  $form['start'] = $form['end'] = date('Y-m-d');
  $form['types'] = $types;
  // Hoverbox by default
  $form['display_thumbnail'] =
    pwg_get_cookie_var('display_thumbnail', 'no_display_thumbnail');
}


$template->assign(
  array(
    'IMAGE_ID' => @$form['image_id'],
    'FILENAME' => @$form['filename'],
    'IP' => @$form['ip'],
    'START' => @$form['start'],
    'END' => @$form['end'],
    )
  );

$template->assign(
    array(
      'type_option_values' => $types,
      'type_option_selected' => $form['types']
    )
  );


$query = '
SELECT
    '.$conf['user_fields']['id'].' AS id,
    '.$conf['user_fields']['username'].' AS username
  FROM '.USERS_TABLE.'
  ORDER BY username ASC
;';
$template->assign(
  array(
    'user_options' => query2array($query, 'id','username'),
    'user_options_selected' => array(@$form['user'])
  )
);

$template->assign('display_thumbnails', $display_thumbnails);
$template->assign('display_thumbnail_selected', $form['display_thumbnail']);

// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'history');
?>
