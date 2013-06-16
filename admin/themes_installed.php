<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2013 Piwigo Team                  http://piwigo.org |
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

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/themes.class.php');

$base_url = get_root_url().'admin.php?page='.$page['page'];

$themes = new themes();

// +-----------------------------------------------------------------------+
// |                          perform actions                              |
// +-----------------------------------------------------------------------+

if (isset($_GET['action']) and isset($_GET['theme']))
{
  $page['errors'] = $themes->perform_action($_GET['action'], $_GET['theme']);

  if (empty($page['errors']))
  {
    if ($_GET['action'] == 'activate' or $_GET['action'] == 'deactivate')
    {
      $template->delete_compiled_templates();
    }
    redirect($base_url);
  }
}

// +-----------------------------------------------------------------------+
// |                     start template output                             |
// +-----------------------------------------------------------------------+

$themes->sort_fs_themes();

$default_theme = get_default_theme();

$db_themes = $themes->get_db_themes();
$db_theme_ids = array();
foreach ($db_themes as $db_theme)
{
  array_push($db_theme_ids, $db_theme['id']);
}

$tpl_themes = array();

foreach ($themes->fs_themes as $theme_id => $fs_theme)
{
  if ($theme_id == 'default')
  {
    continue;
  }
  
  $tpl_theme = array(
    'ID' => $theme_id,
    'NAME' => $fs_theme['name'],
    'VISIT_URL' => $fs_theme['uri'],
    'VERSION' => $fs_theme['version'],
    'DESC' => $fs_theme['description'],
    'AUTHOR' => $fs_theme['author'],
    'AUTHOR_URL' => @$fs_theme['author uri'],
    'PARENT' => @$fs_theme['parent'],
    'SCREENSHOT' => $fs_theme['screenshot'],
    'IS_MOBILE' => $fs_theme['mobile'],
    'ADMIN_URI' => @$fs_theme['admin_uri'],
    );

  if (in_array($theme_id, $db_theme_ids))
  {
    $tpl_theme['STATE'] = 'active';
    $tpl_theme['DEACTIVABLE'] = true;

    if (count($db_theme_ids) <= 1)
    {
      $tpl_theme['DEACTIVABLE'] = false;
      $tpl_theme['DEACTIVATE_TOOLTIP'] = l10n('Impossible to deactivate this theme, you need at least one theme.');
    }
    
    $tpl_theme['IS_DEFAULT'] = ($theme_id == $default_theme);
  }
  else
  {
    $tpl_theme['STATE'] = 'inactive';
    
    // is the theme "activable" ?
    if (isset($fs_theme['activable']) and !$fs_theme['activable'])
    {
      $tpl_theme['ACTIVABLE'] = false;
      $tpl_theme['ACTIVABLE_TOOLTIP'] = l10n('This theme was not designed to be directly activated');
    }
    else
    {
      $tpl_theme['ACTIVABLE'] = true;
    }

    $missing_parent = $themes->missing_parent_theme($theme_id);
    if (isset($missing_parent))
    {
      $tpl_theme['ACTIVABLE'] = false;

      $tpl_theme['ACTIVABLE_TOOLTIP'] = sprintf(
        l10n('Impossible to activate this theme, the parent theme is missing: %s'),
        $missing_parent
        );
    }

    // is the theme "deletable" ?
    $children = $themes->get_children_themes($theme_id);

    $tpl_theme['DELETABLE'] = true;

    if (count($children) > 0)
    {
      $tpl_theme['DELETABLE'] = false;

      $tpl_theme['DELETE_TOOLTIP'] = sprintf(
        l10n('Impossible to delete this theme. Other themes depends on it: %s'),
        implode(', ', $children)
        );
    }
  }
  
  array_push($tpl_themes, $tpl_theme);
}

// sort themes by state then by name
function cmp($a, $b)
{ 
  $s = array('active' => 0, 'inactive' => 1);
  
  if (@$a['IS_DEFAULT']) return -1;
  if (@$b['IS_DEFAULT']) return 1;
  
  if($a['STATE'] == $b['STATE'])
    return strcasecmp($a['NAME'], $b['NAME']); 
  else
    return $s[$a['STATE']] >= $s[$b['STATE']]; 
}
usort($tpl_themes, 'cmp');

$template->assign(
    array(
      'activate_baseurl' => $base_url.'&amp;action=activate&amp;theme=',
      'deactivate_baseurl' => $base_url.'&amp;action=deactivate&amp;theme=',
      'set_default_baseurl' => $base_url.'&amp;action=set_default&amp;theme=',
      'delete_baseurl' => $base_url.'&amp;action=delete&amp;theme=',

      'tpl_themes' => $tpl_themes,
    )
  );


$template->set_filenames(array('themes' => 'themes_installed.tpl'));
$template->assign_var_from_handle('ADMIN_CONTENT', 'themes');
?>
