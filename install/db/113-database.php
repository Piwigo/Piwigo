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

$upgrade_description = 'New settings for resizing original photo (related to multiple sizes feature)';

conf_update_param('original_resize', 'false');
conf_update_param('original_resize_maxwidth', 2016);
conf_update_param('original_resize_maxheight', 2016);
conf_update_param('original_resize_quality', 95);

echo
"\n"
. $upgrade_description
."\n"
;
?>