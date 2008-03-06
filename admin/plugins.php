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

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
check_status(ACCESS_ADMINISTRATOR);


// +-----------------------------------------------------------------------+
// |                     Sections definitions                              |
// +-----------------------------------------------------------------------+
if (empty($_GET['section']))
{
  $page['section'] = 'list';
}
else
{
  $page['section'] = $_GET['section'];
}

$tab_link = get_root_url().'admin.php?page=plugins&amp;section=';

// TabSheet
$tabsheet = new tabsheet();
// TabSheet initialization
$tabsheet->add('list', l10n('plugins_tab_list'), $tab_link.'list');
$tabsheet->add('update', l10n('plugins_tab_update'), $tab_link.'update');
$tabsheet->add('new', l10n('plugins_tab_new'), $tab_link.'new');
// TabSheet selection
$tabsheet->select($page['section']);
// Assign tabsheet to template
$tabsheet->assign();


// +-----------------------------------------------------------------------+
// |                     start template output                             |
// +-----------------------------------------------------------------------+

include(PHPWG_ROOT_PATH.'admin/plugins_'.$page['section'].'.php');
?>