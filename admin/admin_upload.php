<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2004 PhpWebGallery Team - http://phpwebgallery.net |
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

if( !defined("PHPWG_ROOT_PATH") )
{
	die ("Hacking attempt!");
}

include_once( PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php' );
$uploadable = '';
$categories = '';

if (isset($_POST['submit']) || isset($_POST['delete']))
{
  $query = 'UPDATE '.CATEGORIES_TABLE;
  $query.= ' SET uploadable = ';
  if (isset($_POST['submit'])) 
    $query.="'true'";
  else 
    $query.="'false'";
  $query.= ' WHERE id IN (';
  $nb=count($cat_data);
  foreach($cat_data as $i=>$id)
 {
   $query.= $id;
   if ($i+1<$nb) $query.=',';
 } 
 $query.=');';
 pwg_query ($query);
}

// Cache management
$query = 'SELECT id, name, uploadable FROM '.CATEGORIES_TABLE;
$query.= ' WHERE dir IS NOT NULL';
$query.= ' ORDER BY name ASC';
$query.= ';';
$result = pwg_query( $query );
while ( $row = mysql_fetch_assoc( $result ) )
{
  if ($row['uploadable'] == 'false')
  {
    $categories.='<option value="'.$row['id'].'">'.$row['name'].'</option>';
  }
  else 
  {
    $uploadable.='<option value="'.$row['id'].'">'.$row['name'].'</option>';
  }
}

//----------------------------------------------------- template initialization
$template->set_filenames( array('upload'=>'admin/admin_upload.tpl') );

$template->assign_vars(array(
  'PRIVATE_CATEGORIES'=>$categories,
  'UPLOADABLE_CATEGORIES'=>$uploadable,
  
  'L_UPLOAD_TITLE'=>$lang['cat_upload'],
  'L_SUBMIT'=>$lang['submit'],
  'L_DELETE'=>$lang['delete'],
  'L_RESET'=>$lang['reset'],
  'L_UPLOAD_INFO'=>$lang['cat_upload_info'],
  'L_AUTHORIZED'=>$lang['authorized'],
  'L_FORBIDDEN'=>$lang['forbidden']
  ));

//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'upload');

?>