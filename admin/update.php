<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2004 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
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

if (!defined('PHPWG_ROOT_PATH'))
{
  die ('Hacking attempt!');
}
include_once( PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php');

define('CURRENT_DATE', date('Y-m-d'));
$error_labels = array('PWG-UPDATE-1' => $lang['update_wrong_dirname_short'],
                      'PWG-UPDATE-2' => $lang['update_missing_tn_short']);
$errors = array();
$infos = array();
// +-----------------------------------------------------------------------+
// |                      directories / categories                         |
// +-----------------------------------------------------------------------+
if (isset($_POST['submit'])
    and ($_POST['sync'] == 'dirs' or $_POST['sync'] == 'files'))
{
  $counts['new_categories'] = 0;
  $counts['del_categories'] = 0;
  $counts['del_elements'] = 0;
  $counts['new_elements'] = 0;

  // shall we simulate only
  if (isset($_POST['simulate']) and $_POST['simulate'] == 1)
  {
    $simulate = true;
  }
  else
  {
    $simulate = false;
  }
  
  $start = get_moment();
  // which categories to update ?
  $cat_ids = array();

  $query = '
SELECT id, uppercats, global_rank, status, visible
  FROM '.CATEGORIES_TABLE.'
  WHERE dir IS NOT NULL
    AND site_id = 1';
  if (isset($_POST['cat']) and is_numeric($_POST['cat']))
  {
    if (isset($_POST['subcats-included']) and $_POST['subcats-included'] == 1)
    {
      $query.= '
    AND uppercats REGEXP \'(^|,)'.$_POST['cat'].'(,|$)\'
';
    }
    else
    {
      $query.= '
    AND id = '.$_POST['cat'].'
';
    }
  }
  $query.= '
;';
  $result = pwg_query($query);

  $db_categories = array();
  while ($row = mysql_fetch_array($result))
  {
    $db_categories[$row['id']] = $row;
  }

  // get categort full directories in an array for comparison with file
  // system directory tree
  $db_fulldirs = get_fulldirs(array_keys($db_categories));
  
  // what is the base directory to search file system sub-directories ?
  if (isset($_POST['cat']) and is_numeric($_POST['cat']))
  {
    $basedir = $db_fulldirs[$_POST['cat']];
  }
  else
  {
    $query = '
SELECT galleries_url
  FROM '.SITES_TABLE.'
  WHERE id = 1
;';
    list($galleries_url) = mysql_fetch_array(pwg_query($query));
    $basedir = preg_replace('#/*$#', '', $galleries_url);
  }

  // we need to have fulldirs as keys to make efficient comparison
  $db_fulldirs = array_flip($db_fulldirs);

  // finding next rank for each id_uppercat
  $next_rank['NULL'] = 1;
  
  $query = '
SELECT id_uppercat, MAX(rank)+1 AS next_rank
  FROM '.CATEGORIES_TABLE.'
  GROUP BY id_uppercat
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    // for the id_uppercat NULL, we write 'NULL' and not the empty string
    if (!isset($row['id_uppercat']) or $row['id_uppercat'] == '')
    {
      $row['id_uppercat'] = 'NULL';
    }
    $next_rank[$row['id_uppercat']] = $row['next_rank'];
  }
  
  // next category id available
  $query = '
SELECT IF(MAX(id)+1 IS NULL, 1, MAX(id)+1) AS next_id
  FROM '.CATEGORIES_TABLE.'
;';
  list($next_id) = mysql_fetch_array(pwg_query($query));

  // retrieve file system sub-directories fulldirs
  $fs_fulldirs = get_fs_directories($basedir);
  
  $inserts = array();
  // new categories are the directories not present yet in the database
  foreach (array_diff($fs_fulldirs, array_keys($db_fulldirs)) as $fulldir)
  {
    $dir = basename($fulldir);
    if (preg_match('/^[a-zA-Z0-9-_.]+$/', $dir))
    {
      $insert = array();
      
      $insert{'id'} = $next_id++;
      $insert{'dir'} = $dir;
      $insert{'name'} = str_replace('_', ' ', $dir);
      $insert{'site_id'} = 1;
      $insert{'commentable'} = $conf['newcat_default_commentable'];
      $insert{'uploadable'} = $conf['newcat_default_uploadable'];
      $insert{'status'} = $conf{'newcat_default_status'};
      $insert{'visible'} = $conf{'newcat_default_visible'};

      if (isset($db_fulldirs[dirname($fulldir)]))
      {
        $parent = $db_fulldirs[dirname($fulldir)];

        $insert{'id_uppercat'} = $parent;
        $insert{'uppercats'} =
          $db_categories[$parent]['uppercats'].','.$insert{'id'};
        $insert{'rank'} = $next_rank[$parent]++;
        $insert{'global_rank'} =
          $db_categories[$parent]['global_rank'].'.'.$insert{'rank'};
        if ('private' == $db_categories[$parent]['status'])
        {
          $insert{'status'} = 'private';
        }
        if ('false' == $db_categories[$parent]['visible'])
        {
          $insert{'visible'} = 'false';
        }
      }
      else
      {
        $insert{'uppercats'} = $insert{'id'};
        $insert{'rank'} = $next_rank['NULL']++;
        $insert{'global_rank'} = $insert{'rank'};
      }

      array_push($inserts, $insert);
      array_push($infos, array('path' => $fulldir,
                               'info' => $lang['update_research_added']));

      // add the new category to $db_categories and $db_fulldirs array
      $db_categories[$insert{'id'}] =
        array(
          'id' => $insert{'id'},
          'status' => $insert{'status'},
          'visible' => $insert{'visible'},
          'uppercats' => $insert{'uppercats'},
          'global_rank' => $insert{'global_rank'}
          );
      $db_fulldirs[$fulldir] = $insert{'id'};
      $next_rank[$insert{'id'}] = 1;
    }
    else
    {
      array_push($errors, array('path' => $fulldir, 'type' => 'PWG-UPDATE-1'));
    }
  }

  if (count($inserts) > 0)
  {
    if (!$simulate)
    {
      $dbfields = array(
        'id','dir','name','site_id','id_uppercat','uppercats','commentable',
        'uploadable','visible','status','rank','global_rank'
        );
      mass_inserts(CATEGORIES_TABLE, $dbfields, $inserts);
    }
    
    $counts['new_categories'] = count($inserts);
  }

  // to delete categories
  $to_delete = array();
  foreach (array_diff(array_keys($db_fulldirs), $fs_fulldirs) as $fulldir)
  {
    array_push($to_delete, $db_fulldirs[$fulldir]);
    unset($db_fulldirs[$fulldir]);
    array_push($infos, array('path' => $fulldir,
                             'info' => $lang['update_research_deleted']));
  }
  if (count($to_delete) > 0)
  {
    if (!$simulate)
    {
      delete_categories($to_delete);
    }
    $counts['del_categories'] = count($to_delete);
  }
  
  echo get_elapsed_time($start, get_moment());
  echo ' for new method scanning directories<br />';
}
// +-----------------------------------------------------------------------+
// |                           files / elements                            |
// +-----------------------------------------------------------------------+
if (isset($_POST['submit']) and $_POST['sync'] == 'files')
{  
  $start_files = get_moment();
  $start= $start_files;

  $fs = get_fs($basedir);
  
  echo get_elapsed_time($start, get_moment());
  echo ' for get_fs<br />';
  
  $cat_ids = array_diff(array_keys($db_categories), $to_delete);

  $db_elements = array();
  $db_unvalidated = array();
  
  if (count($cat_ids) > 0)
  {
    $query = '
SELECT id, path
  FROM '.IMAGES_TABLE.'
  WHERE storage_category_id IN (
'.wordwrap(implode(', ', $cat_ids), 80, "\n").')
;';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      $db_elements[$row['id']] = $row['path'];
    }

    // searching the unvalidated waiting elements (they must not be taken into
    // account)
    $query = '
SELECT file,storage_category_id
  FROM '.WAITING_TABLE.'
  WHERE storage_category_id IN (
'.wordwrap(implode(', ', $cat_ids), 80, "\n").')
    AND validated = \'false\'
;';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      array_push(
        $db_unvalidated,
        array_search($row['storage_category_id'],
                     $db_fulldirs).'/'.$row['file']
        );
    }
  }

  // next element id available
  $query = '
SELECT IF(MAX(id)+1 IS NULL, 1, MAX(id)+1) AS next_element_id
  FROM '.IMAGES_TABLE.'
;';
  list($next_element_id) = mysql_fetch_array(pwg_query($query));

  $start = get_moment();

  // because isset is one hundred time faster than in_array
  $fs['thumbnails'] = array_flip($fs['thumbnails']);
  $fs['representatives'] = array_flip($fs['representatives']);
  
  $inserts = array();
  $insert_links = array();
  
  foreach (array_diff($fs['elements'], $db_elements, $db_unvalidated) as $path)
  {
    $insert = array();
    // storage category must exist
    $dirname = dirname($path);
    if (!isset($db_fulldirs[$dirname]))
    {
      continue;
    }
    $filename = basename($path);
    if (!preg_match('/^[a-zA-Z0-9-_.]+$/', $filename))
    {
      array_push($errors, array('path' => $path, 'type' => 'PWG-UPDATE-1'));
      continue;
    }

    // searching the thumbnail
    $filename_wo_ext = get_filename_wo_extension($filename);
    $tn_ext = '';
    $base_test = $dirname.'/thumbnail/';
    $base_test.= $conf['prefix_thumbnail'].$filename_wo_ext.'.';
    foreach ($conf['picture_ext'] as $ext)
    {
      $test = $base_test.$ext;
      if (isset($fs['thumbnails'][$test]))
      {
        $tn_ext = $ext;
        break;
      }
      else
      {
        continue;
      }
    }

    // 2 cases : the element is a picture or not. Indeed, for a picture
    // thumbnail is mandatory and for non picture element, thumbnail and
    // representative are optionnal
    if (in_array(get_extension($filename), $conf['picture_ext']))
    {
      // if we found a thumnbnail corresponding to our picture...
      if ($tn_ext != '')
      {
        $insert{'id'} = $next_element_id++;
        $insert{'file'} = $filename;
        $insert{'storage_category_id'} = $db_fulldirs[$dirname];
        $insert{'date_available'} = CURRENT_DATE;
        $insert{'tn_ext'} = $tn_ext;
        $insert{'path'} = $path;

        array_push($inserts, $insert);
        array_push($insert_links,
                   array('image_id' => $insert{'id'},
                         'category_id' => $insert{'storage_category_id'}));
        array_push($infos, array('path' => $insert{'path'},
                                 'info' => $lang['update_research_added']));
      }
      else
      {
        array_push($errors, array('path' => $path, 'type' => 'PWG-UPDATE-2'));
      }
    }
    else
    {
      // searching a representative
      $representative_ext = '';
      $base_test = $dirname.'/pwg_representative/'.$filename_wo_ext.'.';
      foreach ($conf['picture_ext'] as $ext)
      {
        $test = $base_test.$ext;
        if (isset($fs['representatives'][$test]))
        {
          $representative_ext = $ext;
          break;
        }
        else
        {
          continue;
        }
      }

      $insert{'id'} = $next_element_id++;
      $insert{'file'} = $filename;
      $insert{'storage_category_id'} = $db_fulldirs[$dirname];
      $insert{'date_available'} = CURRENT_DATE;
      $insert{'path'} = $path;
        
      if ($tn_ext != '')
      {
        $insert{'tn_ext'} = $tn_ext;
      }
      if ($representative_ext != '')
      {
        $insert{'representative_ext'} = $representative_ext;
      }
      
      array_push($inserts, $insert);
      array_push($insert_links,
                 array('image_id' => $insert{'id'},
                       'category_id' => $insert{'storage_category_id'}));
      array_push($infos, array('path' => $insert{'path'},
                               'info' => $lang['update_research_added']));
    }
  }

  if (count($inserts) > 0)
  {
    if (!$simulate)
    {
      // inserts all new elements
      $dbfields = array(
        'id','file','storage_category_id','date_available','tn_ext'
        ,'representative_ext','path'
        );
      mass_inserts(IMAGES_TABLE, $dbfields, $inserts);

      // insert all links between new elements and their storage category
      $dbfields = array('image_id','category_id');
      mass_inserts(IMAGE_CATEGORY_TABLE, $dbfields, $insert_links);
    }
    $counts['new_elements'] = count($inserts);
  }

  // delete elements that are in database but not in the filesystem
  $to_delete_elements = array();
  foreach (array_diff($db_elements, $fs['elements']) as $path)
  {
    array_push($to_delete_elements, array_search($path, $db_elements));
    array_push($infos, array('path' => $path,
                             'info' => $lang['update_research_deleted']));
  }
  if (count($to_delete_elements) > 0)
  {
    if (!$simulate)
    {
      delete_elements($to_delete_elements);
    }
    $counts['del_elements'] = count($to_delete_elements);
  }
  
  echo get_elapsed_time($start_files, get_moment());
  echo ' for new method scanning files<br />';
}
// +-----------------------------------------------------------------------+
// |                        template initialization                        |
// +-----------------------------------------------------------------------+
$template->set_filenames(array('update'=>'admin/update.tpl'));

$result_title = '';
if (isset($simulate) and $simulate)
{
  $result_title.= $lang['update_simulation_title'].' ';
}
$result_title.= $lang['update_part_research'];

$template->assign_vars(
  array(
    'L_SUBMIT'=>$lang['submit'],
    'L_UPDATE_TITLE'=>$lang['update_default_title'],
    'L_UPDATE_SYNC_FILES'=>$lang['update_sync_files'],
    'L_UPDATE_SYNC_DIRS'=>$lang['update_sync_dirs'],
    'L_UPDATE_SYNC_ALL'=>$lang['update_sync_all'],
    'L_UPDATE_SYNC_METADATA'=>$lang['update_sync_metadata'],
    'L_UPDATE_SYNC_METADATA_NEW'=>$lang['update_sync_metadata_new'],
    'L_UPDATE_SYNC_METADATA_ALL'=>$lang['update_sync_metadata_all'],
    'L_UPDATE_CATS_SUBSET'=>$lang['update_cats_subset'],
    'L_RESULT_UPDATE'=>$result_title,
    'L_NB_NEW_ELEMENTS'=>$lang['update_nb_new_elements'],
    'L_NB_NEW_CATEGORIES'=>$lang['update_nb_new_categories'],
    'L_NB_DEL_ELEMENTS'=>$lang['update_nb_del_elements'],
    'L_NB_DEL_CATEGORIES'=>$lang['update_nb_del_categories'],
    'L_UPDATE_NB_ERRORS'=>$lang['update_nb_errors'],
    'L_SEARCH_SUBCATS_INCLUDED'=>$lang['search_subcats_included'],
    'L_UPDATE_WRONG_DIRNAME_INFO'=>$lang['update_wrong_dirname_info'],
    'L_UPDATE_MISSING_TN_INFO'=>$lang['update_missing_tn_info'],
    'PICTURE_EXT_LIST'=>implode(',', $conf['picture_ext']),
    'L_UPDATE_ERROR_LIST_TITLE'=>$lang['update_error_list_title'],
    'L_UPDATE_ERRORS_CAPTION'=>$lang['update_errors_caption'],
    'L_UPDATE_DISPLAY_INFO'=>$lang['update_display_info'],
    'L_UPDATE_SIMULATE'=>$lang['update_simulate'],
    'L_UPDATE_INFOS_TITLE'=>$lang['update_infos_title']
    ));
// +-----------------------------------------------------------------------+
// |                        introduction : choices                         |
// +-----------------------------------------------------------------------+
if (!isset($_POST['submit']))
{
  $template->assign_block_vars('introduction', array());

  $query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE site_id = 1
;';
  display_select_cat_wrapper($query,
                             array(),
                             'introduction.category_option',
                             false);
}
// +-----------------------------------------------------------------------+
// |                          synchronize files                            |
// +-----------------------------------------------------------------------+
else if (isset($_POST['submit'])
         and ($_POST['sync'] == 'dirs' or $_POST['sync'] == 'files'))
{
  $template->assign_block_vars(
    'update',
    array(
      'NB_NEW_CATEGORIES'=>$counts['new_categories'],
      'NB_DEL_CATEGORIES'=>$counts['del_categories'],
      'NB_NEW_ELEMENTS'=>$counts['new_elements'],
      'NB_DEL_ELEMENTS'=>$counts['del_elements'],
      'NB_ERRORS'=>count($errors),
      ));
  
  if (count($errors) > 0)
  {
    $template->assign_block_vars('update.errors', array());
    foreach ($errors as $error)
    {
      $template->assign_block_vars(
        'update.errors.error',
        array(
          'ELEMENT' => $error['path'],
          'LABEL' => $error['type'].' ('.$error_labels[$error['type']].')'
          ));
    }
  }
  if (count($infos) > 0
      and isset($_POST['display_info'])
      and $_POST['display_info'] == 1)
  {
    $template->assign_block_vars('update.infos', array());
    foreach ($infos as $info)
    {
      $template->assign_block_vars(
        'update.infos.info',
        array(
          'ELEMENT' => $info['path'],
          'LABEL' => $info['info']
          ));
    }
  }

  if (!$simulate)
  {
    $start = get_moment();
    update_category('all');
    echo get_elapsed_time($start,get_moment());
    echo ' for update_category(all)<br />';
    $start = get_moment();
    ordering();
    update_global_rank();
    echo get_elapsed_time($start, get_moment());
    echo ' for ordering categories<br />';
  }
}
// +-----------------------------------------------------------------------+
// |                          synchronize metadata                         |
// +-----------------------------------------------------------------------+
else if (isset($_POST['submit']) and preg_match('/^metadata/', $_POST['sync']))
{
  // sync only never synchronized files ?
  if ($_POST['sync'] == 'metadata_new')
  {
    $opts['only_new'] = true;
  }
  else
  {
    $opts['only_new'] = false;
  }
  $opts['category_id'] = '';
  $opts['recursive'] = true;
  
  if (isset($_POST['cat']))
  {
    $opts['category_id'] = $_POST['cat'];
    // recursive ?
    if (!isset($_POST['subcats-included']) or $_POST['subcats-included'] != 1)
    {
      $opts['recursive'] = false;
    }
  }
  $start = get_moment();
  $files = get_filelist($opts['category_id'],
                        $opts['recursive'],
                        $opts['only_new']);
  echo get_elapsed_time($start, get_moment()).' for get_filelist<br />';
  
  $start = get_moment();
  update_metadata($files);
  echo get_elapsed_time($start, get_moment()).' for metadata update<br />';
}
// +-----------------------------------------------------------------------+
// |                          sending html code                            |
// +-----------------------------------------------------------------------+
$template->assign_var_from_handle('ADMIN_CONTENT', 'update');
?>