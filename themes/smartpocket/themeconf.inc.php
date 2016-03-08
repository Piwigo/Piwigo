<?php
/*
Theme Name: Smart Pocket
Version: auto
Description: Mobile theme.
Theme URI: http://piwigo.org/ext/extension_view.php?eid=599
Author: P@t
Author URI: http://piwigo.org
*/

$themeconf = array(
  'mobile' => true,
);

// Need upgrade?
global $conf;
include(PHPWG_THEMES_PATH.'smartpocket/admin/upgrade.inc.php');

load_language('theme.lang', PHPWG_THEMES_PATH.'smartpocket/');


// Redirect if page is not compatible with mobile theme
/*if (!in_array(script_basename(), array('index', 'register', 'profile', 'identification', 'ws', 'admin')))
  redirect(duplicate_index_url());
*/


class SPThumbPicker
{
  var $candidates;
  var $default;
  var $height;
  
  function init($height)
  {
    $this->candidates = array();
    foreach( ImageStdParams::get_defined_type_map() as $params)
    {
      if ($params->max_height() < $height || $params->sizing->max_crop)
        continue;
      if ($params->max_height() > 3*$height)
        break;
      $this->candidates[] = $params;
    }
    $this->default = ImageStdParams::get_custom($height*3, $height, 1, 0, $height );
    $this->height = $height;
  }
  
  function pick($src_image)
  {
    $ok = false;
    foreach($this->candidates as $candidate)
    {
      $deriv = new DerivativeImage($candidate, $src_image);
      $size = $deriv->get_size();
      if ($size[1]>=$row_height-2)
      {
        $ok = true;
        break;
      }
    }
    if (!$ok)
    {
      $deriv = new DerivativeImage($this->default, $src_image);
    }
    return $deriv;
  }
}

//Retrive all pictures on thumbnails page
add_event_handler('loc_index_thumbnails_selection', 'sp_select_all_thumbnails');

function sp_select_all_thumbnails($selection)
{
  global $page, $template;

  $template->assign('page_selection', array_flip($selection));
  $template->assign('thumb_picker', new SPThumbPicker() );
  return $page['items'];
}

// Retrive all categories on thumbnails page
add_event_handler('loc_end_index_category_thumbnails', 'sp_select_all_categories');

function sp_select_all_categories($selection)
{
  global $tpl_thumbnails_var;
  return $tpl_thumbnails_var;
}

// Get better derive parameters for screen size
$type = IMG_LARGE;
if (!empty($_COOKIE['screen_size']))
{
  $screen_size = explode('x', $_COOKIE['screen_size']);
  foreach (ImageStdParams::get_all_type_map() as $type => $map)
  {
    if (max($map->sizing->ideal_size) >= max($screen_size) and min($map->sizing->ideal_size) >= min($screen_size))
      break;
  }
}

$this->assign('picture_derivative_params', ImageStdParams::get_by_type($type));
$this->assign('thumbnail_derivative_params', ImageStdParams::get_by_type(IMG_SQUARE));

//------------------------------------------------------------- mobile version & theme config
add_event_handler('init', 'mobile_link');

function mobile_link()
{
  global $template, $conf;
  $config = safe_unserialize( $conf['smartpocket'] );
  $template->assign( 'smartpocket', $config );
  if ( !empty($conf['mobile_theme']) && (get_device() != 'desktop' || mobile_theme()))
  {
    $template->assign(array(
                            'TOGGLE_MOBILE_THEME_URL' => add_url_params(htmlspecialchars($_SERVER['REQUEST_URI']),array('mobile' => mobile_theme() ? 'false' : 'true')),
      ));
  }
}


if ( !function_exists( 'add_menu_on_public_pages' ) ) { 
  if ( defined('IN_ADMIN') and IN_ADMIN ) return false; 
  add_event_handler('loc_after_page_header', 'add_menu_on_public_pages', 20); 

  function  add_menu_on_public_pages() { 
    if ( function_exists( 'initialize_menu') ) return false; # The current page has already the menu  
    global $template, $page, $conf; 
    if ( isset($page['body_id']) and $page['body_id']=="thePicturePage" ) 
    {	 	 
      $template->set_filenames(array( 
            'add_menu_on_public_pages' => dirname(__FILE__) . '/template/add_menu_on_public_pages.tpl', 
      )); 
      include_once(PHPWG_ROOT_PATH.'include/menubar.inc.php'); 
      $template->parse('add_menu_on_public_pages');
    }
     
     
  } 
} 


?>
