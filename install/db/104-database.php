<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2016 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
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