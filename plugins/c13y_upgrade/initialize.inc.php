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

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

add_event_handler('get_check_integrity', 'c13y_upgrade');

function c13y_upgrade($c13y_array)
{
  global $lang, $conf;

  include(get_language_filepath('plugin.lang.php', dirname(__FILE__).'/'));
  
  $result = array();

  if (isset($conf['users_table']))
  {
    $result[] = get_c13y(
      l10n('c13y_upgrade_conf_users_table_msg'),
      null,
      null,
      l10n('c13y_upgrade_conf_users_table_correction').
      '<BR />'.
      get_htlm_links_more_info());
  }

  if (count($result) === 0)
  {
    $deactivate_msg_link = 
      '<a href="'.
      PHPWG_ROOT_PATH.
      'admin.php?page=plugins&amp;plugin=c13y_upgrade&amp;action=deactivate'.
      '" onclick="window.open(this.href, \'\'); return false;">'.
      l10n('c13y_upgrade_deactivate').'</a>';

    $result[] = get_c13y(
      l10n('c13y_upgrade_no_anomaly'),
      'c13y_upgrade_correction',
      'deactivate_plugin',
      $deactivate_msg_link
      );
  }

  return array_merge($c13y_array, $result);
}

function c13y_upgrade_correction($action)
{
  $result = false;

  switch ($action)
  {
    case 'deactivate_plugin':
      {
        $query = '
REPLACE INTO '.PLUGINS_TABLE.'
(id, state)
VALUES (\'c13y_upgrade\', \'inactive\')
;';
        pwg_query($query);
        $result = true;
      }
      break;
  }

  return $result;
}

?>
