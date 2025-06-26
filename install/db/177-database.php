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
$derivatives['d'][IMG_3XLARGE] = $default_sizes['3xlarge'];
$derivatives['d'][IMG_4XLARGE] = $default_sizes['4xlarge'];

// Save derivative new settings
ImageStdParams::set_and_save($derivatives['d']);

echo "\n".$upgrade_description."\n";
?>