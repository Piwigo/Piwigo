<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// | Copyright (C) 2006 Ruben ARNAUD - team@phpwebgallery.net              |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2005-09-21 00:04:57 +0200 (mer, 21 sep 2005) $
// | last modifier : $Author: plg $
// | revision      : $Revision: 870 $
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

$upgrade_description = 'Move rate, rate_anonymous and gallery_url from config file to database';

$params = array(
  'gallery_url' => array('http://demo.phpwebgallery.net','URL given in RSS feed'),
  'rate' => array('true','Rating pictures feature is enabled') ,
  'rate_anonymous' => array('true','Rating pictures feature is also enabled for visitors')
  );



// +-Get real values from config file--------------------------------------+

$conf_save = $conf;
unset($conf);
@include(PHPWG_ROOT_PATH. 'include/config_local.inc.php');
if ( isset($conf['gallery_url']) )
{
  $params['gallery_url'][0] = $conf['gallery_url'];
}
if ( isset($conf['rate']) and is_bool($conf['rate']) )
{
  $params['rate'][0] = $conf['rate'] ? 'true' : 'false';
}
if ( isset($conf['rate_anonymous']) and is_bool($conf['rate_anonymous']) )
{
  $params['rate_anonymous'][0] = $conf['rate_anonymous'] ? 'true' : 'false';
}
$conf = $conf_save;



// +-Do I already have them in DB ?----------------------------------------+
$query = 'SELECT param FROM '.PREFIX_TABLE.'config';
$result = pwg_query($query);
while ($row = mysql_fetch_array($result))
{
  unset( $params[ $row['param'] ] );
}

// +-Perform the insert query----------------------------------------------+
foreach ($params as $param_key => $param_values)
{
  $query = '
INSERT INTO '.PREFIX_TABLE.'config (param,value,comment) VALUES (' .
"'$param_key','$param_values[0]','$param_values[1]');";
  pwg_query($query);
}


echo
"\n"
.'Table '.PREFIX_TABLE.'config upgraded'
."\n"
;
?>
