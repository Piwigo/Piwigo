<?php
// +-----------------------------------------------------------------------+
// |                        create_listing_file.php                        |
// +-----------------------------------------------------------------------+
// | application   : PhpWebGallery <http://phpwebgallery.net>              |
// | branch        : BSF (Best So Far)                                     |
// +-----------------------------------------------------------------------+
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

// +-----------------------------------------------------------------------+
// |                              parameters                               |
// +-----------------------------------------------------------------------+

// prefix for thumbnails in "thumbnail" sub directories
$conf['prefix_thumbnail'] = 'TN-';

// $conf['file_ext'] lists all extensions (case insensitive) allowed for
// your PhpWebGallery installation
$conf['file_ext'] = array('jpg','JPG','png','PNG','gif','GIF','mpg','zip',
                          'avi','mp3','ogg');

// $conf['picture_ext'] must be a subset of $conf['file_ext']
$conf['picture_ext'] = array('jpg','JPG','png','PNG','gif','GIF');

// $conf['version'] is used to verify the compatibility of the generated
// listing.xml file and the PhpWebGallery version you're running
$conf['version'] = 'BSF';

// $conf['use_exif'] set to true if you want to use Exif Date as "creation
// date" for the element, otherwise, set to false
$conf['use_exif'] = true;

// $conf['use_iptc'] set to true if you want to use IPTC informations of the
// element according to get_sync_iptc_data function mapping, otherwise, set
// to false
$conf['use_iptc'] = false;

// +-----------------------------------------------------------------------+
// |                               functions                               |
// +-----------------------------------------------------------------------+

/**
 * returns informations from IPTC metadata, mapping is done at the beginning
 * of the function
 *
 * @param string $filename
 * @return array
 */
function get_iptc_data($filename, $map)
{
  $result = array();
  
  // Read IPTC data
  $iptc = array();
  
  $imginfo = array();
  getimagesize($filename, $imginfo);
  
  if (isset($imginfo['APP13']))
  {
    $iptc = iptcparse($imginfo['APP13']);
    if (is_array($iptc))
    {
      $rmap = array_flip($map);
      foreach (array_keys($rmap) as $iptc_key)
      {
        if (isset($iptc[$iptc_key][0]) and $value = $iptc[$iptc_key][0])
        {
          // strip leading zeros (weird Kodak Scanner software)
          while ($value[0] == chr(0))
          {
            $value = substr($value, 1);
          }
          // remove binary nulls
          $value = str_replace(chr(0x00), ' ', $value);
          
          foreach (array_keys($map, $iptc_key) as $pwg_key)
          {
            $result[$pwg_key] = $value;
          }
        }
      }
    }
  }
  return $result;
}

function get_sync_iptc_data($file)
{
  $map = array(
    'keywords'        => '2#025',
    'date_creation'   => '2#055',
    'author'          => '2#122',
    'name'            => '2#085',
    'comment'         => '2#120'
    );
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

/**
 * returns a float value coresponding to the number of seconds since the
 * unix epoch (1st January 1970) and the microseconds are precised :
 * e.g. 1052343429.89276600
 *
 * @return float
 */
function get_moment()
{
  $t1 = explode(' ', microtime());
  $t2 = explode('.', $t1[0]);
  $t2 = $t1[1].'.'.$t2[1];
  return $t2;
}

/**
 * returns the number of seconds (with 3 decimals precision) between the
 * start time and the end time given.
 *
 * @param float start
 * @param float end
 * @return void
 */
function get_elapsed_time($start, $end)
{
  return number_format($end - $start, 3, '.', ' ').' s';
}

/**
 * returns an array with all picture files according to $conf['file_ext']
 *
 * @param string $dir
 * @return array
 */
function get_pwg_files($dir)
{
  global $conf;

  $pictures = array();
  if ($opendir = opendir($dir))
  {
    while ($file = readdir($opendir))
    {
      if (in_array(get_extension($file), $conf['file_ext']))
      {
        array_push($pictures, $file);
        if (!preg_match('/^[a-zA-Z0-9-_.]+$/', $file))
        {
          echo 'PWG-WARNING-2: "'.$file.'" : ';
          echo 'The name of the file should be composed of ';
          echo 'letters, figures, "-", "_" or "." ONLY';
          echo "\n";
        }
      }
    }
  }
  return $pictures;
}

/**
 * returns an array with all thumbnails according to $conf['picture_ext']
 * and $conf['prefix_thumbnail']
 *
 * @param string $dir
 * @return array
 */
function get_thumb_files($dir)
{
  global $conf;

  $prefix_length = strlen($conf['prefix_thumbnail']);
  
  $thumbnails = array();
  if ($opendir = @opendir($dir.'/thumbnail'))
  {
    while ($file = readdir($opendir))
    {
      if (in_array(get_extension($file), $conf['picture_ext'])
          and substr($file,0,$prefix_length) == $conf['prefix_thumbnail'])
      {
        array_push($thumbnails, $file);
      }
    }
  }
  return $thumbnails;
}

/**
 * returns an array with representative picture files of a directory
 * according to $conf['picture_ext']
 *
 * @param string $dir
 * @return array
 */
function get_representative_files($dir)
{
  global $conf;

  $pictures = array();
  if ($opendir = @opendir($dir.'/pwg_representative'))
  {
    while ($file = readdir($opendir))
    {
      if (in_array(get_extension($file), $conf['picture_ext']))
      {
        array_push($pictures, $file);
      }
    }
  }
  return $pictures;
}

/**
 * search in $basedir the sub-directories and calls get_pictures
 *
 * @return void
 */
function get_dirs($basedir, $indent, $level)
{
  $fs_dirs = array();
  $dirs = "";

  if ($opendir = opendir($basedir))
  {
    while ($file = readdir($opendir))
    {
      if ($file != '.'
          and $file != '..'
          and $file != 'thumbnail'
          and $file != 'pwg_high'
          and $file != 'pwg_representative'
          and is_dir ($basedir.'/'.$file))
      {
        array_push($fs_dirs, $file);
        if (!preg_match('/^[a-zA-Z0-9-_.]+$/', $file))
        {
          echo 'PWG-WARNING-1: "'.$file.'" : ';
          echo 'The name of the directory should be composed of ';
          echo 'letters, figures, "-", "_" or "." ONLY';
          echo "\n";
        }
      }
    }
  }
  // write of the dirs
  foreach ($fs_dirs as $fs_dir)
  {
    $dirs.= "\n".$indent.'<dir'.$level.' name="'.$fs_dir.'">';
    $dirs.= get_pictures($basedir.'/'.$fs_dir, $indent.'  ');
    $dirs.= get_dirs($basedir.'/'.$fs_dir, $indent.'  ', $level + 1);
    $dirs.= "\n".$indent.'</dir'.$level.'>';
  }
  return $dirs;		
}

// get_extension returns the part of the string after the last "."
function get_extension($filename)
{
  return substr(strrchr($filename, '.'), 1, strlen ($filename));
}

// get_filename_wo_extension returns the part of the string before the last
// ".".
// get_filename_wo_extension('test.tar.gz') -> 'test.tar'
function get_filename_wo_extension($filename)
{
  return substr($filename, 0, strrpos($filename, '.'));
}

function get_pictures($dir, $indent)
{
  global $conf;
  
  // fs means FileSystem : $fs_files contains files in the filesystem found
  // in $dir that can be managed by PhpWebGallery (see get_pwg_files
  // function), $fs_thumbnails contains thumbnails, $fs_representatives
  // contains potentially representative pictures for non picture files
  $fs_files = get_pwg_files($dir);
  $fs_thumbnails = get_thumb_files($dir);
  $fs_representatives = get_representative_files($dir);

  $elements = array();
  
  foreach ($fs_files as $fs_file)
  {
    $element = array();
    $element['file'] = $fs_file;
    $element['filesize'] = floor(filesize($dir.'/'.$fs_file) / 1024);
    
    $file_wo_ext = get_filename_wo_extension($fs_file);

    foreach ($conf['picture_ext'] as $ext)
    {
      $test = $conf['prefix_thumbnail'].$file_wo_ext.'.'.$ext;
      if (!in_array($test, $fs_thumbnails))
      {
        continue;
      }
      else
      {
        $element['tn_ext'] = $ext;
        break;
      }
    }

    // 2 cases : the element is a picture or not. Indeed, for a picture
    // thumbnail is mandatory and for non picture element, thumbnail and
    // representative is optionnal
    if (in_array(get_extension($fs_file), $conf['picture_ext']))
    {
      // if we found a thumnbnail corresponding to our picture...
      if (isset($element['tn_ext']))
      {
        if ($image_size = @getimagesize($dir.'/'.$fs_file))
        {
          $element['width'] = $image_size[0];
          $element['height'] = $image_size[1];
        }

        if ($conf['use_exif'])
        {
          if ($exif = @read_exif_data($dir.'/'.$fs_file))
          {
            if (isset($exif['DateTime']))
            {
              preg_match('/^(\d{4}):(\d{2}):(\d{2})/'
                         ,$exif['DateTime']
                         ,$matches);
              $element['date_creation'] =
                $matches[1].'-'.$matches[2].'-'.$matches[3];
            }
          }
        }

        if ($conf['use_iptc'])
        {
          $iptc = get_sync_iptc_data($dir.'/'.$fs_file);
          if (count($iptc) > 0)
          {
            foreach (array_keys($iptc) as $key)
            {
              $element[$key] = addslashes($iptc[$key]);
            }
          }
        }
        
        array_push($elements, $element);
      }
      else
      {
        echo 'PWG-ERROR-1: The thumbnail is missing for '.$dir.'/'.$fs_file;
        echo '-> '.$dir.'/thumbnail/';
        echo $conf['prefix_thumbnail'].$file_wo_ext.'.xxx';
        echo ' ("xxx" can be : ';
        echo implode(', ', $conf['picture_ext']);
        echo ')'."\n";
      }
    }
    else
    {
      foreach ($conf['picture_ext'] as $ext)
      {
        $candidate = $file_wo_ext.'.'.$ext;
        if (!in_array($candidate, $fs_representatives))
        {
          continue;
        }
        else
        {
          $element['representative_ext'] = $ext;
          break;
        }
      }
      
      array_push($elements, $element);
    }
  }

  $xml = "\n".$indent.'<root>';
  $attributes = array('file','tn_ext','representative_ext','filesize',
                      'width','height','date_creation','author','keywords',
                      'name','comment');
  foreach ($elements as $element)
  {
    $xml.= "\n".$indent.'  ';
    $xml.= '<element';
    foreach ($attributes as $attribute)
    {
      if (isset($element{$attribute}))
      {
        $xml.= ' '.$attribute.'="'.$element{$attribute}.'"';
      }
    }
    $xml.= ' />';
  }
  $xml.= "\n".$indent.'</root>';

  return $xml;
}

// +-----------------------------------------------------------------------+
// |                                script                                 |
// +-----------------------------------------------------------------------+
if (isset($_GET['action']))
{
  $page['action'] = $_GET['action'];
}
else
{
  $page['action'] = 'generate';
}
echo '<pre>';
switch ($page['action'])
{
  case 'generate' :
  {
    $start = get_moment();
    
    $listing = '<informations';
    $listing.= ' generation_date="'.date('Y-m-d').'"';
    $listing.= ' phpwg_version="'.$conf{'version'}.'"';
    
    $end = strrpos($_SERVER['PHP_SELF'], '/') + 1;
    $local_folder = substr($_SERVER['PHP_SELF'], 0, $end);
    $url = 'http://'.$_SERVER['HTTP_HOST'].$local_folder;
    
    $listing.= ' url="'.$url.'"';
    $listing.= '/>'."\n";
    
    $listing.= get_dirs('.', '', 0);
    
    if ($fp = @fopen("./listing.xml","w"))
    {
      fwrite($fp, $listing);
      fclose($fp);
      echo 'PWG-INFO-1: listing.xml created in ';
      echo get_elapsed_time($start, get_moment());
      echo "\n";
    }
    else
    {
      echo "PWG-ERROR-2: I can't write the file listing.xml"."\n";
    }
    break;
  }
  case 'test' :
  {
    if (isset($_GET['version']))
    {
      if ($_GET['version'] != $conf['version'])
      {
        echo 'PWG-ERROR-4: PhpWebGallery versions differs'."\n";
      }
      else
      {
        echo 'PWG-INFO-2: test successful'."\n";
      }
    }
    else
    {
      echo 'PWG-INFO-2: test successful'."\n";
    }
    break;
  }
  case 'clean' :
  {
    if( @unlink('./listing.xml'))
    {
      echo 'PWG-INFO-3 : listing.xml file deleted'."\n";
    }
    else
    {
      echo 'PWG-ERROR-3 : listing.xml does not exist'."\n";
    }
    break;
  }
}
echo '</pre>';
?>
