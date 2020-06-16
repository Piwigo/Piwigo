<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die ('Hacking attempt!');
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_history.inc.php');

// +-----------------------------------------------------------------------+
// | Functions                                                             |
// +-----------------------------------------------------------------------+

//Get the last unit of time for years, months, days and hours
function get_last($last_number=60, $type='year')
{
  $query = '
SELECT
    year,
    month,
    day,
    hour,
    nb_pages
  FROM '.HISTORY_SUMMARY_TABLE;

  if ($type === 'hour')
  {
    $query.= '
  WHERE year IS NOT NULL
    AND month IS NOT NULL
    AND day IS NOT NULL
    AND hour IS NOT NULL
  ORDER BY
    year DESC,
    month DESC,
    day DESC,
    hour DESC
  LIMIT '.$last_number.'
;';
  }
  elseif ($type === 'day')
  {
    $query.= '
  WHERE year IS NOT NULL
    AND month IS NOT NULL
    AND day IS NOT NULL
    AND hour IS NULL
  ORDER BY
    year DESC,
    month DESC,
    day DESC
  LIMIT '.$last_number.'
;';
  }
  elseif ($type === 'month')
  {
    $query.= '
  WHERE year IS NOT NULL
    AND month IS NOT NULL
    AND day IS NULL
  ORDER BY
    year DESC,
    month DESC
  LIMIT '.$last_number.'
;';
  }
  else
  {
    $query.= '
  WHERE year IS NOT NULL
    AND month IS NULL
  ORDER BY
    year DESC
  LIMIT '.$last_number.'
;';
  }

  $result = pwg_query($query);

  $output = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    $output[] = $row;
  }

  return $output;
}

function get_month_of_last_years ($last = 'all') 
{

  $query = '
SELECT
  year,
  month,
  day,
  hour,
  nb_pages
FROM '.HISTORY_SUMMARY_TABLE.'
WHERE month IS NOT NULL
  AND day IS NULL
ORDER BY
  year DESC,
  month DESC';

  if ($last !== 'all') 
  {
    $date = new DateTime();
    $limit = ($last - 1)*12+$date->format('n') - 1;
    $query .= 
' LIMIT '.$limit;
    $result = query2array($query.';');
    $lastDate = $date->sub(new DateInterval('P'.($last - 1).'Y'.($date->format('n') - 1).'M'));
    return set_missing_values('month', $result, false, $lastDate, new DateTime());
  }

  return set_missing_values('month', query2array($query.';'), false);
}

function get_month_stats() 
{
  $result = array();
  $date = new DateTime();
  $date_last_month = clone $date;
  $date_last_year = clone $date;
  $months = array();

  $date_last_month->sub(new DateInterval('P1M'));
  $date_last_year->sub(new DateInterval('P1Y'));
  $query = '
SELECT
  year,
  month,
  day,
  hour,
  nb_pages
FROM '.HISTORY_SUMMARY_TABLE.'
WHERE 
  (
    (year = '.$date->format('Y').' AND month = '.$date->format('n').')
    OR (year = '.$date_last_month->format('Y').' AND month = '.$date_last_month->format('n').')
    OR (year = '.$date_last_year->format('Y').' AND month = '.$date_last_year->format('n').')
  )
  AND day IS NOT NULL
  AND hour IS NULL
ORDER BY
  year DESC,
  month DESC
;';

  foreach (query2array($query) as $value) 
  {
    $date = get_date_object($value);
    @$months[$date->format('Y/m/1')][] = $value;
  }

  $actual_date = new DateTime();
  if (!isset($months[$actual_date->format('Y/m/1')])) 
  {
    @$months[$actual_date->format('Y/m/1')][] = array(
      'year' => $actual_date->format('Y'),
      'month'=> $actual_date->format('n'),
      'day'=> null,
      'hour'=> null,
      'nb_pages' => 0
    );
  }

  foreach ($months as $key => $val) 
  {
    $lastDate = new DateTime($key);
    $lastDate = $lastDate->add(new DateInterval('P1M'));
    $lastDate = $lastDate->sub(new DateInterval('P1D'));
    if ($lastDate > new DateTime()) 
    {
      $lastDate = new DateTime();
    }
    $result['month'][] = set_missing_values('day',$val, false, new DateTime($key), $lastDate);
  }

  $query = '
SELECT
  AVG(nb_pages)
FROM '.HISTORY_SUMMARY_TABLE.'
WHERE 
  (
  year = '.$date->format('Y').' OR
  (year = '.($date->format('Y')-1).' and month > '.$date->format('n').')
  ) 
  AND day IS NOT NULL
  AND hour IS NULL
ORDER BY
  year DESC,
  month DESC
;';

  $result['avg'] = query2array($query)[0]['AVG(nb_pages)'];
  
  return $result;
}

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// | Refresh summary from details                                          |
// +-----------------------------------------------------------------------+

history_summarize();

// +-----------------------------------------------------------------------+
// | Display statistics header                                             |                                                                                            
// +-----------------------------------------------------------------------+

$template->set_filename('stats', 'stats.tpl');

// TabSheet initialization
history_tabsheet();

$base_url = get_root_url().'admin.php?page=history';

$template->assign(
  array(
    'U_HELP' => get_root_url().'admin/popuphelp.php?page=history',
    'F_ACTION' => $base_url,
    )
  );

// +-----------------------------------------------------------------------+
// | Set missing rows to 0                                                 |
// +-----------------------------------------------------------------------+

function set_missing_values($unit, $data, $keep_size=true, $firstDate = null, $lastDate = null)
{
  if (count($data) <= 1) 
  {
    return $data;
  }

  $limit = count($data);
  $result = array();
  if ($firstDate == null) 
  {
    //firstDate is actually the last date when keep_size is true because we want the most recent values first
    $date = get_date_object($data[0]);
  } else {
    $date = $firstDate;
  }

  //Declare variable according the unit
  if ($unit == 'year') 
  {
    $date_format = 'Y';
    $date_add = 'P1Y';
  } 
  else if ($unit == 'month') 
  {
    $date_format = 'Y-m';
    $date_add = 'P1M';
  } 
  else if ($unit == 'day') 
  {
    $date_format = 'Y-m-d';
    $date_add = 'P1D';
  } 
  else if ($unit == 'hour') 
  {
    $date_format = 'Y-m-d\TH:00';
    $date_add = 'PT1H';
  }

  //Fill an empty array with all the dates
  if ($keep_size) 
  {
    for ($i=0; $i < $limit; $i++) 
    { 
      $result[$date->format($date_format)] = 0;
      $date->sub(new DateInterval($date_add));
    }
  } else {
    if ($lastDate == null) 
    {
      $date_end = get_date_object($data[0]);
    } else {
      $date_end = $lastDate;
    }
    while ($date <= $date_end) {
      $result[$date->format($date_format)] = 0;
      $date->add(new DateInterval($date_add));
    }
  }

  //Overload with database rows
  foreach ($data as $value) 
  {
    $str = get_date_object($value)->format($date_format);
    if (isset($result[$str])) 
    {
      $result[$str] += $value['nb_pages'];
    }
  }

  return $result;
}

//Get a DateTime object for a database row
function get_date_object($row) 
{
  $date_string = $row['year'];
    if ($row['month'] != null) 
    {
      $date_string = $date_string.'-'.$row['month'] ;
      if ($row['day'] != null) 
      {
        $date_string = $date_string.'-'.$row['day'];
        if ($row['hour'] != null) 
        {
          $date_string = $date_string.' '.$row['hour'].':00';
        }
      }
    } 
    else 
    {
      $date_string .= '-1';
    }

  return new DateTime($date_string);
}

// +-----------------------------------------------------------------------+
// | Send data to template                                                 |
// +-----------------------------------------------------------------------+

$template->append(
  'compareYears', 
  get_month_of_last_years($conf['stat_compare_year_displayed'])
);
$template->append('monthStats', get_month_stats());
$template->append('lastHours', set_missing_values('hour',get_last(72, 'hour')));
$template->append('lastDays', set_missing_values('day',get_last(90, 'day')));
$template->append('lastMonths', set_missing_values('month',get_last(24, 'month')));
$template->append('lastYears', set_missing_values('year',get_last(60, 'year')));
$template->assign('langCode', strval($user['language']));

$template->assign_var_from_handle('ADMIN_CONTENT', 'stats');
?>