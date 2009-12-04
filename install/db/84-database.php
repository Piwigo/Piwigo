<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
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
  die('Hacking attempt!');
}

$upgrade_description = 'Update configuration filename for database.
configuration variables will change too.';

$old_config_file = PHPWG_ROOT_PATH .'include/mysql.inc.php';
$new_config_file = PHPWG_ROOT_PATH .'include/config_database.inc.php';

include_once $old_config_file;

$file_content = '<?php
$conf[\'dblayer\'] = \'mysql\';
$conf[\'db_base\'] = \''.$cfgBase.'\';
$conf[\'db_user\'] = \''.$cfgUser.'\';
$conf[\'db_password\'] = \''.$cfgPassword.'\';
$conf[\'db_host\'] = \''.$cfgHote.'\';

$prefixeTable = \''.$prefixeTable.'\';

define(\'PHPWG_INSTALLED\', true);
define(\'PWG_CHARSET\', \''.PWG_CHARSET.'\');
define(\'DB_CHARSET\', \''.DB_CHARSET.'\');
define(\'DB_COLLATE\', \''.DB_COLLATE.'\');

?'.'>';

@umask(0111);
// writing the configuration file
if ( !($fp = @fopen( $config_file, 'w' )))
{
  $html_content = htmlentities( $file_content, ENT_QUOTES, 'utf-8' );
  $html_content = nl2br( $html_content );
  $error_copy = l10n('step1_err_copy');
  $error_copy .= '<br>--------------------------------------------------------------------<br>';
  $error_copy .= '<span class="sql_content">' . $html_content . '</span>';
  $error_copy .= '<br>--------------------------------------------------------------------<br>';
}
@fputs($fp, $file_content, strlen($file_content));
@fclose($fp);

if (isset($error_copy)) 
{
  array_push($page['errors'], $error_copy);
}

echo
"\n"
. $upgrade_description
."\n"
;
?>
