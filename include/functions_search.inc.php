<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
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


/**
 * Prepends and appends a string at each value of the given array.
 *
 * @param array
 * @param string prefix to each array values
 * @param string suffix to each array values
 */
function prepend_append_array_items($array, $prepend_str, $append_str)
{
  array_walk(
    $array,
    create_function('&$s', '$s = "'.$prepend_str.'".$s."'.$append_str.'";')
    );

  return $array;
}

/**
 * returns search rules stored into a serialized array in "search"
 * table. Each search rules set is numericaly identified.
 *
 * @param int search_id
 * @return array
 */
function get_search_array($search_id)
{
  if (!is_numeric($search_id))
  {
    die('Search id must be an integer');
  }

  $query = '
SELECT rules
  FROM '.SEARCH_TABLE.'
  WHERE id = '.$search_id.'
;';
  list($serialized_rules) = mysql_fetch_row(pwg_query($query));

  return unserialize($serialized_rules);
}

/**
 * returns the SQL clause from a search identifier
 *
 * Search rules are stored in search table as a serialized array. This array
 * need to be transformed into an SQL clause to be used in queries.
 *
 * @param array search
 * @return string
 */
function get_sql_search_clause($search)
{
  // SQL where clauses are stored in $clauses array during query
  // construction
  $clauses = array();

  foreach (array('file','name','comment','author') as $textfield)
  {
    if (isset($search['fields'][$textfield]))
    {
      $local_clauses = array();
      foreach ($search['fields'][$textfield]['words'] as $word)
      {
        array_push($local_clauses, $textfield." LIKE '%".$word."%'");
      }

      // adds brackets around where clauses
      $local_clauses = prepend_append_array_items($local_clauses, '(', ')');

      array_push(
        $clauses,
        implode(
          ' '.$search['fields'][$textfield]['mode'].' ',
          $local_clauses
          )
        );
    }
  }

  if (isset($search['fields']['allwords']))
  {
    $fields = array('file', 'name', 'comment', 'author');
    // in the OR mode, request bust be :
    // ((field1 LIKE '%word1%' OR field2 LIKE '%word1%')
    // OR (field1 LIKE '%word2%' OR field2 LIKE '%word2%'))
    //
    // in the AND mode :
    // ((field1 LIKE '%word1%' OR field2 LIKE '%word1%')
    // AND (field1 LIKE '%word2%' OR field2 LIKE '%word2%'))
    $word_clauses = array();
    foreach ($search['fields']['allwords']['words'] as $word)
    {
      $field_clauses = array();
      foreach ($fields as $field)
      {
        array_push($field_clauses, $field." LIKE '%".$word."%'");
      }
      // adds brackets around where clauses
      array_push(
        $word_clauses,
        implode(
          "\n          OR ",
          $field_clauses
          )
        );
    }

    array_walk(
      $word_clauses,
      create_function('&$s','$s="(".$s.")";')
      );

    array_push(
      $clauses,
      "\n         ".
      implode(
        "\n         ".
              $search['fields']['allwords']['mode'].
        "\n         ",
        $word_clauses
        )
      );
  }

  foreach (array('date_available', 'date_creation') as $datefield)
  {
    if (isset($search['fields'][$datefield]))
    {
      array_push(
        $clauses,
        $datefield." = '".$search['fields'][$datefield]['date']."'"
        );
    }

    foreach (array('after','before') as $suffix)
    {
      $key = $datefield.'-'.$suffix;

      if (isset($search['fields'][$key]))
      {
        array_push(
          $clauses,

          $datefield.
          ($suffix == 'after'             ? ' >' : ' <').
          ($search['fields'][$key]['inc'] ? '='  : '').
          " '".$search['fields'][$key]['date']."'"

          );
      }
    }
  }

  if (isset($search['fields']['cat']))
  {
    if ($search['fields']['cat']['sub_inc'])
    {
      // searching all the categories id of sub-categories
      $cat_ids = get_subcat_ids($search['fields']['cat']['words']);
    }
    else
    {
      $cat_ids = $search['fields']['cat']['words'];
    }

    $local_clause = 'category_id IN ('.implode(',', $cat_ids).')';
    array_push($clauses, $local_clause);
  }

  // adds brackets around where clauses
  $clauses = prepend_append_array_items($clauses, '(', ')');

  $where_separator =
    implode(
      "\n    ".$search['mode'].' ',
      $clauses
      );

  $search_clause = $where_separator;

  return $search_clause;
}

/**
 * returns the list of items corresponding to the advanced search array
 *
 * @param array search
 * @return array
 */
function get_regular_search_results($search)
{
  $items = array();

  $search_clause = get_sql_search_clause($search);

  if (!empty($search_clause))
  {
    $query = '
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE '.$search_clause.'
;';
    $items = array_from_query($query, 'id');
  }

  $search = get_search_array($search_id);

  if (isset($search['fields']['tags']))
  {
    $tag_items = get_image_ids_for_tags(
      $search['fields']['tags']['words'],
      $search['fields']['tags']['mode']
      );

    switch ($search['mode'])
    {
      case 'AND':
      {
        if (empty($search_clause))
        {
          $items = $tag_items;
        }
        else
        {
          $items = array_intersect($items, $tag_items);
        }
        break;
      }
      case 'OR':
      {
        $items = array_unique(
          array_merge(
            $items,
            $tag_items
            )
          );
        break;
      }
    }
  }

  return $items;
}


if (!function_exists('array_intersect_key')) {
   function array_intersect_key()
   {
       $arrs = func_get_args();
       $result = array_shift($arrs);
       foreach ($arrs as $array) {
           foreach ($result as $key => $v) {
               if (!array_key_exists($key, $array)) {
                   unset($result[$key]);
               }
           }
       }
       return $result;
   }
}


function get_qsearch_like_clause($q, $field)
{
  $tokens = preg_split('/[\s,.;!\?]+/', $q);
  for ($i=0; $i<count($tokens); $i++)
  {
    $tokens[$i]=str_replace('*','%', $tokens[$i]);
    if (preg_match('/^[+<>]/',$tokens[$i]) )
      $tokens[$i]=substr($tokens[$i], 1);
    else if (substr($tokens[$i], 0, 1)=='-')
    {
      unset($tokens[$i]);
      $i--;
    }
  }

  if (!empty($tokens))
  {
    $query = '(';
    for ($i=0; $i<count($tokens); $i++)
    {
      if ($i>0) $query .= 'OR ';
      $query .= ' '.$field.' LIKE "%'.$tokens[$i].'%" ';
    }
    $query .= ')';
    return $query;
  }
  return null;
}


/**
 * returns the search results corresponding to a quick search
 *
 * @param string q
 * @return array
 */
function get_quick_search_results($q)
{
  global $user, $page;
  $search_results = array();

  $q_like_clause = get_qsearch_like_clause($q, 'CONVERT(name, CHAR)' );
  $by_tag_weights=array();
  if (!empty($q_like_clause))
  {
    $query = '
SELECT id
  FROM '.TAGS_TABLE.'
  WHERE '.$q_like_clause;
    $tag_ids = array_from_query( $query, 'id');
    if (!empty($tag_ids))
    {
      $query = '
SELECT image_id, COUNT(tag_id) AS q
  FROM '.IMAGE_TAG_TABLE.'
  WHERE tag_id IN ('.implode(',',$tag_ids).')
  GROUP BY image_id';
      $result = pwg_query($query);
      while ($row = mysql_fetch_array($result))
      {
        $by_tag_weights[(int)$row['image_id']] = $row['q'];
      }
    }
  }

  $query = '
SELECT
  i.id, i.file, CAST( CONCAT_WS(" ",
    IFNULL(i.name,""),
    IFNULL(i.comment,""),
    IFNULL(GROUP_CONCAT(DISTINCT co.content),""),
    IFNULL(GROUP_CONCAT(DISTINCT c.dir),""),
    IFNULL(GROUP_CONCAT(DISTINCT c.name),""),
    IFNULL(GROUP_CONCAT(DISTINCT c.comment),"") ) AS CHAR) AS ft
FROM (
  (
    '.IMAGES_TABLE.' i LEFT JOIN '.COMMENTS_TABLE.' co on i.id=co.image_id
  )
    INNER JOIN
  '.IMAGE_CATEGORY_TABLE.' ic on ic.image_id=i.id
  )
    INNER JOIN
  '.CATEGORIES_TABLE.' c on c.id=ic.category_id
WHERE category_id NOT IN ('.$user['forbidden_categories'].')
GROUP BY i.id';

  $query = 'SELECT id, MATCH(ft) AGAINST( "'.$q.'" IN BOOLEAN MODE) AS q FROM ('.$query.') AS Y
WHERE MATCH(ft) AGAINST( "'.$q.'" IN BOOLEAN MODE)';

  $q_like_clause = get_qsearch_like_clause($q, 'file' );
  if (! empty($q_like_clause) )
  {
    $query .= ' OR '.$q_like_clause;
  }

  $by_weights=array();
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    $by_weights[(int)$row['id']] = $row['q'] ? $row['q'] : 0;
  }

  foreach ( $by_weights as $image=>$w )
  {
    $by_tag_weights[$image] = 2*$w+ (isset($by_tag_weights[$image])?$by_tag_weights[$image]:0);
  }

  if ( empty($by_tag_weights) or isset($page['super_order_by']) )
  {
    if (! isset($page['super_order_by']) )
    {
      arsort($by_tag_weights, SORT_NUMERIC);
      $search_results['as_is']=1;
    }
    $search_results['items'] = array_keys($by_tag_weights);
  }
  else
  {
    $query = '
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE id IN ('.implode(',', array_keys($by_tag_weights) ).')
    AND category_id NOT IN ('.$user['forbidden_categories'].')';

    $allowed_image_ids = array_from_query( $query, 'id');
    $by_tag_weights = array_intersect_key($by_tag_weights, array_flip($allowed_image_ids));
    arsort($by_tag_weights, SORT_NUMERIC);
    $search_results = array(
          'items'=>array_keys($by_tag_weights),
          'as_is'=>1
        );
  }
  return $search_results;
}

/**
 * returns an array of 'items' corresponding to the search id
 *
 * @param int search id
 * @return array
 */
function get_search_results($search_id)
{
  $search = get_search_array($search_id);
  if ( !isset($search['q']) )
  {
    $result['items'] = get_regular_search_results($search);
    return $result;
  }
  else
  {
    return get_quick_search_results($search['q']);
  }
}
?>