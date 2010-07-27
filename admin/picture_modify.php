<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
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

if(!defined("PHPWG_ROOT_PATH"))
{
  die('Hacking attempt!');
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

check_input_parameter('image_id', $_GET, false, PATTERN_ID);
check_input_parameter('cat_id', $_GET, false, PATTERN_ID);

// +-----------------------------------------------------------------------+
// |                          synchronize metadata                         |
// +-----------------------------------------------------------------------+

if (isset($_GET['sync_metadata']) and !is_adviser())
{
  $query = '
SELECT path
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$_GET['image_id'].'
;';
  list($path) = pwg_db_fetch_row(pwg_query($query));
  update_metadata(array($_GET['image_id'] => $path));

  array_push($page['infos'], l10n('Metadata synchronized from file'));
}

//--------------------------------------------------------- update informations

// first, we verify whether there is a mistake on the given creation date
if (isset($_POST['date_creation_action'])
    and 'set' == $_POST['date_creation_action'])
{
  if (!is_numeric($_POST['date_creation_year'])
    or !checkdate(
          $_POST['date_creation_month'],
          $_POST['date_creation_day'],
          $_POST['date_creation_year'])
    )
  {
    array_push($page['errors'], l10n('wrong date'));
  }
}

if (isset($_POST['submit']) and count($page['errors']) == 0 and !is_adviser())
{
  $data = array();
  $data{'id'} = $_GET['image_id'];
  $data{'name'} = $_POST['name'];
  $data{'author'} = $_POST['author'];
  $data['level'] = $_POST['level'];

  if ($conf['allow_html_descriptions'])
  {
    $data{'comment'} = @$_POST['description'];
  }
  else
  {
    $data{'comment'} = strip_tags(@$_POST['description']);
  }

  if (isset($_POST['date_creation_action']))
  {
    if ('set' == $_POST['date_creation_action'])
    {
      $data{'date_creation'} = $_POST['date_creation_year']
                                 .'-'.$_POST['date_creation_month']
                                 .'-'.$_POST['date_creation_day'];
    }
    else if ('unset' == $_POST['date_creation_action'])
    {
      $data{'date_creation'} = '';
    }
  }

  mass_updates(
    IMAGES_TABLE,
    array(
      'primary' => array('id'),
      'update' => array_diff(array_keys($data), array('id'))
      ),
    array($data)
    );

  // time to deal with tags
  $tag_ids = array();
  if (isset($_POST['tags']))
  {
    $tag_ids = get_fckb_tag_ids($_POST['tags']);
  }
  set_tags($tag_ids, $_GET['image_id']);

  array_push($page['infos'], l10n('Picture informations updated'));
}
// associate the element to other categories than its storage category
if (isset($_POST['associate'])
    and isset($_POST['cat_dissociated'])
    and count($_POST['cat_dissociated']) > 0
    and !is_adviser()
  )
{
  associate_images_to_categories(
    array($_GET['image_id']),
    $_POST['cat_dissociated']
    );
}
// dissociate the element from categories (but not from its storage category)
if (isset($_POST['dissociate'])
    and isset($_POST['cat_associated'])
    and count($_POST['cat_associated']) > 0
    and !is_adviser()
  )
{
  $query = '
DELETE FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE image_id = '.$_GET['image_id'].'
    AND category_id IN ('.implode(',', $_POST['cat_associated']).')
';
  pwg_query($query);

  update_category($_POST['cat_associated']);
}
// elect the element to represent the given categories
if (isset($_POST['elect'])
    and isset($_POST['cat_dismissed'])
    and count($_POST['cat_dismissed']) > 0
    and !is_adviser()
  )
{
  $datas = array();
  foreach ($_POST['cat_dismissed'] as $category_id)
  {
    array_push($datas,
               array('id' => $category_id,
                     'representative_picture_id' => $_GET['image_id']));
  }
  $fields = array('primary' => array('id'),
                  'update' => array('representative_picture_id'));
  mass_updates(CATEGORIES_TABLE, $fields, $datas);
}
// dismiss the element as representant of the given categories
if (isset($_POST['dismiss'])
    and isset($_POST['cat_elected'])
    and count($_POST['cat_elected']) > 0
    and !is_adviser()
  )
{
  set_random_representant($_POST['cat_elected']);
}

// tags
$query = '
SELECT
    tag_id,
    name AS tag_name
  FROM '.IMAGE_TAG_TABLE.' AS it
    JOIN '.TAGS_TABLE.' AS t ON t.id = it.tag_id
  WHERE image_id = '.$_GET['image_id'].'
;';
$tags = get_fckb_taglist($query);

// retrieving direct information about picture
$query = '
SELECT *
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$_GET['image_id'].'
;';
$row = pwg_db_fetch_assoc(pwg_query($query));

$storage_category_id = null;
if (!empty($row['storage_category_id']))
{
  $storage_category_id = $row['storage_category_id'];
}

$image_file = $row['file'];

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(
  array(
    'picture_modify' => 'picture_modify.tpl'
    )
  );

$template->assign(
  array(
    'tags' => $tags,
    'U_SYNC' =>
        get_root_url().'admin.php?page=picture_modify'.
        '&amp;image_id='.$_GET['image_id'].
        (isset($_GET['cat_id']) ? '&amp;cat_id='.$_GET['cat_id'] : '').
        '&amp;sync_metadata=1',

    'PATH'=>$row['path'],

    'TN_SRC' => get_thumbnail_url($row),

    'NAME' =>
      isset($_POST['name']) ?
        stripslashes($_POST['name']) : @$row['name'],

    'DIMENSIONS' => @$row['width'].' * '.@$row['height'],

    'FILESIZE' => @$row['filesize'].' KB',

    'REGISTRATION_DATE' => format_date($row['date_available']),

    'AUTHOR' => htmlspecialchars(
      isset($_POST['author'])
        ? stripslashes($_POST['author'])
        : @$row['author']
      ),

    'DESCRIPTION' =>
      htmlspecialchars( isset($_POST['description']) ?
        stripslashes($_POST['description']) : @$row['comment'] ),

    'F_ACTION' =>
        get_root_url().'admin.php'
        .get_query_string_diff(array('sync_metadata'))
    )
  );

if ($row['has_high'] == 'true')
{
  $template->assign(
    'HIGH_FILESIZE',
    isset($row['high_filesize'])
        ? $row['high_filesize'].' KB'
        : l10n('unknown')
    );
}

// image level options
$selected_level = isset($_POST['level']) ? $_POST['level'] : $row['level'];
$template->assign(
    array(
      'level_options'=> get_privacy_level_options(),
      'level_options_selected' => array($selected_level)
    )
  );

// creation date
unset($day, $month, $year);

if (isset($_POST['date_creation_action'])
    and 'set' == $_POST['date_creation_action'])
{
  foreach (array('day', 'month', 'year') as $varname)
  {
    $$varname = $_POST['date_creation_'.$varname];
  }
}
else if (isset($row['date_creation']) and !empty($row['date_creation']))
{
  list($year, $month, $day) = explode('-', $row['date_creation']);
}
else
{
  list($year, $month, $day) = array('', 0, 0);
}


$month_list = $lang['month'];
$month_list[0]='------------';
ksort($month_list);

$template->assign(
    array(
      'DATE_CREATION_DAY_VALUE' => $day,
      'DATE_CREATION_MONTH_VALUE' => $month,
      'DATE_CREATION_YEAR_VALUE' => $year,
      'month_list' => $month_list,
      )
    );

$query = '
SELECT category_id, uppercats
  FROM '.IMAGE_CATEGORY_TABLE.' AS ic
    INNER JOIN '.CATEGORIES_TABLE.' AS c
      ON c.id = ic.category_id
  WHERE image_id = '.$_GET['image_id'].'
;';
$result = pwg_query($query);

while ($row = pwg_db_fetch_assoc($result))
{
  $name =
    get_cat_display_name_cache(
      $row['uppercats'],
      get_root_url().'admin.php?page=cat_modify&amp;cat_id=',
      false
      );

  if ($row['category_id'] == $storage_category_id)
  {
    $template->assign('STORAGE_CATEGORY', $name);
  }
  else
  {
    $template->append('related_categories', $name);
  }
}

// jump to link
//
// 1. find all linked categories that are reachable for the current user.
// 2. if a category is available in the URL, use it if reachable
// 3. if URL category not available or reachable, use the first reachable
//    linked category
// 4. if no category reachable, no jumpto link

$query = '
SELECT category_id
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE image_id = '.$_GET['image_id'].'
;';

$authorizeds = array_diff(
  array_from_query($query, 'category_id'),
  explode(
    ',',
    calculate_permissions($user['id'], $user['status'])
    )
  );

if (isset($_GET['cat_id'])
    and in_array($_GET['cat_id'], $authorizeds))
{
  $url_img = make_picture_url(
    array(
      'image_id' => $_GET['image_id'],
      'image_file' => $image_file,
      'category' => $cache['cat_names'][ $_GET['cat_id'] ],
      )
    );
}
else
{
  foreach ($authorizeds as $category)
  {
    $url_img = make_picture_url(
      array(
        'image_id' => $_GET['image_id'],
        'image_file' => $image_file,
        'category' => $cache['cat_names'][ $category ],
        )
      );
    break;
  }
}

if (isset($url_img))
{
  $template->assign( 'U_JUMPTO', $url_img );
}

// associate to another category ?
$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON id = category_id
  WHERE image_id = '.$_GET['image_id'];
if (isset($storage_category_id))
{
  $query.= '
    AND id != '.$storage_category_id;
}
$query.= '
;';
display_select_cat_wrapper($query, array(), 'associated_options');

$result = pwg_query($query);
$associateds = array(-1);
if (isset($storage_category_id))
{
  array_push($associateds, $storage_category_id);
}
while ($row = pwg_db_fetch_assoc($result))
{
  array_push($associateds, $row['id']);
}
$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE id NOT IN ('.implode(',', $associateds).')
;';
display_select_cat_wrapper($query, array(), 'dissociated_options');

// representing
$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE representative_picture_id = '.$_GET['image_id'].'
;';
display_select_cat_wrapper($query, array(), 'elected_options');

$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE representative_picture_id != '.$_GET['image_id'].'
    OR representative_picture_id IS NULL
;';
display_select_cat_wrapper($query, array(), 'dismissed_options');

//----------------------------------------------------------- sending html code

$template->assign_var_from_handle('ADMIN_CONTENT', 'picture_modify');
?>
