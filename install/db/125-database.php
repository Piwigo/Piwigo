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

$upgrade_description = 'derivatives: search and replace hotlinks inside Piwigo (page_banner';

include_once(PHPWG_ROOT_PATH.'include/constants.php');

if (!isset($conf['prefix_thumbnail']))
{
  $conf['prefix_thumbnail'] = 'TN-';
}

if (!isset($conf['dir_thumbnail']))
{
  $conf['dir_thumbnail'] = 'thumbnail';
}

$dbconf = array();
$conf_orig = $conf;
load_conf_from_db();
$dbconf = $conf;
$conf = $conf_orig;

$banner_orig = $dbconf['page_banner'];
$banner_new = replace_hotlinks($dbconf['page_banner']);
if ($banner_orig != $banner_new)
{
  conf_update_param('page_banner', pwg_db_real_escape_string($banner_new));
}

//
// Additional Pages
//
$is_plugin_installed = false;
$plugin_table = $prefixeTable.'additionalpages';

$query = 'SHOW TABLES LIKE \''.$plugin_table.'\';';
$result = pwg_query($query);

while ($row = pwg_db_fetch_row($result))
{
  if ($plugin_table == $row[0])
  {
    $is_plugin_installed = true;
  }
}

if ($is_plugin_installed)
{
  $query = '
SELECT
    id,
    content
  FROM '.$plugin_table.'
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    $content_orig = $row['content'];
    $content_new = replace_hotlinks($content_orig);
    if ($content_orig != $content_new)
    {
      single_update(
        $plugin_table,
        array('content' => pwg_db_real_escape_string($content_new)),
        array('id' => $row['id'])
        );
    }
  }

  $upgrade_description.= ', Additional Pages';
}

//
// PWG Stuffs
// 
$is_plugin_installed = false;
$plugin_table = $prefixeTable.'stuffs';

$query = 'SHOW TABLES LIKE \''.$plugin_table.'\';';
$result = pwg_query($query);

while ($row = pwg_db_fetch_row($result))
{
  if ($plugin_table == $row[0])
  {
    $is_plugin_installed = true;
  }
}

if ($is_plugin_installed)
{
  $query = '
SELECT
    id,
    datas
  FROM '.$plugin_table.'
  WHERE path LIKE \'%plugins/PWG_Stuffs/modules/Personal%\'
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    $content_orig = $row['datas'];
    $content_new = serialize(replace_hotlinks(unserialize($content_orig)));
    if ($content_orig != $content_new)
    {
      single_update(
        $plugin_table,
        array('datas' => pwg_db_real_escape_string($content_new)),
        array('id' => $row['id'])
        );
    }
  }
  
  $upgrade_description.= ', PWG Stuffs';
}

$upgrade_description.= ')';

echo "\n".$upgrade_description."\n";

// +-----------------------------------------------------------------------+
// | Functions                                                             |
// +-----------------------------------------------------------------------+

function replace_hotlinks($string)
{
  global $conf;
  
  // websize 2.3 = medium 2.4
  $string = preg_replace(
    '#(upload/\d{4}/\d{2}/\d{2}/\d{14}-\w{8})(\.(jpg|png))#',
    'i.php?/$1-me$2',
    $string
    );

// I've tried but I didn't find the way to do it correctly
// $string = preg_replace(
//   '#(galleries/.*?/)(?!:(pwg_high|'.$conf['dir_thumbnail'].')/)([^/]*?)(\.[a-z0-9]{3,4})([\'"])#',
//   'i.php?/(1=$1)(2=$2)-me(3=$3)(4=$4)', // 'i.php?/$1$2-me$3',
//   $string
//   );

  // thumbnail 2.3 = th size 2.4
  $string = preg_replace(
    '#(upload/\d{4}/\d{2}/\d{2}/)'.$conf['dir_thumbnail'].'/'.$conf['prefix_thumbnail'].'(\d{14}-\w{8})(\.(jpg|png))#',
    'i.php?/$1$2-th$3',
    $string
    );
  
  $string = preg_replace(
    '#(galleries/.*?/)'.$conf['dir_thumbnail'].'/'.$conf['prefix_thumbnail'].'(.*?)(\.[a-z0-9]{3,4})([\'"])#',
    'i.php?/$1$2-th$3$4',
    $string
    );
  
  // HD 2.3 = original 2.4
  $string = preg_replace(
    '#(upload/\d{4}/\d{2}/\d{2}/)pwg_high/(\d{14}-\w{8}\.(jpg|png))#',
    '$1$2',
    $string
    );

  $string = preg_replace(
    '#(galleries/.*?)/pwg_high(/.*?\.[a-z0-9]{3,4})#',
    '$1$2',
    $string
    );
  
  return $string;
}
?>