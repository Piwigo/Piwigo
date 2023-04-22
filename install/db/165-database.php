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

$upgrade_description = 'Add more options to email_admin_on_new_user';

list($old_value) = pwg_db_fetch_row(pwg_query('SELECT value FROM '.PREFIX_TABLE.'config WHERE param = "email_admin_on_new_user"'));

$new_value = 'all';
if ('false' == $old_value)
{
  $new_value = 'none';
}

conf_update_param('email_admin_on_new_user', $new_value);

echo
"\n"
. $upgrade_description
."\n"
;
?>
