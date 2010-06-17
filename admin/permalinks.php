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

function parse_sort_variables(
    $sortable_by, $default_field,
    $get_param, $get_rejects,
    $template_var,
    $anchor = '' )
{
  global $template;

  $url_components = parse_url( $_SERVER['REQUEST_URI'] );

  $base_url = $url_components['path'];

  parse_str($url_components['query'], $vars);
  $is_first = true;
  foreach ($vars as $key => $value)
  {
    if (!in_array($key, $get_rejects) and $key!=$get_param)
    {
      $base_url .= $is_first ? '?' : '&amp;';
      $is_first = false;
      $base_url .= $key.'='.urlencode($value);
    }
  }

  $ret = array();
  foreach( $sortable_by as $field)
  {
    $url = $base_url;
    $disp = '&dArr;'; // TODO: an small image is better

    if ( $field !== @$_GET[$get_param] )
    {
      if ( !isset($default_field) or $default_field!=$field )
      { // the first should be the default
        $url = add_url_params($url, array($get_param=>$field) );
      }
      elseif (isset($default_field) and !isset($_GET[$get_param]) )
      {
        array_push($ret, $field);
        $disp = '<em>'.$disp.'</em>';
      }
    }
    else
    {
      array_push($ret, $field);
      $disp = '<em>'.$disp.'</em>';
    }
    if ( isset($template_var) )
    {
      $template->assign( $template_var.strtoupper($field),
            '<a href="'.$url.$anchor.'" title="'.l10n('Sort order').'">'.$disp.'</a>'
         );
    }
  }
  return $ret;
}

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

include_once(PHPWG_ROOT_PATH.'admin/include/functions_permalinks.php');

$selected_cat = array();
if ( isset($_POST['set_permalink']) and $_POST['cat_id']>0 and !is_adviser() )
{
  $permalink = $_POST['permalink'];
  if ( empty($permalink) )
    delete_cat_permalink($_POST['cat_id'], isset($_POST['save']) );
  else
    set_cat_permalink($_POST['cat_id'], $permalink, isset($_POST['save']) );
  $selected_cat = array( $_POST['cat_id'] );
}
elseif ( isset($_GET['delete_permanent']) and !is_adviser() )
{
  $query = '
DELETE FROM '.OLD_PERMALINKS_TABLE.'
  WHERE permalink=\''.$_GET['delete_permanent'].'\'
  LIMIT 1';
  $result = pwg_query($query);
  if (pwg_db_changes($result)==0)
    array_push($page['errors'], l10n('Cannot delete the old permalink !'));
}


$template->set_filename('permalinks', 'permalinks.tpl' );

$query = '
SELECT
  id, permalink,
  '.pwg_db_concat(array('id', '\' - \'', 'name')).' AS name,
  uppercats, global_rank
FROM '.CATEGORIES_TABLE;

display_select_cat_wrapper( $query, $selected_cat, 'categories', false );


// --- generate display of active permalinks -----------------------------------
$sort_by = parse_sort_variables(
    array('id', 'name', 'permalink'), 'name',
    'psf',
    array('delete_permanent'),
    'SORT_' );

$query = '
SELECT id, permalink, uppercats, global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE permalink IS NOT NULL
';
if ( $sort_by[0]=='id' or $sort_by[0]=='permalink' )
{
  $query .= ' ORDER BY '.$sort_by[0];
}
$categories=array();
$result=pwg_query($query);
while ( $row = pwg_db_fetch_assoc($result) )
{
  $row['name'] = get_cat_display_name_cache( $row['uppercats'] );
  $categories[] = $row;
}

if ( $sort_by[0]=='name')
{
  usort($categories, 'global_rank_compare');
}
$template->assign( 'permalinks', $categories );

// --- generate display of old permalinks --------------------------------------

$sort_by = parse_sort_variables(
    array('cat_id','permalink','date_deleted','last_hit','hit'), null,
    'dpsf',
    array('delete_permanent'),
    'SORT_OLD_', '#old_permalinks' );

$url_del_base = get_root_url().'admin.php?page=permalinks';
$query = 'SELECT * FROM '.OLD_PERMALINKS_TABLE;
if ( count($sort_by) )
{
  $query .= ' ORDER BY '.$sort_by[0];
}
$result = pwg_query($query);
$deleted_permalinks=array();
while ( $row = pwg_db_fetch_assoc($result) )
{
  $row['name'] = get_cat_display_name_cache($row['cat_id']);
  $row['U_DELETE'] =
      add_url_params(
        $url_del_base,
        array( 'delete_permanent'=> $row['permalink'] )
      );
  $deleted_permalinks[] = $row;
}
$template->assign('deleted_permalinks', $deleted_permalinks);
$template->assign('U_HELP', get_root_url().'admin/popuphelp.php?page=permalinks');

$template->assign_var_from_handle('ADMIN_CONTENT', 'permalinks');
?>
