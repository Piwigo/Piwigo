<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2010      Pierrick LE GALL             http://piwigo.org |
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

if (!defined('PHOTOS_ADD_BASE_URL'))
{
  die ("Hacking attempt!");
}

// +-----------------------------------------------------------------------+
// |                        batch management request                       |
// +-----------------------------------------------------------------------+

if (isset($_GET['batch']))
{
  check_input_parameter('batch', $_GET, false, '/^\d+(,\d+)*$/');

  $query = '
DELETE FROM '.CADDIE_TABLE.'
  WHERE user_id = '.$user['id'].'
;';
  pwg_query($query);

  $inserts = array();
  foreach (explode(',', $_GET['batch']) as $image_id)
  {
    array_push(
      $inserts,
      array(
        'user_id' => $user['id'],
        'element_id' => $image_id,
        )
      );
  }
  mass_inserts(
    CADDIE_TABLE,
    array_keys($inserts[0]),
    $inserts
    );

  redirect(get_root_url().'admin.php?page=batch_manager&cat=caddie');
}

// +-----------------------------------------------------------------------+
// |                             process form                              |
// +-----------------------------------------------------------------------+

if (isset($_GET['processed']))
{
//   echo '<pre>POST'."\n"; print_r($_POST); echo '</pre>';
//   echo '<pre>FILES'."\n"; print_r($_FILES); echo '</pre>';
//   echo '<pre>SESSION'."\n"; print_r($_SESSION); echo '</pre>';
//   exit();

  // sometimes, you have submitted the form but you have nothing in $_POST
  // and $_FILES. This may happen when you have an HTML upload and you
  // exceeded the post_max_size (but not the upload_max_size)
  if (!isset($_POST['submit_upload']))
  {
    array_push(
      $page['errors'],
      sprintf(
        l10n('The uploaded files exceed the post_max_size directive in php.ini: %sB'),
        ini_get('post_max_size')
        )
      );
  }
  
  $category_id = null;
  if (!isset($_POST['category_type']))
  {
    // nothing to do, we certainly have the post_max_size issue
  }
  elseif ('existing' == $_POST['category_type'])
  {
    $category_id = $_POST['category'];
  }
  elseif ('new' == $_POST['category_type'])
  {
    $output_create = create_virtual_category(
      $_POST['category_name'],
      (0 == $_POST['category_parent'] ? null : $_POST['category_parent'])
      );
    
    $category_id = $output_create['id'];

    if (isset($output_create['error']))
    {
      array_push($page['errors'], $output_create['error']);
    }
    else
    {
      $category_name = get_cat_display_name_from_id($category_id, 'admin.php?page=cat_modify&amp;cat_id=');
      // information
      array_push(
        $page['infos'],
        sprintf(
          l10n('Album "%s" has been added'),
          '<em>'.$category_name.'</em>'
          )
        );
      // TODO: add the onclick="window.open(this.href); return false;"
      // attribute with jQuery on upload.tpl side for href containing
      // "cat_modify"
    }
  }

  $image_ids = array();
        
  if (isset($_FILES) and !empty($_FILES['image_upload']))
  {
    $starttime = get_moment();

  foreach ($_FILES['image_upload']['error'] as $idx => $error)
  {
    if (UPLOAD_ERR_OK == $error)
    {
      $images_to_add = array();
      
      $extension = pathinfo($_FILES['image_upload']['name'][$idx], PATHINFO_EXTENSION);
      if ('zip' == strtolower($extension))
      {
        $upload_dir = $conf['upload_dir'].'/buffer';
        prepare_directory($upload_dir);
        
        $temporary_archive_name = date('YmdHis').'-'.generate_key(10);
        $archive_path = $upload_dir.'/'.$temporary_archive_name.'.zip';
        
        move_uploaded_file(
          $_FILES['image_upload']['tmp_name'][$idx],
          $archive_path
          );

        define('PCLZIP_TEMPORARY_DIR', $upload_dir.'/');
        include(PHPWG_ROOT_PATH.'admin/include/pclzip.lib.php');
        $zip = new PclZip($archive_path);
        if ($list = $zip->listContent())
        {
          $indexes_to_extract = array();
          
          foreach ($list as $node)
          {
            if (1 == $node['folder'])
            {
              continue;
            }

            if (is_valid_image_extension(pathinfo($node['filename'], PATHINFO_EXTENSION)))
            {
              array_push($indexes_to_extract, $node['index']);
              
              array_push(
                $images_to_add,
                array(
                  'source_filepath' => $upload_dir.'/'.$temporary_archive_name.'/'.$node['filename'],
                  'original_filename' => basename($node['filename']),
                  )
                );
            }
          }
      
          if (count($indexes_to_extract) > 0)
          {
            $zip->extract(
              PCLZIP_OPT_PATH, $upload_dir.'/'.$temporary_archive_name,
              PCLZIP_OPT_BY_INDEX, $indexes_to_extract,
              PCLZIP_OPT_ADD_TEMP_FILE_ON
              );
          }
        }
      }
      elseif (is_valid_image_extension($extension))
      {
        array_push(
          $images_to_add,
          array(
            'source_filepath' => $_FILES['image_upload']['tmp_name'][$idx],
            'original_filename' => $_FILES['image_upload']['name'][$idx],
            )
          );
      }

      foreach ($images_to_add as $image_to_add)
      {
        $image_id = add_uploaded_file(
          $image_to_add['source_filepath'],
          $image_to_add['original_filename'],
          array($category_id),
          $_POST['level']
          );

        array_push($image_ids, $image_id);

        // TODO: if $image_id is not an integer, something went wrong
      }
    }
    else
    {
      $error_message = file_upload_error_message($error);
      
      array_push(
        $page['errors'],
        sprintf(
          l10n('Error on file "%s" : %s'),
          $_FILES['image_upload']['name'][$idx],
          $error_message
          )
        );
    }
  }
  
  $endtime = get_moment();
  $elapsed = ($endtime - $starttime) * 1000;
  // printf('%.2f ms', $elapsed);

  } // if (!empty($_FILES))

  if (isset($_POST['upload_id']))
  {
    // we're on a multiple upload, with uploadify and so on
    if (isset($_SESSION['uploads_error'][ $_POST['upload_id'] ]))
    {
      foreach ($_SESSION['uploads_error'][ $_POST['upload_id'] ] as $error)
      {
        array_push($page['errors'], $error);
      }
    }

    if (isset($_SESSION['uploads'][ $_POST['upload_id'] ]))
    {
      $image_ids = $_SESSION['uploads'][ $_POST['upload_id'] ];

      associate_images_to_categories(
        $image_ids,
        array($category_id)
        );

      $query = '
UPDATE '.IMAGES_TABLE.'
  SET level = '.$_POST['level'].'
  WHERE id IN ('.implode(', ', $image_ids).')
;';
      pwg_query($query);
    
      invalidate_user_cache();
    }
  }
  
  $page['thumbnails'] = array();
  foreach ($image_ids as $image_id)
  {
    // we could return the list of properties from the add_uploaded_file
    // function, but I like the "double check". And it costs nothing
    // compared to the upload process.
    $thumbnail = array();
      
    $query = '
SELECT
    file,
    path,
    tn_ext
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$image_id.'
;';
    $image_infos = pwg_db_fetch_assoc(pwg_query($query));

    $thumbnail['file'] = $image_infos['file'];
    
    $thumbnail['src'] = get_thumbnail_location(
      array(
        'path' => $image_infos['path'],
        'tn_ext' => $image_infos['tn_ext'],
        )
      );

    // TODO: when implementing this plugin in Piwigo core, we should have
    // a function get_image_name($name, $file) (if name is null, then
    // compute a temporary name from filename) that would be also used in
    // picture.php. UPDATE: in fact, "get_name_from_file($file)" already
    // exists and is used twice (batch_manager_unit + comments, but not in
    // picture.php I don't know why) with the same pattern if
    // (empty($name)) {$name = get_name_from_file($file)}, a clean
    // function get_image_name($name, $file) would be better
    $thumbnail['title'] = get_name_from_file($image_infos['file']);

    $thumbnail['link'] = PHPWG_ROOT_PATH.'admin.php?page=picture_modify'
      .'&amp;image_id='.$image_id
      .'&amp;cat_id='.$category_id
      ;

    array_push($page['thumbnails'], $thumbnail);
  }
  
  if (!empty($page['thumbnails']))
  {
    array_push(
      $page['infos'],
      sprintf(
        l10n('%d photos uploaded'),
        count($page['thumbnails'])
        )
      );
    
    if (0 != $_POST['level'])
    {
      array_push(
        $page['infos'],
        sprintf(
          l10n('Privacy level set to "%s"'),
          l10n(
            sprintf('Level %d', $_POST['level'])
            )
          )
        );
    }

    if ('existing' == $_POST['category_type'])
    {
      $query = '
SELECT
    COUNT(*)
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id = '.$category_id.'
;';
      list($count) = pwg_db_fetch_row(pwg_query($query));
      $category_name = get_cat_display_name_from_id($category_id, 'admin.php?page=cat_modify&amp;cat_id=');
      
      // information
      array_push(
        $page['infos'],
        sprintf(
          l10n('Album "%s" now contains %d photos'),
          '<em>'.$category_name.'</em>',
          $count
          )
        );
    }

    $page['batch_link'] = PHOTOS_ADD_BASE_URL.'&batch='.implode(',', $image_ids);
  }
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$uploadify_path = PHPWG_ROOT_PATH.'admin/include/uploadify';

$template->assign(
    array(
      'F_ADD_ACTION'=> PHOTOS_ADD_BASE_URL,
      'uploadify_path' => $uploadify_path,
      'upload_max_filesize' => min(
        get_ini_size('upload_max_filesize'),
        get_ini_size('post_max_size')
        ),
    )
  );

$upload_modes = array('html', 'multiple');
$upload_mode = isset($conf['upload_mode']) ? $conf['upload_mode'] : 'multiple';

if (isset($_GET['upload_mode']) and in_array($_GET['upload_mode'], $upload_modes))
{
  $upload_mode = $_GET['upload_mode'];
  conf_update_param('upload_mode', $upload_mode);
}

// what is the upload switch mode
$index_of_upload_mode = array_flip($upload_modes);
$upload_mode_index = $index_of_upload_mode[$upload_mode];
$upload_switch = $upload_modes[ ($upload_mode_index + 1) % 2 ];

$template->assign(
    array(
      'upload_mode' => $upload_mode,
      'form_action' => PHOTOS_ADD_BASE_URL.'&amp;upload_mode='.$upload_mode.'&amp;processed=1',
      'switch_url' => PHOTOS_ADD_BASE_URL.'&amp;upload_mode='.$upload_switch,
      'upload_id' => md5(rand()),
      'session_id' => session_id(),
      'pwg_token' => get_pwg_token(),
      'another_upload_link' => PHOTOS_ADD_BASE_URL.'&amp;upload_mode='.$upload_mode,
    )
  );

$template->append(
  'head_elements',
  '<link rel="stylesheet" type="text/css" href="'.$uploadify_path.'/uploadify.css">'."\n"
  );

if (isset($page['thumbnails']))
{
  $template->assign(
    array(
      'thumbnails' => $page['thumbnails'],
      )
    );

  // only display the batch link if we have more than 1 photo
  if (count($page['thumbnails']) > 1)
  {
    $template->assign(
      array(
        'batch_link' => $page['batch_link'],
        'batch_label' => sprintf(
          l10n('Manage this set of %d photos'),
          count($page['thumbnails'])
          ),
        )
      );
  }
}

// categories
//
// we need to know the category in which the last photo was added
$selected_category = array();
$selected_parent = array();

$query = '
SELECT
    category_id,
    id_uppercat
  FROM '.IMAGES_TABLE.' AS i
    JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON image_id = i.id
    JOIN '.CATEGORIES_TABLE.' AS c ON category_id = c.id
  ORDER BY i.id DESC
  LIMIT 1
;';
$result = pwg_query($query);
if (pwg_db_num_rows($result) > 0)
{
  $row = pwg_db_fetch_assoc($result);
  
  $selected_category = array($row['category_id']);

  if (!empty($row['id_uppercat']))
  {
    $selected_parent = array($row['id_uppercat']);
  }
}

// existing album
$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
;';

display_select_cat_wrapper(
  $query,
  $selected_category,
  'category_options'
  );

// new category
display_select_cat_wrapper(
  $query,
  $selected_parent,
  'category_parent_options'
  );


// image level options
$selected_level = isset($_POST['level']) ? $_POST['level'] : 0;
$template->assign(
    array(
      'level_options'=> get_privacy_level_options(),
      'level_options_selected' => array($selected_level)
    )
  );

// +-----------------------------------------------------------------------+
// |                         setup errors/warnings                         |
// +-----------------------------------------------------------------------+

// Errors
$setup_errors = array();

$error_message = ready_for_upload_message();
if (!empty($error_message))
{
  array_push($setup_errors, $error_message);
}

if (!function_exists('gd_info'))
{
  array_push($setup_errors, l10n('GD library is missing'));
}

$template->assign(
  array(
    'setup_errors'=> $setup_errors,
    )
  );

// Warnings
if (isset($_GET['hide_warnings']))
{
  $_SESSION['upload_hide_warnings'] = true;
}

if (!isset($_SESSION['upload_hide_warnings']))
{
  $setup_warnings = array();
  
  if ($conf['use_exif'] and !function_exists('read_exif_data'))
  {
    array_push(
      $setup_warnings,
      l10n('Exif extension not available, admin should disable exif use')
      );
  }

  if (get_ini_size('upload_max_filesize') > get_ini_size('post_max_size'))
  {
    array_push(
      $setup_warnings,
      sprintf(
        l10n('In your php.ini file, the upload_max_filesize (%sB) is bigger than post_max_size (%sB), you should change this setting'),
        get_ini_size('upload_max_filesize', false),
        get_ini_size('post_max_size', false)
        )
      );
  }

  $template->assign(
    array(
      'setup_warnings' => $setup_warnings,
      'hide_warnings_link' => PHOTOS_ADD_BASE_URL.'&amp;upload_mode='.$upload_mode.'&amp;hide_warnings=1'
      )
    );
}

// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'photos_add');
?>
