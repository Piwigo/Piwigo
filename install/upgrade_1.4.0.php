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
  die ('This page cannot be loaded directly, load upgrade.php');
}
else
{
  if (!defined('PHPWG_IN_UPGRADE') or !PHPWG_IN_UPGRADE)
  {
    die ('Hacking attempt!');
  }
}

$last_time = get_moment();

// will the user have to edit include/config_local.inc.php for
// prefix_thumbnail configuration parameter
$query = '
SELECT value
  FROM '.CONFIG_TABLE.'
  WHERE param = \'prefix_thumbnail\'
;';
list($prefix_thumbnail) = pwg_db_fetch_row(pwg_query($query));

// delete obsolete configuration
$query = '
DELETE
  FROM '.PREFIX_TABLE.'config
  WHERE param IN (
   \'prefix_thumbnail\',
   \'mail_webmaster\',
   \'upload_maxfilesize\',
   \'upload_maxwidth\',
   \'upload_maxheight\',
   \'upload_maxwidth_thumbnail\',
   \'upload_maxheight_thumbnail\',
   \'mail_notification\',
   \'use_iptc\',
   \'use_exif\',
   \'show_iptc\',
   \'show_exif\',
   \'authorize_remembering\'
   )
;';
pwg_query($query);

$queries = array(

  "
ALTER TABLE piwigo_categories
  CHANGE COLUMN date_last date_last datetime default NULL
;",

  "
ALTER TABLE piwigo_comments
  ADD COLUMN validation_date datetime default NULL
;",

  "
UPDATE piwigo_comments
  SET validation_date = date
",

  "
ALTER TABLE piwigo_comments
  ADD INDEX comments_i1 (image_id)
;",

  "
ALTER TABLE piwigo_comments
  ADD INDEX comments_i2 (validation_date)
;",

  "
ALTER TABLE piwigo_favorites
  CHANGE COLUMN user_id user_id smallint(5) NOT NULL default '0'
;",

  "
ALTER TABLE piwigo_images
  CHANGE COLUMN date_available
    date_available datetime NOT NULL default '0000-00-00 00:00:00'
;",

  "
ALTER TABLE piwigo_rate
  CHANGE COLUMN user_id user_id smallint(5) NOT NULL default '0'
;",

  "
ALTER TABLE piwigo_sessions
  CHANGE COLUMN user_id user_id smallint(5) NOT NULL default '0'
;",

  "
ALTER TABLE piwigo_user_access
  CHANGE COLUMN user_id user_id smallint(5) NOT NULL default '0'
;",

  "
DROP TABLE piwigo_user_forbidden
;",

  "
ALTER TABLE piwigo_user_group
 CHANGE COLUMN user_id user_id smallint(5) NOT NULL default '0'
;",

  "
ALTER TABLE piwigo_users
  CHANGE COLUMN id id smallint(5) NOT NULL auto_increment
;",

  "
CREATE TABLE piwigo_caddie (
  user_id smallint(5) NOT NULL default '0',
  element_id mediumint(8) NOT NULL default '0',
  PRIMARY KEY  (user_id,element_id)
) ENGINE=MyISAM
;",

  "
CREATE TABLE piwigo_user_cache (
  user_id smallint(5) NOT NULL default '0',
  need_update enum('true','false') NOT NULL default 'true',
  forbidden_categories text,
  PRIMARY KEY  (user_id)
) ENGINE=MyISAM
;",

  "
CREATE TABLE piwigo_user_feed (
  id varchar(50) binary NOT NULL default '',
  user_id smallint(5) NOT NULL default '0',
  last_check datetime default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM
;",

  "
CREATE TABLE piwigo_user_infos (
  user_id smallint(5) NOT NULL default '0',
  nb_image_line tinyint(1) unsigned NOT NULL default '5',
  nb_line_page tinyint(3) unsigned NOT NULL default '3',
  status enum('admin','guest') NOT NULL default 'guest',
  language varchar(50) NOT NULL default 'english',
  maxwidth smallint(6) default NULL,
  maxheight smallint(6) default NULL,
  expand enum('true','false') NOT NULL default 'false',
  show_nb_comments enum('true','false') NOT NULL default 'false',
  recent_period tinyint(3) unsigned NOT NULL default '7',
  template varchar(255) NOT NULL default 'yoga',
  registration_date datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY user_infos_ui1 (user_id)
) ENGINE=MyISAM
;"
  );

foreach ($queries as $query)
{
  $query = str_replace('piwigo_', PREFIX_TABLE, $query);
  pwg_query($query);
}

// user datas migration from piwigo_users to piwigo_user_infos
$query = '
SELECT *
  FROM '.USERS_TABLE.'
;';

$datas = array();
list($dbnow) = pwg_db_fetch_row(pwg_query('SELECT NOW();'));

$result = pwg_query($query);
while ($row = pwg_db_fetch_assoc($result))
{
  $row['user_id'] = $row['id'];
  $row['registration_date'] = $dbnow;
  array_push($datas, $row);
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
mass_inserts(
  USER_INFOS_TABLE,
  array(
    'user_id',
    'nb_image_line',
    'nb_line_page',
    'status',
    'language',
    'maxwidth',
    'maxheight',
    'expand',
    'show_nb_comments',
    'recent_period',
    'template',
    'registration_date'
    ),
  $datas
  );

$queries = array(

  "
UPDATE ".USER_INFOS_TABLE."
  SET template = 'yoga'
;",

  "
UPDATE ".USER_INFOS_TABLE."
  SET language = 'en_UK.iso-8859-1'
  WHERE language NOT IN ('en_UK.iso-8859-1', 'fr_FR.iso-8859-1')
;",

  "
UPDATE ".CONFIG_TABLE."
  SET value = 'en_UK.iso-8859-1'
  WHERE param = 'default_language'
    AND value NOT IN ('en_UK.iso-8859-1', 'fr_FR.iso-8859-1')
;",

  "
UPDATE ".CONFIG_TABLE."
  SET value = 'yoga'
  WHERE param = 'default_template'
;",

  "
INSERT INTO ".CONFIG_TABLE."
  (param,value,comment)
  VALUES
  (
    'gallery_title',
    'Piwigo demonstration site',
    'Title at top of each page and for RSS feed'
  )
;",

  "
INSERT INTO ".CONFIG_TABLE."
  (param,value,comment)
  VALUES
  (
    'gallery_description',
    'My photos web site',
    'Short description displayed with gallery title'
  )
;"

  );

foreach ($queries as $query)
{
  $query = str_replace('piwigo_', PREFIX_TABLE, $query);
  pwg_query($query);
}

if ($prefix_thumbnail != 'TN-')
{
  array_push(
    $page['infos'],
    'the thumbnail prefix configuration parameter was moved to configuration
file, copy config.inc.php from "tools" directory to "local/config" directory
and edit $conf[\'prefix_thumbnail\'] = '.$prefix_thumbnail
    );
}

// now we upgrade from 1.5.0 to 1.6.0
include_once(PHPWG_ROOT_PATH.'install/upgrade_1.5.0.php');
?>