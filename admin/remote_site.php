<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
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
  die ("Hacking attempt!");
}
include_once(PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php');

define('CURRENT_DATE', date('Y-m-d'));
// +-----------------------------------------------------------------------+
// |                               functions                               |
// +-----------------------------------------------------------------------+

/**
 * requests the given $url (a remote create_listing_file.php) and fills a
 * list of lines corresponding to request output
 *
 * @param string $url
 * @return void
 */
function remote_output($url)
{
  global $template, $errors, $lang;
  
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
    array_push($errors, $lang['remote_site_file_not_found']);
  }
}

/**
 * returns an array where are linked the sub-categories id and there
 * directories corresponding to the given uppercat id
 *
 * @param int site_id
 * @param mixed id_uppercat
 * @return array
 */
function database_subdirs($site_id, $id_uppercat)
{
  $database_dirs = array();
  
  $query = '
SELECT id,dir
  FROM '.CATEGORIES_TABLE.'
  WHERE site_id = '.$site_id;
  if (!is_numeric($id_uppercat))
  {
    $query.= '
    AND id_uppercat IS NULL';
  }
  else
  {
    $query.= '
    AND id_uppercat = '.$id_uppercat;
  }
  // virtual categories not taken
  $query.= '
    AND dir IS NOT NULL
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    $database_dirs[$row['id']] = $row['dir'];
  }

  return $database_dirs;
}

/**
 * read $listing_file and update a remote site according to its id
 *
 * @param string listing_file
 * @param int site_id
 * @return void
 */
function update_remote_site($listing_file, $site_id)
{
  global $lang, $counts, $template, $removes, $errors;
  
  if (@fopen($listing_file, 'r'))
  {
    $counts = array(
      'new_elements' => 0,
      'new_categories' => 0,
      'del_elements' => 0,
      'del_categories' => 0
      );
    $removes = array();
        
    $xml_content = getXmlCode($listing_file);
    insert_remote_category($xml_content, $site_id, 'NULL', 0);
    update_category();
    ordering();
    update_global_rank();
        
    $template->assign_block_vars(
      'update',
      array(
        'NB_NEW_CATEGORIES'=>$counts['new_categories'],
        'NB_DEL_CATEGORIES'=>$counts['del_categories'],
        'NB_NEW_ELEMENTS'=>$counts['new_elements'],
        'NB_DEL_ELEMENTS'=>$counts['del_elements']
        ));
        
    if (count($removes) > 0)
    {
      $template->assign_block_vars('update.removes', array());
    }
    foreach ($removes as $remove)
    {
      $template->assign_block_vars('update.removes.remote_remove',
                                   array('NAME'=>$remove));
    }
  }
  else
  {
    array_push($errors, $lang['remote_site_listing_not_found']);
  }
}

/**
 * searchs the "dir" node of the xml_dir given and insert the contained
 * categories if the are not in the database yet. The function also deletes
 * the categories that are in the database and not in the xml_file.
 *
 * @param string xml_content
 * @param int site_id
 * @param mixed id_uppercat
 * @param int level
 * @return void
 */
function insert_remote_category($xml_content, $site_id, $id_uppercat, $level)
{
  global $counts, $removes, $conf;
  
  $uppercats = '';
  // 0. retrieving informations on the category to display
		
  if (is_numeric($id_uppercat))
  {
    $query = '
SELECT id,name,uppercats,dir,status,visible
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$id_uppercat.'
;';
    $row = mysql_fetch_array(pwg_query($query));
    $parent = array('id' => $row['id'],
                    'name' => $row['name'],
                    'dir' => $row['dir'],
                    'uppercats' => $row['uppercats'],
                    'visible' => $row['visible'],
                    'status' => $row['status']);
    
    insert_remote_element($xml_content, $id_uppercat);
  }

  // $xml_dirs contains dir names contained in the xml file for this
  // id_uppercat
  $xml_dirs = array();
  $temp_dirs = getChildren($xml_content, 'dir'.$level);
  foreach ($temp_dirs as $temp_dir)
  {
    array_push($xml_dirs, getAttribute($temp_dir, 'name'));
  }

  // $database_dirs contains dir names contained in the database for this
  // id_uppercat and site_id
  $database_dirs = database_subdirs($site_id, $id_uppercat);
  
  // 3. we have to remove the categories of the database not present anymore
  $to_delete = array();
  foreach ($database_dirs as $id => $dir)
  {
    if (!in_array($dir, $xml_dirs))
    {
      array_push($to_delete, $id);
      array_push($removes, get_complete_dir($id));
    }
  }
  delete_categories($to_delete);

  // array of new categories to insert
  $inserts = array();
  
  // calculate default value at category creation
  $create_values = array();
  if (isset($parent))
  {
    // at creation, must a category be visible or not ? Warning : if
    // the parent category is invisible, the category is automatically
    // create invisible. (invisible = locked)
    if ('false' == $parent['visible'])
    {
      $create_values{'visible'} = 'false';
    }
    else
    {
      $create_values{'visible'} = $conf['newcat_default_visible'];
    }
    // at creation, must a category be public or private ? Warning :
    // if the parent category is private, the category is
    // automatically create private.
    if ('private' == $parent['status'])
    {
      $create_values{'status'} = 'private';
    }
    else
    {
      $create_values{'status'} = $conf['newcat_default_status'];
    }
  }
  else
  {
    $create_values{'visible'} = $conf['newcat_default_visible'];
    $create_values{'status'} = $conf['newcat_default_status'];
  }

  foreach ($xml_dirs as $xml_dir)
  {
    // 5. Is the category already existing ? we create a subcat if not
    //    existing
    $category_id = array_search($xml_dir, $database_dirs);
    if (!is_numeric($category_id))
    {
      $name = str_replace('_', ' ', $xml_dir);

      $insert = array();

      $insert{'dir'} = $xml_dir;
      $insert{'name'} = $name;
      $insert{'site_id'} = $site_id;
      $insert{'uppercats'} = 'undef';
      $insert{'commentable'} = $conf['newcat_default_commentable'];
      $insert{'uploadable'} = 'false';
      $insert{'status'} = $create_values{'status'};
      $insert{'visible'} = $create_values{'visible'};
      if (isset($parent))
      {
        $insert{'id_uppercat'} = $parent['id'];
      }
      array_push($inserts, $insert);
    }
  }

  // we have to create the category
  if (count($inserts) > 0)
  {
    // inserts all found categories
    $dbfields = array('dir','name','site_id','uppercats','id_uppercat',
                      'commentable','uploadable','status','visible');
    mass_inserts(CATEGORIES_TABLE, $dbfields, $inserts);
    $counts{'new_categories'}+= count($inserts);
    
    // updating uppercats field
    $query = '
UPDATE '.CATEGORIES_TABLE;
    if (isset($parent))
    {
      $query.= "
  SET uppercats = CONCAT('".$parent['uppercats']."',',',id)
  WHERE id_uppercat = ".$id_uppercat;
    }
    else
    {
      $query.= '
  SET uppercats = id
  WHERE id_uppercat IS NULL';
    }
    $query.= '
;';
    pwg_query($query);
  }

  // Recursive call on the sub-categories (not virtual ones)
  $database_dirs = database_subdirs($site_id, $id_uppercat);
  
  foreach ($temp_dirs as $temp_dir)
  {
    $dir = getAttribute($temp_dir, 'name');
    $id_uppercat = array_search($dir, $database_dirs);
    insert_remote_category($temp_dir, $site_id, $id_uppercat, $level+1);
  }
}

/**
 * searchs the "root" node of $xml_dir (xml string), inserts elements in the
 * database if new
 *
 * @param string xml_dir
 * @param int category_id
 * @return void
 */
function insert_remote_element($xml_dir, $category_id)
{
  global $counts, $lang, $removes;

  $output = '';
  $root = getChild($xml_dir, 'root');

  $xml_files = array();
  $xml_elements = getChildren($root, 'element');
  foreach ($xml_elements as $xml_element)
  {
    array_push($xml_files, getAttribute($xml_element,'file'));
  }
  
  // we have to delete all the images from the database that are not in the
  // directory anymore (not in the XML anymore)
  $query = '
SELECT id,file
  FROM '.IMAGES_TABLE.'
  WHERE storage_category_id = '.$category_id.'
;';
  $result = pwg_query($query);
  $to_delete = array();
  while ($row = mysql_fetch_array($result))
  {
    if (!in_array($row['file'], $xml_files))
    {
      // local_dir is cached
      if (!isset($local_dir))
      {
        $local_dir = get_local_dir($category_id);
      }
      array_push($removes, $local_dir.$row['file']);
      array_push($to_delete, $row['id']);
    }
  }
  delete_elements($to_delete);

  $database_elements = array();
  $query = '
SELECT file
  FROM '.IMAGES_TABLE.'
  WHERE storage_category_id = '.$category_id.'
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    array_push($database_elements, $row['file']);
  }

  $inserts = array();
  foreach ($xml_elements as $xml_element)
  {
    // minimal tag : <element file="albatros.jpg"/>
    $file = getAttribute($xml_element, 'file');

    // is the picture already existing in the database ?
    if (!in_array($file, $database_elements))
    {
      $insert = array();
      $insert{'file'} = $file;
      $insert{'storage_category_id'} = $category_id;
      $insert{'date_available'} = CURRENT_DATE;
      $optional_atts = array('tn_ext',
                             'representative_ext',
                             'filesize',
                             'width',
                             'height',
                             'date_creation',
                             'author',
                             'keywords',
                             'name',
                             'comment',
                             'path');
      foreach ($optional_atts as $att)
      {
        if (getAttribute($xml_element, $att) != '')
        {
          $insert{$att} = getAttribute($xml_element, $att);
        }
      }
      array_push($inserts, $insert);
    }
  }

  if (count($inserts) > 0)
  {
    $dbfields = array('file','storage_category_id','date_available','tn_ext',
                      'filesize','width','height','date_creation','author',
                      'keywords','name','comment','path');
    mass_inserts(IMAGES_TABLE, $dbfields, $inserts);
    $counts{'new_elements'}+= count($inserts);

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

    $query = '
INSERT INTO '.IMAGE_CATEGORY_TABLE.'
  (category_id,image_id)
  VALUES';
    foreach ($ids as $num => $image_id)
    {
      $query.= '
  ';
      if ($num > 0)
      {
        $query.= ',';
      }
      $query.= '('.$category_id.','.$image_id.')';
    }
    $query.= '
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
}
// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+
$template->set_filenames(array('remote_site'=>'admin/remote_site.tpl'));

$template->assign_vars(
  array(
    'L_SUBMIT'=>$lang['submit'],
    'L_REMOTE_SITE_CREATE'=>$lang['remote_site_create'],
    'L_REMOTE_SITE_GENERATE'=>$lang['remote_site_generate'],
    'L_REMOTE_SITE_GENERATE_HINT'=>$lang['remote_site_generate_hint'],
    'L_REMOTE_SITE_UPDATE'=>$lang['remote_site_update'],
    'L_REMOTE_SITE_UPDATE_HINT'=>$lang['remote_site_update_hint'],
    'L_REMOTE_SITE_CLEAN'=>$lang['remote_site_clean'],
    'L_REMOTE_SITE_CLEAN_HINT'=>$lang['remote_site_clean_hint'],
    'L_REMOTE_SITE_DELETE'=>$lang['remote_site_delete'],
    'L_REMOTE_SITE_DELETE_HINT'=>$lang['remote_site_delete_hint'],
    'L_NB_NEW_ELEMENTS'=>$lang['update_nb_new_elements'],
    'L_NB_NEW_CATEGORIES'=>$lang['update_nb_new_categories'],
    'L_NB_DEL_ELEMENTS'=>$lang['update_nb_del_elements'],
    'L_NB_DEL_CATEGORIES'=>$lang['update_nb_del_categories'],
    'L_REMOTE_SITE_REMOVED_TITLE'=>$lang['remote_site_removed_title'],
    'L_REMOTE_SITE_REMOVED'=>$lang['remote_site_removed'],
    'L_REMOTE_SITE_LOCAL_FOUND'=>$lang['remote_site_local_found'],
    'L_REMOTE_SITE_LOCAL_NEW'=>$lang['remote_site_local_new'],
    'L_REMOTE_SITE_LOCAL_UPDATE'=>$lang['remote_site_local_update'],
    
    'F_ACTION'=>add_session_id(PHPWG_ROOT_PATH.'admin.php?page=remote_site')
   )
 );
// +-----------------------------------------------------------------------+
// |                        new site creation form                         |
// +-----------------------------------------------------------------------+
$errors = array();

if (isset($_POST['submit']))
{
  // site must start by http:// or https://
  if (!preg_match('/^https?:\/\/[~\/\.\w-]+$/', $_POST['galleries_url']))
  {
    array_push($errors, $lang['remote_site_uncorrect_url']);
  }
  else
  {
    $page['galleries_url'] = preg_replace('/[\/]*$/',
                                          '',
                                          $_POST['galleries_url']);
    $page['galleries_url'].= '/';
    // site must not exists
    $query = '
SELECT COUNT(id) AS count
  FROM '.SITES_TABLE.'
  WHERE galleries_url = \''.$page['galleries_url'].'\'
;';
    $row = mysql_fetch_array(pwg_query($query));
    if ($row['count'] > 0)
    {
      array_push($errors, $lang['remote_site_already_exists']);
    }
  }

  if (count($errors) == 0)
  {
    $url = $page['galleries_url'].'create_listing_file.php';
    $url.= '?action=test';
    $url.= '&version='.PHPWG_VERSION;
    if ($lines = @file($url))
    {
      $first_line = strip_tags($lines[0]);
      if (!preg_match('/^PWG-INFO-2:/', $first_line))
      {
        array_push($errors, $lang['remote_site_error'].' : '.$first_line);
      }
    }
    else
    {
      array_push($errors, $lang['remote_site_file_not_found']);
    }
  }
  
  if (count($errors) == 0)
  {
    $query = '
INSERT INTO '.SITES_TABLE.'
  (galleries_url)
  VALUES
  (\''.$page['galleries_url'].'\')
;';
    pwg_query($query);

    $template->assign_block_vars(
      'confirmation',
      array(
        'CONTENT'=>$page['galleries_url'].' '.$lang['remote_site_created']
        ));
  }
}
// +-----------------------------------------------------------------------+
// |                            actions on site                            |
// +-----------------------------------------------------------------------+
if (isset($_GET['site']) and is_numeric($_GET['site']))
{
  $page['site'] = $_GET['site'];
}

if (isset($_GET['action']))
{
  if (isset($page['site']))
  {
    $query = '
SELECT galleries_url
  FROM '.SITES_TABLE.'
  WHERE id = '.$page['site'].'
;';
    list($galleries_url) = mysql_fetch_array(pwg_query($query));
  }

  switch($_GET['action'])
  {
    case 'delete' :
    {
      delete_site($page['site']);

      $template->assign_block_vars(
        'confirmation',
        array(
          'CONTENT'=>$galleries_url.' '.$lang['remote_site_deleted']
          ));
      
      break;
    }
    case 'generate' :
    {
      $title = $galleries_url.' : '.$lang['remote_site_generate'];
      $template->assign_vars(array('REMOTE_SITE_TITLE'=>$title));
      remote_output($galleries_url.'create_listing_file.php?action=generate');
      break;
    }
    case 'update' :
    {
      $title = $galleries_url.' : '.$lang['remote_site_update'];
      $template->assign_vars(array('REMOTE_SITE_TITLE'=>$title));
      update_remote_site($galleries_url.'listing.xml', $page['site']);
      break;
    }
    case 'clean' :
    {
      $title = $galleries_url.' : '.$lang['remote_site_clean'];
      $template->assign_vars(array('REMOTE_SITE_TITLE'=>$title));
      remote_output($galleries_url.'create_listing_file.php?action=clean');
      break;
    }
    case 'local_update' :
    {
      $local_listing = PHPWG_ROOT_PATH.'listing.xml';
      $xml_content = getXmlCode($local_listing);
      $url = getAttribute(getChild($xml_content, 'informations'), 'url');

      // is the site already existing ?
      $query = '
SELECT id
  FROM '.SITES_TABLE.'
  WHERE galleries_url = \''.addslashes($url).'\'
;';
      $result = pwg_query($query);
      if (mysql_num_rows($result) == 0)
      {
        // we have to register this site in the database
        $query = '
INSERT INTO '.SITES_TABLE.'
  (galleries_url)
  VALUES
  (\''.$url.'\')
;';
        pwg_query($query);
        $site_id = mysql_insert_id();
      }
      else
      {
        // we get the already registered id
        $row = mysql_fetch_array($result);
        $site_id = $row['id'];
      }
      
      $title = $url.' : '.$lang['remote_site_local_update'];
      $template->assign_vars(array('REMOTE_SITE_TITLE'=>$title));
      update_remote_site($local_listing, $site_id);
      break;
    }
  }
}
else
{
  // we search a "local" listing.xml file
  $local_listing = PHPWG_ROOT_PATH.'listing.xml';
  if (is_file($local_listing))
  {
    $xml_content = getXmlCode($local_listing);
    $url = getAttribute(getChild($xml_content, 'informations'), 'url');

    $base_url = PHPWG_ROOT_PATH.'admin.php?page=remote_site&amp;action=';
    
    $template->assign_block_vars(
      'local',
      array(
        'URL' => $url,
        'U_UPDATE' => add_session_id($base_url.'local_update')
        )
      );

    // is the site already existing ?
    $query = '
SELECT COUNT(*)
  FROM '.SITES_TABLE.'
  WHERE galleries_url = \''.addslashes($url).'\'
;';
    list($count) = mysql_fetch_array(pwg_query($query));
    if ($count == 0)
    {
      $template->assign_block_vars('local.new_site', array());
    }
  }
}
// +-----------------------------------------------------------------------+
// |                           remote sites list                           |
// +-----------------------------------------------------------------------+

// site 1 is the local site, should not be taken into account
$query = '
SELECT id, galleries_url
  FROM '.SITES_TABLE.'
  WHERE id != 1
;';
$result = pwg_query($query);
while ($row = mysql_fetch_array($result))
{
  $base_url = PHPWG_ROOT_PATH.'admin.php';
  $base_url.= '?page=remote_site';
  $base_url.= '&amp;site='.$row['id'];
  $base_url.= '&amp;action=';
  
  $template->assign_block_vars(
    'site',
    array(
      'NAME' => $row['galleries_url'],
      'U_GENERATE' => add_session_id($base_url.'generate'),
      'U_UPDATE' => add_session_id($base_url.'update'),
      'U_CLEAN' => add_session_id($base_url.'clean'),
      'U_DELETE' => add_session_id($base_url.'delete')
     )
   );
}
// +-----------------------------------------------------------------------+
// |                             errors display                            |
// +-----------------------------------------------------------------------+
if (count($errors) != 0)
{
  $template->assign_block_vars('errors',array());
  foreach ($errors as $error)
  {
    $template->assign_block_vars('errors.error',array('ERROR'=>$error));
  }
}
// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+
$template->assign_var_from_handle('ADMIN_CONTENT', 'remote_site');
?>