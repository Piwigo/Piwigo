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

$upgrade_description = 'add new email features : 
users can modify/delete their owns comments';

$query = '
INSERT INTO '.PREFIX_TABLE.'config (param,value,comment)
  VALUES (\'user_can_delete_comment\',\'false\',
    \'administrators can allow user delete their own comments\'),
  (\'user_can_edit_comment\',\'false\',
    \'administrators can allow user edit their own comments\'),
  (\'email_admin_on_comment_edition\',\'false\',
    \'Send an email to the administrators when a comment is modified\'),
  (\'email_admin_on_comment_deletion\',\'false\',
    \'Send an email to the administrators when a comment is deleted\')
;';
pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>
