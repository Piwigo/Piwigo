<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die ('This page cannot be loaded directly, load upgrade.php');
}
else
{
  if (!defined('PHPWG_IN_UPGRADE') or !PHPWG_IN_UPGRADE)
  {
    die ('Hacking attempt!');
  }
}

$queries = array(
  "
ALTER TABLE ".PREFIX_TABLE."user_infos
  ADD auto_login_key varchar(64) NOT NULL
;",
  '
ALTER TABLE '.PREFIX_TABLE.'users
  CHANGE username username VARCHAR(100) binary NOT NULL
;',
  );

foreach ($queries as $query)
{
  pwg_query($query);
}

// now we upgrade from 1.6.2
include_once(PHPWG_ROOT_PATH.'install/upgrade_1.6.2.php');
?>
