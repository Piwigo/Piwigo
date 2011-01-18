<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
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
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// |                       modification registration                       |
// +-----------------------------------------------------------------------+

// print '<pre>';
// print_r($_POST);
// print '</pre>';
if (isset($_POST['falsify'])
    and isset($_POST['cat_true'])
    and count($_POST['cat_true']) > 0)
{
  switch ($_GET['section'])
  {
    case 'comments' :
    {
      $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET commentable = \'false\'
  WHERE id IN ('.implode(',', $_POST['cat_true']).')
;';
      pwg_query($query);
      break;
    }
    case 'visible' :
    {
      set_cat_visible($_POST['cat_true'], 'false');
      break;
    }
    case 'status' :
    {
      set_cat_status($_POST['cat_true'], 'private');
      break;
    }
    case 'representative' :
    {
      $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET representative_picture_id = NULL
  WHERE id IN ('.implode(',', $_POST['cat_true']).')
;';
      pwg_query($query);
      break;
    }
  }
}
else if (isset($_POST['trueify'])
         and isset($_POST['cat_false'])
         and count($_POST['cat_false']) > 0)
{
  switch ($_GET['section'])
  {
    case 'comments' :
    {
      $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET commentable = \'true\'
  WHERE id IN ('.implode(',', $_POST['cat_false']).')
;';
      pwg_query($query);
      break;
    }
    case 'visible' :
    {
      set_cat_visible($_POST['cat_false'], 'true');
      break;
    }
    case 'status' :
    {
      set_cat_status($_POST['cat_false'], 'public');
      break;
    }
    case 'representative' :
    {
      // theoretically, all categories in $_POST['cat_false'] contain at
      // least one element, so Piwigo can find a representant.
      set_random_representant($_POST['cat_false']);
      break;
    }
  }
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(
  array(
    'cat_options' => 'cat_options.tpl',
    'double_select' => 'double_select.tpl'
    )
  );

$page['section'] = isset($_GET['section']) ? $_GET['section'] : 'status';
$base_url = PHPWG_ROOT_PATH.'admin.php?page=cat_options&amp;section=';

$template->assign(
  array(
    'U_HELP' => get_root_url().'admin/popuphelp.php?page=cat_options',
    'F_ACTION'=>$base_url.$page['section']
   )
 );

// TabSheet
$tabsheet = new tabsheet();
// TabSheet initialization
$opt_link = $link_start.'cat_options&amp;section=';
$tabsheet->add('status', l10n('Public / Private'), $opt_link.'status');
$tabsheet->add('visible', l10n('Lock'), $opt_link.'visible');
$tabsheet->add('comments', l10n('Comments'), $opt_link.'comments');
if ($conf['allow_random_representative'])
{
  $tabsheet->add('representative', l10n('Representative'), $opt_link.'representative');
}
// TabSheet selection
$tabsheet->select($page['section']);
// Assign tabsheet to template
$tabsheet->assign();

// +-----------------------------------------------------------------------+
// |                              form display                             |
// +-----------------------------------------------------------------------+

// for each section, categories in the multiselect field can be :
//
// - true : commentable for comment section
// - false : un-commentable for comment section
// - NA : (not applicable) for virtual categories
//
// for true and false status, we associates an array of category ids,
// function display_select_categories will use the given CSS class for each
// option
$cats_true = array();
$cats_false = array();
switch ($page['section'])
{
  case 'comments' :
  {
    $query_true = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE commentable = \'true\'
;';
    $query_false = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE commentable = \'false\'
;';
    $template->assign(
      array(
        'L_SECTION' => l10n('Authorize users to add comments on selected albums'),
        'L_CAT_OPTIONS_TRUE' => l10n('Authorized'),
        'L_CAT_OPTIONS_FALSE' => l10n('Forbidden'),
        )
      );
    break;
  }
  case 'visible' :
  {
    $query_true = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE visible = \'true\'
;';
    $query_false = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE visible = \'false\'
;';
    $template->assign(
      array(
        'L_SECTION' => l10n('Lock albums'),
        'L_CAT_OPTIONS_TRUE' => l10n('Unlocked'),
        'L_CAT_OPTIONS_FALSE' => l10n('Locked'),
        )
      );
    break;
  }
  case 'status' :
  {
    $query_true = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE status = \'public\'
;';
    $query_false = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE status = \'private\'
;';
    $template->assign(
      array(
        'L_SECTION' => l10n('Manage authorizations for selected albums'),
        'L_CAT_OPTIONS_TRUE' => l10n('Public'),
        'L_CAT_OPTIONS_FALSE' => l10n('Private'),
        )
      );
    break;
  }
  case 'representative' :
  {
    $query_true = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE representative_picture_id IS NOT NULL
;';
    $query_false = '
SELECT DISTINCT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.' INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON id=category_id
  WHERE representative_picture_id IS NULL
;';
    $template->assign(
      array(
        'L_SECTION' => l10n('Representative'),
        'L_CAT_OPTIONS_TRUE' => l10n('singly represented'),
        'L_CAT_OPTIONS_FALSE' => l10n('randomly represented')
        )
      );
    break;
  }
}
display_select_cat_wrapper($query_true,array(),'category_option_true');
display_select_cat_wrapper($query_false,array(),'category_option_false');

// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('DOUBLE_SELECT', 'double_select');
$template->assign_var_from_handle('ADMIN_CONTENT', 'cat_options');
?>