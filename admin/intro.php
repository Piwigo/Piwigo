<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2016 Piwigo Team                  http://piwigo.org |
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

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

$my_base_url = get_root_url().'admin.php?page=';

$tabsheet = new tabsheet();
$tabsheet->set_id('admin_home');
$tabsheet->select('');
$tabsheet->assign();

// +-----------------------------------------------------------------------+
// |                                actions                                |
// +-----------------------------------------------------------------------+

// Check for upgrade : code inspired from punbb
if (isset($_GET['action']) and 'check_upgrade' == $_GET['action'])
{
  if (!fetchRemote(PHPWG_URL.'/download/latest_version', $result))
  {
    $page['errors'][] = l10n('Unable to check for upgrade.');
  }
  else
  {
    $versions = array('current' => PHPWG_VERSION);
    $lines = @explode("\r\n", $result);

    // if the current version is a BSF (development branch) build, we check
    // the first line, for stable versions, we check the second line
    if (preg_match('/^BSF/', $versions['current']))
    {
      $versions['latest'] = trim($lines[0]);

      // because integer are limited to 4,294,967,296 we need to split BSF
      // versions in date.time
      foreach ($versions as $key => $value)
      {
        $versions[$key] =
          preg_replace('/BSF_(\d{8})(\d{4})/', '$1.$2', $value);
      }
    }
    else
    {
      $versions['latest'] = trim($lines[1]);
    }

    if ('' == $versions['latest'])
    {
      $page['errors'][] = l10n('Check for upgrade failed for unknown reasons.');
    }
    // concatenation needed to avoid automatic transformation by release
    // script generator
    else if ('%'.'PWGVERSION'.'%' == $versions['current'])
    {
      $page['infos'][] = l10n('You are running on development sources, no check possible.');
    }
    else if (version_compare($versions['current'], $versions['latest']) < 0)
    {
      $page['infos'][] = l10n('A new version of Piwigo is available.');
    }
    else
    {
      $page['infos'][] = l10n('You are running the latest version of Piwigo.');
    }
  }
}

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
  FROM '.GROUPS_TABLE.'
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

$template->assign(
  array(
    'NB_PHOTOS' => number_format($nb_photos, 0, '.', ','),
    'NB_ALBUMS' => $nb_categories,
    'NB_TAGS' => $nb_tags,
    'NB_IMAGE_TAG' => $nb_image_tag,
    'NB_USERS' => $nb_users,
    'NB_GROUPS' => $nb_groups,
    'NB_RATES' => $nb_rates,
    'NB_VIEWS' => number_format_human_readable($nb_views),
    'NB_PLUGINS' => count($pwg_loaded_plugins),
    'STORAGE_USED' => l10n('%sGB', number_format($disk_usage/(1024*1024), 1)),
    'U_CHECK_UPGRADE' => PHPWG_ROOT_PATH.'admin.php?action=check_upgrade',
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
