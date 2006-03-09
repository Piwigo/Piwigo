<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
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
  die('Hacking attempt!');
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

//---------------------------------------------------------------- verification
if ( !isset( $_GET['cat_id'] ) || !is_numeric( $_GET['cat_id'] ) )
{
  $_GET['cat_id'] = '-1';
}

$template->set_filenames( array('categories'=>'admin/cat_modify.tpl') );

//--------------------------------------------------------- form criteria check
if (isset($_POST['submit']))
{
  $data =
    array(
      'id' => $_GET['cat_id'],
      'name' => @$_POST['name'],
      'commentable' => $_POST['commentable'],
      'uploadable' =>
        isset($_POST['uploadable']) ? $_POST['uploadable'] : 'false',
      'comment' =>
        $conf['allow_html_descriptions'] ?
          @$_POST['comment'] : strip_tags(@$_POST['comment'])
      );

  mass_updates(
    CATEGORIES_TABLE,
    array(
      'primary' => array('id'),
      'update' => array_diff(array_keys($data), array('id'))
      ),
    array($data)
    );
  
  set_cat_visible(array($_GET['cat_id']), $_POST['visible']);
  set_cat_status(array($_GET['cat_id']), $_POST['status']);

  if (isset($_POST['parent']))
  {
    move_categories(
      array($_GET['cat_id']),
      $_POST['parent']
      );
  }

  array_push($page['infos'], $lang['editcat_confirm']);
}
else if (isset($_POST['set_random_representant']))
{
  set_random_representant(array($_GET['cat_id']));
}
else if (isset($_POST['delete_representant']))
{
  $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET representative_picture_id = NULL
  WHERE id = '.$_GET['cat_id'].'
;';
  pwg_query($query);
}
else if (isset($_POST['submitAdd']))
{
  $output_create = create_virtual_category(
    $_POST['virtual_name'],
    (0 == $_POST['parent'] ? null : $_POST['parent'])
    );
  
  if (isset($output_create['error']))
  {
    array_push($page['errors'], $output_create['error']);
  }
  else
  {
    // Virtual category creation succeeded
    //
    // Add the information in the information list
    array_push($page['infos'], $output_create['info']);
    
    // Link the new category to the current category
    $query = '
INSERT
  INTO '.CATEGORIES_LINK_TABLE.'
  (source, destination)
  VALUES
  ('.$_GET['cat_id'].', '.$output_create['id'].')
;';
    pwg_query($query);

    check_links(array($output_create['id']));
    update_category(array($output_create['id']));
  }
}
else if (isset($_POST['destination_trueify'])
         and isset($_POST['destination_false'])
         and count($_POST['destination_false']))
{
  $datas = array();
  
  foreach ($_POST['destination_false'] as $category_id)
  {
    array_push(
      $datas,
      array(
        'source'      => $_GET['cat_id'],
        'destination' => $category_id,
        )
      );
  }
  
  mass_inserts(
    CATEGORIES_LINK_TABLE,
    array('source', 'destination'),
    $datas
    );

  check_links($_POST['destination_false']);
  update_category(
    $_POST['destination_false'],
    true                          // recursive update
    );
}
else if (isset($_POST['destination_falsify'])
         and isset($_POST['destination_true'])
         and count($_POST['destination_true']))
{
  foreach ($_POST['destination_true'] as $destination)
  {
    delete_sources($destination, array($_GET['cat_id']));
  }

  update_category(
    $_POST['destination_true'],
    true                          // recursive update
    );
}
else if (isset($_POST['source_trueify'])
         and isset($_POST['source_false'])
         and count($_POST['source_false']))
{
  $datas = array();
  
  foreach ($_POST['source_false'] as $category_id)
  {
    array_push(
      $datas,
      array(
        'source'      => $category_id,
        'destination' => $_GET['cat_id'],
        )
      );
  }
  
  mass_inserts(
    CATEGORIES_LINK_TABLE,
    array('source', 'destination'),
    $datas
    );

  check_links(array($_GET['cat_id']));
  update_category(
    array($_GET['cat_id']),
    true                          // recursive update
    );
}
else if (isset($_POST['source_falsify'])
         and isset($_POST['source_true'])
         and count($_POST['source_true']))
{
  delete_sources($_GET['cat_id'], $_POST['source_true']);
  
  update_category(
    array($_GET['cat_id']),
    true                          // recursive update
    );
}


$query = '
SELECT *
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$_GET['cat_id'].'
;';
$category = mysql_fetch_array( pwg_query( $query ) );
// nullable fields
foreach (array('comment','dir','site_id', 'id_uppercat') as $nullable)
{
  if (!isset($category[$nullable]))
  {
    $category[$nullable] = '';
  }
}

$category['is_virtual'] = empty($category['dir']) ? true : false;

// Navigation path
$url = PHPWG_ROOT_PATH.'admin.php?page=cat_modify&amp;cat_id=';

$navigation = get_cat_display_name_cache(
  $category['uppercats'],
  PHPWG_ROOT_PATH.'admin.php?page=cat_modify&amp;cat_id='
  );

$form_action = PHPWG_ROOT_PATH.'admin.php?page=cat_modify&amp;cat_id='.$_GET['cat_id'];
$status = ($category['status']=='public')?'STATUS_PUBLIC':'STATUS_PRIVATE'; 
$lock = ($category['visible']=='true')?'UNLOCKED':'LOCKED';

if ($category['commentable'] == 'true')
{
  $commentable = 'COMMENTABLE_TRUE';
}
else
{
  $commentable = 'COMMENTABLE_FALSE';
}
if ($category['uploadable'] == 'true')
{
  $uploadable = 'UPLOADABLE_TRUE';
}
else
{
  $uploadable = 'UPLOADABLE_FALSE';
}

//----------------------------------------------------- template initialization

$base_url = PHPWG_ROOT_PATH.'admin.php?page=';
$cat_list_url = $base_url.'cat_list';
  
$self_url = $cat_list_url;
if (!empty($category['id_uppercat']))
{
  $self_url.= '&amp;parent_id='.$category['id_uppercat'];
}

$template->assign_vars(array( 
  'CATEGORIES_NAV'=>$navigation,
  'CAT_NAME'=>$category['name'],
  'CAT_COMMENT'=>$category['comment'],
  
  $status=>'checked="checked"',
  $lock=>'checked="checked"',
  $commentable=>'checked="checked"',
  $uploadable=>'checked="checked"',
  
  'L_EDIT_NAME'=>$lang['name'],
  'L_STORAGE'=>$lang['storage'],
  'L_REMOTE_SITE'=>$lang['remote_site'],
  'L_EDIT_COMMENT'=>$lang['description'],
  'L_EDIT_STATUS'=>$lang['conf_access'],
  'L_STATUS_PUBLIC'=>$lang['public'],
  'L_STATUS_PRIVATE'=>$lang['private'],
  'L_EDIT_LOCK'=>$lang['lock'],
  'L_EDIT_UPLOADABLE'=>$lang['editcat_uploadable'],
  'L_EDIT_COMMENTABLE'=>$lang['comments'],
  'L_YES'=>$lang['yes'],
  'L_NO'=>$lang['no'],
  'L_SUBMIT'=>$lang['submit'],
  'L_SET_RANDOM_REPRESENTANT'=>$lang['cat_representant'],

  'U_JUMPTO'=>PHPWG_ROOT_PATH.'category.php?cat='.$category['id'],
  'U_CHILDREN'=>$cat_list_url.'&amp;parent_id='.$category['id'],
  'U_HELP' => PHPWG_ROOT_PATH.'/popuphelp.php?page=cat_modify',
   
  'F_ACTION'=>$form_action
  ));


if ('private' == $category['status'])
{
  $template->assign_block_vars(
    'permissions',
    array(
      'URL'=>$base_url.'cat_perm&amp;cat='.$category['id']
        )
    );
}

// manage category elements link
if ($category['nb_images'] > 0)
{
  $template->assign_block_vars(
    'elements',
    array(
      'URL'=>$base_url.'element_set&amp;cat='.$category['id']
      )
    );
}

// representant management
if ($category['nb_images'] > 0
    or !empty($category['representative_picture_id']))
{
  $template->assign_block_vars('representant', array());

  // picture to display : the identified representant or the generic random
  // representant ?
  if (!empty($category['representative_picture_id']))
  {
    $query = '
SELECT tn_ext,path
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$category['representative_picture_id'].'
;';
    $row = mysql_fetch_array(pwg_query($query));
    $src = get_thumbnail_src($row['path'], @$row['tn_ext']);
    $url = PHPWG_ROOT_PATH.'admin.php?page=picture_modify';
    $url.= '&amp;image_id='.$category['representative_picture_id'];
  
    $template->assign_block_vars(
      'representant.picture',
      array(
        'SRC' => $src,
        'URL' => $url
        )
      );
  }
  else // $category['nb_images'] > 0
  {
    $template->assign_block_vars('representant.random', array());
  }

  // can the admin choose to set a new random representant ?
  if ($category['nb_images'] > 0)
  {
    $template->assign_block_vars('representant.set_random', array());
  }

  // can the admin delete the current representant ?
  if (
    ($category['nb_images'] > 0
     and $conf['allow_random_representative'])
    or
    ($category['nb_images'] == 0
     and !empty($category['representative_picture_id'])))
  {
    $template->assign_block_vars('representant.delete_representant', array());
  }
}

if (!$category['is_virtual']) //!empty($category['dir']))
{
  $template->assign_block_vars(
    'storage',
    array('CATEGORY_DIR'=>preg_replace('/\/$/',
                                       '',
                                       get_complete_dir($category['id']))));
}
else
{
  $template->assign_block_vars(
    'delete',
    array(
      'URL'=>$self_url.'&amp;delete='.$category['id']
      )
    );

  $template->assign_block_vars('move', array());

  // the category can be moved in any category but in itself, in any
  // sub-category
  $unmovables = get_subcat_ids(array($category['id']));
  
  $blockname = 'move.parent_option';

  $template->assign_block_vars(
    $blockname,
    array(
      'SELECTED'
        => empty($category['id_uppercat']) ? 'selected="selected"' : '',
      'VALUE'=> 0,
      'OPTION' => '------------'
      )
    );
  
  $query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE id NOT IN ('.implode(',', $unmovables).')
;';
  
  display_select_cat_wrapper(
    $query,
    empty($category['id_uppercat']) ? array() : array($category['id_uppercat']),
    $blockname
    );
}

$category['cat_dir'] = get_complete_dir($_GET['cat_id']);
if (is_numeric($category['site_id']) and url_is_remote($category['cat_dir']) )
{
  $query = '
SELECT galleries_url
  FROM '.SITES_TABLE.'
  WHERE id = '.$category['site_id'].'
;';
  list($galleries_url) = mysql_fetch_array(pwg_query($query));
  $template->assign_block_vars('server', array('SITE_URL' => $galleries_url));
}

if (!$category['is_virtual'] and !url_is_remote($category['cat_dir']) )
{
  $template->assign_block_vars('upload' ,array());
}

$blockname = 'category_option_parent';

$template->assign_block_vars(
  $blockname,
  array(
    'VALUE'=> 0,
    'OPTION' => '------------'
    )
  );

$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
;';

display_select_cat_wrapper(
  $query,
  array(),
  $blockname
  );

// destination categories
$query = '
SELECT DISTINCT id, name, uppercats, global_rank
  FROM '.CATEGORIES_TABLE.'
    INNER JOIN '.CATEGORIES_LINK_TABLE.' ON destination = id
  WHERE source = '.$_GET['cat_id'].'
;';
display_select_cat_wrapper($query, array(), 'destination_option_true');

// non destination categories
$destinations = array_merge(
  array($_GET['cat_id']),
  array_from_query($query, 'id')
  );

$query = '
SELECT DISTINCT id, name, uppercats, global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE id NOT IN ('.implode(',', $destinations).')
;';
display_select_cat_wrapper($query, array(), 'destination_option_false');

// source categories
$query = '
SELECT DISTINCT id, name, uppercats, global_rank
  FROM '.CATEGORIES_TABLE.'
    INNER JOIN '.CATEGORIES_LINK_TABLE.' ON source = id
  WHERE destination = '.$_GET['cat_id'].'
;';
display_select_cat_wrapper($query, array(), 'source_option_true');

// non source categories
$sources = array_merge(
  array($_GET['cat_id']),
  array_from_query($query, 'id')
  );

$query = '
SELECT DISTINCT id, name, uppercats, global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE id NOT IN ('.implode(',', $sources).')
;';
display_select_cat_wrapper($query, array(), 'source_option_false');

//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'categories');
?>
