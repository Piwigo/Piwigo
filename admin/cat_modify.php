<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
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

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

//---------------------------------------------------------------- verification
if ( !isset( $_GET['cat_id'] ) || !is_numeric( $_GET['cat_id'] ) )
{
  $_GET['cat_id'] = '-1';
}

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

  $image_order = '';
  if ( !isset($_POST['image_order_default']) )
  {
    for ($i=1; $i<=3; $i++)
    {
      if ( !empty($_POST['order_field_'.$i]) )
      {
        if (! empty($image_order) )
        {
          $image_order .= ',';
        }
        $image_order .= $_POST['order_field_'.$i];
        if ($_POST['order_direction_'.$i]=='DESC')
        {
          $image_order .= ' DESC';
        }
      }
    }
  }
  $image_order = empty($image_order) ? 'null' : "'$image_order'";
  $query = '
UPDATE '.CATEGORIES_TABLE.' SET image_order='.$image_order.'
WHERE ';
  if (isset($_POST['image_order_subcats']))
  {
    $query .= 'uppercats REGEXP \'(^|,)'.$_GET['cat_id'].'(,|$)\'';
  }
  else
  {
    $query .= 'id='.$_GET['cat_id'].';';
  }
  pwg_query($query);

  array_push($page['infos'], l10n('editcat_confirm'));
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
    associate_categories_to_categories(
      array($_GET['cat_id']),
      array($output_create['id'])
      );

    // information
    array_push(
      $page['infos'],
      sprintf(
        l10n('Category elements associated to the following categories: %s'),
        '<ul><li>'
        .get_cat_display_name_from_id($output_create['id'])
        .'</li></ul>'
        )
      );
  }
}
else if (isset($_POST['submitDestinations'])
         and isset($_POST['destinations'])
         and count($_POST['destinations']) > 0)
{
  associate_categories_to_categories(
    array($_GET['cat_id']),
    $_POST['destinations']
    );

  $category_names = array();
  foreach ($_POST['destinations'] as $category_id)
  {
    array_push(
      $category_names,
      get_cat_display_name_from_id($category_id)
      );
  }

  array_push(
    $page['infos'],
    sprintf(
      l10n('Category elements associated to the following categories: %s'),
      '<ul><li>'.implode('</li><li>', $category_names).'</li></ul>'
      )
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

//----------------------------------------------------- template initialization
$template->set_filename( 'categories', 'admin/cat_modify.tpl');

$base_url = PHPWG_ROOT_PATH.'admin.php?page=';
$cat_list_url = $base_url.'cat_list';

$self_url = $cat_list_url;
if (!empty($category['id_uppercat']))
{
  $self_url.= '&amp;parent_id='.$category['id_uppercat'];
}

$template->assign(
  array(
    'CATEGORIES_NAV'     => $navigation,
    'CAT_NAME'           => @htmlspecialchars($category['name']),
    'CAT_COMMENT'        => @htmlspecialchars($category['comment']),

    'status_values'     => array('public','private'),
    'false_true'        => array('false', 'true'),
    'no_yes'            => array('no','yes'),

    'CAT_STATUS'        => $category['status'],
    'CAT_VISIBLE'       => $category['visible'],
    'CAT_COMMENTABLE'   => $category['commentable'],
    'CAT_UPLOADABLE'    => $category['uploadable'],

    'IMG_ORDER_DEFAULT'  => empty($category['image_order']) ?
                              'checked="checked"' : '',

    'U_JUMPTO' => make_index_url(
      array(
        'category' => $category
        )
      ),

    'MAIL_CONTENT' => empty($_POST['mail_content'])
        ? '' : stripslashes($_POST['mail_content']),
    'U_CHILDREN' => $cat_list_url.'&amp;parent_id='.$category['id'],
    'U_HELP' => PHPWG_ROOT_PATH.'popuphelp.php?page=cat_modify',

    'F_ACTION' => $form_action,
    )
  );


if ('private' == $category['status'])
{
  $template->assign( 'U_MANAGE_PERMISSIONS',
      $base_url.'cat_perm&amp;cat='.$category['id']
    );
}

// manage category elements link
if ($category['nb_images'] > 0)
{
  $template->assign( 'U_MANAGE_ELEMENTS',
        $base_url.'element_set&amp;cat='.$category['id']
    );
}

if ($category['is_virtual'])
{
  $template->assign(
    array(
      'U_DELETE' => $self_url.'&amp;delete='.$category['id'],
      )
    );
}
else
{
  $category['cat_full_dir'] = get_complete_dir($_GET['cat_id']);
  $template->assign(
    array(
      'CAT_FULL_DIR'       => preg_replace('/\/$/',
                                    '',
                                    $category['cat_full_dir'] )
      )
    );
  if (!url_is_remote($category['cat_full_dir']) )
  {
    $template->assign('SHOW_UPLOADABLE', true);
  }
}

// image order management

$sort_fields = array(
  '' => '',
  'date_creation' => l10n('Creation date'),
  'date_available' => l10n('Post date'),
  'average_rate' => l10n('Average rate'),
  'hit' => l10n('most_visited_cat'),
  'file' => l10n('File name'),
  'id' => 'Id',
  );

$sort_directions = array(
  'ASC' => l10n('ascending'),
  'DESC' => l10n('descending'),
  );

$template->assign( 'image_order_field_options', $sort_fields);
$template->assign( 'image_order_direction_options', $sort_directions);

$matches = array();
if ( !empty( $category['image_order'] ) )
{
  preg_match_all('/([a-z_]+) *(?:(asc|desc)(?:ending)?)? *(?:, *|$)/i',
    $category['image_order'], $matches);
}

for ($i=0; $i<3; $i++) // 3 fields
{
  $tpl_image_order_select = array(
      'ID' => $i+1,
      'FIELD' => array(''),
      'DIRECTION' => array('ASC'),
    );
  
  if ( isset($matches[1][$i]) )
  {
    $tpl_image_order_select['FIELD'] = array($matches[1][$i]);
  }
  
  if (isset($matches[2][$i]) and strcasecmp($matches[2][$i],'DESC')==0)
  {
    $tpl_image_order_select['DIRECTION'] = array('DESC');
  }
  $template->append( 'image_orders', $tpl_image_order_select);
}


// representant management
if ($category['nb_images'] > 0
    or !empty($category['representative_picture_id']))
{
  $tpl_representant = array();

  // picture to display : the identified representant or the generic random
  // representant ?
  if (!empty($category['representative_picture_id']))
  {
    $query = '
SELECT id,tn_ext,path
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$category['representative_picture_id'].'
;';
    $row = mysql_fetch_array(pwg_query($query));
    $src = get_thumbnail_url($row);
    $url = PHPWG_ROOT_PATH.'admin.php?page=picture_modify';
    $url.= '&amp;image_id='.$category['representative_picture_id'];

    $tpl_representant['picture'] =
      array(
        'SRC' => $src,
        'URL' => $url
      );
  }

  // can the admin choose to set a new random representant ?
  $tpl_representant['ALLOW_SET_RANDOM'] = ($category['nb_images']>0) ? true : false;

  // can the admin delete the current representant ?
  if (
    ($category['nb_images'] > 0
     and $conf['allow_random_representative'])
    or
    ($category['nb_images'] == 0
     and !empty($category['representative_picture_id'])))
  {
    $tpl_representant['ALLOW_DELETE'] = true;
  }
  $template->assign('representant', $tpl_representant);
}

if ($category['is_virtual'])
{
  // the category can be moved in any category but in itself, in any
  // sub-category
  $unmovables = get_subcat_ids(array($category['id']));

  $query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE id NOT IN ('.implode(',', $unmovables).')
;';

  display_select_cat_wrapper(
    $query,
    empty($category['id_uppercat']) ? array() : array($category['id_uppercat']),
    'move_cat_options'
    );
}


// create virtual in parent and link
$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
;';
display_select_cat_wrapper(
  $query,
  array(),
  'create_new_parent_options'
  );


// destination categories
$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE id != '.$category['id'].'
;';
display_select_cat_wrapper(
  $query,
  array(),
  'category_destination_options'
  );

// info by email to an access granted group of category informations
if (isset($_POST['submitEmail']) and !empty($_POST['group']))
{
  set_make_full_url();

  /* TODO: if $category['representative_picture_id'] 
    is empty find child representative_picture_id */
  if (!empty($category['representative_picture_id']))
  {
    $query = '
SELECT id, file, path, tn_ext
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$category['representative_picture_id'].'
;';

    $result = pwg_query($query);
    if (mysql_num_rows($result) > 0)
    {
      $element = mysql_fetch_assoc($result);

      $img_url  = '<a href="'.
                      make_picture_url(array(
                          'image_id' => $element['id'],
                          'image_file' => $element['file'],
                          'category' => $category
                        ))
                      .'"><img src="'.get_thumbnail_url($element).'"/></a>';
    }
  }
  
  if (!isset($img_url))
  {
    $img_url = '';
  }

  // TODO Mettre un array pour traduction subjet
  pwg_mail_group(
    $_POST['group'],
    get_str_email_format(true), /* TODO add a checkbox in order to choose format*/
    get_l10n_args('[%s] Come to visit the category %s',
      array($conf['gallery_title'], $category['name'])),
    'admin',
    'cat_group_info',
    array
    (
      'IMG_URL' => $img_url,
      'CAT_NAME' => $category['name'],
      'LINK' => make_index_url(
          array(
            'category' => array(
              'id' => $category['id'],
              'name' => $category['name'],
              'permalink' => $category['permalink']
              ))),
      'CPL_CONTENT' => empty($_POST['mail_content'])
                          ? '' : stripslashes($_POST['mail_content'])
    ),
    '' /* TODO Add listbox in order to choose Language selected */);

  unset_make_full_url();

  $query = '
SELECT
    name
  FROM '.GROUPS_TABLE.'
  WHERE id = '.$_POST['group'].'
;';
  list($group_name) = mysql_fetch_row(pwg_query($query));
  
  array_push(
    $page['infos'],
    sprintf(
      l10n('An information email was sent to group "%s"'),
      $group_name
      )
    );
}

if ('private' == $category['status'])
{
  $query = '
SELECT
    group_id
  FROM '.GROUP_ACCESS_TABLE.'
  WHERE cat_id = '.$category['id'].'
;';
}
else
{
  $query = '
SELECT
    id AS group_id
  FROM '.GROUPS_TABLE.'
;';
}
$group_ids = array_from_query($query, 'group_id');

if (count($group_ids) > 0)
{
  $query = '
SELECT
    id,
    name
  FROM '.GROUPS_TABLE.'
  WHERE id IN ('.implode(',', $group_ids).')
  ORDER BY name ASC
;';
  $template->assign('group_mail_options',
      simple_hash_from_query($query, 'id', 'name')
    );
}

//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'categories');
?>
