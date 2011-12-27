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

final class ImageStdParams
{
  private static $all_types = array(IMG_SQUARE,IMG_THUMB,IMG_SMALL,IMG_MEDIUM,IMG_LARGE,IMG_XLARGE,IMG_XXLARGE);
  private static $all_type_map = array();
  private static $type_map = array();
  private static $undefined_type_map = array();
  
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
  
  static function load_from_db()
  {
    self::make_default();
    self::build_maps();
  }

  static function load_from_file()
  {
    self::make_default();
    self::build_maps();
  }

  static function make_default()
  {
    //todo
    self::$type_map[IMG_SQUARE] = new ImageParams( SizingParams::square(100,100) );
    self::$type_map[IMG_THUMB] = new ImageParams( SizingParams::classic(144,144) );
    self::$type_map[IMG_SMALL] = new ImageParams( SizingParams::classic(240,240) );
    self::$type_map[IMG_MEDIUM] = new ImageParams( SizingParams::classic(432,432) );
    self::$type_map[IMG_LARGE] = new ImageParams( SizingParams::classic(648,576) );
    self::$type_map[IMG_XLARGE] = new ImageParams( SizingParams::classic(864,648) );
    self::$type_map[IMG_XXLARGE] = new ImageParams( SizingParams::classic(1200,900) );
  }
  
  private static function build_maps()
  {
    foreach (self::$type_map as $type=>$params)
    {
      $params->type = $type;
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