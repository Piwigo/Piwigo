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

$upgrade_description = 'Add upload form parameters in database';

global $conf;

load_conf_from_db();

$upload_form_config = array(
  'websize_resize' => true,
  'websize_maxwidth' => 800,
  'websize_maxheight' => 600,
  'websize_quality' => 95,
  'thumb_maxwidth' => 128,
  'thumb_maxheight' => 96,
  'thumb_quality' => 95,
  'thumb_crop' => false,
  'thumb_follow_orientation' => true,
  'hd_keep' => true,
  'hd_resize' => false,
  'hd_maxwidth' => 2000,
  'hd_maxheight' => 2000,
  'hd_quality' => 95,
);

$inserts = array();

foreach ($upload_form_config as $param_shortname => $param)
{
  $param_name = 'upload_form_'.$param_shortname;

  if (!isset($conf[$param_name]))
  {
    $conf[$param_name] = $param;
    
    array_push(
      $inserts,
      array(
        'param' => $param_name,
        'value' => boolean_to_string($param),
        )
      );
  }
}

if (count($inserts) > 0)
{
  mass_inserts(
    CONFIG_TABLE,
    array_keys($inserts[0]),
    $inserts
    );
}

echo
"\n"
. $upgrade_description
."\n"
;
?>