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
      if ( preg_match('/(\d{4})(\d{2})(\d{2})/', $value, $matches))
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
    define('CURRENT_DATE', "'".date('Y-m-d')."'");
  }

  $inserts = array();

  foreach ($files as $id => $file)
  {
    $insert = array();
    $insert['id'] = $id;
    $insert['filesize'] = floor(filesize($file)/1024);
  
    if ($image_size = @getimagesize($file))
    {
      $insert['width'] = $image_size[0];
      $insert['height'] = $image_size[1];
    }
  
    if ($conf['use_exif'])
    {
      if ($exif = @read_exif_data($file))
      {
        if (isset($exif['DateTime']))
        {
          preg_match('/^(\d{4}).(\d{2}).(\d{2})/'
                     ,$exif['DateTime']
                     ,$matches);
          $insert['date_creation'] =
            "'".$matches[1].'-'.$matches[2].'-'.$matches[3]."'";
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
          $insert[$key] = "'".addslashes($iptc[$key])."'";
        }
      }
    }

    $insert['date_metadata_update'] = CURRENT_DATE;

    array_push($inserts, $insert);
  }
  
  if (count($inserts) > 0)
  {
    $dbfields = array(
      'id','filesize','width','height','name','author','comment'
      ,'date_creation','keywords','date_metadata_update'
      );

    // depending on the MySQL version, we use the multi table update or N
    // update queries
    $query = 'SELECT VERSION() AS version;';
    $row = mysql_fetch_array(pwg_query($query));
    if (version_compare($row['version'],'4.0.4') < 0)
    {
      // MySQL is prior to version 4.0.4, multi table update feature is not
      // available
      echo 'MySQL is prior to version 4.0.4, multi table update feature is not available<br />';
      foreach ($inserts as $insert)
      {
        $query = '
UPDATE '.IMAGES_TABLE.'
  SET ';
        foreach (array_diff(array_keys($insert),array('id')) as $num => $key)
        {
          if ($num > 1)
          {
            $query.= ', ';
          }
          $query.= $key.' = '.$insert[$key];
        }
        $query.= '
  WHERE id = '.$insert['id'].'
;';
        // echo '<pre>'.$query.'</pre>';
        pwg_query($query);
      }
    }
    else
    {
      // creation of the temporary table
      $query = '
DESCRIBE '.IMAGES_TABLE.'
;';
      $result = pwg_query($query);
      $columns = array();
      while ($row = mysql_fetch_array($result))
      {
        if (in_array($row['Field'], $dbfields))
        {
          $column = $row['Field'];
          $column.= ' '.$row['Type'];
          if (!isset($row['Null']) or $row['Null'] == '')
          {
            $column.= ' NOT NULL';
          }
          if (isset($row['Default']))
          {
            $column.= " default '".$row['Default']."'";
          }
          array_push($columns, $column);
        }
      }
      $query = '
CREATE TEMPORARY TABLE '.IMAGE_METADATA_TABLE.'
(
'.implode(",\n", $columns).',
PRIMARY KEY (id)
)
;';
      // echo '<pre>'.$query.'</pre>';
      pwg_query($query);
      // inserts all found pictures
      $query = '
INSERT INTO '.IMAGE_METADATA_TABLE.'
  ('.implode(',', $dbfields).')
   VALUES
   ';
      foreach ($inserts as $insert_id => $insert)
      {
        $query.= '
';
        if ($insert_id > 0)
        {
          $query.= ',';
        }
        $query.= '(';
        foreach ($dbfields as $field_id => $dbfield)
        {
          if ($field_id > 0)
          {
            $query.= ',';
          }
          
          if (!isset($insert[$dbfield]) or $insert[$dbfield] == '')
          {
            $query.= 'NULL';
          }
          else
          {
            $query.= $insert[$dbfield];
          }
        }
        $query.=')';
      }
      $query.= '
;';
      // echo '<pre>'.$query.'</pre>';
      pwg_query($query);
      // update of images table by joining with temporary table
      $query = '
UPDATE '.IMAGES_TABLE.' AS images, '.IMAGE_METADATA_TABLE.' as metadata
  SET '.implode("\n    , ",
                array_map(
                  create_function('$s', 'return "images.$s = metadata.$s";')
                  , array_diff($dbfields, array('id')))).'
  WHERE images.id = metadata.id
;';
      echo '<pre>'.$query.'</pre>';
      pwg_query($query);
    }
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