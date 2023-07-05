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

$upgrade_description = 'add "email" field in comments table';

include_once(PHPWG_ROOT_PATH.'include/constants.php');

$query = 'ALTER TABLE `'.COMMENTS_TABLE.'` ADD `email` varchar(255) default NULL;';
pwg_query($query);

conf_update_param('comments_author_mandatory', 'false');
conf_update_param('comments_email_mandatory', 'false');

echo "\n".$upgrade_description."\n";

?>