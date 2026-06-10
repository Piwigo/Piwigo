<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

$filters_views = safe_unserialize(conf_get_param('filters_views', $conf['default_filters_views']));

$template->assign('display_filter', $filters_views);

// we add isset($page['search_details']) in this condition because it only
// applies to regular search, not the legacy qsearch. As Piwigo 14 will still
// be able to show an old quicksearch result, we must check this condtion too.
if ('search' == $page['section'] and isset($page['search_details']))
{
  $display_filters = $filters_views;

  foreach($filters_views as $filt_name => $filt_conf){
    if(isset($filt_conf['access']))
    {
      if ($filt_conf['access'] == 'everybody' or ($filt_conf['access'] == 'admins-only' and is_admin()) or ($filt_conf['access'] == 'registered-users' and is_classic_user()))
      {
        $display_filters[$filt_name]['access'] = true;
      }
      else
      {
        $display_filters[$filt_name]['access'] = false;
      }
    }
  }

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

  if (isset($my_search['fields']['allwords']) and !($display_filters['words']['access']))
  {
    unset($my_search['fields']['allwords']);
  }

  if (isset($my_search['fields']['tags']) and $display_filters['tags']['access'])
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

  else if (isset($my_search['fields']['tags']) and !($display_filters['tags']['access']))
  {
    unset($my_search['fields']['tags']);
  }

  if (isset($my_search['fields']['expert']))
  {
    if (!$display_filters['expert']['access'])
    {
      unset($my_search['fields']['expert']);
    }
    else
    {
      load_language('help_quick_search.lang');
    }
  }

  if (isset($my_search['fields']['author']) and $display_filters['author']['access'])
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

    if (!preg_match('/^image_id IN/', $filter_clause))
    {
      // we use persistent_cache only for fetching lines filtered only by permissions
      $cache_key = $persistent_cache->make_key('filter_author_rows'.$user['id'].$user['cache_update_time']);
      if (!$persistent_cache->get($cache_key, $filter_rows))
      {
        $filter_rows = query2array($query);
        $persistent_cache->set($cache_key, $filter_rows);
      }
    }
    else
    {
      $filter_rows = query2array($query);
    }

    $author_names = array();
    foreach ($filter_rows as $author)
    {
      $author_names[] = $author['author'];
    }
    $template->assign('AUTHORS', $filter_rows);

    // in case the search has forbidden authors for current user, we need to filter the search rule
    $my_search['fields']['author']['words'] = array_intersect($my_search['fields']['author']['words'], $author_names);
  }

  else if (isset($my_search['fields']['author']) and !($display_filters['author']['access']))
  {
    unset($my_search['fields']['author']);
  }

  if (isset($my_search['fields']['date_posted']) and $display_filters['post_date']['access'])
  {
    $filter_clause = get_clause_for_filter('date_posted');
    $cache_key = $persistent_cache->make_key('filter_date_posted'.$user['id'].$user['cache_update_time']);
    $set_persistent_cache = !preg_match('/^image_id IN/', $filter_clause) and !$persistent_cache->get($cache_key, $date_posted);

    if (!isset($date_posted))
    {
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
    DISTINCT id,
    date_available as date
  FROM '.IMAGES_TABLE.' AS i
    JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON ic.image_id = i.id
  WHERE '.$filter_clause.'
;';

      $list_of_dates = array();
      $pre_counters = array();

      $result = pwg_query($query);
      while ($row = pwg_db_fetch_assoc($result))
      {
        foreach ($thresholds as $threshold => $date_limit)
        {
          if ($row['date'] > $date_limit)
          {
            @$pre_counters[$threshold]++;
          }
        }

        list($date_without_time) = explode(' ', $row['date']);
        list($y, $m) = explode('-', $date_without_time);

        @$list_of_dates[$y]['months'][$y.'-'.$m]['days'][$date_without_time]['count']++;
        @$list_of_dates[$y]['months'][$y.'-'.$m]['count']++;
        @$list_of_dates[$y]['count']++;
      }

      $date_posted = array(
        'pre_counters' => $pre_counters,
        'list_of_dates' => $list_of_dates,
      );

      if ($set_persistent_cache)
      {
        // for this filter, we do not store in cache the $filter_rows : for a big gallery it may
        // take more than 10MB. It is smarter to store in cache the result of the computation,
        // which is just around 100 bytes.
        $persistent_cache->set($cache_key, $date_posted);
      }
    }

    $label_for_threshold = array(
      '24h' => l10n('last 24 hours'),
      '7d' => l10n('last 7 days'),
      '30d' => l10n('last 30 days'),
      '3m' => l10n('last 3 months'),
      '6m' => l10n('last 6 months'),
    );

    $counters = array();
    foreach (array_keys($label_for_threshold) as $threshold)
    {
      $counters[$threshold] = array(
        'label' => $label_for_threshold[$threshold],
        'counter' => $date_posted['pre_counters'][$threshold] ?? 0,
      );
    }

    foreach (array_keys($date_posted['list_of_dates']) as $y)
    {
      $date_posted['list_of_dates'][$y]['label'] = l10n('year %d', $y);

      foreach (array_keys($date_posted['list_of_dates'][$y]['months']) as $ym)
      {
        list(,$m) = explode('-', $ym);
        $date_posted['list_of_dates'][$y]['months'][$ym]['label'] = $lang['month'][(int)$m].' '.$y;

        foreach (array_keys($date_posted['list_of_dates'][$y]['months'][$ym]['days']) as $ymd)
        {
          list(,,$d) = explode('-', $ymd);
          $date_posted['list_of_dates'][$y]['months'][$ym]['days'][$ymd]['label'] = format_date($ymd);
        }
      }
    }
    krsort($date_posted['list_of_dates']);

    $template->assign('LIST_DATE_POSTED', $date_posted['list_of_dates']);
    $template->assign('DATE_POSTED', $counters);
  }

  else if (isset($my_search['fields']['date_posted']) and !($display_filters['post_date']['access']))
  {
    unset($my_search['fields']['date_posted']);
  }

  if (isset($my_search['fields']['date_created']) and $display_filters['creation_date']['access'])
  {
    $filter_clause = get_clause_for_filter('date_created');
    $cache_key = $persistent_cache->make_key('filter_date_created'.$user['id'].$user['cache_update_time']);
    $set_persistent_cache = !preg_match('/^image_id IN/', $filter_clause) and !$persistent_cache->get($cache_key, $date_created);

    if (!isset($date_created))
    {
      $query = '
SELECT
    SUBDATE(NOW(), INTERVAL 7 DAY) AS 7d,
    SUBDATE(NOW(), INTERVAL 30 DAY) AS 30d,
    SUBDATE(NOW(), INTERVAL 3 MONTH) AS 3m,
    SUBDATE(NOW(), INTERVAL 6 MONTH) AS 6m,
    SUBDATE(NOW(), INTERVAL 12 MONTH) AS 12m
;';
      $thresholds = query2array($query)[0];

      $query = '
SELECT
    DISTINCT id,
    date_creation as date
  FROM '.IMAGES_TABLE.' AS i
    JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON ic.image_id = i.id
  WHERE '.$filter_clause.'
;';

      $list_of_dates = array();
      $pre_counters = array();

      $result = pwg_query($query);
      while ($row = pwg_db_fetch_assoc($result))
      {
        if(!empty($row['date'])){
          foreach ($thresholds as $threshold => $date_limit)
          {
            if ($row['date'] > $date_limit)
            {
              @$pre_counters[$threshold]++;
            }
          }

          list($date_without_time) = explode(' ', $row['date']);
          list($y, $m) = explode('-', $date_without_time);

          @$list_of_dates[$y]['months'][$y.'-'.$m]['days'][$date_without_time]['count']++;
          @$list_of_dates[$y]['months'][$y.'-'.$m]['count']++;
          @$list_of_dates[$y]['count']++;
        }
      }

      $date_created = array(
        'pre_counters' => $pre_counters,
        'list_of_dates' => $list_of_dates,
      );

      if ($set_persistent_cache)
      {
        // for this filter, we do not store in cache the $filter_rows : for a big gallery it may
        // take more than 10MB. It is smarter to store in cache the result of the computation,
        // which is just around 100 bytes.
        $persistent_cache->set($cache_key, $date_created);
      }
    }

    $label_for_threshold = array(
      '7d' => l10n('last 7 days'),
      '30d' => l10n('last 30 days'),
      '3m' => l10n('last 3 months'),
      '6m' => l10n('last 6 months'),
      '12m' => l10n('last 12 months'),
    );

    $counters = array();
    foreach (array_keys($label_for_threshold) as $threshold)
    {
      $counters[$threshold] = array(
        'label' => $label_for_threshold[$threshold],
        'counter' => $date_created['pre_counters'][$threshold] ?? 0,
      );
    }

    foreach (array_keys($date_created['list_of_dates']) as $y)
    {
      $date_created['list_of_dates'][$y]['label'] = l10n('year %d', $y);

      foreach (array_keys($date_created['list_of_dates'][$y]['months']) as $ym)
      {
        list(,$m) = explode('-', $ym);
        $date_created['list_of_dates'][$y]['months'][$ym]['label'] = $lang['month'][(int)$m].' '.$y;

        foreach (array_keys($date_created['list_of_dates'][$y]['months'][$ym]['days']) as $ymd)
        {
          list(,,$d) = explode('-', $ymd);
          $date_created['list_of_dates'][$y]['months'][$ym]['days'][$ymd]['label'] = format_date($ymd);
        }
      }
    }
    krsort($date_created['list_of_dates']);

    $template->assign('LIST_DATE_CREATED', $date_created['list_of_dates']);
    $template->assign('DATE_CREATED', $counters);
  }

  else if (isset($my_search['fields']['date_created']) and !($display_filters['creation_date']['access']))
  {
    unset($my_search['fields']['date_created']);
  }

  if (isset($my_search['fields']['added_by']) and $display_filters['added_by']['access'])
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

    if (!preg_match('/^image_id IN/', $filter_clause))
    {
      // we use persistent_cache only for fetching lines filtered only by permissions
      $cache_key = $persistent_cache->make_key('filter_added_by_rows'.$user['id'].$user['cache_update_time']);
      if (!$persistent_cache->get($cache_key, $filter_rows))
      {
        $filter_rows = query2array($query);
        $persistent_cache->set($cache_key, $filter_rows);
      }
    }
    else
    {
      $filter_rows = query2array($query);
    }

    $added_by = $filter_rows;
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

  else if (isset($my_search['fields']['added_by']) and !($display_filters['added_by']['access']))
  {
    unset($my_search['fields']['added_by']);
  }

  if (isset($my_search['fields']['cat']) and $display_filters['album']['access'])
  {
    if (!empty($my_search['fields']['cat']['words']))
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
  }

  else if (isset($my_search['fields']['cat']) and !($display_filters['album']['access']))
  {
    unset($my_search['fields']['cat']);
  }

  if (isset($my_search['fields']['filetypes']) and $display_filters['file_type']['access'])
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

  else if (isset($my_search['fields']['filetypes']) and !($display_filters['file_type']['access']))
  {
    unset($my_search['fields']['filetypes']);
  }

  // For rating
  if ($conf['rate'])
  {
    $template->assign('SHOW_FILTER_RATINGS', true);
    
    if (isset($my_search['fields']['ratings']) and $display_filters['rating']['access'])
    {
      $filter_clause = get_clause_for_filter('ratings');

      $cache_key = $persistent_cache->make_key('filter_ratings'.$user['id'].$user['cache_update_time']);

      $set_persistent_cache = !preg_match('/^image_id IN/', $filter_clause) and !$persistent_cache->get($cache_key, $ratings);

      if (!isset($ratings))
      {
        $query = '
SELECT
    DISTINCT id,
    rating_score
  FROM '.IMAGES_TABLE.' AS i
    JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON ic.image_id = i.id
  WHERE '.$filter_clause;

        $filter_rows = query2array($query);

        $ratings = array_fill(0, 6, 0);

        foreach ($filter_rows as $row)
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

        if ($set_persistent_cache)
        {
          // for this filter, we do not store in cache the $filter_rows : for a big gallery it may
          // take more than 10MB. It is smarter to store in cache the result of the computation,
          // which is just around 100 bytes.
          $persistent_cache->set($cache_key, $ratings);
        }
      }
      $template->assign('RATING', $ratings);
    }
    else if (isset($my_search['fields']['ratings']) and !($display_filters['rating']['access']))
    {
      unset($my_search['fields']['ratings']);
    }
  }
  else
  {
    $template->assign('SHOW_FILTER_RATINGS', false);
    if (isset($my_search['fields']['ratings']))
    {
      unset($my_search['fields']['ratings']);
    }
  }

  // For filesize
  if (isset($my_search['fields']['filesize_min']) && isset($my_search['fields']['filesize_max']) and $display_filters['file_size']['access'])
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

  else if (isset($my_search['fields']['filesize_min']) && isset($my_search['fields']['filesize_max']) and !($display_filters['file_size']['access']))
  {
    unset($my_search['fields']['filesize_min']);
    unset($my_search['fields']['filesize_max']);
  }
  
  if (isset($my_search['fields']['ratios']) and $display_filters['ratio']['access'])
  {
    $filter_clause = get_clause_for_filter('ratios');

    $cache_key = $persistent_cache->make_key('filter_ratios'.$user['id'].$user['cache_update_time']);

    $set_persistent_cache = !preg_match('/^image_id IN/', $filter_clause) and !$persistent_cache->get($cache_key, $ratios);

    if (!isset($ratios))
    {
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

      $filter_rows = query2array($query);

      $ratios = array(
        'Portrait' => 0,
        'square' => 0,
        'Landscape' => 0,
        'Panorama' => 0,
      );

      foreach ($filter_rows as $row)
      {
        if ($row['width'] <= 0 and $row['height'] <= 0)
        {
          continue;
        }

        $r = $row['width'] / $row['height'];
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

      if ($set_persistent_cache)
      {
        // for this filter, we do not store in cache the $filter_rows : for a big gallery it may
        // take more than 10MB. It is smarter to store in cache the result of the computation,
        // which is just around 100 bytes.
        $persistent_cache->set($cache_key, $ratios);
      }
    }
    $template->assign('RATIOS', $ratios);
  }

  else if (isset($my_search['fields']['ratios']) and !($display_filters['ratio']['access']))
  {
    unset($my_search['fields']['ratios']);
  }

  if (isset($my_search['fields']['height_min']) and isset($my_search['fields']['height_max']) and $display_filters['height']['access'])
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

    if (!preg_match('/^image_id IN/', $filter_clause))
    {
      // we use persistent_cache only for fetching lines filtered only by permissions
      $cache_key = $persistent_cache->make_key('filter_height_rows'.$user['id'].$user['cache_update_time']);
      if (!$persistent_cache->get($cache_key, $filter_rows))
      {
        $filter_rows = query2array($query, null, 'height');
        $persistent_cache->set($cache_key, $filter_rows);
      }
    }
    else
    {
      $filter_rows = query2array($query, null, 'height');
    }

    $heights = $filter_rows;

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

  else if (isset($my_search['fields']['height_min']) && isset($my_search['fields']['height_max']) and !($display_filters['height']['access']))
  {
    unset($my_search['fields']['height_min']);
    unset($my_search['fields']['height_max']);
  }

  if (isset($my_search['fields']['width_min']) and isset($my_search['fields']['width_max']) and $display_filters['width']['access'])
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

    if (!preg_match('/^image_id IN/', $filter_clause))
    {
      // we use persistent_cache only for fetching lines filtered only by permissions
      $cache_key = $persistent_cache->make_key('filter_width_rows'.$user['id'].$user['cache_update_time']);
      if (!$persistent_cache->get($cache_key, $filter_rows))
      {
        $filter_rows = query2array($query, null, 'width');
        $persistent_cache->set($cache_key, $filter_rows);
      }
    }
    else
    {
      $filter_rows = query2array($query, null, 'width');
    }

    $widths = $filter_rows;

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

  else if (isset($my_search['fields']['width_min']) && isset($my_search['fields']['width_max']) and !($display_filters['width']['access']))
  {
    unset($my_search['fields']['width_min']);
    unset($my_search['fields']['width_max']);
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
