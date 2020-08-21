<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
 * @package functions\admin\history
 */


include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

/**
 * Init tabsheet for history pages
 * @ignore
 */
function history_tabsheet()
{
  global $page, $link_start;

  // TabSheet
  $tabsheet = new tabsheet();
  $tabsheet->set_id('history');
  $tabsheet->select($page['page']);
  $tabsheet->assign();
}

/**
 * Callback used to sort history entries
 */
function history_compare($a, $b)
{
  return strcmp($a['date'].$a['time'], $b['date'].$b['time']);
}

/**
 * Perform history search.
 *
 * @param array $data  - used in trigger_change
 * @param array $search
 * @param string[] $types
 * @param array
 */
function get_history($data, $search, $types)
{
  if (isset($search['fields']['filename']))
  {
    $query = '
SELECT
    id
  FROM '.IMAGES_TABLE.'
  WHERE file LIKE \''.$search['fields']['filename'].'\'
;';
    $search['image_ids'] = array_from_query($query, 'id');
  }

  // echo '<pre>'; print_r($search); echo '</pre>';

  $clauses = array();

  if (isset($search['fields']['date-after']))
  {
    $clauses[] = "date >= '".$search['fields']['date-after']."'";
  }

  if (isset($search['fields']['date-before']))
  {
    $clauses[] = "date <= '".$search['fields']['date-before']."'";
  }

  if (isset($search['fields']['types']))
  {
    $local_clauses = array();

    foreach ($types as $type) {
      if (in_array($type, $search['fields']['types'])) {
        $clause = 'image_type ';
        if ($type == 'none')
        {
          $clause.= 'IS NULL';
        }
        else
        {
          $clause.= "= '".$type."'";
        }

        $local_clauses[] = $clause;
      }
    }

    if (count($local_clauses) > 0)
    {
      $clauses[] = implode(' OR ', $local_clauses);
    }
  }

  if (isset($search['fields']['user'])
      and $search['fields']['user'] != -1)
  {
    $clauses[] = 'user_id = '.$search['fields']['user'];
  }

  if (isset($search['fields']['image_id']))
  {
    $clauses[] = 'image_id = '.$search['fields']['image_id'];
  }

  if (isset($search['fields']['filename']))
  {
    if (count($search['image_ids']) == 0)
    {
      // a clause that is always false
      $clauses[] = '1 = 2 ';
    }
    else
    {
      $clauses[] = 'image_id IN ('.implode(', ', $search['image_ids']).')';
    }
  }

  if (isset($search['fields']['ip']))
  {
    $clauses[] = 'IP LIKE "'.$search['fields']['ip'].'"';
  }

  $clauses = prepend_append_array_items($clauses, '(', ')');

  $where_separator =
    implode(
      "\n    AND ",
      $clauses
      );

  $query = '
SELECT
    date,
    time,
    user_id,
    IP,
    section,
    category_id,
    tag_ids,
    image_id,
    image_type
  FROM '.HISTORY_TABLE.'
  WHERE '.$where_separator.'
;';

  // LIMIT '.$conf['nb_logs_page'].' OFFSET '.$page['start'].'

  $result = pwg_query($query);

  while ($row = pwg_db_fetch_assoc($result))
  {
    $data[] = $row;
  }

  return $data;
}

/**
 * Compute statistics from history table to history_summary table
 *
 * @param int $max_lines - to only compute the next X lines, not the whole remaining lines
 */
function history_summarize($max_lines=null)
{
  // we need to know which was the last line "summarized"
  $query = '
SELECT
    *
  FROM '.HISTORY_SUMMARY_TABLE.'
  WHERE history_id_to IS NOT NULL
  ORDER BY history_id_to DESC
  LIMIT 1
;';
  $summary_lines = query2array($query);

  $history_min_id = 0;
  if (count($summary_lines) > 0)
  {
    $last_summary = $summary_lines[0];
    $history_min_id = $last_summary['history_id_to'];
  }
  else
  {
    // if we have no "reference", ie "starting point", we need to find
    // one. And "0" is not the right answer here, because history table may
    // have been purged already.
    $query = '
SELECT
    MIN(id) AS min_id
  FROM '.HISTORY_TABLE.'
;';
    $history_lines = query2array($query);
    if (count($history_lines) > 0)
    {
      $history_min_id = $history_lines[0]['min_id'] - 1;
    }
  }

  $query = '
SELECT
    date,
    '.pwg_db_get_hour('time').' AS hour,
    MIN(id) AS min_id,
    MAX(id) AS max_id,
    COUNT(*) AS nb_pages
  FROM '.HISTORY_TABLE.'
  WHERE id > '.$history_min_id;

  if (isset($max_lines))
  {
    $query.= '
    AND id <= '.($history_min_id + $max_lines);
  }

  $query.= '
  GROUP BY
    date,
    hour
  ORDER BY
    date ASC,
    hour ASC
;';
  $result = pwg_query($query);

  $need_update = array();

  $is_first = true;
  $first_time_key = null;

  while ($row = pwg_db_fetch_assoc($result))
  {
    $time_keys = array(
      substr($row['date'], 0, 4), //yyyy
      substr($row['date'], 0, 7), //yyyy-mm
      substr($row['date'], 0, 10),//yyyy-mm-dd
      sprintf(
        '%s-%02u',
        $row['date'], $row['hour']
        ),
      );

    foreach ($time_keys as $time_key)
    {
      if (!isset($need_update[$time_key]))
      {
        $need_update[$time_key] = array(
          'nb_pages' => 0,
          'history_id_from' => $row['min_id'],
          'history_id_to' => $row['max_id'],
          );
      }
      $need_update[$time_key]['nb_pages'] += $row['nb_pages'];

      if ($row['min_id'] < $need_update[$time_key]['history_id_from'])
      {
        $need_update[$time_key]['history_id_from'] = $row['min_id'];
      }

      if ($row['max_id'] > $need_update[$time_key]['history_id_to'])
      {
        $need_update[$time_key]['history_id_to'] = $row['max_id'];
      }
    }

    if ($is_first)
    {
      $is_first = false;
      $first_time_key = $time_keys[3];
    }
  }

// Only the oldest time_key might be already summarized, so we have to
// update the 4 corresponding lines instead of simply inserting them.
//
// For example, if the oldest unsummarized is 2005.08.25.21, the 4 lines
// that can be updated are:
//
// +---------------+----------+
// | id            | nb_pages |
// +---------------+----------+
// | 2005          |   241109 |
// | 2005-08       |    20133 |
// | 2005-08-25    |      620 |
// | 2005-08-25-21 |      151 |
// +---------------+----------+

  $updates = array();
  $inserts = array();

  if (isset($first_time_key))
  {
    list($year, $month, $day, $hour) = explode('-', $first_time_key);

    $query = '
SELECT *
  FROM '.HISTORY_SUMMARY_TABLE.'
  WHERE year='.$year.'
    AND ( month IS NULL
      OR ( month='.$month.'
        AND ( day is NULL
          OR (day='.$day.'
            AND (hour IS NULL OR hour='.$hour.')
          )
        )
      )
    )
;';
    $result = pwg_query($query);
    while ($row = pwg_db_fetch_assoc($result))
    {
      $key = sprintf('%4u', $row['year']);
      if ( isset($row['month']) )
      {
        $key .= sprintf('-%02u', $row['month']);
        if ( isset($row['day']) )
        {
          $key .= sprintf('-%02u', $row['day']);
          if ( isset($row['hour']) )
          {
            $key .= sprintf('-%02u', $row['hour']);
          }
        }
      }

      if (isset($need_update[$key]))
      {
        $row['nb_pages'] += $need_update[$key]['nb_pages'];
        $row['history_id_to'] = $need_update[$key]['history_id_to'];
        $updates[] = $row;
        unset($need_update[$key]);
      }
    }
  }

  foreach ($need_update as $time_key => $summary)
  {
    $time_tokens = explode('-', $time_key);

    $inserts[] = array(
      'year'     => $time_tokens[0],
      'month'    => @$time_tokens[1],
      'day'      => @$time_tokens[2],
      'hour'     => @$time_tokens[3],
      'nb_pages' => $summary['nb_pages'],
      'history_id_from' => $summary['history_id_from'],
      'history_id_to' => $summary['history_id_to'],
      );
  }

  if (count($updates) > 0)
  {
    mass_updates(
      HISTORY_SUMMARY_TABLE,
      array(
        'primary' => array('year','month','day','hour'),
        'update'  => array('nb_pages','history_id_to'),
        ),
      $updates
      );
  }

  if (count($inserts) > 0)
  {
    mass_inserts(
      HISTORY_SUMMARY_TABLE,
      array_keys($inserts[0]),
      $inserts
      );
  }
}

/**
 * Smart purge on history table. Keep some lines, purge only summarized lines
 *
 * @since 2.9
 */
function history_autopurge()
{
  global $conf, $logger;

  if (0 == $conf['history_autopurge_keep_lines'])
  {
    return;
  }

  // we want to purge only if there are too many lines and if the lines are summarized

  $query = '
SELECT
    COUNT(*)
  FROM '.HISTORY_TABLE.'
;';
  list($count) = pwg_db_fetch_row(pwg_query($query));

  if ($count <= $conf['history_autopurge_keep_lines'])
  {
    return; // no need to purge for now
  }

  // 1) find the last summarized history line
  $query = '
SELECT
    *
  FROM '.HISTORY_SUMMARY_TABLE.'
  WHERE history_id_to IS NOT NULL
  ORDER BY history_id_to DESC
  LIMIT 1
;';
  $summary_lines = query2array($query);
  if (count($summary_lines) == 0)
  {
    return; // lines not summarized, no purge
  }

  $history_id_last_summarized = $summary_lines[0]['history_id_to'];

  // 2) find the latest history line (and substract the number of lines to keep)
  $query = '
SELECT
    id
  FROM '.HISTORY_TABLE.'
  ORDER BY id DESC
  LIMIT 1
;';
  $history_lines = query2array($query);
  if (count($history_lines) == 0)
  {
    return;
  }

  $history_id_latest = $history_lines[0]['id'];

  // 3) find the oldest history line (and add the number of lines to delete)
  $query = '
SELECT
    id
  FROM '.HISTORY_TABLE.'
  ORDER BY id ASC
  LIMIT 1
;';
  $history_lines = query2array($query);
  $history_id_oldest = $history_lines[0]['id'];

  $search_min = array(
    $history_id_last_summarized,
    $history_id_latest - $conf['history_autopurge_keep_lines'],
    $history_id_oldest + $conf['history_autopurge_blocksize'],
    );
  
  $history_id_delete_before = min($search_min);

  $logger->debug(__FUNCTION__.', '.join('/', $search_min));

  $query = '
DELETE
  FROM '.HISTORY_TABLE.'
  WHERE id < '.$history_id_delete_before.'
;';
  pwg_query($query);
}

add_event_handler('get_history', 'get_history');
trigger_notify('functions_history_included');

?>