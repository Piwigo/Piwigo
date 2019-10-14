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
