-- MySQL dump 8.21
--
-- Host: localhost    Database: devel
---------------------------------------------------------
-- Server version	3.23.49-log

--
-- Table structure for table 'phpwebgallery_categories'
--

DROP TABLE IF EXISTS phpwebgallery_categories;
CREATE TABLE phpwebgallery_categories (
  id smallint(5) unsigned NOT NULL auto_increment,
  date_last date default NULL,
  nb_images mediumint(8) unsigned NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  id_uppercat smallint(5) unsigned default NULL,
  comment text,
  dir varchar(255) default NULL,
  rank tinyint(3) unsigned default NULL,
  status enum('public','private') NOT NULL default 'public',
  site_id tinyint(4) unsigned NOT NULL default '1',
  visible enum('true','false') NOT NULL default 'true',
  uploadable enum('true','false') NOT NULL default 'false',
  representative_picture_id mediumint(8) unsigned default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table 'phpwebgallery_comments'
--

DROP TABLE IF EXISTS phpwebgallery_comments;
CREATE TABLE phpwebgallery_comments (
  id int(11) unsigned NOT NULL auto_increment,
  image_id mediumint(8) unsigned NOT NULL default '0',
  date int(11) unsigned NOT NULL default '0',
  author varchar(255) default NULL,
  content longtext,
  validated enum('true','false') NOT NULL default 'false',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table 'phpwebgallery_config'
--

DROP TABLE IF EXISTS phpwebgallery_config;
CREATE TABLE phpwebgallery_config (
  prefix_thumbnail varchar(10) default 'TN-',
  webmaster varchar(255) NOT NULL default '',
  mail_webmaster varchar(255) NOT NULL default '',
  access enum('free','restricted') default 'free',
  session_id_size tinyint(3) unsigned NOT NULL default '4',
  session_keyword varchar(255) default NULL,
  session_time tinyint(3) unsigned NOT NULL default '30',
  max_user_listbox tinyint(3) unsigned NOT NULL default '10',
  show_comments enum('true','false') NOT NULL default 'true',
  nb_comment_page tinyint(4) NOT NULL default '10',
  upload_available enum('true','false') NOT NULL default 'false',
  upload_maxfilesize smallint(5) unsigned NOT NULL default '150',
  upload_maxwidth smallint(5) unsigned NOT NULL default '800',
  upload_maxheight smallint(5) unsigned NOT NULL default '600',
  upload_maxwidth_thumbnail smallint(5) unsigned NOT NULL default '150',
  upload_maxheight_thumbnail smallint(5) unsigned NOT NULL default '100',
  log enum('true','false') NOT NULL default 'false',
  comments_validation enum('true','false') NOT NULL default 'false',
  comments_forall enum('true','false') NOT NULL default 'false',
  authorize_cookies enum('true','false') NOT NULL default 'false',
  mail_notification enum('true','false') NOT NULL default 'false'
) TYPE=MyISAM;

--
-- Table structure for table 'phpwebgallery_favorites'
--

DROP TABLE IF EXISTS phpwebgallery_favorites;
CREATE TABLE phpwebgallery_favorites (
  user_id smallint(5) unsigned NOT NULL default '0',
  image_id mediumint(8) unsigned NOT NULL default '0',
  KEY user_id (user_id,image_id)
) TYPE=MyISAM;

--
-- Table structure for table 'phpwebgallery_group_access'
--

DROP TABLE IF EXISTS phpwebgallery_group_access;
CREATE TABLE phpwebgallery_group_access (
  group_id smallint(5) unsigned NOT NULL default '0',
  cat_id smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (group_id,cat_id)
) TYPE=MyISAM;

--
-- Table structure for table 'phpwebgallery_groups'
--

DROP TABLE IF EXISTS phpwebgallery_groups;
CREATE TABLE phpwebgallery_groups (
  id smallint(5) unsigned NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table 'phpwebgallery_history'
--

DROP TABLE IF EXISTS phpwebgallery_history;
CREATE TABLE phpwebgallery_history (
  date int(11) NOT NULL default '0',
  login varchar(15) default NULL,
  IP varchar(50) NOT NULL default '',
  category varchar(150) default NULL,
  file varchar(50) default NULL,
  picture varchar(150) default NULL
) TYPE=MyISAM;

--
-- Table structure for table 'phpwebgallery_image_category'
--

DROP TABLE IF EXISTS phpwebgallery_image_category;
CREATE TABLE phpwebgallery_image_category (
  image_id mediumint(8) unsigned NOT NULL default '0',
  category_id smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (image_id,category_id)
) TYPE=MyISAM;

--
-- Table structure for table 'phpwebgallery_images'
--

DROP TABLE IF EXISTS phpwebgallery_images;
CREATE TABLE phpwebgallery_images (
  id mediumint(8) unsigned NOT NULL auto_increment,
  file varchar(255) NOT NULL default '',
  date_available date NOT NULL default '0000-00-00',
  date_creation date default NULL,
  tn_ext char(3) NOT NULL default 'jpg',
  name varchar(255) default NULL,
  comment text,
  author varchar(255) default NULL,
  hit int(10) unsigned NOT NULL default '0',
  filesize mediumint(9) unsigned default NULL,
  width smallint(9) unsigned default NULL,
  height smallint(9) unsigned default NULL,
  keywords varchar(255) default NULL,
  storage_category_id smallint(5) unsigned default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table 'phpwebgallery_sessions'
--

DROP TABLE IF EXISTS phpwebgallery_sessions;
CREATE TABLE phpwebgallery_sessions (
  id varchar(255) binary NOT NULL default '',
  user_id smallint(5) unsigned NOT NULL default '0',
  expiration int(10) unsigned NOT NULL default '0',
  ip varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table 'phpwebgallery_sites'
--

DROP TABLE IF EXISTS phpwebgallery_sites;
CREATE TABLE phpwebgallery_sites (
  id tinyint(4) NOT NULL auto_increment,
  galleries_url varchar(255) NOT NULL default '',
  PRIMARY KEY  (id),
  UNIQUE KEY galleries_url (galleries_url)
) TYPE=MyISAM;

--
-- Table structure for table 'phpwebgallery_user_access'
--

DROP TABLE IF EXISTS phpwebgallery_user_access;
CREATE TABLE phpwebgallery_user_access (
  user_id smallint(5) unsigned NOT NULL default '0',
  cat_id smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (user_id,cat_id)
) TYPE=MyISAM;

--
-- Table structure for table 'phpwebgallery_user_group'
--

DROP TABLE IF EXISTS phpwebgallery_user_group;
CREATE TABLE phpwebgallery_user_group (
  user_id smallint(5) unsigned NOT NULL default '0',
  group_id smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (group_id,user_id)
) TYPE=MyISAM;

--
-- Table structure for table 'phpwebgallery_users'
--

DROP TABLE IF EXISTS phpwebgallery_users;
CREATE TABLE phpwebgallery_users (
  id smallint(5) unsigned NOT NULL auto_increment,
  username varchar(20) binary NOT NULL default '',
  password varchar(255) NOT NULL default '',
  mail_address varchar(255) default NULL,
  nb_image_line tinyint(1) unsigned NOT NULL default '5',
  nb_line_page tinyint(3) unsigned NOT NULL default '3',
  status enum('admin','guest') NOT NULL default 'guest',
  language varchar(50) NOT NULL default 'english',
  maxwidth smallint(6) default NULL,
  maxheight smallint(6) default NULL,
  expand enum('true','false') NOT NULL default 'false',
  show_nb_comments enum('true','false') NOT NULL default 'false',
  short_period tinyint(3) unsigned NOT NULL default '7',
  long_period tinyint(3) unsigned NOT NULL default '14',
  template varchar(255) NOT NULL default 'default',
  PRIMARY KEY  (id),
  UNIQUE KEY pseudo (username)
) TYPE=MyISAM;

--
-- Table structure for table 'phpwebgallery_waiting'
--

DROP TABLE IF EXISTS phpwebgallery_waiting;
CREATE TABLE phpwebgallery_waiting (
  id int(10) unsigned NOT NULL auto_increment,
  storage_category_id smallint(5) unsigned NOT NULL default '0',
  file varchar(255) NOT NULL default '',
  username varchar(255) NOT NULL default '',
  mail_address varchar(255) NOT NULL default '',
  date int(10) unsigned NOT NULL default '0',
  tn_ext char(3) default NULL,
  validated enum('true','false') NOT NULL default 'false',
  infos text,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

