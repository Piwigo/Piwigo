<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/check_integrity.class.php');
include_once(PHPWG_ROOT_PATH.'admin/include/c13y_internal.class.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// | tabs                                                                  |
// +-----------------------------------------------------------------------+

if (isset($_GET['action']) and 'hide_newsletter_subscription' == $_GET['action'])
{
  conf_update_param('show_newsletter_subscription', 'false', true);
  exit();
}

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

$my_base_url = get_root_url().'admin.php?page=';

$tabsheet = new tabsheet();
$tabsheet->set_id('admin_home');
$tabsheet->select('');
$tabsheet->assign();

// +-----------------------------------------------------------------------+
// |                                actions                                |
// +-----------------------------------------------------------------------+

if (isset($page['nb_pending_comments']))
{
  $message = l10n('User comments').' <i class="icon-chat"></i> ';
  $message.= '<a href="'.$link_start.'comments">';
  $message.= l10n('%d waiting for validation', $page['nb_pending_comments']);
  $message.= ' <i class="icon-right"></i></a>';
  
  $page['messages'][] = $message;
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(array('intro' => 'intro.tpl'));

if ($conf['show_newsletter_subscription']) {
  $template->assign(
    array(
      'EMAIL' => $user['email'],
      'SUBSCRIBE_BASE_URL' => get_newsletter_subscribe_base_url($user['language']),
      )
    );
}


$query = '
SELECT COUNT(*)
  FROM '.IMAGES_TABLE.'
;';
list($nb_photos) = pwg_db_fetch_row(pwg_query($query));

$query = '
SELECT COUNT(*)
  FROM '.CATEGORIES_TABLE.'
;';
list($nb_categories) = pwg_db_fetch_row(pwg_query($query));

$query = '
SELECT COUNT(*)
  FROM '.TAGS_TABLE.'
;';
list($nb_tags) = pwg_db_fetch_row(pwg_query($query));

$query = '
SELECT COUNT(*)
  FROM '.IMAGE_TAG_TABLE.'
;';
list($nb_image_tag) = pwg_db_fetch_row(pwg_query($query));

$query = '
SELECT COUNT(*)
  FROM '.USERS_TABLE.'
;';
list($nb_users) = pwg_db_fetch_row(pwg_query($query));

$query = '
SELECT COUNT(*)
  FROM `'.GROUPS_TABLE.'`
;';
list($nb_groups) = pwg_db_fetch_row(pwg_query($query));

$query = '
SELECT COUNT(*)
  FROM '.RATE_TABLE.'
;';
list($nb_rates) = pwg_db_fetch_row(pwg_query($query));

$query = '
SELECT
    SUM(nb_pages)
  FROM '.HISTORY_SUMMARY_TABLE.'
  WHERE month IS NULL
;';
list($nb_views) = pwg_db_fetch_row(pwg_query($query));

$query = '
SELECT
    SUM(filesize)
  FROM '.IMAGES_TABLE.'
;';
list($disk_usage) = pwg_db_fetch_row(pwg_query($query));

$query = '
SELECT
    SUM(filesize)
  FROM '.IMAGE_FORMAT_TABLE.'
;';
list($formats_disk_usage) = pwg_db_fetch_row(pwg_query($query));

$disk_usage+= $formats_disk_usage;

$du_decimals = 1;
$du_gb = $disk_usage/(1024*1024);
if ($du_gb > 100)
{
  $du_decimals = 0;
}

$template->assign(
  array(
    'NB_PHOTOS' => $nb_photos,
    'NB_ALBUMS' => $nb_categories,
    'NB_TAGS' => $nb_tags,
    'NB_IMAGE_TAG' => $nb_image_tag,
    'NB_USERS' => $nb_users,
    'NB_GROUPS' => $nb_groups,
    'NB_RATES' => $nb_rates,
    'NB_VIEWS' => number_format_human_readable($nb_views),
    'NB_PLUGINS' => count($pwg_loaded_plugins),
    'STORAGE_USED' => l10n('%sGB', number_format($du_gb, $du_decimals)),
    'U_QUICK_SYNC' => PHPWG_ROOT_PATH.'admin.php?page=site_update&amp;site=1&amp;quick_sync=1&amp;pwg_token='.get_pwg_token(),
    )
  );

if ($conf['activate_comments'])
{
  $query = '
SELECT COUNT(*)
  FROM '.COMMENTS_TABLE.'
;';
  list($nb_comments) = pwg_db_fetch_row(pwg_query($query));
  $template->assign('NB_COMMENTS', $nb_comments);
}

if ($nb_photos > 0)
{
  $query = '
SELECT MIN(date_available)
  FROM '.IMAGES_TABLE.'
;';
  list($first_date) = pwg_db_fetch_row(pwg_query($query));

  $template->assign(
    array(
      'first_added_date' => format_date($first_date),
      'first_added_age' => time_since($first_date, 'year', null, false, false),
      )
    );
}

trigger_notify('loc_end_intro');

// +-----------------------------------------------------------------------+
// |                           get activity data                           |
// +-----------------------------------------------------------------------+

$nb_weeks = $conf['dashboard_activity_nb_weeks'];
$date = new DateTime();
//Array for the JS tooltip
$activity_last_weeks = array();
//Count mondays
$mondays = 0;
//Get mondays number for the chart legend
$week_number = array();
//Array for sorting days in circle size
$temp_data = array();

//Get data from $nb_weeks last weeks
while ($mondays < $nb_weeks) 
{
  $date->sub(new DateInterval('P1D'));

  if ($date->format('D') == 'Mon') 
  {
    $week_number[] = $date->format('W');
    $mondays += 1;
  }
}

$week_number = array_reverse($week_number);

$date_string = $date->format('Y-m-d');
$query = '
SELECT *
  FROM `'.ACTIVITY_TABLE.'`
  WHERE occured_on >= "'.$date_string.'%"
;';

$result = query2array($query, null);

foreach ($result as $row) 
{
  $day_date = new DateTime($row['occured_on']);

  $week = 0;
  for ($i=0; $i < $nb_weeks; $i++) 
  { 
    if ($week_number[$i] == $day_date->format('W'))
    {
      $week = $i;
    }
  }
  $day_nb = $day_date->format('N');

  @$activity_last_weeks[$week][$day_nb]['details'][ucfirst($row['object'])][ucfirst($row['action'])] += 1;
  @$activity_last_weeks[$week][$day_nb]['number'] += 1;
  @$activity_last_weeks[$week][$day_nb]['date'] = format_date($day_date->getTimestamp());
}

//echo '<pre>'; print_r($activity_last_weeks); echo '</pre>';

foreach($activity_last_weeks as $week => $i) 
{
  foreach($i as $day => $j) 
  {
    if ($j['number'] > 0) 
    {
      $temp_data[] = array('x' => $j['number'], 'd'=>$day, 'w'=>$week); 
    }
  }
}

// Algorithm to sort days in circle size :
//  * Get the difference between sorted numbers of activity per day (only not null numbers)
//  * Split days max $circle_sizes time on the biggest difference (but not below 120%)
//  * Set the sizes according to the groups created

//Function to sort days by number of activity
function cmp_day($a, $b)
{
  if ($a['x'] == $b['x']) 
  {
    return 0;
  }
  return ($a['x'] < $b['x']) ? -1 : 1;
}

usort($temp_data, 'cmp_day');

//Get the percent difference
$diff_x = array();

for ($i=1; $i < count($temp_data); $i++) 
{ 
  $diff_x[] = $temp_data[$i]['x']/$temp_data[$i-1]['x']*100;
}

$split = 0;
//Split (split represented by -1)
while (max($diff_x) > 120) 
{
  $diff_x[array_search(max($diff_x), $diff_x)] = -1;
  $split++;
}

//Fill empty chart data for the template
$chart_data = array();
for ($i=0; $i < $nb_weeks; $i++) 
{ 
  for ($j=1; $j <= 7; $j++) 
  { 
    $chart_data[$i][$j] = 0;
  }
}

$size = 1;
$chart_data[$temp_data[0]['w']][$temp_data[0]['d']] = $size;
//Set sizes in chart data
for ($i=1; $i < count($temp_data); $i++) 
{ 
  if ($diff_x[$i-1] == -1) 
  {
    $size++;
  }
  $chart_data[$temp_data[$i]['w']][$temp_data[$i]['d']] = $size;
}

//Assign data for the template
$template->assign('ACTIVITY_WEEK_NUMBER',$week_number);
$template->assign('ACTIVITY_LAST_WEEKS', $activity_last_weeks);
$template->assign('ACTIVITY_CHART_DATA',$chart_data);
$template->assign('ACTIVITY_CHART_NUMBER_SIZES',$size);

// +-----------------------------------------------------------------------+
// |                           get storage data                            |
// +-----------------------------------------------------------------------+

$video_format = array('webm','webmv','ogg','ogv','mp4','m4v');
$data_storage = array();

//Select files in Image_Table
$query = '
SELECT file, filesize
  FROM `'.IMAGES_TABLE.'`
;';

$result = query2array($query, null);

foreach ($result as $file) 
{
  $tabString = explode('.',$file['file']);
  $ext = $tabString[count($tabString) -1];
  $size = $file['filesize'];
  if (in_array($ext, $conf['picture_ext'])) 
  {
    if (isset($data_storage['Photos'])) 
    {
      $data_storage['Photos'] += $size;
    } else {
      $data_storage['Photos'] = $size;
    }
  } elseif (in_array($ext, $video_format)) {
    if (isset($data_storage['Videos'])) 
    {
      $data_storage['Videos'] += $size;
    } else {
      $data_storage['Videos'] = $size;
    }
  } else {
    if (isset($data_storage['Others'])) 
    {
      $data_storage['Others'] += $size;
    } else {
      $data_storage['Others'] = $size;
    }
  }
}

//Select files from format table
$query = '
SELECT SUM(filesize)
  FROM `'.IMAGE_FORMAT_TABLE.'`
;';

$result = query2array($query);

if (isset($result[0]['SUM(filesize)']))
{
  $data_storage['Formats'] = $result[0]['SUM(filesize)'];
}

//If the host is not windows, get the cache size
if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') 
{
  $f = './_data';
  $io = popen ( '/usr/bin/du -sk ' . $f, 'r' );
  $size = fgets ($io, 4096);
  $size = substr ( $size, 0, strpos ( $size, "\t" ) );
  pclose ( $io );
  $data_storage['Cache'] = $size;
}

//Calculate total storage
$total_storage = 0;
foreach ($data_storage as $value) 
{
  $total_storage += $value;
}

//Pass data to HTML
$template->assign('STORAGE_TOTAL',$total_storage);
$template->assign('STORAGE_CHART_DATA',$data_storage);
// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'intro');

// Check integrity
$c13y = new check_integrity();
// add internal checks
new c13y_internal();
// check and display
$c13y->check();
$c13y->display();

?>
