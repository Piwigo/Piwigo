<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
 * @package Derivatives
 */


/**
 * Formats a size name into a 2 chars identifier usable in filename.
 *
 * @param string $t one of IMG_*
 * @return string
 */
function derivative_to_url($t)
{
  return substr($t, 0, 2);
}

/**
 * Formats a size array into a identifier usable in filename.
 *
 * @param int[] $s
 * @return string
 */
function size_to_url($s)
{
  if ($s[0]==$s[1])
  {
    return $s[0];
  }
  return $s[0].'x'.$s[1];
}

/**
 * @param int[] $s1
 * @param int[] $s2
 * @return bool
 */
function size_equals($s1, $s2)
{
  return ($s1[0]==$s2[0] && $s1[1]==$s2[1]);
}

/**
 * Converts a char a-z into a float.
 *
 * @param string
 * @return float
 */
function char_to_fraction($c)
{
	return (ord($c) - ord('a'))/25;
}

/**
 * Converts a float into a char a-z.
 *
 * @param float
 * @return string
 */
function fraction_to_char($f)
{
	return chr(ord('a') + round($f*25));
}


/**
 * Small utility to manipulate a 'rectangle'.
 */
final class ImageRect
{
  /**
   * @var int $l
   * @var int $t
   * @var int $r
   * @var int $b
   */
  public $l,$t,$r,$b;

  /**
   * @param int[] $l width and height
   */
  function __construct($l)
  {
    $this->l = $this->t = 0;
    $this->r = $l[0];
    $this->b = $l[1];
  }

  /**
   * @return int
   */
  function width()
  {
    return $this->r - $this->l;
  }

  /**
   * @return int
   */
  function height()
  {
    return $this->b - $this->t;
  }

  /**
   * Crops horizontally this rectangle by increasing left side and/or reducing the right side.
   *
   * @param int $pixels - the amount to substract from the width
   * @param stirng $coi - a 4 character string (or null) containing the center of interest
   */
  function crop_h($pixels, $coi)
  {
    if ($this->width() <= $pixels)
      return;
    $tlcrop = floor($pixels/2);

    if (!empty($coi))
    {
      $coil = floor($this->r * char_to_fraction($coi[0]));
      $coir = ceil($this->r * char_to_fraction($coi[2]));
      $availableL = $coil > $this->l ? $coil - $this->l : 0;
      $availableR = $coir < $this->r ? $this->r - $coir : 0;
      if ($availableL + $availableR >= $pixels)
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

  /**
   * Crops vertically this rectangle by increasing top side and/or reducing the bottom side.
   *
   * @param int $pixels - the amount to substract from the height
   * @param string $coi - a 4 character string (or null) containing the center of interest
   */
  function crop_v($pixels, $coi)
  {
    if ($this->height() <= $pixels)
      return;
    $tlcrop = floor($pixels/2);

    if (!empty($coi))
    {
      $coit = floor($this->b * char_to_fraction($coi[1]));
      $coib = ceil($this->b * char_to_fraction($coi[3]));
      $availableT = $coit > $this->t ? $coit - $this->t : 0;
      $availableB = $coib < $this->b ? $this->b - $coib : 0;
      if ($availableT + $availableB >= $pixels)
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


/**
 * Paramaters for derivative scaling and cropping.
 * Instance of this class contained by DerivativeParams class.
 */
final class SizingParams
{
  /** @var int[] */
  var $ideal_size;
  /** @var float */
  var $max_crop;
  /** @var int[] */
  var $min_size;

  /**
   * @param int[] $ideal_size - two element array of maximum output dimensions (width, height)
   * @param float $max_crop - from 0=no cropping to 1= max cropping (100% of width/height);
   *    expressed as a factor of the input width/height
   * @param int[] $min_size - (used only if _$max_crop_ !=0) two element array of output dimensions (width, height)
   */
  function __construct($ideal_size, $max_crop=0, $min_size=null)
  {
    $this->ideal_size = $ideal_size;
    $this->max_crop = $max_crop;
    $this->min_size = $min_size;
  }

  /**
   * Returns a simple SizingParams object.
   *
   * @param int $w
   * @param int $h
   * @return SizingParams
   */
  static function classic($w, $h)
  {
    return new SizingParams( array($w,$h) );
  }

  /**
   * Returns a square SizingParams object.
   *
   * @param int $x
   * @return SizingParams
   */
  static function square($w)
  {
    return new SizingParams( array($w,$w), 1, array($w,$w) );
  }

  /**
   * Adds tokens depending on sizing configuration.
   *
   * @param array &$tokens
   */
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
        $tokens[] = fraction_to_char($this->max_crop);
        $tokens[] = size_to_url($this->min_size);
      }
  }

  /**
   * Calculates the cropping rectangle and the scaled size for an input image size.
   *
   * @param int[] $in_size - two element array of input dimensions (width, height)
   * @param string $coi - four character encoded string containing the center of interest (unused if max_crop=0)
   * @param ImageRect &$crop_rect - ImageRect containing the cropping rectangle or null if cropping is not required
   * @param int[] &$scale_size - two element array containing width and height of the scaled image
   */
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
            $idealCropPx = $destCrop->width() - floor($destCrop->height() * $this->ideal_size[0] / $this->min_size[1]);
            $maxCropPx = round($this->max_crop * $destCrop->width());
            $destCrop->crop_h( min($idealCropPx, $maxCropPx), $coi);
          }
        }
        else
        {
          $w = $destCrop->width() / $ratio_h;
          if ($w < $this->min_size[0])
          {
            $idealCropPx = $destCrop->height() - floor($destCrop->width() * $this->ideal_size[1] / $this->min_size[0]);
            $maxCropPx = round($this->max_crop * $destCrop->height());
            $destCrop->crop_v( min($idealCropPx, $maxCropPx), $coi);
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
        $scale_size[1] = floor(1e-6 + $scale_size[1] / $ratio_w);
      }
      else
      {
        $scale_size[0] = floor(1e-6 + $scale_size[0] / $ratio_h);
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


/**
 * All needed parameters to generate a derivative image.
 */
final class DerivativeParams
{
  /** @var SizingParams */
  public $sizing;
  /** @var string among IMG_* */
  public $type = IMG_CUSTOM;
  /** @var int used for non-custom images to regenerate the cached files */
  public $last_mod_time = 0;
  /** @var bool */
  public $use_watermark = false;
  /** @var float from 0=no sharpening to 1=max sharpening */
  public $sharpen = 0;

  /**
   * @param SizingParams $sizing
   */
  function __construct($sizing)
  {
    $this->sizing = $sizing;
  }

  /**
   * @return array
   */
  public function __sleep()
  {
    return array('last_mod_time', 'sizing', 'sharpen');
  }

  /**
   * Adds tokens depending on sizing configuration.
   *
   * @param array &$tokens
   */
  function add_url_tokens(&$tokens)
  {
    $this->sizing->add_url_tokens($tokens);
  }

  /**
   * @return int[]
   */
  function compute_final_size($in_size)
  {
    $this->sizing->compute( $in_size, null, $crop_rect, $scale_size );
    return $scale_size != null ? $scale_size : $in_size;
  }

  /**
   * @return int
   */
  function max_width()
  {
    return $this->sizing->ideal_size[0];
  }

  /**
   * @return int
   */
  function max_height()
  {
    return $this->sizing->ideal_size[1];
  }

  /**
   * @todo : description of DerivativeParams::is_identity
   *
   * @return bool
   */
  function is_identity($in_size)
  {
    if ($in_size[0] > $this->sizing->ideal_size[0] or
        $in_size[1] > $this->sizing->ideal_size[1] )
    {
      return false;
    }
    return true;
  }

  /**
   * @return bool
   */
  function will_watermark($out_size)
  {
    if ($this->use_watermark)
    {
      $min_size = ImageStdParams::get_watermark()->min_size;
      return $min_size[0]<=$out_size[0]
        || $min_size[1]<=$out_size[1];
    }
    return false;
  }
}

?>