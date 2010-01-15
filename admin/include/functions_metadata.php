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
        $iptc[$pwg_key] = $matches[1].'-'.$matches[2].'-'.$matches[3];
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
      if (preg_match('/^(\d{4}).(\d{2}).(\d{2})/', $value, $matches))
      {
        $exif[$pwg_key] = $matches[1].'-'.$matches[2].'-'.$matches[3];
      }
    }
    $exif[$pwg_key] = addslashes($exif[$pwg_key]);
  }

  return $exif;
}

function update_metadata($files)
{
  global $conf;

  if (!defined('CURRENT_DATE'))
  {
    define('CURRENT_DATE', date('Y-m-d'));
  }

  $datas = array();
  $tags_of = array();
  $has_high_images = array();

  $image_ids = array();
  foreach ($files as $id => $file)
  {
    array_push($image_ids, $id);
  }

  $query = '
SELECT id
  FROM '.IMAGES_TABLE.'
  WHERE has_high = \'true\'
    AND id IN (
'.wordwrap(implode(', ', $image_ids), 80, "\n").'
)
;';

  $has_high_images = array_from_query($query, 'id');

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

    if (in_array($id, $has_high_images))
    {
      $high_file = dirname($file).'/pwg_high/'.basename($file);

      $data['high_filesize'] = floor(filesize($high_file)/1024);
    }

    if ($conf['use_exif'])
    {
      $exif = get_sync_exif_data($file);
      $data = array_merge($data, $exif);
    }

    if ($conf['use_iptc'])
    {
      $iptc = get_sync_iptc_data($file);
      $data = array_merge($data, $iptc);
      
      if (count($iptc) > 0)
      {
        foreach (array_keys($iptc) as $key)
        {
          if ($key == 'keywords' or $key == 'tags')
          {
            if (!isset($tags_of[$id]))
            {
              $tags_of[$id] = array();
            }

            foreach (explode(',', $iptc[$key]) as $tag_name)
            {
              array_push(
                $tags_of[$id],
                tag_id_from_tag_name($tag_name)
                );
            }
          }
        }
      }
    }

    $data['date_metadata_update'] = CURRENT_DATE;

    array_push($datas, $data);
  }

  if (count($datas) > 0)
  {
    $update_fields =
      array(
        'filesize',
        'width',
        'height',
        'high_filesize',
        'date_metadata_update'
        );

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
          array_diff(
            array_keys($conf['use_iptc_mapping']),
            array('tags', 'keywords')
            )
          );
    }

    mass_updates(
      IMAGES_TABLE,
      array(
        'primary' => array('id'),
        'update'  => array_unique($update_fields)
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

  $files = array();

  $query = '
SELECT id, path
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
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    $files[$row['id']] = $row['path'];
  }

  return $files;
}
?>