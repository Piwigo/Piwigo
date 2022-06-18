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

$upgrade_description = 'add index on images.path';

$query = '
ALTER TABLE '. IMAGES_TABLE .'
  ADD INDEX `images_i7` (`path`) 
;';
pwg_query($query);

echo "\n".$upgrade_description."\n";

?>
