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
include_once(PHPWG_ROOT_PATH.'admin/include/image.class.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

if (isset($_GET['action']))
{
  check_pwg_token();
}

// +-----------------------------------------------------------------------+
// |                                actions                                |
// +-----------------------------------------------------------------------+

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action)
{
  case 'phpinfo' :
  {
    phpinfo();
    exit();
  }
  case 'lock_gallery' :
  {
    conf_update_param('gallery_locked', 'true');
    redirect(get_root_url().'admin.php?page=maintenance');
    break;
  }
  case 'unlock_gallery' :
  {
    conf_update_param('gallery_locked', 'false');
    $_SESSION['page_infos'] = array(l10n('Gallery unlocked'));
    redirect(get_root_url().'admin.php?page=maintenance');
    break;
  }
  case 'categories' :
  {
    images_integrity();
    update_uppercats();
    update_category('all');
    update_global_rank();
    invalidate_user_cache(true);
    break;
  }
  case 'images' :
  {
    images_integrity();
    update_path();
		include_once(PHPWG_ROOT_PATH.'include/functions_rate.inc.php');
    update_rating_score();
    invalidate_user_cache();
    break;
  }
  case 'delete_orphan_tags' :
  {
    delete_orphan_tags();
    break;
  }
  case 'user_cache' :
  {
    invalidate_user_cache();
    break;
  }
  case 'history_detail' :
  {
    $query = '
DELETE
  FROM '.HISTORY_TABLE.'
;';
    pwg_query($query);
    break;
  }
  case 'history_summary' :
  {
    $query = '
DELETE
  FROM '.HISTORY_SUMMARY_TABLE.'
;';
    pwg_query($query);
    break;
  }
  case 'sessions' :
  {
    pwg_session_gc();

    // delete all sessions associated to invalid user ids (it should never happen)
    $query = '
SELECT
    id,
    data
  FROM '.SESSIONS_TABLE.'
;';
    $sessions = query2array($query);

    $query = '
SELECT
    '.$conf['user_fields']['id'].' AS id
  FROM '.USERS_TABLE.'
;';
    $all_user_ids = query2array($query, 'id', null);

    $sessions_to_delete = array();

    foreach ($sessions as $session)
    {
      if (preg_match('/pwg_uid\|i:(\d+);/', $session['data'], $matches))
      {
        if (!isset($all_user_ids[ $matches[1] ]))
        {
          $sessions_to_delete[] = $session['id'];
        }
      }
    }

    if (count($sessions_to_delete) > 0)
    {
      $query = '
DELETE
  FROM '.SESSIONS_TABLE.'
  WHERE id IN (\''.implode("','", $sessions_to_delete).'\')
;';
      pwg_query($query);
    }

    break;
  }
  case 'feeds' :
  {
    $query = '
DELETE
  FROM '.USER_FEED_TABLE.'
  WHERE last_check IS NULL
;';
    pwg_query($query);
    break;
  }
  case 'database' :
  {
    do_maintenance_all_tables();
    break;
  }
  case 'c13y' :
  {
    include_once(PHPWG_ROOT_PATH.'admin/include/check_integrity.class.php');
    $c13y = new check_integrity();
    $c13y->maintenance();
    break;
  }
  case 'search' :
  {
    $query = '
DELETE
  FROM '.SEARCH_TABLE.'
;';
    pwg_query($query);
    break;
  }
  case 'compiled-templates':
  {
    $template->delete_compiled_templates();
    FileCombiner::clear_combined_files();
    $persistent_cache->purge(true);
    break;
  }
  case 'derivatives':
  {
    clear_derivative_cache($_GET['type']);
    break;
  }
  default :
  {
    break;
  }
}


// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(array('maintenance'=>'maintenance.tpl'));

$url_format = get_root_url().'admin.php?page=maintenance&amp;action=%s&amp;pwg_token='.get_pwg_token();

$purge_urls[l10n('All')] = sprintf($url_format, 'derivatives').'&amp;type=all';
foreach(ImageStdParams::get_defined_type_map() as $params)
{
  $purge_urls[ l10n($params->type) ] = sprintf($url_format, 'derivatives').'&amp;type='.$params->type;
}
$purge_urls[ l10n(IMG_CUSTOM) ] = sprintf($url_format, 'derivatives').'&amp;type='.IMG_CUSTOM;

$php_current_timestamp = date("Y-m-d H:i:s");
$db_version = pwg_get_db_version();
list($db_current_date) = pwg_db_fetch_row(pwg_query('SELECT now();'));

$template->assign(
  array(
    'U_MAINT_CATEGORIES' => sprintf($url_format, 'categories'),
    'U_MAINT_IMAGES' => sprintf($url_format, 'images'),
    'U_MAINT_ORPHAN_TAGS' => sprintf($url_format, 'delete_orphan_tags'),
    'U_MAINT_USER_CACHE' => sprintf($url_format, 'user_cache'),
    'U_MAINT_HISTORY_DETAIL' => sprintf($url_format, 'history_detail'),
    'U_MAINT_HISTORY_SUMMARY' => sprintf($url_format, 'history_summary'),
    'U_MAINT_SESSIONS' => sprintf($url_format, 'sessions'),
    'U_MAINT_FEEDS' => sprintf($url_format, 'feeds'),
    'U_MAINT_DATABASE' => sprintf($url_format, 'database'),
    'U_MAINT_C13Y' => sprintf($url_format, 'c13y'),
    'U_MAINT_SEARCH' => sprintf($url_format, 'search'),
    'U_MAINT_COMPILED_TEMPLATES' => sprintf($url_format, 'compiled-templates'),
    'U_MAINT_DERIVATIVES' => sprintf($url_format, 'derivatives'),
    'purge_derivatives' => $purge_urls,
    'U_HELP' => get_root_url().'admin/popuphelp.php?page=maintenance',

    'PHPWG_URL' => PHPWG_URL,
    'PWG_VERSION' => PHPWG_VERSION,
    'OS' => PHP_OS,
    'PHP_VERSION' => phpversion(),
    'DB_ENGINE' => 'MySQL',
    'DB_VERSION' => $db_version,
    'U_PHPINFO' => sprintf($url_format, 'phpinfo'),
    'PHP_DATATIME' => $php_current_timestamp,
    'DB_DATATIME' => $db_current_date,
    )
  );

// graphics library
switch (pwg_image::get_library())
{
  case 'imagick':
    $library = 'ImageMagick';
    $img = new Imagick();
    $version = $img->getVersion();
    if (preg_match('/ImageMagick \d+\.\d+\.\d+-?\d*/', $version['versionString'], $match))
    {
      $library = $match[0];
    }
    $template->assign('GRAPHICS_LIBRARY', $library);
    break;

  case 'ext_imagick':
    $library = 'External ImageMagick';
    exec($conf['ext_imagick_dir'].'convert -version', $returnarray);
    if (preg_match('/Version: ImageMagick (\d+\.\d+\.\d+-?\d*)/', $returnarray[0], $match))
    {
      $library .= ' ' . $match[1];
    }
    $template->assign('GRAPHICS_LIBRARY', $library);
    break;

  case 'gd':
    $gd_info = gd_info();
    $template->assign('GRAPHICS_LIBRARY', 'GD '.@$gd_info['GD Version']);
    break;
}

if ($conf['gallery_locked'])
{
  $template->assign(
    array(
      'U_MAINT_UNLOCK_GALLERY' => sprintf($url_format, 'unlock_gallery'),
      )
    );
}
else
{
  $template->assign(
    array(
      'U_MAINT_LOCK_GALLERY' => sprintf($url_format, 'lock_gallery'),
      )
    );
}

// +-----------------------------------------------------------------------+
// | Define advanced features                                              |
// +-----------------------------------------------------------------------+

$advanced_features = array();

//$advanced_features is array of array composed of CAPTION & URL
$advanced_features = trigger_change(
  'get_admin_advanced_features_links',
  $advanced_features
  );

$template->assign('advanced_features', $advanced_features);

// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'maintenance');
?>