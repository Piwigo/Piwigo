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

$upgrade_description = 'add config parameters to display "search in this set" action and button';

conf_update_param('index_search_in_set_button', true);
conf_update_param('index_search_in_set_action', true);

echo "\n".$upgrade_description."\n";

?>
