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

$upgrade_description = 'add config parameter to override Theme Login & Registration Pages';

// we set it to false in this upgrade script, as opposed to the default value
// for a new installation, because it was the default behavior before Piwigo 16
conf_update_param('use_standard_pages', false);

echo "\n".$upgrade_description."\n";

?>
