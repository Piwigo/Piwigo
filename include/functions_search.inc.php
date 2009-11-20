<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
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
  list($serialized_rules) = pwg_db_fetch_row(pwg_query($query));

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
function get_regular_search_results($search, $images_where)
{
  global $conf;
  $forbidden = get_sql_condition_FandF(
        array
          (
            'forbidden_categories' => 'category_id',
            'visible_categories' => 'category_id',
            'visible_images' => 'id'
          ),
        "\n  AND"
    );

  $items = array();
  $tag_items = array();

  if (isset($search['fields']['tags']))
  {
    $tag_items = get_image_ids_for_tags(
      $search['fields']['tags']['words'],
      $search['fields']['tags']['mode']
      );
  }

  $search_clause = get_sql_search_clause($search);

  if (!empty($search_clause))
  {
    $query = '
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.' i
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE '.$search_clause;
    if (!empty($images_where))
    {
      $query .= "\n  AND ".$images_where;
    }
    if (empty($tag_items) or $search['mode']=='AND')
    { // directly use forbidden and order by
      $query .= $forbidden.'
  '.$conf['order_by'];
    }
    $items = array_from_query($query, 'id');
  }

  if ( !empty($tag_items) )
  {
    $need_permission_check = false;
    switch ($search['mode'])
    {
      case 'AND':
        if (empty($search_clause))
        {
          $need_permission_check = true;
          $items = $tag_items;
        }
        else
        {
          $items = array_intersect($items, $tag_items);
        }
        break;
      case 'OR':
        $before_count = count($items);
        $items = array_unique(
          array_merge(
            $items,
            $tag_items
            )
          );
        if ( $before_count < count($items) )
        {
          $need_permission_check = true;
        }
        break;
    }
    if ($need_permission_check and count($items) )
    {
      $query = '
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.' i
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE id IN ('.implode(',', $items).') '.$forbidden;
      if (!empty($images_where))
      {
        $query .= "\n  AND ".$images_where;
      }
      $query .= '
  '.$conf['order_by'];
      $items = array_from_query($query, 'id');
    }
  }

  return $items;
}

/**
 * returns the LIKE sql clause corresponding to the quick search query $q
 * and the field $field. example q='john bill', field='file' will return
 * file LIKE '%john%' OR file LIKE '%bill%'. Special characters for MySql full
 * text search (+,<,>,~) are omitted. The query can contain a phrase:
 * 'Pierre "New York"' will return LIKE '%Pierre%' OR LIKE '%New York%'.
 * @param string q
 * @param string field
 * @return string
 */
function get_qsearch_like_clause($q, $field, $before='%', $after='%')
{
  $q = stripslashes($q);
  $tokens = array();
  $token_modifiers = array();
  $crt_token = "";
  $crt_token_modifier = "";
  $state = 0;

  for ($i=0; $i<strlen($q); $i++)
  {
    $ch = $q[$i];
    switch ($state)
    {
      case 0:
        if ($ch=='"')
        {
          if (strlen($crt_token))
          {
            $tokens[] = $crt_token;
            $token_modifiers[] = $crt_token_modifier;
            $crt_token = "";
            $crt_token_modifier = "";
          }
          $state=1;
        }
        elseif ( $ch=='*' )
        { // wild card
          $crt_token .= '%';
        }
        elseif ( strcspn($ch, '+-><~')==0 )
        { //special full text modifier
          if (strlen($crt_token))
          {
            $tokens[] = $crt_token;
            $token_modifiers[] = $crt_token_modifier;
            $crt_token = "";
            $crt_token_modifier = "";
          }
          $crt_token_modifier .= $ch;
        }
        elseif (preg_match('/[\s,.;!\?]+/', $ch))
        { // white space
          if (strlen($crt_token))
          {
            $tokens[] = $crt_token;
            $token_modifiers[] = $crt_token_modifier;
            $crt_token = "";
            $crt_token_modifier = "";
          }
        }
        else
        {
          if ( strcspn($ch, '%_')==0)
          {// escape LIKE specials %_
            $ch = '\\'.$ch;
          }
          $crt_token .= $ch;
        }
        break;
      case 1: // qualified with quotes
        switch ($ch)
        {
          case '"':
            $tokens[] = $crt_token;
            $token_modifiers[] = $crt_token_modifier;
            $crt_token = "";
            $crt_token_modifier = "";
            $state=0;
            break;
          default:
            if ( strcspn($ch, '%_')==0)
            {// escape LIKE specials %_
            	$ch = '\\'.$ch;
            }
            $crt_token .= $ch;
        }
        break;
    }
  }
  if (strlen($crt_token))
  {
    $tokens[] = $crt_token;
    $token_modifiers[] = $crt_token_modifier;
  }

  $clauses = array();
  for ($i=0; $i<count($tokens); $i++)
  {
    $tokens[$i] = trim($tokens[$i], '%');
    if (strstr($token_modifiers[$i], '-')!==false)
      continue;
    if ( strlen($tokens[$i])==0)
      continue;
    $clauses[] = $field.' LIKE "'.$before.addslashes($tokens[$i]).$after.'"';
  }

  return count($clauses) ? '('.implode(' OR ', $clauses).')' : null;
}


/**
 * returns the search results corresponding to a quick/query search.
 * A quick/query search returns many items (search is not strict), but results
 * are sorted by relevance unless $super_order_by is true. Returns:
 * array (
 * 'items' => array(85,68,79...)
 * 'qs'    => array(
 *    'matching_tags' => array of matching tags
 *    'matching_cats' => array of matching categories
 *    'matching_cats_no_images' =>array(99) - matching categories without images
 *      ))
 *
 * @param string q
 * @param bool super_order_by
 * @param string images_where optional aditional restriction on images table
 * @return array
 */
function get_quick_search_results($q, $super_order_by, $images_where='')
{
  $search_results =
    array(
      'items' => array(),
      'qs' => array('q'=>stripslashes($q)),
    );
  $q = trim($q);
  if (empty($q))
  {
    return $search_results;
  }
  $q_like_field = '@@__db_field__@@'; //something never in a search
  $q_like_clause = get_qsearch_like_clause($q, $q_like_field );


  // Step 1 - first we find matches in #images table ===========================
  $where_clauses='MATCH(i.name, i.comment) AGAINST( "'.$q.'" IN BOOLEAN MODE)';
  if (!empty($q_like_clause))
  {
    $where_clauses .= '
    OR '. str_replace($q_like_field, 'CONVERT(file, CHAR)', $q_like_clause);
    $where_clauses = '('.$where_clauses.')';
  }
  $where_clauses = array($where_clauses);
  if (!empty($images_where))
  {
    $where_clauses[]='('.$images_where.')';
  }
  $where_clauses[] .= get_sql_condition_FandF
      (
        array( 'visible_images' => 'i.id' ), null, true
      );
  $query = '
SELECT i.id,
    MATCH(i.name, i.comment) AGAINST( "'.$q.'" IN BOOLEAN MODE) AS weight
  FROM '.IMAGES_TABLE.' i
  WHERE '.implode("\n AND ", $where_clauses);

  $by_weights=array();
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  { // weight is important when sorting images by relevance
    if ($row['weight'])
    {
      $by_weights[(int)$row['id']] =  2*$row['weight'];
    }
    else
    {//full text does not match but file name match
      $by_weights[(int)$row['id']] =  2;
    }
  }


  // Step 2 - search tags corresponding to the query $q ========================
  if (!empty($q_like_clause))
  { // search name and url name (without accents)
    $query = '
SELECT id, name, url_name
  FROM '.TAGS_TABLE.'
  WHERE ('.str_replace($q_like_field, 'CONVERT(name, CHAR)', $q_like_clause).'
    OR '.str_replace($q_like_field, 'url_name', $q_like_clause).')';
    $tags = hash_from_query($query, 'id');
    if ( !empty($tags) )
    { // we got some tags; get the images
      $search_results['qs']['matching_tags']=$tags;
      $query = '
SELECT image_id, COUNT(tag_id) AS weight
  FROM '.IMAGE_TAG_TABLE.'
  WHERE tag_id IN ('.implode(',',array_keys($tags)).')
  GROUP BY image_id';
      $result = pwg_query($query);
      while ($row = pwg_db_fetch_assoc($result))
      { // weight is important when sorting images by relevance
        $image_id=(int)$row['image_id'];
        @$by_weights[$image_id] += $row['weight'];
      }
    }
  }


  // Step 3 - search categories corresponding to the query $q ==================
  global $user;
  $query = '
SELECT id, name, permalink, nb_images
  FROM '.CATEGORIES_TABLE.'
    INNER JOIN '.USER_CACHE_CATEGORIES_TABLE.' ON id=cat_id
  WHERE user_id='.$user['id'].'
    AND MATCH(name, comment) AGAINST( "'.$q.'" IN BOOLEAN MODE)'.
  get_sql_condition_FandF (
      array( 'visible_categories' => 'cat_id' ), "\n    AND"
    );
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  { // weight is important when sorting images by relevance
    if ($row['nb_images']==0)
    {
      $search_results['qs']['matching_cats_no_images'][] = $row;
    }
    else
    {
      $search_results['qs']['matching_cats'][$row['id']] = $row;
    }
  }

  if ( empty($by_weights) and empty($search_results['qs']['matching_cats']) )
  {
    return $search_results;
  }

  // Step 4 - now we have $by_weights ( array image id => weight ) that need
  // permission checks and/or matching categories to get images from
  $where_clauses = array();
  if ( !empty($by_weights) )
  {
    $where_clauses[]='i.id IN ('
      . implode(',', array_keys($by_weights)) . ')';
  }
  if ( !empty($search_results['qs']['matching_cats']) )
  {
    $where_clauses[]='category_id IN ('.
      implode(',',array_keys($search_results['qs']['matching_cats'])).')';
  }
  $where_clauses = array( '('.implode("\n    OR ",$where_clauses).')' );
  if (!empty($images_where))
  {
    $where_clauses[]='('.$images_where.')';
  }
  $where_clauses[] = get_sql_condition_FandF(
      array
        (
          'forbidden_categories' => 'category_id',
          'visible_categories' => 'category_id',
          'visible_images' => 'i.id'
        ),
      null,true
    );

  global $conf;
  $query = '
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.' i
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE '.implode("\n AND ", $where_clauses)."\n".
  $conf['order_by'];

  $allowed_images = array_from_query( $query, 'id');

  if ( $super_order_by or empty($by_weights) )
  {
    $search_results['items'] = $allowed_images;
    return $search_results;
  }

  $allowed_images = array_flip( $allowed_images );
  $divisor = 5.0 * count($allowed_images);
  foreach ($allowed_images as $id=>$rank )
  {
    $weight = isset($by_weights[$id]) ? $by_weights[$id] : 1;
    $weight -= $rank/$divisor;
    $allowed_images[$id] = $weight;
  }
  arsort($allowed_images, SORT_NUMERIC);
  $search_results['items'] = array_keys($allowed_images);
  return $search_results;
}

/**
 * returns an array of 'items' corresponding to the search id
 *
 * @param int search id
 * @param string images_where optional aditional restriction on images table
 * @return array
 */
function get_search_results($search_id, $super_order_by, $images_where='')
{
  $search = get_search_array($search_id);
  if ( !isset($search['q']) )
  {
    $result['items'] = get_regular_search_results($search, $images_where);
    return $result;
  }
  else
  {
    return get_quick_search_results($search['q'], $super_order_by, $images_where);
  }
}
?>