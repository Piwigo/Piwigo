-- MySQL dump 9.11
--
-- Host: localhost    Database: pwg-bsf
-- ------------------------------------------------------
-- Server version	4.0.24_Debian-10-log

--
-- Table structure for table `phpwebgallery_caddie`
--

DROP TABLE IF EXISTS `phpwebgallery_caddie`;
CREATE TABLE `phpwebgallery_caddie` (
  `user_id` smallint(5) NOT NULL default '0',
  `element_id` mediumint(8) NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`element_id`)
) TYPE=MyISAM;

--
-- Table structure for table `phpwebgallery_categories`
--

DROP TABLE IF EXISTS `phpwebgallery_categories`;
CREATE TABLE `phpwebgallery_categories` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `date_last` datetime default NULL,
  `nb_images` mediumint(8) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `id_uppercat` smallint(5) unsigned default NULL,
  `comment` text,
  `dir` varchar(255) default NULL,
  `rank` tinyint(3) unsigned default NULL,
  `status` enum('public','private') NOT NULL default 'public',
  `site_id` tinyint(4) unsigned default '1',
  `visible` enum('true','false') NOT NULL default 'true',
  `uploadable` enum('true','false') NOT NULL default 'false',
  `representative_picture_id` mediumint(8) unsigned default NULL,
  `uppercats` varchar(255) NOT NULL default '',
  `commentable` enum('true','false') NOT NULL default 'true',
  `global_rank` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `categories_i2` (`id_uppercat`)
) TYPE=MyISAM;

--
-- Table structure for table `phpwebgallery_categories_link`
--

DROP TABLE IF EXISTS `phpwebgallery_categories_link`;
CREATE TABLE `phpwebgallery_categories_link` (
  `source` smallint(5) unsigned NOT NULL default '0',
  `destination` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`source`,`destination`)
) TYPE=MyISAM;

--
-- Table structure for table `phpwebgallery_comments`
--

DROP TABLE IF EXISTS `phpwebgallery_comments`;
CREATE TABLE `phpwebgallery_comments` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `image_id` mediumint(8) unsigned NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` varchar(255) default NULL,
  `content` longtext,
  `validated` enum('true','false') NOT NULL default 'false',
  `validation_date` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `comments_i2` (`validation_date`),
  KEY `comments_i1` (`image_id`)
) TYPE=MyISAM;

--
-- Table structure for table `phpwebgallery_config`
--

DROP TABLE IF EXISTS `phpwebgallery_config`;
CREATE TABLE `phpwebgallery_config` (
  `param` varchar(40) NOT NULL default '',
  `value` varchar(255) default NULL,
  `comment` varchar(255) default NULL,
  PRIMARY KEY  (`param`)
) TYPE=MyISAM COMMENT='configuration table';

--
-- Table structure for table `phpwebgallery_favorites`
--

DROP TABLE IF EXISTS `phpwebgallery_favorites`;
CREATE TABLE `phpwebgallery_favorites` (
  `user_id` smallint(5) NOT NULL default '0',
  `image_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`image_id`)
) TYPE=MyISAM;

--
-- Table structure for table `phpwebgallery_group_access`
--

DROP TABLE IF EXISTS `phpwebgallery_group_access`;
CREATE TABLE `phpwebgallery_group_access` (
  `group_id` smallint(5) unsigned NOT NULL default '0',
  `cat_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`cat_id`)
) TYPE=MyISAM;

--
-- Table structure for table `phpwebgallery_groups`
--

DROP TABLE IF EXISTS `phpwebgallery_groups`;
CREATE TABLE `phpwebgallery_groups` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `phpwebgallery_history`
--

DROP TABLE IF EXISTS `phpwebgallery_history`;
CREATE TABLE `phpwebgallery_history` (
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `login` varchar(15) default NULL,
  `IP` varchar(50) NOT NULL default '',
  `category` varchar(150) default NULL,
  `file` varchar(50) default NULL,
  `picture` varchar(150) default NULL,
  KEY `history_i1` (`date`)
) TYPE=MyISAM;

--
-- Table structure for table `phpwebgallery_image_category`
--

DROP TABLE IF EXISTS `phpwebgallery_image_category`;
CREATE TABLE `phpwebgallery_image_category` (
  `image_id` mediumint(8) unsigned NOT NULL default '0',
  `category_id` smallint(5) unsigned NOT NULL default '0',
  `is_storage` enum('true','false') default 'false',
  PRIMARY KEY  (`image_id`,`category_id`),
  KEY `image_category_i1` (`image_id`),
  KEY `image_category_i2` (`category_id`)
) TYPE=MyISAM;

--
-- Table structure for table `phpwebgallery_images`
--

DROP TABLE IF EXISTS `phpwebgallery_images`;
CREATE TABLE `phpwebgallery_images` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `file` varchar(255) NOT NULL default '',
  `date_available` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_creation` date default NULL,
  `tn_ext` varchar(4) default '',
  `name` varchar(255) default NULL,
  `comment` text,
  `author` varchar(255) default NULL,
  `hit` int(10) unsigned NOT NULL default '0',
  `filesize` mediumint(9) unsigned default NULL,
  `width` smallint(9) unsigned default NULL,
  `height` smallint(9) unsigned default NULL,
  `keywords` varchar(255) default NULL,
  `storage_category_id` smallint(5) unsigned default NULL,
  `representative_ext` varchar(4) default NULL,
  `date_metadata_update` date default NULL,
  `average_rate` float(5,2) unsigned default NULL,
  `has_high` enum('true') default NULL,
  `path` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `images_i2` (`date_available`),
  KEY `images_i1` (`storage_category_id`),
  KEY `images_i3` (`average_rate`),
  KEY `images_i4` (`hit`),
  KEY `images_i5` (`date_creation`)
) TYPE=MyISAM;

--
-- Table structure for table `phpwebgallery_rate`
--

DROP TABLE IF EXISTS `phpwebgallery_rate`;
CREATE TABLE `phpwebgallery_rate` (
  `user_id` smallint(5) NOT NULL default '0',
  `element_id` mediumint(8) unsigned NOT NULL default '0',
  `anonymous_id` varchar(45) NOT NULL default '',
  `rate` tinyint(2) unsigned NOT NULL default '0',
  `date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`element_id`,`user_id`,`anonymous_id`)
) TYPE=MyISAM;

--
-- Table structure for table `phpwebgallery_search`
--

DROP TABLE IF EXISTS `phpwebgallery_search`;
CREATE TABLE `phpwebgallery_search` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `last_seen` date default NULL,
  `rules` text,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `phpwebgallery_sessions`
--

DROP TABLE IF EXISTS `phpwebgallery_sessions`;
CREATE TABLE `phpwebgallery_sessions` (
  `id` varchar(255) binary NOT NULL default '',
  `data` text NOT NULL,
  `expiration` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `phpwebgallery_sites`
--

DROP TABLE IF EXISTS `phpwebgallery_sites`;
CREATE TABLE `phpwebgallery_sites` (
  `id` tinyint(4) NOT NULL auto_increment,
  `galleries_url` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `sites_ui1` (`galleries_url`)
) TYPE=MyISAM;

--
-- Table structure for table `phpwebgallery_upgrade`
--

DROP TABLE IF EXISTS `phpwebgallery_upgrade`;
CREATE TABLE `phpwebgallery_upgrade` (
  `id` varchar(20) NOT NULL default '',
  `applied` datetime NOT NULL default '0000-00-00 00:00:00',
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `phpwebgallery_user_access`
--

DROP TABLE IF EXISTS `phpwebgallery_user_access`;
CREATE TABLE `phpwebgallery_user_access` (
  `user_id` smallint(5) NOT NULL default '0',
  `cat_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`cat_id`)
) TYPE=MyISAM;

--
-- Table structure for table `phpwebgallery_user_cache`
--

DROP TABLE IF EXISTS `phpwebgallery_user_cache`;
CREATE TABLE `phpwebgallery_user_cache` (
  `user_id` smallint(5) NOT NULL default '0',
  `need_update` enum('true','false') NOT NULL default 'true',
  `forbidden_categories` text,
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;

--
-- Table structure for table `phpwebgallery_user_feed`
--

DROP TABLE IF EXISTS `phpwebgallery_user_feed`;
CREATE TABLE `phpwebgallery_user_feed` (
  `id` varchar(50) binary NOT NULL default '',
  `user_id` smallint(5) NOT NULL default '0',
  `last_check` datetime default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `phpwebgallery_user_group`
--

DROP TABLE IF EXISTS `phpwebgallery_user_group`;
CREATE TABLE `phpwebgallery_user_group` (
  `user_id` smallint(5) NOT NULL default '0',
  `group_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`user_id`)
) TYPE=MyISAM;

--
-- Table structure for table `phpwebgallery_user_infos`
--

DROP TABLE IF EXISTS `phpwebgallery_user_infos`;
CREATE TABLE `phpwebgallery_user_infos` (
  `user_id` smallint(5) NOT NULL default '0',
  `nb_image_line` tinyint(1) unsigned NOT NULL default '5',
  `nb_line_page` tinyint(3) unsigned NOT NULL default '3',
  `status` enum('admin','guest') NOT NULL default 'guest',
  `language` varchar(50) NOT NULL default 'english',
  `maxwidth` smallint(6) default NULL,
  `maxheight` smallint(6) default NULL,
  `expand` enum('true','false') NOT NULL default 'false',
  `show_nb_comments` enum('true','false') NOT NULL default 'false',
  `recent_period` tinyint(3) unsigned NOT NULL default '7',
  `template` varchar(255) NOT NULL default 'yoga/clear',
  `registration_date` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `user_infos_ui1` (`user_id`)
) TYPE=MyISAM;

--
-- Table structure for table `phpwebgallery_user_mail_notification`
--

DROP TABLE IF EXISTS `phpwebgallery_user_mail_notification`;
CREATE TABLE `phpwebgallery_user_mail_notification` (
  `user_id` smallint(5) NOT NULL default '0',
  `check_key` varchar(128) binary NOT NULL default '',
  `enabled` enum('true','false') NOT NULL default 'false',
  `last_send` datetime default NULL,
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `uidx_check_key` (`check_key`)
) TYPE=MyISAM;

--
-- Table structure for table `phpwebgallery_users`
--

DROP TABLE IF EXISTS `phpwebgallery_users`;
CREATE TABLE `phpwebgallery_users` (
  `id` smallint(5) NOT NULL auto_increment,
  `username` varchar(20) binary NOT NULL default '',
  `password` varchar(32) default NULL,
  `mail_address` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `users_ui1` (`username`)
) TYPE=MyISAM;

--
-- Table structure for table `phpwebgallery_waiting`
--

DROP TABLE IF EXISTS `phpwebgallery_waiting`;
CREATE TABLE `phpwebgallery_waiting` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `storage_category_id` smallint(5) unsigned NOT NULL default '0',
  `file` varchar(255) NOT NULL default '',
  `username` varchar(255) NOT NULL default '',
  `mail_address` varchar(255) NOT NULL default '',
  `date` int(10) unsigned NOT NULL default '0',
  `tn_ext` char(3) default NULL,
  `validated` enum('true','false') NOT NULL default 'false',
  `infos` text,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

