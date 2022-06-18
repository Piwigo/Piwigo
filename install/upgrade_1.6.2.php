<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
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

$queries = array(
"
ALTER TABLE `".PREFIX_TABLE."categories`
  ADD COLUMN `permalink` varchar(64) default NULL
;",

"
ALTER TABLE `".PREFIX_TABLE."categories`
  ADD COLUMN `image_order` varchar(128) default NULL
;",

"
ALTER TABLE `".PREFIX_TABLE."categories`
  ADD UNIQUE `categories_i3` (`permalink`)
;",

"
ALTER TABLE `".PREFIX_TABLE."groups`
  ADD COLUMN `is_default` enum('true','false') NOT NULL default 'false'
;",

"
RENAME TABLE `".PREFIX_TABLE."history` TO `".PREFIX_TABLE."history_backup`
;",

"
CREATE TABLE `".PREFIX_TABLE."history` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `date` date NOT NULL default '0000-00-00',
  `time` time NOT NULL default '00:00:00',
  `year` smallint(4) NOT NULL default '0',
  `month` tinyint(2) NOT NULL default '0',
  `day` tinyint(2) NOT NULL default '0',
  `hour` tinyint(2) NOT NULL default '0',
  `user_id` smallint(5) NOT NULL default '0',
  `IP` varchar(15) NOT NULL default '',
  `section` enum('categories','tags','search','list','favorites','most_visited','best_rated','recent_pics','recent_cats') default NULL,
  `category_id` smallint(5) default NULL,
  `tag_ids` varchar(50) default NULL,
  `image_id` mediumint(8) default NULL,
  `summarized` enum('true','false') default 'false',
  `image_type` enum('picture','high','other') default NULL,
  PRIMARY KEY  (`id`),
  KEY `history_i1` (`summarized`)
) ENGINE=MyISAM
;",

"
ALTER TABLE `".PREFIX_TABLE."image_category`
  DROP INDEX `image_category_i1`
;",

"
ALTER TABLE `".PREFIX_TABLE."image_category`
  ADD INDEX `image_category_i1` (`category_id`)
;",

"
ALTER TABLE `".PREFIX_TABLE."image_category`
  DROP INDEX `image_category_i2`
;",

"
ALTER TABLE `".PREFIX_TABLE."images`
  ADD COLUMN `high_filesize` mediumint(9) unsigned default NULL
;",

"
ALTER TABLE `".PREFIX_TABLE."user_infos`
  CHANGE COLUMN `language`
    `language` varchar(50) NOT NULL default 'en_UK.iso-8859-1'
;",

"
ALTER TABLE `".PREFIX_TABLE."user_infos`
  DROP COLUMN `auto_login_key`
;",

"
ALTER TABLE `".PREFIX_TABLE."user_infos`
  ADD COLUMN `show_nb_hits` enum('true','false') NOT NULL default 'false'
;",

"
ALTER TABLE `".PREFIX_TABLE."user_mail_notification`
  DROP INDEX `uidx_check_key`
;",

"
ALTER TABLE `".PREFIX_TABLE."user_mail_notification`
  ADD UNIQUE `user_mail_notification_ui1` (`check_key`)
;",

"
CREATE TABLE `".PREFIX_TABLE."history_summary` (
  `id` varchar(13) NOT NULL default '',
  `year` smallint(4) NOT NULL default '0',
  `month` tinyint(2) default NULL,
  `day` tinyint(2) default NULL,
  `hour` tinyint(2) default NULL,
  `nb_pages` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM
;",

"
CREATE TABLE `".PREFIX_TABLE."old_permalinks` (
  `cat_id` smallint(5) unsigned NOT NULL default '0',
  `permalink` varchar(64) NOT NULL default '',
  `date_deleted` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_hit` datetime default NULL,
  `hit` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`permalink`)
) ENGINE=MyISAM
;",

"
CREATE TABLE `".PREFIX_TABLE."plugins` (
  `id` varchar(64) binary NOT NULL default '',
  `state` enum('inactive','active') NOT NULL default 'inactive',
  `version` varchar(64) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM
;",

"
CREATE TABLE `".PREFIX_TABLE."user_cache_categories` (
  `user_id` smallint(5) NOT NULL default '0',
  `cat_id` smallint(5) unsigned NOT NULL default '0',
  `max_date_last` datetime default NULL,
  `count_images` mediumint(8) unsigned default '0',
  `count_categories` mediumint(8) unsigned default '0',
  PRIMARY KEY  (`user_id`,`cat_id`)
) ENGINE=MyISAM
;",

/* TABLE DROPPED BEFORE Butterfly/Piwigo release - see later DROP IF EXISTS
"
CREATE TABLE `".PREFIX_TABLE."ws_access` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(32) NOT NULL default '',
  `access` varchar(255) default NULL,
  `start` datetime default NULL,
  `end` datetime default NULL,
  `request` varchar(255) default NULL,
  `limit` smallint(5) unsigned default NULL,
  `comment` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `ws_access_ui1` (`name`)
) ENGINE=MyISAM COMMENT='Access for Web Services'
;",*/

"
INSERT INTO ".PREFIX_TABLE."config
  (param,value,comment)
  VALUES
  ('show_nb_hits', 'false', 'Show hits count under thumbnails')
;",

"
INSERT INTO ".PREFIX_TABLE."config
  (param,value,comment)
  VALUES
  ('history_admin','false','keep a history of administrator visits on your website')
;",

"
INSERT INTO ".PREFIX_TABLE."config
  (param,value,comment)
  VALUES
  ('history_guest','true','keep a history of guest visits on your website')
;",

"
INSERT INTO ".PREFIX_TABLE."config
  (param,value,comment)
  VALUES
  ('allow_user_registration','true','allow visitors to register?')
;",

"
INSERT INTO ".PREFIX_TABLE."config
  (param,value,comment)
  VALUES
  ('secret_key', MD5(RAND()), 'a secret key specific to the gallery for internal use')
;",

"
INSERT INTO ".PREFIX_TABLE."config
  (param,value,comment)
  VALUES
  ('nbm_send_html_mail','true','Send mail on HTML format for notification by mail')
;",

"
INSERT INTO ".PREFIX_TABLE."config
  (param,value,comment)
  VALUES
  ('nbm_send_recent_post_dates','true','Send recent post by dates for notification by mail')
;",

"
INSERT INTO ".PREFIX_TABLE."config
  (param,value,comment)
  VALUES
  ('email_admin_on_new_user','false','Send an email to theadministrators when a user registers')
;",

"
INSERT INTO ".PREFIX_TABLE."config
  (param,value,comment)
  VALUES
  ('email_admin_on_comment','false','Send an email to the administrators when a valid comment is entered')
;",

"
INSERT INTO ".PREFIX_TABLE."config
  (param,value,comment)
  VALUES
  ('email_admin_on_comment_validation','false','Send an email to the administrators when a comment requires validation')
;",

"
INSERT INTO ".PREFIX_TABLE."config
  (param,value,comment)
  VALUES
  ('email_admin_on_picture_uploaded','false','Send an email to the administrators when a picture is uploaded')
;",

"
UPDATE ".PREFIX_TABLE."user_cache
  SET need_update = 'true'
;",

);

foreach ($queries as $query)
{
  pwg_query($query);
}

$replacements = array(
  array('&#039;', '\''),
  array('&quot;', '"'),
  array('&lt;',   '<'),
  array('&gt;',   '>'),
  array('&amp;',  '&') // <- this must be the last one
  );

foreach ($replacements as $replacement)
{
    $query = '
UPDATE '.PREFIX_TABLE.'comments
  SET content = REPLACE(content, "'.
  addslashes($replacement[0]).
  '", "'.
  addslashes($replacement[1]).
  '")
;';
    pwg_query($query);
}

load_conf_from_db();

$query = "
UPDATE ".USER_INFOS_TABLE."
SET
  template = '".$conf['default_template']."',
  nb_image_line = ".$conf['nb_image_line'].",
  nb_line_page = ".$conf['nb_line_page'].",
  language = '".$conf['default_language']."',
  maxwidth = ".
  (empty($conf['default_maxwidth']) ? "NULL" : $conf['default_maxwidth']).
  ",
  maxheight = ".
  (empty($conf['default_maxheight']) ? "NULL" : $conf['default_maxheight']).
  ",
  recent_period = ".$conf['recent_period'].",
  expand = '".boolean_to_string($conf['auto_expand'])."',
  show_nb_comments = '".boolean_to_string($conf['show_nb_comments'])."',
  show_nb_hits = '".boolean_to_string($conf['show_nb_hits'])."',
  enabled_high = '".boolean_to_string(
    (isset($conf['newuser_default_enabled_high']) ?
      $conf['newuser_default_enabled_high'] : true)
    ).
  "'
WHERE
  user_id = ".$conf['default_user_id'].";";
pwg_query($query);

$query = "
DELETE FROM ".CONFIG_TABLE."
WHERE
  param IN
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
)
;";
pwg_query($query);

// now we upgrade from 1.7.0
include_once(PHPWG_ROOT_PATH.'install/upgrade_1.7.0.php');
?>
