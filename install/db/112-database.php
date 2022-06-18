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

$upgrade_description = 'Change combined dir';

// Add column
$query = 'DELETE FROM '.CONFIG_TABLE.'
  WHERE param IN (\'local_data_dir_checked\', \'combined_dir_checked\') ';

pwg_query($query);

$dir=PHPWG_ROOT_PATH.'local/combined/';
if (is_dir($dir))
{
  foreach (glob($dir.'*.css') as $file)
  {
    @unlink($file);
  }
  foreach (glob($dir.'*.js') as $file)
    @unlink($file);
  @unlink($dir.'index.htm');
  @rmdir($dir);
}
echo
"\n"
. $upgrade_description
."\n"
;
?>