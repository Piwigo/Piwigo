<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
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

/**
 * Upgrade from 1.3.x (x >= 1) to 1.4.0
 */

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
  CHANGE COLUMN site_id site_id tinyint(4) unsigned default '1'
;",

  "
ALTER TABLE phpwebgallery_categories
  ADD COLUMN commentable enum('true','false') NOT NULL default 'true'
;",
  
  "
ALTER TABLE phpwebgallery_categories
  ADD COLUMN global_rank varchar(255) default NULL
;",

  "
ALTER TABLE phpwebgallery_categories
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
  DROP INDEX user_id
;",

  "
ALTER TABLE phpwebgallery_favorites
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
  ADD INDEX image_category_i1 (image_id),
  ADD INDEX image_category_i2 (category_id)
;",

  "
ALTER TABLE phpwebgallery_images
  CHANGE COLUMN tn_ext tn_ext varchar(4) default ''
;",

  "
ALTER TABLE phpwebgallery_images
  ADD COLUMN path varchar(255) NOT NULL default ''
;",

  "
ALTER TABLE phpwebgallery_images
  ADD COLUMN date_metadata_update date default NULL
;",

  "
ALTER TABLE phpwebgallery_images
  ADD COLUMN average_rate float(5,2) unsigned default NULL
;",

  "
ALTER TABLE phpwebgallery_images
  ADD COLUMN representative_ext varchar(4) default NULL
;",

  "
ALTER TABLE phpwebgallery_images
  DROP INDEX storage_category_id
;",

  "
ALTER TABLE phpwebgallery_images
  ADD INDEX images_i1 (storage_category_id)
;",

  "
ALTER TABLE phpwebgallery_images
  ADD INDEX images_i2 (date_available)
;",

  "
ALTER TABLE phpwebgallery_images
  ADD INDEX images_i3 (average_rate)
;",

  "
ALTER TABLE phpwebgallery_images
  ADD INDEX images_i4 (hit)
;",

  "
ALTER TABLE phpwebgallery_images
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
  DROP INDEX galleries_url
;",

  "
ALTER TABLE phpwebgallery_sites
  ADD UNIQUE sites_ui1 (galleries_url)
;",

  "
DROP TABLE phpwebgallery_user_category
;",
  
  "
ALTER TABLE phpwebgallery_users
  DROP COLUMN long_period
;",

  "
ALTER TABLE phpwebgallery_users
  DROP COLUMN short_period
;",

  "
ALTER TABLE phpwebgallery_users
  ADD COLUMN recent_period tinyint(3) unsigned NOT NULL default '7'
;",

  "
ALTER TABLE phpwebgallery_users
  DROP INDEX username
;",

  "
ALTER TABLE phpwebgallery_users
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

//
// check indexes
//
$indexes_of = array(
  'categories' => array(
    'categories_i2' => array(
      'columns' => array('id_uppercat'),
      'unique' => false,
      )
    ),
  'image_category' => array(
    'image_category_i1' => array(
      'columns' => array('image_id'),
      'unique' => false,
      ),
    'image_category_i2' => array(
      'columns' => array('category_id'),
      'unique' => false,
      ),
    ),
  );

foreach (array_keys($indexes_of) as $table)
{
  $existing_indexes = array();
  
  $query = '
SHOW INDEX
  FROM '.PREFIX_TABLE.$table.'
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    if ($row['Key_name'] != 'PRIMARY')
    {
      if (!in_array($row['Key_name'], array_keys($indexes_of[$table])))
      {
        $query = '
ALTER TABLE '.PREFIX_TABLE.$table.'
  DROP INDEX '.$row['Key_name'].'
;';
        pwg_query($query);
      }
      else
      {
        array_push($existing_indexes, $row['Key_name']);
      }
    }
  }

  foreach ($indexes_of[$table] as $index_name => $index)
  {
    if (!in_array($index_name, $existing_indexes))
    {
      $query = '
ALTER TABLE '.PREFIX_TABLE.$table.'
  ADD '.($index['unique'] ? 'UNIQUE' : 'INDEX').' '
        .$index_name.' ('.implode(',', $index['columns']).')
;';
      pwg_query($query);
    }
  }
}

//
// insert params in new configuration table
//
$params = array(
  array(
    'param'   => 'prefix_thumbnail',
    'value'   => $save['prefix_thumbnail'],
    'comment' => 'thumbnails filename prefix'
    ),
  array(
    'param'   => 'mail_webmaster',
    'value'   => $save['mail_webmaster'],
    'comment' => 'webmaster mail'
    ),
  array(
    'param'   => 'default_language',
    'value'   => 'en_UK.iso-8859-1',
    'comment' => 'Default gallery language'
    ),
  array(
    'param'   => 'default_template',
    'value'   => 'default',
    'comment' => 'Default gallery style'
    ),
  array(
    'param'   => 'default_maxwidth',
    'value'   => '',
    'comment' => 'maximum width authorized for displaying images'
    ),
  array(
    'param'   => 'default_maxheight',
    'value'   => '',
    'comment' => 'maximum height authorized for the displaying images'
    ),
  array(
    'param'   => 'nb_comment_page',
    'value'   => '10',
    'comment' => 'number of comments to display on each page'
    ),
  array(
    'param'   => 'upload_maxfilesize',
    'value'   => '150',
    'comment' => 'maximum filesize for the uploaded pictures'
    ),
  array(
    'param'   => 'upload_maxwidth',
    'value'   => '800',
    'comment' => 'maximum width authorized for the uploaded images'
    ),
  array(
    'param'   => 'upload_maxheight',
    'value'   => '600',
    'comment' => 'maximum height authorized for the uploaded images'
    ),
  array(
    'param'   => 'upload_maxwidth_thumbnail',
    'value'   => '150',
    'comment' => 'maximum width authorized for the uploaded thumbnails'
    ),
  array(
    'param'   => 'upload_maxheight_thumbnail',
    'value'   => '100',
    'comment' => 'maximum height authorized for the uploaded thumbnails'
    ),
  array(
    'param'   => 'log',
    'value'   => 'false',
    'comment' => 'keep an history of visits on your website'
    ),
  array(
    'param'   => 'comments_validation',
    'value'   => 'false',
    'comment' => 'administrators validate users comments before becoming visible'
    ),
  array(
    'param'   => 'comments_forall',
    'value'   => 'false',
    'comment' => 'even guest not registered can post comments'
    ),
  array(
    'param'   => 'mail_notification',
    'value'   => 'false',
    'comment' => 'automated mail notification for adminsitrators'
    ),
  array(
    'param'   => 'nb_image_line',
    'value'   => '5',
    'comment' => 'Number of images displayed per row'
    ),
  array(
    'param'   => 'nb_line_page',
    'value'   => '3',
    'comment' => 'Number of rows displayed per page'
    ),
  array(
    'param'   => 'recent_period',
    'value'   => '7',
    'comment' => 'Period within which pictures are displayed as new (in days)'
    ),
  array(
    'param'   => 'auto_expand',
    'value'   => 'false',
    'comment' => 'Auto expand of the category tree'
    ),
  array(
    'param'   => 'show_nb_comments',
    'value'   => 'false',
    'comment' => 'Show the number of comments under the thumbnails'
    ),
  array(
    'param'   => 'use_iptc',
    'value'   => 'false',
    'comment' => 'Use IPTC data during database synchronization with files metadata'
    ),
  array(
    'param'   => 'use_exif',
    'value'   => 'false',
    'comment' => 'Use EXIF data during database synchronization with files metadata'
    ),
  array(
    'param'   => 'show_iptc',
    'value'   => 'false',
    'comment' => 'Show IPTC metadata on picture.php if asked by user'
    ),
  array(
    'param'   => 'show_exif',
    'value'   => 'true',
    'comment' => 'Show EXIF metadata on picture.php if asked by user'
    ),
  array(
    'param'   => 'authorize_remembering',
    'value'   => 'true',
    'comment' => 'Authorize users to be remembered, see $conf{remember_me_length}'
    ),
  array(
    'param'   => 'gallery_locked',
    'value'   => 'false',
    'comment' => 'Lock your gallery temporary for non admin users'
    ),
  );

mass_inserts(
  CONFIG_TABLE,
  array_keys($params[0]),
  $params
  );

// refresh calculated datas
ordering();
update_global_rank();
update_category();

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

$page['infos'] = array_merge(
  $page['infos'],
  array(
    'all sub-categories of private categories become private',
    
    'user permissions and group permissions have been erased',

    'only thumbnails prefix and webmaster mail address have been saved from
previous configuration',

    'in include/mysql.inc.php, before
<pre style="background-color:lightgray">?&gt;</pre>
insert
<pre style="background-color:lightgray">define(\'PHPWG_INSTALLED\', true);<pre>'
    )
  );


// now we upgrade from 1.4.0
include_once(PHPWG_ROOT_PATH.'install/upgrade_1.4.0.php');
?>