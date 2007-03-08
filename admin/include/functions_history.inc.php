<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | file          : $Id: functions_history.inc.php 1874 2007-03-06 02:07:15Z rub $
// | last update   : $Date: 2007-03-06 03:07:15 +0100 (mar., 06 mars 2007) $
// | last modifier : $Author: rub $
// | revision      : $Revision: 1874 $
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

include_once(PHPWG_ROOT_PATH.'admin/include/functions_tabsheet.inc.php');

function history_tabsheet()
{
  global $page, $link_start;

  // TabSheet initialization
  $page['tabsheet'] = array
  (
    'stats' => array
     (
      'caption' => l10n('Statistics'),
      'url' => $link_start.'stats'
     ),
    'history' => array
     (
      'caption' => l10n('Search'),
      'url' => $link_start.'history'
     )
  );

  $page['tabsheet'][$page['page']]['selected'] = true;

  // Assign tabsheet to template
  template_assign_tabsheet();
}

?>
