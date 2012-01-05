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

define('IMG_SQUARE', 'square');
define('IMG_THUMB', 'thumb');
define('IMG_SMALL', 'small');
define('IMG_MEDIUM', 'medium');
define('IMG_LARGE', 'large');
define('IMG_XLARGE', 'xlarge');
define('IMG_XXLARGE', 'xxlarge');
define('IMG_CUSTOM', 'custom');

final class WatermarkParams
{
  public $file = '';
  public $min_size = array(500,500);
  public $xpos = 50;
  public $ypos = 50;
  public $xrepeat = 0;
  public $opacity = 100;
}


final class ImageStdParams
{
  private static $all_types = array(IMG_SQUARE,IMG_THUMB,IMG_SMALL,IMG_MEDIUM,IMG_LARGE,IMG_XLARGE,IMG_XXLARGE);
  private static $all_type_map = array();
  private static $type_map = array();
  private static $undefined_type_map = array();
  private static $watermark;

  static function get_all_types()
  {
    return self::$all_types;
  }

  static function get_all_type_map()
  {
    return self::$all_type_map;
  }

  static function get_defined_type_map()
  {
    return self::$type_map;
  }

  static function get_undefined_type_map()
  {
    return self::$undefined_type_map;
  }

  static function get_by_type($type)
  {
    return self::$all_type_map[$type];
  }

  static function get_watermark()
  {
    return self::$watermark;
  }

  static function load_from_db()
  {
    global $conf;
    $arr = @unserialize($conf['derivatives']);
    if (false!==$arr)
    {
      self::$type_map = $arr['d'];
      self::$watermark = @$arr['w'];
      if (!self::$watermark) self::$watermark = new WatermarkParams();
    }
    else
    {
      self::make_default();
    }
    self::build_maps();
  }

  static function load_from_file()
  {
    global $conf;
    $arr = @unserialize(@file_get_contents(PHPWG_ROOT_PATH.$conf['data_location'].'derivatives.dat'));
    if (false!==$arr)
    {
      self::$type_map = $arr['d'];
      self::$watermark = @$arr['w'];
      if (!self::$watermark) self::$watermark = new WatermarkParams();
    }
    else
    {
      self::make_default();
    }
    self::build_maps();
  }

  static function set_watermark($watermark)
  {
    self::$watermark = $watermark;
  }
  
  static function set_and_save($map)
  {
    global $conf;
    self::$type_map = $map;

    $ser = serialize( array(
      'd' => self::$type_map,
      'w' => self::$watermark,
      ) );
    conf_update_param('derivatives', addslashes($ser) );
    file_put_contents(PHPWG_ROOT_PATH.$conf['data_location'].'derivatives.dat', $ser);
    self::build_maps();
  }

  private static function make_default()
  {
    self::$watermark = new WatermarkParams();
    self::$type_map[IMG_SQUARE] = new DerivativeParams( SizingParams::square(100,100) );
    self::$type_map[IMG_THUMB] = new DerivativeParams( SizingParams::classic(144,144) );
    self::$type_map[IMG_SMALL] = new DerivativeParams( SizingParams::classic(240,240) );
    self::$type_map[IMG_MEDIUM] = new DerivativeParams( SizingParams::classic(432,432) );
    self::$type_map[IMG_LARGE] = new DerivativeParams( SizingParams::classic(648,576) );
    self::$type_map[IMG_XLARGE] = new DerivativeParams( SizingParams::classic(864,648) );
    self::$type_map[IMG_XXLARGE] = new DerivativeParams( SizingParams::classic(1200,900) );
  }

  public static function apply_global($params)
  {
    if (!empty(self::$watermark->file) &&
        (self::$watermark->min_size[0]<=$params->sizing->ideal_size[0]
        && self::$watermark->min_size[1]<=$params->sizing->ideal_size[1] ) )
    {
      $params->use_watermark = true;
    }
  }
  
  private static function build_maps()
  {
    foreach (self::$type_map as $type=>$params)
    {
      $params->type = $type;
      self::apply_global($params);
    }
    self::$all_type_map = self::$type_map;

    for ($i=0; $i<count(self::$all_types); $i++)
    {
      $tocheck = self::$all_types[$i];
      if (!isset(self::$type_map[$tocheck]))
      {
        for ($j=$i-1; $j>=0; $j--)
        {
          $target = self::$all_types[$j];
          if (isset(self::$type_map[$target]))
          {
            self::$all_type_map[$tocheck] = self::$type_map[$target];
            self::$undefined_type_map[$tocheck] = $target;
            break;
          }
        }
      }
    }
  }

}

?>