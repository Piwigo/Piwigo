<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
 * @package functions\search
 */

function get_search_id_pattern($candidate)
{
  $clause_pattern = null;
  if (preg_match('/^psk-\d{8}-[a-z0-9]{10}$/i', $candidate))
  {
    $clause_pattern = 'search_uuid = \'%s\'';
  }
  elseif (preg_match('/^\d+$/', $candidate))
  {
    $clause_pattern = 'id = %u';
  }

  return $clause_pattern;
}

function get_search_info($candidate)
{
  global $page;

  // $candidate might be a search.id or a search_uuid
  $clause_pattern = get_search_id_pattern($candidate);

  if (empty($clause_pattern))
  {
    die('Invalid search identifier');
  }

  $query = '
SELECT *
  FROM '.SEARCH_TABLE.'
  WHERE '.sprintf($clause_pattern, $candidate).'
;';
  $searches = query2array($query);

  if (count($searches) > 0)
  {
    // we don't want spies to be able to see the search rules of any prior search (performed
    // by any user). We don't want them to be try index.php?/search/123 then index.php?/search/124
    // and so on. That's why we have implemented search_uuid with random characters.
    //
    // We also don't want to break old search urls with only the numeric id, so we only break if
    // there is no uuid.
    //
    // We also don't want to die if we're in the API.
    if (script_basename() != 'ws' and 'id = %u' == $clause_pattern and isset($searches[0]['search_uuid']))
    {
      fatal_error('this search is not reachable with its id, need the search_uuid instead');
    }

    if (isset($page['section']) and 'search' == $page['section'])
    {
      // to be used later in pwg_log
      $page['search_id'] = $searches[0]['id'];
    }

    return $searches[0];
  }

  return null;
}

/**
 * Returns search rules stored into a serialized array in "search"
 * table. Each search rules set is numericaly identified.
 *
 * @param int $search_id
 * @return array
 */
function get_search_array($search_id)
{
  global $user;

  $search = get_search_info($search_id);

  if (empty($search))
  {
    bad_request('this search identifier does not exist');
  }

  return unserialize($search['rules']);
}

/**
 * Returns the list of items corresponding to the advanced search array.
 *
 * @param array $search
 * @param string $images_where optional additional restriction on images table
 * @return array
 */
function get_regular_search_results($search, $images_where='')
{
  global $conf, $logger;

  $logger->debug(__FUNCTION__, 'search', $search);

  $has_filters_filled = false;

  $forbidden = get_sql_condition_FandF(
        array
          (
            'forbidden_categories' => 'category_id',
            'visible_categories' => 'category_id',
            'visible_images' => 'id'
          ),
        "\n  AND"
    );

  $image_ids_for_filter = array();

  //
  // allwords
  //
  if (isset($search['fields']['allwords']) and !empty($search['fields']['allwords']['words']) and count($search['fields']['allwords']['fields']) > 0)
  {
    $has_filters_filled = true;

    // 1) we search in regular fields (ie, the ones in the piwigo_images table)
    $fields = array('file', 'name', 'comment', 'author');

    if (isset($search['fields']['allwords']['fields']) and count($search['fields']['allwords']['fields']) > 0)
    {
      $fields = array_intersect($fields, $search['fields']['allwords']['fields']);
    }

    $cat_fields_dictionnary = array(
      'cat-title' => 'name',
      'cat-desc' => 'comment',
    );
    $cat_fields = array_intersect(array_keys($cat_fields_dictionnary), $search['fields']['allwords']['fields']);

    // in the OR mode, request must be :
    // ((field1 LIKE '%word1%' OR field2 LIKE '%word1%')
    // OR (field1 LIKE '%word2%' OR field2 LIKE '%word2%'))
    //
    // in the AND mode :
    // ((field1 LIKE '%word1%' OR field2 LIKE '%word1%')
    // AND (field1 LIKE '%word2%' OR field2 LIKE '%word2%'))
    $word_clauses = array();
    $cat_ids_by_word = $tag_ids_by_word = array();
    foreach ($search['fields']['allwords']['words'] as $word)
    {
      $field_clauses = array();
      foreach ($fields as $field)
      {
        $field_clauses[] = $field." LIKE '%".$word."%'";
      }

      if (count($cat_fields) > 0)
      {
        $cat_word_clauses = array();
        $cat_field_clauses = array();
        foreach ($cat_fields as $cat_field)
        {
          $cat_field_clauses[] = $cat_fields_dictionnary[$cat_field]." LIKE '%".$word."%'";
        }

        // adds brackets around where clauses
        $cat_word_clauses[] = implode(' OR ', $cat_field_clauses);

        $query = '
SELECT
    id
  FROM '.CATEGORIES_TABLE.'
  WHERE '.implode(' OR ', $cat_word_clauses).'
;';
        $cat_ids = query2array($query, null, 'id');
        $cat_ids_by_word[$word] = $cat_ids;
        if (count($cat_ids) > 0)
        {
          $query = '
SELECT
    image_id
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id IN ('.implode(',', $cat_ids).')
;';
          $cat_image_ids = query2array($query, null, 'image_id');

          if (count($cat_image_ids) > 0)
          {
            $field_clauses[] = 'id IN ('.implode(',', $cat_image_ids).')';
          }
        }
      }

      // search_in_tags
      if (in_array('tags', $search['fields']['allwords']['fields']))
      {
        $query = '
SELECT
    id
  FROM '.TAGS_TABLE.'
  WHERE name LIKE \'%'.$word.'%\'
;';
        $tag_ids = query2array($query, null, 'id');
        $tag_ids_by_word[$word] = $tag_ids;
        if (count($tag_ids) > 0)
        {
          $query = '
SELECT
    image_id
  FROM '.IMAGE_TAG_TABLE.'
  WHERE tag_id IN ('.implode(',', $tag_ids).')
;';
          $tag_image_ids = query2array($query, null, 'image_id');

          if (count($tag_image_ids) > 0)
          {
            $field_clauses[] = 'id IN ('.implode(',', $tag_image_ids).')';
          }
        }
      }

      if (count($field_clauses) > 0)
      {
        // adds brackets around where clauses
        $word_clauses[] = implode(
          "\n          OR ",
          $field_clauses
        );
      }
    }

    if (count($word_clauses) > 0)
    {
      array_walk(
        $word_clauses,
        function(&$s){ $s = "(".$s.")"; }
      );
    }

    // make sure the "mode" is either OR or AND
    if (!in_array($search['fields']['allwords']['mode'], array('OR', 'AND')))
    {
      $search['fields']['allwords']['mode'] = 'AND';
    }

    $filter_clause = "\n         ".implode(
      "\n         ". $search['fields']['allwords']['mode']. "\n         ",
      $word_clauses
    );

    $query = '
SELECT
    DISTINCT(id)
  FROM '.IMAGES_TABLE.' AS i
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE '.$filter_clause.'
  '.$forbidden.'
;';
    $image_ids_for_filter['allwords'] = query2array($query, null, 'id');

    if (count($cat_ids_by_word) > 0)
    {
      $matching_cat_ids = null;
      foreach ($cat_ids_by_word as $idx => $cat_ids)
      {
        if (is_null($matching_cat_ids))
        {
          // first iteration
          $matching_cat_ids = $cat_ids;
        }
        else
        {
          $matching_cat_ids = array_merge($matching_cat_ids, $cat_ids);
        }
      }

      $matching_cat_ids = array_unique($matching_cat_ids);
    }

    if (count($tag_ids_by_word) > 0)
    {
      $matching_tag_ids = null;
      foreach ($tag_ids_by_word as $idx => $tag_ids)
      {
        if (is_null($matching_tag_ids))
        {
          // first iteration
          $matching_tag_ids = $tag_ids;
        }
        else
        {
          $matching_tag_ids = array_merge($matching_tag_ids, $tag_ids);
        }
      }

      $matching_tag_ids = array_unique($matching_tag_ids);
    }
  }

  //
  // author
  //
  if (isset($search['fields']['author']) and count($search['fields']['author']['words']) > 0)
  {
    $has_filters_filled = true;

    $author_clauses = array();
    foreach ($search['fields']['author']['words'] as $word)
    {
      $author_clauses[] = "author = '".$word."'";
    }

    $query = '
SELECT
    DISTINCT(id)
  FROM '.IMAGES_TABLE.' AS i
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE ('.implode(' OR ', $author_clauses).')
  '.$forbidden.'
;';
    $image_ids_for_filter['author'] = query2array($query, null, 'id');
  }

  //
  // filetypes
  //
  if (!empty($search['fields']['filetypes']))
  {
    $has_filters_filled = true;

    $filetypes_clauses = array();
    foreach ($search['fields']['filetypes'] as $ext)
    {
      $filetypes_clauses[] = 'path LIKE \'%.'.$ext.'\'';
    }

    $query = '
SELECT
    DISTINCT(id)
  FROM '.IMAGES_TABLE.' AS i
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE ('.implode(' OR ', $filetypes_clauses).')
  '.$forbidden.'
;';
    $image_ids_for_filter['filetypes'] = query2array($query, null, 'id');
  }

  //
  // added_by
  //
  if (!empty($search['fields']['added_by']))
  {
    $has_filters_filled = true;

    $query = '
SELECT
    DISTINCT(id)
  FROM '.IMAGES_TABLE.' AS i
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE added_by IN ('.implode(',', $search['fields']['added_by']).')
  '.$forbidden.'
;';
    $image_ids_for_filter['added_by'] = query2array($query, null, 'id');
  }

  //
  // cat
  //
  if (isset($search['fields']['cat']) and !empty($search['fields']['cat']['words']))
  {
    $has_filters_filled = true;

    if ($search['fields']['cat']['sub_inc'])
    {
      // searching all the categories id of sub-categories
      $cat_ids = get_subcat_ids($search['fields']['cat']['words']);
    }
    else
    {
      // TODO we take the list of cat_ids "as is", we should check they still
      // exist and are browseable to the user
      $cat_ids = $search['fields']['cat']['words'];
    }

    // in case the album would no longer exists, we consider the filter on album no longer active
    if (!empty($cat_ids))
    {
      $query = '
SELECT
    DISTINCT(id)
  FROM '.IMAGES_TABLE.' AS i
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE category_id IN ('.implode(',', $cat_ids).')
  '.$forbidden.'
;';
      $image_ids_for_filter['cat'] = query2array($query, null, 'id');
    }
  }

  //
  // date_posted
  //
  if (!empty($search['fields']['date_posted']['preset']))
  {

    $has_filters_filled = true;

    $options = array(
      '24h' => '24 HOUR',
      '7d' => '7 DAY',
      '30d' => '30 DAY',
      '3m' => '3 MONTH',
      '6m' => '6 MONTH',
    );

    if (isset($options[ $search['fields']['date_posted']['preset'] ]) and 'custom' != $search['fields']['date_posted']['preset'])
    {
      $date_posted_clause = 'date_available > SUBDATE(NOW(), INTERVAL '.$options[ $search['fields']['date_posted']['preset'] ].')';
    }
    elseif ('custom' == $search['fields']['date_posted']['preset'] and isset($search['fields']['date_posted']['custom']))
    {
      $date_posted_subclauses = array();
      $custom_dates = array_flip($search['fields']['date_posted']['custom']);

      foreach (array_keys($custom_dates) as $custom_date)
      {
        // in real-life tests, we have determined "where year(date_available) = 2024" was
        // far less (4 times less) than "where date_available between '2024-01-01 00:00:00' and '2024-12-31 23:59:59'"
        // so let's find the begin/end for each custom date
        // ... and also, no need to search for images of 2023-10-16 if 2023-10 is already requested
        $begin = $end = null;

        $ymd = substr($custom_date, 0, 1);
        if ('y' == $ymd)
        {
          $year = substr($custom_date, 1);
          $begin = $year.'-01-01 00:00:00';
          $end = $year.'-12-31 23:59:59';
        }
        elseif ('m' == $ymd)
        {
          list($year, $month) = explode('-', substr($custom_date, 1));

          if (!isset($custom_dates['y'.$year]))
          {
            $begin = $year.'-'.$month.'-01 00:00:00';
            $end = $year.'-'.$month.'-'.cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year).' 23:59:59';
          }
        }
        elseif ('d' == $ymd)
        {
          list($year, $month, $day) = explode('-', substr($custom_date, 1));

          if (!isset($custom_dates['y'.$year]) and !isset($custom_dates['m'.$year.'-'.$month]))
          {
            $begin = $year.'-'.$month.'-'.$day.' 00:00:00';
            $end = $year.'-'.$month.'-'.$day.' 23:59:59';
          }
        }

        if (!empty($begin))
        {
          $date_posted_subclauses[] = 'date_available BETWEEN "'.$begin.'" AND "'.$end.'"';
        }
      }

      $date_posted_clause = '('.implode(' OR ', prepend_append_array_items($date_posted_subclauses, '(', ')')).')';
    }

    $query = '
SELECT
    DISTINCT(id)
  FROM '.IMAGES_TABLE.' AS i
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE '.$date_posted_clause.'
  '.$forbidden.'
;';

    $image_ids_for_filter['date_posted'] = query2array($query, null, 'id');
  }

  //
  // date_created
  //
  if (!empty($search['fields']['date_created']['preset']))
  {

    $has_filters_filled = true;

    $options = array(
      '7d' => '7 DAY',
      '30d' => '30 DAY',
      '3m' => '3 MONTH',
      '6m' => '6 MONTH',
      '12m' => '12 MONTH',
    );

    if (isset($options[ $search['fields']['date_created']['preset'] ]) and 'custom' != $search['fields']['date_created']['preset'])
    {
      $date_created_clause = 'date_creation > SUBDATE(NOW(), INTERVAL '.$options[ $search['fields']['date_created']['preset'] ].')';
    }
    elseif ('custom' == $search['fields']['date_created']['preset'] and isset($search['fields']['date_created']['custom']))
    {
      $date_created_subclauses = array();
      $custom_dates = array_flip($search['fields']['date_created']['custom']);

      foreach (array_keys($custom_dates) as $custom_date)
      {
        // in real-life tests, we have determined "where year(date_creation) = 2024" was
        // far less (4 times less) than "where date_creation between '2024-01-01 00:00:00' and '2024-12-31 23:59:59'"
        // so let's find the begin/end for each custom date
        // ... and also, no need to search for images of 2023-10-16 if 2023-10 is already requested
        $begin = $end = null;

        $ymd = substr($custom_date, 0, 1);
        if ('y' == $ymd)
        {
          $year = substr($custom_date, 1);
          $begin = $year.'-01-01 00:00:00';
          $end = $year.'-12-31 23:59:59';
        }
        elseif ('m' == $ymd)
        {
          list($year, $month) = explode('-', substr($custom_date, 1));

          if (!isset($custom_dates['y'.$year]))
          {
            $begin = $year.'-'.$month.'-01 00:00:00';
            $end = $year.'-'.$month.'-'.cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year).' 23:59:59';
          }
        }
        elseif ('d' == $ymd)
        {
          list($year, $month, $day) = explode('-', substr($custom_date, 1));

          if (!isset($custom_dates['y'.$year]) and !isset($custom_dates['m'.$year.'-'.$month]))
          {
            $begin = $year.'-'.$month.'-'.$day.' 00:00:00';
            $end = $year.'-'.$month.'-'.$day.' 23:59:59';
          }
        }

        if (!empty($begin))
        {
          $date_created_subclauses[] = 'date_creation BETWEEN "'.$begin.'" AND "'.$end.'"';
        }
      }

      $date_created_clause = '('.implode(' OR ', prepend_append_array_items($date_created_subclauses, '(', ')')).')';
    }

    $query = '
SELECT
    DISTINCT(id)
  FROM '.IMAGES_TABLE.' AS i
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE '.$date_created_clause.'
  '.$forbidden.'
;';

    $image_ids_for_filter['date_created'] = query2array($query, null, 'id');
  }

  //
  // ratios
  //
  if (!empty($search['fields']['ratios']))
  {
    $has_filters_filled = true;

    $clause_for_ratio = array(
      'Portrait'  => 'width/height < 0.95',
      'square'    => 'width/height BETWEEN 0.95 AND 1.05',
      'Landscape' => '(width/height > 1.05 AND width/height < 2)',
      'Panorama'  => 'width/height >= 2',
    );

    $ratios_clauses = array();
    foreach ($search['fields']['ratios'] as $r)
    {
      $ratios_clauses[] = $clause_for_ratio[$r];
    }

    $query = '
SELECT
    DISTINCT(id)
  FROM '.IMAGES_TABLE.' AS i
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE ('.implode(' OR ', $ratios_clauses).')
  '.$forbidden.'
;';
    $image_ids_for_filter['ratios'] = query2array($query, null, 'id');
  }

  //
  // ratings
  //
  if ($conf['rate'] and !empty($search['fields']['ratings']))
  {
    $has_filters_filled = true;

    $filter_clauses = array();
    foreach ($search['fields']['ratings'] as $r)
    {
      if (0 == $r)
      {
        $filter_clauses[] = 'rating_score IS NULL';
      }
      else
      {
        $filter_clauses[] = '(rating_score >= '.(intval($r)-1).' AND rating_score < '.$r.')';
      }
    }

    $query = '
SELECT
    DISTINCT(id)
  FROM '.IMAGES_TABLE.' AS i
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE ('.implode(' OR ', $filter_clauses).')
  '.$forbidden.'
;';
    $image_ids_for_filter['ratings'] = query2array($query, null, 'id');
  }

  //
  // filesize
  //
  if (!empty($search['fields']['filesize_min']) and !empty($search['fields']['filesize_max']))
  {
    $has_filters_filled = true;

    // because of conversion from kB to mB, approximation, then conversion back to kB,
    // we need to slightly enlarge the range for search
    $query = '
SELECT
    DISTINCT(id)
  FROM '.IMAGES_TABLE.' AS i
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE filesize BETWEEN '.($search['fields']['filesize_min']-100).' AND '.($search['fields']['filesize_max']+100).'
  '.$forbidden.'
;';
    $image_ids_for_filter['filesize'] = query2array($query, null, 'id');
  }

  //
  // height
  //
  if (!empty($search['fields']['height_min']) and !empty($search['fields']['height_max']))
  {
    $has_filters_filled = true;

    $query = '
SELECT
    DISTINCT(id)
  FROM '.IMAGES_TABLE.' AS i
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE height BETWEEN '.$search['fields']['height_min'].' AND '.$search['fields']['height_max'].'
  '.$forbidden.'
;';
    $image_ids_for_filter['height'] = query2array($query, null, 'id');
  }

  //
  // width
  //
  if (!empty($search['fields']['width_min']) and !empty($search['fields']['width_max']))
  {
    $has_filters_filled = true;

    $query = '
SELECT
    DISTINCT(id)
  FROM '.IMAGES_TABLE.' AS i
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE width BETWEEN '.$search['fields']['width_min'].' AND '.$search['fields']['width_max'].'
  '.$forbidden.'
;';
    $image_ids_for_filter['width'] = query2array($query, null, 'id');
  }

  //
  // tags
  //
  if (isset($search['fields']['tags']) and !empty($search['fields']['tags']['words']))
  {
    $has_filters_filled = true;

    $image_ids_for_filter['tags'] = get_image_ids_for_tags(
      $search['fields']['tags']['words'],
      $search['fields']['tags']['mode']
      );
  }

  //
  // custom search
  //
  if (!empty($images_where))
  {
    $query = '
SELECT
    DISTINCT(id)
  FROM '.IMAGES_TABLE.' AS i
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE '.$images_where.'
  '.$forbidden.'
;';
    $image_ids_for_filter['custom'] = query2array($query, null, 'id');
  }

  $items = array();
  if (!empty($image_ids_for_filter))
  {
    if (count($image_ids_for_filter) > 1)
    {
      $items = array_values(array_unique(array_intersect(...array_values($image_ids_for_filter))));
    }
    else
    {
      $items = $image_ids_for_filter[ array_keys($image_ids_for_filter)[0] ];
    }
  }

  $logger->debug(__FUNCTION__.' '.count($items).' items in $unsorted_items');

  if (count($items) > 1)
  {
    $query = '
SELECT
    id
  FROM '.IMAGES_TABLE.' i
  WHERE id IN ('.implode(',', $items).')
  '.$conf['order_by'];

    $items = array_from_query($query, 'id');
  }

  return array(
    'items' => $items,
    'search_details' => array(
      'matching_cat_ids' => isset($matching_cat_ids) ? array_values($matching_cat_ids) : null,
      'matching_tag_ids' => isset($matching_tag_ids) ? array_values($matching_tag_ids) : null,
      'has_filters_filled' => $has_filters_filled,
      'image_ids_for_filter' => $image_ids_for_filter,
    ),
  );
}

/**
 * Returns the SQL WHERE clause to be used to build filter values
 *
 * @since 15
 *
 * @param string $filter_name
 *
 * @return string
 */
function get_clause_for_filter($filter_name)
{
  global $page;

  $other_filters_items = get_items_for_filter($filter_name);
  if (false === $other_filters_items)
  {
    return '1=1'.$page['search_details']['forbidden'];
  }

  return 'image_id IN ('.implode(',', $other_filters_items).')';
}

/**
 * Returns the list of items (image_ids) to be used to build filter values
 * for a given filter. Depends on the other filters. Use a cache to avoid
 * computing the same large array_intersect several times.
 *
 * @since 15
 *
 * @param string $filter_name
 *
 * @return array of image_ids (or false)
 */
function get_items_for_filter($filter_name)
{
  global $page, $logger;

  $other_filters = array_diff(array_keys($page['search_details']['image_ids_for_filter']), array($filter_name));

  if (empty($other_filters))
  {
    return false;
  }

  $cache_key = md5(implode(',', $other_filters));

  if (!isset($page['search_details'][__FUNCTION__][$cache_key]))
  {
    $function_start = get_moment();

    $other_filters_items = $page['search_details']['image_ids_for_filter'][array_shift($other_filters)];
    foreach ($other_filters as $other_filter)
    {
      $other_filters_items = array_intersect($other_filters_items, $page['search_details']['image_ids_for_filter'][$other_filter]);
    }

    $other_filters_items = array_unique($other_filters_items);

    $debug_msg = '['.__FUNCTION__.'] cache computed for '.(count($other_filters)+1).' other filters';
    $debug_msg.= ' ('.count($other_filters_items).' items)';
    $debug_msg.= ', time = '.get_elapsed_time($function_start, get_moment());
    $logger->debug($debug_msg);

    if (empty($other_filters_items))
    {
      $other_filters_items = array(-1);
    }

    @$page['search_details'][__FUNCTION__][$cache_key] = $other_filters_items;
  }

  return $page['search_details'][__FUNCTION__][$cache_key];
}


define('QST_QUOTED',         0x01);
define('QST_NOT',            0x02);
define('QST_OR',             0x04);
define('QST_WILDCARD_BEGIN', 0x08);
define('QST_WILDCARD_END',   0x10);
define('QST_WILDCARD', QST_WILDCARD_BEGIN|QST_WILDCARD_END);
define('QST_BREAK',          0x20);

/**
 * A search scope applies to a single token and restricts the search to a subset of searchable fields.
 */
class QSearchScope
{
  var $id;
  var $aliases;
  var $is_text;
  var $nullable;

  function __construct($id, $aliases, $nullable=false, $is_text=true)
  {
    $this->id = $id;
    $this->aliases = $aliases;
    $this->is_text = $is_text;
    $this->nullable =$nullable;
  }

  function parse($token)
  {
    if (!$this->nullable && 0==strlen($token->term))
      return false;
    return true;
  }

  function process_char(&$ch, &$crt_token)
  {
    return false;
  }
}

class QNumericRangeScope extends QSearchScope
{
  private $epsilon;
  function __construct($id, $aliases, $nullable=false, $epsilon=0)
  {
    parent::__construct($id, $aliases, $nullable, false);
    $this->epsilon = $epsilon;
  }

  function parse($token)
  {
    $str = $token->term;
    $strict = array(0,0);
    $range_requested = true;
    if ( ($pos = strpos($str, '..')) !== false)
      $range = array( substr($str,0,$pos), substr($str, $pos+2));
    elseif ('>' == @$str[0])// ratio:>1
    {
      $range = array( substr($str,1), '');
      $strict[0] = 1;
    }
    elseif ('<' == @$str[0]) // size:<5mp
    {
      $range = array('', substr($str,1));
      $strict[1] = 1;
    }
    elseif( ($token->modifier & QST_WILDCARD_BEGIN) )
      $range = array('', $str);
    elseif( ($token->modifier & QST_WILDCARD_END) )
      $range = array($str, '');
    else
    {
      $range = array($str, $str);
      $range_requested = false;
    }

    foreach ($range as $i =>&$val)
    {
      if (preg_match('#^(-?[0-9.]+)/([0-9.]+)$#i', $val, $matches))
      {
        $val = floatval($matches[1]/$matches[2]);
      }
      elseif (preg_match('/^(-?[0-9.]+)([km])?/i', $val, $matches))
      {
        $val = floatval($matches[1]);
        if (isset($matches[2]))
        {
          $mult = 1;
          if ($matches[2]=='k' || $matches[2]=='K')
            $mult = 1000;
          else
            $mult = 1000000;
          $val *= $mult;
          if ($i && !$range_requested)
          {// round up the upper limit if possible - e.g 6k goes up to 6999, but 6.12k goes only up to 6129
            if ( ($dot_pos = strpos($matches[1], '.')) !== false )
            {
              $requested_precision = strlen($matches[1]) - $dot_pos - 1;
              $mult /= pow(10, $requested_precision);
            }
            if ($mult>1)
              $val += $mult-1;
          }
        }
      }
      else
        $val = '';
      if (is_numeric($val))
      {
        if ($i ^ $strict[$i])
          $val += $this->epsilon;
        else
          $val -= $this->epsilon;
      }
    }

    if (!$this->nullable && $range[0]==='' && $range[1]==='')
      return false;
    $token->scope_data = array( 'range'=>$range, 'strict'=>$strict );
    return true;
  }

  function get_sql($field, $token)
  {
    $clauses = array();
    if ($token->scope_data['range'][0]!=='')
      $clauses[] = $field.' >'.($token->scope_data['strict'][0]?'':'=').$token->scope_data['range'][0].' ';
    if ($token->scope_data['range'][1]!=='')
      $clauses[] = $field.' <'.($token->scope_data['strict'][1]?'':'=').$token->scope_data['range'][1].' ';

    if (empty($clauses))
    {
      if ($token->modifier & QST_WILDCARD)
        return $field.' IS NOT NULL';
      else
        return $field.' IS NULL';
    }
    return '('.implode(' AND ', $clauses).')';
  }
}


class QDateRangeScope extends QSearchScope
{
  function __construct($id, $aliases, $nullable=false)
  {
    parent::__construct($id, $aliases, $nullable, false);
  }

  function parse($token)
  {
    $str = $token->term;
    $strict = array(0,0);
    if ( ($pos = strpos($str, '..')) !== false)
      $range = array( substr($str,0,$pos), substr($str, $pos+2));
    elseif ('>' == @$str[0])
    {
      $range = array( substr($str,1), '');
      $strict[0] = 1;
    }
    elseif ('<' == @$str[0])
    {
      $range = array('', substr($str,1));
      $strict[1] = 1;
    }
    elseif( ($token->modifier & QST_WILDCARD_BEGIN) )
      $range = array('', $str);
    elseif( ($token->modifier & QST_WILDCARD_END) )
      $range = array($str, '');
    else
      $range = array($str, $str);

    foreach ($range as $i =>&$val)
    {
      if (preg_match('/([0-9]{4})-?((?:1[0-2])|(?:0?[1-9]))?-?((?:(?:[1-3][0-9])|(?:0?[1-9])))?/', $val, $matches))
      {
        array_shift($matches);
        if (!isset($matches[1]))
          $matches[1] = ($i ^ $strict[$i]) ? 12 : 1;
        if (!isset($matches[2]))
          $matches[2] = ($i ^ $strict[$i]) ? 31 : 1;
        $val = implode('-', $matches);
        if ($i ^ $strict[$i])
          $val .= ' 23:59:59';
      }
      elseif (strlen($val))
        return false;
    }

    if (!$this->nullable && $range[0]=='' && $range[1] == '')
      return false;

    $token->scope_data = $range;
    return true;
  }

  function get_sql($field, $token)
  {
    $clauses = array();
    if ($token->scope_data[0]!='')
      $clauses[] = $field.' >= \'' . $token->scope_data[0].'\'';
    if ($token->scope_data[1]!='')
      $clauses[] = $field.' <= \'' . $token->scope_data[1].'\'';

    if (empty($clauses))
    {
      if ($token->modifier & QST_WILDCARD)
        return $field.' IS NOT NULL';
      else
        return $field.' IS NULL';
    }
    return '('.implode(' AND ', $clauses).')';
  }
}

/**
 * Analyzes and splits the quick/query search query $q into tokens.
 * q='john bill' => 2 tokens 'john' 'bill'
 * Special characters for MySql full text search (+,<,>,~) appear in the token modifiers.
 * The query can contain a phrase: 'Pierre "New York"' will return 'pierre' qnd 'new york'.
 *
 * @param string $q
 */

/** Represents a single word or quoted phrase to be searched.*/
class QSingleToken
{
  var $is_single = true;
  var $modifier;
  var $term; /* the actual word/phrase string*/
  var $variants = array();
  var $scope;

  var $scope_data;
  var $idx;

  function __construct($term, $modifier, $scope)
  {
    $this->term = $term;
    $this->modifier = $modifier;
    $this->scope = $scope;
  }

  function __toString()
  {
    $s = '';
    if (isset($this->scope))
      $s .= $this->scope->id .':';
    if ($this->modifier & QST_WILDCARD_BEGIN)
      $s .= '*';
    if ($this->modifier & QST_QUOTED)
      $s .= '"';
    $s .= $this->term;
    if ($this->modifier & QST_QUOTED)
      $s .= '"';
    if ($this->modifier & QST_WILDCARD_END)
      $s .= '*';
    return $s;
  }
}

/** Represents an expression of several words or sub expressions to be searched.*/
class QMultiToken
{
  var $is_single = false;
  var $modifier;
  var $tokens = array(); // the actual array of QSingleToken or QMultiToken

  function __toString()
  {
    $s = '';
    for ($i=0; $i<count($this->tokens); $i++)
    {
      $modifier = $this->tokens[$i]->modifier;
      if ($i)
        $s .= ' ';
      if ($modifier & QST_OR)
        $s .= 'OR ';
      if ($modifier & QST_NOT)
        $s .= 'NOT ';
      if (! ($this->tokens[$i]->is_single) )
      {
        $s .= '(';
        $s .= $this->tokens[$i];
        $s .= ')';
      }
      else
      {
        $s .= $this->tokens[$i];
      }
    }
    return $s;
  }

  private function push(&$token, &$modifier, &$scope)
  {
    if (strlen($token) || (isset($scope) && $scope->nullable))
    {
      if (isset($scope))
        $modifier |= QST_BREAK;
      $this->tokens[] = new QSingleToken($token, $modifier, $scope);
    }
    $token = "";
    $modifier = 0;
    $scope = null;
  }

  /**
  * Parses the input query string by tokenizing the input, generating the modifiers (and/or/not/quotation/wildcards...).
  * Recursivity occurs when parsing ()
  * @param string $q the actual query to be parsed
  * @param int $qi the character index in $q where to start parsing
  * @param int $level the depth from root in the tree (number of opened and unclosed opening brackets)
  */
  protected function parse_expression($q, &$qi, $level, $root)
  {
    $crt_token = "";
    $crt_modifier = 0;
    $crt_scope = null;

    for ($stop=false; !$stop && $qi<strlen($q); $qi++)
    {
      $ch = $q[$qi];
      if ( ($crt_modifier&QST_QUOTED)==0)
      {
        switch ($ch)
        {
          case '(':
            if (strlen($crt_token))
              $this->push($crt_token, $crt_modifier, $crt_scope);
            $sub = new QMultiToken;
            $qi++;
            $sub->parse_expression($q, $qi, $level+1, $root);
            $sub->modifier = $crt_modifier;
            if (isset($crt_scope) && $crt_scope->is_text)
            {
              $sub->apply_scope($crt_scope); // eg. 'tag:(John OR Bill)'
            }
            $this->tokens[] = $sub;
            $crt_modifier = 0;
            $crt_scope = null;
            break;
          case ')':
            if ($level>0)
              $stop = true;
            break;
          case ':':
            $scope = @$root->scopes[strtolower($crt_token)];
            if (!isset($scope) || isset($crt_scope))
            { // white space
              $this->push($crt_token, $crt_modifier, $crt_scope);
            }
            else
            {
              $crt_token = "";
              $crt_scope = $scope;
            }
            break;
          case '"':
            if (strlen($crt_token))
              $this->push($crt_token, $crt_modifier, $crt_scope);
            $crt_modifier |= QST_QUOTED;
            break;
          case '-':
            if (strlen($crt_token) || isset($crt_scope))
              $crt_token .= $ch;
            else
              $crt_modifier |= QST_NOT;
            break;
          case '*':
            if (strlen($crt_token))
              $crt_token .= $ch; // wildcard end later
            else
              $crt_modifier |= QST_WILDCARD_BEGIN;
            break;
          case '.':
            if (isset($crt_scope) && !$crt_scope->is_text)
            {
              $crt_token .= $ch;
              break;
            }
            if (strlen($crt_token) && preg_match('/[0-9]/', substr($crt_token,-1))
              && $qi+1<strlen($q) && preg_match('/[0-9]/', $q[$qi+1]))
            {// dot between digits is not a separator e.g. F2.8
              $crt_token .= $ch;
              break;
            }
            // else white space go on..
          default:
            if (!$crt_scope || !$crt_scope->process_char($ch, $crt_token))
            {
              if (strpos(' ,.;!?', $ch)!==false)
              { // white space
                $this->push($crt_token, $crt_modifier, $crt_scope);
              }
              else
                $crt_token .= $ch;
            }
            break;
        }
      }
      else
      {// quoted
        if ($ch=='"')
        {
          if ($qi+1 < strlen($q) && $q[$qi+1]=='*')
          {
            $crt_modifier |= QST_WILDCARD_END;
            $qi++;
          }
          $this->push($crt_token, $crt_modifier, $crt_scope);
        }
        else
          $crt_token .= $ch;
      }
    }

    $this->push($crt_token, $crt_modifier, $crt_scope);

    for ($i=0; $i<count($this->tokens); $i++)
    {
      $token = $this->tokens[$i];
      $remove = false;
      if ($token->is_single)
      {
        if ( ($token->modifier & QST_QUOTED)==0
          && substr($token->term, -1)=='*' )
        {
          $token->term = rtrim($token->term, '*');
          $token->modifier |= QST_WILDCARD_END;
        }

        if ( !isset($token->scope)
          && ($token->modifier & (QST_QUOTED|QST_WILDCARD))==0 )
        {
          if ('not' == strtolower($token->term))
          {
            if ($i+1 < count($this->tokens))
              $this->tokens[$i+1]->modifier |= QST_NOT;
            $token->term = "";
          }
          if ('or' == strtolower($token->term))
          {
            if ($i+1 < count($this->tokens))
              $this->tokens[$i+1]->modifier |= QST_OR;
            $token->term = "";
          }
          if ('and' == strtolower($token->term))
          {
            $token->term = "";
          }
        }

        if (!strlen($token->term)
          && (!isset($token->scope) || !$token->scope->nullable) )
        {
          $remove = true;
        }

        if ( isset($token->scope)
          && !$token->scope->parse($token))
          $remove = true;
      }
      elseif (!count($token->tokens))
      {
          $remove = true;
      }
      if ($remove)
      {
        array_splice($this->tokens, $i, 1);
        if ($i<count($this->tokens) && $this->tokens[$i]->is_single)
        {
          $this->tokens[$i]->modifier |= QST_BREAK;
        }
        $i--;
      }
    }

    if ($level>0 && count($this->tokens) && $this->tokens[0]->is_single)
    {
      $this->tokens[0]->modifier |= QST_BREAK;
    }
  }

  /**
  * Applies recursively a search scope to all sub single tokens. We allow 'tag:(John Bill)' but we cannot evaluate
  * scopes on expressions so we rewrite as '(tag:John tag:Bill)'
  */
  private function apply_scope(QSearchScope $scope)
  {
    for ($i=0; $i<count($this->tokens); $i++)
    {
      if ($this->tokens[$i]->is_single)
      {
        if (!isset($this->tokens[$i]->scope))
          $this->tokens[$i]->scope = $scope;
      }
      else
        $this->tokens[$i]->apply_scope($scope);
    }
  }

  private static function priority($modifier)
  {
    return $modifier & QST_OR ? 0 :1;
  }

  /* because evaluations occur left to right, we ensure that 'a OR b c d' is interpreted as 'a OR (b c d)'*/
  protected function check_operator_priority()
  {
    for ($i=0; $i<count($this->tokens); $i++)
    {
      if (!$this->tokens[$i]->is_single)
        $this->tokens[$i]->check_operator_priority();
      if ($i==1)
        $crt_prio = self::priority($this->tokens[$i]->modifier);
      if ($i<=1)
        continue;
      $prio = self::priority($this->tokens[$i]->modifier);
      if ($prio > $crt_prio)
      {// e.g. 'a OR b c d' i=2, operator(c)=AND -> prio(AND) > prio(OR) = operator(b)
        $term_count = 2; // at least b and c to be regrouped
        for ($j=$i+1; $j<count($this->tokens); $j++)
        {
          if (self::priority($this->tokens[$j]->modifier) >= $prio)
            $term_count++; // also take d
          else
            break;
        }

        $i--; // move pointer to b
        // crate sub expression (b c d)
        $sub = new QMultiToken;
        $sub->tokens = array_splice($this->tokens, $i, $term_count);

        // rewrite ourseleves as a (b c d)
        array_splice($this->tokens, $i, 0, array($sub));
        $sub->modifier = $sub->tokens[0]->modifier & QST_OR;
        $sub->tokens[0]->modifier &= ~QST_OR;

        $sub->check_operator_priority();
      }
      else
        $crt_prio = $prio;
    }
  }
}

class QExpression extends QMultiToken
{
  var $scopes = array();
  var $stokens = array();
  var $stoken_modifiers = array();

  function __construct($q, $scopes)
  {
    foreach ($scopes as $scope)
    {
      $this->scopes[$scope->id] = $scope;
      foreach ($scope->aliases as $alias)
        $this->scopes[strtolower($alias)] = $scope;
    }
    $i = 0;
    $this->parse_expression($q, $i, 0, $this);
    //manipulate the tree so that 'a OR b c' is the same as 'b c OR a'
    $this->check_operator_priority();
    $this->build_single_tokens($this, 0);
  }

  private function build_single_tokens(QMultiToken $expr, $this_is_not)
  {
    for ($i=0; $i<count($expr->tokens); $i++)
    {
      $token = $expr->tokens[$i];
      $crt_is_not = ($token->modifier ^ $this_is_not) & QST_NOT; // no negation OR double negation -> no negation;

      if ($token->is_single)
      {
        $token->idx = count($this->stokens);
        $this->stokens[] = $token;

        $modifier = $token->modifier;
        if ($crt_is_not)
          $modifier |= QST_NOT;
        else
          $modifier &= ~QST_NOT;
        $this->stoken_modifiers[] = $modifier;
      }
      else
        $this->build_single_tokens($token, $crt_is_not);
    }
  }
}

/**
  Structure of results being filled from different tables
*/
class QResults
{
  var $all_tags;
  var $tag_ids;
  var $tag_iids;
  var $all_cats;
  var $cat_ids;
  var $cat_iids;
  var $images_iids;
  var $iids;
}

function qsearch_get_text_token_search_sql($token, $fields)
{
  global $page;

  $clauses = array();
  $variants = array_merge(array($token->term), $token->variants);
  $fts = array();
  foreach ($variants as $variant)
  {
    $use_ft = mb_strlen($variant)>3;
    if ($token->modifier & QST_WILDCARD_BEGIN)
      $use_ft = false;
    if ($token->modifier & (QST_QUOTED|QST_WILDCARD_END) == (QST_QUOTED|QST_WILDCARD_END))
      $use_ft = false;

    if ($use_ft)
    {
      $max = max( array_map( 'mb_strlen',
        preg_split('/['.preg_quote('-\'!"#$%&()*+,./:;<=>?@[\]^`{|}~','/').']+/', $variant)
        ) );
      if ($max<4)
        $use_ft = false;
    }

    if (!$use_ft)
    {// odd term or too short for full text search; fallback to regex but unfortunately this is diacritic/accent sensitive
      if (!isset($page['use_regexp_ICU']))
      {
        // Prior to MySQL 8.0.4, MySQL used the Henry Spencer regular expression library to support
        // regular expression operations, rather than International Components for Unicode (ICU)
        $page['use_regexp_ICU'] = false;
        $db_version = pwg_get_db_version();
        if (!preg_match('/mariadb/i', $db_version) and version_compare($db_version, '8.0.4', '>'))
        {
          $page['use_regexp_ICU'] = true;
        }
      }

      $pre = ($token->modifier & QST_WILDCARD_BEGIN) ? '' : ($page['use_regexp_ICU'] ? '\\\\b' : '[[:<:]]');
      $post = ($token->modifier & QST_WILDCARD_END) ? '' : ($page['use_regexp_ICU'] ? '\\\\b' : '[[:>:]]');
      foreach( $fields as $field)
        $clauses[] = $field.' REGEXP \''.$pre.addslashes(preg_quote($variant)).$post.'\'';
    }
    else
    {
      $ft = $variant;
      if ($token->modifier & QST_QUOTED)
        $ft = '"'.$ft.'"';
      if ($token->modifier & QST_WILDCARD_END)
        $ft .= '*';
      $fts[] = $ft;
    }
  }

  if (count($fts))
  {
    $clauses[] = 'MATCH('.implode(', ',$fields).') AGAINST( \''.addslashes(implode(' ',$fts)).'\' IN BOOLEAN MODE)';
  }
  return $clauses;
}

function qsearch_get_images(QExpression $expr, QResults $qsr)
{
  $qsr->images_iids = array_fill(0, count($expr->stokens), array());

  $query_base = 'SELECT id from '.IMAGES_TABLE.' i WHERE
';
  for ($i=0; $i<count($expr->stokens); $i++)
  {
    $token = $expr->stokens[$i];
    $scope_id = isset($token->scope) ? $token->scope->id : 'photo';
    $clauses = array();

    $like = addslashes($token->term);
    $like = str_replace( array('%','_'), array('\\%','\\_'), $like); // escape LIKE specials %_
    $file_like = 'CONVERT(file, CHAR) LIKE \'%'.$like.'%\'';

    switch ($scope_id)
    {
      case 'photo':
        $clauses[] = $file_like;
        $clauses = array_merge($clauses, qsearch_get_text_token_search_sql($token, array('name','comment')));
        break;

      case 'file':
        $clauses[] = $file_like;
        break;
      case 'author':
        if ( strlen($token->term) )
          $clauses = array_merge($clauses, qsearch_get_text_token_search_sql($token, array('author')));
        elseif ($token->modifier & QST_WILDCARD)
          $clauses[] = 'author IS NOT NULL';
        else
          $clauses[] = 'author IS NULL';
        break;
      case 'width':
      case 'height':
        $clauses[] = $token->scope->get_sql($scope_id, $token);
        break;
      case 'ratio':
        $clauses[] = $token->scope->get_sql('width/height', $token);
        break;
      case 'size':
        $clauses[] = $token->scope->get_sql('width*height', $token);
        break;
      case 'hits':
        $clauses[] = $token->scope->get_sql('hit', $token);
        break;
      case 'score':
        $clauses[] = $token->scope->get_sql('rating_score', $token);
        break;
      case 'filesize':
        $clauses[] = $token->scope->get_sql('1024*filesize', $token);
        break;
      case 'created':
        $clauses[] = $token->scope->get_sql('date_creation', $token);
        break;
      case 'posted':
        $clauses[] = $token->scope->get_sql('date_available', $token);
        break;
      case 'id':
        $clauses[] = $token->scope->get_sql($scope_id, $token);
        break;
      default:
        // allow plugins to have their own scope with columns added in db by themselves
        $clauses = trigger_change('qsearch_get_images_sql_scopes', $clauses, $token, $expr);
        break;
    }
    if (!empty($clauses))
    {
      $query = $query_base.'('.implode("\n OR ", $clauses).')';
      $qsr->images_iids[$i] = query2array($query,null,'id');
    }
  }
}

function qsearch_get_tags(QExpression $expr, QResults $qsr)
{
  $token_tag_ids = $qsr->tag_iids = array_fill(0, count($expr->stokens), array() );
  $all_tags = array();

  for ($i=0; $i<count($expr->stokens); $i++)
  {
    $token = $expr->stokens[$i];
    if (isset($token->scope) && 'tag' != $token->scope->id)
      continue;
    if (empty($token->term))
      continue;

    $clauses = qsearch_get_text_token_search_sql( $token, array('name'));
    $query = 'SELECT * FROM '.TAGS_TABLE.'
WHERE ('. implode("\n OR ",$clauses) .')';
    $result = pwg_query($query);
    while ($tag = pwg_db_fetch_assoc($result))
    {
      $token_tag_ids[$i][] = $tag['id'];
      $all_tags[$tag['id']] = $tag;
    }
  }

  // check adjacent short words
  for ($i=0; $i<count($expr->stokens)-1; $i++)
  {
    if ( (strlen($expr->stokens[$i]->term)<=3 || strlen($expr->stokens[$i+1]->term)<=3)
      && (($expr->stoken_modifiers[$i] & (QST_QUOTED|QST_WILDCARD)) == 0)
      && (($expr->stoken_modifiers[$i+1] & (QST_BREAK|QST_QUOTED|QST_WILDCARD)) == 0) )
    {
      $common = array_intersect( $token_tag_ids[$i], $token_tag_ids[$i+1] );
      if (count($common))
      {
        $token_tag_ids[$i] = $token_tag_ids[$i+1] = $common;
      }
    }
  }

  // get images
  $positive_ids = $not_ids = array();
  for ($i=0; $i<count($expr->stokens); $i++)
  {
    $tag_ids = $token_tag_ids[$i];
    $token = $expr->stokens[$i];

    if (!empty($tag_ids))
    {
      $query = '
SELECT image_id FROM '.IMAGE_TAG_TABLE.'
  WHERE tag_id IN ('.implode(',',$tag_ids).')
  GROUP BY image_id';
      $qsr->tag_iids[$i] = query2array($query, null, 'image_id');
      if ($expr->stoken_modifiers[$i]&QST_NOT)
        $not_ids = array_merge($not_ids, $tag_ids);
      else
      {
        if (strlen($token->term)>2 || count($expr->stokens)==1 || isset($token->scope) || ($token->modifier&(QST_WILDCARD|QST_QUOTED)) )
        {// add tag ids to list only if the word is not too short (such as de / la /les ...)
          $positive_ids = array_merge($positive_ids, $tag_ids);
        }
      }
    }
    elseif (isset($token->scope) && 'tag' == $token->scope->id && strlen($token->term)==0)
    {
      if ($token->modifier & QST_WILDCARD)
      {// eg. 'tag:*' returns all tagged images
        $qsr->tag_iids[$i] = query2array('SELECT DISTINCT image_id FROM '.IMAGE_TAG_TABLE, null, 'image_id');
      }
      else
      {// eg. 'tag:' returns all untagged images
        $qsr->tag_iids[$i] = query2array('SELECT id FROM '.IMAGES_TABLE.' LEFT JOIN '.IMAGE_TAG_TABLE.' ON id=image_id WHERE image_id IS NULL', null, 'id');
      }
    }
  }

  $all_tags = array_intersect_key($all_tags, array_flip( array_diff($positive_ids, $not_ids) ) );
  usort($all_tags, 'tag_alpha_compare');
  foreach ( $all_tags as &$tag )
  {
    $tag['name'] = trigger_change('render_tag_name', $tag['name'], $tag);
  }
  $qsr->all_tags = $all_tags;
  $qsr->tag_ids = $token_tag_ids;
}

function qsearch_get_categories(QExpression $expr, QResults $qsr)
{
  global $user, $conf;

  $token_cat_ids = $qsr->cat_iids = array_fill(0, count($expr->stokens), array() );
  $all_cats = array();

  for ($i=0; $i<count($expr->stokens); $i++)
  {
    $token = $expr->stokens[$i];
    if (isset($token->scope) && 'category' != $token->scope->id) // not relevant yet
      continue;
    if (empty($token->term))
      continue;

    $clauses = qsearch_get_text_token_search_sql( $token, array('name', 'comment'));
    $query = '
SELECT
    *
  FROM '.CATEGORIES_TABLE.'
    INNER JOIN '.USER_CACHE_CATEGORIES_TABLE.' ON id = cat_id and user_id = '.$user['id'].'
  WHERE ('. implode("\n OR ",$clauses) .')';
    $result = pwg_query($query);
    while ($cat = pwg_db_fetch_assoc($result))
    {
      $token_cat_ids[$i][] = $cat['id'];
      $all_cats[$cat['id']] = $cat;
    }
  }

  // check adjacent short words
  for ($i=0; $i<count($expr->stokens)-1; $i++)
  {
    if ( (strlen($expr->stokens[$i]->term)<=3 || strlen($expr->stokens[$i+1]->term)<=3)
      && (($expr->stoken_modifiers[$i] & (QST_QUOTED|QST_WILDCARD)) == 0)
      && (($expr->stoken_modifiers[$i+1] & (QST_BREAK|QST_QUOTED|QST_WILDCARD)) == 0) )
    {
      $common = array_intersect( $token_cat_ids[$i], $token_cat_ids[$i+1] );
      if (count($common))
      {
        $token_cat_ids[$i] = $token_cat_ids[$i+1] = $common;
      }
    }
  }

  // get images
  $positive_ids = $not_ids = array();
  for ($i=0; $i<count($expr->stokens); $i++)
  {
    $cat_ids = $token_cat_ids[$i];
    $token = $expr->stokens[$i];

    if (!empty($cat_ids))
    {
      if ($conf['quick_search_include_sub_albums'])
      {
        $query = '
SELECT
    id
  FROM '.CATEGORIES_TABLE.'
    INNER JOIN '.USER_CACHE_CATEGORIES_TABLE.' ON id = cat_id and user_id = '.$user['id'].'
  WHERE id IN ('.implode(',', get_subcat_ids($cat_ids)) .')
;';
        $cat_ids = query2array($query, null, 'id');
      }

      $query = '
SELECT image_id FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id IN ('.implode(',',$cat_ids).')
  GROUP BY image_id';
      $qsr->cat_iids[$i] = query2array($query, null, 'image_id');
      if ($expr->stoken_modifiers[$i]&QST_NOT)
        $not_ids = array_merge($not_ids, $cat_ids);
      else
      {
        if (strlen($token->term)>2 || count($expr->stokens)==1 || isset($token->scope) || ($token->modifier&(QST_WILDCARD|QST_QUOTED)) )
        {// add cat ids to list only if the word is not too short (such as de / la /les ...)
          $positive_ids = array_merge($positive_ids, $cat_ids);
        }
      }
    }
    elseif (isset($token->scope) && 'category' == $token->scope->id && strlen($token->term)==0)
    {
      if ($token->modifier & QST_WILDCARD)
      {// eg. 'category:*' returns all images associated to an album
        $qsr->cat_iids[$i] = query2array('SELECT DISTINCT image_id FROM '.IMAGE_CATEGORY_TABLE, null, 'image_id');
      }
      else
      {// eg. 'category:' returns all orphan images
        $qsr->cat_iids[$i] = query2array('SELECT id FROM '.IMAGES_TABLE.' LEFT JOIN '.IMAGE_CATEGORY_TABLE.' ON id=image_id WHERE image_id IS NULL', null, 'id');
      }
    }
  }

  $all_cats = array_intersect_key($all_cats, array_flip( array_diff($positive_ids, $not_ids) ) );
  usort($all_cats, 'tag_alpha_compare');
  foreach ( $all_cats as &$cat )
  {
    $cat['name'] = trigger_change('render_category_name', $cat['name'], $cat);
  }
  $qsr->all_cats = $all_cats;
  $qsr->cat_ids = $token_cat_ids;
}


function qsearch_eval(QMultiToken $expr, QResults $qsr, &$qualifies, &$ignored_terms)
{
  $qualifies = false; // until we find at least one positive term
  $ignored_terms = array();

  $ids = $not_ids = array();

  for ($i=0; $i<count($expr->tokens); $i++)
  {
    $crt = $expr->tokens[$i];
    if ($crt->is_single)
    {
      $crt_ids = $qsr->iids[$crt->idx] = array_unique(
        array_merge(
          $qsr->images_iids[$crt->idx],
          $qsr->cat_iids[$crt->idx],
          $qsr->tag_iids[$crt->idx]
          )
        );
      $crt_qualifies = count($crt_ids)>0 || count($qsr->tag_ids[$crt->idx])>0;
      $crt_ignored_terms = $crt_qualifies ? array() : array((string)$crt);
    }
    else
      $crt_ids = qsearch_eval($crt, $qsr, $crt_qualifies, $crt_ignored_terms);

    $modifier = $crt->modifier;
    if ($modifier & QST_NOT)
      $not_ids = array_unique( array_merge($not_ids, $crt_ids));
    else
    {
      $ignored_terms = array_merge($ignored_terms, $crt_ignored_terms);
      if ($modifier & QST_OR)
      {
        $ids = array_unique( array_merge($ids, $crt_ids) );
        $qualifies |= $crt_qualifies;
      }
      elseif ($crt_qualifies)
      {
        if ($qualifies)
          $ids = array_intersect($ids, $crt_ids);
        else
          $ids = $crt_ids;
        $qualifies = true;
      }
    }
  }

  if (count($not_ids))
    $ids = array_diff($ids, $not_ids);
  return $ids;
}


/**
 * Returns the search results corresponding to a quick/query search.
 * A quick/query search returns many items (search is not strict), but results
 * are sorted by relevance unless $super_order_by is true. Returns:
 *  array (
 *    'items' => array of matching images
 *    'qs'    => array(
 *      'unmatched_terms' => array of terms from the input string that were not matched
 *      'matching_tags' => array of matching tags
 *      'matching_cats' => array of matching categories
 *      'matching_cats_no_images' =>array(99) - matching categories without images
 *      )
 *    )
 *
 * @param string $q
 * @param bool $super_order_by
 * @param string $images_where optional additional restriction on images table
 * @return array
 */
function get_quick_search_results($q, $options)
{
  global $persistent_cache, $conf, $user;

  $cache_key = $persistent_cache->make_key( array(
    strtolower($q),
    $conf['order_by'],
    $user['id'],$user['cache_update_time'],
    isset($options['permissions']) ? (boolean)$options['permissions'] : true,
    isset($options['images_where']) ? $options['images_where'] : '',
    ) );
  if ($persistent_cache->get($cache_key, $res))
  {
    return $res;
  }

  $res = get_quick_search_results_no_cache($q, $options);

  if ( count($res['items']) )
  {// cache the results only if not empty - otherwise it is useless
    $persistent_cache->set($cache_key, $res, 300);
  }
  return $res;
}

/**
 * @see get_quick_search_results but without result caching
 */
function get_quick_search_results_no_cache($q, $options)
{
  global $conf;

  $q = trim(stripslashes($q));
  $search_results =
    array(
      'items' => array(),
      'qs' => array('q'=>$q),
    );

  $q = trigger_change('qsearch_pre', $q);

  $scopes = array();
  $scopes[] = new QSearchScope('tag', array('tags'));
  $scopes[] = new QSearchScope('photo', array('photos'));
  $scopes[] = new QSearchScope('file', array('filename'));
  $scopes[] = new QSearchScope('author', array(), true);
  $scopes[] = new QNumericRangeScope('width', array());
  $scopes[] = new QNumericRangeScope('height', array());
  $scopes[] = new QNumericRangeScope('ratio', array(), false, 0.001);
  $scopes[] = new QNumericRangeScope('size', array());
  $scopes[] = new QNumericRangeScope('filesize', array());
  $scopes[] = new QNumericRangeScope('hits', array('hit', 'visit', 'visits'));
  $scopes[] = new QNumericRangeScope('score', array('rating'), true);
  $scopes[] = new QNumericRangeScope('id', array());

  $createdDateAliases = array('taken', 'shot');
  $postedDateAliases = array('added');
  if ($conf['calendar_datefield'] == 'date_creation')
    $createdDateAliases[] = 'date';
  else
    $postedDateAliases[] = 'date';
  $scopes[] = new QDateRangeScope('created', $createdDateAliases, true);
  $scopes[] = new QDateRangeScope('posted', $postedDateAliases);

  // allow plugins to add their own scopes
  $scopes = trigger_change('qsearch_get_scopes', $scopes);
  $expression = new QExpression($q, $scopes);

  // get inflections for terms
  $inflector = null;
  $lang_code = substr(get_default_language(),0,2);
  @include_once(PHPWG_ROOT_PATH.'include/inflectors/'.$lang_code.'.php');
  $class_name = 'Inflector_'.$lang_code;
  if (class_exists($class_name))
  {
    $inflector = new $class_name;
    foreach( $expression->stokens as $token)
    {
      if (isset($token->scope) && !$token->scope->is_text)
        continue;
      if (strlen($token->term)>2
        && ($token->modifier & (QST_QUOTED|QST_WILDCARD))==0
        && strcspn($token->term, '\'0123456789') == strlen($token->term) )
      {
        $token->variants = array_unique( array_diff( $inflector->get_variants($token->term), array($token->term) ) );
      }
    }
  }


  trigger_notify('qsearch_expression_parsed', $expression);
//var_export($expression);

  if (count($expression->stokens)==0)
  {
    return $search_results;
  }
  $qsr = new QResults;
  qsearch_get_tags($expression, $qsr);
  qsearch_get_categories($expression, $qsr);
  qsearch_get_images($expression, $qsr);

  // allow plugins to evaluate their own scopes
  trigger_notify('qsearch_before_eval', $expression, $qsr);

  $ids = qsearch_eval($expression, $qsr, $tmp, $search_results['qs']['unmatched_terms']);

  $debug[] = "<!--\nparsed: ".htmlspecialchars($expression);
  $debug[] = count($expression->stokens).' tokens';
  for ($i=0; $i<count($expression->stokens); $i++)
  {
    $debug[] = htmlspecialchars($expression->stokens[$i]).': '.count($qsr->tag_ids[$i]).' tags, '.count($qsr->tag_iids[$i]).' tiids, '.count($qsr->images_iids[$i]).' iiids, '.count($qsr->iids[$i]).' iids'
      .' modifier:'.dechex($expression->stoken_modifiers[$i])
      .( !empty($expression->stokens[$i]->variants) ? ' variants: '.htmlspecialchars(implode(', ',$expression->stokens[$i]->variants)): '');
  }
  $debug[] = 'before perms '.count($ids);

  $search_results['qs']['matching_tags'] = $qsr->all_tags;
  $search_results['qs']['matching_cats'] = $qsr->all_cats;
  $search_results = trigger_change('qsearch_results', $search_results, $expression, $qsr);
  if (isset($search_results['items']))
  {
    $ids = array_merge($ids, $search_results['items']);
  }
  
  global $template;

  if (empty($ids))
  {
    $debug[] = '-->';
    $template->append('footer_elements', implode("\n", $debug) );
    return $search_results;
  }

  $permissions = !isset($options['permissions']) ? true : $options['permissions'];

  $where_clauses = array();
  $where_clauses[]='i.id IN ('. implode(',', $ids) . ')';
  if (!empty($options['images_where']))
  {
    $where_clauses[]='('.$options['images_where'].')';
  }
  if ($permissions)
  {
    $where_clauses[] = get_sql_condition_FandF(
        array
          (
            'forbidden_categories' => 'category_id',
            'forbidden_images' => 'i.id'
          ),
        null,true
      );
  }

  $query = '
SELECT DISTINCT(id) FROM '.IMAGES_TABLE.' i';
  if ($permissions)
  {
    $query .= '
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id';
  }
  $query .= '
  WHERE '.implode("\n AND ", $where_clauses)."\n".
  $conf['order_by'];

  $ids = query2array($query, null, 'id');

  $debug[] = count($ids).' final photo count -->';
  $template->append('footer_elements', implode("\n", $debug) );

  $search_results['items'] = $ids;
  return $search_results;
}

/**
 * Returns an array of 'items' corresponding to the search id.
 * It can be either a quick search or a regular search.
 *
 * @param int $search_id
 * @param bool $super_order_by
 * @param string $images_where optional aditional restriction on images table
 * @return array
 */
function get_search_results($search_id, $super_order_by, $images_where='')
{
  $search = get_search_array($search_id);
  if ( !isset($search['q']) )
  {
    return get_regular_search_results($search, $images_where);
  }
  else
  {
    return get_quick_search_results($search['q'], array('super_order_by'=>$super_order_by, 'images_where'=>$images_where) );
  }
}

function split_allwords($raw_allwords)
{
  $words = null;

  // we specify the list of characters to trim, to add the ".". We don't want to split words
  // on "." but on ". ", and we have to deal with trailing dots.
  $raw_allwords = trim($raw_allwords, " \n\r\t\v\x00.");

  if (!preg_match('/^\s*$/', $raw_allwords))
  {
    $drop_char_match   = array(';','&','(',')','<','>','`','\'','"','|',',','@','?','%','. ','[',']','{','}',':','\\','/','=','\'','!','*');
    $drop_char_replace = array(' ',' ',' ',' ',' ',' ', '', '', ' ',' ',' ',' ',' ',' ',' ' ,' ',' ',' ',' ',' ','' , ' ',' ',' ', ' ',' ');

    // Split words
    $words = array_unique(
      preg_split(
        '/\s+/',
        str_replace(
          $drop_char_match,
          $drop_char_replace,
          $raw_allwords
        )
      )
    );
  }

  return $words;
}

function get_available_search_uuid()
{
  $candidate = 'psk-'.date('Ymd').'-'.generate_key(10);

  $query = '
SELECT
    COUNT(*)
  FROM '.SEARCH_TABLE.'
  WHERE search_uuid = \''.$candidate.'\'
;';
  list($counter) = pwg_db_fetch_row(pwg_query($query));
  if (0 == $counter)
  {
    return $candidate;
  }
  else
  {
    return get_available_search_uuid();
  }
}

function save_search($rules, $forked_from=null)
{
  global $user;

  list($dbnow) = pwg_db_fetch_row(pwg_query('SELECT NOW()'));
  $search_uuid = get_available_search_uuid();

  single_insert(
    SEARCH_TABLE,
    array(
      'rules' => pwg_db_real_escape_string(serialize($rules)),
      'created_on' => $dbnow,
      'created_by' => $user['user_id'],
      'search_uuid' => $search_uuid,
      'forked_from' => $forked_from,
    )
  );

  if (!is_a_guest() and !is_generic())
  {
    userprefs_update_param('gallery_search_filters', array_keys($rules['fields'] ?? array()));
  }

  $url = make_index_url(
    array(
      'section' => 'search',
      'search'  => $search_uuid,
    )
  );

  return array($search_uuid, $url);
}

?>
