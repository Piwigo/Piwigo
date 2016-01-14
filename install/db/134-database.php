<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2016 Piwigo Team                  http://piwigo.org |
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

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

$upgrade_description = 'replace dblayer to "mysqli" if available.';

global $conf;

$config_file = PHPWG_ROOT_PATH.PWG_LOCAL_DIR .'config/database.inc.php';

if ( extension_loaded('mysqli') and $conf['dblayer']=='mysql' and is_writable($config_file) )
{
  $file_content = file_get_contents($config_file);
  $file_content = preg_replace(
                            '#\$conf\[\'dblayer\'\]( *)=( *)\'mysql\';#', 
                            '$conf[\'dblayer\']$1=$2\'mysqli\';', 
                            $file_content);
  file_put_contents($config_file, $file_content);
}

echo "\n".$upgrade_description."\n";
?>