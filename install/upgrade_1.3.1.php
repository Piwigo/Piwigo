<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2004 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
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

if (!defined('IN_UPGRADE') or !IN_UPGRADE)
{
  die('Hacking attempt!');
}

$last_time = get_moment();

// save data before deletion
$query = '
SELECT prefix_thumbnail, mail_webmaster
  FROM '.PREFIX_TABLE.'config
;';
$save = mysql_fetch_array(mysql_query($query));

$queries = array(
  "
DROP TABLE phpwebgallery_config
;",

  "
CREATE TABLE phpwebgallery_config (
  param varchar(40) NOT NULL default '',
  value varchar(255) default NULL,
  comment varchar(255) default NULL,
  PRIMARY KEY  (param)
) TYPE=MyISAM COMMENT='configuration table'
;",

  "
ALTER TABLE phpwebgallery_categories
  CHANGE COLUMN site_id site_id tinyint(4) unsigned default '1',
  ADD COLUMN commentable enum('true','false') NOT NULL default 'true',
  ADD COLUMN global_rank varchar(255) default NULL,
  DROP INDEX id,
  DROP INDEX id_uppercat,
  ADD INDEX categories_i2 (id_uppercat)
;",

  "
ALTER TABLE phpwebgallery_comments
  ADD COLUMN date_temp int(11) unsigned
;",

  "
UPDATE phpwebgallery_comments
  SET date_temp = date
;",
  
  "
ALTER TABLE phpwebgallery_comments
  CHANGE COLUMN date date datetime NOT NULL default '0000-00-00 00:00:00'
;",

  "
UPDATE phpwebgallery_comments
  SET date = FROM_UNIXTIME(date_temp)
;",

  "
ALTER TABLE phpwebgallery_comments
  DROP COLUMN date_temp
;",

  "
ALTER TABLE phpwebgallery_favorites
  DROP INDEX user_id,
  ADD PRIMARY KEY (user_id,image_id)
;",

  "
ALTER TABLE phpwebgallery_history
  ADD COLUMN date_temp int(11) unsigned
;",

  "
UPDATE phpwebgallery_history
  SET date_temp = date
;",
  
  "
ALTER TABLE phpwebgallery_history
  CHANGE COLUMN date date datetime NOT NULL default '0000-00-00 00:00:00'
;",

  "
UPDATE phpwebgallery_history
  SET date = FROM_UNIXTIME(date_temp)
;",

  "
ALTER TABLE phpwebgallery_history
  DROP COLUMN date_temp
;",

  "
ALTER TABLE phpwebgallery_history
  ADD INDEX history_i1 (date)
;",

  "
ALTER TABLE phpwebgallery_image_category
  DROP INDEX image_id,
  DROP INDEX category_id,
  ADD INDEX image_category_i1 (image_id),
  ADD INDEX image_category_i2 (category_id)
;",

  "
ALTER TABLE phpwebgallery_images
  CHANGE COLUMN tn_ext tn_ext varchar(4) default '',
  ADD COLUMN path varchar(255) NOT NULL default '',
  ADD COLUMN date_metadata_update date default NULL,
  ADD COLUMN average_rate float(5,2) unsigned default NULL,
  ADD COLUMN representative_ext varchar(4) default NULL,
  DROP INDEX storage_category_id,
  ADD INDEX images_i1 (storage_category_id),
  ADD INDEX images_i2 (date_available),
  ADD INDEX images_i3 (average_rate),
  ADD INDEX images_i4 (hit),
  ADD INDEX images_i5 (date_creation)
;",
  
  "
ALTER TABLE phpwebgallery_sessions
  DROP COLUMN ip
;",

    "
ALTER TABLE phpwebgallery_sessions
  ADD COLUMN expiration_temp int(11) unsigned
;",

  "
UPDATE phpwebgallery_sessions
  SET expiration_temp = expiration
;",
  
  "
ALTER TABLE phpwebgallery_sessions
  CHANGE COLUMN expiration expiration datetime NOT NULL default '0000-00-00 00:00:00'
;",

  "
UPDATE phpwebgallery_sessions
  SET expiration = FROM_UNIXTIME(expiration_temp)
;",

  "
ALTER TABLE phpwebgallery_sessions
  DROP COLUMN expiration_temp
;",
  
  "
ALTER TABLE phpwebgallery_sites
  DROP INDEX galleries_url,
  ADD UNIQUE sites_ui1 (galleries_url)
;",
  
  "
DROP TABLE phpwebgallery_user_category
;",

  "
ALTER TABLE phpwebgallery_users
  DROP COLUMN long_period,
  DROP COLUMN short_period,
  DROP COLUMN forbidden_categories,
  ADD COLUMN recent_period tinyint(3) unsigned NOT NULL default '7',
  DROP INDEX username,
  ADD UNIQUE users_ui1 (username)
;",
  
  "
CREATE TABLE phpwebgallery_rate (
  user_id smallint(5) unsigned NOT NULL default '0',
  element_id mediumint(8) unsigned NOT NULL default '0',
  rate tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (user_id,element_id)
) TYPE=MyISAM
;",

  "
CREATE TABLE phpwebgallery_user_forbidden (
  user_id smallint(5) unsigned NOT NULL default '0',
  need_update enum('true','false') NOT NULL default 'true',
  forbidden_categories text,
  PRIMARY KEY  (user_id)
) TYPE=MyISAM
;",

  "
UPDATE phpwebgallery_users
  SET language = 'en_UK.iso-8859-1'
    , template = 'default'
;",

  "
DELETE FROM phpwebgallery_user_access
;",

  "
DELETE FROM phpwebgallery_group_access
;"

  );

foreach ($queries as $query)
{
  $query = str_replace('phpwebgallery_', PREFIX_TABLE, $query);
  pwg_query($query);
}

$new_time = get_moment();
echo '<pre>['.get_elapsed_time($last_time, $new_time).']';
echo ' Basic database structure upgrade done</pre>';
flush();
$last_time = $new_time;

execute_sqlfile(PHPWG_ROOT_PATH.'install/config.sql',
                'phpwebgallery_',
                PREFIX_TABLE);

$queries = array(
  "
UPDATE phpwebgallery_config
  SET value = '".$save['prefix_thumbnail']."'
  WHERE param = 'prefix_thumbnail'
;",

  "
UPDATE phpwebgallery_config
  SET value = '".$save['mail_webmaster']."'
  WHERE param = 'mail_webmaster'
;"
  );

foreach ($queries as $query)
{
  $query = str_replace('phpwebgallery_', PREFIX_TABLE, $query);
  pwg_query($query);
}

$new_time = get_moment();
echo '<pre>['.get_elapsed_time($last_time, $new_time).']';
echo ' Saved configuration information restored</pre>';
flush();
$last_time = $new_time;

ordering();
update_global_rank();
update_category();

$new_time = get_moment();
echo '<pre>['.get_elapsed_time($last_time, $new_time).']';
echo ' Calculated data updated (categories.rank, categories.global_rank,
categories.date_last, categories.representative_picture_id,
categories.nb_images)</pre>';
flush();
$last_time = $new_time;

// update calculated field "images.path"
$cat_ids = array();

$query = '
SELECT DISTINCT(storage_category_id) AS unique_storage_category_id
  FROM '.IMAGES_TABLE.'
;';
$result = pwg_query($query);
while ($row = mysql_fetch_array($result))
{
  array_push($cat_ids, $row['unique_storage_category_id']);
}
$fulldirs = get_fulldirs($cat_ids);

foreach ($cat_ids as $cat_id)
{
  $query = '
UPDATE '.IMAGES_TABLE.'
  SET path = CONCAT(\''.$fulldirs[$cat_id].'\',\'/\',file)
  WHERE storage_category_id = '.$cat_id.'
;';
  pwg_query($query);
}

$new_time = get_moment();
echo '<pre>['.get_elapsed_time($last_time, $new_time).']';
echo ' new column images.path filled</pre>';
flush();
$last_time = $new_time;

// all sub-categories of private categories become private
$cat_ids = array();

$query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE status = \'private\'
;';
$result = pwg_query($query);
while ($row = mysql_fetch_array($result))
{
  array_push($cat_ids, $row['id']);
}

if (count($cat_ids) > 0)
{
  $privates = get_subcat_ids($cat_ids);

  $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET status = \'private\'
  WHERE id IN ('.implode(',', $privates).')
;';
  pwg_query($query);
}

$new_time = get_moment();
echo '<pre>['.get_elapsed_time($last_time, $new_time).']';
echo ' all sub-categories of private categories become private</pre>';
flush();
$last_time = $new_time;

$infos = array(
  'user permissions and group permissions have been erased',

  'only thumbnails prefix and webmaster mail address have been saved from
previous configuration',

  'in include/mysql.inc.php, before
<pre style="background-color:lightgray">?&gt;</pre>
insert
<pre style="background-color:lightgray">define(\'PHPWG_INSTALLED\', true);<pre>'
  
  );

?>