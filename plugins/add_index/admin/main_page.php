<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
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

if ((!defined('PHPWG_ROOT_PATH')) or (!(defined('IN_ADMIN') and IN_ADMIN)))
{
  die('Hacking attempt!');
}

// +-----------------------------------------------------------------------+
// | include                                                               |
// +-----------------------------------------------------------------------+
include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'include/common.inc.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// | Functions                                                             |
// +-----------------------------------------------------------------------+
/**
 * returns an array containing sub-directories
 * recursive by default
 *
 * directories nammed ".svn" are omitted
 *
 * @param string $path
 * @param bool $recursive
 * @return array
 */
function get_add_index_directories($path, $recursive = true)
{
  $dirs = array();

  if (is_dir($path))
  {
    if ($contents = opendir($path))
    {
      while (($node = readdir($contents)) !== false)
      {
        if (
            is_dir($path.'/'.$node)
            and $node != '.'
            and $node != '..'
            and $node != '.svn'
           )
        {
          array_push($dirs, $path.'/'.$node);
          if ($recursive)
          {
            $dirs = array_merge($dirs, get_add_index_directories($path.'/'.$node));
          }
        }
      }
    }
  }

  return $dirs;
}

// +-----------------------------------------------------------------------+
// | Main                                                                  |
// +-----------------------------------------------------------------------+
// Compute values
$index_file_src=$conf['add_index_source_directory_path'].$conf['add_index_filename'];
$overwrite_file=isset($_GET['overwrite']);
$site_id = (isset($_GET['site_id']) and is_numeric($_GET['site_id']) 
            ? $_GET['site_id'] 
            : 0);

// Init values
$add_index_results = array();
$count_copy = 0;
$count_skip = 0;
$count_error = 0;

if (@file_exists($index_file_src))
{
  $query = '
select
  galleries_url
from
  '.SITES_TABLE;
  if (!empty($site_id))
  {
    $query .= '
where
  id = '.$site_id;
  }
    $query .= '
order by
 id';

  $result = pwg_query($query);

  if (mysql_num_rows($result) > 0)
  {
    while (list($galleries_url) = mysql_fetch_row($result))
    {
      if (!url_is_remote($galleries_url))
      {
        //echo $galleries_url.'<BR>';
        foreach (get_add_index_directories($galleries_url) as $dir_galleries)
        {
          $file_dest = $dir_galleries.'/'.$conf['add_index_filename'];
          if ($overwrite_file or !@file_exists($file_dest))
          {
            if (copy($index_file_src, $file_dest))
            {
              array_push($add_index_results,
                sprintf(l10n('add_index_file_copied'), $file_dest));
              $count_copy++;
            }
            else
            {
              array_push($page['errors'],
                sprintf(l10n('add_index_file_not_copied'), $file_dest));
              $count_error++;
            }
          }
          else
          {
            $count_skip++;
          }
        }
      }
      else
      {
        if (!empty($site_id))
        {
          array_push($page['errors'],
            sprintf(l10n('add_index_not_local_site'), 
              $galleries_url, $site_id));
        }
      }
    }
  }

  // Show always an result, defaut (0 copy, $count_copy == $count_skip == 0)
  if (($count_copy != 0) or ($count_skip == 0))
  {
    array_push($add_index_results,
      l10n_dec('add_index_nb_copied_file', 'add_index_nb_copied_files',
        $count_copy));
  }
  if ($count_skip != 0)
  {
    array_push($add_index_results,
      l10n_dec('add_index_nb_skipped_file', 'add_index_nb_skipped_files',
        $count_skip));
  }
  if ($count_error != 0)
  {
    array_push($page['errors'],
      l10n_dec('add_index_nb_not_copied_file', 'add_index_nb_not_copied_files',
        $count_error));
  }
}
else
{
  array_push($page['errors'],
    sprintf(l10n('add_index_src_file_dont_exists'), $index_file_src));
}

// +-----------------------------------------------------------------------+
// | template initialization                                               |
// +-----------------------------------------------------------------------+
$template->set_filenames(array('main_page' => dirname(__FILE__).'/main_page.tpl'));

if (count($add_index_results) != 0)
{
  foreach ($add_index_results as $result)
  {
    $template->assign_block_vars('add_index_results.result', array('RESULT' => $result));
  }
}

// +-----------------------------------------------------------------------+
// | Sending html code                                                     |
// +-----------------------------------------------------------------------+
$template->assign_var_from_handle( 'ADMIN_CONTENT', 'main_page');

?>