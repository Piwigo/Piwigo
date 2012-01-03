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

include_once(PHPWG_ROOT_PATH.'/include/functions_metadata.inc.php');


function get_sync_iptc_data($file)
{
  global $conf;

  $map = $conf['use_iptc_mapping'];

  $iptc = get_iptc_data($file, $map);

  foreach ($iptc as $pwg_key => $value)
  {
    if (in_array($pwg_key, array('date_creation', 'date_available')))
    {
      if (preg_match('/(\d{4})(\d{2})(\d{2})/', $value, $matches))
      {
        $year = $matches[1];
        $month = $matches[2];
        $day = $matches[3];

        if (!checkdate($month, $day, $year))
        {
          // we suppose the year is correct
          $month = 1;
          $day = 1;
        }

        $iptc[$pwg_key] = $year.'-'.$month.'-'.$day;
      }
    }
  }

  if (isset($iptc['keywords']))
  {
    // official keywords separator is the comma
    $iptc['keywords'] = preg_replace('/[.;]/', ',', $iptc['keywords']);
    $iptc['keywords'] = preg_replace('/^,+|,+$/', '', $iptc['keywords']);

    $iptc['keywords'] = implode(
      ',',
      array_unique(
        explode(
          ',',
          $iptc['keywords']
          )
        )
      );
  }

  foreach ($iptc as $pwg_key => $value)
  {
    $iptc[$pwg_key] = addslashes($iptc[$pwg_key]);
  }

  return $iptc;
}

function get_sync_exif_data($file)
{
  global $conf;

  $exif = get_exif_data($file, $conf['use_exif_mapping']);

  foreach ($exif as $pwg_key => $value)
  {
    if (in_array($pwg_key, array('date_creation', 'date_available')))
    {
      if (preg_match('/^(\d{4}).(\d{2}).(\d{2}) (\d{2}).(\d{2}).(\d{2})/', $value, $matches))
      {
        $exif[$pwg_key] = $matches[1].'-'.$matches[2].'-'.$matches[3].' '.$matches[4].':'.$matches[5].':'.$matches[6];
      }
      elseif (preg_match('/^(\d{4}).(\d{2}).(\d{2})/', $value, $matches))
      {
        $exif[$pwg_key] = $matches[1].'-'.$matches[2].'-'.$matches[3];
      }
      else
      {
        unset($exif[$pwg_key]);
        continue;
      }
    }
    $exif[$pwg_key] = addslashes($exif[$pwg_key]);
  }

  return $exif;
}


function get_sync_metadata_attributes()
{
  global $conf;

  $update_fields = array('filesize', 'width', 'height');

  if ($conf['use_exif'])
  {
    $update_fields =
      array_merge(
        $update_fields,
        array_keys($conf['use_exif_mapping'])
        );
  }

  if ($conf['use_iptc'])
  {
    $update_fields =
      array_merge(
        $update_fields,
        array_keys($conf['use_iptc_mapping'])
        );
  }

  return array_unique($update_fields);
}

function get_sync_metadata($infos)
{
  global $conf;
  $file = PHPWG_ROOT_PATH.$infos['path'];
  $fs = @filesize($file);

  if ($fs===false)
  {
    return false;
  }

  $infos['filesize'] = floor($fs/1024);

  if (isset($infos['representative_ext']))
  {
    $file = original_to_representative($file, $infos['representative_ext']);
  }

  if ($image_size = @getimagesize($file))
  {
    $infos['width'] = $image_size[0];
    $infos['height'] = $image_size[1];
  }

  if ($conf['use_exif'])
  {
    $exif = get_sync_exif_data($file);
    $infos = array_merge($infos, $exif);
  }

  if ($conf['use_iptc'])
  {
    $iptc = get_sync_iptc_data($file);
    $infos = array_merge($infos, $iptc);
  }

  return $infos;
}


function sync_metadata($ids)
{
  global $conf;

  if (!defined('CURRENT_DATE'))
  {
    define('CURRENT_DATE', date('Y-m-d'));
  }

  $datas = array();
  $tags_of = array();

  $query = '
SELECT id, path, representative_ext
  FROM '.IMAGES_TABLE.'
  WHERE id IN (
'.wordwrap(implode(', ', $ids), 160, "\n").'
)
;';

  $result = pwg_query($query);
  while ($data = pwg_db_fetch_assoc($result))
  {
    $data = get_sync_metadata($data);
    if ($data === false)
    {
      continue;
    }

    $id = $data['id'];
    foreach (array('keywords', 'tags') as $key)
    {
      if (isset($data[$key]))
      {
        if (!isset($tags_of[$id]))
        {
          $tags_of[$id] = array();
        }

        foreach (explode(',', $data[$key]) as $tag_name)
        {
          array_push(
            $tags_of[$id],
            tag_id_from_tag_name($tag_name)
            );
        }
      }
    }

    $data['date_metadata_update'] = CURRENT_DATE;

    array_push($datas, $data);
  }

  if (count($datas) > 0)
  {
    $update_fields = get_sync_metadata_attributes();
    array_push($update_fields, 'date_metadata_update');

    $update_fields = array_diff(
      $update_fields,
      array('tags', 'keywords')
            );

    mass_updates(
      IMAGES_TABLE,
      array(
        'primary' => array('id'),
        'update'  => $update_fields
        ),
      $datas,
      MASS_UPDATES_SKIP_EMPTY
      );
  }

  set_tags_of($tags_of);
}

/**
 * returns an array associating element id (images.id) with its complete
 * path in the filesystem
 *
 * @param int id_uppercat
 * @param int site_id
 * @param boolean recursive ?
 * @param boolean only newly added files ?
 * @return array
 */
function get_filelist($category_id = '', $site_id=1, $recursive = false,
                      $only_new = false)
{
  // filling $cat_ids : all categories required
  $cat_ids = array();

  $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE site_id = '.$site_id.'
    AND dir IS NOT NULL';
  if (is_numeric($category_id))
  {
    if ($recursive)
    {
      $query.= '
    AND uppercats '.DB_REGEX_OPERATOR.' \'(^|,)'.$category_id.'(,|$)\'
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
  while ($row = pwg_db_fetch_assoc($result))
  {
    array_push($cat_ids, $row['id']);
  }

  if (count($cat_ids) == 0)
  {
    return array();
  }

  $query = '
SELECT id, path, representative_ext
  FROM '.IMAGES_TABLE.'
  WHERE storage_category_id IN ('.implode(',', $cat_ids).')';
  if ($only_new)
  {
    $query.= '
    AND date_metadata_update IS NULL
';
  }
  $query.= '
;';
  return hash_from_query($query, 'id');
}
?>