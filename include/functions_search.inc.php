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

/**
 * returns the LIKE sql clause corresponding to the quick search query $q
 * and the field $field. example q="john bill", field="file" will return
 * file LIKE "%john%" OR file LIKE "%bill%". Special characters for MySql
 * full text search (+,<,>) are omitted.
 * @param string q
 * @param string field
 * @return string
 */
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
 * returns the search results (array of image ids) corresponding to a
 * quick/query search. A quick/query search returns many items (search is
 * not strict), but results are sorted by relevance.
 *
 * @param string q
 * @return array
 */
function get_quick_search_results($q)
{
  global $page;
  $search_results = array();
  $q = trim($q);
  if (empty($q))
  {
    $search_results['items'] = array();
    return $search_results;
  }
  // prepare the big join on images, comments and categories
  $query = '
SELECT
  i.id, CAST( CONCAT_WS(" ",
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
'.get_sql_condition_FandF
  (
    array
      (
        'forbidden_categories' => 'category_id',
        'visible_categories' => 'category_id',
        'visible_images' => 'i.id'
      ),
    'WHERE'
  ).'
GROUP BY i.id';

  $query = 'SELECT id, MATCH(ft) AGAINST( "'.$q.'" IN BOOLEAN MODE) AS q FROM ('.$query.') AS Y
WHERE MATCH(ft) AGAINST( "'.$q.'" IN BOOLEAN MODE)';

  $by_weights=array();
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  { // weight is important when sorting images by relevance
    if ($row['q'])
    {
      $by_weights[(int)$row['id']] =  2*$row['q'];
    }
  }

  $permissions_checked = true;
  // now search the file name separately (not done in full text because slower 
  // and the filename in pwg doesn't have spaces so full text is meaningless )
  $q_like_clause = get_qsearch_like_clause($q, 'file' );
  if (!empty($q_like_clause))
  {
    $query = '
SELECT id
  FROM '.IMAGES_TABLE.'
  WHERE '.$q_like_clause.
      get_sql_condition_FandF
      (
        array
          (
            'visible_images' => 'id'
          ),
        'AND'
      );
    $result = pwg_query($query);
    while ($row = mysql_fetch_assoc($result))
    { // weight is important when sorting images by relevance
      $id=(int)$row['id'];
      @$by_weights[$id] += 2;
      $permissions_checked = false;
    }
  }

  // now search tag names corresponding to the query $q. we could have searched
  // tags earlier during the big join, but for the sake of the performance and
  // because tags have only a simple name we do it separately
  $q_like_clause = get_qsearch_like_clause($q, 'CONVERT(name, CHAR)' );
  if (!empty($q_like_clause))
  {
    $query = '
SELECT id
  FROM '.TAGS_TABLE.'
  WHERE '.$q_like_clause;
    $tag_ids = array_from_query( $query, 'id');
    if (!empty($tag_ids))
    { // we got some tags
      $query = '
SELECT image_id, COUNT(tag_id) AS q
  FROM '.IMAGE_TAG_TABLE.'
  WHERE tag_id IN ('.implode(',',$tag_ids).')
  GROUP BY image_id';
      $result = pwg_query($query);
      while ($row = mysql_fetch_assoc($result))
      { // weight is important when sorting images by relevance
        $image_id=(int)$row['image_id'];
        @$by_weights[$image_id] += $row['q'];
        $permissions_checked = false;
      }
    }
  }

  //at this point, found images might contain images not allowed for the user
  if ( !$permissions_checked
       and !empty($by_weights)
       and !isset($page['super_order_by']) )
  {
    // before returning the result "as is", make sure the user has the
    // permissions for every item
    $query = '
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE id IN ('.implode(',', array_keys($by_weights) ).')
'.get_sql_condition_FandF
  (
    array
      (
        'forbidden_categories' => 'category_id',
        'visible_categories' => 'category_id',
        'visible_images' => 'id'
      ),
    'AND'
  );
    $allowed_image_ids = array_from_query( $query, 'id');
    $by_weights = array_intersect_key($by_weights, array_flip($allowed_image_ids));
    $permissions_checked = true;
  }
  arsort($by_weights, SORT_NUMERIC);
  if ( $permissions_checked )
  {
    $search_results['as_is']=1;
  }
  
  $search_results['items'] = array_keys($by_weights);
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