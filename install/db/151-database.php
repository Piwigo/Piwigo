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

$upgrade_description = 'add "picture_sizes_icon" and "index_sizes_icon" parameters';

conf_update_param('index_sizes_icon', 'true');
conf_update_param('picture_sizes_icon', 'true');

echo "\n".$upgrade_description."\n";

?>
