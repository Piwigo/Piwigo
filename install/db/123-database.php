<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

$upgrade_description = 'convert 2.3 resize settings into 2.4 derivative settings';

include_once(PHPWG_ROOT_PATH.'include/constants.php');

$dbconf = array();
$conf_orig = $conf;
load_conf_from_db();
$dbconf = $conf;
$conf = $conf_orig;

//
// Piwigo 2.3 "HD resize" settings become "original resize" settings in Piwigo 2.4
//

if ($dbconf['upload_form_hd_keep'])
{
  if ($dbconf['upload_form_hd_resize'])
  {
    conf_update_param('original_resize', 'true');
    conf_update_param('original_resize_maxwidth', $dbconf['upload_form_hd_maxwidth']);
    conf_update_param('original_resize_maxheight', $dbconf['upload_form_hd_maxheight']);
    conf_update_param('original_resize_quality', $dbconf['upload_form_hd_quality']);
  }
}
else
{
  // The user has decided to remove the high quality. In Piwigo 2.4, this
  // setting does not exists anymore, but we can simulate it by an original
  // resize with 2.3 websize dimensions
  conf_update_param('original_resize', 'true');
  
  conf_update_param(
    'original_resize_maxwidth',
    is_numeric($dbconf['upload_form_websize_maxwidth']) ? $dbconf['upload_form_websize_maxwidth'] : 800
    );
                    
  conf_update_param(
    'original_resize_maxheight',
    is_numeric($dbconf['upload_form_websize_maxheight']) ? $dbconf['upload_form_websize_maxheight'] : 600
    );
  
  conf_update_param('original_resize_quality', $dbconf['upload_form_hd_quality']);
}

$types = ImageStdParams::get_default_sizes();

// 
// Piwigo 2.3 "thumbnail" becomes "thumb" size in Piwigo 2.4
//

$thumb_width_min = 128; // the default value in Piwigo 2.3
$thumb_width_max = 300; // slightly bigger than XXS default maxwidth
$thumb_height_min = 96; // the default value in Piwigo 2.3
$thumb_height_max = 300; // slightly bigger than XXS default maxheight

$thumb_is_square = false;
if ($dbconf['upload_form_thumb_crop'])
{
  if ($dbconf['upload_form_thumb_maxwidth'] == $dbconf['upload_form_thumb_maxheight'])
  {
    $thumb_is_square = true;
  }
}

if ($dbconf['upload_form_thumb_maxwidth'] < $thumb_width_min)
{
  $dbconf['upload_form_thumb_maxwidth'] = $thumb_width_min;
}

if ($dbconf['upload_form_thumb_maxwidth'] > $thumb_width_max)
{
  $dbconf['upload_form_thumb_maxwidth'] = $thumb_width_max;
}

if ($dbconf['upload_form_thumb_maxheight'] < $thumb_height_min)
{
  $dbconf['upload_form_thumb_maxheight'] = $thumb_height_min;
}

if ($dbconf['upload_form_thumb_maxheight'] > $thumb_height_max)
{
  $dbconf['upload_form_thumb_maxheight'] = $thumb_height_max;
}

if ($thumb_is_square)
{
  $dbconf['upload_form_thumb_maxheight'] = $dbconf['upload_form_thumb_maxwidth'];
}

$size = array($dbconf['upload_form_thumb_maxwidth'], $dbconf['upload_form_thumb_maxheight']);

$thumb = new DerivativeParams(
  new SizingParams(
    $size,
    $dbconf['upload_form_thumb_crop'] ? 1 : 0,
    $dbconf['upload_form_thumb_crop'] ? $size : null
    )
  );

$types[IMG_THUMB] = $thumb;

// slightly enlarge XSS to be bigger than thumbnail size (but smaller than XS)
if ($dbconf['upload_form_thumb_maxwidth'] >= $types[IMG_XXSMALL]->sizing->ideal_size[0]
    or $dbconf['upload_form_thumb_maxheight'] >= $types[IMG_XXSMALL]->sizing->ideal_size[1])
{
  $xxs_maxwidth = $types[IMG_XXSMALL]->sizing->ideal_size[0];
  if ($dbconf['upload_form_thumb_maxwidth'] >= $xxs_maxwidth)
  {
    $xxs_maxwidth = 350;
  }

  $xxs_maxheight = $types[IMG_XXSMALL]->sizing->ideal_size[1];
  if ($dbconf['upload_form_thumb_maxheight'] >= $xxs_maxheight)
  {
    $xxs_maxheight = 310;
  }

  $xxs = new DerivativeParams(new SizingParams(array($xxs_maxwidth, $xxs_maxheight)));

  $types[IMG_XXSMALL] = $xxs;
}

// 
// Piwigo 2.3 "websize" becomes "medium" size in Piwigo 2.4
//

// if there was no "websize resize" on Piwigo 2.3, we can't take the resize
// settings into account, we keep the default settings of Piwigo 2.4.
if ($dbconf['upload_form_websize_resize'])
{
  $medium_width_min = 577; // default S maxwidth + 1 pixel
  $medium_width_max = 1007; // default L maxwidth - 1 pixel
  $medium_height_min = 433; // default S maxheight + 1 pixel
  $medium_height_max = 755; // default L maxheight - 1 pixel

  // width
  if (!is_numeric($dbconf['upload_form_websize_maxwidth'])) // sometimes maxwidth="false"
  {
    $dbconf['upload_form_websize_maxwidth'] = $medium_width_max;
  }
  
  if ($dbconf['upload_form_websize_maxwidth'] < $medium_width_min)
  {
    $dbconf['upload_form_websize_maxwidth'] = $medium_width_min;
  }
  
  if ($dbconf['upload_form_websize_maxwidth'] > $medium_width_max)
  {
    $dbconf['upload_form_websize_maxwidth'] = $medium_width_max;
  }
  
  // height
  if (!is_numeric($dbconf['upload_form_websize_maxheight'])) // sometimes maxheight="false"
  {
    $dbconf['upload_form_websize_maxheight'] = $medium_height_max;
  }
  
  if ($dbconf['upload_form_websize_maxheight'] < $medium_height_min)
  {
    $dbconf['upload_form_websize_maxheight'] = $medium_height_min;
  }
  
  if ($dbconf['upload_form_websize_maxheight'] > $medium_height_max)
  {
    $dbconf['upload_form_websize_maxheight'] = $medium_height_max;
  }

  $medium = new DerivativeParams(
    new SizingParams(
      array(
        $dbconf['upload_form_websize_maxwidth'],
        $dbconf['upload_form_websize_maxheight']
        )
      )
    );

  $types[IMG_MEDIUM] = $medium;
}

//
// Save derivative new settings
// 

ImageStdParams::set_and_save($types);

pwg_query('DELETE FROM '.CONFIG_TABLE.' WHERE param = \'disabled_derivatives\'');
clear_derivative_cache();

echo "\n".$upgrade_description."\n";
?>