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

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

$link = get_root_url().'admin.php?page=help&section=';
$selected = null;
$help_section_title = null;

$tabs = array(
  array(
    'code' => 'add_photos',
    'label' => l10n('Add Photos'),
    ),
  array(
    'code' => 'permissions',
    'label' => l10n('Permissions'),
    ),
  array(
    'code' => 'groups',
    'label' => l10n('Groups'),
    ),
  array(
    'code' => 'user_upload',
    'label' => l10n('User Upload'),
    ),
  array(
    'code' => 'virtual_links',
    'label' => l10n('Virtual Links'),
    ),
  array(
    'code' => 'misc',
    'label' => l10n('Miscellaneous'),
    ),
  );

if (!isset($_GET['section']))
{
  $section = $tabs[0]['code'];
}
else
{
  $section = $_GET['section'];
}

$tabsheet = new tabsheet();
foreach ($tabs as $tab)
{
  if ($tab['code'] == $section)
  {
    $selected_tab = $tab['code'];
    $help_section_title = $tab['label'];
  }
  
  $tabsheet->add($tab['code'], $tab['label'], $link.$tab['code']);
}
$tabsheet->select($selected_tab);
$tabsheet->assign();

$template->set_filenames(array('help' => 'help.tpl'));

$template->assign(
  array(
    'HELP_CONTENT' => load_language(
      'help/help_'.$selected_tab.'.html',
      '',
      array('return'=>true)
      ),
    'HELP_SECTION_TITLE' => $help_section_title,
    )
  );

// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'help');
?>
