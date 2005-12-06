<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2005-10-23 23:02:21 +0200 (dim, 23 oct 2005) $
// | last modifier : $Author: plg $
// | revision      : $Revision: 911 $
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
  die ('This page cannot be loaded directly, load upgrade.php');
}
else
{
  if (!defined('PHPWG_IN_UPGRADE') or !PHPWG_IN_UPGRADE)
  {
    die ('Hacking attempt!');
  }
}

// depending on the way the 1.5.0 was installed (from scratch or by upgrade)
// the database structure has small differences that should be corrected.

$query = '
ALTER TABLE phpwebgallery_users
  CHANGE COLUMN password password varchar(32) default NULL
;';

pwg_query(
  str_replace(
    'phpwebgallery_',
    PREFIX_TABLE,
    $query
    )
  );

$to_keep = array('id', 'username', 'password', 'mail_address');
  
$query = '
DESC phpwebgallery_users
;';

$result =
pwg_query(
  str_replace(
    'phpwebgallery_',
    PREFIX_TABLE,
    $query
    )
  );

while ($row = mysql_fetch_array($result))
{
  if (!in_array($row['Field'], $to_keep))
  {
    $query = '
ALTER TABLE phpwebgallery_users
  DROP COLUMN '.$row['Field'].'
;';
    pwg_query(
      str_replace(
        'phpwebgallery_',
        PREFIX_TABLE,
        $query
        )
      );
  }
}

?>