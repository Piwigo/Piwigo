<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2012 Piwigo Team                  http://piwigo.org |
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

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

check_input_parameter('image_id', $_GET, false, PATTERN_ID);

if (isset($_POST['submit']))
{
  $query = 'UPDATE '.IMAGES_TABLE;
  if (strlen($_POST['l'])==0)
  {
    $query .= ' SET coi=NULL';
  }
  else
  {
    $coi = fraction_to_char($_POST['l'])
      .fraction_to_char($_POST['t'])
      .fraction_to_char($_POST['r'])
      .fraction_to_char($_POST['b']);
    $query .= ' SET coi=\''.$coi.'\'';
  }
  $query .= ' WHERE id='.$_GET['image_id'];
  pwg_query($query);
}

$query = 'SELECT * FROM '.IMAGES_TABLE.' WHERE id='.$_GET['image_id'];
$row = pwg_db_fetch_assoc( pwg_query($query) );

if (isset($_POST['submit']))
{
  delete_element_derivatives($row);
}

$tpl_var = array(
  'ALT' => $row['file'],
  'U_IMG' => DerivativeImage::url(IMG_LARGE, $row),
  'U_EDIT' => get_root_url().'admin.php?page=picture_modify&amp;image_id='.$_GET['image_id'],
  );

if (!empty($row['coi']))
{
  $tpl_var['coi'] = array(
    'l'=> char_to_fraction($row['coi'][0]),
    't'=> char_to_fraction($row['coi'][1]),
    'r'=> char_to_fraction($row['coi'][2]),
    'b'=> char_to_fraction($row['coi'][3]),
  );
}

if (isset($_POST['submit']))
{
  $uid = '&b='.time();
  $conf['question_mark_in_urls'] = $conf['php_extension_in_urls'] = true;
  $conf['derivative_url_style']=2; //script
  $tpl_var['U_SQUARE'] = DerivativeImage::url(IMG_SQUARE, $row).$uid;
  $tpl_var['U_THUMB'] = DerivativeImage::url(IMG_THUMB, $row).$uid;
}
else
{
  $tpl_var['U_SQUARE'] = DerivativeImage::url(IMG_SQUARE, $row);
  $tpl_var['U_THUMB'] = DerivativeImage::url(IMG_THUMB, $row);
}

$template->assign($tpl_var);
$template->set_filename('picture_coi', 'picture_coi.tpl');

$template->assign_var_from_handle('ADMIN_CONTENT', 'picture_coi');
?>
