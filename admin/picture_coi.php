<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
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
  foreach(ImageStdParams::get_defined_type_map() as $params)
  {
    if ($params->sizing->max_crop != 0)
    {
      delete_element_derivatives($row, $params->type);
    }
  }
  delete_element_derivatives($row, IMG_CUSTOM);
  $uid = '&b='.time();
  $conf['question_mark_in_urls'] = $conf['php_extension_in_urls'] = true;
  if ($conf['derivative_url_style']==1)
  {
    $conf['derivative_url_style']=0; //auto
  }
}
else
{
  $uid = '';
}

$tpl_var = array(
  'TITLE' => render_element_name($row),
  'ALT' => $row['file'],
  'U_IMG' => DerivativeImage::url(IMG_LARGE, $row),
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

foreach(ImageStdParams::get_defined_type_map() as $params)
{
  if ($params->sizing->max_crop != 0)
  {
    $derivative = new DerivativeImage($params, new SrcImage($row) );
    $template->append( 'cropped_derivatives', array( 
      'U_IMG' => $derivative->get_url().$uid,
      'HTM_SIZE' => $derivative->get_size_htm(),
    ) );
  }
}


$template->assign($tpl_var);
$template->set_filename('picture_coi', 'picture_coi.tpl');

$template->assign_var_from_handle('ADMIN_CONTENT', 'picture_coi');
?>
