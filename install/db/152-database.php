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

$upgrade_description = 'add 5 parameters to show/hide icons (edit/caddie/repressentative)';

conf_update_param('index_edit_icon','true');
conf_update_param('index_caddie_icon','true');
conf_update_param('picture_edit_icon','true');
conf_update_param('picture_caddie_icon','true');
conf_update_param('picture_representative_icon','true');


echo "\n".$upgrade_description."\n";

?>
