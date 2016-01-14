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

$upgrade_description = 'Move "gallery_url" parameter from config table to local configuration file';

include_once(PHPWG_ROOT_PATH.'include/constants.php');

if (!isset($page))
{
  $page = array();
}

if (!isset($page['errors']))
{
  $page['errors'] = array();
}

$query = '
SELECT
    value
  FROM '.CONFIG_TABLE.'
  WHERE param =\'gallery_url\'
;';
list($gallery_url) = pwg_db_fetch_row(pwg_query($query));

if (!empty($gallery_url))
{
  // let's try to write it in the local configuration file
  $local_conf = PHPWG_ROOT_PATH. 'local/config/config.inc.php';
  if (isset($conf['local_dir_site']))
  {
    $local_conf = PHPWG_ROOT_PATH.PWG_LOCAL_DIR.'config/config.inc.php';
  }

  $conf_line = '$conf[\'gallery_url\'] = \''.$gallery_url.'\';';

  if (!is_file($local_conf))
  {
    $config_file_contents_new = "<?php\n".$conf_line."\n?>";
  }
  else
  {
    // we have to update the local conf
    $config_file_contents = @file_get_contents($local_conf);
    if ($config_file_contents === false)
    {
      $error = 'Cannot load '.$local_conf.', add by hand: '.$conf_line;
      
      array_push($page['errors'], $error);
      echo $error;
    }
    else
    {
      $php_end_tag = strrpos($config_file_contents, '?'.'>');
      if ($php_end_tag === false)
      {
        // the file is empty
        $config_file_contents_new = "<?php\n".$conf_line."\n?>";
      }
      else
      {
        $config_file_contents_new =
          substr($config_file_contents, 0, $php_end_tag) . "\n"
          .$conf_line."\n"
          .substr($config_file_contents, $php_end_tag)
          ;
      }
    }
  }

  if (isset($config_file_contents_new))
  {
    if (!@file_put_contents($local_conf, $config_file_contents_new))
    {
      $error = 'Cannot write into local configuration file '.$local_conf.', add by hand: '.$conf_line;
      
      array_push($page['errors'], $error);
      echo $error;
    }
  }
}

$query = '
DELETE
  FROM '.CONFIG_TABLE.'
  WHERE param =\'gallery_url\'
;';
pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>