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

$upgrade_description = 'add persons tables and people settings';

$query = '
CREATE TABLE `'.PREFIX_TABLE.'persons` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `user_id` mediumint(8) unsigned NULL,
  `cover_image_person_id` mediumint(8) unsigned NULL,
  `name` varchar(255) NOT NULL,
  `url_name` varchar(255) NULL,
  `is_visible` enum(\'true\',\'false\') NOT NULL DEFAULT \'true\',
  `source` enum(\'manual\',\'auto\', \'imported\') NOT NULL DEFAULT \'manual\',
  `lastmodified` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `persons_user_id` (`user_id`),
  UNIQUE KEY `persons_cover` (`cover_image_person_id`),
  KEY `persons_name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
;';
pwg_query($query);

$query = '
CREATE TABLE `'.PREFIX_TABLE.'image_person` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `image_id` mediumint(8) unsigned NOT NULL,
  `person_id` mediumint(8) unsigned NOT NULL,
  `box_x1` smallint(5) unsigned DEFAULT NULL,
  `box_y1` smallint(5) unsigned DEFAULT NULL,
  `box_x2` smallint(5) unsigned DEFAULT NULL,
  `box_y2` smallint(5) unsigned DEFAULT NULL,
  `image_width` smallint(5) unsigned DEFAULT NULL,
  `image_height` smallint(5) unsigned DEFAULT NULL,
  `source` enum(\'manual\',\'auto\', \'imported\') NOT NULL DEFAULT \'manual\',
  `tagged_on` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `tagger_user_id` mediumint(8) unsigned NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `image_person_unique` (`image_id`, `person_id`),
  KEY `image_person_i1` (`person_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
;';
pwg_query($query);

load_conf_from_db();

conf_update_param('people_access', 'registered-users');
conf_update_param('people_access_groups', array());

if (isset($conf['picture_informations']))
{
  $picture_informations = safe_unserialize($conf['picture_informations']);

  if (!isset($picture_informations['people']))
  {
    $picture_informations['people'] = true;
    conf_update_param('picture_informations', $picture_informations);
  }
}

echo "\n".$upgrade_description."\n";

?>
