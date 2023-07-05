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

$upgrade_description = 'makes sure default user has a theme and a language';

$query = '
SELECT
    theme,
    language
  FROM '.USER_INFOS_TABLE.'
  WHERE user_id = '.$conf['default_user_id'].'
;';
$result = pwg_query($query);
list($theme, $language) = pwg_db_fetch_row($result);

$data = array(
  'user_id' => $conf['default_user_id'],
  'theme' => empty($theme) ? 'Sylvia' : $theme,
  'language' => empty($language) ? 'en_UK' : $language,
  );

mass_updates(
  USER_INFOS_TABLE,
  array(
    'primary' => array('user_id'),
    'update'  => array('theme', 'language')
    ),
  array(
    $data
    )
  );

echo
"\n"
. $upgrade_description
."\n"
;
?>