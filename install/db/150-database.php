<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2016 Piwigo Team                  http://piwigo.org |
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

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

$upgrade_description = 'add history_id_from+history_id_to in history_summary table';

// we use PREFIX_TABLE, in case Piwigo uses an external user table
pwg_query('
ALTER TABLE `'.PREFIX_TABLE.'history_summary`
  ADD COLUMN `history_id_from` int(10) unsigned default NULL,
  ADD COLUMN `history_id_to` int(10) unsigned default NULL
;');

$query = '
SELECT
    *
  FROM '.PREFIX_TABLE.'history
  WHERE summarized = \'true\'
  ORDER BY id DESC
  LIMIT 1
;';
// note : much faster than searching MAX(ID), ie on my big sample 14 seconds Vs 2 seconds
$history_lines = query2array($query);
if (count($history_lines) > 0)
{
  $last_summarized = $history_lines[0];

  list($year, $month, $day) = explode('-', $last_summarized['date']);
  list($hour) = explode(':', $last_summarized['time']);

  single_update(
    PREFIX_TABLE.'history_summary',
    array(
      'history_id_to' => $last_summarized['id'],
      ),
    array(
      'year' => $year,
      'month' => $month,
      'day' => $day,
      'hour' => $hour,
      )
    );

  // in case this script would update no summary line, it would mean the
  // summary has been purged and will be rebuild from scratch, based on the
  // content of history table
}

// for now, we keep column history.summarized even if Piwigo 2.9 no longer
// uses it. We will remove it in a future version. First we need to have
// "less" lines in history table. This will be possible with the automatic
// purge implemented in Piwigo 2.9.

echo "\n".$upgrade_description."\n";

?>
