<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2013 Piwigo Team                  http://piwigo.org |
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
  die('Hacking attempt!');
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+

if (!$conf['enable_synchronization'])
{
  die('synchronization is disabled');
}

check_status(ACCESS_ADMINISTRATOR);

if (!is_numeric($_GET['site']))
{
  die ('site param missing or invalid');
}
$site_id = $_GET['site'];

$query='
SELECT galleries_url
  FROM '.SITES_TABLE.'
  WHERE id = '.$site_id;
list($site_url) = pwg_db_fetch_row(pwg_query($query));
if (!isset($site_url))
{
  die('site '.$site_id.' does not exist');
}
$site_is_remote = url_is_remote($site_url);

list($dbnow) = pwg_db_fetch_row(pwg_query('SELECT NOW();'));
define('CURRENT_DATE', $dbnow);

$error_labels = array(
  'PWG-UPDATE-1' => array(
    l10n('wrong filename'),
    l10n('The name of directories and files must be composed of letters, numbers, "-", "_" or "."')
    ),
  'PWG-ERROR-NO-FS' => array(
    l10n('File/directory read error'),
    l10n('The file or directory cannot be accessed (either it does not exist or the access is denied)')
    ),
  );
$errors = array();
$infos = array();

if ($site_is_remote)
{
  fatal_error('remote sites not supported');
}
else
{
  include_once( PHPWG_ROOT_PATH.'admin/site_reader_local.php');
  $site_reader = new LocalSiteReader($site_url);
}

$general_failure = true;
if (isset($_POST['submit']))
{
  if ($site_reader->open())
  {
    $general_failure = false;
  }

  // shall we simulate only
  if (isset($_POST['simulate']) and $_POST['simulate'] == 1)
  {
    $simulate = true;
  }
  else
  {
    $simulate = false;
  }
}

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
  $counts['upd_elements'] = 0;
}


if (isset($_POST['submit'])
    and ($_POST['sync'] == 'dirs' or $_POST['sync'] == 'files')
    and !$general_failure)
{
  $start = get_moment();
  // which categories to update ?
  $query = '
SELECT id, uppercats, global_rank, status, visible
  FROM '.CATEGORIES_TABLE.'
  WHERE dir IS NOT NULL
    AND site_id = '.$site_id;
  if (isset($_POST['cat']) and is_numeric($_POST['cat']))
  {
    if (isset($_POST['subcats-included']) and $_POST['subcats-included'] == 1)
    {
      $query.= '
    AND uppercats '.DB_REGEX_OPERATOR.' \'(^|,)'.$_POST['cat'].'(,|$)\'
';
    }
    else
    {
      $query.= '
    AND id = '.$_POST['cat'].'
';
    }
  }
  $db_categories = hash_from_query($query, 'id');

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
    $basedir = preg_replace('#/*$#', '', $site_url);
  }

  // we need to have fulldirs as keys to make efficient comparison
  $db_fulldirs = array_flip($db_fulldirs);

  // finding next rank for each id_uppercat. By default, each category id
  // has 1 for next rank on its sub-categories to create
  $next_rank['NULL'] = 1;

  $query = '
SELECT id
  FROM '.CATEGORIES_TABLE;
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    $next_rank[$row['id']] = 1;
  }

  // let's see if some categories already have some sub-categories...
  $query = '
SELECT id_uppercat, MAX(rank)+1 AS next_rank
  FROM '.CATEGORIES_TABLE.'
  GROUP BY id_uppercat';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    // for the id_uppercat NULL, we write 'NULL' and not the empty string
    if (!isset($row['id_uppercat']) or $row['id_uppercat'] == '')
    {
      $row['id_uppercat'] = 'NULL';
    }
    $next_rank[$row['id_uppercat']] = $row['next_rank'];
  }

  // next category id available
  $next_id = pwg_db_nextval('id', CATEGORIES_TABLE);

  // retrieve sub-directories fulldirs from the site reader
  $fs_fulldirs = $site_reader->get_full_directories($basedir);

  // get_full_directories doesn't include the base directory, so if it's a
  // category directory, we need to include it in our array
  if (isset($_POST['cat']))
  {
    array_push($fs_fulldirs, $basedir);
  }
  // If $_POST['subcats-included'] != 1 ("Search in sub-albums" is unchecked)
  // $db_fulldirs doesn't include any subdirectories and $fs_fulldirs does
  // So $fs_fulldirs will be limited to the selected basedir
  // (if that one is in $fs_fulldirs)
  if (!isset($_POST['subcats-included']) or $_POST['subcats-included'] != 1)
  {
    $fs_fulldirs = array_intersect($fs_fulldirs, array_keys($db_fulldirs));
  }
  $inserts = array();
  // new categories are the directories not present yet in the database
  foreach (array_diff($fs_fulldirs, array_keys($db_fulldirs)) as $fulldir)
  {
    $dir = basename($fulldir);
    if (preg_match($conf['sync_chars_regex'], $dir))
    {
      $insert = array(
        'id'          => $next_id++,
        'dir'         => $dir,
        'name'        => str_replace('_', ' ', $dir),
        'site_id'     => $site_id,
        'commentable' =>
          boolean_to_string($conf['newcat_default_commentable']),
        'status'      => $conf['newcat_default_status'],
        'visible'     => boolean_to_string($conf['newcat_default_visible']),
        );

      if (isset($db_fulldirs[dirname($fulldir)]))
      {
        $parent = $db_fulldirs[dirname($fulldir)];

        $insert['id_uppercat'] = $parent;
        $insert['uppercats'] =
          $db_categories[$parent]['uppercats'].','.$insert['id'];
        $insert['rank'] = $next_rank[$parent]++;
        $insert['global_rank'] =
          $db_categories[$parent]['global_rank'].'.'.$insert['rank'];
        if ('private' == $db_categories[$parent]['status'])
        {
          $insert['status'] = 'private';
        }
        if ('false' == $db_categories[$parent]['visible'])
        {
          $insert['visible'] = 'false';
        }
      }
      else
      {
        $insert['uppercats'] = $insert['id'];
        $insert{'rank'} = $next_rank['NULL']++;
        $insert['global_rank'] = $insert['rank'];
      }

      array_push($inserts, $insert);
      array_push(
        $infos,
        array(
          'path' => $fulldir,
          'info' => l10n('added')
          )
        );

      // add the new category to $db_categories and $db_fulldirs array
      $db_categories[$insert{'id'}] =
        array(
          'id' => $insert['id'],
          'parent' => (isset($parent)) ? $parent : Null,
          'status' => $insert['status'],
          'visible' => $insert['visible'],
          'uppercats' => $insert['uppercats'],
          'global_rank' => $insert['global_rank']
          );
      $db_fulldirs[$fulldir] = $insert['id'];
      $next_rank[$insert{'id'}] = 1;
    }
    else
    {
      array_push(
        $errors,
        array(
          'path' => $fulldir,
          'type' => 'PWG-UPDATE-1'
          )
        );
    }
  }

  if (count($inserts) > 0)
  {
    if (!$simulate)
    {
      $dbfields = array(
        'id','dir','name','site_id','id_uppercat','uppercats','commentable',
        'visible','status','rank','global_rank'
        );
      mass_inserts(CATEGORIES_TABLE, $dbfields, $inserts);

      // add default permissions to categories
      $category_ids = array();
      $category_up = array();
      foreach ($inserts as $category)
      {
        $category_ids[] = $category['id'];
        if (!empty($category['id_uppercat']))
        {
          $category_up[] = $category['id_uppercat'];
        }
      }
      $category_up=implode(',',array_unique($category_up));
      if ($conf['inheritance_by_default'])
      {
        $query = '
          SELECT *
          FROM '.GROUP_ACCESS_TABLE.'
          WHERE cat_id IN ('.$category_up.')
        ;';
        $result = pwg_query($query);
        if (!empty($result))
        {
          $granted_grps = array();
          while ($row = pwg_db_fetch_assoc($result))
          {
            if (!is_array ($granted_grps[$row['cat_id']]))
            {
              $granted_grps[$row['cat_id']]=array();
            }
            array_push(
              $granted_grps,
              array(
                $row['cat_id'] => array_push($granted_grps[$row['cat_id']],$row['group_id'])
              )
            );
          }
          $insert_granted_grps=array();
          foreach ($category_ids as $ids)
          {
            $parent_id=$db_categories[$ids]['parent'];
            while (in_array($parent_id, $category_ids))
            {
              $parent_id= $db_categories[$parent_id]['parent'];
            }
            if ($db_categories[$ids]['status']=='private' and !is_null($parent_id))
            {
              foreach ($granted_grps[$parent_id] as $granted_grp)
              {
                array_push(
                  $insert_granted_grps,
                  array(
                    'group_id' => $granted_grp,
                    'cat_id' => $ids
                  )
                );
               
              }
            }
          }
  
          mass_inserts(GROUP_ACCESS_TABLE, array('group_id','cat_id'), $insert_granted_grps);
        }

        $query = '
          SELECT *
          FROM '.USER_ACCESS_TABLE.'
          WHERE cat_id IN ('.$category_up.')
        ;';
        $result = pwg_query($query);
        if (!empty($result))
        {

          $granted_users = array();
          while ($row = pwg_db_fetch_assoc($result))
          {
            if (!is_array ($granted_users[$row['cat_id']]))
            {
              $granted_users[$row['cat_id']]=array();
            }
            array_push(
              $granted_users,
              array(
                $row['cat_id'] => array_push($granted_users[$row['cat_id']],$row['user_id'])
              )
            );
          }
          $insert_granted_users=array();
          foreach ($category_ids as $ids)
          {
            $parent_id=$db_categories[$ids]['parent'];
             while (in_array($parent_id, $category_ids))
            {
              $parent_id= $db_categories[$parent_id]['parent'];
            }
            if ($db_categories[$ids]['status']=='private' and !is_null($parent_id))
            {
              foreach ($granted_users[$parent_id] as $granted_user)
              {
                array_push(
                  $insert_granted_users,
                  array(
                    'user_id' => $granted_user,
                    'cat_id' => $ids
                  )
                );
              }
            }
          }
          mass_inserts(USER_ACCESS_TABLE, array('user_id','cat_id'), $insert_granted_users);
        }
      }     
      else
      {
        add_permission_on_category($category_ids, get_admins());
      }
    }

    $counts['new_categories'] = count($inserts);
  }

  // to delete categories
  $to_delete = array(); $to_delete_derivative_dirs = array();
  foreach (array_diff(array_keys($db_fulldirs), $fs_fulldirs) as $fulldir)
  {
    array_push($to_delete, $db_fulldirs[$fulldir]);
    unset($db_fulldirs[$fulldir]);
    array_push($infos, array('path' => $fulldir,
                             'info' => l10n('deleted')));
    if (substr_compare($fulldir, '../', 0, 3)==0)
    {
      $fulldir = substr($fulldir, 3);
    }
    $to_delete_derivative_dirs[] = PHPWG_ROOT_PATH.PWG_DERIVATIVE_DIR.$fulldir;
  }
  if (count($to_delete) > 0)
  {
    if (!$simulate)
    {
      delete_categories($to_delete);
      foreach($to_delete_derivative_dirs as $to_delete_dir)
      {
        if (is_dir($to_delete_dir))
        {
          clear_derivative_cache_rec($to_delete_dir, '#.+#');
        }
      }
    }
    $counts['del_categories'] = count($to_delete);
  }

  $template->append('footer_elements', '<!-- scanning dirs : '
    . get_elapsed_time($start, get_moment())
    . ' -->' );
}
// +-----------------------------------------------------------------------+
// |                           files / elements                            |
// +-----------------------------------------------------------------------+
if (isset($_POST['submit']) and $_POST['sync'] == 'files'
      and !$general_failure)
{
  $start_files = get_moment();
  $start= $start_files;

  $fs = $site_reader->get_elements($basedir);
  $template->append('footer_elements', '<!-- get_elements: '
    . get_elapsed_time($start, get_moment())
    . ' -->' );

  $cat_ids = array_diff(array_keys($db_categories), $to_delete);

  $db_elements = array();

  if (count($cat_ids) > 0)
  {
    $query = '
SELECT id, path
  FROM '.IMAGES_TABLE.'
  WHERE storage_category_id IN ('
      .wordwrap(
        implode(', ', $cat_ids),
        160,
        "\n"
        ).')';
    $db_elements = simple_hash_from_query($query, 'id', 'path');
  }

  // next element id available
  $next_element_id = pwg_db_nextval('id', IMAGES_TABLE);

  $start = get_moment();

  $inserts = array();
  $insert_links = array();

  foreach (array_diff(array_keys($fs), $db_elements) as $path)
  {
    $insert = array();
    // storage category must exist
    $dirname = dirname($path);
    if (!isset($db_fulldirs[$dirname]))
    {
      continue;
    }
    $filename = basename($path);
    if (!preg_match($conf['sync_chars_regex'], $filename))
    {
      array_push(
        $errors,
        array(
          'path' => $path,
          'type' => 'PWG-UPDATE-1'
          )
        );

      continue;
    }

    $insert = array(
      'id'             => $next_element_id++,
      'file'           => $filename,
      'name'           => get_name_from_file($filename),
      'date_available' => CURRENT_DATE,
      'path'           => $path,
      'representative_ext'  => $fs[$path]['representative_ext'],
      'storage_category_id' => $db_fulldirs[$dirname],
      'added_by'       => $user['id'],
      );

    if ( $_POST['privacy_level']!=0 )
    {
      $insert['level'] = $_POST['privacy_level'];
    }

    array_push(
      $inserts,
      $insert
      );

    array_push(
      $insert_links,
      array(
        'image_id'    => $insert['id'],
        'category_id' => $insert['storage_category_id'],
        )
      );

    array_push(
      $infos,
      array(
        'path' => $insert['path'],
        'info' => l10n('added')
        )
      );

    $caddiables[] = $insert['id'];
  }

  if (count($inserts) > 0)
  {
    if (!$simulate)
    {
      // inserts all new elements
      mass_inserts(
        IMAGES_TABLE,
        array_keys($inserts[0]),
        $inserts
        );

      // inserts all links between new elements and their storage category
      mass_inserts(
        IMAGE_CATEGORY_TABLE,
        array_keys($insert_links[0]),
        $insert_links
        );

      // add new photos to caddie
      if (isset($_POST['add_to_caddie']) and $_POST['add_to_caddie'] == 1)
      {
        fill_caddie($caddiables);
      }
    }
    $counts['new_elements'] = count($inserts);
  }

  // delete elements that are in database but not in the filesystem
  $to_delete_elements = array();
  foreach (array_diff($db_elements, array_keys($fs)) as $path)
  {
    array_push($to_delete_elements, array_search($path, $db_elements));
    array_push($infos, array('path' => $path,
                             'info' => l10n('deleted')));
  }
  if (count($to_delete_elements) > 0)
  {
    if (!$simulate)
    {
      delete_elements($to_delete_elements);
    }
    $counts['del_elements'] = count($to_delete_elements);
  }

  $template->append('footer_elements', '<!-- scanning files : '
    . get_elapsed_time($start_files, get_moment())
    . ' -->' );
}

// +-----------------------------------------------------------------------+
// |                          synchronize files                            |
// +-----------------------------------------------------------------------+
if (isset($_POST['submit'])
    and ($_POST['sync'] == 'dirs' or $_POST['sync'] == 'files')
    and !$general_failure )
{
  if (!$simulate)
  {
    $start = get_moment();
    update_category('all');
    $template->append('footer_elements', '<!-- update_category(all) : '
      . get_elapsed_time($start,get_moment())
      . ' -->' );
    $start = get_moment();
    update_global_rank();
    $template->append('footer_elements', '<!-- ordering categories : '
      . get_elapsed_time($start, get_moment())
      . ' -->');
  }

  if ($_POST['sync'] == 'files')
  {
    $start = get_moment();
    $opts['category_id'] = '';
    $opts['recursive'] = true;
    if (isset($_POST['cat']))
    {
      $opts['category_id'] = $_POST['cat'];
      if (!isset($_POST['subcats-included']) or $_POST['subcats-included'] != 1)
      {
        $opts['recursive'] = false;
      }
    }
    $files = get_filelist($opts['category_id'], $site_id,
                          $opts['recursive'],
                          false);
    $template->append('footer_elements', '<!-- get_filelist : '
      . get_elapsed_time($start, get_moment())
      . ' -->');
    $start = get_moment();

    $datas = array();
    foreach ( $files as $id=>$file )
    {
      $file = $file['path'];
      $data = $site_reader->get_element_update_attributes($file);
      if ( !is_array($data) )
      {
        continue;
      }

      $data['id']=$id;
      array_push($datas, $data);
    } // end foreach file

    $counts['upd_elements'] = count($datas);
    if (!$simulate and count($datas)>0 )
    {
      mass_updates(
        IMAGES_TABLE,
        // fields
        array(
          'primary' => array('id'),
          'update'  => $site_reader->get_update_attributes(),
          ),
        $datas
        );
    }
    $template->append('footer_elements', '<!-- update files : '
      . get_elapsed_time($start,get_moment())
      . ' -->');
  }// end if sync files
}

// +-----------------------------------------------------------------------+
// |                          synchronize files                            |
// +-----------------------------------------------------------------------+
if (isset($_POST['submit'])
    and ($_POST['sync'] == 'dirs' or $_POST['sync'] == 'files'))
{
  $template->assign(
    'update_result',
    array(
      'NB_NEW_CATEGORIES'=>$counts['new_categories'],
      'NB_DEL_CATEGORIES'=>$counts['del_categories'],
      'NB_NEW_ELEMENTS'=>$counts['new_elements'],
      'NB_DEL_ELEMENTS'=>$counts['del_elements'],
      'NB_UPD_ELEMENTS'=>$counts['upd_elements'],
      'NB_ERRORS'=>count($errors),
      ));
}

// +-----------------------------------------------------------------------+
// |                          synchronize metadata                         |
// +-----------------------------------------------------------------------+
if (isset($_POST['submit']) and isset($_POST['sync_meta'])
         and !$general_failure)
{
  // sync only never synchronized files ?
  $opts['only_new'] = isset($_POST['meta_all']) ? false : true;
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
  $files = get_filelist($opts['category_id'], $site_id,
                        $opts['recursive'],
                        $opts['only_new']);

  $template->append('footer_elements', '<!-- get_filelist : '
    . get_elapsed_time($start, get_moment())
    . ' -->');

  $start = get_moment();
  $datas = array();
  $tags_of = array();

  foreach ( $files as $id => $element_infos )
  {
    $data = $site_reader->get_element_metadata($element_infos);

    if ( is_array($data) )
    {
      $data['date_metadata_update'] = CURRENT_DATE;
      $data['id']=$id;
      array_push($datas, $data);

      foreach (array('keywords', 'tags') as $key)
      {
        if (isset($data[$key]))
        {
          if (!isset($tags_of[$id]))
          {
            $tags_of[$id] = array();
          }

          foreach (explode(',', $data[$key]) as $tag_name)
          {
            array_push(
              $tags_of[$id],
              tag_id_from_tag_name($tag_name)
              );
          }
        }
      }
    }
    else
    {
      array_push($errors, array('path' => $element_infos['path'], 'type' => 'PWG-ERROR-NO-FS'));
    }
  }

  if (!$simulate)
  {
    if (count($datas) > 0)
    {
      mass_updates(
        IMAGES_TABLE,
        // fields
        array(
          'primary' => array('id'),
          'update'  => array_unique(
            array_merge(
              array_diff(
                $site_reader->get_metadata_attributes(),
                // keywords and tags fields are managed separately
                array('keywords', 'tags')
                ),
              array('date_metadata_update'))
            )
          ),
        $datas,
        isset($_POST['meta_empty_overrides']) ? 0 : MASS_UPDATES_SKIP_EMPTY
        );
    }
    set_tags_of($tags_of);
  }

  $template->append('footer_elements', '<!-- metadata update : '
    . get_elapsed_time($start, get_moment())
    . ' -->');

  $template->assign(
    'metadata_result',
    array(
      'NB_ELEMENTS_DONE' => count($datas),
      'NB_ELEMENTS_CANDIDATES' => count($files),
      'NB_ERRORS' => count($errors),
      ));
}

// +-----------------------------------------------------------------------+
// |                        template initialization                        |
// +-----------------------------------------------------------------------+
$template->set_filenames(array('update'=>'site_update.tpl'));
$result_title = '';
if (isset($simulate) and $simulate)
{
  $result_title.= '['.l10n('Simulation').'] ';
}

// used_metadata string is displayed to inform admin which metadata will be
// used from files for synchronization
$used_metadata = implode( ', ', $site_reader->get_metadata_attributes());
if ($site_is_remote and !isset($_POST['submit']) )
{
  $used_metadata.= ' + ...';
}

$template->assign(
  array(
    'SITE_URL'=>$site_url,
    'U_SITE_MANAGER'=> get_root_url().'admin.php?page=site_manager',
    'L_RESULT_UPDATE'=>$result_title.l10n('Search for new images in the directories'),
    'L_RESULT_METADATA'=>$result_title.l10n('Metadata synchronization results'),
    'METADATA_LIST' => $used_metadata,
    'U_HELP' => get_root_url().'admin/popuphelp.php?page=synchronize',
    ));

// +-----------------------------------------------------------------------+
// |                        introduction : choices                         |
// +-----------------------------------------------------------------------+
if (isset($_POST['submit']))
{
  $tpl_introduction = array(
      'sync'  => $_POST['sync'],
      'sync_meta'  => isset($_POST['sync_meta']) ? true : false,
      'display_info' => isset($_POST['display_info']) and $_POST['display_info']==1,
      'add_to_caddie' => isset($_POST['add_to_caddie']) and $_POST['add_to_caddie']==1,
      'subcats_included' => isset($_POST['subcats-included']) and $_POST['subcats-included']==1,
      'privacy_level_selected' => (int)@$_POST['privacy_level'],
      'meta_all'  => isset($_POST['meta_all']) ? true : false,
      'meta_empty_overrides'  => isset($_POST['meta_empty_overrides']) ? true : false,
    );

  if (isset($_POST['cat']) and is_numeric($_POST['cat']))
  {
    $cat_selected = array($_POST['cat']);
  }
  else
  {
    $cat_selected = array();
  }
}
else
{
  $tpl_introduction = array(
      'sync'  => 'dirs',
      'sync_meta'  => true,
      'display_info' => false,
      'add_to_caddie' => false,
      'subcats_included' => true,
      'privacy_level_selected' => 0,
      'meta_all'  => false,
      'meta_empty_overrides'  => false,
    );

  $cat_selected = array();

  if (isset($_GET['cat_id']))
  {
    check_input_parameter('cat_id', $_GET, false, PATTERN_ID);

    $cat_selected = array($_GET['cat_id']);
    $tpl_introduction['sync'] = 'files';
  }
}

$tpl_introduction['privacy_level_options'] = get_privacy_level_options();

$template->assign('introduction', $tpl_introduction);

$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE site_id = '.$site_id;
display_select_cat_wrapper($query,
                           $cat_selected,
                           'category_options',
                           false);


if (count($errors) > 0)
{
  foreach ($errors as $error)
  {
    $template->append(
      'sync_errors',
      array(
        'ELEMENT' => $error['path'],
        'LABEL' => $error['type'].' ('.$error_labels[$error['type']][0].')'
        ));
  }

  foreach ($error_labels as $error_type=>$error_description)
  {
    $template->append(
      'sync_error_captions',
      array(
        'TYPE' => $error_type,
        'LABEL' => $error_description[1]
        ));
  }
}

if (count($infos) > 0
    and isset($_POST['display_info'])
    and $_POST['display_info'] == 1)
{
  foreach ($infos as $info)
  {
    $template->append(
      'sync_infos',
      array(
        'ELEMENT' => $info['path'],
        'LABEL' => $info['info']
        ));
  }
}

// +-----------------------------------------------------------------------+
// |                          sending html code                            |
// +-----------------------------------------------------------------------+
$template->assign_var_from_handle('ADMIN_CONTENT', 'update');
?>
