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

/**
 * Define replacement conditions for each template from template-extension
 * (template called "replacer").
 *
 * "original template" from ./template/yoga (or any other than yoga)
 * will be replaced by a "replacer" if the replacer is linked to this "original template"
 * (and optionally, when the requested URL contains an "optional URL keyword").
 *
 * "Optional URL keywords" are those you can find after the module name in URLs.
 *
 * Therefore "Optional URL keywords" can be an active "permalink" 
 * (see permalinks in our documentation for further explanation).
 */

// +-----------------------------------------------------------------------+
//                            initialization                              |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }
include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
check_status(ACCESS_ADMINISTRATOR);

$tpl_extension = isset($conf['extents_for_templates']) ?
      unserialize($conf['extents_for_templates']) : array();
$new_extensions = get_extents(); 

/* Selective URLs keyword */
$relevant_parameters = array(
    '----------',
    'category',
    'favorites',
    'most_visited',
    'best_rated',
    'recent_pics',
    'recent_cats',
    'created-monthly-calendar',
    'posted-monthly-calendar',
    'search',
    'flat',
    'list',); /* <=> Random */
  $query = '
SELECT permalink
  FROM '.CATEGORIES_TABLE.'
  WHERE permalink IS NOT NULL
';

/* Add active permalinks */ 
$permalinks = array_from_query($query, 'permalink');
$relevant_parameters = array_merge($relevant_parameters, $permalinks);

/* Link all supported templates to their respective handle */
$eligible_templates = array(
    '----------'                 => 'N/A',
    'about.tpl'                  => 'about',
    'footer.tpl'                 => 'tail',
    'header.tpl'                 => 'header',
    'identification.tpl'         => 'identification',
    'index.tpl'                  => 'index',
    'mainpage_categories.tpl'    => 'index_category_thumbnails',
    'menubar.tpl'                => 'menubar',
    'menubar_categories.tpl'     => 'mbCategories',
    'menubar_identification.tpl' => 'mbIdentification',
    'menubar_links.tpl'          => 'mbLinks',
    'menubar_menu.tpl'           => 'mbMenu',
    'menubar_specials.tpl'       => 'mbSpecials',
    'menubar_tags.tpl'           => 'mbTags',
    'navigation_bar.tpl'         => 'navbar',
    'nbm.tpl'                    => 'nbm',
    'notification.tpl'           => 'notification',
    'picture.tpl'                => 'picture',
    'picture_content.tpl'        => 'default_content',
    'popuphelp.tpl'              => 'popuphelp',
    'profile.tpl'                => 'profile',
    'profile_content.tpl'        => 'profile_content',
    'redirect.tpl'               => 'redirect',
    'register.tpl'               => 'register',
    'search.tpl'                 => 'search',
    'search_rules.tpl'           => 'search_rules',
    'slideshow.tpl'              => 'slideshow',
    'tags.tpl'                   => 'tags',
    'thumbnails.tpl'             => 'index_thumbnails',
    'upload.tpl'                 => 'upload',);

$flip_templates = array_flip($eligible_templates);

$available_templates = array_merge(
  array('N/A' => '----------'),
  get_dirs(PHPWG_ROOT_PATH.'template'));

// +-----------------------------------------------------------------------+
// |                            selected templates                         |
// +-----------------------------------------------------------------------+

if (isset($_POST['submit']) and !is_adviser())
{
  $replacements = array();
  $i = 0;
  while (isset($_POST['reptpl'][$i]))
  {
    $newtpl = $_POST['reptpl'][$i];
    $original = $_POST['original'][$i];
    $handle = $eligible_templates[$original];
    $url_keyword = $_POST['url'][$i];
    if ($url_keyword == '----------') $url_keyword = 'N/A';
    $bound_tpl = $_POST['bound'][$i];
    if ($bound_tpl == '----------') $bound_tpl = 'N/A';
    if ($handle != 'N/A')
    {
      $replacements[$newtpl] = array($handle, $url_keyword, $bound_tpl);
    }
    $i++;
  }
  $conf['extents_for_templates'] = serialize($replacements);
  $tpl_extension = $replacements;
  /* ecrire la nouvelle conf */
  $query = "
UPDATE ".CONFIG_TABLE."
  SET value = '". $conf['extents_for_templates'] ."'
WHERE param = 'extents_for_templates';";
  if (pwg_query($query))
  {
    array_push($page['infos'], 
      l10n('Templates recorded.'));
  }
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

/* Clearing (remove old extents, add new ones) */
foreach ($tpl_extension as $file => $conditions)
{
  if ( !in_array($file,$new_extensions) ) unset($tpl_extension[$file]);
  else $new_extensions = array_diff($new_extensions,array($file)); 
}
foreach ($new_extensions as $file)
{
  $tpl_extension[$file] = array('N/A', 'N/A', 'N/A');
}

$template->set_filenames(array('extend_for_templates'
     => 'extend_for_templates.tpl'));

$base_url = PHPWG_ROOT_PATH.'admin.php?page=extend_for_templates';

$template->assign(
  array(
    'U_HELP' => get_root_url().'popuphelp.php?page=extend_for_templates',
    ));
ksort($tpl_extension);
foreach ($tpl_extension as $file => $conditions)    
{
  $handle = $conditions[0];
  $url_keyword = $conditions[1];
  $bound_tpl = $conditions[2];
  {  
  $template->append('extents',
    array(
      'replacer'       => $file,
      'url_parameter'  => $relevant_parameters,
      'original_tpl'   => array_keys($eligible_templates),
      'bound_tpl'          => $available_templates,
      'selected_tpl'   => $flip_templates[$handle],
      'selected_url'   => $url_keyword,
      'selected_bound' => $bound_tpl,)
      );
  }
}
// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'extend_for_templates');
?>