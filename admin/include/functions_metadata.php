<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2004 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
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

include_once(PHPWG_ROOT_PATH.'/include/functions_metadata.inc.php');

function get_sync_iptc_data($file)
{
  global $conf;
  
  $map = $conf['use_iptc_mapping'];
  $datefields = array('date_creation', 'date_available');
  
  $iptc = get_iptc_data($file, $map);

  foreach ($iptc as $pwg_key => $value)
  {
    if (in_array($pwg_key, $datefields))
    {
      if (preg_match('/(\d{4})(\d{2})(\d{2})/', $value, $matches))
      {
        $iptc[$pwg_key] = $matches[1].'-'.$matches[2].'-'.$matches[3];
      }
    }
  }

  if (isset($iptc['keywords']))
  {
    // keywords separator is the comma, nothing else. Allowed characters in
    // keywords : [A-Za-z0-9], "-" and "_". All other characters will be
    // considered as separators
    $iptc['keywords'] = preg_replace('/[^\w-]+/', ',', $iptc['keywords']);
    $iptc['keywords'] = preg_replace('/^,+|,+$/', '', $iptc['keywords']);
  }

  return $iptc;
}

function update_metadata($files)
{
  global $conf;

  if (!defined('CURRENT_DATE'))
  {
    define('CURRENT_DATE', date('Y-m-d'));
  }

  $datas = array();

  foreach ($files as $id => $file)
  {
    $data = array();
    $data['id'] = $id;
    $data['filesize'] = floor(filesize($file)/1024);
  
    if ($image_size = @getimagesize($file))
    {
      $data['width'] = $image_size[0];
      $data['height'] = $image_size[1];
    }
  
    if ($conf['use_exif'])
    {
      if ($exif = @read_exif_data($file))
      {
        if (isset($exif['DateTime']))
        {
          preg_match('/^(\d{4}).(\d{2}).(\d{2})/',$exif['DateTime'],$matches);
          $data['date_creation'] = $matches[1].'-'.$matches[2].'-'.$matches[3];
        }
      }
    }

    if ($conf['use_iptc'])
    {
      $iptc = get_sync_iptc_data($file);
      if (count($iptc) > 0)
      {
        foreach (array_keys($iptc) as $key)
        {
          $data[$key] = "'".addslashes($iptc[$key])."'";
        }
      }
    }

    $data['date_metadata_update'] = CURRENT_DATE;

    array_push($datas, $data);
  }
  
  if (count($datas) > 0)
  {
    $fields = array('primary' => array('id'),
                    'update'  => array('filesize','width','height','name',
                                       'author','comment','date_creation',
                                       'keywords','date_metadata_update'));
    mass_updates(IMAGES_TABLE, $fields, $datas);
  }
}

/**
 * returns an array associating element id (images.id) with its complete
 * path in the filesystem
 *
 * @param int id_uppercat
 * @param boolean recursive ?
 * @param boolean only newly added files ?
 * @return array
 */
function get_filelist($category_id = '', $recursive = false, $only_new = false)
{
  $files = array();

  $query = '
SELECT id, dir
  FROM '.CATEGORIES_TABLE.'
  WHERE dir IS NOT NULL
;';
  $result = pwg_query($query);
  $cat_dirs = array();
  while ($row = mysql_fetch_array($result))
  {
    $cat_dirs[$row['id']] = $row['dir'];
  }

  // filling $uppercats_array : to each category id the uppercats list is
  // associated
  $uppercats_array = array();
  
  $query = '
SELECT id, uppercats
  FROM '.CATEGORIES_TABLE.'
  WHERE site_id = 1
    AND dir IS NOT NULL';
  if (is_numeric($category_id))
  {
    if ($recursive)
    {
      $query.= '
    AND uppercats REGEXP \'(^|,)'.$category_id.'(,|$)\'
';
    }
    else
    {
      $query.= '
    AND id = '.$category_id.'
';
    }
  }
  $query.= '
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    $uppercats_array[$row['id']] =  $row['uppercats'];
  }

  if (count($uppercats_array) == 0)
  {
    return array();
  }

  $query = '
SELECT galleries_url
  FROM '.SITES_TABLE.'
  WHERE id = 1
';
  $row = mysql_fetch_array(pwg_query($query));
  $basedir = $row['galleries_url'];
  
  // filling $cat_fulldirs
  $cat_fulldirs = array();
  foreach ($uppercats_array as $cat_id => $uppercats)
  {
    $uppercats = str_replace(',', '/', $uppercats);
    $cat_fulldirs[$cat_id] = $basedir.preg_replace('/(\d+)/e',
                                                   "\$cat_dirs['$1']",
                                                   $uppercats);
  }

  $query = '
SELECT id, file, storage_category_id
  FROM '.IMAGES_TABLE.'
  WHERE storage_category_id IN ('.implode(','
                                          ,array_keys($uppercats_array)).')';
  if ($only_new)
  {
    $query.= '
    AND date_metadata_update IS NULL
';
  }
  $query.= '
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    $files[$row['id']]
      = $cat_fulldirs[$row['storage_category_id']].'/'.$row['file'];
  }
  
  return $files;
}
?>