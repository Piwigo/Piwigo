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

$upgrade_description = '#config (gallery_description out page_banner in)';

$query = "
ALTER TABLE ".PREFIX_TABLE."config MODIFY COLUMN `value` TEXT;";
pwg_query($query);


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

$page_banner='<div id="theHeader"><h1>'.$t.'</h1><p>'.$d.'</p></div>';
$page_banner=addslashes($page_banner);
$query = '
INSERT INTO '.PREFIX_TABLE.'config (param,value,comment) VALUES (' .
"'page_banner','$page_banner','html displayed on the top each page of your gallery');";
pwg_query($query);

$query = '
DELETE FROM '.PREFIX_TABLE.'config
  WHERE param=\'gallery_description\'
;';
pwg_query($query);


echo
"\n"
.'Table '.PREFIX_TABLE.'config updated'
."\n"
;
?>
