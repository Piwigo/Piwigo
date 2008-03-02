<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | file          : $Id$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
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

$template->set_filenames(array('plugins' => 'admin/plugins_list.tpl'));

//----------------------------------------------------------------sort options
$order = isset($_GET['order']) ? $_GET['order'] : 'name';

$template->assign('order', 
    array(htmlentities($my_base_url.'&order=name') => l10n('Name'),
          htmlentities($my_base_url.'&order=status') => l10n('Status')
    )
  );
          
$template->assign('selected', htmlentities($my_base_url.'&order=').$order);


// +-----------------------------------------------------------------------+
// |                     start template output                             |
// +-----------------------------------------------------------------------+

if ($order == 'status')
{
  $fs_plugins = sort_plugins_by_state($fs_plugins, $db_plugins_by_id);
}

foreach($fs_plugins as $plugin_id => $fs_plugin)
{
  $display_name = $fs_plugin['name'];
  if (!empty($fs_plugin['uri']))
  {
    $display_name = '<a href="' . $fs_plugin['uri']
                    . '" onclick="window.open(this.href); return false;">'
                    . $display_name . '</a>';
  }
  $desc = $fs_plugin['description'];
  if (!empty($fs_plugin['author']))
  {
    $desc .= ' (<em>';
    if (!empty($fs_plugin['author uri']))
    {
      $desc .= '<a href="' . $fs_plugin['author uri'] . '">'
               . $fs_plugin['author'] . '</a>';
    }
    else
    {
      $desc .= $fs_plugin['author'];
    }
    $desc .= '</em>)';
  }
  $tpl_plugin =
    array('NAME' => $display_name,
          'VERSION' => $fs_plugin['version'],
          'DESCRIPTION' => $desc);

  $action_url = htmlentities($my_base_url) . '&amp;plugin=' . $plugin_id;

  if (isset($db_plugins_by_id[$plugin_id]))
  { 
    switch ($db_plugins_by_id[$plugin_id]['state'])
    {
      case 'active':
        $tpl_plugin['actions'][] = 
            array('U_ACTION' => $action_url . '&amp;action=deactivate',
                  'L_ACTION' => l10n('Deactivate'));
        break;

      case 'inactive':
        $tpl_plugin['actions'][] = 
            array('U_ACTION' => $action_url . '&amp;action=activate',
                  'L_ACTION' => l10n('Activate'));
        $tpl_plugin['actions'][] = 
            array('U_ACTION' => $action_url . '&amp;action=uninstall',
                  'L_ACTION' => l10n('Uninstall'),
                  'CONFIRM' => l10n('Are you sure?'));
        break;
    }
  }
  else
  {
    $tpl_plugin['actions'][] = 
        array('U_ACTION' => $action_url . '&amp;action=install',
              'L_ACTION' => l10n('Install'),
              'CONFIRM' => l10n('Are you sure?'));
    $tpl_plugin['actions'][] = 
        array('U_ACTION' => $action_url . '&amp;action=delete',
                'L_ACTION' => l10n('plugins_delete'),
                'CONFIRM' => l10n('plugins_confirm_delete'));
  }
  $template->append('plugins', $tpl_plugin);
}

$missing_plugin_ids = array_diff(
    array_keys($db_plugins_by_id), array_keys($fs_plugins)
    );

foreach($missing_plugin_ids as $plugin_id)
{
  $action_url = $my_base_url.'&amp;plugin='.$plugin_id;

  $template->append( 'plugins',
      array(
        'NAME' => $plugin_id,
        'VERSION' => $db_plugins_by_id[$plugin_id]['version'],
        'DESCRIPTION' => "ERROR: THIS PLUGIN IS MISSING BUT IT IS INSTALLED! UNINSTALL IT NOW !",
        'actions' => array ( array (
              'U_ACTION' => $action_url . '&amp;action=uninstall',
              'L_ACTION' => l10n('Uninstall'),
          ) )
        )
     );
}

?>