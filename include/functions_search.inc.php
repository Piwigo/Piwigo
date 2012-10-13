<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2012 Piwigo Team                  http://piwigo.org |
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
    $query .= $forbidden.'
  '.$conf['order_by'];
    $items = array_from_query($query, 'id');
  }

  if ( !empty($tag_items) )
  {
    switch ($search['mode'])
    {
      case 'AND':
        if (empty($search_clause))
        {
          $items = $tag_items;
        }
        else
        {
          $items = array_values( array_intersect($items, $tag_items) );
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
        break;
    }
  }

  return $items;
}


function is_word_char($ch)
{
  return ($ch>='0' && $ch<='9') || ($ch>='a' && $ch<='z') || ($ch>='A' && $ch<='Z') || ord($ch)>127;
}

function is_odd_wbreak_begin($ch)
{
  return strpos('[{<=*+', $ch)===false ? false:true;
}

function is_odd_wbreak_end($ch)
{
  return strpos(']}>=*+', $ch)===false ? false:true;
}

define('QST_QUOTED',   0x01);
define('QST_NOT',      0x02);
define('QST_WILDCARD_BEGIN',0x04);
define('QST_WILDCARD_END',  0x08);
define('QST_WILDCARD', QST_WILDCARD_BEGIN|QST_WILDCARD_END);


/**
 * analyzes and splits the quick/query search query $q into tokens
 * q='john bill' => 2 tokens 'john' 'bill'
 * Special characters for MySql full text search (+,<,>,~) appear in the token modifiers.
 * The query can contain a phrase: 'Pierre "New York"' will return 'pierre' qnd 'new york'.
 */
function analyse_qsearch($q, &$qtokens, &$qtoken_modifiers)
{
  $q = stripslashes($q);
  $tokens = array();
  $token_modifiers = array();
  $crt_token = "";
  $crt_token_modifier = 0;

  for ($i=0; $i<strlen($q); $i++)
  {
    $ch = $q[$i];
    if ( ($crt_token_modifier&QST_QUOTED)==0)
    {
        if ($ch=='"')
        {
          if (strlen($crt_token))
          {
            $tokens[] = $crt_token; $token_modifiers[] = $crt_token_modifier;
            $crt_token = ""; $crt_token_modifier = 0;
          }
          $crt_token_modifier |= QST_QUOTED;
        }
        elseif ( strcspn($ch, '*+-><~')==0 )
        { //special full text modifier
          if (strlen($crt_token))
          {
            $crt_token .= $ch;
          }
          else
          {
            if ( $ch=='*' )
              $crt_token_modifier |= QST_WILDCARD_BEGIN;
            if ( $ch=='-' )
              $crt_token_modifier |= QST_NOT;
          }
        }
        elseif (preg_match('/[\s,.;!\?]+/', $ch))
        { // white space
          if (strlen($crt_token))
          {
            $tokens[] = $crt_token; $token_modifiers[] = $crt_token_modifier;
            $crt_token = "";
          }
          $crt_token_modifier = 0;
        }
        else
        {
          $crt_token .= $ch;
        }
    }
    else // qualified with quotes
    {
      if ($ch=='"')
      {
        if ($i+1 < strlen($q) && $q[$i+1]=='*')
        {
          $crt_token_modifier |= QST_WILDCARD_END;
          $i++;
        }
        $tokens[] = $crt_token; $token_modifiers[] = $crt_token_modifier;
        $crt_token = ""; $crt_token_modifier = 0;
        $state=0;
      }
      else
        $crt_token .= $ch;
    }
  }

  if (strlen($crt_token))
  {
    $tokens[] = $crt_token;
    $token_modifiers[] = $crt_token_modifier;
  }

  $qtokens = array();
  $qtoken_modifiers = array();
  for ($i=0; $i<count($tokens); $i++)
  {
    if (strstr($token_modifiers[$i], 'q')===false)
    {
      if ( substr($tokens[$i], -1)=='*' )
      {
        $tokens[$i] = rtrim($tokens[$i], '*');
        $token_modifiers[$i] .= '*';
      }
    }
    if ( strlen($tokens[$i])==0)
      continue;
    $qtokens[] = $tokens[$i];
    $qtoken_modifiers[] = $token_modifiers[$i];
  }
}


/**
 * returns the LIKE sql clause corresponding to the quick search query
 * that has been split into tokens
 * for example file LIKE '%john%' OR file LIKE '%bill%'.
 */
function get_qsearch_like_clause($tokens, $token_modifiers, $field)
{
  $clauses = array();
  for ($i=0; $i<count($tokens); $i++)
  {
    $token = trim($tokens[$i], '%');
    if ($token_modifiers[$i]&QST_NOT)
      continue;
    if ( strlen($token)==0 )
      continue;
    $token = addslashes($token);
    $token = str_replace( array('%','_'), array('\\%','\\_'), $token); // escape LIKE specials %_
    $clauses[] = $field.' LIKE \'%'.$token.'%\'';
  }

  return count($clauses) ? '('.implode(' OR ', $clauses).')' : null;
}

/**
*/
function get_qsearch_tags($tokens, $token_modifiers, &$token_tag_ids, &$not_tag_ids, &$all_tags)
{
  $token_tag_ids = array_fill(0, count($tokens), array() );
  $not_tag_ids = $all_tags = array();

  $token_tag_scores = $token_tag_ids;
  $transliterated_tokens = array();
  foreach ($tokens as $token)
  {
    $transliterated_tokens[] = transliterate($token);
  }

  $query = '
SELECT t.*, COUNT(image_id) AS counter
  FROM '.TAGS_TABLE.' t
    INNER JOIN '.IMAGE_TAG_TABLE.' ON id=tag_id
  GROUP BY id';
  $result = pwg_query($query);
  while ($tag = pwg_db_fetch_assoc($result))
  {
    $transliterated_tag = transliterate($tag['name']);

    // find how this tag matches query tokens
    for ($i=0; $i<count($tokens); $i++)
    {
      $transliterated_token = $transliterated_tokens[$i];

      $match = false;
      $pos = 0;
      while ( ($pos = strpos($transliterated_tag, $transliterated_token, $pos)) !== false)
      {
        if ( ($token_modifiers[$i]&QST_WILDCARD)==QST_WILDCARD )
        {// wildcard in this token
          $match = 1;
          break;
        }
        $token_len = strlen($transliterated_token);

        // search begin of word
        $wbegin_len=0; $wbegin_char=' ';
        while ($pos-$wbegin_len > 0)
        {
          if (! is_word_char($transliterated_tag[$pos-$wbegin_len-1]) )
          {
            $wbegin_char = $transliterated_tag[$pos-$wbegin_len-1];
            break;
          }
          $wbegin_len++;
        }

        // search end of word
        $wend_len=0; $wend_char=' ';
        while ($pos+$token_len+$wend_len < strlen($transliterated_tag))
        {
          if (! is_word_char($transliterated_tag[$pos+$token_len+$wend_len]) )
          {
            $wend_char = $transliterated_tag[$pos+$token_len+$wend_len];
            break;
          }
          $wend_len++;
        }

        $this_score = 0;
        if ( ($token_modifiers[$i]&QST_WILDCARD)==0 )
        {// no wildcard begin or end
          if ($token_len <= 2)
          {// search for 1 or 2 characters must match exactly to avoid retrieving too much data
            if ($wbegin_len==0 && $wend_len==0 && !is_odd_wbreak_begin($wbegin_char) && !is_odd_wbreak_end($wend_char) )
              $this_score = 1;
          }
          elseif ($token_len == 3)
          {
            if ($wbegin_len==0)
              $this_score = $token_len / ($token_len + $wend_len);
          }
          else
          {
            $this_score = $token_len / ($token_len + 1.1 * $wbegin_len + 0.9 * $wend_len);
          }
        }

        if ($this_score>0)
          $match = max($match, $this_score );
        $pos++;
      }

      if ($match)
      {
        $tag_id = (int)$tag['id'];
        $all_tags[$tag_id] = $tag;
        $token_tag_ids[$i][] = $tag_id;
        $token_tag_scores[$i][] = $match;
      }
    }
  }

  // process not tags
  for ($i=0; $i<count($tokens); $i++)
  {
    if ( ! ($token_modifiers[$i]&QST_NOT) )
      continue;

    array_multisort($token_tag_scores[$i], SORT_DESC|SORT_NUMERIC, $token_tag_ids[$i]);

    for ($j=0; $j<count($token_tag_scores[$i]); $j++)
    {
      if ($token_tag_scores[$i][$j] < 0.8)
        break;
      if ($j>0 && $token_tag_scores[$i][$j] < $token_tag_scores[$i][0])
        break;
      $tag_id = $token_tag_ids[$i][$j];
      if ( isset($all_tags[$tag_id]) )
      {
        unset($all_tags[$tag_id]);
        $not_tag_ids[] = $tag_id;
      }
    }
    $token_tag_ids[$i] = array();
  }

  // process regular tags
  for ($i=0; $i<count($tokens); $i++)
  {
    if ( $token_modifiers[$i]&QST_NOT )
      continue;

    array_multisort($token_tag_scores[$i], SORT_DESC|SORT_NUMERIC, $token_tag_ids[$i]);

    $counter = 0;
    for ($j=0; $j<count($token_tag_scores[$i]); $j++)
    {
      $tag_id = $token_tag_ids[$i][$j];
      if ( ! isset($all_tags[$tag_id]) )
      {
        array_splice($token_tag_ids[$i], $j, 1);
        array_splice($token_tag_scores[$i], $j, 1);
      }

      $counter += $all_tags[$tag_id]['counter'];
      if ($counter > 200 && $j>0 && $token_tag_scores[$i][0] > $token_tag_scores[$i][$j] )
      {// "many" images in previous tags and starting from this tag is less relevent
        array_splice($token_tag_ids[$i], $j);
        array_splice($token_tag_scores[$i], $j);
        break;
      }
    }
  }
  
  usort($all_tags, 'tag_alpha_compare');
  foreach ( $all_tags as &$tag )
    $tag['name'] = trigger_event('render_tag_name', $tag['name']);
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
  global $user, $conf;

  $search_results =
    array(
      'items' => array(),
      'qs' => array('q'=>stripslashes($q)),
    );
  $q = trim($q);
  analyse_qsearch($q, $tokens, $token_modifiers);
  if (count($tokens)==0)
  {
    return $search_results;
  }
  $debug[] = '<!--'.count($tokens).' tokens';

  $q_like_field = '@@__db_field__@@'; //something never in a search
  $q_like_clause = get_qsearch_like_clause($tokens, $token_modifiers, $q_like_field );

  // Step 1 - first we find matches in #images table ===========================
  $where_clauses='MATCH(i.name, i.comment) AGAINST( \''.$q.'\' IN BOOLEAN MODE)';
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
    MATCH(i.name, i.comment) AGAINST( \''.$q.'\' IN BOOLEAN MODE) AS weight
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
  $debug[] = count($by_weights).' fulltext';
  if (!empty($by_weights))
  {
    $debug[] = 'ft score min:'.min($by_weights).' max:'.max($by_weights);
  }


  // Step 2 - get the tags and the images for tags
  get_qsearch_tags($tokens, $token_modifiers, $token_tag_ids, $not_tag_ids, $search_results['qs']['matching_tags']);
  $debug[] = count($search_results['qs']['matching_tags']).' tags';

  for ($i=0; $i<count($token_tag_ids); $i++)
  {
    $tag_ids = $token_tag_ids[$i];
    $debug[] = count($tag_ids).' unique tags';

    if (!empty($tag_ids))
    {
      $tag_photo_count=0;
      $query = '
SELECT image_id FROM '.IMAGE_TAG_TABLE.'
  WHERE tag_id IN ('.implode(',',$tag_ids).')
  GROUP BY image_id';
      $result = pwg_query($query);
      while ($row = pwg_db_fetch_assoc($result))
      { // weight is important when sorting images by relevance
        $image_id=(int)$row['image_id'];
        @$by_weights[$image_id] += 1;
        $tag_photo_count++;
      }
      $debug[] = $tag_photo_count.' photos for tag';
      $debug[] = count($by_weights).' photos after';
    }
  }

  // Step 3 - search categories corresponding to the query $q ==================
  $query = '
SELECT id, name, permalink, nb_images
  FROM '.CATEGORIES_TABLE.'
    INNER JOIN '.USER_CACHE_CATEGORIES_TABLE.' ON id=cat_id
  WHERE user_id='.$user['id'].'
    AND MATCH(name, comment) AGAINST( \''.$q.'\' IN BOOLEAN MODE)'.
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
  $debug[] = count(@$search_results['qs']['matching_cats']).' albums with images';

  if ( empty($by_weights) and empty($search_results['qs']['matching_cats']) )
  {
    return $search_results;
  }

  if (!empty($not_tag_ids))
  {
    $query = '
SELECT image_id FROM '.IMAGE_TAG_TABLE.'
  WHERE tag_id IN ('.implode(',',$not_tag_ids).')
  GROUP BY image_id';
      $result = pwg_query($query);
      while ($row = pwg_db_fetch_row($result))
      {
        $id = $row[0];
        unset($by_weights[$id]);
      }
      $debug[] = count($by_weights).' after not tags';
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

  $query = '
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.' i
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE '.implode("\n AND ", $where_clauses)."\n".
  $conf['order_by'];

  $allowed_images = array_from_query( $query, 'id');

  $debug[] = count($allowed_images).' final photo count -->';
  global $template;
  $template->append('footer_elements', implode(', ', $debug) );

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