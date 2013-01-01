<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2013 Piwigo Team                  http://piwigo.org |
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

/**
 * replace old style #images.keywords by #tags. Requires a big data
 * migration.
 *
 * @return void
 */
function tag_replace_keywords()
{
  // code taken from upgrades 19 and 22
  
  $query = '
CREATE TABLE '.PREFIX_TABLE.'tags (
  id smallint(5) UNSIGNED NOT NULL auto_increment,
  name varchar(255) BINARY NOT NULL,
  url_name varchar(255) BINARY NOT NULL,
  PRIMARY KEY (id)
)
;';
  pwg_query($query);
  
  $query = '
CREATE TABLE '.PREFIX_TABLE.'image_tag (
  image_id mediumint(8) UNSIGNED NOT NULL,
  tag_id smallint(5) UNSIGNED NOT NULL,
  PRIMARY KEY (image_id,tag_id)
)
;';
  pwg_query($query);
  
  //
  // Move keywords to tags
  //

  // each tag label is associated to a numeric identifier
  $tag_id = array();
  // to each tag id (key) a list of image ids (value) is associated
  $tag_images = array();

  $current_id = 1;

  $query = '
SELECT id, keywords
  FROM '.PREFIX_TABLE.'images
  WHERE keywords IS NOT NULL
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    foreach(preg_split('/[,]+/', $row['keywords']) as $keyword)
    {
      if (!isset($tag_id[$keyword]))
      {
        $tag_id[$keyword] = $current_id++;
      }

      if (!isset($tag_images[ $tag_id[$keyword] ]))
      {
        $tag_images[ $tag_id[$keyword] ] = array();
      }

      array_push(
        $tag_images[ $tag_id[$keyword] ],
        $row['id']
        );
    }
  }

  $datas = array();
  foreach ($tag_id as $tag_name => $tag_id)
  {
    array_push(
      $datas,
      array(
        'id'       => $tag_id,
        'name'     => $tag_name,
        'url_name' => str2url($tag_name),
        )
      );
  }
  
  if (!empty($datas))
  {
    mass_inserts(
      PREFIX_TABLE.'tags',
      array_keys($datas[0]),
      $datas
      );
  }

  $datas = array();
  foreach ($tag_images as $tag_id => $images)
  {
    foreach (array_unique($images) as $image_id)
    {
      array_push(
        $datas,
        array(
          'tag_id'   => $tag_id,
          'image_id' => $image_id,
          )
        );
    }
  }
  
  if (!empty($datas))
  {
    mass_inserts(
      PREFIX_TABLE.'image_tag',
      array_keys($datas[0]),
      $datas
      );
  }

  //
  // Delete images.keywords
  //
  $query = '
ALTER TABLE '.PREFIX_TABLE.'images DROP COLUMN keywords
;';
  pwg_query($query);

  //
  // Add useful indexes
  //
  $query = '
ALTER TABLE '.PREFIX_TABLE.'tags
  ADD INDEX tags_i1(url_name)
;';
  pwg_query($query);


  $query = '
ALTER TABLE '.PREFIX_TABLE.'image_tag
  ADD INDEX image_tag_i1(tag_id)
;';
  pwg_query($query);

  // print_time('tags have replaced keywords');
}

tag_replace_keywords();

$queries = array(
  "
CREATE TABLE ".PREFIX_TABLE."search (
  id int UNSIGNED NOT NULL AUTO_INCREMENT,
  last_seen date DEFAULT NULL,
  rules text,
  PRIMARY KEY  (id)
);",

  "
CREATE TABLE ".PREFIX_TABLE."user_mail_notification (
  user_id smallint(5) NOT NULL default '0',
  check_key varchar(16) binary NOT NULL default '',
  enabled enum('true','false') NOT NULL default 'false',
  last_send datetime default NULL,
  PRIMARY KEY  (user_id),
  UNIQUE KEY uidx_check_key (check_key)
);",

  "
CREATE TABLE ".PREFIX_TABLE."upgrade (
  id varchar(20) NOT NULL default '',
  applied datetime NOT NULL default '0000-00-00 00:00:00',
  description varchar(255) default NULL,
  PRIMARY KEY  (`id`)
);",

  "
ALTER TABLE ".PREFIX_TABLE."config
  MODIFY COLUMN value TEXT
;",

  "
ALTER TABLE ".PREFIX_TABLE."images
  ADD COLUMN has_high enum('true') default NULL
;",

  "
ALTER TABLE ".PREFIX_TABLE."rate
  ADD COLUMN anonymous_id varchar(45) NOT NULL default ''
;",
  "
ALTER TABLE ".PREFIX_TABLE."rate
  ADD COLUMN date date NOT NULL default '0000-00-00'
;",
  "
ALTER TABLE ".PREFIX_TABLE."rate
  DROP PRIMARY KEY
;",
  "
ALTER TABLE ".PREFIX_TABLE."rate
  ADD PRIMARY KEY (element_id,user_id,anonymous_id)
;",
  "
UPDATE ".PREFIX_TABLE."rate
  SET date = CURDATE()
;",
  
  "
DELETE
  FROM ".PREFIX_TABLE."sessions
;",
  "
ALTER TABLE ".PREFIX_TABLE."sessions
  DROP COLUMN user_id
;",
  "
ALTER TABLE ".PREFIX_TABLE."sessions
  ADD COLUMN data text NOT NULL
;",
  
  "
ALTER TABLE ".PREFIX_TABLE."user_cache
  ADD COLUMN nb_total_images mediumint(8) unsigned default NULL
;",
  
  "
ALTER TABLE ".PREFIX_TABLE."user_infos
  CHANGE COLUMN status
     status enum('webmaster','admin','normal','generic','guest')
     NOT NULL default 'guest'
;",
  "
UPDATE ".PREFIX_TABLE."user_infos
  SET status = 'normal'
  WHERE status = 'guest'
;",
  "
UPDATE ".PREFIX_TABLE."user_infos
  SET status = 'guest'
  WHERE user_id = ".$conf['guest_id']."
;",
  "
UPDATE ".PREFIX_TABLE."user_infos
  SET status = 'webmaster'
  WHERE user_id = ".$conf['webmaster_id']."
;",

  "
ALTER TABLE ".PREFIX_TABLE."user_infos
   CHANGE COLUMN template template varchar(255) NOT NULL default 'yoga/clear'
;",

  "
UPDATE ".PREFIX_TABLE."user_infos
  SET template = 'yoga/dark'
  WHERE template = 'yoga-dark'
;",
  "
UPDATE ".PREFIX_TABLE."user_infos
  SET template = 'yoga/clear'
  WHERE template != 'yoga/dark'
;",
  "
ALTER TABLE ".PREFIX_TABLE."user_infos
  ADD COLUMN adviser enum('true','false') NOT NULL default 'false'
;",
  "
ALTER TABLE ".PREFIX_TABLE."user_infos
  ADD COLUMN enabled_high enum('true','false') NOT NULL default 'true'
;",
  "
ALTER TABLE ".PREFIX_TABLE."categories
  CHANGE COLUMN rank rank SMALLINT(5) UNSIGNED DEFAULT NULL
;",
  // configuration table
  "
UPDATE ".PREFIX_TABLE."config
  SET value = 'yoga/clear'
  WHERE param = 'default_template'
;"
  );

foreach ($queries as $query)
{
  pwg_query($query);
}

//
// Move rate, rate_anonymous and gallery_url from config file to database
//
$params = array(
  'gallery_url' => array(
    '',
    'Optional alternate homepage for the gallery'
    ),
  'rate' => array(
    'true',
    'Rating pictures feature is enabled'
    ),
  'rate_anonymous' => array(
    'true',
    'Rating pictures feature is also enabled for visitors'
    )
  );
// Get real values from config file
$conf_save = $conf;
unset($conf);
@include(PHPWG_ROOT_PATH. 'local/config/config.inc.php');
if ( isset($conf['gallery_url']) )
{
  $params['gallery_url'][0] = $conf['gallery_url'];
}
if ( isset($conf['rate']) and is_bool($conf['rate']) )
{
  $params['rate'][0] = $conf['rate'] ? 'true' : 'false';
}
if ( isset($conf['rate_anonymous']) and is_bool($conf['rate_anonymous']) )
{
  $params['rate_anonymous'][0] = $conf['rate_anonymous'] ? 'true' : 'false';
}
$conf = $conf_save;

// Do I already have them in DB ?
$query = 'SELECT param FROM '.PREFIX_TABLE.'config';
$result = pwg_query($query);
while ($row = pwg_db_fetch_assoc($result))
{
  unset( $params[ $row['param'] ] );
}

// Perform the insert query
foreach ($params as $param_key => $param_values)
{
  $query = '
INSERT INTO '.PREFIX_TABLE.'config
  (param,value,comment)
  VALUES
 ('."'$param_key','$param_values[0]','$param_values[1]')
;";
  pwg_query($query);
}

$query = "
ALTER TABLE ".PREFIX_TABLE."config MODIFY COLUMN `value` TEXT;";
pwg_query($query);


//
// replace gallery_description by page_banner
//
$query = '
SELECT value
  FROM '.PREFIX_TABLE.'config
  WHERE param=\'gallery_title\'
;';
list($t) = array_from_query($query, 'value');

$query = '
SELECT value
  FROM '.PREFIX_TABLE.'config
  WHERE param=\'gallery_description\'
;';
list($d) = array_from_query($query, 'value');

$page_banner='<h1>'.$t.'</h1><p>'.$d.'</p>';
$page_banner=addslashes($page_banner);
$query = '
INSERT INTO '.PREFIX_TABLE.'config
  (param,value,comment)
  VALUES
  (
    \'page_banner\',
    \''.$page_banner.'\',
    \'html displayed on the top each page of your gallery\'
  )
;';
pwg_query($query);

$query = '
DELETE FROM '.PREFIX_TABLE.'config
  WHERE param=\'gallery_description\'
;';
pwg_query($query);

//
// configuration for notification by mail
//
$query = "
INSERT INTO ".CONFIG_TABLE."
  (param,value,comment)
  VALUES
  (
    'nbm_send_mail_as',
    '',
    'Send mail as param value for notification by mail'
  ),
  (
    'nbm_send_detailed_content',
    'true',
    'Send detailed content for notification by mail'
  ),
  (
    'nbm_complementary_mail_content',
    '',
    'Complementary mail content for notification by mail'
  )
;";
pwg_query($query);

// depending on the way the 1.5.0 was installed (from scratch or by upgrade)
// the database structure has small differences that should be corrected.

$query = '
ALTER TABLE '.PREFIX_TABLE.'users
  CHANGE COLUMN password password varchar(32) default NULL
;';
pwg_query($query);

$to_keep = array('id', 'username', 'password', 'mail_address');
  
$query = '
DESC '.PREFIX_TABLE.'users
;';

$result = pwg_query($query);

while ($row = pwg_db_fetch_assoc($result))
{
  if (!in_array($row['Field'], $to_keep))
  {
    $query = '
ALTER TABLE '.PREFIX_TABLE.'users
  DROP COLUMN '.$row['Field'].'
;';
    pwg_query($query);
  }
}

// now we upgrade from 1.6.0 to 1.6.2
include_once(PHPWG_ROOT_PATH.'install/upgrade_1.6.0.php');
?>
