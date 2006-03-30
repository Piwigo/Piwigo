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
 * @param int search_id
 * @return string
 */
function get_sql_search_clause($search_id)
{
  $search = get_search_array($search_id);

  // SQL where clauses are stored in $clauses array during query
  // construction
  $clauses = array();

  foreach (array('file','name','comment','keywords','author') as $textfield)
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
    $fields = array('file', 'name', 'comment', 'keywords', 'author');
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

  if (isset($forbidden))
  {
    $search_clause.= "\n    AND ".$forbidden;
  }

  return $search_clause;
}

?>