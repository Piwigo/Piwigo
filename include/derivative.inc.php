<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2012 Piwigo Team                  http://piwigo.org |
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

final class SrcImage
{
  const IS_ORIGINAL = 0x01;
  const IS_MIMETYPE = 0x02;

  public $rel_path;

  public $coi=null;
  private $size=null;
  private $flags=0;

  function __construct($infos)
  {
    global $conf;

    $ext = get_extension($infos['path']);
    if (in_array($ext, $conf['picture_ext']))
    {
      $this->rel_path = $infos['path'];
      $this->flags |= self::IS_ORIGINAL;
    }
    elseif (!empty($infos['representative_ext']))
    {
      $this->rel_path = original_to_representative($infos['path'], $infos['representative_ext']);
    }
    else
    {
      $ext = strtolower($ext);
      $this->rel_path = trigger_event('get_mimetype_location', get_themeconf('mime_icon_dir').$ext.'.png', $ext );
      $this->flags |= self::IS_MIMETYPE;
      $this->size = @getimagesize(PHPWG_ROOT_PATH.$this->rel_path);
    }

    $this->coi = @$infos['coi'];
    if (!$this->size && isset($infos['width']) && isset($infos['height']))
    {
      $this->size = array($infos['width'], $infos['height']);
    }
  }

  function is_original()
  {
    return $this->flags & self::IS_ORIGINAL;
  }

  function is_mimetype()
  {
    return $this->flags & self::IS_MIMETYPE;
  }

  function get_path()
  {
    return PHPWG_ROOT_PATH.$this->rel_path;
  }

  function get_url()
  {
    return get_root_url().$this->rel_path;
  }

  function has_size()
  {
    return $this->size != null;
  }

  function get_size()
  {
    if ($this->size == null)
      not_implemented(); // get size from file
    return $this->size;
  }
}



final class DerivativeImage
{
  const SAME_AS_SRC = 0x10;

  public $src_image;

  private $requested_type;

  private $flags = 0;
  private $params;
  private $rel_path, $rel_url;

  function __construct($type, $src_image)
  {
    $this->src_image = $src_image;
    if (is_string($type))
    {
      $this->requested_type = $type;
      $this->params = ImageStdParams::get_by_type($type);
    }
    else
    {
      $this->requested_type = IMG_CUSTOM;
      $this->params = $type;
    }

    self::build($src_image, $this->params, $this->rel_path, $this->rel_url, $this->flags);
  }

  static function thumb_url($infos)
  {
    $src_image = new SrcImage($infos);
    self::build($src_image, ImageStdParams::get_by_type(IMG_THUMB), $rel_path, $rel_url);
    return get_root_url().$rel_url;
  }

  static function url($type, $infos)
  {
    $src_image = new SrcImage($infos);
    $params = is_string($type) ? ImageStdParams::get_by_type($type) : $type;
    self::build($src_image, $params, $rel_path, $rel_url);
    return get_root_url().$rel_url;
  }

  static function get_all($src_image)
  {
    $ret = array();
    foreach (ImageStdParams::get_defined_type_map() as $type => $params)
    {
      $derivative = new DerivativeImage($params, $src_image);
      $ret[$type] = $derivative;
    }
    foreach (ImageStdParams::get_undefined_type_map() as $type => $type2)
    {
      $ret[$type] = $ret[$type2];
    }

    return $ret;
  }

  private static function build($src, &$params, &$rel_path, &$rel_url, &$flags = null)
  {
    if ( $src->has_size() && $params->is_identity( $src->get_size() ) )
    {
      // todo - what if we have a watermark maybe return a smaller size?
      $flags |= self::SAME_AS_SRC;
      $params = null;
      $rel_path = $rel_url = $src->rel_path;
      return;
    }

    $tokens=array();
    $tokens[] = substr($params->type,0,2);

    if ($params->sizing->max_crop != 0 and !empty($src->coi))
    {
      $tokens[] = 'ci'.$src->coi;
    }

    if ($params->type==IMG_CUSTOM)
    {
      $params->add_url_tokens($tokens);
    }

    $loc = $src->rel_path;
    if (substr_compare($loc, './', 0, 2)==0)
    {
      $loc = substr($loc, 2);
    }
    elseif (substr_compare($loc, '../', 0, 3)==0)
    {
      $loc = substr($loc, 3);
    }
    $loc = substr_replace($loc, '-'.implode('_', $tokens), strrpos($loc, '.'), 0 );

    $rel_path = PWG_DERIVATIVE_DIR.$loc;

    global $conf;
    $url_style=$conf['derivative_url_style'];
    if (!$url_style)
    {
      $mtime = @filemtime(PHPWG_ROOT_PATH.$rel_path);
      if ($mtime===false or $mtime < $params->last_mod_time)
      {
        $url_style = 2;
      }
      else
      {
        $url_style = 1;
      }
    }

    if ($url_style == 2)
    {
      $rel_url = 'i';
      if ($conf['php_extension_in_urls']) $rel_url .= '.php';
      if ($conf['question_mark_in_urls']) $rel_url .= '?';
      $rel_url .= '/'.$loc;
    }
    else
    {
      $rel_url = $rel_path;
    }
  }

  function get_path()
  {
    return PHPWG_ROOT_PATH.$this->rel_path;
  }

  function get_url()
  {
    return get_root_url().$this->rel_url;
  }

  function same_as_source()
  {
    return $this->flags & self::SAME_AS_SRC;
  }


  function get_type()
  {
    if ($this->flags & self::SAME_AS_SRC)
      return 'original';
    return $this->params->type;
  }

  /* returns the size of the derivative image*/
  function get_size()
  {
    if ($this->flags & self::SAME_AS_SRC)
    {
      return $this->src_image->get_size();
    }
    return $this->params->compute_final_size($this->src_image->get_size(), $this->src_image->coi);
  }

  function get_size_htm()
  {
    $size = $this->get_size();
    if ($size)
    {
      return 'width="'.$size[0].'" height="'.$size[1].'"';
    }
  }

  function get_size_hr()
  {
    $size = $this->get_size();
    if ($size)
    {
      return $size[0].' x '.$size[1];
    }
  }

}

?>