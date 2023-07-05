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

$upgrade_description = 'add "latitude" and "longitude" fields';

// add fields
$query = '
ALTER TABLE '. IMAGES_TABLE .'
  ADD `latitude` DOUBLE(8, 6) DEFAULT NULL,
  ADD `longitude` DOUBLE(9, 6) DEFAULT NULL
;';
pwg_query($query);

// add index
$query = '
ALTER TABLE '. IMAGES_TABLE .'
  ADD INDEX `images_i6` (`latitude`) 
;';
pwg_query($query);

// search for old "lat" field
$query = 'SHOW COLUMNS FROM '. IMAGES_TABLE .' LIKE "lat";';

if (pwg_db_num_rows(pwg_query($query)))
{
  // duplicate non-null values
  $query = '
UPDATE '. IMAGES_TABLE .'
  SET latitude = lat,
    longitude = lon
  WHERE lat IS NOT NULL
    AND lon IS NOT NULL
;';
  pwg_query($query);
}

echo "\n".$upgrade_description."\n";

?>
