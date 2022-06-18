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

$upgrade_description = 'Update column save author_id with value.';

$query = '
UPDATE
  '.COMMENTS_TABLE.' AS c ,
  '.USERS_TABLE.' AS u,
  '.USER_INFOS_TABLE.' AS i
SET c.author_id = u.'.$conf['user_fields']['id'].'
WHERE
    c.author_id is null
AND c.author = u.'.$conf['user_fields']['username'].' 
AND u.'.$conf['user_fields']['id'].' = i.user_id
AND i.registration_date <= c.date
;';

pwg_query($query);

$query = '
UPDATE '.COMMENTS_TABLE.' AS c 
SET c.author_id = '.$conf['guest_id'].'
WHERE c.author_id is null
;';

pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>
