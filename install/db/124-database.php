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

$upgrade_description = 'derivatives: remove useless configuration settings and columns';

include_once(PHPWG_ROOT_PATH.'include/constants.php');

//
// Clean useless configuration settings
//
pwg_query('DELETE FROM '.CONFIG_TABLE.' WHERE param like \'upload_form_%\';');

//
// Remove useless columns
//

$query = '
ALTER TABLE '.USER_INFOS_TABLE.'
  DROP `maxwidth`,
  DROP `maxheight`
;';
pwg_query($query);

$query = '
ALTER TABLE '.IMAGES_TABLE.'
  DROP `high_width`,
  DROP `high_height`,
  DROP `high_filesize`,
  DROP `has_high`,
  DROP `tn_ext`
;';
pwg_query($query);

echo "\n".$upgrade_description."\n";
?>