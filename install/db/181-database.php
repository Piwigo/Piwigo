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

$upgrade_description = 'add "expert mode" in filters_views for gallery search';

load_conf_from_db();

// if the filters_views is not already registered in the config table, no need
// to update it because it will be initialized with all filters
if (isset($conf['filters_views']))
{
  $conf['filters_views'] = safe_unserialize($conf['filters_views']);

  if (!isset($conf['filters_views']['expert']))
  {
    $conf['filters_views']['expert'] = $conf['default_filters_views']['expert'];
    conf_update_param('filters_views', $conf['filters_views']);
  }
}

echo "\n".$upgrade_description."\n";

?>
