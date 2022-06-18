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

$upgrade_description = 'Show date period of an album';

function value_display_fromto()
{
  $file = PHPWG_ROOT_PATH.'local/config/config.inc.php';
  if (file_exists($file))
  {
    $conf = array();
    include($file);
    if (isset($conf['display_fromto']) and $conf['display_fromto'])
    {
      return 'true';
    }
  }
  return 'false';
}
$value = value_display_fromto();

$query = '
INSERT INTO '.PREFIX_TABLE.'config (param,value,comment)
  VALUES (\'display_fromto\',\''.$value.'\', \''.$upgrade_description.'\')
;';

pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>
