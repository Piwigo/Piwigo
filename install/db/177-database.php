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

global $conf;
load_conf_from_db();

$upgrade_description = 'Add 3XL and 4XL sizes';

//Get predefined image sizes
$derivatives = unserialize($conf['derivatives']);

//get default sizes from derivative_std_params
$default_sizes = ImageStdParams::get_default_sizes();

//Get 3XL and 4XL from default values
$default_derivative_3XL = $default_sizes['3xlarge'];
$default_derivative_4XL = $default_sizes['4xlarge'];

//We need to make sure that a user hasn't redefined the XXL size bigger than the default 3XL
//Get xxl size
$xxl = $derivatives['d']['xxlarge'];
$xxl_height = $xxl->sizing->ideal_size[0];
$xxl_width = $xxl->sizing->ideal_size[1];

//get 3xl size
$triple_xl_height = $default_sizes['3xlarge']->sizing->ideal_size[0];
$triple_xl_width = $default_sizes['3xlarge']->sizing->ideal_size[1];

//Get 4xl size
$quad_xl_height = $default_sizes['3xlarge']->sizing->ideal_size[0];
$quad_xl_width = $default_sizes['3xlarge']->sizing->ideal_size[1];

//Set 3XL and 4xl size to be bigger than XXL if needed
if ($triple_xl_height < $xxl_height or $triple_xl_width < $xxl_width)
{
  $new_3xl_height = ceil($xxl_height*1.5);
  $new_3xl_width = ceil($xxl_width*1.5);

  $default_sizes['3xlarge']->sizing->ideal_size[0] = $new_3xl_height;
  $default_sizes['3xlarge']->sizing->ideal_size[1] = $new_3xl_width;

  $default_sizes['4xlarge']->sizing->ideal_size[0] = ceil($new_3xl_width*1.5);
  $default_sizes['4xlarge']->sizing->ideal_size[1] = ceil($new_3xl_width*1.5);
}

//Add new 3xl and 4xl to derivatives sizes config
$derivatives['d'][IMG_3XLARGE] = $default_sizes['3xlarge'];
$derivatives['d'][IMG_4XLARGE] = $default_sizes['4xlarge'];

// Save derivative new settings
ImageStdParams::set_and_save($derivatives['d']);

echo "\n".$upgrade_description."\n";
?>