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

$upgrade_description = 'add config parameter to handle duplicate on upload';

// we set it to false in this upgrade script, as opposed to the default value
// for a new installation, because it was the default behavior before Piwigo 14
conf_update_param('upload_detect_duplicate', false);

echo "\n".$upgrade_description."\n";

?>
