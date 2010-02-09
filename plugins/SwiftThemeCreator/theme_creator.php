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

/* TODO: Revoir le lien du menu de l'admin */
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');
if (!defined('IN_ADMIN') or !IN_ADMIN) die('Hacking attempt!');
define('STC_PATH', PHPWG_PLUGINS_PATH.'SwiftThemeCreator/');
define('STC_INTERNAL_VERSION', '1.40'); 
load_language('plugin.lang', STC_PATH);

/*
 * stc_hex2rgb convert any string to array of RGB values
 */
function stc_hex2rgb($color)
{
  if ($color[0] == '#') $color = substr($color, 1);
  if (strlen($color) == 6)
    list($r, $g, $b) = array($color[0].$color[1],
                             $color[2].$color[3],
                             $color[4].$color[5]);
  else {
    $color .= $color . $color . '000';
    list($r, $g, $b) = array($color[0].$color[0],
                             $color[1].$color[1], 
                             $color[2].$color[2]);
  }
  return array(hexdec($r), hexdec($g), hexdec($b));
}
/*
 * lighten returns array of x% of lighter RGB values
 */
function lighten( $r, $g, $b, $percent)
{
  $r = min(round($r+(($percent*(255-$r))/100)),255);
  $g = min(round($g+(($percent*(255-$g))/100)),255);
  $b = min(round($b+(($percent*(255-$b))/100)),255);
  return sprintf('#%02X%02X%02X', $r, $g, $b);
}
/*
 * darken returns array of x% of darker RGB values
 */
function darken( $r, $g, $b, $percent)
{
  $r = max(round($r-(($percent*$r)/100)),0);
  $g = max(round($g-(($percent*$g)/100)),0);
  $b = max(round($b-(($percent*$b)/100)),0);
  return sprintf('#%02X%02X%02X', $r, $g, $b);
}
/*
 * stc_newfile create a new file
 */
function stc_newfile( $filename, $data )
{
  $fp = @fopen($filename, 'w');
  if ($fp) {
    $ret = fwrite($fp, $data); 
    @fclose($fp);
    return $ret;
  }
  return false;
}
/*
 * Default values 
 */
function init_main(&$main)
{
  global $available_templates;
  $main = array(
    STC_INTERNAL_VERSION => true, /* $main version */
    'template_sel' => 0, 
    'newtpl' => 'yoga',
    'newtheme' => '',
    'simulate' => true,
    'colorize' => false,
    'brightness' => false,
    'contrast' => false,
    'new_theme' => '',
    'color1' => '#111111',
    'color' => array('#111111', '#EEEEEE', '#FF7700', '#FF3333', '#FF3363', ),
    'templatedir' => PHPWG_ROOT_PATH . 'plugins/SwiftThemeCreator/simul',
    'color1' => '#111111',
    'color2' => '#EEEEEE',
    'color3' => '#FF7700',
    'color4' => '#FF3333',
    'color5' => '#FF3363',
    'background' => 'fixed',
    'picture_url' => PHPWG_ROOT_PATH . 'plugins/SwiftThemeCreator/sample.jpg',
    'picture_width' => 2048,
    'picture_height' => 100,
    'background_mode' => 'as',
    'src_category' => 0,
    'category' => 'header',
    'phase' => 'Kernel init',
    'subphase' => 'New version',
  );    
} 

$errors = array();
$infos = array();

// +-----------------------------------------------------------------------+
// |                            Kernel init                                |
// +-----------------------------------------------------------------------+
if (!isset($swift_theme_creator)) $swift_theme_creator = new ThemeCreator();
$swift_theme_creator->reload();
$main = $swift_theme_creator->theme_config;

/* 
 * Find templates
 */
$available_templates = array();
$template_dir = PHPWG_ROOT_PATH.'template';
foreach (get_dirs($template_dir) as $dir)
{ array_push($available_templates, $dir);
}
/* 
 * $main is reloaded but does template still exist?
 * Does the fixed background still exist? Category? ...
 */
if (!isset($main[STC_INTERNAL_VERSION])) init_main($main);
$flip = array_flip($available_templates);
$main['template_sel'] = (isset($flip[$main['newtpl']])) ?
    $flip[$main['newtpl']] : 0; /* Deleted ? First available */
$main['subphase'] = 'Find category';
$query = 'SELECT id,name,uppercats,global_rank
  FROM ' . CATEGORIES_TABLE . ';';
display_select_cat_wrapper($query,array(),'src_category');
$available_cat = $template->get_template_vars('src_category');
$flip = array_flip($available_cat);
$main['src_category'] = (isset($flip[$main['category']])) ?
    $flip[$main['category']] : max($flip); /* Deleted ? Most recent */

    
// +-----------------------------------------------------------------------+
// |                            $_POST controls                            |
// +-----------------------------------------------------------------------+
$main['phase'] = 'POST controls';
if (!isset($_POST['reset']))
{
  $main['simulate'] = isset($_POST['simulate']);
  if (!isset($_POST['submit'])) $main['simulate'] = true;
  /* 
   * Template controls
   */
  $main['subphase'] = 'template controls';
  if (isset($_POST['template'])) $main['template_sel'] = $_POST['template'];
  $main['newtpl'] = $available_templates[$main['template_sel']];
  if ($main['newtpl'] != 'yoga')
    array_push($infos, l10n('Unpredictable results could be observed with ' 
        . 'this template. Preview is based on yoga template only.')); 

  /* 
   * Theme controls
   */
  $main['subphase'] = 'theme controls';
  if (isset($_POST['new_theme'])) $main['newtheme'] = strip_tags($_POST['new_theme']);
  if ($main['newtheme'] == '') $main['simulate'] = true; /* Empty = Simulate */
  $cleaning = true; /* Delete files on failure */
  if ( !$main['simulate'] and !preg_match('/^[a-z0-9-_]{1,8}$/', $main['newtheme']) )
    array_push($errors, l10n('Invalid theme name: 1 to 8 lowercase'
      . ' alphanumeric characters including "-" and "_".')); 
  if ($main['simulate']) { /* $main['templatedir'] != $template_dir (Smarty) */
    $main['templatedir'] = PHPWG_ROOT_PATH . 'plugins/SwiftThemeCreator/simul';
    $themedir = $main['templatedir'];
    $cleaning = false; /* No delete with simulate */ 
  } else { 
    $main['templatedir'] = PHPWG_ROOT_PATH . 'template/' . $main['newtpl']; 
    $themedir = $main['templatedir'] . '/theme/' . $main['newtheme'];
  }

  /* 
   * Directories controls
   */
  $main['subphase'] = 'directories controls';
  if (is_dir( $themedir ) and !$main['simulate']) {
    array_push($errors, '['.$themedir.'] : '.l10n('Invalid theme: This' 
         . ' theme exists already (no override available).')); 
    $cleaning = false; /* No delete on existing theme */
  } elseif ( !is_writable($main['templatedir']) )
    array_push($errors, '['.$main['templatedir'].'] : '.l10n('no_write_access'));

  /* 
   * Colors controls
   */
  $main['subphase'] = 'colors controls';
  if (isset($_POST['color1'])) 
    $main['color'] = array(
      $_POST['color1'], $_POST['color2'], $_POST['color3'], $_POST['color4'], 
      $_POST['color5']);
  $main['color1'] = $main['color'][0];
  $main['color2'] = $main['color'][1];
  $main['color3'] = $main['color'][2];
  $main['color4'] = $main['color'][3];
  $main['color5'] = $main['color'][4];
  $colors = $main['color1'] . $main['color2']
          . $main['color3'] . $main['color4'] . $main['color5'];
  if ( !preg_match('/^(#?([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})){5}$/', $colors) ) 
    array_push($errors, l10n('Invalid color code: 3 or 6 hexadecimal characters,' 
          . ' preceded or not by "#"')); 
  
  /* 
   * Background and text difference control
   */
  $main['subphase'] = 'text difference controls';
  list($r,$g,$b) = stc_hex2rgb($main['color'][0]);
  list($r2,$g2,$b2) = stc_hex2rgb($main['color'][1]);
  $dif = abs( ( (($r*299)+($g*587)+($b*114)) / 1000 )
          - ( (( $r2*299)+($g2*587)+($b2*114)) / 1000 ) );
  if ( $dif < 65 )
    array_push($errors, l10n('Insufficient brightness difference between ' 
          . 'text and background. dif=') . $dif); 
  $dif = (max($r, $r2) - min($r, $r2)) 
     + (max($g, $g2) - min($g, $g2)) + (max($b, $b2) - min($b, $b2));
  if ( $dif < 200 )
    array_push($errors, l10n('Insufficient colour difference between ' 
          . 'text and background. dif=') . $dif); 

  /* 
   * Background and Internal links difference control
   */
  $main['subphase'] = 'links difference controls';
  if (false)
  {
    list($r,$g,$b) = stc_hex2rgb($main['color'][0]);
    list($r2,$g2,$b2) = stc_hex2rgb($main['color'][2]);
    $dif = abs( ( (($r*299)+($g*587)+($b*114)) / 1000 )
            - ( (($r2*299)+($g2*587)+($b2*114)) / 1000 ));
    if ( $dif < 65 )
      array_push($errors, l10n('Insufficient brightness difference between ' 
            . 'Internal links and background. dif=') . $dif); 
    $dif = (max($r, $r2) - min($r, $r2)) 
       + (max($g, $g2) - min($g, $g2)) + (max($b, $b2) - min($b, $b2));
    if ( $dif < 200 )
      array_push($errors, l10n('Insufficient colour difference between ' 
            . 'Internal links and background. dif=') . $dif); 
  }

  /* 
   * Header background controls
   */
  $main['subphase'] = 'fixed background controls'; 
  if (isset($_POST['picture_url'])) $main['picture_url'] = $_POST['picture_url'];
  if (isset($_POST['background_mode'])) 
      $main['background_mode'] = $_POST['background_mode'];  
  if (isset($_POST['background'])) $main['background'] = $_POST['background'];
  // Fixed
  if ( $main['background'] == 'fixed') {
    if ( is_dir($main['picture_url'])
        or !is_file($main['picture_url']) )
      array_push($errors, l10n('Header picture is not found, check its path and name.')); 
    $extension = substr($main['picture_url'],strrpos($main['picture_url'],'.')+1);
    if (!in_array($extension, array('jpg','jpeg','png')))
      array_push($errors, l10n('Compliant extensions are .jpg, .jpeg or .png.')); 
  }
  $main['subphase'] = 'random background controls';
  if (isset($_POST['src_category'])) 
    $main['src_category'] = (int) $_POST['src_category'];
  $main['category'] = $available_cat[$main['src_category']];

  /*
   * Width and Height limits control
   */
  $main['subphase'] = 'width and height controls'; 
  if ($main['background'] != 'off' 
    and isset($_POST['picture_width'])
    and isset($_POST['picture_height']))
  { 
    if( !(ctype_digit($_POST['picture_width']) 
      and $_POST['picture_width'] > 11 
      and $_POST['picture_width'] < 4097 ) )
      array_push($errors, '['.$_POST['picture_width'].'] : ' 
            . l10n('incorrect width value [12-4096].')); 
    else $main['picture_width'] = $_POST['picture_width'];
    if ( !(ctype_digit($_POST['picture_height'])
      and $_POST['picture_height'] > 11 
      and $_POST['picture_height'] < 201 ) )
      array_push($errors, '['.$_POST['picture_height'].'] : '
            . l10n('incorrect width value [12-200].')); 
    else $main['picture_height'] = $_POST['picture_height'];
  }     

  /*
   * Generate missing colors values
   */
  $main['subphase'] = 'complementary colors';
  list($r,$g,$b) = stc_hex2rgb($main['color'][0]);
  if ((( (($r+1)/256)*(($g+1)/256)*(($b+1)/256) ) * 1000 ) < 125 )
       $main['color6'] = lighten( $r, $g, $b, 10);
  else $main['color6'] = darken( $r, $g, $b, 10);
  list($r,$g,$b) = stc_hex2rgb($main['color'][4]);
  if ((( (($r+1)/256)*(($g+1)/256)*(($b+1)/256) ) * 1000 ) < 125 )
       $main['color7'] = lighten( $r, $g, $b, 10);
  else $main['color7'] = darken( $r, $g, $b, 10);
  $main['colorize'] = isset($_POST['colorize']) ? true : false;
  $main['brightness'] = isset($_POST['brightness']) ? true : false;
  $main['contrast'] = isset($_POST['contrast']) ? true : false;
}

// +-----------------------------------------------------------------------+
// |                            Build files                                |
// +-----------------------------------------------------------------------+
$main['phase'] = 'Files building';
if (!function_exists('imagecreatetruecolor') or !function_exists('imagefilter')) {
  array_push($errors, l10n('Some Php Graphic resources are missing.' 
        . ' Sorry for the inconvenience, but Swift Theme Creator couldn\'t work in such case.')); 
  array_push($infos, l10n('This plugin requires PHP 5.2.5 or later' 
        . ' and compiled against graphic library GD 2.0.1 or later.'));
  array_push($infos, l10n('On this server, PHP is:'). phpversion());
  if (function_exists('gd_info')) {
    $GD = gd_info();            
    array_push($infos, l10n('and graphic library is:').$GD['GD Version']);
  }
  else array_push($infos, l10n('graphic library version is not available.'));
  $main['background'] = 'off';
}
if ((isset($_POST['submit']) or $main['simulate'] ) and (!is_adviser()))
{
  /*
   * Go ahead
   */
  $main['subphase'] = 'Mkdir control';
  if (count($errors) == 0) {
    umask(0000);
    @mkdir($themedir, 0705);
    if (!is_dir(  $themedir ))
        array_push($errors,
          l10n('Theme directory creation failure: ' 
          . 'it can\'t be created (for now en attendant la suite 8-) ).'));
    else {
      $main['ldelim'] = '{ldelim}';
      /*
       * Build themeconf.inc.php
       **/
      $main['subphase'] = 'Build themeconf';
      $plugin_tpl = new Template();
      $plugin_tpl->set_filenames(array('themeconf'=>
      STC_PATH . 'themeconf.inc.tpl'));
      $plugin_tpl->assign('main',$main);
      $main['themeconf_inc_php'] = $plugin_tpl->parse('themeconf', true);
      $rfs = stc_newfile( $themedir . '/themeconf.inc.php', 
        $main['themeconf_inc_php'] );
      /*
       * Build mail-css.tpl
       **/  
      $main['subphase'] = 'Build mail-css';
      $plugin_tpl->set_filenames(array('mailcss'=>
      STC_PATH . 'mail-css.tpl2'));
      $plugin_tpl->assign('main',$main);
      $main['mail-css.tpl'] = $plugin_tpl->parse('mailcss', true);  
      $rfs = $rfs && stc_newfile( $themedir . '/mail-css.tpl', 
        $main['mail-css.tpl'] );
      /*
       * Build theme.css
       **/
      $main['subphase'] = 'Build theme';
      $plugin_tpl->set_filenames(array('theme'=> STC_PATH . 'theme.tpl'));
      $plugin_tpl->assign('main',$main);
      $main['theme.css'] = $plugin_tpl->parse('theme', true);  
      $rfs = $rfs && stc_newfile( $themedir . '/theme.css', $main['theme.css'] );
      $internal = stc_hex2rgb($main['color'][2]);
      list($r,$g,$b) = $internal;
      $background = stc_hex2rgb($main['color'][0]);
      list($r2,$g2,$b2) = $background;
      $delta = floor(((array_sum($internal)/3) - (array_sum($background)/3))/5.1);
      /* Brightness is half of difference between colors of internal lnks and bkground */
      /* but if color range is 0-255, resulting brightness range is between -50 and 50 */
      if ($delta > 0) { /* Colorize need a darker color on a dark background */
         $r = floor($r / 5);
         $g = floor($g / 5);
         $b = floor($b / 5);
      }
      if (isset($_POST['background']) and $_POST['background'] == 'random')
      {
        $main['subphase'] = 'Pick random for a pic';
        $main['random'] = mt_rand(12, 4096);
        $result = pwg_query('
        SELECT i.path
          FROM '.CATEGORIES_TABLE.' c, 
               '.IMAGES_TABLE.' i, 
               '.IMAGE_CATEGORY_TABLE.' ic
         WHERE c.status=\'public\'
           AND c.id = ic.category_id
           AND c.id = ' . $main['src_category'] . '
           AND ic.category_id = ' . $main['src_category'] . '
           AND ic.image_id = i.id
         ORDER BY RAND(' . $main['random'] . ')
         LIMIT 0,1');
        if($result) list($main['pic_path']) = mysql_fetch_array($result);
        else $main['pic_path'] = 
             PHPWG_ROOT_PATH . 'plugins/SwiftThemeCreator/simul/header.jpg';
        $main['pic_ext'] = substr($main['pic_path'],strrpos($main['pic_path'],'.')+1);
        if ($main['pic_ext']=='png')
          $img = imagecreatefrompng($main['pic_path']);
        elseif (in_array($main['pic_ext'],array('jpg','jpeg'))) 
                $img = imagecreatefromjpeg($main['pic_path']);
        else $img = imagecreatefromjpeg(PHPWG_ROOT_PATH 
                     . 'plugins/SwiftThemeCreator/simul/header.jpg');
        imagejpeg( $img, $themedir . '/header.jpg', 90 );
        imagedestroy ($img);
      }
      if (isset($_POST['background']) and $_POST['background'] == 'fixed')
      {
        $main['subphase'] = 'Fixed background';
        $hdr = imagecreatetruecolor ($main['picture_width'], $main['picture_height']);
        imagecolorset ( $hdr, 0, $r2, $g2, $b2 );
        if ($extension == 'png') $img = imagecreatefrompng($main['picture_url']);
        else $img = imagecreatefromjpeg($main['picture_url']);
        imagecopymerge ( $hdr, $img, 0, 0, 0, 0, $main['picture_width'], $main['picture_height'], 60 );
        imagedestroy ($img);
        if ($main['colorize']) imagefilter($hdr, IMG_FILTER_COLORIZE, $r, $g, $b);
        if ($main['brightness']) imagefilter($hdr, IMG_FILTER_BRIGHTNESS, $delta);
        if ($main['contrast']) imagefilter($hdr, IMG_FILTER_CONTRAST, 20);
        imagejpeg( $hdr, $themedir . '/header.jpg', 90 );
          imagedestroy ($hdr);
      }
      /*
       * Build background image for titrePage or definition list (in #menubar)
       **/
      $main['subphase'] = 'Headbars background'; 
      $hdr = imagecreatetruecolor (1, 38);
      imagecolorset ( $hdr, 0, $r2, $g2, $b2 );
      $img = imagecreatefrompng(STC_PATH . '/titrePage-bg.png');
      imagecopymerge ( $hdr, $img, 0, 0, 0, 0, 1, 38, 60 );
      imagedestroy ($img);
      if ($main['colorize']) imagefilter($hdr, IMG_FILTER_COLORIZE, $r, $g, $b);
      if ($main['brightness']) imagefilter($hdr, IMG_FILTER_BRIGHTNESS, $delta);
      if ($main['contrast']) imagefilter($hdr, IMG_FILTER_CONTRAST, 20);
      imagepng( $hdr, $themedir . '/stc.png', 9 );
      imagedestroy ($hdr);

      /*
       * Errors and cleaning or Congratulations
       **/
      $main['phase'] = 'Congratulation';
      $main['subphase'] = 'cleaning';
      if ($rfs == false) {
        array_push($errors,
          l10n('Theme files creation failure: theme should be deleted.'));
        if ($cleaning) {
          @unlink( $themedir . '/header.jpg' );
          @unlink( $themedir . '/stc.png' );
          @unlink( $themedir . '/themeconf.inc.php' );
          @unlink( $themedir . '/mail-css.tpl' );
          @unlink( $themedir . '/theme.css' );
          @rmdir( $themedir );
        }
      }
      elseif (!$main['simulate']) {
        array_push($infos,
       '['.$main['newtpl'] . '/' . $main['newtheme'].'] : '
       .l10n('Congratulation! You have got(/ten) a new available theme.')); 
        @copy( $themedir . '/header.jpg', PHPWG_ROOT_PATH . 'plugins/SwiftThemeCreator/simul/header.jpg');
        @copy( $themedir . '/stc.png', PHPWG_ROOT_PATH . 'plugins/SwiftThemeCreator/simul/stc.png');
        @copy( $themedir . '/themeconf.inc.php', PHPWG_ROOT_PATH . 'plugins/SwiftThemeCreator/simul/themeconf.inc.php');
        @copy( $themedir . '/mail-css.tpl', PHPWG_ROOT_PATH . 'plugins/SwiftThemeCreator/simul/mail-css.tpl');
        @copy( $themedir . '/theme.css', PHPWG_ROOT_PATH . 'plugins/SwiftThemeCreator/simul/theme.css');
      }
    }
  }

  // TODO       ********   theSwiftHeader itself   *********
  
  $swift_theme_creator->save_theme_config();  
}

// +-----------------------------------------------------------------------+
// |                            reset values
// +-----------------------------------------------------------------------+
if (isset($_POST['reset']) and (!is_adviser())) {
  $main = array();
  init_main($main);
  $swift_theme_creator->theme_config = $main;
  $swift_theme_creator->save_theme_config();  
  redirect( get_admin_plugin_menu_link(dirname(__FILE__).'/theme_creator.php'));
}

// Don't forget to re-read because some statements are superfluous
 
// +-----------------------------------------------------------------------+
// |                            template initialization
// +-----------------------------------------------------------------------+
$template->set_filenames(array(
    'plugin_admin_content' => dirname(__FILE__) . '/theme_creator.tpl'));
$template->append('head_elements',
   '<script type="text/javascript" 
        src="./plugins/SwiftThemeCreator/farbtastic/farbtastic.js"></script>
<link rel="stylesheet" type="text/css"
      href="./plugins/SwiftThemeCreator/farbtastic/farbtastic.css" />
<style type="text/css" media="screen">
.colorwell { border: 3px double #F30; width: 6em; 
  text-align: center; cursor: pointer; }
body .colorwell-selected { border: 3px double #F36; font-weight: bold; }
.radio { margin: 0 10px 0 50px; }
</style>'
    );
$template->assign('radio_options',
 array(
  'true' => l10n('Yes'),
  'false' => l10n('No')));
$template->assign('template_options', $available_templates);
$template->assign('background_options',
  array(
    'off' => l10n('No'),
    'random' => l10n('24H Random'),
    'fixed' => l10n('Fixed URL'),
  ));
$template->assign('background_mode_options',
  array(
    'as' => l10n('As is'),
    'crop' => l10n('Truncated'),
    'sized' => l10n('Resized'),
  ));
if (count($errors) != 0) $template->assign('errors', $errors);
if (count($infos) != 0) $template->assign('infos', $infos);
/* Restore Main values */
$template->assign('main', $main);
$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');
$swift_theme_creator->theme_config = $main;
$swift_theme_creator->save_theme_config();
?>