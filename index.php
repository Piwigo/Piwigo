<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

//--------------------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
include(PHPWG_ROOT_PATH.'include/section_init.inc.php');

// Check Access and exit when user status is not ok
check_status(ACCESS_GUEST);


// access authorization check
if (isset($page['category']))
{
  check_restrictions($page['category']['id']);
}
if ($page['start']>0 && $page['start']>=count($page['items']))
{
  page_not_found('', duplicate_index_url(array('start'=>0)));
}

trigger_notify('loc_begin_index');

//---------------------------------------------- change of image display order
if (isset($_GET['image_order']))
{
  if ( (int)$_GET['image_order'] > 0)
  {
    pwg_set_session_var('image_order', (int)$_GET['image_order']);
  }
  else
  {
    pwg_unset_session_var('image_order');
  }
  redirect(
    duplicate_index_url(
      array(),        // nothing to redefine
      array('start')  // changing display order goes back to section first page
      )
    );
}
if (isset($_GET['display']))
{
  $page['meta_robots']['noindex']=1;
  if (array_key_exists($_GET['display'], ImageStdParams::get_defined_type_map()))
  {
    pwg_set_session_var('index_deriv', $_GET['display']);
  }
}

//-------------------------------------------------------------- initialization
// navigation bar
$page['navigation_bar'] = array();
if (count($page['items']) > $page['nb_image_page'])
{
  $page['navigation_bar'] = create_navigation_bar(
    duplicate_index_url(array(), array('start')),
    count($page['items']),
    $page['start'],
    $page['nb_image_page'],
    true, 'start'
    );
}

$template->assign('thumb_navbar', $page['navigation_bar'] );

// caddie filling :-)
if (isset($_GET['caddie']))
{
  fill_caddie($page['items']);
  redirect(duplicate_index_url());
}

if (isset($page['is_homepage']) and $page['is_homepage'])
{
  $canonical_url = get_gallery_home_url();
}
else
{
  $start = $page['nb_image_page'] * round($page['start'] / $page['nb_image_page']);
  if ($start>0 && $start >= count($page['items']) )
  {
    $start -= $page['nb_image_page'];
  }
  $canonical_url = duplicate_index_url(array('start' => $start));
}
$template->assign('U_CANONICAL', $canonical_url);

//-------------------------------------------------------------- page title
$title = $page['title'];
$template_title = $page['section_title'];
$nb_items = count($page['items']);
$template->assign('TITLE', $template_title);
$template->assign('NB_ITEMS', $nb_items);

//-------------------------------------------------------------- menubar
include( PHPWG_ROOT_PATH.'include/menubar.inc.php');

$template->set_filename('index', 'index.tpl');

// +-----------------------------------------------------------------------+
// |  index page (categories, thumbnails, search, calendar, random, etc.)  |
// +-----------------------------------------------------------------------+
if ( empty($page['is_external']) )
{
  //----------------------------------------------------- template initialization
  $page['body_id'] = 'theCategoryPage';

  if (isset($page['flat']) or isset($page['chronology_field']))
  {
    $template->assign(
      'U_MODE_NORMAL',
      duplicate_index_url( array(), array('chronology_field', 'start', 'flat') )
      );
  }

  if ($conf['index_flat_icon'] and !isset($page['flat']) and 'categories' == $page['section'])
  {
    $template->assign(
      'U_MODE_FLAT',
      duplicate_index_url(array('flat' => ''), array('start', 'chronology_field'))
      );
  }

  if (!isset($page['chronology_field']))
  {
    $chronology_params = array(
      'chronology_field' => 'created',
      'chronology_style' => 'monthly',
      'chronology_view' => 'list',
      );
    if ($conf['index_created_date_icon'])
    {
      $template->assign(
        'U_MODE_CREATED',
        duplicate_index_url( $chronology_params, array('start', 'flat') )
        );
    }
    if ($conf['index_posted_date_icon'])
    {
      $chronology_params['chronology_field'] = 'posted';
      $template->assign(
        'U_MODE_POSTED',
        duplicate_index_url( $chronology_params, array('start', 'flat') )
        );
    }
  }
  else
  {
    if ($page['chronology_field'] == 'created')
    {
      $chronology_field = 'posted';
    }
    else
    {
      $chronology_field = 'created';
    }
    if ($conf['index_'.$chronology_field.'_date_icon'])
    {
      $url = duplicate_index_url(
                array('chronology_field'=>$chronology_field ),
                array('chronology_date', 'start', 'flat')
              );
      $template->assign(
          'U_MODE_'.strtoupper($chronology_field),
          $url
        );
    }
  }

  // we add isset($page['search_details']) in this condition because it only
  // applies to regular search, not the legacy qsearch. As Piwigo 14 will still
  // be able to show an old quicksearch result, we must check this condtion too.
  if ('search' == $page['section'] and isset($page['search_details']))
  {
    include_once(PHPWG_ROOT_PATH.'include/functions_search.inc.php');

    $my_search = get_search_array($page['search']);

    $page['search_details']['forbidden'] = get_sql_condition_FandF(
      array(
        'forbidden_categories' => 'category_id',
        'visible_categories' => 'category_id',
        'visible_images' => 'id',
      ),
      "\n  AND"
    );

    // we want filters to be filled with values related to current items ONLY IF we have some filters filled
    if ($page['search_details']['has_filters_filled'])
    {
      $search_items = array(-1);
      if (!empty($page['items']))
      {
        $search_items = $page['items'];
      }

      $search_items_clause = 'image_id IN ('.implode(',', $search_items).')';
    }
    else
    {
      $search_items_clause = '1=1';
    }

    if (isset($my_search['fields']['tags']))
    {
      $filter_tags = array();

      // TODO calling get_available_tags(), with lots of photos/albums/tags may cost time,
      // we should reuse the result if already executed (for building the menu for example)

      $other_filters_items = get_items_for_filter('tags');
      if (false === $other_filters_items)
      {
        $filter_tags = get_available_tags();
        usort($filter_tags, 'tag_alpha_compare');
      }
      else
      {
        $filter_tags = get_common_tags($other_filters_items, 0);

        // the user may have started a search on 2 or more tags that have no
        // intersection. In this case, $search_items is empty and get_common_tags
        // returns nothing. We should still display the list of selected tags. We
        // have to "force" them in the list.
        $missing_tag_ids = array_diff($my_search['fields']['tags']['words'], array_column($filter_tags, 'id'));

        if (count($missing_tag_ids) > 0)
        {
          $filter_tags = array_merge(get_available_tags($missing_tag_ids), $filter_tags);
        }
      }

      $template->assign('TAGS', $filter_tags);

      $filter_tag_ids = count($filter_tags) > 0 ? array_column($filter_tags, 'id') : array();

      // in case the search has forbidden tags for current user, we need to filter the search rule
      $my_search['fields']['tags']['words'] = array_intersect($my_search['fields']['tags']['words'], $filter_tag_ids);
    }

    if (isset($my_search['fields']['author']))
    {
      $filter_clause = get_clause_for_filter('author');

      $query = '
SELECT
    author,
    COUNT(DISTINCT(id)) AS counter
  FROM '.IMAGES_TABLE.' AS i
    JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON ic.image_id = i.id
  WHERE '.$filter_clause.'
    AND author IS NOT NULL
  GROUP BY author
;';
      $authors = query2array($query);
      $author_names = array();
      foreach ($authors as $author)
      {
        $author_names[] = $author['author'];
      }
      $template->assign('AUTHORS', $authors);

      // in case the search has forbidden authors for current user, we need to filter the search rule
      $my_search['fields']['author']['words'] = array_intersect($my_search['fields']['author']['words'], $author_names);
    }

    if (isset($my_search['fields']['date_posted']))
    {
      $filter_clause = get_clause_for_filter('date_posted');

      $query = '
SELECT
    SUBDATE(NOW(), INTERVAL 24 HOUR) AS 24h,
    SUBDATE(NOW(), INTERVAL 7 DAY) AS 7d,
    SUBDATE(NOW(), INTERVAL 30 DAY) AS 30d,
    SUBDATE(NOW(), INTERVAL 3 MONTH) AS 3m,
    SUBDATE(NOW(), INTERVAL 6 MONTH) AS 6m
;';
    $thresholds = query2array($query)[0];

    $query = '
SELECT
    image_id,
    date_available
  FROM '.IMAGES_TABLE.' AS i
    JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON ic.image_id = i.id
  WHERE '.$filter_clause.'
;';
    $dates = query2array($query);
    $pre_counters = array_fill_keys(array_keys($thresholds), array());
    foreach ($dates as $date_row)
    {
      $year = date('Y', strtotime($date_row['date_available']));
      @$pre_counters['y'.$year][ $date_row['image_id'] ] = 1;
      foreach ($thresholds as $threshold => $date_limit)
      {
        if ($date_row['date_available'] > $date_limit)
        {
          @$pre_counters[$threshold][ $date_row['image_id'] ] = 1;
        }
      }
    }

    $label_for_threshold = array(
      '24h' => l10n('last 24 hours'),
      '7d' => l10n('last 7 days'),
      '30d' => l10n('last 30 days'),
      '3m' => l10n('last 3 months'),
      '6m' => l10n('last 6 months'),
    );

    // pre_counters need to be deduplicated because a photo can be in several albums
    $counters = array_fill_keys(array_keys($thresholds), array('label'=>'default label', 'counter'=>0));
    foreach (array_keys($thresholds) as $threshold)
    {
      $counters[$threshold] = array(
        'label' => $label_for_threshold[$threshold],
        'counter' => count(array_keys($pre_counters[$threshold]))
      );
    }

    $pre_counters_keys = array_keys($pre_counters);
    rsort($pre_counters_keys); // we want y2023 to come before y2022 in the list

    foreach ($pre_counters_keys as $key)
    {
      if (preg_match('/^y(\d+)$/', $key, $matches))
      {
        $counters[$key] = array(
          'label' => l10n('year %d', $matches[1]),
          'counter' => count(array_keys($pre_counters[$key]))
        );
      }
    }

    foreach ($counters as $key => $counter)
    {
      if (0 == $counter['counter'])
      {
        unset($counters[$key]);
      }
    }

    $template->assign('DATE_POSTED', $counters);
  }

    if (isset($my_search['fields']['added_by']))
    {
      $filter_clause = get_clause_for_filter('added_by');

      $query = '
SELECT
    COUNT(DISTINCT(id)) AS counter,
    added_by AS added_by_id
  FROM '.IMAGES_TABLE.' AS i
    JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON ic.image_id = i.id
  WHERE '.$filter_clause.'
  GROUP BY added_by_id
  ORDER BY counter DESC
;';
      $added_by = query2array($query);
      $user_ids = array();

      if (count($added_by) > 0)
      {
        // now let's find the usernames of added_by users
        foreach ($added_by as $i)
        {
          $user_ids[] = $i['added_by_id'];
        }

        $query = '
SELECT
    '.$conf['user_fields']['id'].' AS id,
    '.$conf['user_fields']['username'].' AS username
  FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['id'].' IN ('.implode(',', $user_ids).')
;';
        $username_of = query2array($query, 'id', 'username');

        foreach (array_keys($added_by) as $added_by_idx)
        {
          $added_by_id = $added_by[$added_by_idx]['added_by_id'];
          $added_by[$added_by_idx]['added_by_name'] = $username_of[$added_by_id] ?? 'user #'.$added_by_id.' (deleted)';
        }
      }

      $template->assign('ADDED_BY', $added_by);

      // in case the search has forbidden added_by users for current user, we need to filter the search rule
      $my_search['fields']['added_by'] = array_intersect($my_search['fields']['added_by'], $user_ids);
    }

    if (isset($my_search['fields']['cat']) and !empty($my_search['fields']['cat']['words']))
    {
      $fullname_of = array();

      $query = '
SELECT
    id, 
    uppercats
  FROM '.CATEGORIES_TABLE.'
    INNER JOIN '.USER_CACHE_CATEGORIES_TABLE.' ON id = cat_id AND user_id = '.$user['id'].'
  WHERE id IN ('.implode(',', $my_search['fields']['cat']['words']).')
;';
      $result = pwg_query($query);

      while ($row = pwg_db_fetch_assoc($result))
      {
        $cat_display_name = get_cat_display_name_cache(
          $row['uppercats'],
          'admin.php?page=album-' // TODO not sure it's relevant to link to admin pages
        );
        $row['fullname'] = strip_tags($cat_display_name);

        $fullname_of[$row['id']] = $row['fullname'];
      }

      $template->assign('fullname_of', json_encode($fullname_of));

      // in case the search has forbidden albums for current user, we need to filter the search rule
      $my_search['fields']['cat']['words'] = array_intersect($my_search['fields']['cat']['words'], array_keys($fullname_of));
    }

    if (isset($my_search['fields']['filetypes']))
    {
      $filter_clause = get_clause_for_filter('filetypes');

      // get all file extensions for this user in the gallery, whatever the current filters
      $cache_key = $persistent_cache->make_key('file_exts'.$user['id'].$user['cache_update_time']);
      if (!$persistent_cache->get($cache_key, $all_exts))
      {
        $query = '
SELECT
    SUBSTRING_INDEX(path, ".", -1) AS ext,
    COUNT(DISTINCT(id)) AS counter
  FROM '.IMAGES_TABLE.' AS i
    JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON ic.image_id = i.id
  WHERE 1=1'.$page['search_details']['forbidden'].'
  GROUP BY ext
  ORDER BY counter DESC
;';
        $all_exts = query2array($query, 'ext', 'counter');
        $persistent_cache->set($cache_key, $all_exts);
      }

      if (preg_match('/^image_id IN/', $filter_clause))
      {
        $query = '
SELECT
    SUBSTRING_INDEX(path, ".", -1) AS ext,
    COUNT(DISTINCT(id)) AS counter
  FROM '.IMAGES_TABLE.' AS i
    JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON ic.image_id = i.id
  WHERE '.$filter_clause.'
  GROUP BY ext
  ORDER BY counter DESC
;';
        $filtered_exts = query2array($query, 'ext', 'counter');

        $exts = array();
        foreach ($all_exts as $ext => $counter)
        {
          $exts[$ext] = $filtered_exts[$ext] ?? 0;
        }

        $template->assign('FILETYPES', $exts);
      }
      else
      {
        $template->assign('FILETYPES', $all_exts);
      }
    }

    // For rating
    if (isset($my_search['fields']['ratings']))
    {
      $filter_clause = get_clause_for_filter('ratings');

      $ratings = array_fill(0, 6, 0);

      $query = '
SELECT
    DISTINCT id,
    rating_score
  FROM '.IMAGES_TABLE.' AS i
    JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON ic.image_id = i.id
  WHERE '.$filter_clause;

      $result = pwg_query($query);
      while ($row = pwg_db_fetch_assoc($result))
      {
        $r = 5;

        if (!isset($row['rating_score']))
        {
          $r = 0;
        }
        else
        {
          for ($i=1; $i<=4; $i++)
          {
            if ($row['rating_score'] < $i)
            {
              $r = $i;
              break;
            }
          }
        }

        $ratings[$r]++;
      }

      $template->assign('RATING', $ratings);
    }

    // For filesize
    if (isset($my_search['fields']['filesize_min']) && isset($my_search['fields']['filesize_max']))
    {
      $filter_clause = get_clause_for_filter('filesize');

      $filesizes = array();
      $filesize = array();

      $query = '
SELECT
    DISTINCT id,
    filesize
  FROM '.IMAGES_TABLE.' AS i
    JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON ic.image_id = i.id
  WHERE '.$filter_clause.'
;';
      $result = pwg_query($query);
      while ($row = pwg_db_fetch_assoc($result))
      {
        @$filesizes[sprintf('%.1f', $row['filesize']/1024)]++;
      }

      if (empty($filesizes))
      { // arbitrary values, only used when no photos on the gallery
        $filesizes = array(0, 1, 2, 5, 8, 15);
      }

      $unique_filesizes = array_keys($filesizes);
      sort($unique_filesizes, SORT_NUMERIC);

      $filesize['list'] = implode(',', $unique_filesizes);

      $filesize['bounds'] = array(
        'min' => $unique_filesizes[0],
        'max' => end($unique_filesizes),
      );

      // warning: we will (hopefully) have smarter values for filters. The min/max of the
      // current search won't always be the first/last values found. It's going to be a
      // problem with this way to select selected values
      $filesize['selected'] = array(
        'min' => !empty($my_search['fields']['filesize_min']) ? sprintf('%.1f', $my_search['fields']['filesize_min']/1024) : $unique_filesizes[0],
        'max' => !empty($my_search['fields']['filesize_max']) ? sprintf('%.1f', $my_search['fields']['filesize_max']/1024) : end($unique_filesizes),
      );

      $template->assign('FILESIZE', $filesize );
    }
    
    if (isset($my_search['fields']['ratios']))
    {
      $filter_clause = get_clause_for_filter('ratios');

      $query = '
SELECT
    DISTINCT id,
    width,
    height
  FROM '.IMAGES_TABLE.' as i
    JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON ic.image_id = i.id
  WHERE '.$filter_clause.'
    AND width IS NOT NULL
    AND height IS NOT NULL
;';
      $ratios = array(
        'Portrait' => 0,
        'square' => 0,
        'Landscape' => 0,
        'Panorama' => 0,
      );

      $result = pwg_query($query);
      while ($row = pwg_db_fetch_assoc($result))
      {
        $r = floor($row['width'] / $row['height'] * 100) / 100;
        if ($r < 0.95)
        {
          $ratios['Portrait']++;
        }
        else if ($r >= 0.95 and $r <= 1.05)
        {
          $ratios['square']++;
        }
        else if ($r > 1.05 and $r < 2)
        {
          $ratios['Landscape']++;
        }
        else if ($r >= 2)
        {
          $ratios['Panorama']++;
        }
      }
      $template->assign('RATIOS', $ratios);
    }

    if (isset($my_search['fields']['height_min']) and isset($my_search['fields']['height_max']))
    {
      $filter_clause = get_clause_for_filter('height');

      $query = '
SELECT
    height
  FROM '.IMAGES_TABLE.' as i
    JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON ic.image_id = i.id
  WHERE '.$filter_clause.'
    AND height IS NOT NULL
  GROUP BY height
  ORDER BY height ASC
;';
      $heights = query2array($query, null, 'height');

      $height = array(
        'list' => implode(',', $heights),
        'bounds' => array(
          'min' => $heights[0],
          'max' => end($heights),
        ),
        'selected' => array(
          'min' => !empty($my_search['fields']['height_min']) ? $my_search['fields']['height_min'] : $heights[0],
          'max' => !empty($my_search['fields']['height_max']) ? $my_search['fields']['height_max'] : end($heights),
        )
      );

      $template->assign('HEIGHT', $height);
    }

    if (isset($my_search['fields']['width_min']) and isset($my_search['fields']['width_max']))
    {
      $filter_clause = get_clause_for_filter('width');

      $query = '
SELECT
    width
  FROM '.IMAGES_TABLE.' as i
    JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON ic.image_id = i.id
  WHERE '.$filter_clause.'
    AND width IS NOT NULL
  GROUP BY width
  ORDER BY width ASC
;';
      $widths = query2array($query, null, 'width');

      $width = array(
        'list' => implode(',', $widths),
        'bounds' => array(
          'min' => $widths[0],
          'max' => end($widths),
        ),
        'selected' => array(
          'min' => !empty($my_search['fields']['width_min']) ? $my_search['fields']['width_min'] : $widths[0],
          'max' => !empty($my_search['fields']['width_max']) ? $my_search['fields']['width_max'] : end($widths),
        )
      );

      $template->assign('WIDTH', $width);
    }

    $template->assign(
      array(
        'GP' => json_encode($my_search),
        'SEARCH_ID' => $page['search'],
      )
    );

    if (0 == $page['start'] and !isset($page['chronology_field']) and isset($page['search_details']))
    {
      if (isset($page['search_details']['matching_cat_ids']))
      {
        $cat_ids = $page['search_details']['matching_cat_ids'];
        if (count($cat_ids))
        {
          $query = '
SELECT
    c.*
  FROM '.CATEGORIES_TABLE.' AS c
    INNER JOIN '.USER_CACHE_CATEGORIES_TABLE.' ON c.id = cat_id and user_id = '.$user['id'].'
  WHERE id IN ('.implode(',', $cat_ids).')
;';
          $cats = query2array($query);
          usort($cats, 'name_compare');
          $albums_found = array();
          foreach ($cats as $cat)
          {
            $single_link = false;
            $albums_found[] = get_cat_display_name_cache(
              $cat['uppercats'],
              '',
              $single_link
            );
          }

          if (count($albums_found) > 0)
          {
            $template->assign('ALBUMS_FOUND', $albums_found);
          }
        }
      }
      if (isset($page['search_details']['matching_tag_ids']))
      {
        $tag_ids = $page['search_details']['matching_tag_ids'];

        if (count($tag_ids) > 0)
        {
          $tags = get_available_tags($tag_ids);
          usort($tags, 'tag_alpha_compare');
          $tags_found = array();
          foreach ($tags as $tag)
          {
            $url = make_index_url(
              array(
                'tags' => array($tag)
              )
            );
            $tags_found[] = sprintf('<a href="%s">%s</a>', $url, $tag['name']);
          }

          if (count($tags_found) > 0)
          {
            $template->assign('TAGS_FOUND', $tags_found);
          }
        }
      }
    }
  }

  if ('categories' == $page['section'] and isset($page['category']) and !isset($page['combined_categories']))
  {
    $template->assign(
      array(
        'SEARCH_IN_SET_BUTTON' => $conf['index_search_in_set_button'],
        'SEARCH_IN_SET_ACTION' => $conf['index_search_in_set_action'],
        'SEARCH_IN_SET_URL' => get_root_url().'search.php?cat_id='.$page['category']['id'],
      )
    );
  }

  if (isset($page['body_data']['tag_ids']))
  {
    $template->assign(
      array(
        'SEARCH_IN_SET_BUTTON' => $conf['index_search_in_set_button'],
        'SEARCH_IN_SET_ACTION' => $conf['index_search_in_set_action'],
        'SEARCH_IN_SET_URL' => get_root_url().'search.php?tag_id='.implode(',', $page['body_data']['tag_ids']),
      )
    );
  }

  if (isset($page['category']) and is_admin() and $conf['index_edit_icon'])
  {
    $template->assign(
      'U_EDIT',
      get_root_url().'admin.php?page=album-'.$page['category']['id']
      );
  }

  if (is_admin() and !empty($page['items']) and $conf['index_caddie_icon'])
  {
    $template->assign(
      'U_CADDIE',
       add_url_params(duplicate_index_url(), array('caddie'=>1) )
      );
  }

  if ( $page['section']=='search' and $page['start']==0 and
      !isset($page['chronology_field']) and isset($page['qsearch_details']) )
  {
    $cats = array_merge(
        (array)@$page['qsearch_details']['matching_cats_no_images'],
        (array)@$page['qsearch_details']['matching_cats'] );
    if (count($cats))
    {
      usort($cats, 'name_compare');
      $hints = array();
      foreach ( $cats as $cat )
      {
        $hints[] = get_cat_display_name( array($cat), '' );
      }
      $template->assign( 'category_search_results', $hints);
    }

    $tags = (array)@$page['qsearch_details']['matching_tags'];
    foreach ( $tags as $tag )
    {
      $tag['URL'] = make_index_url(array('tags'=>array($tag)));
      $template->append( 'tag_search_results', $tag);
    }
    
    if (empty($page['items']))
    {
      $template->append( 'no_search_results', htmlspecialchars($page['qsearch_details']['q']));
    }
    elseif (!empty($page['qsearch_details']['unmatched_terms']))
    {
      $template->assign( 'no_search_results', array_map('htmlspecialchars', $page['qsearch_details']['unmatched_terms']));
    }
  }

  // image order
  if ( $conf['index_sort_order_input']
      and count($page['items']) > 0
      and $page['section'] != 'most_visited'
      and $page['section'] != 'best_rated')
  {
    $preferred_image_orders = get_category_preferred_image_orders();
    $order_idx = pwg_get_session_var( 'image_order', 0 );
    
    // get first order field and direction
    $first_order = substr($conf['order_by'], 9);
    if (($pos = strpos($first_order, ',')) !== false)
    {
      $first_order = substr($first_order, 0, $pos);
    }
    $first_order = trim($first_order);
    
    $url = add_url_params(
            duplicate_index_url(),
            array('image_order' => '')
          );
    $tpl_orders = array();
    $order_selected = false;
    
    foreach ($preferred_image_orders as $order_id => $order)
    {
      if ($order[2])
      {
        // force select if the field is the first field of order_by
        if (!$order_selected && $order[1]==$first_order)
        {
          $order_idx = $order_id;
          $order_selected = true;
        }
        
        $tpl_orders[ $order_id ] = array(
          'DISPLAY' => $order[0],
          'URL' => $url.$order_id,
          'SELECTED' => $order_idx==$order_id,
          );
      }
    }
    
    $tpl_orders[0]['SELECTED'] = !$order_selected; // unselect "Default" if another one is selected
    $template->assign('image_orders', $tpl_orders);
  }

  // category comment
  if (($page['start']==0 or $conf['album_description_on_all_pages']) and !isset($page['chronology_field']) and !empty($page['comment']) )
  {
    $template->assign('CONTENT_DESCRIPTION', $page['comment'] );
  }

  if ( isset($page['category']['count_categories']) and $page['category']['count_categories']==0 )
  {// count_categories might be computed by menubar - if the case unassign flat link if no sub albums
    $template->clear_assign('U_MODE_FLAT');
  }

  //------------------------------------------------------ main part : thumbnails
  if ( 0==$page['start']
    and !isset($page['flat'])
    and !isset($page['chronology_field'])
    and ('recent_cats'==$page['section'] or 'categories'==$page['section'])
    and (!isset($page['category']['count_categories']) or $page['category']['count_categories']>0 )
  )
  {
    include(PHPWG_ROOT_PATH.'include/category_cats.inc.php');
  }

  if ( !empty($page['items']) )
  {
    include(PHPWG_ROOT_PATH.'include/category_default.inc.php');

    if ($conf['index_sizes_icon'])
    {
      $url = add_url_params(
        duplicate_index_url(),
        array('display' => '')
        );

      $selected_type = $template->get_template_vars('derivative_params')->type;
      $template->clear_assign( 'derivative_params' );
      $type_map = ImageStdParams::get_defined_type_map();
      unset($type_map[IMG_XXLARGE], $type_map[IMG_XLARGE]);

      foreach($type_map as $params)
      {
        $template->append(
          'image_derivatives',
          array(
            'DISPLAY' => l10n($params->type),
            'URL' => $url.$params->type,
            'SELECTED' => ($params->type == $selected_type ? true:false),
            )
          );
      }
    }
  }

  // slideshow
  // execute after init thumbs in order to have all picture informations
  if (!empty($page['cat_slideshow_url']))
  {
    if (isset($_GET['slideshow']))
    {
      redirect($page['cat_slideshow_url']);
    }
    elseif ($conf['index_slideshow_icon'])
    {
      $template->assign('U_SLIDESHOW', $page['cat_slideshow_url']);
    }
  }
}

//------------------------------------------------------------ end
include(PHPWG_ROOT_PATH.'include/page_header.php');
trigger_notify('loc_end_index');
flush_page_messages();
$template->parse_index_buttons();
$template->pparse('index');

//------------------------------------------------------------ log informations
pwg_log();
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
