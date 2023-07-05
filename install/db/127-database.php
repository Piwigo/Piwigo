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

$upgrade_description = 'rename language no_NO into nb_NO';

include_once(PHPWG_ROOT_PATH.'include/constants.php');

$query = '
UPDATE '.USER_INFOS_TABLE.'
  SET language = \'nb_NO\'
  WHERE language = \'no_NO\'
;';
pwg_query($query);

$query = '
UPDATE '.LANGUAGES_TABLE.'
  SET id = \'nb_NO\'
  WHERE id = \'no_NO\'
;';
pwg_query($query);

echo "\n".$upgrade_description."\n";
?>