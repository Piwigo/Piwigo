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

$upgrade_description = 'convert file configuration setting webmaster_id to database';

// If the webmaster_id has been modified, it must be present in local/config/config.inc.php
// so we retrieve it and insert it into the database.
conf_update_param('webmaster_id', $conf['webmaster_id'] ?? 1);

echo "\n".$upgrade_description."\n";

?>