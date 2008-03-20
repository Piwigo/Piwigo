<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2008 PhpWebGallery Team - http://phpwebgallery.net |
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

add_event_handler('list_check_integrity', 'c13y_upgrade');

function c13y_upgrade($c13y)
{
  global $conf;

  load_language('plugin.lang', dirname(__FILE__).'/');

  $can_be_deactivate = true;

  /* Check user with same e-mail */
  $query = '
select
  count(*)
from
  '.USERS_TABLE.'
where
  '.$conf['user_fields']['email'].' is not null
group by
  upper('.$conf['user_fields']['email'].')
having count(*) > 1
limit 0,1
;';

  if (mysql_fetch_array(pwg_query($query)))
  {
    $can_be_deactivate = false;
    $c13y->add_anomaly(
      l10n('c13y_dbl_email_user'),
      null,
      null,
      l10n('c13y_correction_dbl_email_user'));
  }

  /* Check plugin included in Piwigo sources */
  $included_plugins = array('dew', 'UpToDate', 'PluginsManager');
  $query = '
select
  id
from
  '.PLUGINS_TABLE.'
where
  id in ('.
    implode(
      ',',
      array_map(
        create_function('$s', 'return "\'".$s."\'";'),
        $included_plugins
        )
      )
      .')
;';

  $result = pwg_query($query);
  while ($row = mysql_fetch_assoc($result))
  {
    $can_be_deactivate = false;

    $uninstall_msg_link =
      '<a href="'.
      PHPWG_ROOT_PATH.
      'admin.php?page=plugins_list&amp;plugin='.$row['id'].'&amp;action=uninstall'.
      '" onclick="window.open(this.href, \'\'); return false;">'.
      sprintf(l10n('c13y_correction_obsolete_plugin'), $row['id']).'</a>';

    $c13y->add_anomaly(
      l10n('c13y_obsolete_plugin'),
      null,
      null,
      $uninstall_msg_link);
  }

  /* Check if this plugin must deactivate */
  if ($can_be_deactivate)
  {
    $deactivate_msg_link =
      '<a href="'.
      PHPWG_ROOT_PATH.
      'admin.php?page=plugins&amp;plugin=c13y_upgrade&amp;action=deactivate'.
      '" onclick="window.open(this.href, \'\'); return false;">'.
      l10n('c13y_upgrade_deactivate').'</a>';

    $c13y->add_anomaly(
      l10n('c13y_upgrade_no_anomaly'),
      'c13y_upgrade_correction',
      'deactivate_plugin',
      $deactivate_msg_link
      );
  }
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
