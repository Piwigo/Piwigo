<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if (!defined('PHOTOS_ADD_BASE_URL'))
{
  die ("Hacking attempt!");
}

// +-----------------------------------------------------------------------+
// |                        batch management request                       |
// +-----------------------------------------------------------------------+

if (isset($_GET['batch']))
{
  check_input_parameter('batch', $_GET, false, '/^\d+(,\d+)*$/');

  $query = '
DELETE FROM '.CADDIE_TABLE.'
  WHERE user_id = '.$user['id'].'
;';
  pwg_query($query);

  $inserts = array();
  foreach (explode(',', $_GET['batch']) as $image_id)
  {
    $inserts[] = array(
      'user_id' => $user['id'],
      'element_id' => $image_id,
      );
  }
  mass_inserts(
    CADDIE_TABLE,
    array_keys($inserts[0]),
    $inserts
    );

  redirect(get_root_url().'admin.php?page=batch_manager&filter=prefilter-caddie');
}

// +-----------------------------------------------------------------------+
// |                             Formats Mode                              |
// +-----------------------------------------------------------------------+

$display_formats = $conf['enable_formats'] && isset($_GET['formats']);

$have_formats_original = false;
$formats_original_info = array();

// If URL parameter isn't empty
if ($display_formats && $_GET['formats']) 
{
  check_input_parameter('formats', $_GET, false, PATTERN_ID, false);
  
  $formats_original_info = get_image_infos($_GET['formats']);
  if ($formats_original_info)
  {
    $src_image = new SrcImage($formats_original_info);
  
    $formats_original_info['src'] = DerivativeImage::url(IMG_SQUARE, $src_image);

    // Fetch actual formats
    $query = '
SELECT *
  FROM '.IMAGE_FORMAT_TABLE.'
  WHERE image_id = '.$formats_original_info['id'].'
;';
    $formats = query2array($query);

    if (!empty($formats))
    {
      $format_strings = array();
      
      foreach ($formats as $format)
      {
        $format_strings[] = sprintf('%s (%.2fMB)', $format['ext'], $format['filesize']/1024);
      }

      $formats_original_info['formats'] = l10n('Formats: %s', implode(', ', $format_strings));
    }

    $extTab = explode('.',$formats_original_info['file']);

    $formats_original_info['ext'] = l10n('%s file type',strtoupper(end($extTab)));

    $formats_original_info['u_edit'] = get_root_url().'admin.php?page=photo-'.$formats_original_info['id'];
      
    $have_formats_original = true;
  } 
  else
  {
    $page['errors'][] = l10n('The original picture selected dosen\'t exists.');
  }
  
}

// +-----------------------------------------------------------------------+
// |                             prepare form                              |
// +-----------------------------------------------------------------------+

include_once(PHPWG_ROOT_PATH.'admin/include/photos_add_direct_prepare.inc.php');

// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+

trigger_notify('loc_end_photo_add_direct');

$template->assign(array(
  'ENABLE_FORMATS' => $conf['enable_formats'],
  'DISPLAY_FORMATS' => $display_formats,
  'HAVE_FORMATS_ORIGINAL' => $have_formats_original,
  'FORMATS_ORIGINAL_INFO' => $formats_original_info,
  'SWITCH_MODE_URL' => get_root_url().'admin.php?page=photos_add'.($display_formats ? '':'&formats'),
  'format_ext' =>  implode(',', $conf['format_ext']),
  'str_format_ext' =>  implode(', ', $conf['format_ext']),
));

$template->assign_var_from_handle('ADMIN_CONTENT', 'photos_add');
?>
