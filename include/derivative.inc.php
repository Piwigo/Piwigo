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

  public $id;
  public $rel_path;

  private $size=null;
  private $flags=0;

  function __construct($infos)
  {
    global $conf;

    $this->id = $infos['id'];
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
    return embellish_url(get_root_url().$this->rel_path);
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
  public $src_image;

  private $params;
  private $rel_path, $rel_url;

  function __construct($type, $src_image)
  {
    $this->src_image = $src_image;
    if (is_string($type))
    {
      $this->params = ImageStdParams::get_by_type($type);
    }
    else
    {
      $this->params = $type;
    }

    self::build($src_image, $this->params, $this->rel_path, $this->rel_url);
  }

  static function thumb_url($infos)
  {
    return self::url(IMG_THUMB, $infos);
  }

  static function url($type, $infos)
  {
    $src_image = is_object($infos) ? $infos : new SrcImage($infos);
    $params = is_string($type) ? ImageStdParams::get_by_type($type) : $type;
    self::build($src_image, $params, $rel_path, $rel_url);
    if ($params == null)
    {
      return $src_image->get_url();
    }
    return embellish_url(
        trigger_event('get_derivative_url',
          get_root_url().$rel_url,
          $params, $src_image, $rel_url
          ) );
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

  private static function build($src, &$params, &$rel_path, &$rel_url)
  {
    if ( $src->has_size() && $params->is_identity( $src->get_size() ) )
    {
      // todo - what if we have a watermark maybe return a smaller size?
      $params = null;
      $rel_path = $rel_url = $src->rel_path;
      return;
    }

    $tokens=array();
    $tokens[] = substr($params->type,0,2);

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
    if ($this->params == null)
    {
      return $this->src_image->get_url();
    }
    return embellish_url(
        trigger_event('get_derivative_url',
          get_root_url().$this->rel_url,
          $this->params, $this->src_image, $this->rel_url
          ) );
  }

  function same_as_source()
  {
    return $this->params == null;
  }


  function get_type()
  {
    if ($this->params == null)
      return 'Original';
    return $this->params->type;
  }

  /* returns the size of the derivative image*/
  function get_size()
  {
    if ($this->params == null)
    {
      return $this->src_image->get_size();
    }
    return $this->params->compute_final_size($this->src_image->get_size());
  }

  function get_size_htm()
  {
    $size = $this->get_size();
    if ($size)
    {
      return 'width='.$size[0].' height='.$size[1];
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