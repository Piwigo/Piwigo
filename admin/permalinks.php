<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
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

function parse_sort_variables(
    $sortable_by, $default_field,
    $get_param, $get_rejects,
    $template_var )
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
    if ( $field !== @$_GET[$get_param] )
    {
      if ( !isset($default_field) or $default_field!=$field )
      { // the first should be the default
        $url = add_url_params($url, array($get_param=>$field) );
      }
      $disp = '&dArr;'; // TODO: an small image is better
    }
    else
    {
      array_push($ret, $field);
      $disp = '<em>&dArr;</em>'; // TODO: an small image is better
    }
    if ( isset($template_var) )
    {
      $template->assign_var( $template_var.strtoupper($field),
            '<a href="'.$url.'" title="'.l10n('Sort order').'">'.$disp.'</a>'
         );
    }
  }
  return $ret;
}

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

include_once(PHPWG_ROOT_PATH.'admin/include/functions_permalinks.php');

$selected_cat = array();
if ( isset($_POST['set_permalink']) and $_POST['cat_id']>0 )
{
  $permalink = $_POST['permalink'];
  if ( empty($permalink) )
    delete_cat_permalink($_POST['cat_id'], isset($_POST['save']) );
  else
    set_cat_permalink($_POST['cat_id'], $permalink, isset($_POST['save']) );
  $selected_cat = array( $_POST['cat_id'] );
}
elseif ( isset($_GET['delete_permanent']) )
{
  $query = '
DELETE FROM '.OLD_PERMALINKS_TABLE.'
  WHERE permalink="'.$_GET['delete_permanent'].'"
  LIMIT 1';
  pwg_query($query);
  if (mysql_affected_rows()==0)
    array_push($page['errors'], 'Cannot delete the old permalink !');
}


$template->set_filename('permalinks', 'admin/permalinks.tpl' );

$query = '
SELECT 
  id, 
  CONCAT(id, " - ", name, IF(permalink IS NULL, "", " &radic;") ) AS name,
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
if ( count($sort_by) and
      ($sort_by[0]=='id' or $sort_by[0]=='permalink')
    )
{
  $query .= ' ORDER BY '.$sort_by[0];
}
$categories=array();
$result=pwg_query($query);
while ( $row=mysql_fetch_assoc($result) )
{
  $row['name'] = get_cat_display_name_cache( $row['uppercats'] );
  $categories[] = $row;
}

if ( !count($sort_by) or $sort_by[0]='name')
{
  usort($categories, 'global_rank_compare');
}
foreach ($categories as $cat)
{
  $template->assign_block_vars( 'permalink', $cat );
}


// --- generate display of old permalinks --------------------------------------

$sort_by = parse_sort_variables(
    array('cat_id','permalink','date_deleted','last_hit','hit'), null,
    'dpsf',
    array('delete_permanent'),
    'SORT_OLD_' );

$url_del_base = get_root_url().'admin.php?page=permalinks';
$query = 'SELECT * FROM '.OLD_PERMALINKS_TABLE;
if ( count($sort_by) )
{
  $query .= ' ORDER BY '.$sort_by[0];
}
$result = pwg_query($query);
while ( $row=mysql_fetch_assoc($result) )
{
  $row['name'] = get_cat_display_name_cache($row['cat_id']);
  $row['U_DELETE'] =
      add_url_params(
        $url_del_base,
        array( 'delete_permanent'=> $row['permalink'] )
      );
  $template->assign_block_vars( 'deleted_permalink', $row );
}

$template->assign_var('U_HELP', get_root_url().'popuphelp.php?page=permalinks');

$template->assign_var_from_handle('ADMIN_CONTENT', 'permalinks');
?>
