<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
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

/*
 * Build TabSheet and assign this content to current page
 *
 * Uses $page['tabsheet'], it's an array of array
 *
 * $page['tabsheet'] description:
 *  $page['tabsheet']'[url'] : Tab link
 *  $page['tabsheet']['Caption'] : Tab caption
 *  $page['tabsheet']['selected'] : Is the selected tab (default value false)
 *
 * Fill {TABSHEET} with HTML code for tabshette
 * Fill {U_TABSHEET_TITLE} with formated caption of the selected tab
 */

function template_assign_tabsheet()
{
  global $page, $template;

  if (count($page['tabsheet']) > 0)
  {
    $template->set_filename('tabsheet', 'admin/tabsheet.tpl');

    foreach ($page['tabsheet'] as $tab_name => $tab)
    {
      $is_selected = isset($tab['selected']) and $tab['selected'] === true;
      $template->assign_block_vars
      (
        'tab',
        array
        (
          'CLASSNAME' => ($is_selected ? 'selected_tab' : 'normal_tab'),
          'URL' => $tab['url'],
          'CAPTION' => $tab['caption']
        )
      );

      if ($is_selected)
      {
        $template->assign_vars(
          array('TABSHEET_TITLE' => '['.$tab['caption'].']'));
      }
    }

    $template->assign_var_from_handle('TABSHEET', 'tabsheet');
  }
}

//TOTO:Voir pour intégrer U_TABSHEET_TITLE dans les autres tabs 
//TODO:Selected sans link
//Remplacer mode par tab_caption
?>
