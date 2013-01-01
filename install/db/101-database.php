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
  die('Hacking attempt!');
}

$upgrade_description = 'merge nb_line_page and nb_image_line into nb_image_page';

// add column
if ('mysql' == $conf['dblayer'])
{
  pwg_query('
    ALTER TABLE '.USER_INFOS_TABLE.' 
      ADD COLUMN `nb_image_page` smallint(3) unsigned NOT NULL default \'15\'
  ;');
}
else if (in_array($conf['dblayer'], array('pgsql', 'sqlite', 'pdo-sqlite')))
{
  pwg_query('
    ALTER TABLE '.USER_INFOS_TABLE.' 
      ADD COLUMN "nb_image_page" INTEGER default 15 NOT NULL
  ;');
}

// merge datas
pwg_query('
  UPDATE '.USER_INFOS_TABLE.' 
  SET nb_image_page = nb_line_page*nb_image_line
;');

// delete old columns
pwg_query('
  ALTER TABLE '.USER_INFOS_TABLE.' 
    DROP `nb_line_page`,
    DROP `nb_image_line`
;');

echo
"\n"
. $upgrade_description
."\n"
;
?>