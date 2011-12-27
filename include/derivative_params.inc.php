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

function derivative_to_url($t)
{
  return substr($t, 0, 2);
}

function size_to_url($s)
{
  if ($s[0]==$s[1])
  {
    return $s[0];
  }
  return $s[0].'x'.$s[1];
}

function url_to_size($s)
{
  $pos = strpos($s, 'x');
  if ($pos===false)
  {
    return array((int)$s, (int)$s);
  }
  return array((int)substr($s,0,$pos), (int)substr($s,$pos+1));
}

function size_equals($s1, $s2)
{
  return ($s1[0]==$s2[0] && $s1[1]==$s2[1]);
}


/** small utility to manipulate a 'rectangle'*/
final class ImageRect
{
  public $l,$t,$r,$b;

  function __construct($l)
  {
    $this->l = $this->t = 0;
    $this->r = $l[0];
    $this->b = $l[1];
  }

  function width()
  {
    return $this->r - $this->l;
  }

  function height()
  {
    return $this->b - $this->t;
  }

  function crop_h($pixels, $coi, $force)
  {
    if ($this->width() <= $pixels)
      return;
    $tlcrop = floor($pixels/2);

    if (!empty($coi))
    {
      $coil = floor($this->r * (ord($coi[0]) - ord('a'))/25);
      $coir = ceil($this->r * (ord($coi[2]) - ord('a'))/25);
      $availableL = $coil > $this->l ? $coil - $this->l : 0;
      $availableR = $coir < $this->r ? $this->r - $coir : 0;
      if ($availableL + $availableR <= $pixels)
      {
        if (!$force)
        {
          $pixels = $availableL + $availableR;
          $tlcrop = $availableL;
        }
      }
      else
      {
        if ($availableL < $tlcrop)
        {
          $tlcrop = $availableL;
        }
        elseif ($availableR < $tlcrop)
        {
          $tlcrop = $pixels - $availableR;
        }
      }
    }
    $this->l += $tlcrop;
    $this->r -= $pixels - $tlcrop;
  }

  function crop_v($pixels, $coi, $force)
  {
    if ($this->height() <= $pixels)
      return;
    $tlcrop = floor($pixels/2);

    if (!empty($coi))
    {
      $coit = floor($this->b * (ord($coi[1]) - ord('a'))/25);
      $coib = ceil($this->b * (ord($coi[3]) - ord('a'))/25);
      $availableT = $coit > $this->t ? $coit - $this->t : 0;
      $availableB = $coib < $this->b ? $this->b - $coib : 0;
      if ($availableT + $availableB <= $pixels)
      {
        if (!$force)
        {
          $pixels = $availableT + $availableB;
          $tlcrop = $availableT;
        }
      }
      else
      {
        if ($availableT < $tlcrop)
        {
          $tlcrop = $availableT;
        }
        elseif ($availableB < $tlcrop)
        {
          $tlcrop = $pixels - $availableB;
        }
      }
    }
    $this->t += $tlcrop;
    $this->b -= $pixels - $tlcrop;
  }

}


/*how we crop and/or resize an image*/
final class SizingParams
{
  function __construct($ideal_size, $max_crop = 0, $min_size = null)
  {
    $this->ideal_size = $ideal_size;
    $this->max_crop = $max_crop;
    $this->min_size = $min_size;
  }

  static function classic($w, $h)
  {
    return new SizingParams( array($w,$h) );
  }

  static function square($w)
  {
    return new SizingParams( array($w,$w), 1, array($w,$w) );
  }

  function add_url_tokens(&$tokens)
  {
      if ($this->max_crop == 0)
      {
        $tokens[] = 's'.size_to_url($this->ideal_size);
      }
      elseif ($this->max_crop == 1 && size_equals($this->ideal_size, $this->min_size) )
      {
        $tokens[] = 'e'.size_to_url($this->ideal_size);
      }
      else
      {
        $tokens[] = size_to_url($this->ideal_size);
        $tokens[] = sprintf('%02x', round(100*$this->max_crop) );
        $tokens[] = size_to_url($this->min_size);
      }
  }

  static function from_url_tokens($tokens)
  {
    if (count($tokens)<1)
      throw new Exception('Empty array while parsing Sizing');
    $token = array_shift($tokens);
    if ($token[0]=='s')
    {
      return new SizingParams( url_to_size( substr($token,1) ) );
    }
    if ($token[0]=='e')
    {
      $s = url_to_size( substr($token,1) );
      return new SizingParams($s, 1, $s);
    }

    $ideal_size = url_to_size( $token );
    if (count($tokens)<2)
      throw new Exception('Sizing arr');

    $token = array_shift($tokens);
    $crop = sscanf('%02x' , $token) / 100;

    $token = array_shift($tokens);
    $min_size = url_to_size( $token );
    return new SizingParams($ideal_size, $crop, $min_size);
  }


  function compute($in_size, $coi, &$crop_rect, &$scale_size)
  {
    $destCrop = new ImageRect($in_size);

    if ($this->max_crop > 0)
    {
      $ratio_w = $destCrop->width() / $this->ideal_size[0];
      $ratio_h = $destCrop->height() / $this->ideal_size[1];
      if ($ratio_w>1 || $ratio_h>1)
      {
        if ($ratio_w > $ratio_h)
        {
          $h = $destCrop->height() / $ratio_w;
          if ($h < $this->min_size[1])
          {
            $idealCropPx = $destCrop->width() - round($destCrop->height() * $this->ideal_size[0] / $this->min_size[1], 0);
            $maxCropPx = round($this->max_crop * $destCrop->width());
            $destCrop->crop_h( min($idealCropPx, $maxCropPx), $coi, false);
          }
        }
        else
        {
          $w = $destCrop->width() / $ratio_h;
          if ($w < $this->min_size[0])
          {
            $idealCropPx = $destCrop->height() - round($destCrop->width() * $this->ideal_size[1] / $this->min_size[0], 0);
            $maxCropPx = round($this->max_crop * $destCrop->height());
            $destCrop->crop_v( min($idealCropPx, $maxCropPx), $coi, false);
          }
        }
      }
    }

    $scale_size = array($destCrop->width(), $destCrop->height());

    $ratio_w = $destCrop->width() / $this->ideal_size[0];
    $ratio_h = $destCrop->height() / $this->ideal_size[1];
    if ($ratio_w>1 || $ratio_h>1)
    {
      if ($ratio_w > $ratio_h)
      {
        $scale_size[0] = $this->ideal_size[0];
        $scale_size[1] = floor($scale_size[1] / $ratio_w);
      }
      else
      {
        $scale_size[0] = floor($scale_size[0] / $ratio_h);
        $scale_size[1] = $this->ideal_size[1];
      }
    }
    else
    {
      $scale_size = null;
    }

    $crop_rect = null;
    if ($destCrop->width()!=$in_size[0] || $destCrop->height()!=$in_size[1] )
    {
      $crop_rect = $destCrop;
    }
  }

}


/*how we generate a derivative image*/
final class ImageParams
{
  public $type = IMG_CUSTOM;
  public $last_mod_time = 0; // used for non-custom images to regenerate the cached files
  public $sizing;

  function __construct($sizing)
  {
    $this->sizing = $sizing;
  }

  function add_url_tokens(&$tokens)
  {
    $this->sizing->add_url_tokens($tokens);
  }

  static function from_url_tokens($tokens)
  {
    $sizing = SizingParams::from_url_tokens($tokens);
    $ret = new ImageParams($sizing);
    return $ret;
  }

  function compute_final_size($in_size, $coi)
  {
    $this->sizing->compute( $in_size, $coi, $crop_rect, $scale_size );
    return $scale_size != null ? $scale_size : $in_size;
  }

  function is_identity($in_size)
  {
    if ($in_size[0] > $this->sizing->ideal_size[0] or
        $in_size[1] > $this->sizing->ideal_size[1] )
    {
      return false;
    }
    return true;
  }
}
?>