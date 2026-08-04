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

$upgrade_description = 'Add 3XL and 4XL sizes (disabled by default)';

load_conf_from_db();

//get default sizes from derivative_std_params
$default_sizes = ImageStdParams::get_default_sizes();

// set new derivatives to add
$new_derivatives = array(IMG_3XLARGE, IMG_4XLARGE);

// get current disabled derivative_std_params
$disabled_derivatives = safe_unserialize(ImageStdParams::get_disabled_type_map());

// get the new derivative_std_params and merge with current disabled derivative_std_params
$new_disabled_derivatives = array_intersect_key($default_sizes, array_flip($new_derivatives));
$disabled = array_merge($disabled_derivatives, $new_disabled_derivatives);

// set and set new derivative_std_params in disabled list
ImageStdParams::set_and_save_disabled($disabled);

echo "\n".$upgrade_description."\n";
?>