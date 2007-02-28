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

$query = '
SELECT id, name, permalink 
  FROM '.CATEGORIES_TABLE.'
  WHERE permalink IS NOT NULL';
$result=pwg_query($query);
while ( $row=mysql_fetch_assoc($result) )
{
  $display_name = get_cat_display_name( array($row) );
  $template->assign_block_vars( 'permalink',
    array(
      'CAT_ID' => $row['id'],
      'CAT' => $display_name,
      'PERMALINK' => $row['permalink'],
    )
    );
}

$url_del_base = get_root_url().'admin.php?page=permalinks';

$query = 'SELECT * FROM '.OLD_PERMALINKS_TABLE;
$result = pwg_query($query);
while ( $row=mysql_fetch_assoc($result) )
{
  $row['display_name'] = get_cat_display_name_cache($row['cat_id']);
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
