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

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}
include_once( PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php');

define('CURRENT_DATE', date('Y-m-d'));
// +-----------------------------------------------------------------------+
// |                              functions                                |
// +-----------------------------------------------------------------------+

/**
 * order categories (update categories.rank database field)
 *
 * the purpose of this function is to give a rank for all categories
 * (insides its sub-category), even the newer that have none at te
 * beginning. For this, ordering function selects all categories ordered by
 * rank ASC then name ASC for each uppercat.
 *
 * @returns void
 */
function ordering()
{
  $current_rank = 0;
  $current_uppercat = '';
		
  $query = '
SELECT id, if(id_uppercat is null,\'\',id_uppercat) AS id_uppercat
  FROM '.CATEGORIES_TABLE.'
  ORDER BY id_uppercat,rank,name
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    if ($row['id_uppercat'] != $current_uppercat)
    {
      $current_rank = 0;
      $current_uppercat = $row['id_uppercat'];
    }
    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET rank = '.++$current_rank.'
  WHERE id = '.$row['id'].'
;';
    pwg_query($query);
  }
}

function insert_local_category($id_uppercat)
{
  global $conf, $page, $user, $lang, $counts;
 
  $uppercats = '';
  $output = '';

  // 0. retrieving informations on the category to display
  $cat_directory = PHPWG_ROOT_PATH.'galleries';
  if (is_numeric($id_uppercat))
  {
    $query = 'SELECT name,uppercats,dir FROM '.CATEGORIES_TABLE;
    $query.= ' WHERE id = '.$id_uppercat;
    $query.= ';';
    $row = mysql_fetch_array( pwg_query( $query));
    $uppercats = $row['uppercats'];
    $name      = $row['name'];
    $dir       = $row['dir'];

    $upper_array = explode( ',', $uppercats);

    $local_dir = '';

    $database_dirs = array();
    $query = '
SELECT id,dir FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.$uppercats.')
;';
    $result = pwg_query( $query);
    while ($row = mysql_fetch_array($result))
    {
      $database_dirs[$row['id']] = $row['dir'];
    }
    foreach ($upper_array as $id)
    {
      $local_dir.= $database_dirs[$id].'/';
    }

    $cat_directory.= '/'.$local_dir;

    // 1. display the category name to update
    $output = '<ul class="menu">';
    $output.= '<li><strong>'.$name.'</strong>';
    $output.= ' [ '.$dir.' ]';
    $output.= '</li>';

    // 2. we search pictures of the category only if the update is for all
    //    or a cat_id is specified
    if ($_POST['sync'] == 'files')
    {
      $output.= insert_local_element($cat_directory, $id_uppercat);
    }
  }

  $fs_subdirs = get_category_directories($cat_directory);

  $sub_category_dirs = array();
  $query = '
SELECT id,dir FROM '.CATEGORIES_TABLE.'
  WHERE site_id = 1
';
  if (!is_numeric($id_uppercat))
  {
    $query.= ' AND id_uppercat IS NULL';
  }
  else
  {
    $query.= ' AND id_uppercat = '.$id_uppercat;
  }
  $query.= '
    AND dir IS NOT NULL'; // virtual categories not taken
  $query.= '
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    $sub_category_dirs[$row['id']] = $row['dir'];
  }
  
  // 3. we have to remove the categories of the database not present anymore
  $to_delete_categories = array();
  foreach ($sub_category_dirs as $id => $dir)
  {
    if (!in_array($dir, $fs_subdirs))
    {
      array_push($to_delete_categories,$id);
    }
  }
  if (count($to_delete_categories) > 0)
  {
    delete_categories($to_delete_categories);
  }

  // array of new categories to insert
  $inserts = array();
  
  foreach ($fs_subdirs as $fs_subdir)
  {
    // 5. Is the category already existing ? we create a subcat if not
    //    existing
    $category_id = array_search($fs_subdir, $sub_category_dirs);
    if (!is_numeric($category_id))
    {
      if (preg_match('/^[a-zA-Z0-9-_.]+$/', $fs_subdir))
      {
        $name = str_replace('_', ' ', $fs_subdir);

        $value = "('".$fs_subdir."','".$name."',1";
        if (!is_numeric($id_uppercat))
        {
          $value.= ',NULL';
        }
        else
        {
          $value.= ','.$id_uppercat;
        }
        $value.= ",'undef'";
        $value.= ')';
        array_push($inserts, $value);
      }
      else
      {
        $output.= '<span class="update_category_error">"'.$fs_subdir.'" : ';
        $output.= $lang['update_wrong_dirname'].'</span><br />';
      }
    }
  }

  // we have to create the category
  if (count($inserts) > 0)
  {
    $query = '
INSERT INTO '.CATEGORIES_TABLE.'
  (dir,name,site_id,id_uppercat,uppercats) VALUES
';
    $query.= implode(',', $inserts);
    $query.= '
;';
    pwg_query($query);

    $counts['new_categories']+= count($inserts);
    // updating uppercats field
    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET uppercats = ';
    if ($uppercats != '')
    {
      $query.= "CONCAT('".$uppercats."',',',id)";
    }
    else
    {
      $query.= 'id';
    }
    $query.= '
  WHERE id_uppercat ';
    if (!is_numeric($id_uppercat))
    {
      $query.= 'IS NULL';
    }
    else
    {
      $query.= '= '.$id_uppercat;
    }
    $query.= '
;';
    pwg_query($query);
  }

  // Recursive call on the sub-categories (not virtual ones)
  if (!isset($_POST['cat'])
      or (isset($_POST['subcats-included'])
          and $_POST['subcats-included'] == 1))
  {
    $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE site_id = 1
';
    if (!is_numeric($id_uppercat))
    {
      $query.= '    AND id_uppercat IS NULL';
    }
    else
    {
      $query.= '    AND id_uppercat = '.$id_uppercat;
    }
    $query.= '
    AND dir IS NOT NULL'; // virtual categories not taken
    $query.= '
;';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      $output.= insert_local_category($row['id']);
    }
  }

  if (is_numeric($id_uppercat))
  {
    $output.= '</ul>';
  }
  return $output;
}

function insert_local_element($dir, $category_id)
{
  global $lang,$conf,$counts;

  $output = '';

  // fs means FileSystem : $fs_files contains files in the filesystem found
  // in $dir that can be managed by PhpWebGallery (see get_pwg_files
  // function), $fs_thumbnails contains thumbnails, $fs_representatives
  // contains potentially representative pictures for non picture files
  $fs_files = get_pwg_files($dir);
  $fs_thumbnails = get_thumb_files($dir);
  $fs_representatives = get_representative_files($dir);

  // element deletion
  $to_delete_elements = array();
  // deletion of element if the correspond file doesn't exist anymore
  $query = '
SELECT id,file
  FROM '.IMAGES_TABLE.'
  WHERE storage_category_id = '.$category_id.'
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    if (!in_array($row['file'], $fs_files))
    {
      $output.= $row['file'];
      $output.= ' <span style="font-weight:bold;">';
      $output.= $lang['update_disappeared'].'</span><br />';
      array_push($to_delete_elements, $row['id']);
    }
  }
  // in picture case, we also delete the element if the thumbnail doesn't
  // existe anymore
  $query = '
SELECT id,file,tn_ext
  FROM '.IMAGES_TABLE.'
  WHERE storage_category_id = '.$category_id.'
    AND ('.implode(' OR ',
                   array_map(
                     create_function('$s', 'return "file LIKE \'%".$s."\'";')
                     , $conf['picture_ext'])).')
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    $thumbnail = $conf['prefix_thumbnail'];
    $thumbnail.= get_filename_wo_extension($row['file']);
    $thumbnail.= '.'.$row['tn_ext'];
    if (!in_array($thumbnail, $fs_thumbnails))
    {
      $output.= $row['file'];
      $output.= ' : <span style="font-weight:bold;">';
      $output.= $lang['update_disappeared_tn'].'</span><br />';
      array_push($to_delete_elements, $row['id']);
    }
  }

  $to_delete_elements = array_unique($to_delete_elements);
  if (count($to_delete_elements) > 0)
  {
    delete_elements($to_delete_elements);
  }
  
  $registered_elements = array();
  $query = '
SELECT file FROM '.IMAGES_TABLE.'
   WHERE storage_category_id = '.$category_id.'
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    array_push($registered_elements, $row['file']);
  }

  // unvalidated pictures are picture uploaded by users, but not validated
  // by an admin (so not registered truly visible yet)
  $unvalidated_pictures  = array();
  
  $query = '
SELECT file
  FROM '.WAITING_TABLE.'
  WHERE storage_category_id = '.$category_id.'
    AND validated = \'false\'
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    array_push($unvalidated_pictures, $row['file']);
  }

  // we only search among the picture present in the filesystem and not
  // present in the database yet. If we know that this picture is known as
  // an uploaded one but not validated, it's not tested neither
  $unregistered_elements = array_diff($fs_files
                                      ,$registered_elements
                                      ,$unvalidated_pictures);

  $inserts = array();
  
  foreach ($unregistered_elements as $unregistered_element)
  {
    if (preg_match('/^[a-zA-Z0-9-_.]+$/', $unregistered_element))
    {
      $file_wo_ext = get_filename_wo_extension($unregistered_element);
      $tn_ext = '';
      foreach ($conf['picture_ext'] as $ext)
      {
        $test = $conf['prefix_thumbnail'].$file_wo_ext.'.'.$ext;
        if (!in_array($test, $fs_thumbnails))
        {
          continue;
        }
        else
        {
          $tn_ext = $ext;
          break;
        }
      }

      // 2 cases : the element is a picture or not. Indeed, for a picture
      // thumbnail is mandatory and for non picture element, thumbnail and
      // representative is optionnal
      if (in_array(get_extension($unregistered_element), $conf['picture_ext']))
      {
        // if we found a thumnbnail corresponding to our picture...
        if ($tn_ext != '')
        {
          $insert = array();
          $insert['file'] = $unregistered_element;
          $insert['storage_category_id'] = $category_id;
          $insert['date_available'] = CURRENT_DATE;
          $insert['tn_ext'] = $tn_ext;
          $insert['path'] = $dir.$unregistered_element;

          $counts['new_elements']++;
          array_push($inserts, $insert);
        }
        else
        {
          $output.= '<span class="update_error_element">';
          $output.= $lang['update_missing_tn'].' : '.$unregistered_element;
          $output.= ' (<span style="font-weight:bold;">';
          $output.= $conf['prefix_thumbnail'];
          $output.= get_filename_wo_extension($unregistered_element);
          $output.= '.XXX</span>';
          $output.= ', XXX = ';
          $output.= implode(', ', $conf['picture_ext']);
          $output.= ')</span><br />';
        }
      }
      else
      {
        $representative_ext = '';
        foreach ($conf['picture_ext'] as $ext)
        {
          $candidate = $file_wo_ext.'.'.$ext;
          if (!in_array($candidate, $fs_representatives))
          {
            continue;
          }
          else
          {
            $representative_ext = $ext;
            break;
          }
        }

        $insert = array();
        $insert['file'] = $unregistered_element;
        $insert['path'] = $dir.$unregistered_element;
        $insert['storage_category_id'] = $category_id;
        $insert['date_available'] = CURRENT_DATE;
        if ( $tn_ext != '' )
        {
          $insert['tn_ext'] = $tn_ext;
        }
        if ( $representative_ext != '' )
        {
          $insert['representative_ext'] = $representative_ext;
        }

        $counts['new_elements']++;
        array_push($inserts, $insert);
      }
    }
    else
    {
      $output.= '<span class="update_error_element">"';
      $output.= $unregistered_element.'" : ';
      $output.= $lang['update_wrong_dirname'].'</span><br />';
    }
  }
  
  if (count($inserts) > 0)
  {
    // inserts all found pictures
    $dbfields = array(
      'file','storage_category_id','date_available','tn_ext'
      ,'representative_ext','path'
      );
    mass_inserts(IMAGES_TABLE, $dbfields, $inserts);

    // what are the ids of the pictures in the $category_id ?
    $ids = array();

    $query = '
SELECT id
  FROM '.IMAGES_TABLE.'
  WHERE storage_category_id = '.$category_id.'
;';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      array_push($ids, $row['id']);
    }

    // recreation of the links between this storage category pictures and
    // its storage category
    $query = '
DELETE FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id = '.$category_id.'
    AND image_id IN ('.implode(',', $ids).')
;';
    pwg_query($query);

    foreach ($ids as $num => $image_id)
    {
      $ids[$num] =  '('.$category_id.','.$image_id.')';
    }
    $query = '
INSERT INTO '.IMAGE_CATEGORY_TABLE.'
  (category_id,image_id) VALUES
  '.implode(',', $ids).'
;';
    pwg_query($query);

    // set a new representative element for this category
    $query = '
SELECT image_id
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id = '.$category_id.'
  ORDER BY RAND()
  LIMIT 0,1
;';
    list($representative) = mysql_fetch_array(pwg_query($query));
    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET representative_picture_id = '.$representative.'
  WHERE id = '.$category_id.'
;';
    pwg_query($query);
  }
  return $output;
}
// +-----------------------------------------------------------------------+
// |                        template initialization                        |
// +-----------------------------------------------------------------------+
$template->set_filenames(array('update'=>'admin/update.tpl'));

$base_url = PHPWG_ROOT_PATH.'admin.php?page=update';

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
    'L_RESULT_UPDATE'=>$lang['update_part_research'],
    'L_NB_NEW_ELEMENTS'=>$lang['update_nb_new_elements'],
    'L_NB_NEW_CATEGORIES'=>$lang['update_nb_new_categories'],
    'L_NB_DEL_ELEMENTS'=>$lang['update_nb_del_elements'],
    'L_NB_DEL_CATEGORIES'=>$lang['update_nb_del_categories'],
    'L_SEARCH_SUBCATS_INCLUDED'=>$lang['search_subcats_included'],
    
    'U_SYNC_DIRS'=>add_session_id($base_url.'&amp;update=dirs'),
    'U_SYNC_ALL'=>add_session_id($base_url.'&amp;update=all'),
    'U_SYNC_METADATA_NEW'=>add_session_id($base_url.'&amp;metadata=all:new'),
    'U_SYNC_METADATA_ALL'=>add_session_id($base_url.'&amp;metadata=all')
    ));
// +-----------------------------------------------------------------------+
// |                        introduction : choices                         |
// +-----------------------------------------------------------------------+
if (!isset($_POST['submit']))
{
  $template->assign_block_vars('introduction', array());

  $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE site_id != 1
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    array_push($user['restrictions'], $row['id']);
  }
  $user['forbidden_categories'] = implode(',', $user['restrictions']);
  $user['expand'] = true;
  $structure = create_user_structure('');
  display_select_categories($structure,
                            '&nbsp;',
                            array(),
                            'introduction.category_option',
                            array());
}
// +-----------------------------------------------------------------------+
// |                          synchronize files                            |
// +-----------------------------------------------------------------------+
else if (isset($_POST['submit'])
         and ($_POST['sync'] == 'dirs' or $_POST['sync'] == 'files'))
{
  $counts = array(
    'new_elements' => 0,
    'new_categories' => 0,
    'del_elements' => 0,
    'del_categories' => 0
    );

  if (isset($_POST['cat']))
  {
    $opts['category_id'] = $_POST['cat'];
  }
  else
  {
    $opts['category_id'] = 'NULL';
  }
  
  $start = get_moment();
  $categories = insert_local_category($opts['category_id']);
  echo get_elapsed_time($start,get_moment()).' for scanning directories<br />';
  
  $template->assign_block_vars(
    'update',
    array(
      'CATEGORIES'=>$categories,
      'NB_NEW_CATEGORIES'=>$counts['new_categories'],
      'NB_DEL_CATEGORIES'=>$counts['del_categories'],
      'NB_NEW_ELEMENTS'=>$counts['new_elements'],
      'NB_DEL_ELEMENTS'=>$counts['del_elements']
      ));
  $start = get_moment();
  update_category('all');
  echo get_elapsed_time($start,get_moment()).' for update_category(all)<br />';
  $start = get_moment();
  ordering();
  echo get_elapsed_time($start, get_moment()).' for ordering categories<br />';
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