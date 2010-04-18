<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
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

if (!defined("PHPWG_ROOT_PATH"))
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_history.inc.php');

// +-----------------------------------------------------------------------+
// | Functions                                                             |
// +-----------------------------------------------------------------------+

function get_summary($year = null, $month = null, $day = null)
{
  $query = '
SELECT
    year,
    month,
    day,
    hour,
    nb_pages
  FROM '.HISTORY_SUMMARY_TABLE;

  if (isset($day))
  {
    $query.= '
  WHERE year = '.$year.'
    AND month = '.$month.'
    AND day = '.$day.'
    AND hour IS NOT NULL
  ORDER BY
    year ASC,
    month ASC,
    day ASC,
    hour ASC
;';
  }
  elseif (isset($month))
  {
    $query.= '
  WHERE year = '.$year.'
    AND month = '.$month.'
    AND day IS NOT NULL
    AND hour IS NULL
  ORDER BY
    year ASC,
    month ASC,
    day ASC
;';
  }
  elseif (isset($year))
  {
    $query.= '
  WHERE year = '.$year.'
    AND month IS NOT NULL
    AND day IS NULL
  ORDER BY
    year ASC,
    month ASC
;';
  }
  else
  {
    $query.= '
  WHERE year IS NOT NULL
    AND month IS NULL
  ORDER BY
    year ASC
;';
  }

  $result = pwg_query($query);

  $output = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    array_push($output, $row);
  }

  return $output;
}

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// | Refresh summary from details                                          |
// +-----------------------------------------------------------------------+

$query = '
SELECT
    date,
    HOUR(time) AS hour,
    MAX(id) AS max_id,
    COUNT(*) AS nb_pages
  FROM '.HISTORY_TABLE.'
  WHERE summarized = \'false\'
  GROUP BY
    date ASC,
    HOUR(time) ASC
;';
$result = pwg_query($query);

$need_update = array();

$max_id = 0;
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
      $need_update[$time_key] = 0;
    }
    $need_update[$time_key] += $row['nb_pages'];
  }

  if ($row['max_id'] > $max_id)
  {
    $max_id = $row['max_id'];
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
      $row['nb_pages'] += $need_update[$key];
      array_push($updates, $row);
      unset($need_update[$key]);
    }
  }
}

foreach ($need_update as $time_key => $nb_pages)
{
  $time_tokens = explode('-', $time_key);

  array_push(
      $inserts,
      array(
        'year'     => $time_tokens[0],
        'month'    => @$time_tokens[1],
        'day'      => @$time_tokens[2],
        'hour'     => @$time_tokens[3],
        'nb_pages' => $nb_pages,
        )
      );
}

if (count($updates) > 0)
{
  mass_updates(
    HISTORY_SUMMARY_TABLE,
    array(
      'primary' => array('year','month','day','hour'),
      'update'  => array('nb_pages'),
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

if ($max_id != 0)
{
  $query = '
UPDATE '.HISTORY_TABLE.'
  SET summarized = \'true\'
  WHERE summarized = \'false\'
    AND id <= '.$max_id.'
;';
  pwg_query($query);
}

// +-----------------------------------------------------------------------+
// | Page parameters check                                                 |
// +-----------------------------------------------------------------------+

foreach (array('day', 'month', 'year') as $key)
{
  if (isset($_GET[$key]))
  {
    $page[$key] = (int)$_GET[$key];
  }
}

if (isset($page['day']))
{
  if (!isset($page['month']))
  {
    die('month is missing in URL');
  }
}

if (isset($page['month']))
{
  if (!isset($page['year']))
  {
    die('year is missing in URL');
  }
}

$summary_lines = get_summary(
  @$page['year'],
  @$page['month'],
  @$page['day']
  );

// +-----------------------------------------------------------------------+
// | Display statistics header                                             |
// +-----------------------------------------------------------------------+

// page title creation
$title_parts = array();

$url = PHPWG_ROOT_PATH.'admin.php?page=stats';

array_push(
  $title_parts,
  '<a href="'.$url.'">'.l10n('Overall').'</a>'
  );

$period_label = l10n('Year');

if (isset($page['year']))
{
  $url.= '&amp;year='.$page['year'];

  array_push(
    $title_parts,
    '<a href="'.$url.'">'.$page['year'].'</a>'
    );

  $period_label = l10n('Month');
}

if (isset($page['month']))
{
  $url.= '&amp;month='.$page['month'];

  array_push(
    $title_parts,
    '<a href="'.$url.'">'.$lang['month'][$page['month']].'</a>'
    );

  $period_label = l10n('Day');
}

if (isset($page['day']))
{
  $url.= '&amp;day='.$page['day'];

  $time = mktime(12, 0, 0, $page['month'], $page['day'], $page['year']);

  $day_title = sprintf(
    '%u (%s)',
    $page['day'],
    $lang['day'][date('w', $time)]
    );

  array_push(
    $title_parts,
    '<a href="'.$url.'">'.$day_title.'</a>'
    );

  $period_label = l10n('Hour');
}

$template->set_filename('stats', 'stats.tpl');

// TabSheet initialization
history_tabsheet();

$base_url = get_root_url().'admin.php?page=history';

$template->assign(
  array(
    'L_STAT_TITLE' => implode($conf['level_separator'], $title_parts),
    'PERIOD_LABEL' => $period_label,
    'U_HELP' => get_root_url().'admin/popuphelp.php?page=history',
    'F_ACTION' => $base_url,
    )
  );

// +-----------------------------------------------------------------------+
// | Display statistic rows                                                |
// +-----------------------------------------------------------------------+

$max_width = 400;

$datas = array();

if (isset($page['day']))
{
  $key = 'hour';
  $min_x = 0;
  $max_x = 23;
}
elseif (isset($page['month']))
{
  $key = 'day';
  $min_x = 1;
  $max_x = date(
    't',
    mktime(12, 0, 0, $page['month'], 1, $page['year'])
    );
}
elseif (isset($page['year']))
{
  $key = 'month';
  $min_x = 1;
  $max_x = 12;
}
else
{
  $key = 'year';
}

$max_pages = 1;
foreach ($summary_lines as $line)
{
  if ($line['nb_pages'] > $max_pages)
  {
    $max_pages = $line['nb_pages'];
  }

  $datas[ $line[$key] ] = $line['nb_pages'];
}

if (!isset($min_x) and !isset($max_x) and count($datas) > 0)
{
  $min_x = min(array_keys($datas));
  $max_x = max(array_keys($datas));
}

if (count($datas) > 0)
{
  for ($i = $min_x; $i <= $max_x; $i++)
  {
    if (!isset($datas[$i]))
    {
      $datas[$i] = 0;
    }

    $url = null;

    if (isset($page['day']))
    {
      $value = sprintf('%02u', $i);
    }
    else if (isset($page['month']))
    {
      $url =
        get_root_url().'admin.php'
        .'?page=stats'
        .'&amp;year='.$page['year']
        .'&amp;month='.$page['month']
        .'&amp;day='.$i
        ;

      $time = mktime(12, 0, 0, $page['month'], $i, $page['year']);

      $value = $i.' ('.$lang['day'][date('w', $time)].')';
    }
    else if (isset($page['year']))
    {
      $url =
        get_root_url().'admin.php'
        .'?page=stats'
        .'&amp;year='.$page['year']
        .'&amp;month='.$i
        ;

      $value = $lang['month'][$i];
    }
    else
    {
      // at least the year is defined
      $url =
        get_root_url().'admin.php'
        .'?page=stats'
        .'&amp;year='.$i
        ;

      $value = $i;
    }

    if ($datas[$i] != 0 and isset($url))
    {
      $value = '<a href="'.$url.'">'.$value.'</a>';
    }

    $template->append(
      'statrows',
      array(
        'VALUE' => $value,
        'PAGES' => $datas[$i],
        'WIDTH' => ceil(($datas[$i] * $max_width) / $max_pages ),
        )
      );
  }
}

// +-----------------------------------------------------------------------+
// | Sending html code                                                     |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'stats');
?>