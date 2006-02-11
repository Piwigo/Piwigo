<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2006-01-31 21:46:26 -0500 (Tue, 31 Jan 2006) $
// | last modifier : $Author: rvelices $
// | revision      : $Revision: 1020 $
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
include_once(PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php');

/**
 * requests the given $url (a remote create_listing_file.php) and fills a
 * list of lines corresponding to request output
 *
 * @param string $url
 * @return void
 */
function remote_output($url)
{
  global $template, $page;

  if($lines = @file($url))
  {
    $template->assign_block_vars('remote_output', array());
    // cleaning lines from HTML tags
    foreach ($lines as $line)
    {
      $line = trim(strip_tags($line));
      if (preg_match('/^PWG-([A-Z]+)-/', $line, $matches))
      {
        $template->assign_block_vars(
          'remote_output.remote_line',
          array(
            'CLASS' => 'remote'.ucfirst(strtolower($matches[1])),
            'CONTENT' => $line
           )
         );
      }
    }
  }
  else
  {
    array_push($page['errors'], l10n('remote_site_file_not_found'));
  }
}


// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+
$template->set_filenames(array('site_manager'=>'admin/site_manager.tpl'));

// +-----------------------------------------------------------------------+
// |                        new site creation form                         |
// +-----------------------------------------------------------------------+
if (isset($_POST['submit']))
{
  $is_remote = url_is_remote( $_POST['galleries_url'] );
  $url = preg_replace('/[\/]*$/', '', $_POST['galleries_url']);
  $url.= '/';
  if (! $is_remote)
  {
    if ( ! (strpos($url, '.') === 0 ) )
    {
      $url = './' . $url;
    }
  }

  // site must not exists
  $query = '
SELECT COUNT(id) AS count
  FROM '.SITES_TABLE.'
  WHERE galleries_url = \''.$url.'\'
;';
  $row = mysql_fetch_array(pwg_query($query));
  if ($row['count'] > 0)
  {
    array_push($page['errors'],
      l10n('remote_site_already_exists').' ['.$url.']');
  }
  if (count($page['errors']) == 0)
  {
    if ($is_remote)
    {
      $clf_url = $url.'create_listing_file.php';
      $clf_url.= '?action=test';
      $clf_url.= '&version='.PHPWG_VERSION;
      if ($lines = @file($clf_url))
      {
        $first_line = strip_tags($lines[0]);
        if (!preg_match('/^PWG-INFO-2:/', $first_line))
        {
          array_push($page['errors'],
                     l10n('remote_site_error').' : '.$first_line);
        }
      }
      else
      {
        array_push($page['errors'], l10n('remote_site_file_not_found') );
      }
    }
    else
    { // local directory
      if ( ! file_exists($url) )
      {
        array_push($page['errors'],
          l10n('Directory does not exist').' ['.$url.']');
      }
    }
  }

  if (count($page['errors']) == 0)
  {
    $query = '
INSERT INTO '.SITES_TABLE.'
  (galleries_url)
  VALUES
  (\''.$url.'\')
;';
    pwg_query($query);
    array_push($page['infos'],
               $url.' '.l10n('remote_site_created'));
  }
}

// +-----------------------------------------------------------------------+
// |                            actions on site                            |
// +-----------------------------------------------------------------------+
if (isset($_GET['site']) and is_numeric($_GET['site']))
{
  $page['site'] = $_GET['site'];
}
if (isset($_GET['action']) and isset($page['site']) )
{
  $query = '
SELECT galleries_url
  FROM '.SITES_TABLE.'
  WHERE id = '.$page['site'].'
;';
  list($galleries_url) = mysql_fetch_array(pwg_query($query));
  switch($_GET['action'])
  {
    case 'generate' :
    {
      $title = $galleries_url.' : '.l10n('remote_site_generate');
      $template->assign_vars(array('REMOTE_SITE_TITLE'=>$title));
      remote_output($galleries_url.'create_listing_file.php?action=generate');
      break;
    }
    case 'test' :
    {
      $title = $galleries_url.' : '.l10n('remote_site_test');
      $template->assign_vars(array('REMOTE_SITE_TITLE'=>$title));
      remote_output($galleries_url.'create_listing_file.php?action=test&version='.PHPWG_VERSION);
      break;
    }
    case 'clean' :
    {
      $title = $galleries_url.' : '.l10n('remote_site_clean');
      $template->assign_vars(array('REMOTE_SITE_TITLE'=>$title));
      remote_output($galleries_url.'create_listing_file.php?action=clean');
      break;
    }
    case 'delete' :
    {
      delete_site($page['site']);
      array_push($page['infos'],
                 $galleries_url.' '.l10n('remote_site_deleted'));
      break;
    }
  }
}

$template->assign_vars( array(
  'F_ACTION' => PHPWG_ROOT_PATH.'admin.php'
                .get_query_string_diff( array('action','site') ) 
  ) );
  
// +-----------------------------------------------------------------------+
// |                           remote sites list                           |
// +-----------------------------------------------------------------------+

$query = '
SELECT s.*, COUNT(c.id) AS nb_categories, SUM(c.nb_images) AS nb_images
FROM '.SITES_TABLE.' AS s LEFT JOIN '.CATEGORIES_TABLE.' AS c
ON s.id=c.site_id
GROUP BY s.id'.
';';
$result = pwg_query($query);

if (mysql_num_rows($result) > 0)
{
  $template->assign_block_vars('sites', array());
}
while ($row = mysql_fetch_array($result))
{
  $is_remote = url_is_remote($row['galleries_url']);
  $base_url = PHPWG_ROOT_PATH.'admin.php';
  $base_url.= '?page=site_manager';
  $base_url.= '&amp;site='.$row['id'];
  $base_url.= '&amp;action=';

  $update_url = PHPWG_ROOT_PATH.'admin.php';
  $update_url.= '?page=site_update';
  $update_url.= '&amp;site='.$row['id'];

  $template->assign_block_vars(
    'sites.site',
    array(
      'NAME' => $row['galleries_url'],
      'TYPE' => l10n( $is_remote ? 'Remote' : 'Local' ),
      'CATEGORIES' => $row['nb_categories'],
      'IMAGES' => isset($row['nb_images']) ? $row['nb_images'] : 0,
      'U_UPDATE' => $update_url
     )
   );

   if ($is_remote)
   {
     $template->assign_block_vars('sites.site.remote',
       array(
         'U_TEST' => $base_url.'test',
         'U_GENERATE' => $base_url.'generate',
         'U_CLEAN' => $base_url.'clean'
         )
       );
   }

   if ($row['id'] != 1)
   {
     $template->assign_block_vars( 'sites.site.delete',
       array('U_DELETE' => $base_url.'delete') );
   }
}

$template->assign_var_from_handle('ADMIN_CONTENT', 'site_manager');
?>