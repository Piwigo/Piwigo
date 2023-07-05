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

$upgrade_description = 'change nb_image_page into smallint(3)';

// add column
if ('mysql' == $conf['dblayer'])
{
  pwg_query('
    ALTER TABLE '.USER_INFOS_TABLE.' 
      CHANGE `nb_image_page` `nb_image_page` SMALLINT(3) UNSIGNED NOT NULL DEFAULT 15
  ;');
}

echo
"\n"
. $upgrade_description
."\n"
;
?>