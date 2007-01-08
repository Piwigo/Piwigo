<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2006-04-21 23:16:37 +0200 (ven., 21 avr. 2006) $
// | last modifier : $Author: nikrou $
// | revision      : $Revision: 1250 $
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

if (!defined('PHPWG_ROOT_PATH'))
{
  die ("Hacking attempt!");
}

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// | Actions                                                               |
// +-----------------------------------------------------------------------+

/*$action = (isset($_GET['action']) and !is_adviser()) ? $_GET['action'] : '';

switch ($action)
{
  case '???' :
  {
    break;
  }
  default :
  {
    break;
  }
}*/

// +-----------------------------------------------------------------------+
// | Define advanced features                                              |
// +-----------------------------------------------------------------------+

$advanced_features = array();

// Add advanced features
/*array_push($advanced_features,
  array
  (
    'CAPTION' => l10n('???'),
    'URL' => $start_url.'???'
  ));*/

array_push($advanced_features,
  array
  (
    'CAPTION' => l10n('Elements_not_linked'),
    'URL' => get_root_url().'admin.php?page=element_set&cat=not_linked'
  ));

array_push($advanced_features,
  array
  (
    'CAPTION' => l10n('Duplicates'),
    'URL' => get_root_url().'admin.php?page=element_set&cat=duplicates'
  ));

//$advanced_features is array of array composed of CAPTION & URL
$advanced_features = 
    trigger_event('array_advanced_features', $advanced_features);

// +-----------------------------------------------------------------------+
// |  Template init                                                        |
// +-----------------------------------------------------------------------+

$template->set_filenames(array('advanced_feature'=>'admin/advanced_feature.tpl'));

$start_url = get_root_url().'admin.php?page=advanced_feature&amp;action=';

$template->assign_vars(
  array
  (
    'U_HELP' => get_root_url().'popuphelp.php?page=advanced_feature'
  ));

// advanced_features
if (count($advanced_features) > 0)
{
  foreach ($advanced_features as $advanced_feature)
  {
    $template->assign_block_vars('advanced_features.advanced_feature', $advanced_feature);
  }
}

// +-----------------------------------------------------------------------+
// | Sending html code                                                     |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'advanced_feature');

?>
