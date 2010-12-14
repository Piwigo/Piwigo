<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
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

$active_themes = array();
$inactive_themes = array();

foreach ($themes->fs_themes as $theme_id => $fs_theme)
{
  if ($theme_id == 'default')
  {
    continue;
  }

  if (in_array($theme_id, $db_theme_ids))
  {
    $fs_theme['deactivable'] = true;

    if (count($db_theme_ids) <= 1)
    {
      $fs_theme['deactivable'] = false;
      $fs_theme['deactivate_tooltip'] = l10n('Impossible to deactivate this theme, you need at least one theme.');
    }

    if ($theme_id == $default_theme)
    {
      $fs_theme['is_default'] = true;
      array_unshift($active_themes, $fs_theme);
    }
    else
    {
      $fs_theme['is_default'] = false;
      array_push($active_themes, $fs_theme);
    }
  }
  else
  {
    // is the theme "activable" ?
    if (isset($fs_theme['activable']) and !$fs_theme['activable'])
    {
      $fs_theme['activate_tooltip'] = l10n('This theme was not designed to be directly activated');
    }
    else
    {
      $fs_theme['activable'] = true;
    }

    $missing_parent = $themes->missing_parent_theme($theme_id);
    if (isset($missing_parent))
    {
      $fs_theme['activable'] = false;

      $fs_theme['activate_tooltip'] = sprintf(
        l10n('Impossible to activate this theme, the parent theme is missing: %s'),
        $missing_parent
        );
    }

    // is the theme "deletable" ?
    $children = $themes->get_children_themes($theme_id);

    $fs_theme['deletable'] = true;

    if (count($children) > 0)
    {
      $fs_theme['deletable'] = false;

      $fs_theme['delete_tooltip'] = sprintf(
        l10n('Impossible to delete this theme. Other themes depends on it: %s'),
        implode(', ', $children)
        );
    }

    array_push($inactive_themes, $fs_theme);
  }
}

$template->assign(
    array(
      'activate_baseurl' => $base_url.'&amp;action=activate&amp;theme=',
      'deactivate_baseurl' => $base_url.'&amp;action=deactivate&amp;theme=',
      'set_default_baseurl' => $base_url.'&amp;action=set_default&amp;theme=',
      'delete_baseurl' => $base_url.'&amp;action=delete&amp;theme=',

      'active_themes' => $active_themes,
      'inactive_themes' => $inactive_themes,
    )
  );


$themes->set_tabsheet($page['page']);
$template->set_filenames(array('themes' => 'themes_installed.tpl'));
$template->assign_var_from_handle('ADMIN_CONTENT', 'themes');
?>
