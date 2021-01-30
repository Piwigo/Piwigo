<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if (!defined("PHPWG_ROOT_PATH"))
{
  die('Hacking attempt!');
}

$upgrade_description = 'Add display configuration for picture properties.';

$query = '
INSERT INTO '.CONFIG_TABLE.' (param,value,comment)
  VALUES
    ("picture_download_icon","true","Display download icon on picture page"),
    (
      "picture_informations", 
      "a:11:{s:6:\"author\";b:1;s:10:\"created_on\";b:1;s:9:\"posted_on\";b:1;s:10:\"dimensions\";b:1;s:4:\"file\";b:1;s:8:\"filesize\";b:1;s:4:\"tags\";b:1;s:10:\"categories\";b:1;s:6:\"visits\";b:1;s:12:\"average_rate\";b:1;s:13:\"privacy_level\";b:1;}",
      "Information displayed on picture page"
    )
;';

pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>