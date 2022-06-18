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

$upgrade_description = 'Add a table to manage languages.';

$query = "
CREATE TABLE ".PREFIX_TABLE."languages (
  `id` varchar(64) NOT NULL default '',
  `version` varchar(64) NOT NULL default '0',
  `name` varchar(64) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM";

if (DB_CHARSET == 'utf8')
{
  $query .= " DEFAULT CHARACTER SET utf8";
}

pwg_query($query);

// Fill table
include_once(PHPWG_ROOT_PATH.'include/constants.php');
include_once(PHPWG_ROOT_PATH.'admin/include/languages.class.php');

$languages = new languages(PWG_CHARSET);

foreach ($languages->fs_languages as $language_code => $fs_language)
{
  $languages->perform_action('activate', $language_code);
}

echo
"\n"
. $upgrade_description
."\n"
;
?>
