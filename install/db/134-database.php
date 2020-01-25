<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

$upgrade_description = 'replace dblayer to "mysqli" if available.';

global $conf;

$config_file = PHPWG_ROOT_PATH.PWG_LOCAL_DIR .'config/database.inc.php';

if ( extension_loaded('mysqli') and $conf['dblayer']=='mysql' and is_writable($config_file) )
{
  $file_content = file_get_contents($config_file);
  $file_content = preg_replace(
                            '#\$conf\[\'dblayer\'\]( *)=( *)\'mysql\';#', 
                            '$conf[\'dblayer\']$1=$2\'mysqli\';', 
                            $file_content);
  file_put_contents($config_file, $file_content);
}

echo "\n".$upgrade_description."\n";
?>