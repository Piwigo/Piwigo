<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
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

$upgrade_description = 'add order parameters to bdd #2';

$order_regex = '#^(( *)(id|file|name|date_available|date_creation|hit|average_rate|comment|author|filesize|width|height|high_filesize|high_width|high_height|rank) (ASC|DESC),{1}){1,}$#';

// local file is writable
if ( is_writable($local_file = PHPWG_ROOT_PATH. 'local/config/config.inc.php') )
{
  $order_by = str_ireplace(
    array('order by ', 'asc', 'desc'),
    array(null, 'ASC', 'DESC'),
    trim($conf['order_by_inside_category'])
    );
    
  // for a simple patern
  if ( preg_match($order_regex, $order_by.',') )
  {
    $order_by = 'ORDER BY '.$order_by;
    // update database
    $query = '
    UPDATE '.PREFIX_TABLE.'config
      SET value = \''.preg_replace('# rank (ASC|DESC)(,?)#', null, $order_by).'\'
      WHERE param = \'order_by\'
    ;';
    pwg_query($query);
    $query = '
    UPDATE '.PREFIX_TABLE.'config
      SET value = \''.$order_by.'\'
      WHERE param = \'order_by_inside_category\'
    ';
    pwg_query($query);
    
    // update local file (delete lines)
    $local_config = file($local_file);
    $new_local_config = array();
    foreach ($local_config as $line)
    {
      if (strpos($line, 'order_by') === false)
      {
        $new_local_config[] = $line;
      }
    }
    var_dump($new_local_config);
    file_put_contents(
      $local_file,
      implode("", $new_local_config)
      );
  }
  // for a complex patern
  else
  {
    // update database with default param
    $query = '
    UPDATE '.PREFIX_TABLE.'config
      SET value = \'ORDER BY date_available DESC, file ASC, id ASC\'
      WHERE param = \'order_by\'
    ;';
    pwg_query($query);
    $query = '
    UPDATE '.PREFIX_TABLE.'config
      SET value = \'ORDER BY date_available DESC, file ASC, id ASC\'
      WHERE param = \'order_by_inside_category\'
    ';
    pwg_query($query);
    
    // update local file (rename lines)
    $local_config = file_get_contents($local_file);
    $new_local_config = str_replace(
      array("['order_by']", "['order_by_inside_category']"),
      array("['order_by_custom']", "['order_by_inside_category_custom']"),
      $local_config
      );
    file_put_contents($local_file, $new_local_config);
  }
  
}
// local file is locked
else
{
  // update database with default param
  $query = '
  UPDATE '.PREFIX_TABLE.'config
    SET value = \'ORDER BY date_available DESC, file ASC, id ASC\'
    WHERE param = \'order_by\'
  ;';
  pwg_query($query);
  $query = '
  UPDATE '.PREFIX_TABLE.'config
    SET value = \'ORDER BY date_available DESC, file ASC, id ASC\'
    WHERE param = \'order_by_inside_category\'
  ';
  pwg_query($query);
}

echo
"\n"
. $upgrade_description
."\n"
;
?>