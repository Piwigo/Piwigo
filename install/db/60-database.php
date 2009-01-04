<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
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

$upgrade_description = 'Change default value on #user_infos for guest';

include_once(PHPWG_ROOT_PATH.'include/constants.php');
include(PHPWG_ROOT_PATH . 'include/config_default.inc.php');
@include(PHPWG_ROOT_PATH. 'include/config_local.inc.php');

// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+

load_conf_from_db();

$query = "
update ".USER_INFOS_TABLE."
set
  template = '".$conf['default_template']."',
  nb_image_line = ".$conf['nb_image_line'].",
  nb_line_page = ".$conf['nb_line_page'].",
  language = '".$conf['default_language']."',
  maxwidth = ".(empty($conf['default_maxwidth']) ? "null" : $conf['default_maxwidth']).",
  maxheight = ".(empty($conf['default_maxheight']) ? "null" : $conf['default_maxheight']).",
  recent_period = ".$conf['recent_period'].",
  expand = '".boolean_to_string($conf['auto_expand'])."',
  show_nb_comments = '".boolean_to_string($conf['show_nb_comments'])."',
  show_nb_hits = '".boolean_to_string($conf['show_nb_hits'])."',
  enabled_high = '".boolean_to_string(
    (isset($conf['newuser_default_enabled_high']) ? 
      $conf['newuser_default_enabled_high'] : true))."'
where
  user_id = ".$conf['default_user_id'].";";
pwg_query($query);


$query = "
delete from ".CONFIG_TABLE."
where
  param in
(
  'default_template',
  'nb_image_line',
  'nb_line_page',
  'default_language',
  'default_maxwidth',
  'default_maxheight',
  'recent_period',
  'auto_expand',
  'show_nb_comments',
  'show_nb_hits'
);";
pwg_query($query);

echo
"\n"
.'"'.$upgrade_description.'"'.' ended'
."\n"
;

?>
