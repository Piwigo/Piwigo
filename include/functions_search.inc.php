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


/**
 * Returns search rules stored into a serialized array in "search"
 * table. Each search rules set is numericaly identified.
 *
 * @param int $search_id
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
 * Returns the SQL clause for a search.
 * Transforms the array returned by get_search_array() into SQL sub-query.
 *
 * @param array $search
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
        if ('author' == $textfield)
        {
          $local_clauses[] = $textfield."='".$word."'";
        }
        else
        {
          $local_clauses[] = $textfield." LIKE '%".$word."%'";
        }
      }

      // adds brackets around where clauses
      $local_clauses = prepend_append_array_items($local_clauses, '(', ')');

      $clauses[] = implode(
        ' '.$search['fields'][$textfield]['mode'].' ',
        $local_clauses
        );
    }
  }

  if (isset($search['fields']['allwords']) and count($search['fields']['allwords']['fields']) > 0)
  {
    $fields = array('file', 'name', 'comment');

    if (isset($search['fields']['allwords']['fields']) and count($search['fields']['allwords']['fields']) > 0)
    {
      $fields = array_intersect($fields, $search['fields']['allwords']['fields']);
    }

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
        $field_clauses[] = $field." LIKE '%".$word."%'";
      }
      // adds brackets around where clauses
      $word_clauses[] = implode(
        "\n          OR ",
        $field_clauses
        );
    }

    array_walk(
      $word_clauses,
      function(&$s){ $s = "(".$s.")"; }
      );

    // make sure the "mode" is either OR or AND
    if ($search['fields']['allwords']['mode'] != 'AND' and $search['fields']['allwords']['mode'] != 'OR')
    {
      $search['fields']['allwords']['mode'] = 'AND';
    }

    $clauses[] = "\n         ".
      implode(
        "\n         ". $search['fields']['allwords']['mode']. "\n         ",
        $word_clauses
        );
  }

  foreach (array('date_available', 'date_creation') as $datefield)
  {
    if (isset($search['fields'][$datefield]))
    {
      $clauses[] = $datefield." = '".$search['fields'][$datefield]['date']."'";
    }

    foreach (array('after','before') as $suffix)
    {
      $key = $datefield.'-'.$suffix;

      if (isset($search['fields'][$key]))
      {
        $clauses[] = $datefield.
          ($suffix == 'after'             ? ' >' : ' <').
          ($search['fields'][$key]['inc'] ? '='  : '').
          " '".$search['fields'][$key]['date']."'";
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
    $clauses[] = $local_clause;
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

  if (isset($search['fields']['search_in_tags']))
  {
    $word_clauses = array();
    foreach ($search['fields']['allwords']['words'] as $word)
    {
      $word_clauses[] = "name LIKE '%".$word."%'";
    }

    $query = '
SELECT
    id
  FROM '.TAGS_TABLE.'
  WHERE '.implode(' OR ', $word_clauses).'
;';
    $tag_ids = query2array($query, null, 'id');

    $search_in_tags_items = get_image_ids_for_tags($tag_ids, 'OR');

    $logger->debug(__FUNCTION__.' '.count($search_in_tags_items).' items in $search_in_tags_items');
  }

  if (isset($search['fields']['tags']))
  {
    $tag_items = get_image_ids_for_tags(
      $search['fields']['tags']['words'],
      $search['fields']['tags']['mode']
      );

    $logger->debug(__FUNCTION__.' '.count($tag_items).' items in $tag_items');
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

    $logger->debug(__FUNCTION__.' '.count($items).' items in $items');
  }

  if (isset($search_in_tags_items))
  {
    // TODO the sorting order will not match $conf['order_by'], a solution would be
    // to have a new SQL query 'where id in (merged ids) order by $conf[order_by]'
    //
    // array_values will reset the numeric keys, without changing the sorting order.
    // picture.php relies on these keys to be sequential {0,1,2} and not {0,1,5}
    $items = array_values(
      array_unique(
        array_merge(
          $items,
          $search_in_tags_items
          )
        )
      );
  }

  if ( !empty($tag_items) )
  {
    switch ($search['mode'])
    {
      case 'AND':
        if (empty($search_clause) and !isset($search_in_tags_items))
        {
          $items = $tag_items;
        }
        else
        {
          $items = array_values( array_intersect($items, $tag_items) );
        }
        break;
      case 'OR':
        $items = array_values(
          array_unique(
            array_merge(
              $items,
              $tag_items
              )
            )
          );
        break;
    }
  }

  return $items;
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
      $pre = ($token->modifier & QST_WILDCARD_BEGIN) ? '' : '[[:<:]]';
      $post = ($token->modifier & QST_WILDCARD_END) ? '' : '[[:>:]]';
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
  global $user;

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
    $result['items'] = get_regular_search_results($search, $images_where);
    return $result;
  }
  else
  {
    return get_quick_search_results($search['q'], array('super_order_by'=>$super_order_by, 'images_where'=>$images_where) );
  }
}

?>
