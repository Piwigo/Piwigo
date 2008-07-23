<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
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

/* Ajouter le lien au menu de l'admin */
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');
if (!defined('IN_ADMIN') or !IN_ADMIN) die('Hacking attempt!');

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


$errors = array();
$infos = array();
$available_templates = array();
$template_dir = PHPWG_ROOT_PATH.'template';
foreach (get_dirs($template_dir) as $dir)
{
  array_push($available_templates, $dir);
}

// +-----------------------------------------------------------------------+
// |                            selected templates                         |
// +-----------------------------------------------------------------------+
if (!isset($swift_theme_creator)) $swift_theme_creator = new ThemeCreator();
$swift_theme_creator->reload();
$main = $swift_theme_creator->theme_config;

if (isset($_POST['submit']) and (!is_adviser()))
{
  // 1 - Theme name control
  $main['newtheme'] = strip_tags($_POST['new_theme']);
  if ( !preg_match('/^[a-z0-9-_]{1,8}$/', $main['newtheme']) ) 
      array_push($errors,
         l10n('Invalid theme name: 1 to 8 lowercase alphanumeric characters'
         . ' including "-" and "_".')); 

  // 2 - Colours control
  $main['color'] = array($_POST['color1'], $_POST['color2'], 
                         $_POST['color3'], $_POST['color4'], 
                         $_POST['color5']);
  $colors = $main['color'][0] . $main['color'][1] . $main['color'][2] 
          . $main['color'][3] . $main['color'][4];
  if ( !preg_match('/^(#?([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})){5}$/', $colors) ) 
      array_push($errors,
         l10n('Invalid color code: 3 or 6 hexadecimal characters, preceded' 
         . ' or not by "#"')); 

  // 2.1 - Background and text control
  list($r1,$g1,$b1) = stc_hex2rgb($main['color'][0]);
  list($r2,$g2,$b2) = stc_hex2rgb($main['color'][1]);
  // Formula for converting RGB values to YIQ values as perceived brightness difference.
  // Background and text "brightness" difference control:
  $dif = abs( ( (($r1*299)+($g1*587)+($b1*114)) / 1000 )
          - ( (($r2*299)+($g2*587)+($b2*114)) / 1000 ));
  if ( $dif < 125 )
      array_push($errors,
       l10n('Insufficient brightness difference between text and background. dif=') . $dif); 
  // Background and text "colour" difference control:
  $dif = (max($r1, $r2) - min($r1, $r2)) 
     + (max($g1, $g2) - min($g1, $g2)) 
     + (max($b1, $b2) - min($b1, $b2));
  if ( $dif < 200 )
    array_push($errors,
       l10n('Insufficient colour difference between text and background. dif=') . $dif); 

  // 2.2 - Background and Internal links control
  list($r1,$g1,$b1) = stc_hex2rgb($main['color'][0]);
  list($r2,$g2,$b2) = stc_hex2rgb($main['color'][2]);
  // Background and Internal links "brightness" difference control:
  $dif = abs( ( (($r1*299)+($g1*587)+($b1*114)) / 1000 )
          - ( (($r2*299)+($g2*587)+($b2*114)) / 1000 ));
  if ( $dif < 125 )
      array_push($errors,
       l10n('Insufficient brightness difference between Internal links and background. dif=') . $dif); 
  // Background and Internal links "colour" difference control:
  $dif = (max($r1, $r2) - min($r1, $r2)) 
     + (max($g1, $g2) - min($g1, $g2)) 
     + (max($b1, $b2) - min($b1, $b2));
  if ( $dif < 200 )
    array_push($errors,
       l10n('Insufficient colour difference between Internal links and background. dif=') . $dif); 

  // 3 - Directory control
  $main['templatedir'] = PHPWG_ROOT_PATH . 'template/' 
               . $available_templates[$_POST['template']];
  $main['newtpl'] = $available_templates[$_POST['template']];
  $themedir = $main['templatedir'] . '/' . $main['newtheme'];
  if (is_dir(  $themedir )) 
    array_push($errors,
       '['.$themedir.'] : '.l10n('Invalid theme: This theme exists already (no override available).')); 
  elseif (!is_writable($main['templatedir']))
    array_push($errors,
       '['.$main['templatedir'].'] : '.l10n('no_write_access'));

  // 4 - Picture URL control
  if ( $_POST['background'] == 'fixed' and (is_dir($_POST['picture_url'])
      or !is_file($_POST['picture_url'])) )
    array_push($errors,
       l10n('Header picture is not found, check its path and name.')); 
  
  // 5 - Expected Width and Height limits control
  if ( !(ctype_digit($_POST['picture_width']) and $_POST['picture_width'] > 11 
       and $_POST['picture_width'] < 4097 ) )
    array_push($errors,
       '['.$_POST['picture_width'].'] : ' 
       . l10n('incorrect width value [12-4096].')); 
  if ( !(ctype_digit($_POST['picture_height']) and $_POST['picture_height'] > 11 
       and $_POST['picture_height'] < 201 ) )
    array_push($errors,
       '['.$_POST['picture_height'].'] : '
       . l10n('incorrect width value [12-200].')); 
       
  // 6 - Generate missing colors values
  list($r1,$g1,$b1) = stc_hex2rgb($main['color'][0]);
  if ((( (($r1+1)/256)*(($g1+1)/256)*(($b1+1)/256) ) * 1000 ) < 125 )
       $main['color6'] = lighten( $r1, $g1, $b1, 10);
  else $main['color6'] = darken( $r1, $g1, $b1, 10);
  list($r1,$g1,$b1) = stc_hex2rgb($main['color'][4]);
  if ((( (($r1+1)/256)*(($g1+1)/256)*(($b1+1)/256) ) * 1000 ) < 125 )
       $main['color7'] = lighten( $r1, $g1, $b1, 10);
  else $main['color7'] = darken( $r1, $g1, $b1, 10);

  // Go ahead 
  if (count($errors) == 0) {
    umask(0000);
    // mkdir($themedir, 0777);
    if (!is_dir(  $themedir ))
        array_push($errors,
          l10n('Theme directory creation failure: it can\'t be created (for now en attendant la suite 8-) ).'));
  }

  $main['ldelim'] = '{ldelim}';
  /*
   * Build themeconf.inc.php
   **/
  $plugin_tpl = new Template();
  $plugin_tpl->set_filenames(array('themeconf'=>
  dirname(__FILE__) . '/themeconf.inc.tpl'));
  $plugin_tpl->assign('main',$main);
  $main['themeconf_inc_php'] = $plugin_tpl->parse('themeconf', true);
  /*
   * Build mail-css.tpl
   **/  
  $plugin_tpl->set_filenames(array('mailcss'=>
  dirname(__FILE__) . '/mail-css.tpl2'));
  $plugin_tpl->assign('main',$main);
  $main['mail-css.tpl'] = $plugin_tpl->parse('mailcss', true);  

  // Smarty trace
  $plugin_tpl->assign('main',$main);
  
  // Interesting Graphic Charter
  // http://accessites.org/site/2006/08/visual-vs-structural/

  /*
   * Build background image for titrePage or definition list (in #menubar)
   **/
  if (function_exists('imagecreatefrompng'))
  {
    $img = imagecreatefrompng(dirname(__FILE__) . '/titrePage-bg.png');
    $dest = imagecreate(1, 64);
    for ($i=0; $i<256; $i++) {
      imagecolorallocate($dest, $i, $i, $i); 
    }
    imagecopy($dest, $img, 0, 0, 0, 0, 1, 64);
    list($r1,$g1,$b1) = stc_hex2rgb($main['color'][4]);
    for ($i = 0; $i < 256; $i++) {
      imagecolorset($dest, $i, min($i * $r1 / 255, 255), 
                     min($i * $g1 / 255, 255), 
                     min($i * $b1 / 255, 255));
    }
    // to be tested imagecopymerge($dest,$img,0,0,0,0,1,64,33);
    
    // Uncomment to create the header stc.png
    // imagepng( $dest, dirname(__FILE__) . '/stc.png', 9 );
    imagedestroy ($img);
    imagedestroy ($dest);
  }

  
  /* All logic is there
   * On "todo" : Create files and uncomment some previous statements.
   */


  $swift_theme_creator->save_theme_config();  
}

// +-----------------------------------------------------------------------+
// |                            reset values
// +-----------------------------------------------------------------------+

// To be implemented delete $main save and redirect

// Don't forget to re-read because some statements are superfluous
 
// +-----------------------------------------------------------------------+
// |                            template initialization
// +-----------------------------------------------------------------------+
$template->set_filenames(array(
    'plugin_admin_content' => dirname(__FILE__) . '/theme_creator.tpl'));
$template->append('head_elements',
  '<script type="text/javascript" src="./plugins/SwiftThemeCreator/farbtastic/farbtastic.js"></script>
<link rel="stylesheet" href="./plugins/SwiftThemeCreator/farbtastic/farbtastic.css" type="text/css" />
<style type="text/css" media="screen">
.colorwell { border: 3px double #F30; width: 6em; text-align: center; cursor: pointer; }
body .colorwell-selected { border: 3px double #F36; font-weight: bold; }
.radio { margin: 0 10px 0 50px; }
</style>'
    );

/* Templates */
$template->assign('template_options', $available_templates);
if (!isset($main['template_options'])) $main['template_options'] = 0; 

/* New theme */
if (isset($_POST['new_theme'])) $main['new_theme'] = $_POST['new_theme'];

/* Colors */
if (isset($_POST['color1'])) $main['color1'] = $_POST['color1'];
if (isset($_POST['color2'])) $main['color2'] = $_POST['color2'];
if (isset($_POST['color3'])) $main['color3'] = $_POST['color3'];
if (isset($_POST['color4'])) $main['color4'] = $_POST['color4'];
if (isset($_POST['color5'])) $main['color5'] = $_POST['color5'];
if (!isset($main['color1'])) $main['color1'] = '#111111'; 
if (!isset($main['color2'])) $main['color2'] = '#EEEEEE'; 
if (!isset($main['color3'])) $main['color3'] = '#FF7700'; 
if (!isset($main['color4'])) $main['color4'] = '#FF3333'; 
if (!isset($main['color5'])) $main['color5'] = '#FF3363'; 

/* header */
if (isset($_POST['background'])) $main['background'] = $_POST['background'];
if (!isset($main['background'])) $main['background'] = 'off'; 
$template->assign('background_options',
  array(
    'off' => l10n('No'),
    'random' => l10n('24H Random'),
    'fixed' => l10n('Fixed URL'),
  ));

$query = '
SELECT id,name,uppercats,global_rank
  FROM ' . CATEGORIES_TABLE . ';';
display_select_cat_wrapper($query,array(),'src_category');
if (isset($_POST['src_category'])) $main['src_category'] = 
    $_POST['src_category'];

$main['picture_url'] = PHPWG_ROOT_PATH . 'plugins/SwiftThemeCreator/sample.jpg';
if (isset($swift_theme_creator->picture_url)) 
    $main['picture_url'] = $swift_theme_creator->picture_url;
if (isset($_POST['picture_url'])) $main['picture_url'] = $_POST['picture_url'];

if (isset($_POST['picture_width'])) $main['picture_width'] = $_POST['picture_width'];
if (!isset($main['picture_width'])) $main['picture_width'] = 2048; 
if (isset($_POST['picture_height'])) $main['picture_height'] = $_POST['picture_height'];
if (!isset($main['picture_height'])) $main['picture_height'] = 100; 

if (isset($_POST['background_mode'])) 
      $main['background_mode'] = $_POST['background_mode'];
if (!isset($main['background_mode'])) $main['background_mode'] = 'as'; 
$template->assign('background_mode_options',
  array(
    'as' => l10n('As is'),
    'crop' => l10n('Truncated'),
    'sized' => l10n('Resized'),
  ));
if (count($errors) != 0) $template->assign('errors', $errors);
/* Restore Main values */
$template->assign('main', $main);
$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');
$swift_theme_creator->theme_config = $main;
$swift_theme_creator->save_theme_config();
?>
